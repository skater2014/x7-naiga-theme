<?php
/**
 * =========================================================
 * Front Page Hub
 * =========================================================
 *
 * 目的:
 * - フロントページを「総合窓口」として構成する
 * - hero はメタボックス管理
 * - hero は Swiper 対応
 * - 物件ピックアップは既存 Isotope / dess_home_cats を壊さない
 * - 会社ニュースが無ければ company_news を用意
 *
 * 既存JSを動かすために残すもの:
 * - hero: .swiper / .js-hub-swiper / .swiper-wrapper / .swiper-slide
 * - 物件タブ: #dess_home_cats / data-filter
 * - 物件カード: .archive-post-box / .term-{ID}
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 会社ニュース CPT
 * =========================================================
 *
 * 既に company_news が存在する場合は何もしない。
 * 無い場合だけ、管理画面に「会社ニュース」を追加。
 */
add_action('init', function () {
    if (post_type_exists('company_news')) {
        return;
    }

    register_post_type('company_news', array(
        'labels' => array(
            'name'          => '会社ニュース',
            'singular_name' => '会社ニュース',
            'add_new_item'  => '会社ニュースを追加',
            'edit_item'     => '会社ニュースを編集',
        ),
        'public'        => true,
        'has_archive'   => false,
        'menu_position' => 22,
        'menu_icon'     => 'dashicons-megaphone',
        'supports'      => array('title', 'editor', 'excerpt', 'thumbnail'),
        'show_in_rest'  => true,
        'rewrite'       => array('slug' => 'news'),
    ));
});

/**
 * =========================================================
 * フロント hero / 3カード meta keys
 * =========================================================
 */
function naigai_front_page_keys()
{
    return array(
        // hero source
        'hero_source'       => '_hub_front_hero_source',
        'hero_ref_page_id'  => '_hub_front_hero_ref_page_id',
        'hero_feature_id'   => '_hub_front_hero_feature_post_id',

        // self hero
        'hero_kicker'       => '_hub_front_hero_kicker',
        'hero_title'        => '_hub_front_hero_title',
        'hero_lead'         => '_hub_front_hero_lead',
        'hero_image_ids'    => '_hub_front_hero_image_ids',

        // hero CTA
        'hero_primary_label'   => '_hub_front_hero_primary_label',
        'hero_primary_url'     => '_hub_front_hero_primary_url',
        'hero_secondary_label' => '_hub_front_hero_secondary_label',
        'hero_secondary_url'   => '_hub_front_hero_secondary_url',

        // gateway cards
        'card1_title' => '_hub_front_card1_title',
        'card1_text'  => '_hub_front_card1_text',
        'card1_url'   => '_hub_front_card1_url',
        'card1_image' => '_hub_front_card1_image_id',

        'card2_title' => '_hub_front_card2_title',
        'card2_text'  => '_hub_front_card2_text',
        'card2_url'   => '_hub_front_card2_url',
        'card2_image' => '_hub_front_card2_image_id',

        'card3_title' => '_hub_front_card3_title',
        'card3_text'  => '_hub_front_card3_text',
        'card3_url'   => '_hub_front_card3_url',
        'card3_image' => '_hub_front_card3_image_id',
    );
}

/**
 * =========================================================
 * 管理画面 metabox
 * =========================================================
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'naigai_front_page_hub_meta',
        'フロントページ 総合窓口設定',
        'naigai_front_page_hub_meta_box',
        'page',
        'normal',
        'high'
    );
});

function naigai_front_page_hub_meta_box($post)
{
    $keys = naigai_front_page_keys();

    wp_nonce_field('naigai_front_page_hub_save', 'naigai_front_page_hub_nonce');

    $v = function ($key, $default = '') use ($post) {
        $value = get_post_meta($post->ID, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    };

    $hero_source = $v($keys['hero_source'], 'self');

    ?>
    <style>
      .naigai-front-admin {
        display: grid;
        gap: 16px;
      }
      .naigai-front-admin__box {
        border: 1px solid #dcdcde;
        background: #fff;
        padding: 14px;
      }
      .naigai-front-admin__box h3 {
        margin: 0 0 12px;
      }
      .naigai-front-admin__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
      }
      .naigai-front-admin__field {
        margin: 0 0 14px;
      }
      .naigai-front-admin__field:last-child {
        margin-bottom: 0;
      }
      .naigai-front-admin label {
        display: block;
        margin: 0 0 6px;
        font-weight: 700;
      }
      .naigai-front-admin input[type="text"],
      .naigai-front-admin input[type="url"],
      .naigai-front-admin input[type="number"],
      .naigai-front-admin textarea,
      .naigai-front-admin select {
        width: 100%;
        max-width: 820px;
      }
      .naigai-front-admin__help {
        margin: 5px 0 0;
        color: #666;
        font-size: 12px;
      }
      @media (max-width: 900px) {
        .naigai-front-admin__grid {
          grid-template-columns: 1fr;
        }
      }
    </style>

    <div class="naigai-front-admin">

      <div class="naigai-front-admin__box">
        <h3>Hero 表示元</h3>

        <div class="naigai-front-admin__field">
          <label for="hub_front_hero_source">hero表示元</label>
          <select id="hub_front_hero_source" name="<?php echo esc_attr($keys['hero_source']); ?>">
            <option value="self" <?php selected($hero_source, 'self'); ?>>このフロントページ専用hero</option>
            <option value="page" <?php selected($hero_source, 'page'); ?>>他のhub固定ページheroを流用</option>
            <option value="feature" <?php selected($hero_source, 'feature'); ?>>feature投稿IDをheroに使う</option>
          </select>
          <p class="naigai-front-admin__help">
            self = このページ専用 / page = construction-hub 等のheroメタ流用 / feature = 投稿・物件・施工事例ID
          </p>
        </div>

        <div class="naigai-front-admin__grid">
          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_ref_page_id">他ページhero流用：固定ページID</label>
            <input type="number" id="hub_front_hero_ref_page_id" name="<?php echo esc_attr($keys['hero_ref_page_id']); ?>" value="<?php echo esc_attr($v($keys['hero_ref_page_id'])); ?>" min="0">
          </div>

          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_feature_post_id">feature投稿ID</label>
            <input type="number" id="hub_front_hero_feature_post_id" name="<?php echo esc_attr($keys['hero_feature_id']); ?>" value="<?php echo esc_attr($v($keys['hero_feature_id'])); ?>" min="0">
            <p class="naigai-front-admin__help">
              page_featured_type / page_video_id / アイキャッチを参照。
            </p>
          </div>
        </div>
      </div>

      <div class="naigai-front-admin__box">
        <h3>このページ専用 Hero</h3>

        <div class="naigai-front-admin__field">
          <label for="hub_front_hero_kicker">小見出し</label>
          <input type="text" id="hub_front_hero_kicker" name="<?php echo esc_attr($keys['hero_kicker']); ?>" value="<?php echo esc_attr($v($keys['hero_kicker'])); ?>" placeholder="NAIGAI REAL ESTATE / CONSTRUCTION / STAY">
        </div>

        <div class="naigai-front-admin__field">
          <label for="hub_front_hero_title">タイトル</label>
          <input type="text" id="hub_front_hero_title" name="<?php echo esc_attr($keys['hero_title']); ?>" value="<?php echo esc_attr($v($keys['hero_title'])); ?>" placeholder="不動産・住宅建設・民泊ステイの総合窓口">
        </div>

        <div class="naigai-front-admin__field">
          <label for="hub_front_hero_lead">説明文</label>
          <textarea id="hub_front_hero_lead" name="<?php echo esc_attr($keys['hero_lead']); ?>" rows="4" placeholder="土地探しから建築、賃貸、民泊ステイの運営、土地・建物の活用まで。"><?php echo esc_textarea($v($keys['hero_lead'])); ?></textarea>
        </div>

        <div class="naigai-front-admin__field">
          <label for="hub_front_hero_image_ids">Hero画像ID 複数可</label>
          <input type="text" id="hub_front_hero_image_ids" name="<?php echo esc_attr($keys['hero_image_ids']); ?>" value="<?php echo esc_attr($v($keys['hero_image_ids'])); ?>" placeholder="123,456,789">
          <p class="naigai-front-admin__help">
            画像IDをカンマ区切り。2枚以上でSwiper。空なら _hub_hero_image_ids → アイキャッチの順で使用。
          </p>
        </div>

        <div class="naigai-front-admin__grid">
          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_primary_label">メインCTA文言</label>
            <input type="text" id="hub_front_hero_primary_label" name="<?php echo esc_attr($keys['hero_primary_label']); ?>" value="<?php echo esc_attr($v($keys['hero_primary_label'])); ?>" placeholder="不動産を見る">
          </div>

          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_primary_url">メインCTA URL</label>
            <input type="url" id="hub_front_hero_primary_url" name="<?php echo esc_attr($keys['hero_primary_url']); ?>" value="<?php echo esc_url($v($keys['hero_primary_url'])); ?>" placeholder="<?php echo esc_url(home_url('/fudousan/')); ?>">
          </div>

          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_secondary_label">サブCTA文言</label>
            <input type="text" id="hub_front_hero_secondary_label" name="<?php echo esc_attr($keys['hero_secondary_label']); ?>" value="<?php echo esc_attr($v($keys['hero_secondary_label'])); ?>" placeholder="住宅建設を見る">
          </div>

          <div class="naigai-front-admin__field">
            <label for="hub_front_hero_secondary_url">サブCTA URL</label>
            <input type="url" id="hub_front_hero_secondary_url" name="<?php echo esc_attr($keys['hero_secondary_url']); ?>" value="<?php echo esc_url($v($keys['hero_secondary_url'])); ?>" placeholder="<?php echo esc_url(home_url('/construction-hub/')); ?>">
          </div>
        </div>
      </div>

      <div class="naigai-front-admin__box">
        <h3>Hero直下 3カード</h3>

        <div class="naigai-front-admin__grid">
          <?php for ($i = 1; $i <= 3; $i++) : ?>
            <div class="naigai-front-admin__field">
              <label>カード<?php echo esc_html($i); ?> タイトル</label>
              <input type="text" name="<?php echo esc_attr($keys["card{$i}_title"]); ?>" value="<?php echo esc_attr($v($keys["card{$i}_title"])); ?>">
            </div>

            <div class="naigai-front-admin__field">
              <label>カード<?php echo esc_html($i); ?> URL</label>
              <input type="url" name="<?php echo esc_attr($keys["card{$i}_url"]); ?>" value="<?php echo esc_url($v($keys["card{$i}_url"])); ?>">
            </div>

            <div class="naigai-front-admin__field">
              <label>カード<?php echo esc_html($i); ?> 画像ID</label>
              <input type="number" name="<?php echo esc_attr($keys["card{$i}_image"]); ?>" value="<?php echo esc_attr($v($keys["card{$i}_image"])); ?>" min="0">
            </div>

            <div class="naigai-front-admin__field">
              <label>カード<?php echo esc_html($i); ?> 説明</label>
              <textarea name="<?php echo esc_attr($keys["card{$i}_text"]); ?>" rows="3"><?php echo esc_textarea($v($keys["card{$i}_text"])); ?></textarea>
            </div>
          <?php endfor; ?>
        </div>
      </div>

    </div>
    <?php
}

/**
 * =========================================================
 * 保存
 * =========================================================
 */
add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['naigai_front_page_hub_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['naigai_front_page_hub_nonce'], 'naigai_front_page_hub_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $keys = naigai_front_page_keys();

    $source = isset($_POST[$keys['hero_source']]) ? sanitize_key($_POST[$keys['hero_source']]) : 'self';
    if (!in_array($source, array('self', 'page', 'feature'), true)) {
        $source = 'self';
    }

    update_post_meta($post_id, $keys['hero_source'], $source);
    update_post_meta($post_id, $keys['hero_ref_page_id'], isset($_POST[$keys['hero_ref_page_id']]) ? absint($_POST[$keys['hero_ref_page_id']]) : 0);
    update_post_meta($post_id, $keys['hero_feature_id'], isset($_POST[$keys['hero_feature_id']]) ? absint($_POST[$keys['hero_feature_id']]) : 0);

    update_post_meta($post_id, $keys['hero_kicker'], isset($_POST[$keys['hero_kicker']]) ? sanitize_text_field($_POST[$keys['hero_kicker']]) : '');
    update_post_meta($post_id, $keys['hero_title'], isset($_POST[$keys['hero_title']]) ? sanitize_text_field($_POST[$keys['hero_title']]) : '');
    update_post_meta($post_id, $keys['hero_lead'], isset($_POST[$keys['hero_lead']]) ? sanitize_textarea_field($_POST[$keys['hero_lead']]) : '');
    update_post_meta($post_id, $keys['hero_image_ids'], isset($_POST[$keys['hero_image_ids']]) ? sanitize_text_field($_POST[$keys['hero_image_ids']]) : '');

    update_post_meta($post_id, $keys['hero_primary_label'], isset($_POST[$keys['hero_primary_label']]) ? sanitize_text_field($_POST[$keys['hero_primary_label']]) : '');
    update_post_meta($post_id, $keys['hero_primary_url'], isset($_POST[$keys['hero_primary_url']]) ? esc_url_raw($_POST[$keys['hero_primary_url']]) : '');
    update_post_meta($post_id, $keys['hero_secondary_label'], isset($_POST[$keys['hero_secondary_label']]) ? sanitize_text_field($_POST[$keys['hero_secondary_label']]) : '');
    update_post_meta($post_id, $keys['hero_secondary_url'], isset($_POST[$keys['hero_secondary_url']]) ? esc_url_raw($_POST[$keys['hero_secondary_url']]) : '');

    for ($i = 1; $i <= 3; $i++) {
        update_post_meta($post_id, $keys["card{$i}_title"], isset($_POST[$keys["card{$i}_title"]]) ? sanitize_text_field($_POST[$keys["card{$i}_title"]]) : '');
        update_post_meta($post_id, $keys["card{$i}_text"], isset($_POST[$keys["card{$i}_text"]]) ? sanitize_textarea_field($_POST[$keys["card{$i}_text"]]) : '');
        update_post_meta($post_id, $keys["card{$i}_url"], isset($_POST[$keys["card{$i}_url"]]) ? esc_url_raw($_POST[$keys["card{$i}_url"]]) : '');
        update_post_meta($post_id, $keys["card{$i}_image"], isset($_POST[$keys["card{$i}_image"]]) ? absint($_POST[$keys["card{$i}_image"]]) : 0);
    }
});

/**
 * =========================================================
 * helper
 * =========================================================
 */
function naigai_front_csv_ids($value)
{
    if (is_array($value)) {
        return array_values(array_filter(array_map('absint', $value)));
    }

    return array_values(array_filter(array_map('absint', explode(',', (string) $value))));
}

function naigai_front_meta($post_id, $key, $default = '')
{
    $value = get_post_meta($post_id, $key, true);
    return ($value !== '' && $value !== null) ? $value : $default;
}

function naigai_front_hub_get($post_id, $key, $default = '')
{
    if (function_exists('naigai_hub_get')) {
        return naigai_hub_get($post_id, $key, $default);
    }

    return naigai_front_meta($post_id, $key, $default);
}

function naigai_front_page_cta_url($post_id, $url_key, $page_id_key, $default = '')
{
    $url = naigai_front_hub_get($post_id, $url_key, '');
    if ($url) {
        return $url;
    }

    $page_id = (int) naigai_front_hub_get($post_id, $page_id_key, 0);
    if ($page_id) {
        return get_permalink($page_id);
    }

    return $default;
}

function naigai_front_page_image_ids_from_hub($post_id)
{
    $ids = naigai_front_hub_get($post_id, '_hub_hero_image_ids', '');
    $ids = naigai_front_csv_ids($ids);

    if (!empty($ids)) {
        return $ids;
    }

    $thumb_id = get_post_thumbnail_id($post_id);
    return $thumb_id ? array($thumb_id) : array();
}

/**
 * =========================================================
 * hero data
 * =========================================================
 */
function naigai_front_page_hero_data($front_id)
{
    $front_id = (int) $front_id;
    $keys = naigai_front_page_keys();

    $source = naigai_front_meta($front_id, $keys['hero_source'], 'self');

    $data = array(
        'source'          => $source,
        'kicker'          => '',
        'title'           => '',
        'lead'            => '',
        'slides'          => array(),
        'primary_label'   => '',
        'primary_url'     => '',
        'secondary_label' => '',
        'secondary_url'   => '',
    );

    /**
     * 他ページhero流用
     */
    if ($source === 'page') {
        $ref_id = (int) naigai_front_meta($front_id, $keys['hero_ref_page_id'], 0);

        if ($ref_id && get_post_status($ref_id)) {
            $data['kicker'] = naigai_front_hub_get($ref_id, '_hub_kicker', '');
            $data['title']  = naigai_front_hub_get($ref_id, '_hub_title', get_the_title($ref_id));
            $data['lead']   = naigai_front_hub_get($ref_id, '_hub_lead', '');

            foreach (naigai_front_page_image_ids_from_hub($ref_id) as $image_id) {
                $url = wp_get_attachment_image_url($image_id, 'full');
                if ($url) {
                    $data['slides'][] = array('type' => 'image', 'url' => $url);
                }
            }

            $data['primary_label']   = naigai_front_hub_get($ref_id, '_hub_cta_primary_label', '');
            $data['primary_url']     = naigai_front_page_cta_url($ref_id, '_hub_cta_primary_url', '_hub_cta_primary_page_id', '');
            $data['secondary_label'] = naigai_front_hub_get($ref_id, '_hub_cta_secondary_label', '');
            $data['secondary_url']   = naigai_front_page_cta_url($ref_id, '_hub_cta_secondary_url', '_hub_cta_secondary_page_id', '');

            return $data;
        }
    }

    /**
     * feature投稿ID
     */
    if ($source === 'feature') {
        $feature_id = (int) naigai_front_meta($front_id, $keys['hero_feature_id'], 0);

        if ($feature_id && get_post_status($feature_id)) {
            $featured_type = get_post_meta($feature_id, 'page_featured_type', true);
            $video_id      = get_post_meta($feature_id, 'page_video_id', true);

            $data['kicker'] = 'FEATURE';
            $data['title']  = get_the_title($feature_id);

            $excerpt = get_the_excerpt($feature_id);
            if (!$excerpt) {
                $excerpt = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $feature_id)), 46, '...');
            }
            $data['lead'] = $excerpt;

            if ($featured_type === 'youtube' && $video_id) {
                $data['slides'][] = array('type' => 'youtube', 'video_id' => $video_id);
            } elseif ($featured_type === 'vimeo' && $video_id) {
                $data['slides'][] = array('type' => 'vimeo', 'video_id' => $video_id);
            } elseif (($featured_type === 'mp4' || $featured_type === 'video') && $video_id) {
                $video_url = is_numeric($video_id) ? wp_get_attachment_url((int) $video_id) : $video_id;
                if ($video_url) {
                    $data['slides'][] = array('type' => 'mp4', 'url' => $video_url);
                }
            } else {
                $thumb_id = get_post_thumbnail_id($feature_id);
                $url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'full') : '';
                if ($url) {
                    $data['slides'][] = array('type' => 'image', 'url' => $url);
                }
            }

            $data['primary_label']   = '詳しく見る';
            $data['primary_url']     = get_permalink($feature_id);
            $data['secondary_label'] = 'お問い合わせ';
            $data['secondary_url']   = home_url('/contact/');

            return $data;
        }
    }

    /**
     * このページ専用hero
     */
    $data['source'] = 'self';

    $data['kicker'] = naigai_front_meta(
        $front_id,
        $keys['hero_kicker'],
        naigai_front_hub_get($front_id, '_hub_kicker', 'NAIGAI REAL ESTATE / CONSTRUCTION / STAY')
    );

    $data['title'] = naigai_front_meta(
        $front_id,
        $keys['hero_title'],
        naigai_front_hub_get($front_id, '_hub_title', '不動産・住宅建設・民泊ステイの総合窓口')
    );

    $data['lead'] = naigai_front_meta(
        $front_id,
        $keys['hero_lead'],
        naigai_front_hub_get($front_id, '_hub_lead', '土地探しから建築、賃貸、民泊ステイの運営、土地・建物の活用まで。那須エリアの暮らしと資産価値をトータルにサポートします。')
    );

    $image_ids = naigai_front_csv_ids(naigai_front_meta($front_id, $keys['hero_image_ids'], ''));

    if (empty($image_ids)) {
        $image_ids = naigai_front_page_image_ids_from_hub($front_id);
    }

    foreach ($image_ids as $image_id) {
        $url = wp_get_attachment_image_url($image_id, 'full');
        if ($url) {
            $data['slides'][] = array('type' => 'image', 'url' => $url);
        }
    }

    $data['primary_label']   = naigai_front_meta($front_id, $keys['hero_primary_label'], '不動産を見る');
    $data['primary_url']     = naigai_front_meta($front_id, $keys['hero_primary_url'], home_url('/fudousan/'));
    $data['secondary_label'] = naigai_front_meta($front_id, $keys['hero_secondary_label'], '住宅建設を見る');
    $data['secondary_url']   = naigai_front_meta($front_id, $keys['hero_secondary_url'], home_url('/construction-hub/'));

    return $data;
}

/**
 * =========================================================
 * 3カード data
 * =========================================================
 */
function naigai_front_page_gateway_cards($front_id)
{
    $front_id = (int) $front_id;
    $keys = naigai_front_page_keys();

    $defaults = array(
        1 => array(
            'title' => '土地を買う・借りる',
            'text'  => '土地の購入・賃借・活用を考える方へ。',
            'url'   => home_url('/fudousan/'),
        ),
        2 => array(
            'title' => '建物を注文する',
            'text'  => '注文住宅・新築・建替えをご検討の方へ。',
            'url'   => home_url('/construction-hub/'),
        ),
        3 => array(
            'title' => 'お部屋を借りる',
            'text'  => '賃貸・民泊などお部屋探しの方へ。',
            'url'   => home_url('/rent/'),
        ),
    );

    $cards = array();

    for ($i = 1; $i <= 3; $i++) {
        $image_id = (int) naigai_front_meta($front_id, $keys["card{$i}_image"], 0);

        $cards[] = array(
            'title' => naigai_front_meta($front_id, $keys["card{$i}_title"], $defaults[$i]['title']),
            'text'  => naigai_front_meta($front_id, $keys["card{$i}_text"], $defaults[$i]['text']),
            'url'   => naigai_front_meta($front_id, $keys["card{$i}_url"], $defaults[$i]['url']),
            'image' => $image_id ? wp_get_attachment_image_url($image_id, 'large') : '',
        );
    }

    return $cards;
}

/**
 * =========================================================
 * 物件ピックアップ query
 * =========================================================
 */
function naigai_front_page_property_post_types()
{
    $candidates = array('house', 'post');
    $post_types = array();

    foreach ($candidates as $post_type) {
        if (post_type_exists($post_type)) {
            $post_types[] = $post_type;
        }
    }

    return !empty($post_types) ? $post_types : array('post');
}

function naigai_front_page_news_post_type()
{
    if (post_type_exists('company_news')) {
        return 'company_news';
    }

    if (post_type_exists('news')) {
        return 'news';
    }

    return 'post';
}

/**
 * =========================================================
 * フロント用 fallback JS
 * =========================================================
 *
 * 既存JSが動く前提だが、読み込み漏れがあっても最低限動くようにする。
 * - Swiperが存在すれば .js-hub-swiper を初期化
 * - Isotopeが存在すれば #dess_home_cats を Isotope で切替
 * - Isotopeが無ければ show/hide で切替
 */
add_action('wp_footer', function () {
    if (!is_front_page()) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.Swiper) {
        document.querySelectorAll('.js-hub-swiper').forEach(function (el) {
          if (el.classList.contains('swiper-initialized')) return;

          var enabled = el.getAttribute('data-swiper-enabled');
          if (enabled === '0') return;

          new Swiper(el, {
            loop: true,
            speed: 700,
            autoplay: {
              delay: 5200,
              disableOnInteraction: false
            },
            pagination: {
              el: el.querySelector('.swiper-pagination'),
              clickable: true
            }
          });
        });
      }

      var tabs = document.querySelector('#dess_home_cats');
      var grid = document.querySelector('.hub-front-property-grid');

      if (!tabs || !grid) return;

      tabs.addEventListener('click', function (event) {
        var link = event.target.closest('a[data-filter]');
        if (!link) return;

        event.preventDefault();

        tabs.querySelectorAll('a').forEach(function (a) {
          a.classList.remove('active');
        });
        link.classList.add('active');

        var filter = link.getAttribute('data-filter') || '*';

        if (window.jQuery && jQuery.fn && jQuery.fn.isotope) {
          jQuery(grid).isotope({
            itemSelector: '.archive-post-box',
            layoutMode: 'fitRows',
            filter: filter
          });
          return;
        }

        grid.querySelectorAll('.archive-post-box').forEach(function (item) {
          if (filter === '*') {
            item.style.display = '';
            return;
          }

          var cls = filter.replace('.', '');
          item.style.display = item.classList.contains(cls) ? '' : 'none';
        });
      });
    });
    </script>
    <?php
}, 99);
