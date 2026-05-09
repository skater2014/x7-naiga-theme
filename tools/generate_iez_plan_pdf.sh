#!/bin/bash
set -euo pipefail

SLUG="${1:-}"

if [ -z "$SLUG" ]; then
  echo "Usage: tools/generate_iez_plan_pdf.sh compact-hiraya-pa"
  exit 1
fi

THEME_DIR="/Users/kaz/development/wp-local-naiga/wp-content/themes/x7-naigaicorp"
UPLOAD_DIR="/Users/kaz/development/wp-local-naiga/wp-content/uploads/iezukuri-pdf"
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

URL="http://127.0.0.1:8080/iezukuri/plan/${SLUG}?plan_pdf=1"
OUT="${UPLOAD_DIR}/${SLUG}.pdf"

mkdir -p "$UPLOAD_DIR"

"$CHROME" \
  --headless \
  --disable-gpu \
  --no-sandbox \
  --print-to-pdf="$OUT" \
  "$URL"

echo "PDF generated:"
echo "$OUT"
echo
echo "URL:"
echo "http://127.0.0.1:8080/wp-content/uploads/iezukuri-pdf/${SLUG}.pdf"
