<?php
if (!defined(ABSPATH)) {
    exit;
}

/**
 * =========================================================
 * layout-hero.php
 * 役割:
 * - hero を全セクションと同じ骨格で出す
 * - 幅管理は main.mnpk-page-shell 側に任せる
 * - ここでは section-layer / section-inner のみを出す
 * =========================================================
 */

$section = is_array($args ?? null) ? $args : array();

$eyebrow   = trim((string) ($section[eyebrow] ?? ));
$title     = trim((string) ($section[title] ?? get_the_title()));
$lead      = trim((string) ($section[lead] ?? ));
$image_url = esc_url((string) ($section[image_url] ?? ));
$actions   = is_array($section[actions] ?? null) ? $section[actions] : array();

$hero_classes = array(mnpk-page-section-layer, is-hero);
if ($image_url !== ) {
    $hero_classes[] = has-image;
}
?>
<section class="mnpk-page-section mnpk-page-section--hero">
    <div class="<?php echo esc_attr(implode(
