<?php
/**
 * Template Name: 民泊B2C共通LP
 *
 * =========================================================
 * このファイルの役割
 * =========================================================
 *
 * 1. 民泊B2C 固定ページの共通親テンプレート
 * 2. 共通データをここで作る
 *    - HERO
 *    - 導入
 *    - CTA
 *    - FAQ
 * 3. どのHTML本体を読むかは
 *    _mpb_layout_type で決める
 *
 * 重要:
 * - 以前は slug で guide / difference / standard を切り替えていた
 * - 今後は slug ではなく _mpb_layout_type で切り替える
 * - 新しいURLを作ってもコードを増やさない
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * ヘッダー読み込み
 * =========================================================
 */
get_header('77');

/**
 * =========================================================
 * 現在ページID
 * =========================================================
 */
$post_id = get_the_ID();

/**
 * =========================================================
 * meta を安全に取る helper
 * =========================================================
 *
 * 役割:
 * - 保存済み post meta があればそれを返す
 * - 空なら functions 側の初期値を返す
 */
if (!function_exists('mpb_meta')) {
    function mpb_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);

        if ($value !== '' && $value !== null) {
            return $value;
        }

        if (function_exists('mpb_default_value')) {
            return mpb_default_value($post_id, $key, $default);
        }

        return $default;
    }
}

/**
 * =========================================================
 * 画像URL取得
 * =========================================================
 */
if (!function_exists('mpb_image_url')) {
    function mpb_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));

        if (!$attachment_id) {
            return '';
        }

        return (string) wp_get_attachment_image_url($attachment_id, $size);
    }
}

/**
 * =========================================================
 * 画像alt取得
 * =========================================================
 */
if (!function_exists('mpb_image_alt')) {
    function mpb_image_alt($post_id, $key, $fallback = '')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));

        if (!$attachment_id) {
            return $fallback;
        }

        $alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
        if ($alt !== '') {
            return $alt;
        }

        $title = get_the_title($attachment_id);
        return $title ? $title : $fallback;
    }
}

/**
 * =========================================================
 * 固定ページ選択metaからURL取得
 * =========================================================
 */
if (!function_exists('mpb_get_selected_page_url')) {
    function mpb_get_selected_page_url($post_id, $meta_key)
    {
        $page_id = absint(get_post_meta($post_id, $meta_key, true));

        if (!$page_id && function_exists('mpb_default_page_id')) {
            $page_id = absint(mpb_default_page_id($post_id, $meta_key));
        }

        if (!$page_id) {
            return '';
        }

        $page = get_post($page_id);

        if (!$page || $page->post_type !== 'page' || $page->post_status !== 'publish') {
            return '';
        }

        return get_permalink($page_id);
    }
}

/**
 * =========================================================
 * ボタンHTML生成
 * =========================================================
 */
if (!function_exists('mpb_btn')) {
    function mpb_btn($url, $text, $class = 'is-primary')
    {
        $url  = trim((string) $url);
        $text = trim((string) $text);

        if ($url === '' || $text === '') {
            return '';
        }

        return sprintf(
            '<a class="mnpk-page-btn %1$s" href="%2$s">%3$s</a>',
            esc_attr($class),
            esc_url($url),
            esc_html($text)
        );
    }
}

/**
 * =========================================================
 * FAQ 配列生成
 * =========================================================
 */
if (!function_exists('mpb_faq_items')) {
    function mpb_faq_items($post_id)
    {
        $items = array();

        for ($i = 1; $i <= 10; $i++) {
            $q = trim((string) mpb_meta($post_id, '_mpb_faq_' . $i . '_q', ''));
            $a = trim((string) mpb_meta($post_id, '_mpb_faq_' . $i . '_a', ''));

            if ($q !== '' && $a !== '') {
                $items[] = array(
                    'q' => $q,
                    'a' => $a,
                );
            }
        }

        return $items;
    }
}

/**
 * =========================================================
 * 関連リンク一覧
 * =========================================================
 *
 * 役割:
 * - 関連リンク 1〜6 の文言 / 遷移先をまとめる
 * - 文言が空なら遷移先ページのタイトルを使う
 */
if (!function_exists('mpb_related_links_items')) {
    function mpb_related_links_items($post_id)
    {
        $items = array();

        for ($i = 1; $i <= 6; $i++) {
            $page_id = absint(get_post_meta($post_id, '_mpb_related_link' . $i . '_page_id', true));
            if (!$page_id) {
                continue;
            }

            $page = get_post($page_id);
            if (!$page || $page->post_type !== 'page' || $page->post_status !== 'publish') {
                continue;
            }

            $text = trim((string) mpb_meta($post_id, '_mpb_related_link' . $i . '_text', ''));
            if ($text === '') {
                $text = get_the_title($page_id);
            }

            $url = get_permalink($page_id);
            if (!$url || $text === '') {
                continue;
            }

            $items[] = array(
                'text' => $text,
                'url'  => $url,
            );
        }

        return $items;
    }
}

/**
 * =========================================================
 * レイアウト種別
 * =========================================================
 *
 * 役割:
 * - slug ではなく _mpb_layout_type を使う
 * - guide / difference / campaign / faq / flow / rules / standard
 *   の値を取る
 */
$layout_type = function_exists('mpb_get_layout_type')
    ? mpb_get_layout_type($post_id)
    : 'standard';

/**
 * =========================================================
 * このページで有効なパーツ一覧
 * =========================================================
 *
 * 役割:
 * - ベース layout_type の基本パーツ
 * - 追加パーツON/OFF
 * を統合した最終結果を返す
 */
$enabled_parts = function_exists('mpb_enabled_parts_for_post')
    ? mpb_enabled_parts_for_post($post_id)
    : (function_exists('mpb_enabled_parts_by_layout')
        ? mpb_enabled_parts_by_layout($layout_type)
        : array());

/**
 * =========================================================
 * partial 読み込み先の決定
 * =========================================================
 *
 * 役割:
 * - guide      → layout-guide.php
 * - difference → layout-difference.php
 * - それ以外   → layout-standard.php
 *
 * campaign / faq / flow / rules / standard は
 * standard レイアウトをベースに使う
 */
$partial_slug = 'standard';

if ($layout_type === 'guide') {
    $partial_slug = 'guide';
} elseif ($layout_type === 'difference') {
    $partial_slug = 'difference';
}

$partial = get_template_directory() . '/templates/minpaku/b2c/layout-' . $partial_slug . '.php';

/**
 * =========================================================
 * HERO データ
 * =========================================================
 */
$hero = array(
    'eyebrow' => mpb_meta($post_id, '_mpb_hero_eyebrow', 'MINPAKU STAY'),
    'title'   => mpb_meta($post_id, '_mpb_hero_title', get_the_title($post_id)),
    'lead'    => mpb_meta($post_id, '_mpb_hero_lead', ''),
    'image'   => mpb_image_url($post_id, '_mpb_hero_image_id', 'full'),
    'alt'     => mpb_image_alt($post_id, '_mpb_hero_image_id', get_the_title($post_id)),
    'btn1'    => mpb_btn(
        mpb_get_selected_page_url($post_id, '_mpb_hero_btn1_page_id'),
        mpb_meta($post_id, '_mpb_hero_btn1_text', ''),
        'is-primary'
    ),
    'btn2'    => mpb_btn(
        mpb_get_selected_page_url($post_id, '_mpb_hero_btn2_page_id'),
        mpb_meta($post_id, '_mpb_hero_btn2_text', ''),
        'is-secondary'
    ),
);

/**
 * =========================================================
 * 導入データ
 * =========================================================
 */
$intro = array(
    'eyebrow' => mpb_meta($post_id, '_mpb_intro_eyebrow', ''),
    'title'   => mpb_meta($post_id, '_mpb_intro_title', ''),
    'text'    => mpb_meta($post_id, '_mpb_intro_text', ''),
    'image'   => mpb_image_url($post_id, '_mpb_intro_image_id', 'large'),
    'alt'     => mpb_image_alt($post_id, '_mpb_intro_image_id', ''),
);

/**
 * =========================================================
 * CTA データ
 * =========================================================
 */
$cta = array(
    'title' => mpb_meta($post_id, '_mpb_cta_title', ''),
    'text'  => mpb_meta($post_id, '_mpb_cta_text', ''),
    'image' => mpb_image_url($post_id, '_mpb_cta_image_id', 'full'),
    'alt'   => mpb_image_alt($post_id, '_mpb_cta_image_id', ''),
    'btn1'  => mpb_btn(
        mpb_get_selected_page_url($post_id, '_mpb_cta_btn1_page_id'),
        mpb_meta($post_id, '_mpb_cta_btn1_text', ''),
        'is-primary'
    ),
    'btn2'  => mpb_btn(
        mpb_get_selected_page_url($post_id, '_mpb_cta_btn2_page_id'),
        mpb_meta($post_id, '_mpb_cta_btn2_text', ''),
        'is-secondary'
    ),
);

/**
 * =========================================================
 * FAQ データ
 * =========================================================
 *
 * 役割:
 * - standard レイアウト側で必要になる
 */
$faq_items = mpb_faq_items($post_id);

/**
 * =========================================================
 * 関連リンクデータ
 * =========================================================
 */
$related_links = array(
    'title' => mpb_meta($post_id, '_mpb_related_links_title', '関連リンク'),
    'text'  => mpb_meta($post_id, '_mpb_related_links_text', ''),
    'items' => function_exists('mpb_related_links_items')
        ? mpb_related_links_items($post_id)
        : array(),
);

/**
 * =========================================================
 * 抜粋
 * =========================================================
 *
 * 役割:
 * - post_excerpt を独立パネルで出す
 * - the_content やメタキーのHTMLブロックと混ぜない
 */
$page_excerpt = trim((string) get_post_field('post_excerpt', $post_id));
?>
<div class="mnpk-page mnpk-page--<?php echo esc_attr($partial_slug); ?> mnpk-page--layout-<?php echo esc_attr(sanitize_title($layout_type)); ?>">

    <?php if (file_exists($partial)) : ?>
        <?php
        /**
         * =====================================================
         * partial 読み込み
         * =====================================================
         *
         * ここで実際の HTML 本体を読み込む
         */
        include $partial;
        ?>
    <?php else : ?>
        <main class="mnpk-page-shell">
            <section class="mnpk-page-hero <?php echo $hero['image'] ? 'has-image' : ''; ?>" <?php if ($hero['image']) : ?> style="background-image:url('<?php echo esc_url($hero['image']); ?>');" <?php endif; ?>>
                <div class="mnpk-page-hero__overlay"></div>
                <div class="mnpk-page-hero__inner">
                    <?php if ($hero['eyebrow']) : ?>
                        <p class="mnpk-page-eyebrow"><?php echo esc_html($hero['eyebrow']); ?></p>
                    <?php endif; ?>

                    <h1 class="mnpk-page-hero__title"><?php echo esc_html($hero['title']); ?></h1>

                    <?php if ($hero['lead']) : ?>
                        <p class="mnpk-page-hero__lead"><?php echo esc_html($hero['lead']); ?></p>
                    <?php endif; ?>

                    <?php if ($hero['btn1'] || $hero['btn2']) : ?>
                        <div class="mnpk-page-actions">
                            <?php echo $hero['btn1']; ?>
                            <?php echo $hero['btn2']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    <?php endif; ?>
</div>
<?php
/**
 * =========================================================
 * footer nav
 * =========================================================
 *
 * 役割:
 * - 民泊共通の footer links があれば読み込む
 * - いまは既存動作を維持する
 */
$mnpk_footer_nav = get_template_directory() . '/minpaku/common/templates/minpaku-footer-nav.php';

if (file_exists($mnpk_footer_nav)) {
    include $mnpk_footer_nav;
}

get_footer();
