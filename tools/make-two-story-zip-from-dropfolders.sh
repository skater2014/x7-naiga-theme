#!/bin/zsh

THEME="/Users/kaz/development/wp-local-naiga/wp-content/themes/x7-naigaicorp"

PLAN_SLUG="compact-2story"
BASE_NAME="two-story-plan-images"
VERSION="${1:-v03}"

DROP="$HOME/Downloads/two-story-drop"
WORK="$HOME/Downloads/${BASE_NAME}_${VERSION}"
ZIP="$HOME/Downloads/${BASE_NAME}_${VERSION}.zip"
INCOMING="$THEME/_incoming"

SLOTS=(
  "01_exterior:two-story_01_work_exterior_${VERSION}.png"
  "02_ldk:two-story_02_interior_ldk_${VERSION}.png"
  "03_bedroom:two-story_03_interior_bedroom_${VERSION}.png"
  "04_bathroom:two-story_04_interior_bathroom_${VERSION}.png"
  "05_plan_1f:two-story_05_plan_1f_${VERSION}.png"
  "06_plan_2f:two-story_05_plan_2f_${VERSION}.png"
  "07_site:two-story_06_plan_site_${VERSION}.png"
)

echo "=== prepare drop folders ==="
mkdir -p "$DROP"

for item in "${SLOTS[@]}"; do
  slot="${item%%:*}"
  mkdir -p "$DROP/$slot"
done

cat > "$DROP/README.txt" <<TXT
ここに画像を入れてください。

01_exterior  外観
02_ldk       LDK内装
03_bedroom   主寝室・居室
04_bathroom  洗面・浴室
05_plan_1f   1F平面図
06_plan_2f   2F平面図
07_site      配置図

各フォルダーに画像を1枚ずつ入れてから、
もう一度このスクリプトを実行してください。
TXT

missing=0

echo
echo "=== check images ==="

for item in "${SLOTS[@]}"; do
  slot="${item%%:*}"

  src="$(find "$DROP/$slot" -maxdepth 1 -type f \( -iname "*.png" -o -iname "*.jpg" -o -iname "*.jpeg" \) | sort | sed -n '1p')"

  if [ -z "$src" ]; then
    echo "MISSING: $DROP/$slot に画像がありません"
    missing=1
  else
    echo "OK: $slot -> $src"
  fi
done

if [ "$missing" = "1" ]; then
  echo
  echo "まだ画像が足りません。上の MISSING フォルダーに画像を入れてから再実行してください。"
  echo "DROP: $DROP"
  return 1
fi

echo
echo "=== create work folder ==="
rm -rf "$WORK"
mkdir -p "$WORK"

for item in "${SLOTS[@]}"; do
  slot="${item%%:*}"
  target="${item#*:}"

  src="$(find "$DROP/$slot" -maxdepth 1 -type f \( -iname "*.png" -o -iname "*.jpg" -o -iname "*.jpeg" \) | sort | sed -n '1p')"

  echo "convert: $slot -> $target"
  sips -s format png "$src" --out "$WORK/$target" >/dev/null
done

cat > "$WORK/manifest.json" <<JSON
{
  "plan_slug": "compact-2story",
  "post_type": "iez_plan",
  "taxonomy": {
    "iez_plan_type": [
      {
        "slug": "two-story",
        "name": "2階建て"
      }
    ]
  },
  "post": {
    "title": "コンパクト2階建てプラン",
    "style": "ローコスト2階建て",
    "layout": "2LDK",
    "total_area": "68.5㎡",
    "tsubo": "約20.7坪",
    "building_area": "68.5㎡"
  },
  "slots": {
    "exterior": "two-story_01_work_exterior_${VERSION}.png",
    "gallery": [
      "two-story_02_interior_ldk_${VERSION}.png",
      "two-story_03_interior_bedroom_${VERSION}.png",
      "two-story_04_interior_bathroom_${VERSION}.png"
    ],
    "plan_1f": "two-story_05_plan_1f_${VERSION}.png",
    "plan_2f": "two-story_05_plan_2f_${VERSION}.png",
    "site": "two-story_06_plan_site_${VERSION}.png"
  }
}
JSON

cat > "$WORK/README-import.txt" <<TXT
compact-2story / 2階建て住宅用ZIP

外観:
_ch_plan_exterior_image_id
_thumbnail_id

内装3枚:
_ch_plan_gallery_image_ids

1F平面図:
_ch_plan_1f_image_id
_ch_plan_floor_image_id

2F平面図:
_ch_plan_2f_image_id

配置図:
_ch_plan_site_image_id
TXT

echo
echo "=== zip ==="
rm -f "$ZIP"
cd "$HOME/Downloads" || return 1
zip -qr "$(basename "$ZIP")" "$(basename "$WORK")"

echo
echo "=== copy zip to theme _incoming by Finder ==="
mkdir -p "$INCOMING"

osascript <<OSA
tell application "Finder"
  set srcFile to POSIX file "${ZIP}" as alias
  set dstFolder to POSIX file "${INCOMING}" as alias
  duplicate srcFile to dstFolder with replacing
end tell
OSA

echo
echo "DONE"
echo "ZIP: $ZIP"
echo "INCOMING: $INCOMING/$(basename "$ZIP")"
