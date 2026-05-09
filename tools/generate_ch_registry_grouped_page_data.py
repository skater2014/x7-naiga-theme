from pathlib import Path
import re
import html

OUT = Path("hub/inc/customhome-ch-subpage-meta-registry.php")
REPORT = Path("docs/ch-grouped-page-data-summary.txt")

TARGETS = {
    "concept": {
        "file": "hub/pages/iezukuri/templates/page-concept.php",
        "label": "コンセプト",
    },
    "design_policy": {
        "file": "hub/pages/iezukuri/templates/page-design-policy.php",
        "label": "設計方針",
    },
    "nasu_shot": {
        "file": "hub/pages/iezukuri/templates/page-nasu-house.php",
        "label": "那須の家",
    },
    "design_office": {
        "file": "hub/pages/iezukuri/templates/page-design-office.php",
        "label": "設計事務所",
    },
    "company": {
        "file": "hub/pages/iezukuri/templates/page-company.php",
        "label": "会社案内",
    },
    "contact": {
        "file": "hub/pages/iezukuri/templates/page-contact.php",
        "label": "お問い合わせ",
    },
}

EXPAND_MAX = 12

LABEL_WORDS = {
    "title": "見出し",
    "heading": "見出し",
    "lead": "リード文",
    "text": "本文",
    "body": "本文",
    "desc": "説明文",
    "eyebrow": "小見出し",
    "label": "ラベル",
    "url": "URL",
    "page": "リンク先ページ",
    "image": "画像",
    "gallery": "画像ギャラリー",
    "btn": "ボタン",
    "button": "ボタン",
    "map": "地図",
    "iframe": "埋め込み",
}

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

    ng = [
        "ABSPATH",
        "get_post_meta",
        "function",
        "$",
        "=>",
        "<?php",
        "wp_get_attachment",
        "the_content",
        "get_post_field",
        "apply_filters",
        "esc_html",
        "array(",
        "return ",
    ]

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
    if (
        key.endswith("_text")
        or key.endswith("_lead")
        or key.endswith("_desc")
        or key.endswith("_body")
        or key.endswith("_iframe")
        or len(default) > 40
    ):
        return "textarea"
    return "text"

def label_from_key(key):
    raw = key.replace("_hub_ch_", "").replace("_ch_", "")
    parts = [p for p in raw.split("_") if p]

    label_parts = []
    for p in parts:
        label_parts.append(LABEL_WORDS.get(p, p))

    return " / ".join(label_parts)

def source_group_from_key(key, page_label):
    raw = key.replace("_hub_ch_", "").replace("_ch_", "")
    parts = [p for p in raw.split("_") if p]

    # 先頭のページ名・種別を大枠として使う
    if parts:
        first = parts[0]
        jp = {
            "concept": "コンセプト",
            "design": "設計",
            "policy": "方針",
            "nasu": "那須の家",
            "intro": "導入",
            "hero": "Hero",
            "flow": "流れ",
            "cta": "CTA",
            "company": "会社案内",
            "contact": "お問い合わせ",
            "office": "設計事務所",
            "card": "カード",
            "step": "ステップ",
            "faq": "FAQ",
            "access": "アクセス",
            "profile": "プロフィール",
        }.get(first)
        if jp:
            return jp

    return page_label

def nearest_heading(headings, line, fallback):
    current = fallback
    for hline, text in headings:
        if hline <= line:
            current = text
        else:
            break
    return current or fallback

def extract_headings(src, page_label):
    headings = []

    for m in re.finditer(r'<(h[1-4])\b[^>]*>(.*?)</\1>', src, flags=re.I | re.S):
        text = clean_text(m.group(2))
        if keep_text(text):
            line = src[:m.start()].count("\n") + 1
            headings.append((line, text))

    if not headings:
        headings.append((1, page_label))

    return sorted(headings, key=lambda x: x[0])

def add_field(fields, key, default="", source="meta", group="", default_url=""):
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
            "group": group,
        }

def extract_meta_fields(src, page_label, headings):
    fields = {}

    def group_at(pos, key):
        line = src[:pos].count("\n") + 1
        base = nearest_heading(headings, line, source_group_from_key(key, page_label))
        return base

    # $meta('_ch_xxx', 'default')
    for m in re.finditer(r"\$meta\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\)", src, flags=re.S):
        key = m.group(1)
        raw = m.group(2).strip()
        default = ""
        dm = re.match(r"['\"](.*?)['\"]", raw, flags=re.S)
        if dm:
            default = dm.group(1)
        add_field(fields, key, default, "$meta", group_at(m.start(), key))

    # get_post_meta($post_id, '_ch_xxx', true)
    for m in re.finditer(r"get_post_meta\(\s*[^,]+,\s*['\"]([^'\"]+)['\"]", src):
        key = m.group(1)
        add_field(fields, key, "", "get_post_meta", group_at(m.start(), key))

    # $image('_ch_xxx_image_id')
    for m in re.finditer(r"\$image(?:_tag)?\(\s*['\"]([^'\"]+)['\"]", src):
        key = m.group(1)
        add_field(fields, key, "", "$image", group_at(m.start(), key))

    # $url('_page_id', '_url')
    for m in re.finditer(r"\$(?:url|page_url)\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]", src):
        key1 = m.group(1)
        key2 = m.group(2)
        add_field(fields, key1, "", "$url", group_at(m.start(), key1))
        add_field(fields, key2, "", "$url", group_at(m.start(), key2))

    # loop key: _ch_xxx{$i}_title
    for m in re.finditer(r"['\"](_ch_[^'\"]*\{\$i\}[^'\"]*)['\"]", src):
        pattern = m.group(1)
        for i in range(1, EXPAND_MAX + 1):
            key = pattern.replace("{$i}", str(i))
            add_field(fields, key, "", "loop", group_at(m.start(), key))

    return fields

def extract_static_text_fields(src, page_key, page_label, headings):
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

        line = src[:m.start()].count("\n") + 1
        group = nearest_heading(headings, line, page_label)

        key = f"_ch_{page_key}_static_{idx:03d}_{tag}"

        fields[key] = {
            "key": key,
            "label": f"{tag} / {text[:28]}",
            "type": "textarea" if tag in ["p", "li"] or len(text) > 40 else "text",
            "default": text,
            "default_url": "",
            "source": "static-html",
            "group": group,
        }
        idx += 1

    return fields

def extract_static_image_fields(src, page_key, page_label, headings):
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

        line = src[:m.start()].count("\n") + 1
        group = nearest_heading(headings, line, page_label)

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
            "group": group,
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
lines.append("/** Auto generated: /iezukuri grouped page-specific ch registry. */")
lines.append("if (!defined('ABSPATH')) { exit; }")
lines.append("if (!function_exists('naigai_ch_subpage_meta_registry')) {")
lines.append("function naigai_ch_subpage_meta_registry() {")
lines.append("return array(")

summary = []

for page_key, data in TARGETS.items():
    file_path = data["file"]
    page_label = data["label"]

    path = Path(file_path)
    src = path.read_text(encoding="utf-8", errors="ignore") if path.exists() else ""

    headings = extract_headings(src, page_label)
    blocks = extract_blocks(src)
    fields = extract_meta_fields(src, page_label, headings)

    for group in (
        extract_static_text_fields(src, page_key, page_label, headings),
        extract_static_image_fields(src, page_key, page_label, headings),
    ):
        for k, v in group.items():
            if k not in fields:
                fields[k] = v

    image_count = sum(1 for f in fields.values() if f["type"] in ["media_id", "media_ids"])
    text_count = len(fields) - image_count
    groups = sorted(set(f["group"] for f in fields.values()))

    summary.append(f"{page_label} / fields={len(fields)} / text={text_count} / images={image_count} / groups={len(groups)} / {file_path}")

    lines.append(f"{php_str(page_key)} => array(")
    lines.append(f"'page_key' => {php_str(page_key)},")
    lines.append(f"'page_label' => {php_str(page_label)},")
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
        lines.append(f"'group' => {php_str(f['group'])},")
        lines.append("),")
    lines.append("),")
    lines.append("),")

lines.append(");")
lines.append("}")
lines.append("}")

OUT.write_text("\n".join(lines) + "\n", encoding="utf-8")
REPORT.write_text("\n".join(summary) + "\n", encoding="utf-8")

print("\n".join(summary))
