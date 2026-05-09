from pathlib import Path
import re
import html

OUT = Path("hub/inc/customhome-ch-subpage-meta-registry.php")
REPORT = Path("docs/ch-page-data-registry-summary.txt")

TARGETS = {
    "concept": "hub/pages/iezukuri/templates/page-concept.php",
    "design_policy": "hub/pages/iezukuri/templates/page-design-policy.php",
    "nasu_shot": "hub/pages/iezukuri/templates/page-nasu-house.php",
    "design_office": "hub/pages/iezukuri/templates/page-design-office.php",
    "company": "hub/pages/iezukuri/templates/page-company.php",
    "contact": "hub/pages/iezukuri/templates/page-contact.php",
}

EXPAND_MAX = 12

def php_str(s):
    return "'" + str(s).replace("\\", "\\\\").replace("'", "\\'") + "'"

def keyify(s):
    s = str(s).lower()
    s = re.sub(r"[^a-z0-9]+", "_", s)
    return re.sub(r"_+", "_", s).strip("_") or "item"

def clean_text(s):
    s = re.sub(r"<\?php.*?\?>", " ", s, flags=re.S)
    s = re.sub(r"<!--.*?-->", " ", s, flags=re.S)
    s = re.sub(r"<[^>]+>", " ", s)
    s = html.unescape(s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

def keep_text(s):
    if not s or len(s) < 2:
        return False
    ng = ["ABSPATH", "get_post_meta", "function", "$", "=>", "<?php", "wp_get_attachment", "the_content", "get_post_field"]
    if any(x in s for x in ng):
        return False
    if re.search(r"[ぁ-んァ-ン一-龥]", s):
        return True
    if s in ["FLOW", "ACCESS", "CONTACT", "DETAIL", "PLAN A", "PLAN B", "PLAN C"]:
        return True
    return False

def field_type(key, default=""):
    if key.endswith("_image_ids") or key.endswith("_gallery_ids"):
        return "media_ids"
    if key.endswith("_image_id") or key.endswith("_mp4_id") or key.endswith("_video_mp4_id"):
        return "media_id"
    if key.endswith("_page_id"):
        return "page_id"
    if key.endswith("_url"):
        return "url"
    if key.endswith("_text") or key.endswith("_lead") or key.endswith("_desc") or key.endswith("_body") or len(default) > 40:
        return "textarea"
    return "text"

def label_from_key(key):
    return key.replace("_ch_", "").replace("_hub_ch_", "").strip("_").replace("_", " ")

def add_field(fields, key, default="", source="meta", default_url=""):
    if not key.startswith("_ch_") and not key.startswith("_hub_ch_"):
        return
    if key not in fields:
        fields[key] = {
            "key": key,
            "label": label_from_key(key),
            "type": field_type(key, default),
            "default": default,
            "default_url": default_url,
            "source": source,
        }

def extract_meta_fields(src):
    fields = {}

    for m in re.finditer(r"\$meta\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\)", src, flags=re.S):
        key = m.group(1)
        raw = m.group(2).strip()
        default = ""
        dm = re.match(r"['\"](.*?)['\"]", raw, flags=re.S)
        if dm:
            default = dm.group(1)
        add_field(fields, key, default, "$meta")

    for m in re.finditer(r"get_post_meta\(\s*[^,]+,\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "get_post_meta")

    for m in re.finditer(r"\$image(?:_tag)?\(\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "$image")

    for m in re.finditer(r"\$(?:url|page_url)\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]", src):
        add_field(fields, m.group(1), "", "$url")
        add_field(fields, m.group(2), "", "$url")

    for m in re.finditer(r"['\"](_ch_[^'\"]*\{\$i\}[^'\"]*)['\"]", src):
        pattern = m.group(1)
        for i in range(1, EXPAND_MAX + 1):
            add_field(fields, pattern.replace("{$i}", str(i)), "", "loop")

    return fields

def extract_static_text_fields(src, page_key):
    fields = {}
    idx = 1
    seen = set()

    for m in re.finditer(r'<(h[1-6]|p|li|a|span|strong|small|button)\b([^>]*)>(.*?)</\1>', src, flags=re.I | re.S):
        tag = m.group(1).lower()
        text = clean_text(m.group(3))

        if not keep_text(text):
            continue
        if text in seen:
            continue

        seen.add(text)
        key = f"_ch_{page_key}_static_{idx:03d}_{tag}"

        fields[key] = {
            "key": key,
            "label": f"{tag} / {text[:28]}",
            "type": "textarea" if tag in ["p", "li"] or len(text) > 40 else "text",
            "default": text,
            "default_url": "",
            "source": "static-html",
        }
        idx += 1

    return fields

def extract_static_image_fields(src, page_key):
    fields = {}
    idx = 1

    for m in re.finditer(r'<img\b([^>]+)>', src, flags=re.I | re.S):
        tag = m.group(0)

        src_m = re.search(r'src=["\']([^"\']+)["\']', tag)
        if not src_m:
            continue

        image_url = html.unescape(src_m.group(1)).strip()
        if not image_url or image_url.startswith("<?php") or "$" in image_url:
            continue

        alt = ""
        alt_m = re.search(r'alt=["\']([^"\']*)["\']', tag)
        if alt_m:
            alt = html.unescape(alt_m.group(1)).strip()

        key = f"_ch_{page_key}_static_image_{idx:03d}_image_id"
        label = "画像"
        if alt:
            label += f" / {alt[:28]}"

        fields[key] = {
            "key": key,
            "label": label,
            "type": "media_id",
            "default": "",
            "default_url": image_url,
            "source": "static-img",
        }
        idx += 1

    return fields

def extract_blocks(src):
    blocks = []
    for m in re.finditer(r'<(section|div|article|aside|header|footer)\b([^>]*)>', src, flags=re.I):
        tag = m.group(0)
        cm = re.search(r'class=["\']([^"\']+)["\']', tag)
        if not cm:
            continue

        classes = cm.group(1).split()
        if not any(c.startswith("ch-") for c in classes):
            continue

        line = src[:m.start()].count("\n") + 1
        key = "_ch_block_" + keyify("_".join([c for c in classes if c.startswith("ch-")])) + "_" + str(line)

        blocks.append({
            "key": key,
            "label": " ".join(classes),
            "line": line,
            "tag": m.group(1).lower(),
            "classes": classes,
        })

    return blocks

lines = []
lines.append("<?php")
lines.append("/** Auto generated: /iezukuri page-specific ch registry. */")
lines.append("if (!defined('ABSPATH')) { exit; }")
lines.append("if (!function_exists('naigai_ch_subpage_meta_registry')) {")
lines.append("function naigai_ch_subpage_meta_registry() {")
lines.append("return array(")

summary = []

for page_key, file_path in TARGETS.items():
    path = Path(file_path)
    src = path.read_text(encoding="utf-8", errors="ignore") if path.exists() else ""

    blocks = extract_blocks(src)
    fields = extract_meta_fields(src)

    for group in (extract_static_text_fields(src, page_key), extract_static_image_fields(src, page_key)):
        for k, v in group.items():
            if k not in fields:
                fields[k] = v

    image_count = sum(1 for f in fields.values() if f["type"] in ["media_id", "media_ids"])
    text_count = len(fields) - image_count

    summary.append(f"{page_key} / fields={len(fields)} / text={text_count} / images={image_count} / blocks={len(blocks)} / {file_path}")

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
    for f in fields.values():
        lines.append("array(")
        lines.append(f"'key' => {php_str(f['key'])},")
        lines.append(f"'label' => {php_str(f['label'])},")
        lines.append(f"'type' => {php_str(f['type'])},")
        lines.append(f"'default' => {php_str(f['default'])},")
        lines.append(f"'default_url' => {php_str(f['default_url'])},")
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
