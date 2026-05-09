#!/bin/bash
set -euo pipefail

cd /Users/kaz/development/wp-local-naiga/wp-content/themes/x7-naigaicorp

SLUGS=$(docker exec -i wp-local-naiga-wordpress-1 bash -lc 'cd /var/www/html && php <<'\''PHP'\''
<?php
require_once "/var/www/html/wp-load.php";

$q = new WP_Query(array(
    "post_type"      => "iez_plan",
    "post_status"    => "publish",
    "posts_per_page" => -1,
    "orderby"        => "ID",
    "order"          => "ASC",
));

foreach ($q->posts as $post) {
    echo $post->post_name . "\n";
}
PHP')

for slug in $SLUGS; do
  echo "=== generate: $slug ==="
  tools/generate_iez_plan_pdf.sh "$slug"
done
