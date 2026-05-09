<?php
/**
 * =========================================================
 * /fudousan 管理画面
 * hub/pages/fudousan/admin/fudousan-admin.php
 * =========================================================
 *
 * 方針:
 * - 画像ID番号は画面に見せない
 * - 画像はサムネイルで表示
 * - mp4 は動画プレビューで表示
 * - YouTube は URL / ID 入力対応
 * - 保存先は /fudousan 固定ページの post_meta
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!function_exists('naigai_fudo_admin_page_id')) {
  function naigai_fudo_admin_page_id() {
    $page = get_page_by_path('fudousan');
    return $page ? (int) $page->ID : 0;
  }
}

if (!function_exists('naigai_fudo_admin_get')) {
  function naigai_fudo_admin_get($page_id, $key, $default = '') {
    $value = get_post_meta($page_id, $key, true);
    return ($value === '' || $value === null) ? $default : $value;
  }
}

if (!function_exists('naigai_fudo_admin_clean_ids')) {
  function naigai_fudo_admin_clean_ids($value) {
    $ids = array_filter(array_map('absint', preg_split('/[\s,]+/', (string) $value)));
    return implode(',', array_values(array_unique($ids)));
  }
}

add_action('admin_menu', function () {
  add_menu_page(
    '不動産ページ設定',
    '不動産ページ設定',
    'edit_pages',
    'naigai-fudousan-settings',
    'naigai_fudo_admin_render_page',
    'dashicons-admin-home',
    58
  );
});

add_action('admin_enqueue_scripts', function ($hook) {
  /*
   * /fudousan 管理画面 assets
   *
   * 対象:
   * - 左メニュー「不動産ページ設定」
   * - 固定ページ /fudousan の編集画面
   *
   * 役割:
   * - 管理画面専用CSS/JSだけを読む。
   * - wp_enqueue_media() はメディアライブラリ選択に必要。
   *
   * 注意:
   * - isotope / fudousan-page.js / fudousan.css はフロント用なのでここでは読まない。
   */
  $is_settings_page = ($hook === 'toplevel_page_naigai-fudousan-settings');

  $is_fudo_edit_page = false;

  if ($hook === 'post.php' || $hook === 'post-new.php') {
    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    $post = $post_id ? get_post($post_id) : null;
    $is_fudo_edit_page = naigai_fudousan_is_target_admin_page($post);
  }

  if (!$is_settings_page && !$is_fudo_edit_page) {
    return;
  }

  $theme_dir = get_template_directory();
  $theme_uri = get_template_directory_uri();

  $admin_css = '/hub/pages/fudousan/admin/fudousan-admin.css';
  if (file_exists($theme_dir . $admin_css)) {
    wp_enqueue_style(
      'naigai-fudousan-admin',
      $theme_uri . $admin_css,
      array(),
      filemtime($theme_dir . $admin_css)
    );
  }

  if ($is_settings_page) {
    wp_enqueue_media();

    $admin_js = '/hub/pages/fudousan/admin/fudousan-admin.js';
    if (file_exists($theme_dir . $admin_js)) {
      wp_enqueue_script(
        'naigai-fudousan-admin',
        $theme_uri . $admin_js,
        array('jquery'),
        filemtime($theme_dir . $admin_js),
        true
      );
    }
  }
});

if (!function_exists('naigai_fudo_admin_text_field')) {
  function naigai_fudo_admin_text_field($page_id, $key, $label, $default = '', $type = 'text') {
    $value = naigai_fudo_admin_get($page_id, $key, $default);
    ?>
    <tr>
      <th scope="row">
        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
      </th>
      <td>
        <?php if ($type === 'textarea') : ?>
          <textarea id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php else : ?>
          <input id="<?php echo esc_attr($key); ?>" type="text" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php endif; ?>
      </td>
    </tr>
    <?php
  }
}

if (!function_exists('naigai_fudo_admin_image_field')) {
  function naigai_fudo_admin_image_field($page_id, $key, $label, $multiple = true) {
    $value = naigai_fudo_admin_get($page_id, $key, '');
    $ids = array_filter(array_map('absint', preg_split('/[\s,]+/', (string) $value)));
    ?>
    <tr>
      <th scope="row"><?php echo esc_html($label); ?></th>
      <td>
        <input
          type="hidden"
          id="<?php echo esc_attr($key); ?>"
          name="<?php echo esc_attr($key); ?>"
          value="<?php echo esc_attr(implode(',', $ids)); ?>"
          data-fudo-media-input
          data-preview="#<?php echo esc_attr($key); ?>_preview"
        >

        <div id="<?php echo esc_attr($key); ?>_preview" class="fudo-admin-preview fudo-admin-preview--images">
          <?php if (!empty($ids)) : ?>
            <?php foreach ($ids as $id) : ?>
              <?php $src = wp_get_attachment_image_url($id, 'thumbnail'); ?>
              <?php if ($src) : ?>
                <span class="fudo-admin-preview__thumb">
                  <img src="<?php echo esc_url($src); ?>" alt="">
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="fudo-admin-preview__empty">画像が選択されていません。</p>
          <?php endif; ?>
        </div>

        <p>
          <button type="button" class="button button-primary"
            data-fudo-select-media
            data-target="#<?php echo esc_attr($key); ?>"
            data-type="image"
            data-multiple="<?php echo $multiple ? '1' : '0'; ?>">
            画像を選択
          </button>

          <button type="button" class="button"
            data-fudo-clear-media
            data-target="#<?php echo esc_attr($key); ?>">
            クリア
          </button>
        </p>

        <p class="description">画像ID番号は表示しません。複数選択するとフロントでSwiperになります。</p>
      </td>
    </tr>
    <?php
  }
}

if (!function_exists('naigai_fudo_admin_video_field')) {
  function naigai_fudo_admin_video_field($page_id, $key, $label) {
    $id = absint(naigai_fudo_admin_get($page_id, $key, 0));
    $url = $id ? wp_get_attachment_url($id) : '';
    ?>
    <tr>
      <th scope="row"><?php echo esc_html($label); ?></th>
      <td>
        <input
          type="hidden"
          id="<?php echo esc_attr($key); ?>"
          name="<?php echo esc_attr($key); ?>"
          value="<?php echo esc_attr($id); ?>"
          data-fudo-media-input
          data-preview="#<?php echo esc_attr($key); ?>_preview"
        >

        <div id="<?php echo esc_attr($key); ?>_preview" class="fudo-admin-preview fudo-admin-preview--video">
          <?php if ($url) : ?>
            <video src="<?php echo esc_url($url); ?>" controls muted playsinline></video>
          <?php else : ?>
            <p class="fudo-admin-preview__empty">mp4 が選択されていません。</p>
          <?php endif; ?>
        </div>

        <p>
          <button type="button" class="button button-primary"
            data-fudo-select-media
            data-target="#<?php echo esc_attr($key); ?>"
            data-type="video"
            data-multiple="0">
            mp4を選択
          </button>

          <button type="button" class="button"
            data-fudo-clear-media
            data-target="#<?php echo esc_attr($key); ?>">
            クリア
          </button>
        </p>

        <p class="description">動画ID番号は表示しません。メディアライブラリから mp4 を選択します。</p>
      </td>
    </tr>
    <?php
  }
}

if (!function_exists('naigai_fudo_admin_render_page')) {
  function naigai_fudo_admin_render_page() {
    $page_id = naigai_fudo_admin_page_id();

    if (!$page_id) {
      echo '<div class="wrap"><h1>不動産ページ設定</h1><p><code>/fudousan</code> 固定ページが見つかりません。</p></div>';
      return;
    }

    $fields_text = array(
      '_fudo_hero_kicker',
      '_fudo_hero_title',
      '_fudo_hero_lead',
      '_fudo_hero_youtube_id',
      '_fudo_list_kicker',
      '_fudo_list_title',
      '_fudo_list_note',
      '_fudo_default_category_name',
      '_fudo_cta_kicker',
      '_fudo_cta_title',
      '_fudo_cta_text',
      '_fudo_cta_primary_label',
      '_fudo_cta_primary_url',
      '_fudo_cta_secondary_label',
      '_fudo_cta_secondary_url',
    );

    $fields_media = array(
      '_fudo_hero_image_ids',
      '_fudo_hero_mp4_id',
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['naigai_fudo_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['naigai_fudo_nonce'])), 'naigai_fudo_save')) {
      if (!current_user_can('edit_page', $page_id)) {
        wp_die('権限がありません。');
      }

      foreach ($fields_text as $key) {
        $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';

        if (str_ends_with($key, '_url')) {
          update_post_meta($page_id, $key, esc_url_raw($value));
        } elseif (in_array($key, array('_fudo_hero_lead', '_fudo_list_note', '_fudo_cta_text'), true)) {
          update_post_meta($page_id, $key, sanitize_textarea_field($value));
        } else {
          update_post_meta($page_id, $key, sanitize_text_field($value));
        }
      }

      update_post_meta($page_id, '_fudo_hero_image_ids', naigai_fudo_admin_clean_ids($_POST['_fudo_hero_image_ids'] ?? ''));
      update_post_meta($page_id, '_fudo_hero_mp4_id', absint($_POST['_fudo_hero_mp4_id'] ?? 0));

      echo '<div class="notice notice-success is-dismissible"><p>保存しました。</p></div>';
    }

    ?>
    <div class="wrap fudo-admin-wrap">
      <h1>不動産ページ設定</h1>
      <p>ここで設定した内容は <code>/fudousan</code> の Hero / 見出し / CTA に反映します。</p>
      <form method="post">
        <?php wp_nonce_field('naigai_fudo_save', 'naigai_fudo_nonce'); ?>

        <h2>Hero</h2>
        <table class="form-table" role="presentation">
          <?php
          naigai_fudo_admin_text_field($page_id, '_fudo_hero_kicker', 'Hero 小見出し', 'Nasu Real Estate');
          naigai_fudo_admin_text_field($page_id, '_fudo_hero_title', 'Hero 見出し', '那須の土地・住まい・不動産相談');
          naigai_fudo_admin_text_field($page_id, '_fudo_hero_lead', 'Hero 本文', '那須エリアを中心に、土地探し・住宅購入・賃貸・売却相談まで。暮らしと事業に合わせた不動産情報をご案内します。', 'textarea');
          naigai_fudo_admin_image_field($page_id, '_fudo_hero_image_ids', 'Hero 画像', true);
          naigai_fudo_admin_video_field($page_id, '_fudo_hero_mp4_id', 'Hero mp4');
          naigai_fudo_admin_text_field($page_id, '_fudo_hero_youtube_id', 'Hero YouTube URL / ID', '');
          ?>
        </table>

        <h2>一覧見出し</h2>
        <table class="form-table" role="presentation">
          <?php
          naigai_fudo_admin_text_field($page_id, '_fudo_list_kicker', '一覧 小見出し', 'Property & Area Column');
          naigai_fudo_admin_text_field($page_id, '_fudo_list_title', '一覧 見出し', 'カテゴリー別で探す');
          naigai_fudo_admin_text_field($page_id, '_fudo_list_note', '一覧 本文', '管理画面で選択したカテゴリーを切り替えて表示します。', 'textarea');
          naigai_fudo_admin_text_field($page_id, '_fudo_default_category_name', '初期表示カテゴリ名', '内外土地開発');
          ?>
        </table>

        <h2>CTA</h2>
        <table class="form-table" role="presentation">
          <?php
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_kicker', 'CTA 小見出し', 'Contact');
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_title', 'CTA 見出し', '那須の不動産について相談する');
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_text', 'CTA 本文', '土地探し、住宅購入、賃貸、売却・買取、資料請求など、目的に合わせてご相談ください。', 'textarea');
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_primary_label', 'ボタン1 ラベル', 'お問い合わせ');
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_primary_url', 'ボタン1 URL', home_url('/contact/'));
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_secondary_label', 'ボタン2 ラベル', '資料請求');
          naigai_fudo_admin_text_field($page_id, '_fudo_cta_secondary_url', 'ボタン2 URL', home_url('/contact/'));
          ?>
        </table>

        <?php submit_button('保存する'); ?>
      </form>
    </div>
    <?php
  }
}

/* =========================================================
 * FUDOUSAN REMOVE OLD PAGE METABOXES
 * 対象:
 * - 固定ページ /fudousan の編集画面だけ
 *
 * 目的:
 * - 昔のメタボックスを表示しない
 * - 新しい左メニュー「不動産ページ設定」で管理する
 *
 * 注意:
 * - DBのメタ値は消さない
 * - 他の固定ページには影響させない
 * - 公開ボックス / 固定ページ属性 / アイキャッチなどの標準ボックスは残す
 * ========================================================= */

if (!function_exists('naigai_fudousan_is_target_admin_page')) {
  function naigai_fudousan_is_target_admin_page($post) {
    if (!$post || empty($post->ID) || $post->post_type !== 'page') {
      return false;
    }

    if ($post->post_name === 'fudousan') {
      return true;
    }

    $fudo_page = get_page_by_path('fudousan');
    return ($fudo_page && (int) $fudo_page->ID === (int) $post->ID);
  }
}

add_action('add_meta_boxes_page', function ($post) {
  if (!naigai_fudousan_is_target_admin_page($post)) {
    return;
  }

  global $wp_meta_boxes;

  if (empty($wp_meta_boxes['page']) || !is_array($wp_meta_boxes['page'])) {
    return;
  }

  /*
   * WordPress標準で残すもの。
   * 古いテーマ独自メタボックスはここに入れないので消える。
   */
  $keep_ids = array(
    'submitdiv',          // 公開
    'pageparentdiv',      // 固定ページ属性
    'postimagediv',       // アイキャッチ
    'slugdiv',            // スラッグ
    'revisionsdiv',       // リビジョン
    'authordiv',          // 投稿者
  );

  foreach ($wp_meta_boxes['page'] as $context => $priorities) {
    if (!is_array($priorities)) {
      continue;
    }

    foreach ($priorities as $priority => $boxes) {
      if (!is_array($boxes)) {
        continue;
      }

      foreach ($boxes as $box_id => $box) {
        if (!in_array($box_id, $keep_ids, true)) {
          unset($wp_meta_boxes['page'][$context][$priority][$box_id]);
        }
      }
    }
  }
}, 9999);

/*
 * /fudousan 固定ページ編集画面の余白調整は
 * hub/pages/fudousan/admin/fudousan-admin.css に分離済み。
 */
