<?php

/****************************************
  header-77.php
  Webサイトのヘッダー部分を表示するための
  テンプレートファイルです。
 *****************************************/

// 現在のクエリ対象から安全に情報取得
$queried_obj = get_queried_object();

$current_page_slug = '';
$cat_slugs = array();
$is_front = is_front_page();

if ($queried_obj instanceof WP_Post) {
  $current_page_slug = (string) $queried_obj->post_name;

  $cats = get_the_category($queried_obj->ID);
  if (!empty($cats) && !is_wp_error($cats)) {
    foreach ($cats as $c) {
      $cat_slugs[] = $c->slug;
    }
  }
} elseif ($queried_obj instanceof WP_Term) {
  $current_page_slug = (string) $queried_obj->slug;
  $cat_slugs[] = $queried_obj->slug;
} else {
  $post_obj = get_post();
  if ($post_obj instanceof WP_Post) {
    $current_page_slug = (string) get_post_field('post_name', $post_obj);
    $cats = get_the_category($post_obj->ID);
    if (!empty($cats) && !is_wp_error($cats)) {
      foreach ($cats as $c) {
        $cat_slugs[] = $c->slug;
      }
    }
  }
}

// ロゴ設定
$logo_url = '';
$logo_link = '';
$logo_width = '';
$logo_height = '';

if (
  !$is_front &&
  (
    in_array('naigai-tochi', $cat_slugs, true) ||
    strpos($current_page_slug, 'naigai-tochi') !== false
  )
) {
  $logo_url    = get_theme_mod('dess_logo_genshin', '');
  $logo_link   = get_theme_mod('logo_link_genshin_select', '');
  $logo_width  = get_theme_mod('logo_width_genshin', '');
  $logo_height = get_theme_mod('logo_height_genshin', '');
} elseif (
  !$is_front &&
  (
    in_array('naigai-construction', $cat_slugs, true) ||
    strpos($current_page_slug, 'naigai-construction') !== false
  )
) {
  $logo_url    = get_theme_mod('dess_logo_tekken7', '');
  $logo_link   = get_theme_mod('logo_link_tekken7_select', '');
  $logo_width  = get_theme_mod('logo_width_tekken7', '');
  $logo_height = get_theme_mod('logo_height_tekken7', '');
}

$mobile_phone = get_theme_mod('dess_phone');

/*
 * NAIGAI_GTM_ENV_SWITCH
 *
 * 【アクセス解析の環境分離】
 *
 * このPHPでは「GA4の測定ID」ではなく、
 * 読み込む「GTMコンテナID」だけを環境ごとに切り替える。
 *
 * ローカル:
 *   127.0.0.1 / localhost
 *   → GTM-5BWSL67B
 *
 * 本番:
 *   naigaicorp.net
 *   → GTM-P6LLPHR5
 *
 * GA4の測定IDはここでは管理しない。
 * 各GTMコンテナ内の「Google タグ」で管理する。
 *
 * そのため、ローカルで行うStripe TEST決済やGA4テストデータが
 * 本番用GTM / GA4へ混ざらない構成になっている。
 *
 * ローカル環境と本番環境でGTMコンテナを分離する。
 * ローカルのStripe TEST / GA4テストデータを
 * 本番Analyticsへ混ぜないための切替。
 */
$naigai_gtm_host = wp_parse_url(home_url('/'), PHP_URL_HOST);

$naigai_gtm_id = in_array(
    $naigai_gtm_host,
    array('127.0.0.1', 'localhost'),
    true
)
    ? 'GTM-5BWSL67B'
    : 'GTM-P6LLPHR5';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l !== 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?php echo esc_js($naigai_gtm_id); ?>');
  </script>
  <!-- End Google Tag Manager -->

  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- External Scripts -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script src="https://apis.google.com/js/api.js"></script>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe
      src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($naigai_gtm_id); ?>"
      height="0"
      width="0"
      style="display:none;visibility:hidden"></iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->

  <header id="head" class="l-header"><!-- /#head START -->
    <div class="header-wrapper"><!-- /.header-wrapper START -->

      <div class="header__top header__top_under"><!-- /.header__top START -->

        <!-- 開く -->
        <span
          id="touch-menu"
          class="mobile-menu"
          role="button"
          aria-label="メニューを開く"
          aria-controls="sidebar-navigation"
          aria-expanded="false"
          tabindex="0">
          <span aria-hidden="true"></span>
        </span><!-- /#touch-menu END -->

        <!-- ロゴ（PCのみ） -->
        <?php if (!wp_is_mobile()) : ?>
          <div class="logo-container"><!-- /.logo-container START -->
            <div class="customizer-logo"><!-- /.customizer-logo START -->
              <?php if ($logo_url) : ?>
                <?php if ($logo_link) : ?>
                  <a href="<?php echo esc_url($logo_link); ?>">
                  <?php endif; ?>

                  <img
                    src="<?php echo esc_url($logo_url); ?>"
                    alt="Logo"
                    <?php if ($logo_width) : ?>width="<?php echo esc_attr($logo_width); ?>" <?php endif; ?>
                    <?php if ($logo_height) : ?>height="<?php echo esc_attr($logo_height); ?>" <?php endif; ?> />

                  <?php if ($logo_link) : ?>
                  </a>
                <?php endif; ?>
              <?php endif; ?>
            </div><!-- /.customizer-logo END -->
          </div><!-- /.logo-container END -->
        <?php endif; ?>

        <!-- 電話（SP表示想定） -->
        <?php if ($mobile_phone) : ?>
          <div class="phone-container"><!-- /.phone-container START -->
            <a href="tel:<?php echo esc_attr($mobile_phone); ?>" class="icon icon-phone" id="phone-link">
              <svg class="icon icon-phone" width="24" height="24" aria-hidden="true">
                <use xlink:href="#icon-phone"></use>
              </svg>
              <span class="phone-text">TEL</span>
            </a>
          </div><!-- /.phone-container END -->
        <?php endif; ?>

        <!-- サイドバー本体 -->
        <div class="sidebar-navigation" id="sidebar-navigation"><!-- /.sidebar-navigation START -->

          <div class="drawer-head"><!-- /.drawer-head START -->
            <span
              class="drawer-close"
              role="button"
              aria-label="メニューを閉じる"
              tabindex="0">
              <span aria-hidden="true"></span>
              <span aria-hidden="true"></span>
            </span><!-- /.drawer-close END -->
          </div><!-- /.drawer-head END -->

          <div class="drawer-body"><!-- /.drawer-body START -->
            <?php if (is_active_sidebar('custom-widget-area')) : ?>
              <?php dynamic_sidebar('custom-widget-area'); ?>
            <?php endif; ?>

            <?php my_custom_menu(); ?>
          </div><!-- /.drawer-body END -->
        </div><!-- /.sidebar-navigation END -->

        <!-- PCバナー枠 -->
        <div class="header-banner-slot" aria-hidden="true"></div><!-- /.header-banner-slot END -->

        <div class="color-mode"><!-- /.color-mode START -->
          <input id="btn-mode" type="checkbox" aria-label="ダークモード切り替え">
          <label for="btn-mode" class="btn-switch"></label>
          <span class="color-mode-label">Dark Mode</span>
        </div><!-- /.color-mode END -->

        <!-- パンくず -->
        <div class="top-header"><!-- /.top-header START -->
          <?php
          if (function_exists('breadcrumb1')) {
            breadcrumb1();
          }
          ?>
        </div><!-- /.top-header END -->

        <!-- 検索 -->
        <div class="search-form-container"><!-- /.search-form-container START -->
          <?php include get_template_directory() . '/searchform20.php'; ?>
        </div><!-- /.search-form-container END -->

      </div><!-- /.header__top END -->

      <nav class="navg navg_under">
        <?php
        /**
         * ============================================================
         * HEADER MENU LOCATION : SINGLE SOURCE
         * ヘッダーメニューのロケーション名はここだけで管理する。
         * ============================================================
         *
         * 【通常ページ】
         * PC     : header-menu-pc
         * Mobile : header-menu-mobile
         *
         * 【民泊ページ】
         * PC     : minpaku-header-menu-pc
         * Mobile : minpaku-header-menu-mobile
         *
         * 重要:
         * - ここではメニューを新規作成しない。
         * - WordPress管理画面のメニュー割当も変更しない。
         * - set_theme_mod() 等でDBを書き換えない。
         * - ページ種別によって「使用するロケーション」だけ切り替える。
         */

        $header_menu_locations = array(

          // 通常の不動産・会社案内など
          'default' => array(
            'pc'     => 'header-menu-pc',
            'mobile' => 'header-menu-mobile',
          ),

          // 民泊関連ページ
          'minpaku' => array(
            'pc'     => 'minpaku-header-menu-pc',
            'mobile' => 'minpaku-header-menu-mobile',
          ),

        );

        /**
         * 民泊ヘッダーを使用するページ判定
         */
        $is_minpaku_header = (
          is_singular('minpaku')
          || is_post_type_archive('minpaku')
          || is_page_template('page-minpaku-b2c.php')
          || is_page_template('page-minpaku-support.php')
          || is_page('minpaku')
          || is_page('minpaku-stay')
        );

        /**
         * 使用するロケーションセットを決定
         */
        $current_header_locations = $is_minpaku_header
          ? $header_menu_locations['minpaku']
          : $header_menu_locations['default'];

        /**
         * MOBILE HEADER MENU
         *
         * 通常 : header-menu-mobile
         * 民泊 : minpaku-header-menu-mobile
         */
        wp_nav_menu(array(
          'theme_location' => $current_header_locations['mobile'],
          'container'      => false,
          'menu_class'     => 'navg__list header-menu-mobile',
          'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
          'fallback_cb'    => false,
        ));

        /**
         * PC HEADER MENU
         *
         * 通常 : header-menu-pc
         * 民泊 : minpaku-header-menu-pc
         */
        wp_nav_menu(array(
          'theme_location' => $current_header_locations['pc'],
          'container'      => false,
          'menu_class'     => 'navg__list header-menu-pc',
          'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
          'fallback_cb'    => false,
        ));
        ?>
      </nav>

    </div><!-- /.header-wrapper END -->
  </header>
<!-- /#head END -->

  <?php
  $slider_page_slug = '';
  $post_obj_for_slider = get_post();
  if ($post_obj_for_slider instanceof WP_Post) {
    $slider_page_slug = (string) get_post_field('post_name', $post_obj_for_slider);
  }

  if (false) :
    echo '<div class="home-view">';
    require get_template_directory() . '/inc/functions/swiper-slider.php';
    echo '</div>';
  endif;
  ?>

  <?php
  if (false && (is_front_page() || is_page('home'))) {
    require get_template_directory() . '/inc/functions/home-slider.php';
  } elseif (is_page('naigai-tochi')) {
    require get_template_directory() . '/inc/functions/category-slider-tochi.php';
  } elseif (is_page('naigai-construction')) {
    require get_template_directory() . '/inc/functions/category-slider-construction.php';
  } elseif (is_post_type_archive('house')) {
    require get_template_directory() . '/inc/functions/category-slider-house.php';
  } elseif (is_tag() || is_search() || is_404()) {
    require get_template_directory() . '/inc/functions/home-slider.php';
  }
  ?>

  <?php
  $page_wrapper_class = is_page('room-gallary') ? 'full-width-page' : 'page-wrapper';
  ?>
  <div class="<?php echo esc_attr($page_wrapper_class); ?>">
