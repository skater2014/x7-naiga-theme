<?php
/**
 * PDF専用HTML
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$plan_label         = get_post_meta($post_id, '_ch_plan_label', true) ?: '参考プラン';
$plan_name          = get_post_meta($post_id, '_ch_plan_name', true) ?: get_the_title($post_id);
$plan_style         = get_post_meta($post_id, '_ch_plan_style', true) ?: '';
$plan_layout        = get_post_meta($post_id, '_ch_plan_layout', true) ?: '';
$plan_catch         = get_post_meta($post_id, '_ch_plan_catch', true) ?: '';
$plan_total_area    = get_post_meta($post_id, '_ch_plan_total_area', true) ?: '';
$plan_tsubo         = get_post_meta($post_id, '_ch_plan_tsubo', true) ?: '';
$plan_building_area = get_post_meta($post_id, '_ch_plan_building_area', true) ?: '';
$plan_family        = get_post_meta($post_id, '_ch_plan_family', true) ?: '';
$plan_description   = get_post_meta($post_id, '_ch_plan_description', true) ?: '';

$exterior_id = (int) get_post_meta($post_id, '_ch_plan_exterior_image_id', true);
$plan_1f_id  = (int) get_post_meta($post_id, '_ch_plan_1f_image_id', true);
$plan_2f_id  = (int) get_post_meta($post_id, '_ch_plan_2f_image_id', true);
$site_id     = (int) get_post_meta($post_id, '_ch_plan_site_image_id', true);

$gallery_raw = get_post_meta($post_id, '_ch_plan_gallery_image_ids', true);
$gallery_ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery_raw))));

if (!$exterior_id && has_post_thumbnail($post_id)) {
    $exterior_id = (int) get_post_thumbnail_id($post_id);
}

function iez_pdf_img($id, $size = 'large') {
    $id = absint($id);
    if (!$id) {
        return '';
    }

    $url = wp_get_attachment_image_url($id, $size);

    if (!$url) {
        return '';
    }

    /*
     * PDF生成コンテナから見ても画像が読めるように、
     * 絶対URLではなく /wp-content/uploads/... の相対パスにする。
     */
    return wp_make_link_relative($url);
}

$feature_lines_raw = get_post_meta($post_id, '_ch_plan_marketing_features', true);

if ($feature_lines_raw === '') {
    $feature_lines_raw = implode("\n", array(
        '耐震性｜構造・基礎・柱の考え方を整理し、安心して暮らせる住まいを目指します。',
        '基礎｜建物を支える基礎部分を重視し、長く住むための土台を整えます。',
        '屋根｜那須の気候や積雪・雨風を考え、住まいを守る屋根計画を検討します。',
        '壁・外装｜外観デザインだけでなく、耐久性やメンテナンス性にも配慮します。',
        '樹脂サッシ｜窓まわりの断熱性を高め、冬の冷え込みや結露対策にもつなげます。',
        '寒冷地対応｜那須エリアの寒さを考え、断熱・窓・換気を含めた快適性を検討します。',
        'ユニットバス｜掃除しやすさ、断熱性、将来の使いやすさを考えた水まわりを選べます。',
        'トイレ｜日常の使いやすさと掃除のしやすさを考え、配置や設備を整理できます。',
        'バリアフリー｜将来の暮らしやすさを考え、段差や動線を整理できます。',
        'ウッドデッキ｜庭とのつながりをつくり、外時間を楽しめる住まいにできます。',
        '駐車スペース｜敷地条件に合わせて、車の出入りや来客時の使いやすさを考えます。',
        '収納計画｜各所に収納を設け、生活感を抑えたすっきりした暮らしを目指します。',
    ));
}

$features = array();

foreach (preg_split('/\r\n|\r|\n/', $feature_lines_raw) as $line) {
    $line = trim($line);

    if ($line === '') {
        continue;
    }

    $parts = array_map('trim', explode('｜', $line, 2));

    $features[] = array(
        'title' => $parts[0],
        'text'  => isset($parts[1]) ? $parts[1] : '',
    );
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html($plan_name); ?> PDF</title>
<?php
/*
 * PDF CSS管理:
 * - ?plan_pdf=1 専用CSSは hub/pages/iezukuri/css/pdf/plan-pdf.css に分離
 * - 通常ページCSS plan-detail-viewer.css とは混ぜない
 * - Playwright PDF生成時もこの外部CSSを読む
 */
$plan_pdf_css_path = get_template_directory() . '/hub/pages/iezukuri/css/pdf/plan-pdf.css';
$plan_pdf_css_uri_abs = get_template_directory_uri() . '/hub/pages/iezukuri/css/pdf/plan-pdf.css';
$plan_pdf_css_uri     = wp_parse_url($plan_pdf_css_uri_abs, PHP_URL_PATH);
if (!$plan_pdf_css_uri) {
    $plan_pdf_css_uri = $plan_pdf_css_uri_abs;
}
$plan_pdf_css_ver  = file_exists($plan_pdf_css_path) ? filemtime($plan_pdf_css_path) : time();
?>
<link rel="stylesheet" href="<?php echo esc_url($plan_pdf_css_uri . '?ver=' . $plan_pdf_css_ver); ?>" media="all">

</head>
<body>
<div class="pdf-book">

  <section class="pdf-sheet pdf-sheet--cover">
    <header class="pdf-header">
    <div class="pdf-brand">内外グループ CUSTOM HOME</div>
    <div><?php echo esc_html(date_i18n('Y/m/d')); ?></div>
  </header>

    <div class="pdf-cover-head">
      

  <p class="pdf-title-small">FLOOR PLAN</p>
  <h1><?php echo esc_html($plan_name); ?></h1>
  <p class="pdf-lead"><?php echo esc_html($plan_description); ?></p>
    </div>

    <div class="pdf-cover-grid">
      <div class="pdf-cover-visual">
        <?php if ($exterior_id && iez_pdf_img($exterior_id, 'large')) : ?>
    <section class="pdf-exterior">
      <img src="<?php echo esc_url(iez_pdf_img($exterior_id, 'large')); ?>" alt="<?php echo esc_attr($plan_name); ?> 外観">
    </section>
  <?php endif; ?>
      </div>
      <div class="pdf-cover-summary">
        <aside class="pdf-summary">
      <p class="pdf-summary-label"><?php echo esc_html($plan_label); ?></p>
      <div class="pdf-summary-title"><?php echo esc_html($plan_name); ?></div>
      <?php if ($plan_style !== '') : ?>
        <p class="pdf-style"><?php echo esc_html($plan_style); ?></p>
      <?php endif; ?>

      <?php if ($plan_catch !== '') : ?>
        <p class="pdf-layout"><?php echo esc_html($plan_catch); ?></p>
      <?php endif; ?>
<dl>
        <div><dt>間取り</dt><dd><?php echo esc_html($plan_layout); ?></dd></div>
        <div><dt>延床面積</dt><dd><?php echo esc_html($plan_total_area); ?>（<?php echo esc_html($plan_tsubo); ?>）</dd></div>
        <div><dt>建築面積</dt><dd><?php echo esc_html($plan_building_area); ?></dd></div>
        <div><dt>想定家族</dt><dd><?php echo esc_html($plan_family); ?></dd></div>
      </dl>
    </aside>
      </div>
    </div>
  </section>

  <?php $has_2f_drawing = ($plan_2f_id && iez_pdf_img($plan_2f_id, 'large')); ?>

  <section class="pdf-sheet pdf-sheet--drawings">
    <div class="pdf-sheet-header">
      <div><?php echo $has_2f_drawing ? '間取り図' : '図面'; ?></div>
      <div><?php echo esc_html($plan_name); ?></div>
    </div>

    <div class="pdf-drawing-pair">
      <div class="pdf-drawing-card pdf-drawing-card--floor">
        <div class="pdf-drawings">
          <?php if ($plan_1f_id && iez_pdf_img($plan_1f_id, 'large')) : ?>
            <div class="pdf-drawing">
              <h2><?php echo $has_2f_drawing ? '1F 間取り図' : '平面図'; ?></h2>
              <img src="<?php echo esc_url(iez_pdf_img($plan_1f_id, 'large')); ?>" alt="<?php echo $has_2f_drawing ? '1F 間取り図' : '平面図'; ?>">
            </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($has_2f_drawing) : ?>
        <div class="pdf-drawing-card pdf-drawing-card--floor">
          <div class="pdf-drawings">
            <div class="pdf-drawing">
              <h2>2F 間取り図</h2>
              <img src="<?php echo esc_url(iez_pdf_img($plan_2f_id, 'large')); ?>" alt="2F 間取り図">
            </div>
          </div>
        </div>
      <?php else : ?>
        <div class="pdf-drawing-card pdf-drawing-card--site">
          <section class="pdf-siteplan-section">
            <h2>配置図</h2>
            <img src="<?php echo esc_url(iez_pdf_img($site_id, 'large')); ?>" alt="配置図">
          </section>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php if ($has_2f_drawing && $site_id && iez_pdf_img($site_id, 'large')) : ?>
    <section class="pdf-sheet pdf-sheet--drawings">
      <div class="pdf-sheet-header">
        <div>配置図</div>
        <div><?php echo esc_html($plan_name); ?></div>
      </div>

      <div class="pdf-drawing-pair">
        <div class="pdf-drawing-card pdf-drawing-card--site">
          <section class="pdf-siteplan-section">
            <h2>配置図</h2>
            <img src="<?php echo esc_url(iez_pdf_img($site_id, 'large')); ?>" alt="配置図">
          </section>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="pdf-sheet pdf-sheet--gallery">
    <div class="pdf-sheet-header">
      <div>内装写真</div>
      <div><?php echo esc_html($plan_name); ?></div>
    </div>

    <section class="pdf-gallery">
      <div class="pdf-gallery-grid">
        <?php foreach (array_slice($gallery_ids, 0, 6) as $gallery_id) : ?>
          <?php $url = iez_pdf_img($gallery_id, 'medium'); ?>
          <?php if ($url) : ?>
            <div class="pdf-gallery-item">
              <img src="<?php echo esc_url($url); ?>" alt="">
              <p>内装写真</p>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
  </section>

  <section class="pdf-sheet pdf-sheet--features">
    <div class="pdf-sheet-header">
      <div>住宅の特徴</div>
      <div><?php echo esc_html($plan_name); ?></div>
    </div>

    <section class="pdf-features">
    <div class="pdf-feature-grid">
      <?php foreach ($features as $feature) : ?>
        <div class="pdf-feature-card">
          <h3><?php echo esc_html($feature['title']); ?></h3>
          <p><?php echo esc_html($feature['text']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  </section>

</div>
</body>
</html>
