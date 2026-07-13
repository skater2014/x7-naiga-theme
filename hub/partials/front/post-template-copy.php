<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * templates/post-template copy.php
 * 一覧カード 修正版
 * =========================================================
 *
 * ルール:
 * - ブログ:
 *   - blog_genre を 1個だけ badge 表示（画像左上）
 *
 * - 不動産:
 *   - 価格（画像右上）
 *   - 住所
 *   - 面積
 *   - Google位置（既存の共通モーダルを開く）
 *
 * - 出さない:
 *   - 不動産の複数 badge
 *   - 間取り
 *   - カードごとの個別モーダル
 *   - 動画カード上の価格表示
 */

$post_id    = get_the_ID();
$permalink  = get_permalink($post_id);
$post_title = get_the_title($post_id);
$post_type  = get_post_type($post_id);

/**
 * ---------------------------------------------------------
 * class
 * ---------------------------------------------------------
 */
$cats  = get_the_category($post_id);
$class = '';

if (!empty($cats) && !is_wp_error($cats)) {
    foreach ($cats as $cat) {
        $class .= ' term-' . esc_attr($cat->term_id);
    }
}

/**
 * ---------------------------------------------------------
 * helper
 * ---------------------------------------------------------
 */
$normalize_number = function ($value) {
    return preg_replace('/[^0-9.]/', '', (string) $value);
};

/**
 * ---------------------------------------------------------
 * 土地 / 建物 判定
 * ---------------------------------------------------------
 */
$is_land  = false;
$is_house = false;

if (!empty($cats) && !is_wp_error($cats)) {
    foreach ($cats as $category) {
        if ($category->slug === 'naigai-tochi') {
            $is_land = true;
        } elseif ($category->slug === 'naigai-construction') {
            $is_house = true;
        }
    }
}

$is_realestate = ($is_land || $is_house);
$is_blog       = ($post_type === 'blog' || $post_type === 'post');

/**
 * ---------------------------------------------------------
 * メディア種別
 * ---------------------------------------------------------
 */
$type     = trim((string) get_post_meta($post_id, 'page_featured_type', true));
$video_id = trim((string) get_post_meta($post_id, 'page_video_id', true));
$is_video = in_array($type, array('youtube', 'vimeo'), true);

/**
 * ---------------------------------------------------------
 * noimage
 * ---------------------------------------------------------
 */
$noimage = get_template_directory_uri() . '/images/noimage.gif';

/**
 * ---------------------------------------------------------
 * 価格
 * ---------------------------------------------------------
 */
$price_raw = get_post_meta($post_id, 'NewPrice', true);
if ($price_raw === '') {
    $price_raw = get_post_meta($post_id, 'Price', true);
}

$price_display = '';
$price_number  = $normalize_number($price_raw);

if ($price_number !== '' && (float) $price_number > 0) {
    $price_display = number_format((float) $price_number) . '万円';
} else {
    $sold_out = get_post_meta($post_id, 'sold-out', true);
    if ($sold_out) {
        $price_display = '売却済';
    }
}

/**
 * ---------------------------------------------------------
 * 住所
 * ---------------------------------------------------------
 */
$location = get_post_meta($post_id, 'NewLocation', true);
if ($location === '') {
    $location = get_post_meta($post_id, 'Location', true);
}

/**
 * ---------------------------------------------------------
 * 面積
 * ---------------------------------------------------------
 */
$land_area = get_post_meta($post_id, 'NewLandArea', true);
if ($land_area === '') {
    $land_area = get_post_meta($post_id, 'LandArea', true);
}

$building_area = get_post_meta($post_id, 'NewBuildingArea', true);
if ($building_area === '') {
    $building_area = get_post_meta($post_id, 'BuildingArea', true);
}

$land_area_number     = $normalize_number($land_area);
$building_area_number = $normalize_number($building_area);

$area_rows = array();

if ($is_land && $land_area_number !== '') {
    $area_rows[] = array(
        'label' => '土地面積',
        'value' => number_format((float) $land_area_number) . '㎡',
    );
}

if ($is_house) {
    if ($land_area_number !== '') {
        $area_rows[] = array(
            'label' => '土地面積',
            'value' => number_format((float) $land_area_number) . '㎡',
        );
    }

    if ($building_area_number !== '') {
        $area_rows[] = array(
            'label' => '建物面積',
            'value' => number_format((float) $building_area_number) . '㎡',
        );
    }
}

/**
 * ---------------------------------------------------------
 * Google Map 埋め込み
 * - 既存の共通モーダルへ渡すため base64 化
 * ---------------------------------------------------------
 */
$google_embed_code = get_post_meta($post_id, 'NewGoogleEmbedcode', true);
if ($google_embed_code === '') {
    $google_embed_code = get_post_meta($post_id, 'GoogleEmbedcode', true);
}
if ($google_embed_code === '') {
    $google_embed_code = get_post_meta($post_id, '_google_map_iframe_1', true);
}

$google_iframe_html = '';

if ($google_embed_code !== '') {
    if (strpos($google_embed_code, '<iframe') !== false) {
        $allowed_html = array(
            'iframe' => array(
                'src'             => true,
                'width'           => true,
                'height'          => true,
                'style'           => true,
                'allowfullscreen' => true,
                'loading'         => true,
                'referrerpolicy'  => true,
            ),
        );

        $google_iframe_html = wp_kses($google_embed_code, $allowed_html);
    } else {
        $google_iframe_html = '<iframe src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    }
}

$google_iframe_payload = '';
if ($google_iframe_html !== '') {
    $google_iframe_payload = base64_encode($google_iframe_html);
}

/**
 * ---------------------------------------------------------
 * ブログ taxonomy badge
 * - blog_genre の最初の1個だけ
 * ---------------------------------------------------------
 */
$blog_badge = array();

if ($is_blog && taxonomy_exists('blog_genre')) {
    $blog_terms = get_the_terms($post_id, 'blog_genre');

    if (!empty($blog_terms) && !is_wp_error($blog_terms)) {
        $first_term = array_shift($blog_terms);
        $term_link  = get_term_link($first_term);

        if (!is_wp_error($term_link)) {
            $blog_badge = array(
                'label' => $first_term->name,
                'url'   => $term_link,
            );
        }
    }
}
?>

<article class="archive-post-box archive-post-box--clean<?php echo esc_attr($class); ?>">

    <div class="archive-post-feature">

        <?php if ($is_blog && !empty($blog_badge)) : ?>
            <div class="archive-post-badges archive-post-badges--blog">
                <a class="archive-post-badge archive-post-badge-link" href="<?php echo esc_url($blog_badge['url']); ?>">
                    <?php echo esc_html($blog_badge['label']); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($is_realestate && !$is_video && $price_display !== '') : ?>
            <div class="archive-post-price">
                <span><?php echo esc_html($price_display); ?></span>
            </div>
        <?php endif; ?>

        <?php
        switch ($type) {
            case 'youtube':
                echo '<div class="archive-post-media archive-post-media--video">';
                echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="' . esc_attr($post_title) . '"></lite-youtube>';
                echo '</div>';
                break;

            case 'vimeo':
                echo '<div class="archive-post-media archive-post-media--video">';
                echo '<lite-vimeo videoid="' . esc_attr($video_id) . '"><div class="ltv-playbtn"></div></lite-vimeo>';
                echo '</div>';
                break;

            default:
                if (has_post_thumbnail()) {
                    echo '<div class="archive-post-image">';
                    echo '<a href="' . esc_url($permalink) . '">';
                    the_post_thumbnail('home-thumb');
                    echo '</a>';
                    echo '</div>';
                } else {
                    echo '<div class="archive-post-image archive-post-image--noimage">';
                    echo '<a href="' . esc_url($permalink) . '" class="thumbnail-link">';
                    echo '<div class="archive-post-image__fallback" style="background-image:url(' . esc_url($noimage) . ');"></div>';
                    echo '</a>';
                    echo '</div>';
                }
                break;
        }
        ?>

    </div><!-- /.archive-post-feature -->

    <div class="archive-post-info">

        <h3 class="archive-post-title">
            <a href="<?php echo esc_url($permalink); ?>">
                <?php echo esc_html($post_title); ?>
            </a>
        </h3>

        <?php if ($is_realestate && $location !== '') : ?>
            <div class="archive-post-location-text">
                <?php echo esc_html($location); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_realestate && !empty($area_rows)) : ?>
            <div class="archive-post-area">
                <?php foreach ($area_rows as $row) : ?>
                    <div class="archive-post-area__row">
                        <span class="archive-post-area__label"><?php echo esc_html($row['label']); ?></span>
                        <span class="archive-post-area__value"><?php echo esc_html($row['value']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_realestate && $google_iframe_payload !== '') : ?>
            <div class="archive-post-location-action">
                <button
                    type="button"
                    class="google-location-trigger"
                    data-map-html="<?php echo esc_attr($google_iframe_payload); ?>">
                    <svg class="icon-location" aria-hidden="true" focusable="false" width="20" height="20">
                        <use xlink:href="#icon-location"></use>
                    </svg>
                    <span>Google位置</span>
                </button>
            </div>
        <?php endif; ?>

    </div><!-- /.archive-post-info -->

</article><!-- /.archive-post-box -->