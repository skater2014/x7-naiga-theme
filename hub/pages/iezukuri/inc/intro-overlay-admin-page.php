<?php
/**
 * 家づくりイントロ 専用管理メニュー
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_intro_admin_menu')) {
    function naigai_iez_intro_admin_menu(): void
    {
        add_menu_page(
            '家づくりイントロ',
            '家づくりイントロ',
            'edit_pages',
            'naigai-iez-intro',
            'naigai_iez_intro_admin_render_page',
            'dashicons-format-gallery',
            58
        );
    }
}
add_action('admin_menu', 'naigai_iez_intro_admin_menu');

if (!function_exists('naigai_iez_intro_admin_enqueue')) {
    function naigai_iez_intro_admin_enqueue(string $hook): void
    {
        if ($hook !== 'toplevel_page_naigai-iez-intro') {
            return;
        }

        wp_enqueue_media();

        $theme_uri = get_template_directory_uri();
        $theme_dir = get_template_directory();
        $js_rel = '/hub/pages/iezukuri/js/intro-overlay-admin.js';

        wp_enqueue_script(
            'naigai-iez-intro-overlay-admin',
            $theme_uri . $js_rel,
            array('jquery'),
            file_exists($theme_dir . $js_rel) ? (string) filemtime($theme_dir . $js_rel) : null,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'naigai_iez_intro_admin_enqueue');

if (!function_exists('naigai_iez_intro_admin_sanitize')) {
    function naigai_iez_intro_admin_sanitize(array $input): array
    {
        $defaults = naigai_iez_intro_defaults();
        $output = array();

        foreach (array('enabled', 'bgm_enabled', 'show_bgm_button', 'show_skip') as $key) {
            $output[$key] = isset($input[$key]) ? '1' : '0';
        }

        $duration = isset($input['duration']) ? (int) $input['duration'] : 7500;
        $output['duration'] = (string) max(3000, $duration);

        $slide_interval = isset($input['slide_interval']) ? (int) $input['slide_interval'] : 3500;
        $output['slide_interval'] = (string) max(1600, $slide_interval);

        $bgm_delay = isset($input['bgm_delay']) ? (int) $input['bgm_delay'] : 300;
        $output['bgm_delay'] = (string) max(0, $bgm_delay);

        $ratio = isset($input['nasu_ratio']) ? (int) $input['nasu_ratio'] : 60;
        $output['nasu_ratio'] = (string) max(50, min(80, $ratio));

        $motion_duration = isset($input['motion_duration']) ? (int) $input['motion_duration'] : 11000;
        $output['motion_duration'] = (string) max(3000, min(30000, $motion_duration));

        $output['motion_easing'] = isset($input['motion_easing'])
            ? sanitize_text_field(wp_unslash($input['motion_easing']))
            : 'ease-in-out';

        $output['text_motion_enabled'] = isset($input['text_motion_enabled']) ? '1' : '0';

        foreach (array(
            'text_motion_duration',
            'text_motion_delay_side',
            'text_motion_delay_center',
            'text_motion_y',
        ) as $text_motion_key) {
            $output[$text_motion_key] = isset($input[$text_motion_key])
                ? sanitize_text_field(wp_unslash($input[$text_motion_key]))
                : '';
        }

        foreach (array(
            'nasu_motion_preset',
            'nasu_scale_start',
            'nasu_scale_end',
            'nasu_translate_x',
            'nasu_translate_y',
            'tokyo_motion_preset',
            'tokyo_scale_start',
            'tokyo_scale_end',
            'tokyo_translate_x',
            'tokyo_translate_y',
        ) as $motion_key) {
            $output[$motion_key] = isset($input[$motion_key])
                ? sanitize_text_field(wp_unslash($input[$motion_key]))
                : '';
        }

        foreach (array(
            'center_kicker',
            'center_headline',
            'center_lead',
            'nasu_kicker',
            'nasu_title',
            'nasu_lead',
            'tokyo_kicker',
            'tokyo_title',
            'tokyo_lead',
            'bgm_url',
            'nasu_images',
            'tokyo_images',
        ) as $key) {
            $value = isset($input[$key]) ? wp_unslash($input[$key]) : $defaults[$key];
            $output[$key] = sanitize_textarea_field($value);
        }

        return array_merge($defaults, $output);
    }
}

if (!function_exists('naigai_iez_intro_admin_render_page')) {
    function naigai_iez_intro_admin_render_page(): void
    {
        if (!current_user_can('edit_pages')) {
            wp_die('このページを編集する権限がありません。');
        }

        if (
            isset($_POST['naigai_iez_intro_admin_nonce'])
            && wp_verify_nonce($_POST['naigai_iez_intro_admin_nonce'], 'naigai_iez_intro_admin_save')
        ) {
            $settings = isset($_POST['naigai_iez_intro']) && is_array($_POST['naigai_iez_intro'])
                ? naigai_iez_intro_admin_sanitize($_POST['naigai_iez_intro'])
                : naigai_iez_intro_defaults();

            update_option('naigai_iez_intro_settings', $settings, false);

            echo '<div class="notice notice-success is-dismissible"><p>家づくりイントロ設定を保存しました。</p></div>';
        }

        $settings = naigai_iez_intro_settings();
        ?>
        <div class="wrap naigai-iez-intro-admin">
            <h1>家づくりイントロ</h1>

            <p>
                <a class="button button-secondary" href="<?php echo esc_url(home_url('/iezukuri/')); ?>" target="_blank" rel="noopener">/iezukuri/ を確認</a>
            </p>

            <style>
                .naigai-iez-intro-admin .iez-admin-layout {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr) 420px;
                    gap: 24px;
                    max-width: 1280px;
                }
                .naigai-iez-intro-admin .iez-admin-card {
                    background: #fff;
                    border: 1px solid #dcdcde;
                    padding: 20px;
                    box-shadow: 0 1px 2px rgba(0,0,0,.04);
                }
                .naigai-iez-intro-admin .iez-admin-card h2 {
                    margin-top: 0;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #eee;
                }
                .naigai-iez-intro-admin .iez-admin-field {
                    margin: 0 0 20px;
                }
                .naigai-iez-intro-admin input[type="text"],
                .naigai-iez-intro-admin input[type="number"],
                .naigai-iez-intro-admin textarea {
                    width: 100%;
                    max-width: 100%;
                }
                .naigai-iez-intro-admin textarea {
                    min-height: 130px;
                    font-family: Menlo, Monaco, Consolas, monospace;
                }
                .naigai-iez-intro-admin .description {
                    color: #646970;
                }
                .naigai-iez-intro-admin .iez-preview-box {
                    display: grid;
                    grid-template-columns: <?php echo esc_attr($settings['nasu_ratio']); ?>% <?php echo esc_attr((string) (100 - (int) $settings['nasu_ratio'])); ?>%;
                    min-height: 220px;
                    overflow: hidden;
                    border: 1px solid #dcdcde;
                    background: #111;
                }
                .naigai-iez-intro-admin .iez-preview-nasu,
                .naigai-iez-intro-admin .iez-preview-tokyo {
                    display: grid;
                    place-items: center;
                    min-height: 220px;
                    color: #fff;
                    text-align: center;
                }
                .naigai-iez-intro-admin .iez-preview-nasu {
                    background: linear-gradient(135deg, #25452d, #101510);
                }
                .naigai-iez-intro-admin .iez-preview-tokyo {
                    background: linear-gradient(135deg, #111827, #3a342f);
                }
                @media (max-width: 960px) {
                    .naigai-iez-intro-admin .iez-admin-layout {
                        grid-template-columns: 1fr;
                    }
                }
            </style>

            <form method="post">
                <?php wp_nonce_field('naigai_iez_intro_admin_save', 'naigai_iez_intro_admin_nonce'); ?>

                <div class="iez-admin-layout">
                    <div class="iez-admin-card">
                        <h2>基本設定</h2>

                        <p class="iez-admin-field">
                            <label><input type="checkbox" name="naigai_iez_intro[enabled]" value="1" <?php checked($settings['enabled'], '1'); ?>> /iezukuri/ でイントロを表示する</label>
                        </p>

                        <p class="iez-admin-field">
                            <label><input type="checkbox" name="naigai_iez_intro[bgm_enabled]" value="1" <?php checked($settings['bgm_enabled'], '1'); ?>> BGMを自動再生する</label><br>
                            <span class="description">YouTube音源ではなく、メディアにアップした mp3 / m4a を選択してください。ブラウザ制限で自動再生されない場合はBGMボタンで再生します。</span>
                        </p>

                        <p class="iez-admin-field">
                            <label><input type="checkbox" name="naigai_iez_intro[show_bgm_button]" value="1" <?php checked($settings['show_bgm_button'], '1'); ?>> BGM ON/OFF ボタンを表示する</label>
                        </p>

                        <p class="iez-admin-field">
                            <label><input type="checkbox" name="naigai_iez_intro[show_skip]" value="1" <?php checked($settings['show_skip'], '1'); ?>> Skip Intro ボタンを表示する</label>
                        </p>

                        <p class="iez-admin-field">
                            <label>表示時間 ms</label><br>
                            <input type="number" name="naigai_iez_intro[duration]" value="<?php echo esc_attr($settings['duration']); ?>">
                            <span class="description">12000 = 12秒。BGMを聞かせるなら10〜15秒くらいが目安です。</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>画像切り替え間隔 ms</label><br>
                            <input type="number" name="naigai_iez_intro[slide_interval]" value="<?php echo esc_attr($settings['slide_interval']); ?>">
                            <span class="description">3500 = 3.5秒ごとに切り替え。短すぎると落ち着きません。</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>那須側の面積割合 %</label><br>
                            <input type="number" min="50" max="80" name="naigai_iez_intro[nasu_ratio]" value="<?php echo esc_attr($settings['nasu_ratio']); ?>">
                            <span class="description">60なら 那須60% / 東京40%</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>BGM開始遅延 ms</label><br>
                            <input type="number" name="naigai_iez_intro[bgm_delay]" value="<?php echo esc_attr($settings['bgm_delay'] ?? '300'); ?>">
                            <span class="description">300 = 0.3秒後に再生開始。0にすると即再生。</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>BGM URL</label><br>
                            <input type="text" name="naigai_iez_intro[bgm_url]" value="<?php echo esc_attr($settings['bgm_url']); ?>">
                        </p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>表示イメージ</h2>
                        <div class="iez-preview-box">
                            <div class="iez-preview-nasu">
                                <div><strong>NASU <?php echo esc_html($settings['nasu_ratio']); ?>%</strong><br><span>那須側画像</span></div>
                            </div>
                            <div class="iez-preview-tokyo">
                                <div><strong>TOKYO <?php echo esc_html((string) (100 - (int) $settings['nasu_ratio'])); ?>%</strong><br><span>東京側画像</span></div>
                            </div>
                        </div>
                        <p class="description">画像未設定時はテーマ内のデフォルト画像が表示されます。</p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>Burns Effect</h2>

                        <p class="description">
                            基本は zoom-in / zoom-out。左右上下の移動は強くしすぎない方が安全です。
                        </p>

                        <p class="iez-admin-field">
                            <label>エフェクト時間 ms</label><br>
                            <input type="number" name="naigai_iez_intro[motion_duration]" value="<?php echo esc_attr($settings['motion_duration'] ?? '11000'); ?>">
                            <span class="description">11000 = 11秒。標準。</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>easing</label><br>
                            <input type="text" name="naigai_iez_intro[motion_easing]" value="<?php echo esc_attr($settings['motion_easing'] ?? 'ease-in-out'); ?>">
                        </p>

                        <h3>文章アニメーション</h3>

                        <p class="iez-admin-field">
                            <label>
                                <input type="checkbox" name="naigai_iez_intro[text_motion_enabled]" value="1" <?php checked($settings['text_motion_enabled'] ?? '1', '1'); ?>>
                                文章を fade-in させる
                            </label>
                        </p>

                        <p class="iez-admin-field">
                            <label>文章アニメーション時間 ms</label><br>
                            <input type="number" name="naigai_iez_intro[text_motion_duration]" value="<?php echo esc_attr($settings['text_motion_duration'] ?? '900'); ?>">
                            <span class="description">900 = 0.9秒</span>
                        </p>

                        <p class="iez-admin-field">
                            <label>左右文章の表示遅延 ms</label><br>
                            <input type="number" name="naigai_iez_intro[text_motion_delay_side]" value="<?php echo esc_attr($settings['text_motion_delay_side'] ?? '600'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>中央文章の表示遅延 ms</label><br>
                            <input type="number" name="naigai_iez_intro[text_motion_delay_center]" value="<?php echo esc_attr($settings['text_motion_delay_center'] ?? '900'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>文章の上がり幅 px</label><br>
                            <input type="number" name="naigai_iez_intro[text_motion_y]" value="<?php echo esc_attr($settings['text_motion_y'] ?? '18'); ?>">
                        </p>

                        <h3>那須側</h3>

                        <p class="iez-admin-field">
                            <label>プリセット</label><br>
                            <select name="naigai_iez_intro[nasu_motion_preset]">
                                <?php foreach (array('zoom-in','zoom-out','zoom-in-left','zoom-in-right','zoom-in-up','zoom-in-down','none') as $preset) : ?>
                                    <option value="<?php echo esc_attr($preset); ?>" <?php selected($settings['nasu_motion_preset'] ?? 'zoom-in', $preset); ?>><?php echo esc_html($preset); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>

                        <p class="iez-admin-field">
                            <label>開始倍率</label><br>
                            <input type="number" step="0.01" name="naigai_iez_intro[nasu_scale_start]" value="<?php echo esc_attr($settings['nasu_scale_start'] ?? '1.00'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>終了倍率</label><br>
                            <input type="number" step="0.01" name="naigai_iez_intro[nasu_scale_end]" value="<?php echo esc_attr($settings['nasu_scale_end'] ?? '1.08'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>X移動 %</label><br>
                            <input type="number" step="0.1" name="naigai_iez_intro[nasu_translate_x]" value="<?php echo esc_attr($settings['nasu_translate_x'] ?? '0'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>Y移動 %</label><br>
                            <input type="number" step="0.1" name="naigai_iez_intro[nasu_translate_y]" value="<?php echo esc_attr($settings['nasu_translate_y'] ?? '-1'); ?>">
                        </p>

                        <h3>東京側</h3>

                        <p class="iez-admin-field">
                            <label>プリセット</label><br>
                            <select name="naigai_iez_intro[tokyo_motion_preset]">
                                <?php foreach (array('zoom-in','zoom-out','zoom-in-left','zoom-in-right','zoom-in-up','zoom-in-down','none') as $preset) : ?>
                                    <option value="<?php echo esc_attr($preset); ?>" <?php selected($settings['tokyo_motion_preset'] ?? 'zoom-out', $preset); ?>><?php echo esc_html($preset); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>

                        <p class="iez-admin-field">
                            <label>開始倍率</label><br>
                            <input type="number" step="0.01" name="naigai_iez_intro[tokyo_scale_start]" value="<?php echo esc_attr($settings['tokyo_scale_start'] ?? '1.08'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>終了倍率</label><br>
                            <input type="number" step="0.01" name="naigai_iez_intro[tokyo_scale_end]" value="<?php echo esc_attr($settings['tokyo_scale_end'] ?? '1.00'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>X移動 %</label><br>
                            <input type="number" step="0.1" name="naigai_iez_intro[tokyo_translate_x]" value="<?php echo esc_attr($settings['tokyo_translate_x'] ?? '0'); ?>">
                        </p>

                        <p class="iez-admin-field">
                            <label>Y移動 %</label><br>
                            <input type="number" step="0.1" name="naigai_iez_intro[tokyo_translate_y]" value="<?php echo esc_attr($settings['tokyo_translate_y'] ?? '0'); ?>">
                        </p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>中央メッセージ</h2>
                        <p class="iez-admin-field"><label>小見出し</label><br><input type="text" name="naigai_iez_intro[center_kicker]" value="<?php echo esc_attr($settings['center_kicker']); ?>"></p>
                        <p class="iez-admin-field"><label>大見出し</label><br><input type="text" name="naigai_iez_intro[center_headline]" value="<?php echo esc_attr($settings['center_headline']); ?>"></p>
                        <p class="iez-admin-field"><label>リード文</label><br><input type="text" name="naigai_iez_intro[center_lead]" value="<?php echo esc_attr($settings['center_lead']); ?>"></p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>那須側テキスト</h2>
                        <p class="iez-admin-field"><label>小見出し</label><br><input type="text" name="naigai_iez_intro[nasu_kicker]" value="<?php echo esc_attr($settings['nasu_kicker']); ?>"></p>
                        <p class="iez-admin-field"><label>見出し</label><br><input type="text" name="naigai_iez_intro[nasu_title]" value="<?php echo esc_attr($settings['nasu_title']); ?>"></p>
                        <p class="iez-admin-field"><label>説明文</label><br><input type="text" name="naigai_iez_intro[nasu_lead]" value="<?php echo esc_attr($settings['nasu_lead']); ?>"></p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>東京側テキスト</h2>
                        <p class="iez-admin-field"><label>小見出し</label><br><input type="text" name="naigai_iez_intro[tokyo_kicker]" value="<?php echo esc_attr($settings['tokyo_kicker']); ?>"></p>
                        <p class="iez-admin-field"><label>見出し</label><br><input type="text" name="naigai_iez_intro[tokyo_title]" value="<?php echo esc_attr($settings['tokyo_title']); ?>"></p>
                        <p class="iez-admin-field"><label>説明文</label><br><input type="text" name="naigai_iez_intro[tokyo_lead]" value="<?php echo esc_attr($settings['tokyo_lead']); ?>"></p>
                    </div>

                    <div class="iez-admin-card">
                        <h2>那須側 画像</h2>
                        <p class="description">1行に1枚。メディアから複数選択できます。</p>
                        <textarea name="naigai_iez_intro[nasu_images]"><?php echo esc_textarea($settings['nasu_images']); ?></textarea>
                    </div>

                    <div class="iez-admin-card">
                        <h2>東京側 画像</h2>
                        <p class="description">1行に1枚。メディアから複数選択できます。</p>
                        <textarea name="naigai_iez_intro[tokyo_images]"><?php echo esc_textarea($settings['tokyo_images']); ?></textarea>
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large">イントロ設定を保存</button>
                </p>
            </form>
        </div>
        <?php
    }
}
