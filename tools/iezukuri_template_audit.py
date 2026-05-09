from pathlib import Path
import re
import json
from datetime import datetime

ROOT = Path(".")
REPORT = Path("docs/iezukuri-template-audit.md")
REGISTRY = Path("docs/iezukuri-section-registry-candidate.json")

TARGET_FILES = []

for pattern in [
    "page-construction-hub-sub.php",
    "hub/pages/iezukuri/**/*.php",
    "hub/templates/**/*.php",
    "hub/partials/**/*.php",
    "hub/inc/customhome-*.php",
]:
    TARGET_FILES.extend(ROOT.glob(pattern))

TARGET_FILES = sorted(set([p for p in TARGET_FILES if p.is_file()]))

def read(p):
    return p.read_text(encoding="utf-8", errors="ignore")

def find_sections(text):
    found = []
    for m in re.finditer(r'<section[^>]*class=["\']([^"\']+)["\'][^>]*>', text, flags=re.I):
        found.append(m.group(1))
    return found

def find_classes(text):
    classes = set()
    for m in re.finditer(r'class=["\']([^"\']+)["\']', text, flags=re.I):
        for c in m.group(1).split():
            if c.startswith("ch-") or c.startswith("hub-") or c.startswith("naigai-"):
                classes.add(c)
    return sorted(classes)

def find_meta_keys(text):
    keys = set()
    for m in re.finditer(r"get_post_meta\s*\([^,]+,\s*['\"]([^'\"]+)['\"]", text):
        keys.add(m.group(1))
    for m in re.finditer(r"name=[\"']([^\"']+)[\"']", text):
        name = m.group(1)
        if name.startswith("_ch_") or name.startswith("_hub_ch_") or name.startswith("naigai_ch_"):
            keys.add(name)
    return sorted(keys)

def find_functions(text):
    funcs = []
    for m in re.finditer(r"function\s+(naigai_[a-zA-Z0-9_]+)\s*\(", text):
        funcs.append(m.group(1))
    return funcs

def infer_part_name(path, text, classes, funcs):
    p = str(path)

    if "page-construction-hub-sub.php" in p:
        return "subpage_shell"

    if "contact" in p and ("contact-form" in text or "contact_flow" in text or "contact-flow" in text):
        return "contact"

    if "company-info" in p or "company-map" in text:
        return "company_access"

    if "plan-tabs" in p or "_ch_plan_" in text:
        return "plan_layout"

    if "cta" in p or "ch-cta" in text or "_hub_ch_cta" in text:
        return "cta"

    if "section-builder" in p or "_ch_builder_sections_json" in text:
        return "section_builder"

    if "layout-fields" in p:
        return "layout_fields"

    if "phase2" in p:
        return "media_gallery_or_works"

    if "page-" in path.name:
        return path.stem.replace("page-", "page_body_")

    if any("flow" in c for c in classes):
        return "flow"

    if any("faq" in c for c in classes):
        return "faq"

    if any("gallery" in c or "works" in c for c in classes):
        return "gallery_or_works"

    return "unknown"

audit = []
registry = {}

for path in TARGET_FILES:
    text = read(path)
    classes = find_classes(text)
    sections = find_sections(text)
    metas = find_meta_keys(text)
    funcs = find_functions(text)
    part = infer_part_name(path, text, classes, funcs)

    audit.append({
        "file": str(path),
        "part": part,
        "sections": sections,
        "classes": classes,
        "meta_keys": metas,
        "functions": funcs,
    })

    registry.setdefault(part, {
        "part": part,
        "files": [],
        "classes": set(),
        "meta_keys": set(),
        "functions": set(),
    })

    registry[part]["files"].append(str(path))
    registry[part]["classes"].update(classes)
    registry[part]["meta_keys"].update(metas)
    registry[part]["functions"].update(funcs)

# set -> list
registry_out = {}
for part, data in registry.items():
    registry_out[part] = {
        "label": part,
        "files": sorted(data["files"]),
        "classes": sorted(data["classes"]),
        "meta_keys": sorted(data["meta_keys"]),
        "functions": sorted(data["functions"]),
    }

REGISTRY.write_text(json.dumps(registry_out, ensure_ascii=False, indent=2), encoding="utf-8")

lines = []
lines.append("# iezukuri template audit")
lines.append("")
lines.append(f"- generated: {datetime.now().isoformat(timespec='seconds')}")
lines.append(f"- files scanned: {len(TARGET_FILES)}")
lines.append("")
lines.append("## 台帳パーツ候補")
lines.append("")

for part, data in sorted(registry_out.items()):
    lines.append(f"### {part}")
    lines.append("")
    lines.append("#### files")
    for f in data["files"]:
        lines.append(f"- `{f}`")

    if data["functions"]:
        lines.append("")
        lines.append("#### functions")
        for fn in data["functions"]:
            lines.append(f"- `{fn}()`")

    if data["meta_keys"]:
        lines.append("")
        lines.append("#### meta keys")
        for key in data["meta_keys"]:
            lines.append(f"- `{key}`")

    if data["classes"]:
        lines.append("")
        lines.append("#### classes")
        for c in data["classes"][:80]:
            lines.append(f"- `{c}`")

    lines.append("")

lines.append("## ファイル別詳細")
lines.append("")

for item in audit:
    lines.append(f"### {item['file']}")
    lines.append(f"- inferred part: `{item['part']}`")

    if item["functions"]:
        lines.append("- functions: " + ", ".join(f"`{x}()`" for x in item["functions"]))

    if item["meta_keys"]:
        lines.append("- meta keys: " + ", ".join(f"`{x}`" for x in item["meta_keys"][:40]))

    if item["classes"]:
        lines.append("- classes: " + ", ".join(f"`{x}`" for x in item["classes"][:40]))

    lines.append("")

REPORT.write_text("\n".join(lines), encoding="utf-8")

print(f"generated: {REPORT}")
print(f"generated: {REGISTRY}")
print("")
print("parts:")
for part in sorted(registry_out.keys()):
    print("-", part)
