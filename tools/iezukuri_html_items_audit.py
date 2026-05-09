from pathlib import Path
import re
import html
from datetime import datetime

ROOT = Path(".")
OUT = Path("docs/iezukuri-html-items-audit.md")
JSON_OUT = Path("docs/iezukuri-html-items-audit.json")

TARGETS = []

# 可能性があるテンプレート置き場を全部見る
for pattern in [
    "page-construction-hub-sub.php",
    "hub/templates/customhome-pages/page-*.php",
    "hub/pages/iezukuri/templates/page-*.php",
    "hub/inc/customhome-contact-sections.php",
    "hub/inc/customhome-company-info.php",
    "hub/inc/customhome-plan-tabs.php",
    "hub/inc/customhome-section-builder.php",
    "hub/inc/customhome-phase2.php",
]:
    TARGETS.extend(ROOT.glob(pattern))

TARGETS = sorted(set([p for p in TARGETS if p.exists() and p.is_file()]))

def clean_text(s: str) -> str:
    s = html.unescape(s)
    s = re.sub(r"<\?php.*?\?>", "", s, flags=re.S)
    s = re.sub(r"<[^>]+>", " ", s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

def get_classes(tag: str):
    m = re.search(r'class=["\']([^"\']+)["\']', tag)
    if not m:
        return []
    return m.group(1).split()

def get_id(tag: str):
    m = re.search(r'id=["\']([^"\']+)["\']', tag)
    return m.group(1) if m else ""

def get_data_attrs(tag: str):
    return re.findall(r'(data-[a-zA-Z0-9_-]+)=["\']([^"\']*)["\']', tag)

def extract_blocks(text: str):
    blocks = []

    # div/section/article/aside/header/footer 単位で、class付きの開始タグを拾う
    for m in re.finditer(r'<(section|div|article|aside|header|footer)\b([^>]*)>', text, flags=re.I):
        tag_name = m.group(1)
        attrs = m.group(2)
        full_tag = m.group(0)
        classes = get_classes(full_tag)

        # ch- / hub- / naigai- 系だけ拾う
        if not any(c.startswith(("ch-", "hub-", "naigai-")) for c in classes):
            continue

        blocks.append({
            "tag": tag_name,
            "line": text[:m.start()].count("\n") + 1,
            "id": get_id(full_tag),
            "classes": classes,
            "data_attrs": get_data_attrs(full_tag),
        })

    return blocks

def extract_text_items(text: str):
    items = []

    # h1-h6, p, li, span, a, strong, small の表示テキストを拾う
    pattern = r'<(h[1-6]|p|li|span|a|strong|small|em|button)\b([^>]*)>(.*?)</\1>'
    for m in re.finditer(pattern, text, flags=re.I | re.S):
        tag = m.group(1)
        attrs = m.group(2)
        inner = m.group(3)
        full_tag = m.group(0)

        # PHPだけのものは飛ばす
        if "<?php" in inner and clean_text(inner) == "":
            continue

        label = clean_text(inner)
        if not label:
            continue

        if len(label) > 180:
            label = label[:180] + "..."

        classes = get_classes("<x " + attrs + ">")

        items.append({
            "tag": tag,
            "line": text[:m.start()].count("\n") + 1,
            "classes": classes,
            "text": label,
        })

    return items

def extract_meta_keys(text: str):
    keys = set()

    # get_post_meta($post_id, '_key', true)
    for m in re.finditer(r"get_post_meta\s*\([^,]+,\s*['\"]([^'\"]+)['\"]", text):
        keys.add(m.group(1))

    # name="_key"
    for m in re.finditer(r"name=[\"']([^\"']+)[\"']", text):
        name = m.group(1)
        if name.startswith(("_ch_", "_hub_ch_", "naigai_ch_", "_iez_")):
            keys.add(name)

    return sorted(keys)

def extract_functions(text: str):
    return re.findall(r"function\s+(naigai_[a-zA-Z0-9_]+)\s*\(", text)

def infer_page_key(path: Path, text: str):
    name = path.name

    # wrapper class があればそれを優先
    m = re.search(r'ch-[a-z0-9_-]+-page', text)
    if m:
        return m.group(0)

    if name.startswith("page-"):
        return name.replace("page-", "").replace(".php", "")

    if "contact-flow-section" in text:
        return "ch-contact-flow-section"

    if "contact-form-section" in text:
        return "ch-contact-form-section"

    if "company-map-section" in text:
        return "ch-company-map-section"

    if "plan" in name:
        return "plan-layout"

    return path.stem

audit = []

for path in TARGETS:
    text = path.read_text(encoding="utf-8", errors="ignore")

    audit.append({
        "file": str(path),
        "page_key": infer_page_key(path, text),
        "blocks": extract_blocks(text),
        "text_items": extract_text_items(text),
        "meta_keys": extract_meta_keys(text),
        "functions": extract_functions(text),
    })

# JSON
import json
JSON_OUT.write_text(json.dumps(audit, ensure_ascii=False, indent=2), encoding="utf-8")

# Markdown
lines = []
lines.append("# iezukuri HTML items audit")
lines.append("")
lines.append(f"- generated: {datetime.now().isoformat(timespec='seconds')}")
lines.append(f"- files scanned: {len(TARGETS)}")
lines.append("")

for item in audit:
    lines.append(f"## {item['file']}")
    lines.append("")
    lines.append(f"- page/key: `{item['page_key']}`")
    lines.append("")

    if item["functions"]:
        lines.append("### functions")
        for fn in item["functions"]:
            lines.append(f"- `{fn}()`")
        lines.append("")

    if item["meta_keys"]:
        lines.append("### meta keys already used")
        for key in item["meta_keys"]:
            lines.append(f"- `{key}`")
        lines.append("")

    lines.append("### HTML blocks / layout classes")
    if item["blocks"]:
        for b in item["blocks"]:
            cls = " ".join(b["classes"])
            ident = f" id=\"{b['id']}\"" if b["id"] else ""
            lines.append(f"- line {b['line']}: `<{b['tag']}{ident} class=\"{cls}\">`")
    else:
        lines.append("- none")
    lines.append("")

    lines.append("### visible text items")
    if item["text_items"]:
        for t in item["text_items"]:
            cls = " ".join(t["classes"])
            cls_text = f" class=\"{cls}\"" if cls else ""
            lines.append(f"- line {t['line']}: `<{t['tag']}{cls_text}>` → {t['text']}")
    else:
        lines.append("- none")
    lines.append("")

OUT.write_text("\n".join(lines), encoding="utf-8")

print(f"generated: {OUT}")
print(f"generated: {JSON_OUT}")
print("")
for item in audit:
    print(item["page_key"], "=>", item["file"])
