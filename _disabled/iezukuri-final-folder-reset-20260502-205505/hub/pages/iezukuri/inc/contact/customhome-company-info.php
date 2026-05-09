<?php
if (!defined('ABSPATH')) {
    exit;
}

function naigai_ch_company_is_target($post_id)
{
    if (!$post_id || get_page_template_slug($post_id) !== 'page-construction-hub-sub.php') {
        return false;
    }

    $slug = (string) get_post_field('post_name', $post_id);
    $layout = (string) get_post_meta($post_id, '_ch_subpage_template', true);

    return $slug === 'company' || $layout === 'company';
}

function naigai_ch_company_find_meta($source_id, $type)
{
    $meta = get_post_meta((int) $source_id);

    foreach ($meta as $key => $values) {
        $value = maybe_unserialize($values[0] ?? '');
        $text = is_array($value) ? print_r($value, true) : (string) $value;

        if ($type === 'map') {
            if (
                stripos($key, 'map') !== false ||
                stripos($key, 'google') !== false ||
                stripos($key, 'iframe') !== false ||
                stripos($text, 'google.com/maps') !== false ||
                stripos($text, '<iframe') !== false
            ) {
                return $text;
            }
        }

        if ($type === 'address') {
            if (
                stripos($key, 'address') !== false ||
                stripos($key, '住所') !== false ||
                stripos($key, 'location') !== false
            ) {
                return $text;
            }
        }
    }

    return '';
}

function naigai_ch_company_source_page_id($post_id)
{
    $manual = absint(get_post_meta($post_id, '_ch_company_source_page_id', true));
    if ($manual) {
        return $manual;
    }

    $company = get_page_by_path('company');
    return $company ? (int) $company->ID : 0;
}

function naigai_ch_company_get_access_data($post_id)
{
    $source_type = (string) get_post_meta($post_id, '_ch_company_info_source', true);
    if ($source_type === '') {
        $source_type = 'parent';
    }

    $title = get_post_meta($post_id, '_ch_company_map_title', true);
    $text = get_post_meta($post_id, '_ch_company_map_text', true);

    if ($source_type === 'custom') {
        return array(
            'title' => $title !== '' ? $title : 'アクセス',
            'text' => $text,
            'address' => get_post_meta($post_id, '_ch_company_address', true),
            'map' => get_post_meta($post_id, '_ch_company_map_iframe', true),
        );
    }

    $source_id = naigai_ch_company_source_page_id($post_id);

    return array(
        'title' => $title !== '' ? $title : 'アクセス',
        'text' => $text,
        'address' => naigai_ch_company_find_meta($source_id, 'address'),
        'map' => naigai_ch_company_find_meta($source_id, 'map'),
    );
}

function naigai_ch_company_allowed_iframe()
{
    return array(
        'iframe' => array(
            'src' => true,
            'width' => true,
            'height' => true,
            'style' => true,
            'allowfullscreen' => true,
            'loading' => true,
            'referrerpolicy' => true,
            'title' => true,
            'class' => true,
        ),
    );
}

function naigai_ch_company_info_box($post)
{
    wp_nonce_field('naigai_ch_company_info_save', 'naigai_ch_company_info_nonce');

    $source = (string) get_post_meta($post->ID, '_ch_company_info_source', true);
    $source = $source !== '' ? $source : 'parent';

    $source_page_id = naigai_ch_company_source_page_id($post->ID);

    $pages = get_pages(array(
        'post_status' => array('publish', 'draft', 'private'),
        'sort_column' => 'menu_order,post_title',
        'sort_order' => 'ASC',
    ));
    ?>
    <div class="naigai-admin-guide">
        <strong>会社情報の取得方法</strong>
        <p>住所・Google Mapを /company から参照するか、このページ専用で入力するか選びます。</p>
    </div>

    <div class="naigai-admin-section">
        <h3>会社情報の取得方法</h3>

        <p>
            <label>
                <input type="radio" name="_ch_company_info_source" value="parent" <?php checked($source, 'parent'); ?>>
                /company の会社情報を使う
            </label>
        </p>

        <p>
            <label>
                <input type="radio" name="_ch_company_info_source" value="custom" <?php checked($source, 'custom'); ?>>
                このページ専用に入力する
            </label>
        </p>

        <p>
            <label>参照元ページ</label><br>
            <select name="_ch_company_source_page_id">
                <option value="0">自動: /company</option>
                <?php foreach ($pages as $page) : ?>
                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($source_page_id, $page->ID); ?>>
                        <?php echo esc_html($page->post_title . ' / ' . get_page_uri($page->ID)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>

    <div class="naigai-admin-section">
        <h3>表示文言</h3>

        <p>
            <label>見出し</label><br>
            <input type="text" class="regular-text" name="_ch_company_map_title" value="<?php echo esc_attr(get_post_meta($post->ID, '_ch_company_map_title', true)); ?>" placeholder="アクセス">
        </p>

        <p>
            <label>説明文</label><br>
            <textarea class="large-text" rows="3" name="_ch_company_map_text"><?php echo esc_textarea(get_post_meta($post->ID, '_ch_company_map_text', true)); ?></textarea>
        </p>
    </div>

    <div class="naigai-admin-section">
        <h3>このページ専用の会社情報</h3>
        <p class="description">「このページ専用に入力する」を選んだ場合だけ使われます。</p>

        <p>
            <label>住所</label><br>
            <textarea class="large-text" rows="3" name="_ch_company_address"><?php echo esc_textarea(get_post_meta($post->ID, '_ch_company_address', true)); ?></textarea>
        </p>

        <p>
            <label>Google Map iframe</label><br>
            <textarea class="large-text code" rows="5" name="_ch_company_map_iframe"><?php echo esc_textarea(get_post_meta($post->ID, '_ch_company_map_iframe', true)); ?></textarea>
        </p>
    </div>
    <?php
}

function naigai_ch_company_info_add_box()
{
    global $post;

    if (!$post || !naigai_ch_company_is_target($post->ID)) {
        return;
    }

    add_meta_box(
        'naigai_ch_company_info_box',
        '会社情報・Google Map設定',
        'naigai_ch_company_info_box',
        'page',
        'normal',
        'high'
    );
}
// DISABLED: unified /iezukuri reflected metabox handles these fields.
// add_action('add_meta_boxes', 'naigai_ch_company_info_add_box');

function naigai_ch_company_info_save($post_id)
{
    if (!naigai_ch_company_is_target($post_id)) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['naigai_ch_company_info_nonce']) || !wp_verify_nonce($_POST['naigai_ch_company_info_nonce'], 'naigai_ch_company_info_save')) {
        return;
    }

    $source = isset($_POST['_ch_company_info_source']) ? sanitize_key(wp_unslash($_POST['_ch_company_info_source'])) : 'parent';
    if (!in_array($source, array('parent', 'custom'), true)) {
        $source = 'parent';
    }

    update_post_meta($post_id, '_ch_company_info_source', $source);
    update_post_meta($post_id, '_ch_company_source_page_id', isset($_POST['_ch_company_source_page_id']) ? absint($_POST['_ch_company_source_page_id']) : 0);
    update_post_meta($post_id, '_ch_company_map_title', isset($_POST['_ch_company_map_title']) ? sanitize_text_field(wp_unslash($_POST['_ch_company_map_title'])) : '');
    update_post_meta($post_id, '_ch_company_map_text', isset($_POST['_ch_company_map_text']) ? sanitize_textarea_field(wp_unslash($_POST['_ch_company_map_text'])) : '');
    update_post_meta($post_id, '_ch_company_address', isset($_POST['_ch_company_address']) ? sanitize_textarea_field(wp_unslash($_POST['_ch_company_address'])) : '');

    $iframe = isset($_POST['_ch_company_map_iframe']) ? wp_unslash($_POST['_ch_company_map_iframe']) : '';
    update_post_meta($post_id, '_ch_company_map_iframe', wp_kses($iframe, naigai_ch_company_allowed_iframe()));
}
add_action('save_post_page', 'naigai_ch_company_info_save', 36);

function naigai_ch_render_company_access_section($post_id)
{
    $data = naigai_ch_company_get_access_data($post_id);

    if (trim((string) $data['address']) === '' && trim((string) $data['map']) === '') {
        return;
    }
    ?>
    <section class="ch-section-block ch-company-map-section">
        <div class="ch-section-head">
            <span class="ch-eyebrow">ACCESS</span>
            <h2><?php echo esc_html($data['title']); ?></h2>

            <?php if ($data['text'] !== '') : ?>
                <p><?php echo nl2br(esc_html($data['text'])); ?></p>
            <?php endif; ?>

            <?php if ($data['address'] !== '') : ?>
                <p class="ch-company-map-address"><?php echo nl2br(esc_html($data['address'])); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($data['map'] !== '') : ?>
            <div class="ch-company-map-embed">
                <?php echo wp_kses($data['map'], naigai_ch_company_allowed_iframe()); ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
}
