<?php
/**
 * /iezukuri トップ 3カード用 iez_plan 動的データ
 *
 * 目的:
 * - show in home / show in cards がONの iez_plan だけ取得
 * - 1件なら通常HTML
 * - 複数件ならJS側でSwiper風表示
 * - primary を先頭にする
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CSS/JS enqueue は inc/assets.php に移動済み。
 * このファイルは /iezukuri トップ3カード用データ生成専用。
 */

function naigai_iez_plan_home_switch_payload() {
    $terms = array(
        'one-family' => array(
            'label'         => '一世帯住宅',
            'reading_title' => '土地と暮らし方から考える住まい',
            'reading_text'  => '家族構成、将来の使い方、土地の向き、光と風の入り方を整理しながら、無理のない住まい方を組み立てます。',
        ),
        'two-family' => array(
            'label'         => '二世帯住宅',
            'reading_title' => '世帯ごとの暮らしを大切にする家',
            'reading_text'  => '玄関、水まわり、収納、生活音、プライバシーをどう分けるか。親世帯と子世帯が無理なく暮らせる線引きから考えます。',
        ),
        'dual-life' => array(
            'label'         => '二拠点生活',
            'reading_title' => '那須と都市を行き来する住まい',
            'reading_text'  => '滞在する時の快適さと、不在時の管理しやすさを両立できるサイズ・動線・外構を考えます。',
        ),
    );

    $payload = array(
        'terms' => array(),
        'contact_url' => home_url('/iezukuri/contact/'),
    );

    foreach ($terms as $slug => $info) {
        $plans = naigai_iez_plan_home_switch_get_plans_by_type($slug, $info);

        $payload['terms'][$slug] = array(
            'label' => $info['label'],
            'reading_title' => $info['reading_title'],
            'reading_text' => $info['reading_text'],
            'plans' => $plans,
        );
    }

    return $payload;
}

function naigai_iez_plan_home_switch_get_plans_by_type($type_slug, $info) {
    $q = new WP_Query(array(
        'post_type'      => 'iez_plan',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'iez_plan_type',
                'field'    => 'slug',
                'terms'    => $type_slug,
            ),
        ),
        'meta_query' => array(
            array(
                'key'   => '_ch_plan_show_in_home',
                'value' => '1',
            ),
            array(
                'key'   => '_ch_plan_show_in_cards',
                'value' => '1',
            ),
        ),
        'orderby' => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        ),
    ));

    $posts = $q->posts;

    usort($posts, function($a, $b) {
        $ap = get_post_meta($a->ID, '_ch_plan_is_card_primary', true) === '1' ? 1 : 0;
        $bp = get_post_meta($b->ID, '_ch_plan_is_card_primary', true) === '1' ? 1 : 0;

        if ($ap !== $bp) {
            return $bp <=> $ap;
        }

        return $a->ID <=> $b->ID;
    });

    $plans = array();

    foreach ($posts as $post) {
        $plans[] = naigai_iez_plan_home_switch_plan_data($post->ID, $info);
    }

    return $plans;
}

function naigai_iez_plan_home_switch_img($attachment_id, $title, $kind) {
    $attachment_id = absint($attachment_id);

    if (!$attachment_id) {
        return null;
    }

    $full = wp_get_attachment_image_url($attachment_id, 'full');
    $large = wp_get_attachment_image_url($attachment_id, 'large');

    if (!$full) {
        return null;
    }

    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

    return array(
        'title' => $title,
        'alt'   => $alt !== '' ? $alt : $title,
        'src'   => $large ?: $full,
        'href'  => $full,
        'kind'  => $kind,
    );
}

function naigai_iez_plan_home_switch_parse_ids($raw) {
    return array_values(array_filter(array_map('absint', explode(',', (string) $raw))));
}

function naigai_iez_plan_home_switch_plan_data($post_id, $info) {
    $post_id = absint($post_id);

    $name   = get_the_title($post_id);
    $style  = get_post_meta($post_id, '_ch_plan_style', true);
    $layout = get_post_meta($post_id, '_ch_plan_layout', true);
    $area   = get_post_meta($post_id, '_ch_plan_total_area', true);
    $tsubo  = get_post_meta($post_id, '_ch_plan_tsubo', true);
    $desc   = get_post_meta($post_id, '_ch_plan_description', true);

    $exterior_id = absint(get_post_meta($post_id, '_ch_plan_exterior_image_id', true));
    if (!$exterior_id && has_post_thumbnail($post_id)) {
        $exterior_id = absint(get_post_thumbnail_id($post_id));
    }

    $gallery_ids = naigai_iez_plan_home_switch_parse_ids(get_post_meta($post_id, '_ch_plan_gallery_image_ids', true));

    $works = array();

    $exterior = naigai_iez_plan_home_switch_img($exterior_id, '外観写真', 'work');
    if ($exterior) {
        $works[] = $exterior;
    }

    $gallery_labels = array('内装写真 1', '内装写真 2', '内装写真 3', '内装写真 4');
    foreach ($gallery_ids as $i => $id) {
        $img = naigai_iez_plan_home_switch_img($id, $gallery_labels[$i] ?? '内装写真', 'work');
        if ($img) {
            $works[] = $img;
        }
    }

    $floor_type = get_post_meta($post_id, '_ch_plan_floor_type', true) ?: 'one_story';

    $plans = array();

    $plan_1f_id = absint(get_post_meta($post_id, '_ch_plan_1f_image_id', true));
    $plan_2f_id = absint(get_post_meta($post_id, '_ch_plan_2f_image_id', true));
    $site_id    = absint(get_post_meta($post_id, '_ch_plan_site_image_id', true));

    if ($floor_type === 'two_story') {
        $img = naigai_iez_plan_home_switch_img($plan_1f_id, '1F 平面図', 'plan');
        if ($img) {
            $plans[] = $img;
        }

        $img = naigai_iez_plan_home_switch_img($plan_2f_id, '2F 平面図', 'plan');
        if ($img) {
            $plans[] = $img;
        }
    } else {
        $img = naigai_iez_plan_home_switch_img($plan_1f_id, '平面図', 'plan');
        if ($img) {
            $plans[] = $img;
        }
    }

    $img = naigai_iez_plan_home_switch_img($site_id, '配置図', 'plan');
    if ($img) {
        $plans[] = $img;
    }

    $specs = array_values(array_filter(array($layout, $style, $area, $tsubo)));

    return array(
        'id'            => $post_id,
        'title'         => $name,
        'url'           => get_permalink($post_id),
        'reading_title' => $info['label'] . '：' . $info['reading_title'],
        'reading_text'  => $desc ?: $info['reading_text'],
        'specs'         => $specs,
        'works'         => $works,
        'plans'         => $plans,
        'is_primary'    => get_post_meta($post_id, '_ch_plan_is_card_primary', true) === '1',
    );
}
