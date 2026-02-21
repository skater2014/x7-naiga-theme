<?php

/****************************************
  header77.php
  Webサイトのヘッダー部分を表示するための
  テンプレートファイルです。
 *****************************************/
?>
<!DOCTYPE html>
<html lang='ja'>

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
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-P6LLPHR5');
  </script>
  <!-- End Google Tag Manager -->

  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php if (!is_front_page()) {
            wp_title('::', true, 'right');
          }
          bloginfo('name'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" />-->

  <!-- External Scripts -->
  <script type="module" src="https://cdn.jsdelivr.net/npm/lite-vimeo-embed/+esm"></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script src="https://apis.google.com/js/api.js"></script>






  <?php wp_head(); ?>

  <style>
    /* ========= メニュー共通（全幅） ========= */
    .navg_under {
      position: relative;
      background: #004bba;
      box-sizing: border-box;
      overflow: visible;
      /* 子のサブメニューが切れないように */
    }

    .navg__list {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      padding: 0;
      list-style: none;
      overflow: visible;
      /* 念のため */
    }

    .navg__list>li {
      position: relative;
      /* サブメニューの基準 */
      list-style: none;
    }

    .navg__list>li>a {
      display: block;
      padding: 15px 10px;
      color: var(--link-color-white);
      !important;
      font-weight: bold;
      text-decoration: none;
      transition: background .2s ease, color .2s ease, opacity .2s ease;
    }

    .navg__list>li:hover>a,
    .navg__list>li>a:focus-visible {
      background: rgba(255, 255, 255, .12);
      color: var(--link-color-white);
      outline: none;
    }

    /* ========= サブメニュー（メガ除外）共通デザイン ========= */
    .navg__list ul.sub-menu:not(.mega-sub-menu) {
      display: none !important;
      /* WPが inline で出す display:block を潰す */
      position: absolute;
      top: 100%;
      left: 0;
      min-width: 200px;
      box-sizing: border-box;
      padding: 0;
      /* li にボーダーを付けるのでここは 0 */
      background: #004bba;
      border: 2px solid #fff;
      /* ← 白い角なし枠 */
      border-radius: 0;
      /* ← 角丸なし */
      z-index: 9999;
    }

    .navg__list ul.sub-menu:not(.mega-sub-menu)>li {
      list-style: none;
      margin: 0;
      padding: 0;
      border-bottom: 1px solid #fff;
    }

    .navg__list ul.sub-menu:not(.mega-sub-menu)>li:last-child {
      border-bottom: none;
    }

    .navg__list ul.sub-menu:not(.mega-sub-menu) a {
      display: block;
      padding: 10px 12px;
      font-size: 14px;
      color: #fff;
      text-decoration: none;
      transition: background .2s ease, color .2s ease;
      /* ← トランジション */
    }

    .navg__list ul.sub-menu:not(.mega-sub-menu) li:hover>a {
      background: rgba(255, 255, 255, .15);
    }


    /* ===== PC（≥1200px）：メガだけ常に中央、通常サブは親基準 ===== */
    @media (min-width:1200px) {

      /* PC表示切替 */
      .header-menu-mobile {
        display: none !important;
      }

      .header-menu-pc {
        display: flex !important;
        width: 1200px;
        margin: 0 auto;
      }

      /* 絶対配置の基準はナビバー */
      .navg_under {
        position: relative;
        background: #004bba;
        overflow: visible;
      }

      /* UL は基準にしない（= .navg_under を基準にする） */
      .navg__list {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0;
        display: flex;
        justify-content: center;
        position: static;
        overflow: visible;
      }

      /* 通常サブ = 親 li の直下 */
      .navg__list>li {
        position: relative;
      }

      .navg__list>.menu-item-has-children:hover>ul:not(.mega-sub-menu) {
        display: block !important;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 200px;
        /* 見た目は共通サブメニューのデザインが効く */
      }

      /* メガメニュー = いつでもバー中央（親<li>は基準にしない） */
      .navg__list>.mega-menu-item {
        position: static;
      }

      .navg__list>.mega-menu-item>.mega-sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 50% !important;
        /* 中央基準 */
        transform: translateX(-50%) !important;
        /* 中央へ */
        right: auto !important;

        width: 1180px !important;
        max-width: calc(100vw - 40px);
        /* はみ出し保険 */
        box-sizing: border-box;
        background: #004bba;
        padding: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        z-index: 10000;
      }

      .navg__list>.mega-menu-item:hover>.mega-sub-menu,
      .navg__list>.mega-menu-item:focus-within>.mega-sub-menu {
        display: flex !important;
        flex-wrap: nowrap;
        justify-content: flex-start;
      }

      /* ★インライン style の translate/left を強制リセット */
      .navg__list>.mega-menu-item>.mega-sub-menu[style*="translate"] {
        left: 50% !important;
        transform: translateX(-50%) !important;
      }

      .navg__list>.mega-menu-item>.mega-sub-menu[style*="left"] {
        left: 50% !important;
      }
    }

    /* メガメニューの中を整える（横中央寄せには影響しないが見た目が安定） */
    @media (min-width:1200px) {
      .mega-sub-menu {
        display: none;
        /* hover時にflexで出すのは既存のまま */
        align-items: stretch;
        gap: 20px;
        /* カラム間の余白 */
      }

      /* 各カラムを同じ高さで並べる */
      .mega-sub-menu>.menu-item {
        flex: 1 1 calc(33.333% - 20px);
        min-width: 0;
        display: flex;
        flex-direction: column;
      }

      /* 画像/動画の枠を一定比率にする（高さ固定にしたいなら下のaspect-ratioをコメントアウトしてheightに） */
      .blog-post-media {
        width: 100%;
        aspect-ratio: 16 / 9;
        /* ←比率固定。高さ固定にしたいなら `height: 180px;` に変更 */
        /* height: 180px; */
        /* ←こちらを使う場合は aspect-ratio は消す */
        overflow: hidden;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .blog-post-media img,
      .blog-post-media lite-youtube,
      .blog-post-media lite-vimeo,
      .blog-post-media iframe {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* 画像トリミングで枠にフィット */
        display: block;
      }

      .mega-sub-menu .blog-post-title {
        display: block;
        margin-top: 8px;
        padding: 10px;
        background: #f8f8f8;
        color: #333;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
      }

      .mega-sub-menu .menu-item a.blog-post-title:hover {
        color: #004bba;
      }
    }

    /* ========= 表示切替：PC(hover) / SP&Tablet(click) ========= */

    /* PC: hover で表示 */
    @media (min-width:1200px) {
      .header-menu-mobile {
        display: none !important;
      }

      .header-menu-pc {
        display: flex !important;
        width: 1200px;
        margin: 0 auto;
      }

      /* 通常ドロップダウン */
      .navg__list>.menu-item-has-children:hover>ul:not(.mega-sub-menu) {
        display: block !important;
        position: absolute;
        top: 100%;
        left: 0;
      }

      /* メガメニュー */
      .mega-menu-item:hover>.mega-sub-menu,
      .mega-menu-item:focus-within>.mega-sub-menu {
        display: flex !important;
        flex-wrap: nowrap;
        justify-content: flex-start;
      }
    }

    /* SP & Tablet: クリック(.open/.show)で表示、位置はフロー内 */
    /* ========= SP & Tablet（～1199px）：中央寄せ＋クリック展開 ========= */
    @media (max-width:1199px) {
      .header-menu-mobile {
        display: flex !important;
      }

      .header-menu-pc {
        display: none !important;
      }

      .navg__list {
        display: flex;
        width: 100%;
        margin: 0;
        padding: 0;
        justify-content: center;
        /* ← 中央寄せ */
        align-items: stretch;
        gap: 6px;
        /* 均等すぎを防ぐ隙間 */
        background: #004bba;
        overflow: visible;
      }

      .navg__list>li {
        flex: 0 0 auto;
        position: relative;
      }

      /* ← 伸びない */
      .navg__list>li>a {
        line-height: var(--nav-height, 48px);
        padding: 0 12px;
      }

      /* クリック展開（.open / .show をJSや既存コードが付与） */
      .navg__list .menu-item-has-children.open>.sub-menu,
      .navg__list .menu-item-has-children.show>.sub-menu {
        display: block !important;
      }

      /* タッチ幅では hover 色を無効化（誤反応防止） */
      @media (max-width:1024px) {

        .navg__list>li:hover>a,
        .navg__list .sub-menu li:hover>a {
          background: transparent !important;
        }
      }
    }

    /* （任意）横向きタブレット以上では hover も許可したい場合 */
    @media (min-width:992px) and (max-width:1199px) {
      .navg__list>.menu-item-has-children:hover>.sub-menu {
        display: block !important;
        position: absolute;
        top: 100%;
        left: 0;
      }
    }


    /* ================================
   スマホだけ固定：上段＋下段ナビ
   （サイドメニュー/電話/ダーク/パンくず すべて表示）
================================ */
    /* 1) テーマ別の色を“変数”で管理 */
    :root {
      --header-bg: #fff;
      --header-fg: #111;
    }

    .dark-theme {
      --header-bg: #1d1d1d;
      --header-fg: #f5f5f5;
    }

    /* 2) モバイルだけヘッダー固定＋変数で色を適用 */
    /* モバイルだけ横並び（～767.98px） */
    @media (max-width:767.98px) {}

    /* 3) モバイル以外は固定を解除（念のため） */
    @media (min-width:768px) {
      .header__top {
        position: static;
        left: auto;
        transform: none;
        background: var(--header-bg);
        color: var(--header-fg);
      }
    }


    /* === モバイル：header-wrapper を fixed。押し下げは実測値で === */
    /* ============================
   モバイルだけ適用（～767.98px）
   ============================ */
    /*@media (max-width: 767.98px){
  /* ① ヘッダー全体を1つだけ fixed にする（上段＋ナビをまとめて固定） */
    /*.header-wrapper{
    position: fixed;
    top: var(--admin-offset, 0px); /* ← 管理バーの高さぶんだけ下げる（JSが値を入れる） */
    /*left: 0; right: 0;
    z-index: 23000;
    background: var(--header-bg);
  }

  /* ② 子ども側（.header__top / .navg_under）では fixed を使わない
        → 固定が二重になるとズレるので、flow（普通の配置）に戻す */
    /*.header__top, .header__top_under, .navg_under{
    position: static !important;
  }

  /* ③ 本文の先頭に「固定ヘッダーの高さぶんの余白」を入れる
        値は JS が --hdr-spacer に自動で入れてくれる */
    /*header.l-header + :is(.home-view, .home-slider, .page-wrapper, .full-width-page){
    padding-top: var(--hdr-spacer, 0px) !important;
    margin-top: 0 !important;
  }
}


/* モバイル〜タブレットは親ひとつだけ fixed（子の fixed は無効化） */
    /* スマホ〜タブレット（〜1199.98px）で、親ひとつだけ fixed */
    /* =========================================================
  目的（スマホ〜タブレットだけ）
  ---------------------------------------------------------
  1) ヘッダー全体（.header-wrapper）を画面上に固定する
  2) 固定したぶん本文が上に重ならないよう、「ヘッダーの高さ」だけ
     本文の上に余白（padding-top）を自動で入れる
  3) 子要素側の fixed/sticky は一旦オフにして、親だけ固定に統一
========================================================= */

    @media (max-width: 1199.98px) {
      /* ←スマホ〜タブレット用のルール */

      /* ① 親ひとつだけ固定（.header-wrapper が“箱ごと”固定される） */
      .header-wrapper {
        position: fixed;
        /* 画面に貼り付く（スクロールしても動かない） */
        top: var(--admin-offset, 0px);
        /* WPの黒バー高さぶん下げる（JSが数値を入れる） */
        left: 0;
        right: 0;
        /* 横いっぱいに広げる */
        z-index: 23000;
        /* 手前に出す（メニューが隠れない） */
        background: var(--header-bg);
        /* 下が透けないよう背景色を付ける */

        /* ★ポイント：
       flow-root = この要素を“独立した箱（BFC）”にする。
       → 子どもの margin-top が親の外に食い込む（相殺）問題を防げる。 */
        display: flow-root;
        /* 子の上マージン食い込み防止（BFCを作る） */
      }

      /* ② 子側の fixed/sticky を無効化
        親を固定にしたので、子の固定はオフにして高さズレを防ぐ */
      .header__top,
      .header__top_under,
      .navg_under {
        position: static !important;
        /* “普通の配置”に戻す（top/left等は効かない） */
      }

      /* ③ 本文側を“ヘッダーの高さぶん”だけ下げる
        - header.l-header の「あと」に出てくる兄弟要素のうち、
          候補クラス（.home-view / .home-slider / .page-wrapper / .full-width-page /
          .store-reservation-page-content）の“最初の1つ”にだけ当てる
        - padding-top に JSが入れた --hdr-spacer（ヘッダー実高さ）をそのまま使う
        - margin-top:0 は、先頭の余計な上マージン対策（相殺の保険） */
      header.l-header~ :is(.home-view,
        .home-slider,
        .page-wrapper,
        .full-width-page,
        .store-reservation-page-content):first-of-type {
        padding-top: var(--hdr-spacer, 0px) !important;
        /* ← ここが“押し下げ”の本体 */
        margin-top: 0 !important;
        /* ← マージン相殺のズレ防止 */
      }

      /* ④ さらに安全策：
        .page-wrapper の“直下の最初の子”に margin-top があれば 0 にして食い込み防止
        >  … 直下の子に限定
        *  … タグは何でもOK（div, section, p など）
        :first-child … 最初の1つだけ */
      .page-wrapper>*:first-child {
        margin-top: 0 !important;
        /* 先頭子の上マージン食い込みを止める */
      }
    }






    /* ← ここで終わり。余計な '}' は置かない */


    /* ================================
   細かいバグ潰し（共通）
================================ */

    /* color の !important の位置が誤っていたので修正 */
    .navg__list>li>a {
      color: var(--link-color-white) !important;
    }

    /* PC/Tabで固定しない（念のための打ち消し） */
    @media (min-width: 768px) {
      .navg_under {
        position: relative;
        top: auto;
      }
    }
  </style>


</head>

<body <?php body_class(); ?>>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P6LLPHR5" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <header id="head" class="l-header"><!-- /#head START -->
    <div class="header-wrapper"><!-- /.header-wrapper START -->

      <!-- タッチメニュー（.header__top の“外”に配置） -->
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

      <?php if (! wp_is_mobile()) : ?>
        <div class="logo-container"><!-- /.logo-container START -->
          <div class="customizer-logo"><!-- /.customizer-logo START -->
            <?php
            // 現在の投稿/ページ情報の安全取得
            $page_obj   = get_post();
            $page_slug  = $page_obj ? get_post_field('post_name', $page_obj) : '';
            $is_front   = is_front_page();

            // カテゴリーのスラッグ配列
            $cats = get_the_category();
            $cat_slugs = array();
            if (! empty($cats) && ! is_wp_error($cats)) {
              foreach ($cats as $c) {
                $cat_slugs[] = $c->slug;
              }
            }

            // 初期化
            $logo_url = $logo_link = $logo_width = $logo_height = '';

            // 表示ロジック
            if (! $is_front && (in_array('naigai-tochi', $cat_slugs, true) || strpos((string)$page_slug, 'naigai-tochi') !== false)) {
              $logo_url    = get_theme_mod('dess_logo_genshin', '');
              $logo_link   = get_theme_mod('logo_link_genshin_select', '');
              $logo_width  = get_theme_mod('logo_width_genshin', '');
              $logo_height = get_theme_mod('logo_height_genshin', '');
            } elseif (! $is_front && (in_array('naigai-construction', $cat_slugs, true) || strpos((string)$page_slug, 'naigai-construction') !== false)) {
              $logo_url    = get_theme_mod('dess_logo_tekken7', '');
              $logo_link   = get_theme_mod('logo_link_tekken7_select', '');
              $logo_width  = get_theme_mod('logo_width_tekken7', '');
              $logo_height = get_theme_mod('logo_height_tekken7', '');
            }

            // 出力
            if ($logo_url) {
              if ($logo_link) echo '<a href="' . esc_url($logo_link) . '">';
              echo '<img src="' . esc_url($logo_url) . '" alt="Logo" width="' . esc_attr($logo_width) . '" height="' . esc_attr($logo_height) . '" />';
              if ($logo_link) echo '</a>';
            }
            ?>
          </div><!-- /.customizer-logo END -->
        </div><!-- /.logo-container END -->
      <?php endif; ?>


      <!-- management-logo は使わないので完全停止 -->
      <?php /* management-logo disabled (do not output)
    <div class="management-logo"> ... </div>
    */ ?>

      <div class="header__top header__top_under"><!-- /.header__top START -->

        <!-- 電話（SPのみ表示。PC/TabはCSSで非表示） -->
        <?php $mobilePhoneTest = get_theme_mod('mobile_phone_test'); ?>
        <?php if ($mobilePhoneTest) : ?>
          <div class="phone-container"><!-- /.phone-container START -->
            <a href="tel:<?php echo esc_attr($mobilePhoneTest); ?>" class="icon icon-phone" id="phone-link">
              <svg class="icon icon-phone" width="24" height="24">
                <use xlink:href="#icon-phone"></use>
              </svg>
              <span class="phone-text">TEL</span>
            </a>
          </div><!-- /.phone-container END -->
        <?php endif; ?>

        <!-- サイドバー（タッチメニューの制御対象） -->
        <div class="sidebar-navigation" id="sidebar-navigation"><!-- /.sidebar-navigation START -->
          <?php if (is_active_sidebar('custom-widget-area')) {
            dynamic_sidebar('custom-widget-area');
          } ?>
          <?php my_custom_menu(); ?>
        </div><!-- /.sidebar-navigation END -->

        <!-- ダークモードトグル -->
        <div class="color-mode"><!-- /.color-mode START -->
          <input id="btn-mode" type="checkbox">Dark Mode
          <label for="btn-mode" class="btn-switch"></label>
        </div><!-- /.color-mode END -->

        <!-- パンくず ＋ 検索 -->
        <div class="top-header"><!-- /.top-header START -->
          <?php if (is_home() || is_page() || is_single() || is_category() || is_tag() || is_search() || is_404()) : ?>
            <?php breadcrumb1(); ?>
          <?php else : ?>
            <?php breadcrumb2(); ?>
          <?php endif; ?>

          <div class="search-form-container"><!-- /.search-form-container START -->
            <?php include(get_template_directory() . '/searchform20.php'); ?>
          </div><!-- /.search-form-container END -->
        </div><!-- /.top-header END -->

      </div><!-- /.header__top END -->

      <nav class="navg navg_under">
        <?php
        // モバイル用メニュー
        wp_nav_menu(array(
          'theme_location' => 'header-menu-mobile',
          'container' => false,
          'menu_class' => 'navg__list header-menu-mobile',
          'items_wrap' => '<ul class="%2$s">%3$s</ul>',
        ));

        // PC用メニュー（メガメニュー適用）
        wp_nav_menu(array(
          'theme_location' => 'header-menu-pc',
          'container' => false,
          'menu_class' => 'navg__list header-menu-pc',
          'items_wrap' => '<ul class="%2$s">%3$s</ul>',
          //'walker' => new PC_Custom_MegaMenu_Walker() // ← `new` をつける
        ));
        ?>
      </nav>



    </div><!-- /.header-wrapper END -->
  </header><!-- /#head END -->


  <?php
  // 現在のページのスラッグを取得
  $page_slug = get_post_field('post_name', get_post());
  // 条件: ギャラリーページまたは genshin-impact-characters ページテンプレートの場合
  if ($page_slug == 'tekken-gallery' || is_page_template('page-tekken7-character.php') || is_page_template('genshin-impact-characters.php')) :
    echo '<div class="home-view">';
    require get_template_directory() . '/inc/functions/swiper-slider.php';
    echo '</div>';
  endif;
  ?>

  <?php // Home Slider 読み込み 
  ?>
  <?php
  // ホームページやブログページに表示、contactページを除外
  if (is_front_page() || is_page('home')) {
    require get_template_directory() . '/inc/functions/home-slider.php';
  } elseif (is_page('naigai-tochi')) {
    require get_template_directory() . '/inc/functions/category-slider-tochi.php';
  } elseif (is_page('naigai-construction')) {
    require get_template_directory() . '/inc/functions/category-slider-construction.php';
  } elseif (is_post_type_archive('house')) {
    require get_template_directory() . '/inc/functions/category-slider-house.php';
  } elseif (is_tag() || is_search() || is_404()) {
    require get_template_directory() . '/inc/functions/home-slider.php';
  } else {
    // var_dump('該当するページが見つかりませんでした。');
  }
  ?>

  <!-- ページラッパー開始 -->
  <?php
  // "room-gallary" のページなら、`full-width-page` クラスを追加
  $page_wrapper_class = is_page('room-gallary') ? 'full-width-page' : 'page-wrapper';
  ?>
  <div class="<?php echo esc_attr($page_wrapper_class); ?>">


    <!-- 残りの本文コンテンツ -->
    <?php // 本文コンテンツはここに入ります 
    ?>