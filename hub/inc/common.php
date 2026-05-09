<?php
if (!defined("ABSPATH")) {
    exit;
}

function naigai_is_hub_target_page($post_id)
{
    return naigai_is_hub_page((int) $post_id);
}

function naigai_hub_get($post_id, $key, $default = "")
{
    $value = get_post_meta((int) $post_id, $key, true);
    return ($value !== "" && $value !== null) ? $value : $default;
}

function naigai_hub_resolve_meta_link($post_id, $page_id_key, $url_key)
{
    $page_id = (int) get_post_meta((int) $post_id, $page_id_key, true);

    if ($page_id > 0 && get_post_status($page_id)) {
        $permalink = get_permalink($page_id);
        if (!empty($permalink)) {
            return $permalink;
        }
    }

    return naigai_hub_get($post_id, $url_key, "");
}

function naigai_hub_resolve_item_url($post_id, $base_key)
{
    return naigai_hub_resolve_meta_link($post_id, "{$base_key}_page_id", "{$base_key}_url");
}

function naigai_hub_get_image_ids_from_meta($post_id, $meta_key)
{
    $raw = (string) get_post_meta((int) $post_id, $meta_key, true);
    if ($raw === "") {
        return array();
    }

    $parts = preg_split("/\s*,\s*/", $raw, -1, PREG_SPLIT_NO_EMPTY);
    $ids   = array();

    foreach ($parts as $part) {
        $id = absint($part);
        if ($id > 0) {
            $ids[$id] = $id;
        }
    }

    return array_values($ids);
}

function naigai_hub_build_image_data($attachment_id, $size = "large")
{
    $attachment_id = absint($attachment_id);
    if ($attachment_id <= 0) {
        return null;
    }

    $src = wp_get_attachment_image_url($attachment_id, $size);
    if (!$src) {
        return null;
    }

    $alt = trim((string) get_post_meta($attachment_id, "_wp_attachment_image_alt", true));
    if ($alt === "") {
        $alt = get_the_title($attachment_id);
    }

    return array(
        "id"     => $attachment_id,
        "src"    => $src,
        "srcset" => wp_get_attachment_image_srcset($attachment_id, $size),
        "sizes"  => wp_get_attachment_image_sizes($attachment_id, $size),
        "alt"    => $alt,
    );
}

function naigai_hub_get_images_from_meta($post_id, $meta_key, $size = "large")
{
    $images = array();
    $ids    = naigai_hub_get_image_ids_from_meta($post_id, $meta_key);

    foreach ($ids as $attachment_id) {
        $image = naigai_hub_build_image_data($attachment_id, $size);
        if ($image) {
            $images[] = $image;
        }
    }

    return $images;
}

function naigai_hub_get_item_images($post_id, $base_key, $page_id = 0, $size = "large")
{
    $images = naigai_hub_get_images_from_meta($post_id, "{$base_key}_image_ids", $size);
    if (!empty($images)) {
        return $images;
    }

    $page_id = absint($page_id);
    if ($page_id > 0 && has_post_thumbnail($page_id)) {
        $thumb_id = (int) get_post_thumbnail_id($page_id);
        $image    = naigai_hub_build_image_data($thumb_id, $size);

        if ($image) {
            return array($image);
        }
    }

    return array();
}

function naigai_hub_get_items($post_id, $prefix, $count = 4)
{
    $items = array();

    for ($i = 1; $i <= $count; $i++) {
        $base_key = "{$prefix}_{$i}";
        $page_id  = (int) get_post_meta((int) $post_id, "{$base_key}_page_id", true);
        $raw_url  = naigai_hub_get($post_id, "{$base_key}_url", "");

        $items[] = array(
            "title"     => naigai_hub_get($post_id, "{$base_key}_title", ""),
            "text"      => naigai_hub_get($post_id, "{$base_key}_text", ""),
            "url"       => naigai_hub_resolve_item_url($post_id, $base_key),
            "page_id"   => $page_id,
            "raw_url"   => $raw_url,
            "image_ids" => naigai_hub_get_image_ids_from_meta($post_id, "{$base_key}_image_ids"),
        );
    }

    return $items;
}

/**
 * =========================================================
 * Hub コンセプト別の既定構成を返す
 * 役割:
 * - front / construction / realestate の文書設計を分ける
 * - 管理画面で未入力ならここにある既定文言を使う
 * - 同じ文章をそのまま使い回さず、相談目的に応じて変える
 * =========================================================
 */
if (!function_exists('naigai_hub_get_concept_profile')) {
    function naigai_hub_get_concept_profile($context = 'front')
    {
        $profiles = array(
            'front' => array(
                'flow_title' => '総合窓口で相談を整理する流れ',
                'flow_lead'  => '土地・注文住宅・売却・住み替え・民泊活用まで、相談内容を分断せずに整理しながら進めます。',
                'flow_items' => array(
                    array(
                        'step'  => 'STEP 1',
                        'title' => 'いま考えていることを整理する',
                        'text'  => '土地探し、家づくり、売却、住み替え、資産活用など、相談テーマを一つの窓口で切り分けます。',
                    ),
                    array(
                        'step'  => 'STEP 2',
                        'title' => '優先順位を決める',
                        'text'  => '予算、時期、エリア、現住まいの状況を確認し、先に動くべき内容を整理します。',
                    ),
                    array(
                        'step'  => 'STEP 3',
                        'title' => '次に進む窓口を決める',
                        'text'  => '不動産相談・注文住宅相談・活用相談など、今の状況に合う進め方へつなげます。',
                    ),
                ),
                'cta_text' => 'まだ方向が決まっていなくても大丈夫です。まずは状況整理から始めて、どの相談に進むべきかを一緒に確認します。',
            ),

            'construction' => array(
                'flow_title' => '注文住宅相談の進め方',
                'flow_lead'  => '暮らし方・敷地条件・間取り・工法を整理しながら、注文住宅として形にしていく流れです。',
                'flow_items' => array(
                    array(
                        'step'  => '01',
                        'title' => '暮らしの希望を整理する',
                        'text'  => '家族構成、必要な部屋数、予算、土地の有無を整理し、注文住宅としての前提条件を確認します。',
                    ),
                    array(
                        'step'  => '02',
                        'title' => '実例と考え方を見る',
                        'text'  => '施工事例や住宅情報を見ながら、デザイン・性能・暮らし方の方向性を固めます。',
                    ),
                    array(
                        'step'  => '03',
                        'title' => '工法と仕様を比較する',
                        'text'  => '在来工法や枠組壁工法などの特徴を整理し、希望に合う考え方を比較します。',
                    ),
                    array(
                        'step'  => '04',
                        'title' => '注文住宅相談へ進む',
                        'text'  => '要望と条件が見えてきた段階で、具体的な注文住宅の相談へ進みます。',
                    ),
                ),
                'cta_text' => '建設業全体の窓口ではなく、注文住宅を考える人の相談導線として整理します。構想段階でも大丈夫です。',
            ),

            'realestate' => array(
                'secondary_title' => '不動産相談の入口を整理する',
                'secondary_lead'  => '探す・売る・住み替える・空き家を整理するなど、状況に応じて相談テーマを分けて進めます。',
                'cta_text'        => '売却査定だけに寄らず、購入・売却・住み替え・買取のどこから進めるべきかを整理してご相談いただけます。',
            ),
        );

        return isset($profiles[$context]) ? $profiles[$context] : array();
    }
}

/**
 * =========================================================
 * メタキー優先で値を返す
 * 役割:
 * - 管理画面に入力があればその値を使う
 * - 空なら既定値を返す
 * =========================================================
 */
if (!function_exists('naigai_hub_meta_or_default')) {
    function naigai_hub_meta_or_default($post_id, $key, $default = '')
    {
        $value = trim((string) get_post_meta($post_id, $key, true));
        return ($value !== '') ? $value : $default;
    }
}

/**
 * =========================================================
 * Flow 項目を組み立てる
 * 役割:
 * - 管理画面の _hub_flow_* を優先
 * - 未入力ならコンセプト別既定文言を使う
 * =========================================================
 */
if (!function_exists('naigai_hub_get_flow_items')) {
    function naigai_hub_get_flow_items($post_id, $context = 'front')
    {
        $profile  = function_exists('naigai_hub_get_concept_profile') ? naigai_hub_get_concept_profile($context) : array();
        $defaults = (isset($profile['flow_items']) && is_array($profile['flow_items'])) ? $profile['flow_items'] : array();

        $items = array();
        $count = max(count($defaults), 4);

        for ($i = 1; $i <= $count; $i++) {
            $default = isset($defaults[$i - 1]) ? $defaults[$i - 1] : array(
                'step'  => '',
                'title' => '',
                'text'  => '',
            );

            $step  = function_exists('naigai_hub_meta_or_default') ? naigai_hub_meta_or_default($post_id, "_hub_flow_{$i}_step", isset($default['step']) ? $default['step'] : '') : '';
            $title = function_exists('naigai_hub_meta_or_default') ? naigai_hub_meta_or_default($post_id, "_hub_flow_{$i}_title", isset($default['title']) ? $default['title'] : '') : '';
            $text  = function_exists('naigai_hub_meta_or_default') ? naigai_hub_meta_or_default($post_id, "_hub_flow_{$i}_text", isset($default['text']) ? $default['text'] : '') : '';

            if ($step === '' && $title === '' && $text === '') {
                continue;
            }

            $items[] = array(
                'step'  => $step,
                'title' => $title,
                'text'  => $text,
            );
        }

        return $items;
    }
}

/**
 * =========================================================
 * CTA 補足文を返す
 * 役割:
 * - 管理画面 _hub_cta_text を優先
 * - 未入力ならコンセプト別既定文言を使う
 * =========================================================
 */
if (!function_exists('naigai_hub_get_cta_text')) {
    function naigai_hub_get_cta_text($post_id, $context = 'front')
    {
        $profile = function_exists('naigai_hub_get_concept_profile') ? naigai_hub_get_concept_profile($context) : array();
        $default = isset($profile['cta_text']) ? $profile['cta_text'] : '';
        return function_exists('naigai_hub_meta_or_default') ? naigai_hub_meta_or_default($post_id, '_hub_cta_text', $default) : $default;
    }
}
