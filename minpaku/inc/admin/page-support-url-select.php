<?php
/**
 * 民泊運営サポートLP URL候補UI
 *
 * 対象:
 * - 固定ページ slug=minpaku の編集画面だけ
 *
 * 役割:
 * - URL入力欄の下に候補セレクトを追加
 * - DBが空の入力欄に初期文言を表示
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    if (!$post_id) {
        return;
    }

    $slug = (string) get_post_field('post_name', $post_id);
    if ($slug !== 'minpaku') {
        return;
    }

    wp_enqueue_script('jquery');

    $choices = array(
        home_url('/minpaku-stay/') => '民泊一覧 /minpaku-stay/',
        home_url('/minpaku-stay/room/test-minpaku1/') => 'テスト民泊詳細 /minpaku-stay/room/test-minpaku1/',
        home_url('/contact/') => 'お問い合わせ /contact/',
    );

    $defaults = array(
        '_mps_hero_primary_text'   => '民泊運営を相談する',
        '_mps_hero_primary_url'    => home_url('/contact/'),
        '_mps_hero_secondary_text' => '宿泊施設を見る',
        '_mps_hero_secondary_url'  => home_url('/minpaku-stay/'),

        '_mps_detail_primary_text'   => '宿泊施設を見る',
        '_mps_detail_primary_url'    => home_url('/minpaku-stay/'),
        '_mps_detail_secondary_text' => 'オンライン決済を見る',
        '_mps_detail_secondary_url'  => home_url('/minpaku-stay/room/test-minpaku1/'),

        '_mps_contact_cta_text' => '民泊運営を相談する',
        '_mps_contact_cta_url'  => home_url('/contact/'),
    );

    $choices_json  = wp_json_encode($choices, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $defaults_json = wp_json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    wp_add_inline_script('jquery', <<<JS
jQuery(function($) {
  var choices = {$choices_json};
  var defaults = {$defaults_json};

  $.each(defaults, function(key, value) {
    var field = $('[name="mps_meta[' + key + ']"]');
    if (field.length && !field.val()) {
      field.val(value);
    }
  });

  var urlKeys = [
    '_mps_hero_primary_url',
    '_mps_hero_secondary_url',
    '_mps_detail_primary_url',
    '_mps_detail_secondary_url',
    '_mps_support_primary_url',
    '_mps_support_secondary_url',
    '_mps_contact_cta_url'
  ];

  function makeSelect(current) {
    var select = $('<select class="mps-url-select-safe" style="margin-top:8px;width:100%;max-width:100%;"></select>');
    select.append($('<option></option>').val('').text('URL候補から選択'));

    $.each(choices, function(url, label) {
      var option = $('<option></option>').val(url).text(label);
      if (current === url) {
        option.prop('selected', true);
      }
      select.append(option);
    });

    return select;
  }

  urlKeys.forEach(function(key) {
    var input = $('[name="mps_meta[' + key + ']"]');
    if (!input.length) {
      return;
    }
    if (input.next('.mps-url-select-safe').length) {
      return;
    }
    makeSelect(input.val()).insertAfter(input);
  });

  $(document).on('change', '.mps-url-select-safe', function() {
    var url = $(this).val();
    if (!url) {
      return;
    }
    $(this).prevAll('input[type="url"], input[type="text"]').first().val(url);
  });
});
JS);
}, 99);
