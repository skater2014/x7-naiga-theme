from pathlib import Path
import re
import html

OUT = Path("hub/inc/customhome-ch-subpage-meta-registry.php")
REPORT = Path("docs/ch-subpage-meta-registry-summary.txt")

TARGETS = {
    "concept": "hub/pages/iezukuri/templates/page-concept.php",
    "design_policy": "hub/pages/iezukuri/templates/page-design-policy.php",
    "nasu_shot": "hub/pages/iezukuri/templates/page-nasu-house.php",
    "design_office": "hub/pages/iezukuri/templates/page-design-office.php",
    "company": "hub/pages/iezukuri/templates/page-company.php",
    "contact": "hub/pages/iezukuri/templates/page-contact.php",
}

TEXT_TAGS = ["h1", "h2", "h3", "h4", "h5", "h6", "p", "li", "a", "span", "strong", "small", "button"]

EXCLUDE_PATTERNS = [
    r"^ch[-_]",
    r"^hub[-_]",
    r"^naigai[-_]",
    r"^_[a-z0-9_]+$",
    r"\.php$",
    r"\.css$",
    r"\.js$",
    r"^https?://",
    r"^/#",
    r"^/[a-zA-Z0-9/_-]+/?$",
    r"^[a-zA-Z0-9_-]+$",
    r"^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp|gif|svg|mp4)$",
]

ALLOW_ENGLISH = {
    "FLOW",
    "ACCESS",
    "CONTACT",
    "CONTACT FORM",
    "DETAIL",
    "PLAN",
    "PLAN A",
    "PLAN B",
    "PLAN C",
    "COMPANY",
    "CONCEPT",
}

def php_str(s):
    return "'" + str(s).replace("\\", "\\\\").replace("'", "\\'") + "'"

def keyify(s):
    s = str(s).lower()
    s = s.replace("ch-", "")
    s = re.sub(r"[^a-z0-9_]+", "_", s)
    s = re.sub(r"_+", "_", s).strip("_")
    return s or "item"

def clean_text(s):
    s = re.sub(r"<\?php.*?\?>", " ", s, flags=re.S)
    s = re.sub(r"<!--.*?-->", " ", s, flags=re.S)
    s = re.sub(r"<[^>]+>", " ", s)
    s = html.unescape(s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

def attr_classes(tag):
    m = re.search(r'class=["\']([^"\']+)["\']', tag)
    return m.group(1).split() if m else []

def attr_id(tag):
    m = re.search(r'id=["\']([^"\']+)["\']', tag)
    return m.group(1) if m else ""

def visible_string(s):
    s = html.unescape(str(s))
    s = re.sub(r"\s+", " ", s).strip()

    if len(s) < 2:
        return False

    if s in ALLOW_ENGLISH:
        return True

    # 日本語があるものは原則採用
    if re.search(r"[ぁ-んァ-ン一-龥]", s):
        return True

    # 英語でも、見出しに使う可能性が高い短文だけ採用
    if re.search(r"[A-Za-z]", s) and " " in s and len(s) <= 80:
        return True

    return False

def is_code_like(s):
    s = str(s).strip()

    for pat in EXCLUDE_PATTERNS:
        if re.search(pat, s):
            return True

    if "=>" in s or "<?php" in s or "$" in s:
        return True

    if re.fullmatch(r"[-–—_・|/｜:：,，.。!！?？\s]+", s):
        return True

    return False

def keep_text(s):
    return visible_string(s) and not is_code_like(s)

def extract_blocks(source):
    blocks = []

    for m in re.finditer(r'<(section|div|article|aside|header|footer)\b([^>]*)>', source, flags=re.I):
        full = m.group(0)
        classes = attr_classes(full)

        if not any(c.startswith("ch-") for c in classes):
            continue

        line = source[:m.start()].count("\n") + 1
        class_key = "_".join(keyify(c) for c in classes if c.startswith("ch-")) or "block"

        blocks.append({
            "key": f"_ch_block_{class_key}_{line}",
            "label": " ".join(classes),
            "line": line,
            "tag": m.group(1).lower(),
            "id": attr_id(full),
            "classes": classes,
        })

    return blocks

def extract_html_texts(source, page_key):
    rows = []

    for tag in TEXT_TAGS:
        pattern = rf'<{tag}\b([^>]*)>(.*?)</{tag}>'

        for m in re.finditer(pattern, source, flags=re.I | re.S):
            attrs = m.group(1)
            inner = m.group(2)
            text = clean_text(inner)

            if not keep_text(text):
                continue

            line = source[:m.start()].count("\n") + 1
            classes = attr_classes("<x " + attrs + ">")

            rows.append({
                "source": "html",
                "default": text,
                "line": line,
                "tag": tag,
                "classes": classes,
            })

    return rows

def extract_php_strings(source):
    rows = []

    # PHP配列や変数に入っている日本語文字列も拾う
    pattern = r"""(?P<quote>['"])(?P<text>(?:\\.|(?!\1).)*?)(?P=quote)"""

    for m in re.finditer(pattern, source, flags=re.S):
        raw = m.group("text")
        text = html.unescape(raw.encode("utf-8", "ignore").decode("unicode_escape", "ignore"))
        text = re.sub(r"\s+", " ", text).strip()

        if not keep_text(text):
            continue

        line = source[:m.start()].count("\n") + 1

        rows.append({
            "source": "php-string",
            "default": text,
            "line": line,
            "tag": "php",
            "classes": [],
        })

    return rows

def dedupe_texts(rows):
    seen = set()
    out = []

    for r in sorted(rows, key=lambda x: (x["line"], x["default"])):
        key = (r["default"], r["line"])
        if key in seen:
            continue
        seen.add(key)
        out.append(r)

    return out

lines = []
lines.append("<?php")
lines.append("/**")
lines.append(" * Auto generated: ch subpage meta registry")
lines.append(" * 対象: hub/pages/iezukuri/templates/page-*.php")
lines.append(" * 構成ビルダーは使わない。ch サブページの HTML/PHP 文言をメタキー化する。")
lines.append(" */")
lines.append("if (!defined('ABSPATH')) { exit; }")
lines.append("")
lines.append("if (!function_exists('naigai_ch_subpage_meta_registry')) {")
lines.append("    function naigai_ch_subpage_meta_registry() {")
lines.append("        return array(")

summary = []

for page_key, file_path in TARGETS.items():
    path = Path(file_path)

    if not path.exists():
        summary.append((page_key, file_path, 0, 0, "MISSING"))
        continue

    source = path.read_text(encoding="utf-8", errors="ignore")

    blocks = extract_blocks(source)
    text_rows = dedupe_texts(
        extract_html_texts(source, page_key) +
        extract_php_strings(source)
    )

    summary.append((page_key, file_path, len(blocks), len(text_rows), "OK"))

    lines.append(f"            {php_str(page_key)} => array(")
    lines.append(f"                'page_key' => {php_str(page_key)},")
    lines.append(f"                'file' => {php_str(file_path)},")
    lines.append("                'blocks' => array(")

    for b in blocks:
        lines.append("                    array(")
        lines.append(f"                        'key' => {php_str(b['key'])},")
        lines.append(f"                        'label' => {php_str(b['label'])},")
        lines.append(f"                        'line' => {int(b['line'])},")
        lines.append(f"                        'tag' => {php_str(b['tag'])},")
        lines.append(f"                        'id' => {php_str(b['id'])},")
        lines.append("                        'classes' => array(" + ",".join(php_str(c) for c in b["classes"]) + "),")
        lines.append("                    ),")

    lines.append("                ),")
    lines.append("                'texts' => array(")

    for i, t in enumerate(text_rows, start=1):
        key = f"_ch_text_{page_key}_{i:03d}"

        lines.append("                    array(")
        lines.append(f"                        'key' => {php_str(key)},")
        lines.append(f"                        'label' => {php_str(t['source'] + ' ' + t['tag'] + ' line ' + str(t['line']))},")
        lines.append(f"                        'default' => {php_str(t['default'])},")
        lines.append(f"                        'line' => {int(t['line'])},")
        lines.append(f"                        'tag' => {php_str(t['tag'])},")
        lines.append("                        'classes' => array(" + ",".join(php_str(c) for c in t["classes"]) + "),")
        lines.append("                    ),")

    lines.append("                ),")
    lines.append("            ),")

lines.append("        );")
lines.append("    }")
lines.append("}")
lines.append("")

OUT.write_text("\n".join(lines), encoding="utf-8")

report = []
for row in summary:
    report.append(f"{row[0]} / blocks={row[2]} / texts={row[3]} / {row[4]} / {row[1]}")
REPORT.write_text("\n".join(report) + "\n", encoding="utf-8")

print(f"generated: {OUT}")
print(f"generated: {REPORT}")
print("")
print("\n".join(report))
