<?php
/**
 * =========================================================
 * hub/inc/customhome-contact-sections.php
 *
 * 注文住宅 /iezukuri 用:
 * - ご相談・資料請求 Flow
 * - 既存 /contact フォームの引用表示
 *
 * 方針:
 * - /contact の既存フォームをまず利用する
 * - /iezukuri/contact 側では wrapper とCSSだけ調整
 * - フォーム本体はなるべく複製しない
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_meta')) {
    function naigai_ch_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta((int) $post_id, $key, true);
        return ($value !== '' && $value !== array() && $value !== null) ? $value : $default;
    }
}

if (!function_exists('naigai_ch_contact_source_page_id')) {
    function naigai_ch_contact_source_page_id($post_id = 0)
    {
        $manual = $post_id ? absint(get_post_meta($post_id, '_ch_contact_form_source_page_id', true)) : 0;
        if ($manual) {
            return $manual;
        }

        $contact = get_page_by_path('contact');
        return $contact ? (int) $contact->ID : 0;
    }
}

if (!function_exists('naigai_ch_render_contact_flow_section')) {
    function naigai_ch_render_contact_flow_section($post_id)
    {
        $post_id = absint($post_id);

        $title = naigai_ch_meta($post_id, '_ch_contact_flow_title', 'ご相談・資料請求の流れ');
        $lead  = naigai_ch_meta($post_id, '_ch_contact_flow_lead', '内容を確認したうえで、担当者よりご連絡いたします。');

        $defaults = array(
            1 => array(
                'label' => 'STEP 01',
                'title' => '内容を入力',
                'text'  => 'お名前・ご連絡先・ご相談内容を入力してください。',
            ),
            2 => array(
                'label' => 'STEP 02',
                'title' => '内容を確認',
                'text'  => '入力内容を確認し、問題なければ送信します。',
            ),
            3 => array(
                'label' => 'STEP 03',
                'title' => '受付完了',
                'text'  => '送信後、受付完了画面が表示されます。',
            ),
            4 => array(
                'label' => 'STEP 04',
                'title' => '担当者より連絡',
                'text'  => '内容を確認後、担当者よりご連絡いたします。',
            ),
        );

        $items = array();

        for ($i = 1; $i <= 4; $i++) {
            $items[] = array(
                'label' => naigai_ch_meta($post_id, "_ch_contact_flow_{$i}_label", $defaults[$i]['label']),
                'title' => naigai_ch_meta($post_id, "_ch_contact_flow_{$i}_title", $defaults[$i]['title']),
                'text'  => naigai_ch_meta($post_id, "_ch_contact_flow_{$i}_text", $defaults[$i]['text']),
            );
        }
        ?>
        <section class="ch-section ch-section--white ch-contact-flow-section">
            <div class="ch-shell">
                <div class="ch-section-head ch-section-head--center">
                    <span class="ch-eyebrow">FLOW</span>
                    <h2><?php echo esc_html($title); ?></h2>
                    <?php if ($lead !== '') : ?>
                        <p><?php echo nl2br(esc_html($lead)); ?></p>
                    <?php endif; ?>
                </div>

                <div class="ch-contact-flow">
                    <?php foreach ($items as $index => $item) : ?>
                        <article class="ch-contact-flow__item">
                            <span class="ch-contact-flow__num"><?php echo esc_html($item['label']); ?></span>
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <p><?php echo nl2br(esc_html($item['text'])); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}

if (!function_exists('naigai_ch_contact_form_source_html')) {
    function naigai_ch_contact_form_source_html($post_id)
    {
        $post_id = absint($post_id);

        /*
         * 1. このページ専用ショートコードがあれば最優先
         */
        $shortcode = trim((string) get_post_meta($post_id, '_ch_contact_form_shortcode', true));
        if ($shortcode !== '') {
            return do_shortcode($shortcode);
        }

        /*
         * 2. /contact 固定ページ本文を引用
         *    /contact 側の本文にショートコードが入っている場合はここで展開される。
         */
        $source_id = naigai_ch_contact_source_page_id($post_id);
        if ($source_id) {
            $content = get_post_field('post_content', $source_id);
            if (trim((string) $content) !== '') {
                return apply_filters('the_content', $content);
            }
        }

        /*
         * 3. 既存フォームがテンプレートPHPだけで出ている場合の fallback
         *    まずは /contact への導線を出す。
         *    次段階で既存フォーム関数を特定して shortcode 化する。
         */
        $url = $source_id ? get_permalink($source_id) : home_url('/contact/');

        return '<div class="ch-contact-form-fallback">'
            . '<p>既存のお問い合わせフォームは下記ページからご利用いただけます。</p>'
            . '<a class="ch-btn ch-btn--primary" href="' . esc_url($url) . '">お問い合わせフォームを開く</a>'
            . '</div>';
    }
}

if (!function_exists('naigai_ch_render_contact_form_section')) {
    function naigai_ch_render_contact_form_section($post_id)
    {
        $post_id = absint($post_id);

        $title = naigai_ch_meta($post_id, '_ch_contact_form_title', 'お問い合わせフォーム');
        $lead  = naigai_ch_meta($post_id, '_ch_contact_form_text', '必要事項をご入力のうえ、確認画面へお進みください。');

        $html = function_exists('naigai_ch_render_existing_contact_form_bridge')
            ? naigai_ch_render_existing_contact_form_bridge($post_id)
            : naigai_ch_contact_form_source_html($post_id);
        ?>
        <section class="ch-section ch-section--white ch-contact-form-section">
            <div class="ch-shell">
                <div class="ch-section-head ch-section-head--center">
                    <span class="ch-eyebrow">CONTACT FORM</span>
                    <h2><?php echo esc_html($title); ?></h2>
                    <?php if ($lead !== '') : ?>
                        <p><?php echo nl2br(esc_html($lead)); ?></p>
                    <?php endif; ?>
                </div>

                <div class="ch-contact-form-shell">
                    <?php echo $html; ?>
                </div>
            </div>
        </section>
        <?php
    }
}

if (!function_exists('naigai_ch_contact_flow_shortcode')) {
    function naigai_ch_contact_flow_shortcode()
    {
        ob_start();
        naigai_ch_render_contact_flow_section(get_queried_object_id());
        return ob_get_clean();
    }
    add_shortcode('customhome_contact_flow', 'naigai_ch_contact_flow_shortcode');
}

if (!function_exists('naigai_ch_contact_form_shortcode')) {
    function naigai_ch_contact_form_shortcode()
    {
        ob_start();
        naigai_ch_render_contact_form_section(get_queried_object_id());
        return ob_get_clean();
    }
    add_shortcode('customhome_contact_form', 'naigai_ch_contact_form_shortcode');
}
