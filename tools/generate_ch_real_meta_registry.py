from pathlib import Path
import re

OUT = Path("hub/inc/customhome-ch-subpage-meta-registry.php")
REPORT = Path("docs/ch-real-meta-registry-summary.txt")

TARGETS = {
    "concept": "hub/pages/iezukuri/templates/page-concept.php",
    "design_policy": "hub/pages/iezukuri/templates/page-design-policy.php",
    "nasu_shot": "hub/pages/iezukuri/templates/page-nasu-house.php",
    "design_office": "hub/pages/iezukuri/templates/page-design-office.php",
    "company": "hub/pages/iezukuri/templates/page-company.php",
    "contact": "hub/pages/iezukuri/templates/page-contact.php",
}

EXPAND_MAX = 8

def php_str(s):
    return "'" + str(s).replace("\\", "\\\\").replace("'", "\\'") + "'"

def clean_default(s):
    if s is None:
        return ""
    s = str(s)
    if s.startswith("home_url(") or s.startswith("get_the_title("):
        return ""
    return s.strip().strip("'").strip('"')

def field_type(key):
    if key.endswith("_image_id") or key.endswith("_mp4_id"):
        return "media_id"
    if key.endswith("_page_id"):
        return "page_id"
    if key.endswith("_url"):
        return "url"
    if key.endswith("_text") or key.endswith("_lead") or key.endswith("_desc") or key.endswith("_iframe"):
        return "textarea"
    if key.startswith("_ch_show_") or key.startswith("_ch_is_"):
        return "checkbox"
    return "text"

def label_from_key(key):
    label = key
    label = label.replace("_ch_", "")
    label = label.replace("_hub_ch_", "")
    label = label.replace("_", " ")
    return label

def extract_blocks(src):
    rows = []
    for m in re.finditer(r'<(section|div|article|aside|header|footer)\b([^>]*)>', src, flags=re.I):
        tag = m.group(0)
        cm = re.search(r'class=["\']([^"\']+)["\']', tag)
        if not cm:
            continue
        classes = cm.group(1).split()
        if not any(c.startswith("ch-") for c in classes):
            continue
        line = src[:m.start()].count("\n") + 1
        key_name = "_".join(
            re.sub(r"[^a-zA-Z0-9]+", "_", c.replace("ch-", "")).strip("_").lower()
            for c in classes if c.startswith("ch-")
        )
        rows.append({
            "key": f"_ch_block_{key_name}_{line}",
            "label": " ".join(classes),
            "line": line,
            "tag": m.group(1).lower(),
            "classes": classes,
        })
    return rows

def add_field(fields, key, default="", source="meta"):
    if not key.startswith("_ch_") and not key.startswith("_hub_ch_"):
        return
    if key not in fields:
        fields[key] = {
            "key": key,
            "label": label_from_key(key),
            "type": field_type(key),
            "default": clean_default(default),
            "source": source,
        }

def extract_fields(src):
    fields = {}

    # $meta('_ch_xxx', 'default')
    for m in re.finditer(r"\$meta\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\)", src):
        key = m.group(1)
        default_raw = m.group(2).strip()
        default = ""
        dm = re.match(r"['\"](.*?)['\"]", default_raw, flags=re.S)
        if dm:
            default = dm.group(1)
        add_field(fields, key, default, "$meta")

    # get_post_meta($post_id, '_ch_xxx', true)
    for m in re.finditer(r"get_post_meta\(\s*[^,]+,\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "get_post_meta")

    # $image('_ch_xxx_image_id'
    for m in re.finditer(r"\$image(?:_tag)?\(\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "$image")

    # $url('_page_id', '_url')
    for m in re.finditer(r"\$url\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "$url")
        add_field(fields, m.group(2), "", "$url")

    # $page_url('_page_id', '_url')
    for m in re.finditer(r"\$page_url\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "$page_url")
        add_field(fields, m.group(2), "", "$page_url")

    # "_ch_xxx{$i}_yyy" を 1〜8 に展開
    for m in re.finditer(r"['\"](_ch_[^'\"]*\{\$i\}[^'\"]*)['\"]", src):
        pattern = m.group(1)
        for i in range(1, EXPAND_MAX + 1):
            key = pattern.replace("{$i}", str(i))
            add_field(fields, key, "", "loop")

    return list(fields.values())

lines = []
lines.append("<?php")
lines.append("/** Auto generated: /iezukuri ch real meta registry. */")
lines.append("if (!defined('ABSPATH')) { exit; }")
lines.append("if (!function_exists('naigai_ch_subpage_meta_registry')) {")
lines.append("function naigai_ch_subpage_meta_registry() {")
lines.append("return array(")

summary = []

for page_key, file_path in TARGETS.items():
    path = Path(file_path)
    src = path.read_text(encoding="utf-8", errors="ignore") if path.exists() else ""

    blocks = extract_blocks(src)
    fields = extract_fields(src)

    summary.append(f"{page_key} / blocks={len(blocks)} / fields={len(fields)} / {file_path}")

    lines.append(f"{php_str(page_key)} => array(")
    lines.append(f"'page_key' => {php_str(page_key)},")
    lines.append(f"'file' => {php_str(file_path)},")
    lines.append("'blocks' => array(")

    for b in blocks:
        lines.append("array(")
        lines.append(f"'key' => {php_str(b['key'])},")
        lines.append(f"'label' => {php_str(b['label'])},")
        lines.append(f"'line' => {int(b['line'])},")
        lines.append(f"'tag' => {php_str(b['tag'])},")
        lines.append("'classes' => array(" + ",".join(php_str(c) for c in b["classes"]) + "),")
        lines.append("),")

    lines.append("),")
    lines.append("'fields' => array(")

    for f in fields:
        lines.append("array(")
        lines.append(f"'key' => {php_str(f['key'])},")
        lines.append(f"'label' => {php_str(f['label'])},")
        lines.append(f"'type' => {php_str(f['type'])},")
        lines.append(f"'default' => {php_str(f['default'])},")
        lines.append(f"'source' => {php_str(f['source'])},")
        lines.append("),")

    lines.append("),")
    lines.append("),")

lines.append(");")
lines.append("}")
lines.append("}")

OUT.write_text("\n".join(lines) + "\n", encoding="utf-8")
REPORT.write_text("\n".join(summary) + "\n", encoding="utf-8")

print("\n".join(summary))
