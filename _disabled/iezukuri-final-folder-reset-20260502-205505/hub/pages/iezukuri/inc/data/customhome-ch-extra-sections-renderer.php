<?php
/**
 * /iezukuri extra section renderer
 * 足りないHTMLセクションを共通で補う。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_extra_key')) {
    function naigai_ch_extra_key($post_id)
    {
        $slug = get_post_field('post_name', $post_id);

        $map = array(
            'concept'       => array('_ch_concept', '注文住宅の考え方'),
            'design-policy' => array('_ch_design_policy', '設計姿勢'),
            'nasu-house'    => array('_ch_nasu', '那須での家づくり'),
            'design-office' => array('_ch_design_office', 'デザインと設計'),
            'company'       => array('_ch_company', '会社概要'),
            'contact'       => array('_ch_contact', 'ご相談・資料請求'),
        );

        return $map[$slug] ?? array('', '');
    }
}

if (!function_exists('naigai_ch_meta')) {
    function naigai_ch_meta($post_id, $key, $default = '')
    {
        $v = get_post_meta($post_id, $key, true);
        return $v !== '' ? $v : $default;
    }
}

if (!function_exists('naigai_ch_link')) {
    function naigai_ch_link($post_id, $prefix)
    {
        $page_id = absint(get_post_meta($post_id, "{$prefix}_cta_btn_page_id", true));
        $url = trim((string) get_post_meta($post_id, "{$prefix}_cta_btn_url", true));

        if ($page_id) {
            return get_permalink($page_id);
        }

        return $url ?: '';
    }
}

if (!function_exists('naigai_ch_render_extra_sections')) {
    function naigai_ch_render_extra_sections($post_id = 0)
    {
        $post_id = $post_id ? absint($post_id) : get_the_ID();

        list($prefix, $page_label) = naigai_ch_extra_key($post_id);

        if (!$prefix) {
            return;
        }

        $intro_title = naigai_ch_meta($post_id, "{$prefix}_intro_title");
        $intro_text  = naigai_ch_meta($post_id, "{$prefix}_intro_text");
        $split_title = naigai_ch_meta($post_id, "{$prefix}_split_title");
        $split_text  = naigai_ch_meta($post_id, "{$prefix}_split_text");
        $split_image = absint(get_post_meta($post_id, "{$prefix}_split_image_id", true));

        echo '<div class="ch-extra-sections">';

        if ($intro_title || $intro_text) {
            echo '<section class="ch-extra-section ch-extra-intro">';
            echo '<div class="ch-extra-inner">';
            echo '<p class="ch-extra-kicker">' . esc_html($page_label) . '</p>';
            if ($intro_title) echo '<h2>' . esc_html($intro_title) . '</h2>';
            if ($intro_text) echo '<p>' . esc_html($intro_text) . '</p>';
            echo '</div>';
            echo '</section>';
        }

        if ($split_title || $split_text || $split_image) {
            echo '<section class="ch-extra-section ch-extra-split">';
            echo '<div class="ch-extra-inner ch-extra-split__grid">';
            echo '<div>';
            if ($split_title) echo '<h2>' . esc_html($split_title) . '</h2>';
            if ($split_text) echo '<p>' . esc_html($split_text) . '</p>';
            echo '</div>';
            if ($split_image) {
                echo '<figure class="ch-extra-split__image">';
                echo wp_get_attachment_image($split_image, 'large');
                echo '</figure>';
            }
            echo '</div>';
            echo '</section>';
        }

        $has_cards = false;
        for ($i = 1; $i <= 4; $i++) {
            if (naigai_ch_meta($post_id, "{$prefix}_card{$i}_title") || naigai_ch_meta($post_id, "{$prefix}_card{$i}_text") || get_post_meta($post_id, "{$prefix}_card{$i}_image_id", true)) {
                $has_cards = true;
                break;
            }
        }

        if ($has_cards) {
            echo '<section class="ch-extra-section ch-extra-cards">';
            echo '<div class="ch-extra-inner">';
            echo '<h2>' . esc_html($page_label) . 'のポイント</h2>';
            echo '<div class="ch-extra-card-grid">';
            for ($i = 1; $i <= 4; $i++) {
                $title = naigai_ch_meta($post_id, "{$prefix}_card{$i}_title");
                $text  = naigai_ch_meta($post_id, "{$prefix}_card{$i}_text");
                $img   = absint(get_post_meta($post_id, "{$prefix}_card{$i}_image_id", true));

                if (!$title && !$text && !$img) {
                    continue;
                }

                echo '<article class="ch-extra-card">';
                if ($img) echo '<figure>' . wp_get_attachment_image($img, 'medium_large') . '</figure>';
                if ($title) echo '<h3>' . esc_html($title) . '</h3>';
                if ($text) echo '<p>' . esc_html($text) . '</p>';
                echo '</article>';
            }
            echo '</div>';
            echo '</div>';
            echo '</section>';
        }

        $has_faq = false;
        for ($i = 1; $i <= 6; $i++) {
            if (naigai_ch_meta($post_id, "{$prefix}_faq{$i}_q") || naigai_ch_meta($post_id, "{$prefix}_faq{$i}_a")) {
                $has_faq = true;
                break;
            }
        }

        if ($has_faq) {
            echo '<section class="ch-extra-section ch-extra-faq">';
            echo '<div class="ch-extra-inner">';
            echo '<h2>Q&A</h2>';
            for ($i = 1; $i <= 6; $i++) {
                $q = naigai_ch_meta($post_id, "{$prefix}_faq{$i}_q");
                $a = naigai_ch_meta($post_id, "{$prefix}_faq{$i}_a");

                if (!$q && !$a) {
                    continue;
                }

                echo '<details class="ch-extra-faq-item"' . ($i === 1 ? ' open' : '') . '>';
                echo '<summary>' . esc_html($q ?: '質問') . '</summary>';
                if ($a) echo '<p>' . esc_html($a) . '</p>';
                echo '</details>';
            }
            echo '</div>';
            echo '</section>';
        }

        if ($prefix === '_ch_contact') {
            $form_title = naigai_ch_meta($post_id, '_ch_contact_form_title', 'お問い合わせフォーム');
            $form_lead = naigai_ch_meta($post_id, '_ch_contact_form_lead');
            $shortcode = naigai_ch_meta($post_id, '_ch_contact_form_shortcode');

            echo '<section class="ch-extra-section ch-extra-form">';
            echo '<div class="ch-extra-inner">';
            echo '<h2>' . esc_html($form_title) . '</h2>';
            if ($form_lead) echo '<p>' . esc_html($form_lead) . '</p>';
            if ($shortcode) {
                echo do_shortcode($shortcode);
            }
            echo '</div>';
            echo '</section>';
        }

        $cta_title = naigai_ch_meta($post_id, "{$prefix}_cta_title");
        $cta_text  = naigai_ch_meta($post_id, "{$prefix}_cta_text");
        $cta_label = naigai_ch_meta($post_id, "{$prefix}_cta_btn_label", '相談する');
        $cta_url   = naigai_ch_link($post_id, $prefix);

        if ($cta_title || $cta_text || $cta_url) {
            echo '<section class="ch-extra-section ch-extra-cta">';
            echo '<div class="ch-extra-inner">';
            if ($cta_title) echo '<h2>' . esc_html($cta_title) . '</h2>';
            if ($cta_text) echo '<p>' . esc_html($cta_text) . '</p>';
            if ($cta_url) echo '<a class="ch-extra-btn" href="' . esc_url($cta_url) . '">' . esc_html($cta_label) . '</a>';
            echo '</div>';
            echo '</section>';
        }

        echo '</div>';
    }
}

add_action('wp_enqueue_scripts', function () {
    wp_register_style('naigai-ch-extra-sections', false);
    wp_enqueue_style('naigai-ch-extra-sections');

    wp_add_inline_style('naigai-ch-extra-sections', '
.ch-extra-sections{
  width:100%;
  background:#f6f2ea;
}
.ch-extra-section{
  padding:56px 0;
}
.ch-extra-inner{
  width:min(1120px, calc(100% - 40px));
  margin:0 auto;
}
.ch-extra-kicker{
  font-size:12px;
  font-weight:800;
  letter-spacing:.12em;
  color:#9a6a2f;
  margin:0 0 10px;
}
.ch-extra-section h2{
  font-size:clamp(24px, 3vw, 38px);
  line-height:1.35;
  margin:0 0 18px;
}
.ch-extra-section p{
  line-height:1.9;
}
.ch-extra-split__grid{
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(280px,420px);
  gap:32px;
  align-items:center;
}
.ch-extra-split__image img,
.ch-extra-card figure img{
  width:100%;
  height:auto;
  display:block;
  border-radius:18px;
}
.ch-extra-card-grid{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:18px;
}
.ch-extra-card{
  background:#fff;
  border:1px solid rgba(0,0,0,.08);
  border-radius:18px;
  padding:18px;
  box-shadow:0 12px 28px rgba(0,0,0,.06);
}
.ch-extra-card h3{
  font-size:18px;
  margin:12px 0 8px;
}
.ch-extra-faq-item{
  background:#fff;
  border:1px solid rgba(0,0,0,.08);
  border-radius:12px;
  padding:14px 18px;
  margin:0 0 10px;
}
.ch-extra-faq-item summary{
  cursor:pointer;
  font-weight:800;
}
.ch-extra-cta{
  background:#1f2933;
  color:#fff;
}
.ch-extra-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-height:44px;
  padding:0 18px;
  border-radius:999px;
  background:#fff;
  color:#1f2933;
  font-weight:800;
  text-decoration:none;
}
@media(max-width:900px){
  .ch-extra-split__grid,
  .ch-extra-card-grid{
    grid-template-columns:1fr;
  }
}
    ');
});
