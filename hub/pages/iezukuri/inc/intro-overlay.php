<?php
/**
 * /iezukuri/ intro overlay
 *
 * - /iezukuri/ だけに表示
 * - 専用メニュー「家づくりイントロ」で設定
 * - 画像未設定でもデフォルト画像を表示
 * - Burns effect はCSSで標準有効
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_intro_defaults')) {
    function naigai_iez_intro_defaults(): array
    {
        return array(
            'enabled' => '1',
            'duration' => '12000',
            'slide_interval' => '3500',
            'bgm_delay' => '300',
            'nasu_ratio' => '60',

            'bgm_enabled' => '0',
            'show_bgm_button' => '1',
            'bgm_url' => '',

            'show_skip' => '1',

            'center_kicker' => 'NASU CUSTOM HOME',
            'center_headline' => '東京から、那須でつくる住まいへ。',
            'center_lead' => '自然と暮らしが近づく那須で、注文住宅という新しい選択を。',

            'nasu_kicker' => 'NASU STORY',
            'nasu_title' => '那須へようこそ。',
            'nasu_lead' => '豊かな自然と四季の彩りに包まれて。',

            'tokyo_kicker' => 'TOKYO STORY',
            'tokyo_title' => '東京から。',
            'tokyo_lead' => '都市の暮らしを見直し、新しい住まいへ。',

            'nasu_images' => '',
            'tokyo_images' => '',
        );
    }
}

if (!function_exists('naigai_iez_intro_settings')) {
    function naigai_iez_intro_settings(): array
    {
        $saved = get_option('naigai_iez_intro_settings', array());

        if (!is_array($saved)) {
            $saved = array();
        }

        return array_merge(naigai_iez_intro_defaults(), $saved);
    }
}

if (!function_exists('naigai_iez_intro_is_target_page')) {
    function naigai_iez_intro_is_target_page(): bool
    {
        return is_page('iezukuri');
    }
}

if (!function_exists('naigai_iez_intro_lines_to_urls')) {
    function naigai_iez_intro_lines_to_urls(string $text): array
    {
        $items = array();
        $lines = preg_split('/\R/u', $text);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line !== '') {
                $items[] = esc_url_raw($line);
            }
        }

        return $items;
    }
}

if (!function_exists('naigai_iez_intro_default_image_url')) {
    function naigai_iez_intro_default_image_url(string $side): string
    {
        $file = $side === 'tokyo' ? 'default-tokyo.svg' : 'default-nasu.svg';
        return get_template_directory_uri() . '/hub/pages/iezukuri/assets/intro/' . $file;
    }
}

if (!function_exists('naigai_iez_intro_enqueue')) {
    function naigai_iez_intro_enqueue(): void
    {
        $settings = naigai_iez_intro_settings();

        if (!naigai_iez_intro_is_target_page() || $settings['enabled'] !== '1') {
            return;
        }

        $theme_uri = get_template_directory_uri();
        $theme_dir = get_template_directory();

        $css_rel = '/hub/pages/iezukuri/css/top/intro-overlay.css';
        $js_rel  = '/hub/pages/iezukuri/js/intro-overlay.js';

        wp_enqueue_style(
            'naigai-iez-intro-overlay',
            $theme_uri . $css_rel,
            array(),
            file_exists($theme_dir . $css_rel) ? (string) filemtime($theme_dir . $css_rel) : null
        );

        wp_enqueue_script(
            'naigai-iez-intro-overlay',
            $theme_uri . $js_rel,
            array(),
            file_exists($theme_dir . $js_rel) ? (string) filemtime($theme_dir . $js_rel) : null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'naigai_iez_intro_enqueue', 40);

if (!function_exists('naigai_iez_intro_render')) {
    function naigai_iez_intro_render(): void
    {
        static $rendered = false;

        $settings = naigai_iez_intro_settings();

        if ($rendered || !naigai_iez_intro_is_target_page() || $settings['enabled'] !== '1') {
            return;
        }

        $rendered = true;

        $duration = max(3000, (int) $settings['duration']);
        $slide_interval = max(1600, (int) $settings['slide_interval']);
        $bgm_delay = max(0, (int) ($settings['bgm_delay'] ?? 300));

        $ratio = (int) $settings['nasu_ratio'];
        $ratio = max(50, min(80, $ratio));

        $nasu_images = naigai_iez_intro_lines_to_urls((string) $settings['nasu_images']);
        $tokyo_images = naigai_iez_intro_lines_to_urls((string) $settings['tokyo_images']);

        if (!$nasu_images) {
            $nasu_images = array(naigai_iez_intro_default_image_url('nasu'));
        }

        if (!$tokyo_images) {
            $tokyo_images = array(naigai_iez_intro_default_image_url('tokyo'));
        }

        $bgm_url = esc_url((string) $settings['bgm_url']);
        $bgm_enabled = $settings['bgm_enabled'] === '1';
        $show_bgm_button = $settings['show_bgm_button'] === '1';
        $show_skip = $settings['show_skip'] === '1';

        // Motion settings: 空欄や0化けで画像が潰れないように必ず標準値へ戻す。
        $intro_float = static function ($value, float $default, float $min = -20, float $max = 20): float {
            if ($value === '' || $value === null) {
                return $default;
            }

            $num = (float) $value;
            return max($min, min($max, $num));
        };

        $motion_duration = max(3000, (int) ($settings['motion_duration'] ?? 11000));
        $motion_easing = preg_replace('/[^a-zA-Z0-9\-\.\(\), ]/', '', (string) ($settings['motion_easing'] ?? 'ease-in-out'));

        $nasu_scale_start = $intro_float($settings['nasu_scale_start'] ?? null, 1.00, 0.8, 1.5);
        $nasu_scale_end = $intro_float($settings['nasu_scale_end'] ?? null, 1.10, 0.8, 1.5);
        $nasu_translate_x = $intro_float($settings['nasu_translate_x'] ?? null, 0, -8, 8);
        $nasu_translate_y = $intro_float($settings['nasu_translate_y'] ?? null, -1, -8, 8);

        $tokyo_scale_start = $intro_float($settings['tokyo_scale_start'] ?? null, 1.10, 0.8, 1.5);
        $tokyo_scale_end = $intro_float($settings['tokyo_scale_end'] ?? null, 1.00, 0.8, 1.5);
        $tokyo_translate_x = $intro_float($settings['tokyo_translate_x'] ?? null, 0, -8, 8);
        $tokyo_translate_y = $intro_float($settings['tokyo_translate_y'] ?? null, 0, -8, 8);

        $text_motion_enabled = (($settings['text_motion_enabled'] ?? '1') === '1') ? 1 : 0;
        $text_motion_duration = max(200, (int) ($settings['text_motion_duration'] ?? 900));
        $text_motion_delay_side = max(0, (int) ($settings['text_motion_delay_side'] ?? 600));
        $text_motion_delay_center = max(0, (int) ($settings['text_motion_delay_center'] ?? 900));
        $text_motion_y = $intro_float($settings['text_motion_y'] ?? null, 18, 0, 80);
        $motion_duration = max(3000, (int) ($settings['motion_duration'] ?? 11000));
        $motion_easing = preg_replace('/[^a-zA-Z0-9\-\.\(\), ]/', '', (string) ($settings['motion_easing'] ?? 'ease-in-out'));

        $intro_float = static function ($value, float $default, float $min = -20, float $max = 20): float {
            if ($value === '' || $value === null) {
                return $default;
            }

            $num = (float) $value;

            if ($num == 0.0 && !in_array((string) $value, array('0', '0.0', '0.00'), true)) {
                return $default;
            }

            return max($min, min($max, $num));
        };

        $nasu_scale_start = $intro_float($settings['nasu_scale_start'] ?? null, 1.00, 0.8, 1.5);
        $nasu_scale_end = $intro_float($settings['nasu_scale_end'] ?? null, 1.10, 0.8, 1.5);
        $nasu_translate_x = $intro_float($settings['nasu_translate_x'] ?? null, 0, -8, 8);
        $nasu_translate_y = $intro_float($settings['nasu_translate_y'] ?? null, -1, -8, 8);

        $tokyo_scale_start = $intro_float($settings['tokyo_scale_start'] ?? null, 1.10, 0.8, 1.5);
        $tokyo_scale_end = $intro_float($settings['tokyo_scale_end'] ?? null, 1.00, 0.8, 1.5);
        $tokyo_translate_x = $intro_float($settings['tokyo_translate_x'] ?? null, 0, -8, 8);
        $tokyo_translate_y = $intro_float($settings['tokyo_translate_y'] ?? null, 0, -8, 8);
        ?>
        <div
            class="iez-intro-overlay"
            style="
                --iez-intro-nasu-ratio: <?php echo esc_attr((string) $ratio); ?>%;
                --iez-motion-duration: <?php echo esc_attr((string) $motion_duration); ?>ms;
                --iez-motion-easing: <?php echo esc_attr($motion_easing); ?>;
                --iez-nasu-scale-start: <?php echo esc_attr((string) $nasu_scale_start); ?>;
                --iez-nasu-scale-end: <?php echo esc_attr((string) $nasu_scale_end); ?>;
                --iez-nasu-x: <?php echo esc_attr((string) $nasu_translate_x); ?>%;
                --iez-nasu-y: <?php echo esc_attr((string) $nasu_translate_y); ?>%;
                --iez-tokyo-scale-start: <?php echo esc_attr((string) $tokyo_scale_start); ?>;
                --iez-tokyo-scale-end: <?php echo esc_attr((string) $tokyo_scale_end); ?>;
                --iez-tokyo-x: <?php echo esc_attr((string) $tokyo_translate_x); ?>%;
                --iez-tokyo-y: <?php echo esc_attr((string) $tokyo_translate_y); ?>%;
            
                --iez-motion-duration: <?php echo esc_attr((string) $motion_duration); ?>ms;
                --iez-motion-easing: <?php echo esc_attr($motion_easing); ?>;
                --iez-nasu-scale-start: <?php echo esc_attr((string) $nasu_scale_start); ?>;
                --iez-nasu-scale-end: <?php echo esc_attr((string) $nasu_scale_end); ?>;
                --iez-nasu-x: <?php echo esc_attr((string) $nasu_translate_x); ?>%;
                --iez-nasu-y: <?php echo esc_attr((string) $nasu_translate_y); ?>%;
                --iez-tokyo-scale-start: <?php echo esc_attr((string) $tokyo_scale_start); ?>;
                --iez-tokyo-scale-end: <?php echo esc_attr((string) $tokyo_scale_end); ?>;
                --iez-tokyo-x: <?php echo esc_attr((string) $tokyo_translate_x); ?>%;
                --iez-tokyo-y: <?php echo esc_attr((string) $tokyo_translate_y); ?>%;
                --iez-text-motion-enabled: <?php echo esc_attr((string) $text_motion_enabled); ?>;
                --iez-text-motion-duration: <?php echo esc_attr((string) $text_motion_duration); ?>ms;
                --iez-text-delay-side: <?php echo esc_attr((string) $text_motion_delay_side); ?>ms;
                --iez-text-delay-center: <?php echo esc_attr((string) $text_motion_delay_center); ?>ms;
                --iez-text-y: <?php echo esc_attr((string) $text_motion_y); ?>px;
        "
            data-duration="<?php echo esc_attr((string) $duration); ?>"
            data-slide-interval="<?php echo esc_attr((string) $slide_interval); ?>"
            data-bgm-delay="<?php echo esc_attr((string) $bgm_delay); ?>"
            data-bgm-enabled="<?php echo esc_attr($bgm_enabled ? '1' : '0'); ?>"
            aria-label="家づくりイントロ"
        >
            <div class="iez-intro-overlay__visual" aria-hidden="true">
                <div class="iez-intro-overlay__side iez-intro-overlay__side--nasu">
                    <?php foreach ($nasu_images as $i => $url) : ?>
                        <span
                            class="iez-intro-overlay__slide <?php echo $i === 0 ? 'is-active' : ''; ?>"
                            style="background-image:url('<?php echo esc_url($url); ?>')"
                        ></span>
                    <?php endforeach; ?>

                    <div class="iez-intro-overlay__caption is-nasu">
                        <p><?php echo esc_html((string) $settings['nasu_kicker']); ?></p>
                        <h2><?php echo esc_html((string) $settings['nasu_title']); ?></h2>
                        <span><?php echo esc_html((string) $settings['nasu_lead']); ?></span>
                    </div>
                </div>

                <div class="iez-intro-overlay__side iez-intro-overlay__side--tokyo">
                    <?php foreach ($tokyo_images as $i => $url) : ?>
                        <span
                            class="iez-intro-overlay__slide <?php echo $i === 0 ? 'is-active' : ''; ?>"
                            style="background-image:url('<?php echo esc_url($url); ?>')"
                        ></span>
                    <?php endforeach; ?>

                    <div class="iez-intro-overlay__caption is-tokyo">
                        <p><?php echo esc_html((string) $settings['tokyo_kicker']); ?></p>
                        <h2><?php echo esc_html((string) $settings['tokyo_title']); ?></h2>
                        <span><?php echo esc_html((string) $settings['tokyo_lead']); ?></span>
                    </div>
                </div>
            </div>

            <div class="iez-intro-overlay__center">
                <p class="iez-intro-overlay__kicker"><?php echo esc_html((string) $settings['center_kicker']); ?></p>
                <h1><?php echo esc_html((string) $settings['center_headline']); ?></h1>
                <p><?php echo esc_html((string) $settings['center_lead']); ?></p>
            </div>

            <div class="iez-intro-overlay__progress" aria-hidden="true">
                <span></span>
            </div>

            <?php if ($show_bgm_button) : ?>
                <button class="iez-intro-overlay__sound" type="button" aria-pressed="false">
                    ♪ BGM OFF
                </button>
            <?php endif; ?>

            <?php if ($show_skip) : ?>
                <button class="iez-intro-overlay__skip" type="button">
                    Skip Intro
                </button>
            <?php endif; ?>

            <?php if ($bgm_url) : ?>
                <audio class="iez-intro-overlay__audio" src="<?php echo esc_url($bgm_url); ?>" preload="auto" loop></audio>
            <?php endif; ?>
        </div>
        <?php
    }
}
add_action('wp_body_open', 'naigai_iez_intro_render', 5);
add_action('wp_footer', 'naigai_iez_intro_render', 1);

$naigai_iez_intro_admin_page = __DIR__ . '/intro-overlay-admin-page.php';
if (file_exists($naigai_iez_intro_admin_page)) {
    require_once $naigai_iez_intro_admin_page;
}


// 家づくりイントロ: ドラッグ&ドロップアップロード
$naigai_iez_intro_drop_upload = __DIR__ . '/intro-drop-upload.php';
if (file_exists($naigai_iez_intro_drop_upload)) {
    require_once $naigai_iez_intro_drop_upload;
}

