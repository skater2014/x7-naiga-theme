from __future__ import annotations
import sys
from collections import defaultdict
from dataclasses import dataclass
from typing import List, Tuple

import tinycss2
from tinycss2.ast import QualifiedRule, AtRule, Declaration

@dataclass
class RuleRec:
    ctx: Tuple[str, ...]
    selector: str
    decls_norm: str
    decls_raw: str
    line: int
    col: int

def ser(nodes) -> str:
    return tinycss2.serialize(nodes).strip()

def parse_decls(content_tokens) -> Tuple[str, str]:
    decls = tinycss2.parse_declaration_list(content_tokens, skip_whitespace=True, skip_comments=True)
    items = []
    raw_items = []
    for d in decls:
        if isinstance(d, Declaration) and d.value is not None:
            name = d.name.strip().lower()
            val = tinycss2.serialize(d.value).strip()
            imp = bool(d.important)
            raw_items.append((name, val, imp))
            items.append((name, val, imp))

    raw = "; ".join([f"{n}:{v}{' !important' if imp else ''}" for n, v, imp in raw_items]).strip()
    items_sorted = sorted(items, key=lambda x: (x[0], x[1], x[2]))
    norm = "; ".join([f"{n}:{v}{' !important' if imp else ''}" for n, v, imp in items_sorted]).strip()
    return norm, raw

def walk_rules(tokens, ctx: Tuple[str, ...], out: List[RuleRec]):
    rules = tinycss2.parse_rule_list(tokens, skip_whitespace=True, skip_comments=True)
    for r in rules:
        if isinstance(r, QualifiedRule):
            selector = ser(r.prelude)
            norm, raw = parse_decls(r.content)
            out.append(RuleRec(
                ctx=ctx,
                selector=selector,
                decls_norm=norm,
                decls_raw=raw,
                line=getattr(r, "source_line", 0) or 0,
                col=getattr(r, "source_column", 0) or 0,
            ))
        elif isinstance(r, AtRule):
            name = (r.at_keyword or "").lower()
            prelude = ser(r.prelude) if r.prelude else ""
            if r.content is None:
                continue
            if name in ("media", "supports", "layer"):
                ctx2 = ctx + (f"@{name} {prelude}".strip(),)
                walk_rules(r.content, ctx2, out)

def main():
    if len(sys.argv) < 2:
        print("Usage: python3 tools/css_dedupe_report.py style.css", file=sys.stderr)
        sys.exit(2)
    path = sys.argv[1]
    css = open(path, "r", encoding="utf-8", errors="ignore").read()
    sheet = tinycss2.parse_stylesheet(css, skip_whitespace=True, skip_comments=True)

    out: List[RuleRec] = []
    walk_rules(sheet, tuple(), out)

    exact = defaultdict(list)
    for rr in out:
        key = (rr.ctx, rr.selector, rr.decls_raw)
        exact[key].append(rr)
    exact_dupes = [(k, v) for k, v in exact.items() if len(v) >= 2]

    selmap = defaultdict(list)
    for rr in out:
        selmap[(rr.ctx, rr.selector)].append(rr)
    sel_repeats = [(k, v) for k, v in selmap.items() if len(v) >= 2 and len({x.decls_raw for x in v}) >= 2]

    declset = defaultdict(list)
    for rr in out:
        if rr.decls_norm:
            declset[(rr.ctx, rr.decls_norm)].append(rr)
    common_sets = sorted(
        [(k, v) for k, v in declset.items() if len(v) >= 3],
        key=lambda kv: len(kv[1]),
        reverse=True
    )

    def ctx_str(ctx):
        return " > ".join(ctx) if ctx else "(global)"

    print("=== CSS DUPLICATE REPORT ===")
    print(f"file: {path}")
    print(f"rules parsed: {len(out)}")
    print()

    print("## 1) Exact duplicate blocks (safe to delete one of them)")
    if not exact_dupes:
        print("(none)")
    else:
        for (ctx, sel, raw), recs in sorted(exact_dupes, key=lambda x: (ctx_str(x[0][0]), x[0][1]))[:120]:
            locs = ", ".join([f"L{r.line}:{r.col}" for r in recs])
            print(f"- {ctx_str(ctx)} | {sel}  -> {locs}")
    print()

    print("## 2) Same selector repeated with different declarations (needs merge)")
    if not sel_repeats:
        print("(none)")
    else:
        for (ctx, sel), recs in sorted(sel_repeats, key=lambda x: (ctx_str(x[0][0]), x[0][1]))[:120]:
            print(f"- {ctx_str(ctx)} | {sel}")
            for r in sorted(recs, key=lambda x: x.line):
                s = r.decls_raw
                print(f"    - L{r.line}:{r.col}  {s[:140]}{'...' if len(s)>140 else ''}")
    print()

    print("## 3) Reused declaration sets across many selectors (candidate for grouping/utilities)")
    if not common_sets:
        print("(none)")
    else:
        for (ctx, norm), recs in common_sets[:60]:
            print(f"- {ctx_str(ctx)} | used by {len(recs)} selectors | decls: {norm[:160]}{'...' if len(norm)>160 else ''}")
            for r in sorted(recs, key=lambda x: x.line)[:10]:
                print(f"    - {r.selector} (L{r.line})")
            if len(recs) > 10:
                print(f"    ... +{len(recs)-10} more")
    print()

if __name__ == "__main__":
    main()
