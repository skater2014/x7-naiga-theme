<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 注文住宅ページビルダー
 * 管理画面メニュー登録
 * =========================================================
 */
if (!function_exists('naigai_register_customhome_builder_admin_menu')) {
    function naigai_register_customhome_builder_admin_menu() {
        add_menu_page(
            '注文住宅ページビルダー',
            '注文住宅ページビルダー',
            'edit_pages',
            'naigai-customhome-builder',
            'naigai_render_customhome_builder_admin_page',
            'dashicons-layout',
            25
        );

        add_submenu_page(
            'naigai-customhome-builder',
            'セクションビルダー',
            'セクションビルダー',
            'edit_pages',
            'naigai-customhome-builder',
            'naigai_render_customhome_builder_admin_page'
        );
    }
    add_action('admin_menu', 'naigai_register_customhome_builder_admin_menu');
}

/**
 * =========================================================
 * 管理画面用CSS
 * =========================================================
 */
if (!function_exists('naigai_enqueue_customhome_builder_admin_assets')) {
    function naigai_enqueue_customhome_builder_admin_assets($hook) {
        if ($hook !== 'toplevel_page_naigai-customhome-builder') {
            return;
        }

        $css_rel = '/hub/pages/iezukuri/admin/css/customhome-builder-admin.css';
        $css_abs = get_template_directory() . $css_rel;

        if (file_exists($css_abs)) {
            wp_enqueue_style(
                'naigai-customhome-builder-admin',
                get_template_directory_uri() . $css_rel,
                array(),
                filemtime($css_abs)
            );
        }
    }
    add_action('admin_enqueue_scripts', 'naigai_enqueue_customhome_builder_admin_assets');
}

/**
 * =========================================================
 * サンプル画面
 * 役割:
 * - いきなり本番実装ではなく、レイアウトの土台だけ見せる
 * - 左: セクション一覧
 * - 右: 設定とプレビューのダミー
 * =========================================================
 */
if (!function_exists('naigai_render_customhome_builder_admin_page')) {
    function naigai_render_customhome_builder_admin_page() {
        ?>
        <div class="wrap naigai-builder-admin">
            <h1>注文住宅ページビルダー</h1>
            <p class="naigai-builder-admin__lead">
                これはサンプル管理画面です。今後ここに、セクション追加・並び替え・プレビューを入れていきます。
            </p>

            <div class="naigai-builder-admin__layout">
                <section class="naigai-builder-admin__panel is-left">
                    <h2>セクション構成</h2>

                    <div class="naigai-builder-admin__section-list">
                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">1 Hero</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>

                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">2 導入（イントロ）</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>

                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">3 3つの視点（3カラム）</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>

                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">4 那須の自然とともに（左テキスト + 右画像）</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>

                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">5 4つのこだわり</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>

                        <div class="naigai-builder-admin__section-item">
                            <span class="naigai-builder-admin__drag">⋮⋮</span>
                            <span class="naigai-builder-admin__label">6 CTA</span>
                            <button class="button button-secondary" type="button">編集</button>
                        </div>
                    </div>

                    <div class="naigai-builder-admin__actions">
                        <button class="button button-primary" type="button">セクションを追加</button>
                    </div>
                </section>

                <section class="naigai-builder-admin__panel is-center">
                    <h2>選択中のセクション設定</h2>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label>セクションタイプ</label></th>
                            <td>
                                <select>
                                    <option>左テキスト + 右画像</option>
                                    <option>3カラムカード</option>
                                    <option>CTA</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>見出し</label></th>
                            <td><input type="text" class="regular-text" value="那須の自然とともに、心地よく暮らす。"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label>本文</label></th>
                            <td><textarea class="large-text" rows="6">那須の豊かな自然環境や四季の移ろいを尊重し、その土地で心地よく暮らすための住まいを目指しています。</textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label>画像</label></th>
                            <td>
                                <button class="button" type="button">画像を選択</button>
                            </td>
                        </tr>
                    </table>
                </section>

                <section class="naigai-builder-admin__panel is-right">
                    <h2>プレビュー（サンプル）</h2>

                    <div class="naigai-builder-admin__preview">
                        <div class="naigai-builder-admin__preview-hero">Hero Preview</div>
                        <div class="naigai-builder-admin__preview-nav">ローカルナビ Preview</div>
                        <div class="naigai-builder-admin__preview-block">イントロ</div>
                        <div class="naigai-builder-admin__preview-block">3カラムカード</div>
                        <div class="naigai-builder-admin__preview-block is-large">左テキスト + 右画像</div>
                        <div class="naigai-builder-admin__preview-block">4つのこだわり</div>
                        <div class="naigai-builder-admin__preview-block is-dark">CTA</div>
                    </div>
                </section>
            </div>
        </div>
        <?php
    }
}
