<?php
if (!defined('ABSPATH')) exit;

/**
 * 見学受付一覧 管理画面
 * - 管理画面メタ保存
 * - FullCalendar 用 AJAX
 * - Property List 用 AJAX
 * - Reservation Modal 用 AJAX
 */

/* =========================================================
 * choices
 * ========================================================= */
if (!function_exists('naigai_get_viewing_type_choices')) {
    function naigai_get_viewing_type_choices()
    {
        $choices = [
            'land'       => '土地',
            'rental'     => '賃貸',
            'commercial' => '商業施設',
            'new_house'  => '新築住宅',
            'used_house' => '中古住宅',
            'renovation' => 'リノベーション住宅',
        ];
        return apply_filters('naigai_viewing_type_choices', $choices);
    }
}

if (!function_exists('naigai_get_area_choices')) {
    function naigai_get_area_choices()
    {
        $choices = [
            'nasu'         => '那須町',
            'nishinasuno'  => '西那須野',
            'nasushiobara' => '那須塩原市',
        ];
        return apply_filters('naigai_property_area_choices', $choices);
    }
}

/* =========================================================
 * utils
 * ========================================================= */
if (!function_exists('naigai_admin_short_title')) {
    function naigai_admin_short_title($title, $max = 28)
    {
        $title = wp_strip_all_tags((string) $title);
        if (mb_strlen($title) <= $max) return $title;
        return mb_substr($title, 0, $max) . '…';
    }
}

if (!function_exists('naigai_make_calendar_short_title')) {
    function naigai_make_calendar_short_title($title, $max = 16)
    {
        $title = wp_strip_all_tags((string) $title);
        $title = preg_replace('/^那須\s*不動産\s*[|｜]\s*/u', '', $title);
        $title = preg_replace('/[―—\-].*$/u', '', $title);
        $title = trim((string) $title);

        if (mb_strlen($title) <= $max) return $title;
        return mb_substr($title, 0, $max) . '…';
    }
}

if (!function_exists('naigai_format_period_label')) {
    function naigai_format_period_label($start_date, $end_date = '')
    {
        if (!$start_date) return '';
        $s = date_i18n('n月j日', strtotime($start_date));
        if (!$end_date) return $s;
        $e = date_i18n('n月j日', strtotime($end_date));
        return $s . '〜' . $e;
    }
}

if (!function_exists('naigai_end_exclusive')) {
    function naigai_end_exclusive($end_date)
    {
        if (!$end_date) return '';
        return date('Y-m-d', strtotime($end_date . ' +1 day'));
    }
}

if (!function_exists('naigai_sanitize_weekdays')) {
    function naigai_sanitize_weekdays($raw)
    {
        $out = [];

        if (is_array($raw)) {
            foreach ($raw as $v) {
                $n = (int) $v;
                if ($n >= 1 && $n <= 7) $out[] = $n;
            }
        } elseif (is_string($raw) && $raw !== '') {
            foreach (explode(',', $raw) as $v) {
                $n = (int) trim($v);
                if ($n >= 1 && $n <= 7) $out[] = $n;
            }
        }

        $out = array_values(array_unique($out));
        sort($out);
        return $out;
    }
}

if (!function_exists('naigai_sanitize_time_value')) {
    function naigai_sanitize_time_value($value)
    {
        $value = trim((string) $value);
        if ($value === '') return '';

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        return '';
    }
}

if (!function_exists('naigai_format_time_range')) {
    function naigai_format_time_range($start, $end)
    {
        $start = trim((string) $start);
        $end   = trim((string) $end);

        if ($start !== '' && $end !== '') return $start . '〜' . $end;
        if ($start !== '') return $start . '〜';
        if ($end !== '') return '〜' . $end;
        return '';
    }
}

if (!function_exists('naigai_get_property_price_value')) {
    function naigai_get_property_price_value($post_id)
    {
        $candidates = [
            '_naigai_property_price',
            'Price',
            'NewPrice',
            'price',
        ];

        foreach ($candidates as $key) {
            $value = (string) get_post_meta($post_id, $key, true);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}

/* =========================================================
 * meta getter / setter
 * ========================================================= */
if (!function_exists('naigai_in_category_tree')) {
    function naigai_in_category_tree($post_id, $root_slug)
    {
        $cats = get_the_category($post_id);
        if (empty($cats) || is_wp_error($cats)) return false;

        foreach ($cats as $cat) {
            if (!($cat instanceof WP_Term)) continue;

            if ($cat->slug === $root_slug) return true;

            $ancestors = get_ancestors($cat->term_id, 'category');
            if (!empty($ancestors)) {
                foreach ($ancestors as $aid) {
                    $t = get_term($aid, 'category');
                    if ($t && !is_wp_error($t) && $t->slug === $root_slug) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

if (!function_exists('naigai_guess_viewing_type')) {
    function naigai_guess_viewing_type($post_id)
    {
        if (get_post_type($post_id) === 'house') return 'new_house';
        if (naigai_in_category_tree($post_id, 'naigai-tochi')) return 'land';
        if (naigai_in_category_tree($post_id, 'naigai-construction')) return 'used_house';

        return 'used_house';
    }
}

if (!function_exists('naigai_get_viewing_meta')) {
    function naigai_get_viewing_meta($post_id)
    {
        $types = naigai_get_viewing_type_choices();
        $areas = naigai_get_area_choices();

        $type = (string) get_post_meta($post_id, '_naigai_viewing_type', true);
        if (!$type || !isset($types[$type])) {
            $type = naigai_guess_viewing_type($post_id);
        }

        $area = (string) get_post_meta($post_id, '_naigai_property_area', true);
        if ($area && !isset($areas[$area])) {
            $area = '';
        }

        $weekdays = naigai_sanitize_weekdays(get_post_meta($post_id, '_naigai_viewing_weekdays', true));

        return [
            'enabled'     => (string) get_post_meta($post_id, '_naigai_viewing_enabled', true),
            'type'        => $type,
            'start_date'  => (string) get_post_meta($post_id, '_naigai_viewing_start_date', true),
            'end_date'    => (string) get_post_meta($post_id, '_naigai_viewing_end_date', true),
            'cta_label'   => (string) get_post_meta($post_id, '_naigai_viewing_cta_label', true),
            'staff'       => (string) get_post_meta($post_id, '_naigai_viewing_staff', true),
            'repeat_mode' => (string) get_post_meta($post_id, '_naigai_viewing_repeat_mode', true),
            'weekdays'    => $weekdays,
            'time_start'  => (string) get_post_meta($post_id, '_naigai_viewing_time_start', true),
            'time_end'    => (string) get_post_meta($post_id, '_naigai_viewing_time_end', true),
            'price'       => naigai_get_property_price_value($post_id),
            'area'        => $area,
            'options'     => (string) get_post_meta($post_id, '_naigai_property_options', true),
        ];
    }
}

if (!function_exists('naigai_save_viewing_meta')) {
    function naigai_save_viewing_meta($post_id, $raw)
    {
        $types = naigai_get_viewing_type_choices();
        $areas = naigai_get_area_choices();

        $enabled = !empty($raw['naigai_viewing_enabled']) ? '1' : '0';

        $type = isset($raw['naigai_viewing_type'], $types[$raw['naigai_viewing_type']])
            ? sanitize_key($raw['naigai_viewing_type'])
            : naigai_guess_viewing_type($post_id);

        $start_date = !empty($raw['naigai_viewing_start_date'])
            ? sanitize_text_field($raw['naigai_viewing_start_date'])
            : '';

        $end_date = !empty($raw['naigai_viewing_end_date'])
            ? sanitize_text_field($raw['naigai_viewing_end_date'])
            : '';

        if ($start_date && $end_date && strtotime($start_date) > strtotime($end_date)) {
            $tmp = $start_date;
            $start_date = $end_date;
            $end_date = $tmp;
        }

        $cta_label = !empty($raw['naigai_viewing_cta_label'])
            ? sanitize_text_field($raw['naigai_viewing_cta_label'])
            : '見学申込';

        $staff = !empty($raw['naigai_viewing_staff'])
            ? sanitize_text_field($raw['naigai_viewing_staff'])
            : '';

        $time_start = !empty($raw['naigai_viewing_time_start'])
            ? naigai_sanitize_time_value($raw['naigai_viewing_time_start'])
            : '';

        $time_end = !empty($raw['naigai_viewing_time_end'])
            ? naigai_sanitize_time_value($raw['naigai_viewing_time_end'])
            : '';

        $repeat_mode = (!empty($raw['naigai_viewing_repeat_mode']) && $raw['naigai_viewing_repeat_mode'] === 'weekdays')
            ? 'weekdays'
            : 'none';

        $weekdays = naigai_sanitize_weekdays($raw['naigai_viewing_weekdays'] ?? []);
        $weekdays_csv = implode(',', $weekdays);

        $price = !empty($raw['naigai_property_price'])
            ? sanitize_text_field($raw['naigai_property_price'])
            : '';

        $area = (!empty($raw['naigai_property_area']) && isset($areas[$raw['naigai_property_area']]))
            ? sanitize_key($raw['naigai_property_area'])
            : '';

        $options = !empty($raw['naigai_property_options'])
            ? sanitize_textarea_field($raw['naigai_property_options'])
            : '';

        update_post_meta($post_id, '_naigai_viewing_enabled', $enabled);
        update_post_meta($post_id, '_naigai_viewing_type', $type);
        update_post_meta($post_id, '_naigai_viewing_start_date', $start_date);
        update_post_meta($post_id, '_naigai_viewing_end_date', $end_date);
        update_post_meta($post_id, '_naigai_viewing_cta_label', $cta_label);
        update_post_meta($post_id, '_naigai_viewing_staff', $staff);
        update_post_meta($post_id, '_naigai_viewing_time_start', $time_start);
        update_post_meta($post_id, '_naigai_viewing_time_end', $time_end);
        update_post_meta($post_id, '_naigai_viewing_repeat_mode', $repeat_mode);
        update_post_meta($post_id, '_naigai_viewing_weekdays', $weekdays_csv);
        update_post_meta($post_id, '_naigai_property_price', $price);
        update_post_meta($post_id, '_naigai_property_area', $area);
        update_post_meta($post_id, '_naigai_property_options', $options);
    }
}

/* =========================================================
 * save handler
 * ========================================================= */
add_action('admin_init', function () {
    if (empty($_POST['naigai_viewing_save']) || empty($_POST['naigai_post_id'])) return;

    $post_id = absint($_POST['naigai_post_id']);
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_die('この投稿を編集する権限がありません。');
    }

    check_admin_referer('naigai_viewing_save_' . $post_id);
    naigai_save_viewing_meta($post_id, $_POST);

    wp_safe_redirect(add_query_arg([
        'page'    => 'naigai-viewing-list',
        'updated' => '1',
    ], admin_url('admin.php')));
    exit;
});

/* =========================================================
 * target posts
 * ========================================================= */
if (!function_exists('naigai_admin_get_viewing_posts')) {
    function naigai_admin_get_viewing_posts()
    {
        $ids = [];

        $post_q = new WP_Query([
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 50,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'fields'              => 'ids',
            'tax_query'           => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => ['naigai-tochi', 'naigai-construction'],
                ],
            ],
        ]);

        if (!empty($post_q->posts)) {
            $ids = array_merge($ids, $post_q->posts);
        }

        if (post_type_exists('house')) {
            $house_q = new WP_Query([
                'post_type'      => 'house',
                'post_status'    => 'publish',
                'posts_per_page' => 50,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ]);

            if (!empty($house_q->posts)) {
                $ids = array_merge($ids, $house_q->posts);
            }
        }

        $ids = array_unique(array_map('intval', $ids));

        usort($ids, function ($a, $b) {
            return strtotime(get_post_field('post_date', $b)) <=> strtotime(get_post_field('post_date', $a));
        });

        return $ids;
    }
}

/* =========================================================
 * admin page
 * ========================================================= */
add_action('admin_menu', function () {
    add_menu_page(
        '見学受付一覧',
        '見学受付一覧',
        'edit_posts',
        'naigai-viewing-list',
        'naigai_render_viewing_list_admin_page',
        'dashicons-building',
        25
    );
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_naigai-viewing-list') return;

    wp_register_style('naigai-viewing-admin-inline', false);
    wp_enqueue_style('naigai-viewing-admin-inline');

    $css = <<<CSS
#calendar .fc .fc-button {
  border-radius: 10px;
}
#calendar .fc a.fc-event:hover,
#calendar .fc .fc-daygrid-event:hover {
  filter: none;
  opacity: 0.95;
}
.naigai-admin-wrap { margin-top: 16px; }
.naigai-admin-head { margin-bottom: 20px; }
.naigai-admin-head p { margin: 6px 0 0; color: #50575e; }

.naigai-admin-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 16px; }
.naigai-admin-card { background: #fff; border: 1px solid #dcdcde; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
.naigai-admin-card__thumb { display:block; aspect-ratio: 16/9; background:#f6f7f7; overflow:hidden; }
.naigai-admin-card__thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.naigai-admin-card__body { padding: 14px; }

.naigai-admin-meta { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.naigai-admin-pill { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:12px; line-height:1.2; }
.naigai-pill-type { background:#e7f3ff; color:#0a4b78; }
.naigai-pill-area { background:#fff7e6; color:#7a4b00; }
.naigai-pill-status { background:#edf7ed; color:#1e6934; }

.naigai-admin-title { font-size: 16px; line-height: 1.5; margin: 0 0 8px; }
.naigai-admin-title a { text-decoration:none; }
.naigai-admin-info { margin:0 0 12px; color:#50575e; font-size:13px; }

.naigai-admin-form { margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e5e5; }
.naigai-admin-form-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px 14px; }
.naigai-admin-form p,
.naigai-admin-form div { margin:0 0 10px; }
.naigai-admin-form label { font-weight: 600; }
.naigai-admin-form .description { color:#646970; margin-top:6px; }

.naigai-weekdays { display:flex; flex-wrap:wrap; gap:8px; }
.naigai-weekdays label { font-weight: 400; }

.naigai-admin-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top: 10px; }
.naigai-admin-empty { background:#fff; border:1px solid #dcdcde; border-radius:12px; padding:20px; }

.naigai-admin-filterbar {
  background: #fff;
  border: 1px solid #dcdcde;
  border-radius: 12px;
  padding: 16px;
  margin: 0 0 18px;
  box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.naigai-admin-filterbar__grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0,1fr));
  gap: 12px 14px;
}
.naigai-admin-filterbar__grid p {
  margin: 0;
}
.naigai-admin-filterbar__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 14px;
  flex-wrap: wrap;
}
.naigai-admin-filterbar__count {
  color: #646970;
}
.naigai-admin-pagination {
  display: flex;
  gap: 8px;
  margin-top: 18px;
  flex-wrap: wrap;
}

@media (max-width: 960px) {
  .naigai-admin-filterbar__grid {
    grid-template-columns: repeat(2, minmax(0,1fr));
  }
}
@media (max-width: 782px) {
  .naigai-admin-form-grid,
  .naigai-admin-filterbar__grid {
    grid-template-columns: 1fr;
  }
}

/* ===== FullCalendar 種類別カラー ===== */
#calendar .fc-event.naigai-type-land,
#calendar .fc-h-event.naigai-type-land {
  background: #2e8b57 !important;
  border-color: #2e8b57 !important;
  color: #fff !important;
}

#calendar .fc-event.naigai-type-used_house,
#calendar .fc-h-event.naigai-type-used_house {
  background: #2f6db3 !important;
  border-color: #2f6db3 !important;
  color: #fff !important;
}

#calendar .fc-event.naigai-type-new_house,
#calendar .fc-h-event.naigai-type-new_house {
  background: #2aa7c8 !important;
  border-color: #2aa7c8 !important;
  color: #fff !important;
}

#calendar .fc-event.naigai-type-rental,
#calendar .fc-h-event.naigai-type-rental {
  background: #7a4fc2 !important;
  border-color: #7a4fc2 !important;
  color: #fff !important;
}

#calendar .fc-event.naigai-type-commercial,
#calendar .fc-h-event.naigai-type-commercial {
  background: #d67a1f !important;
  border-color: #d67a1f !important;
  color: #fff !important;
}

#calendar .fc-event.naigai-type-renovation,
#calendar .fc-h-event.naigai-type-renovation {
  background: #b84d65 !important;
  border-color: #b84d65 !important;
  color: #fff !important;
}

CSS;

    wp_add_inline_style('naigai-viewing-admin-inline', $css);
});

if (!function_exists('naigai_render_viewing_list_admin_page')) {
    function naigai_render_viewing_list_admin_page()
    {
        if (!current_user_can('edit_posts')) {
            wp_die('このページを表示する権限がありません。');
        }

        $post_ids     = naigai_admin_get_viewing_posts();
        $type_choices = naigai_get_viewing_type_choices();
        $area_choices = naigai_get_area_choices();

        $search_q       = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $filter_type    = isset($_GET['viewing_type']) ? sanitize_key($_GET['viewing_type']) : '';
        $filter_area    = isset($_GET['property_area']) ? sanitize_key($_GET['property_area']) : '';
        $filter_enabled = isset($_GET['viewing_enabled']) ? sanitize_text_field(wp_unslash($_GET['viewing_enabled'])) : '';
        $paged          = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
        $per_page       = 12;

        $filtered_ids = [];

        foreach ($post_ids as $pid) {
            $title = (string) get_the_title($pid);
            $m     = naigai_get_viewing_meta($pid);

            if ($search_q !== '') {
                $haystack = mb_strtolower($title . ' ' . ($m['price'] ?? '') . ' ' . ($m['staff'] ?? '') . ' ' . ($m['options'] ?? ''));
                if (mb_strpos($haystack, mb_strtolower($search_q)) === false) {
                    continue;
                }
            }

            if ($filter_type !== '' && ($m['type'] ?? '') !== $filter_type) continue;
            if ($filter_area !== '' && ($m['area'] ?? '') !== $filter_area) continue;
            if ($filter_enabled === '1' && ($m['enabled'] ?? '') !== '1') continue;
            if ($filter_enabled === '0' && ($m['enabled'] ?? '') === '1') continue;

            $filtered_ids[] = $pid;
        }

        $total_items = count($filtered_ids);
        $total_pages = max(1, (int) ceil($total_items / $per_page));
        $paged       = min($paged, $total_pages);
        $offset      = ($paged - 1) * $per_page;
        $display_ids = array_slice($filtered_ids, $offset, $per_page);

        $base_url = admin_url('admin.php?page=naigai-viewing-list');
?>
        <div class="wrap naigai-admin-wrap">
            <div class="naigai-admin-head">
                <h1>見学受付一覧</h1>
                <p>担当者が「物件種類・価格・地域・見学期間・案内時間・CTA」を登録し、カレンダーと一覧へ反映します。</p>

                <?php if (!empty($_GET['updated'])) : ?>
                    <div class="notice notice-success is-dismissible">
                        <p>保存しました。</p>
                    </div>
                <?php endif; ?>
            </div>

            <form method="get" class="naigai-admin-filterbar">
                <input type="hidden" name="page" value="naigai-viewing-list">

                <div class="naigai-admin-filterbar__grid">
                    <p>
                        <label for="naigai-filter-s">検索</label><br>
                        <input id="naigai-filter-s" type="text" name="s" class="regular-text" value="<?php echo esc_attr($search_q); ?>" placeholder="物件名・価格・担当者・補足で検索">
                    </p>

                    <p>
                        <label for="naigai-filter-type">物件種類</label><br>
                        <select id="naigai-filter-type" name="viewing_type" class="regular-text">
                            <option value="">すべて</option>
                            <?php foreach ($type_choices as $k => $v) : ?>
                                <option value="<?php echo esc_attr($k); ?>" <?php selected($filter_type, $k); ?>>
                                    <?php echo esc_html($v); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="naigai-filter-area">地域</label><br>
                        <select id="naigai-filter-area" name="property_area" class="regular-text">
                            <option value="">すべて</option>
                            <?php foreach ($area_choices as $k => $v) : ?>
                                <option value="<?php echo esc_attr($k); ?>" <?php selected($filter_area, $k); ?>>
                                    <?php echo esc_html($v); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="naigai-filter-enabled">表示状態</label><br>
                        <select id="naigai-filter-enabled" name="viewing_enabled" class="regular-text">
                            <option value="">すべて</option>
                            <option value="1" <?php selected($filter_enabled, '1'); ?>>カレンダー表示ON</option>
                            <option value="0" <?php selected($filter_enabled, '0'); ?>>カレンダー表示OFF</option>
                        </select>
                    </p>
                </div>

                <div class="naigai-admin-filterbar__actions">
                    <button type="submit" class="button button-primary">絞り込む</button>
                    <a class="button" href="<?php echo esc_url($base_url); ?>">リセット</a>
                    <span class="naigai-admin-filterbar__count">件数：<?php echo (int) $total_items; ?>件</span>
                </div>
            </form>

            <?php if (!empty($display_ids)) : ?>
                <div class="naigai-admin-grid">
                    <?php foreach ($display_ids as $pid) :
                        $title     = get_the_title($pid);
                        $short     = naigai_admin_short_title($title, 34);
                        $edit_link = get_edit_post_link($pid);
                        $view_link = get_permalink($pid);

                        $thumb_url = get_the_post_thumbnail_url($pid, 'medium_large');
                        if (!$thumb_url) {
                            $thumb_url = get_template_directory_uri() . '/images/noimage.gif';
                        }

                        $m = naigai_get_viewing_meta($pid);

                        $type_label   = isset($type_choices[$m['type']]) ? $type_choices[$m['type']] : $m['type'];
                        $area_label   = ($m['area'] && isset($area_choices[$m['area']])) ? $area_choices[$m['area']] : '';
                        $period_label = naigai_format_period_label($m['start_date'], $m['end_date']);
                        $time_label   = naigai_format_time_range($m['time_start'], $m['time_end']);
                        $status_label = ($m['enabled'] === '1') ? 'カレンダー表示ON' : 'カレンダー表示OFF';
                        $price        = $m['price'] ? $m['price'] : '未設定';
                    ?>
                        <div class="naigai-admin-card">
                            <a class="naigai-admin-card__thumb" href="<?php echo esc_url($view_link); ?>" target="_blank" rel="noopener noreferrer">
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>">
                            </a>

                            <div class="naigai-admin-card__body">
                                <div class="naigai-admin-meta">
                                    <span class="naigai-admin-pill naigai-pill-type"><?php echo esc_html($type_label); ?></span>
                                    <?php if ($area_label) : ?>
                                        <span class="naigai-admin-pill naigai-pill-area"><?php echo esc_html($area_label); ?></span>
                                    <?php endif; ?>
                                    <span class="naigai-admin-pill naigai-pill-status"><?php echo esc_html($status_label); ?></span>
                                </div>

                                <h2 class="naigai-admin-title">
                                    <a href="<?php echo esc_url($edit_link); ?>"><?php echo esc_html($short); ?></a>
                                </h2>

                                <p class="naigai-admin-info">
                                    価格：<?php echo esc_html($price); ?>
                                    <?php if ($period_label) : ?> ／ 見学期間：<?php echo esc_html($period_label); ?><?php endif; ?>
                                        <?php if ($time_label) : ?> ／ 案内時間：<?php echo esc_html($time_label); ?><?php endif; ?>
                                            <?php if (!empty($m['staff'])) : ?> ／ 担当者：<?php echo esc_html($m['staff']); ?><?php endif; ?>
                                </p>

                                <form method="post" class="naigai-admin-form">
                                    <?php wp_nonce_field('naigai_viewing_save_' . $pid); ?>
                                    <input type="hidden" name="naigai_viewing_save" value="1">
                                    <input type="hidden" name="naigai_post_id" value="<?php echo esc_attr($pid); ?>">

                                    <p>
                                        <label>
                                            <input type="checkbox" name="naigai_viewing_enabled" value="1" <?php checked($m['enabled'], '1'); ?>>
                                            カレンダーに表示する
                                        </label>
                                    </p>

                                    <div class="naigai-admin-form-grid">
                                        <p>
                                            <label>物件種類</label><br>
                                            <select name="naigai_viewing_type" class="regular-text">
                                                <?php foreach ($type_choices as $k => $v) : ?>
                                                    <option value="<?php echo esc_attr($k); ?>" <?php selected($m['type'], $k); ?>>
                                                        <?php echo esc_html($v); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </p>

                                        <p>
                                            <label>地域</label><br>
                                            <select name="naigai_property_area" class="regular-text">
                                                <option value="">未設定</option>
                                                <?php foreach ($area_choices as $k => $v) : ?>
                                                    <option value="<?php echo esc_attr($k); ?>" <?php selected($m['area'], $k); ?>>
                                                        <?php echo esc_html($v); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </p>

                                        <p>
                                            <label>価格</label><br>
                                            <input type="text" name="naigai_property_price" class="regular-text"
                                                value="<?php echo esc_attr($m['price']); ?>" placeholder="例：300万円 / 応相談">
                                        </p>

                                        <p>
                                            <label>CTA文言</label><br>
                                            <input type="text" name="naigai_viewing_cta_label" class="regular-text"
                                                value="<?php echo esc_attr($m['cta_label'] ?: '見学予約'); ?>" placeholder="見学予約">
                                        </p>

                                        <p>
                                            <label>見学・申込開始日</label><br>
                                            <input type="date" name="naigai_viewing_start_date" value="<?php echo esc_attr($m['start_date']); ?>">
                                        </p>

                                        <p>
                                            <label>見学・申込終了日</label><br>
                                            <input type="date" name="naigai_viewing_end_date" value="<?php echo esc_attr($m['end_date']); ?>">
                                        </p>

                                        <p>
                                            <label>案内時間（開始）</label><br>
                                            <input type="time" name="naigai_viewing_time_start" value="<?php echo esc_attr($m['time_start']); ?>">
                                        </p>

                                        <p>
                                            <label>案内時間（終了）</label><br>
                                            <input type="time" name="naigai_viewing_time_end" value="<?php echo esc_attr($m['time_end']); ?>">
                                        </p>

                                        <p>
                                            <label>担当者名</label><br>
                                            <input type="text" name="naigai_viewing_staff" class="regular-text"
                                                value="<?php echo esc_attr($m['staff']); ?>" placeholder="担当：田中">
                                        </p>

                                        <div>
                                            <label>繰り返し</label><br>
                                            <select name="naigai_viewing_repeat_mode" class="regular-text">
                                                <option value="none" <?php selected($m['repeat_mode'] ?: 'none', 'none'); ?>>なし（期間イベント）</option>
                                                <option value="weekdays" <?php selected($m['repeat_mode'], 'weekdays'); ?>>曜日指定（期間内の該当曜日だけ表示）</option>
                                            </select>

                                            <div class="naigai-weekdays" style="margin-top:6px;">
                                                <?php
                                                $labels = [1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土', 7 => '日'];
                                                foreach ($labels as $num => $lab) :
                                                ?>
                                                    <label>
                                                        <input type="checkbox" name="naigai_viewing_weekdays[]"
                                                            value="<?php echo (int) $num; ?>" <?php checked(in_array($num, $m['weekdays'], true)); ?>>
                                                        <?php echo esc_html($lab); ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>

                                            <p class="description">例：平日だけ表示したいなら「曜日指定」＋ 月〜金をチェック。</p>
                                        </div>

                                        <div style="grid-column: 1 / -1;">
                                            <label>追加情報（任意）</label><br>
                                            <textarea name="naigai_property_options" rows="3" class="large-text"
                                                placeholder="例：土地 見学会 / 予約制 / 駐車場あり"><?php echo esc_textarea($m['options']); ?></textarea>
                                            <p class="description">一覧で「補足情報」などを見せたい場合に使用します。</p>
                                        </div>
                                    </div>

                                    <p class="description">
                                        表示プレビュー：
                                        <?php echo esc_html($title); ?>
                                        <?php if ($period_label) : ?> ／ <?php echo esc_html($period_label); ?><?php endif; ?>
                                            <?php if ($type_label) : ?> ／ 物件種類：<?php echo esc_html($type_label); ?><?php endif; ?>
                                                <?php if ($price) : ?> ／ 価格：<?php echo esc_html($price); ?><?php endif; ?>
                                                    <?php if ($time_label) : ?> ／ 案内時間：<?php echo esc_html($time_label); ?><?php endif; ?>
                                                        <?php if (!empty($m['staff'])) : ?> ／ 担当者：<?php echo esc_html($m['staff']); ?><?php endif; ?>
                                    </p>

                                    <div class="naigai-admin-actions">
                                        <button type="submit" class="button button-primary">保存</button>
                                        <a class="button" href="<?php echo esc_url($edit_link); ?>">物件を編集</a>
                                        <a class="button" href="<?php echo esc_url($view_link); ?>" target="_blank" rel="noopener noreferrer">公開ページを見る</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1) : ?>
                    <div class="naigai-admin-pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++) :
                            $page_url = add_query_arg([
                                'page'            => 'naigai-viewing-list',
                                's'               => $search_q,
                                'viewing_type'    => $filter_type,
                                'property_area'   => $filter_area,
                                'viewing_enabled' => $filter_enabled,
                                'paged'           => $i,
                            ], admin_url('admin.php'));
                        ?>
                            <a class="button <?php echo $i === $paged ? 'button-primary' : ''; ?>" href="<?php echo esc_url($page_url); ?>">
                                <?php echo (int) $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="naigai-admin-empty">条件に一致する対象物件がありません。</div>
            <?php endif; ?>
        </div>
        <?php
    }
}

/* =========================================================
 * AJAX: FullCalendar events
 * ========================================================= */
add_action('wp_ajax_naigai_get_calendar_events', 'naigai_ajax_get_calendar_events');
add_action('wp_ajax_nopriv_naigai_get_calendar_events', 'naigai_ajax_get_calendar_events');
add_action('wp_ajax_get_post_titles', 'naigai_ajax_get_calendar_events');
add_action('wp_ajax_nopriv_get_post_titles', 'naigai_ajax_get_calendar_events');

if (!function_exists('naigai_ajax_get_calendar_events')) {
    function naigai_ajax_get_calendar_events()
    {
        $q = new WP_Query([
            'posts_per_page' => -1,
            'post_type'      => ['post', 'house'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'   => '_naigai_viewing_enabled',
                    'value' => '1',
                ],
            ],
        ]);

        $type_choices = naigai_get_viewing_type_choices();
        $area_choices = naigai_get_area_choices();
        $events = [];

        foreach ($q->posts as $pid) {
            $meta = naigai_get_viewing_meta($pid);
            if (empty($meta['start_date'])) continue;

            $title       = html_entity_decode((string) get_the_title($pid), ENT_QUOTES, 'UTF-8');
            $short_title = naigai_make_calendar_short_title($title);

            $type_label = isset($type_choices[$meta['type']])
                ? $type_choices[$meta['type']]
                : (string) $meta['type'];

            $area_label = (!empty($meta['area']) && isset($area_choices[$meta['area']]))
                ? $area_choices[$meta['area']]
                : '';

            $end_ymd      = !empty($meta['end_date']) ? $meta['end_date'] : $meta['start_date'];
            $period_label = naigai_format_period_label($meta['start_date'], $end_ymd);
            $time_label   = naigai_format_time_range($meta['time_start'], $meta['time_end']);

            $thumb = get_the_post_thumbnail_url($pid, 'medium');
            if (!$thumb) {
                $thumb = get_template_directory_uri() . '/images/noimage.gif';
            }

            $thumb_id  = get_post_thumbnail_id($pid);
            $thumb_alt = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';

            $has_time_range = !empty($meta['time_start']) && !empty($meta['time_end']);

            $start_value = $meta['start_date'];
            $end_value   = naigai_end_exclusive($end_ymd);
            $all_day     = true;

            if ($has_time_range) {
                $start_value = $meta['start_date'] . 'T' . $meta['time_start'] . ':00';
                $end_value   = $end_ymd . 'T' . $meta['time_end'] . ':00';
                $all_day     = false;
            }

            $base = [
                'title'       => $title,
                'short_title' => $short_title,
                'allDay'      => $all_day,

                'id'        => (string) $pid,
                'start'     => $start_value,
                'end'       => $end_value,
                'permalink' => get_permalink($pid),

                'eyecatch'     => $thumb,
                'eyecatch_alt' => $thumb_alt,

                'excerpt' => wp_trim_words(wp_strip_all_tags(get_the_excerpt($pid)), 20, '...'),
                'content' => wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $pid)), 40, '...'),

                'post_id'              => (int) $pid,
                'post_type'            => get_post_type($pid),
                'property_type'        => (string) $meta['type'],
                'property_type_label'  => $type_label,
                'area'                 => (string) ($meta['area'] ?? ''),
                'area_label'           => $area_label,
                'price'                => (string) ($meta['price'] ?? ''),
                'viewing_period_label' => $period_label,
                'time_label'           => $time_label,
                'time_start'           => (string) ($meta['time_start'] ?? ''),
                'time_end'             => (string) ($meta['time_end'] ?? ''),
                'cta_label'            => (string) ($meta['cta_label'] ?: '見学予約'),
                'staff'                => (string) ($meta['staff'] ?? ''),
                'options'              => (string) ($meta['options'] ?? ''),
                'note'                 => (string) ($meta['options'] ?? ''),

                'classNames' => [
                    'naigai-viewing-event',
                    'naigai-type-' . sanitize_html_class((string) $meta['type']),
                    'naigai-area-' . sanitize_html_class((string) ($meta['area'] ?? '')),
                    $has_time_range ? 'naigai-has-time-range' : 'naigai-all-day',
                ],
            ];

            if (($meta['repeat_mode'] ?? 'none') === 'weekdays') {
                $days = !empty($meta['weekdays']) && is_array($meta['weekdays'])
                    ? array_values(array_unique(array_map('intval', $meta['weekdays'])))
                    : [];

                $cursor = strtotime($meta['start_date']);
                $end_ts = strtotime($end_ymd);
                $count  = 0;

                while ($cursor !== false && $cursor <= $end_ts) {
                    $isoN = (int) date('N', $cursor);

                    if (in_array($isoN, $days, true)) {
                        $ymd = date('Y-m-d', $cursor);

                        $evt = $base;
                        $evt['id'] = $pid . ':' . $ymd;

                        if ($has_time_range) {
                            $evt['allDay'] = false;
                            $evt['start']  = $ymd . 'T' . $meta['time_start'] . ':00';
                            $evt['end']    = $ymd . 'T' . $meta['time_end'] . ':00';
                        } else {
                            $evt['allDay'] = true;
                            $evt['start']  = $ymd;
                            $evt['end']    = naigai_end_exclusive($ymd);
                        }

                        $events[] = $evt;

                        $count++;
                        if ($count >= 370) break;
                    }

                    $cursor = strtotime('+1 day', $cursor);
                }
            } else {
                $events[] = $base;
            }
        }

        wp_send_json($events);
    }
}

/* =========================================================
 * Property List build
 * ========================================================= */
if (!function_exists('naigai_build_property_list_rows')) {
    function naigai_build_property_list_rows()
    {
        $type_choices = naigai_get_viewing_type_choices();
        $area_choices = naigai_get_area_choices();

        $q = new WP_Query([
            'posts_per_page' => -1,
            'post_type'      => ['post', 'house'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'   => '_naigai_viewing_enabled',
                    'value' => '1',
                ],
            ],
        ]);

        $rows = [];

        foreach ($q->posts as $pid) {
            $m = naigai_get_viewing_meta($pid);
            if (empty($m['start_date'])) continue;

            $title = html_entity_decode((string) get_the_title($pid), ENT_QUOTES, 'UTF-8');

            $type_label = !empty($m['type']) && isset($type_choices[$m['type']])
                ? $type_choices[$m['type']]
                : (string) ($m['type'] ?? '');

            $area_label = !empty($m['area']) && isset($area_choices[$m['area']])
                ? $area_choices[$m['area']]
                : '';

            $end_date     = !empty($m['end_date']) ? $m['end_date'] : $m['start_date'];
            $period_label = naigai_format_period_label($m['start_date'], $end_date);
            $time_label   = naigai_format_time_range($m['time_start'], $m['time_end']);
            $cta_label    = !empty($m['cta_label']) ? (string) $m['cta_label'] : '見学予約';

            $rows[] = [
                'post_id'      => (int) $pid,
                'title'        => $title,
                'type_label'   => $type_label,
                'area_label'   => $area_label,
                'price'        => (string) ($m['price'] ?? ''),
                'period_label' => $period_label,
                'time_label'   => $time_label,
                'staff'        => (string) ($m['staff'] ?? ''),
                'options'      => (string) ($m['options'] ?? ''),
                'cta_label'    => $cta_label,
                'permalink'    => get_permalink($pid),
                'start_date'   => (string) $m['start_date'],
                'end_date'     => (string) $end_date,
            ];
        }

        usort($rows, function ($a, $b) {
            $at = !empty($a['start_date']) ? strtotime($a['start_date']) : PHP_INT_MAX;
            $bt = !empty($b['start_date']) ? strtotime($b['start_date']) : PHP_INT_MAX;
            return $at <=> $bt;
        });

        return $rows;
    }
}

if (!function_exists('naigai_render_property_list_html')) {
    function naigai_render_property_list_html($rows)
    {
        ob_start();

        if (empty($rows)) : ?>
            <div class="naigai-property-list-empty">
                現在、見学予約できる物件はありません。
            </div>
            <?php return ob_get_clean(); ?>
        <?php endif; ?>

        <div class="naigai-property-list-wrap">
            <?php foreach ($rows as $row) : ?>
                <article class="naigai-property-row" data-post-id="<?php echo (int) $row['post_id']; ?>">
                    <div class="naigai-property-row__main">
                        <div class="naigai-property-row__headline">
                            <?php if (!empty($row['type_label'])) : ?>
                                <span class="naigai-property-row__theme">物件種類：<?php echo esc_html($row['type_label']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($row['period_label'])) : ?>
                                <span class="naigai-property-row__period-badge">見学会期間：<?php echo esc_html($row['period_label']); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="naigai-property-row__title">
                            <a href="<?php echo esc_url($row['permalink']); ?>">
                                タイトル名：<?php echo esc_html($row['title']); ?>
                            </a>
                        </h3>

                        <div class="naigai-property-row__meta">
                            <?php if (!empty($row['time_label'])) : ?>
                                <span class="naigai-property-row__time">案内時間：<?php echo esc_html($row['time_label']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($row['staff'])) : ?>
                                <span class="naigai-property-row__staff">担当者：<?php echo esc_html($row['staff']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($row['area_label'])) : ?>
                                <span class="naigai-property-row__area">地域：<?php echo esc_html($row['area_label']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($row['price'])) : ?>
                                <span class="naigai-property-row__price">価格：<?php echo esc_html($row['price']); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($row['options'])) : ?>
                            <p class="naigai-property-row__options"><?php echo nl2br(esc_html($row['options'])); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="naigai-property-row__actions">
                        <button
                            type="button"
                            class="naigai-property-row__cta"
                            data-post-id="<?php echo (int) $row['post_id']; ?>"
                            data-permalink="<?php echo esc_url($row['permalink']); ?>">
                            <?php echo esc_html($row['cta_label']); ?>
                        </button>

                        <a class="naigai-property-row__link" href="<?php echo esc_url($row['permalink']); ?>">
                            詳細を見る
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
<?php
        return ob_get_clean();
    }
}

/* =========================================================
 * AJAX: Property List
 * ========================================================= */
add_action('wp_ajax_naigai_get_property_list', 'naigai_ajax_get_property_list');
add_action('wp_ajax_nopriv_naigai_get_property_list', 'naigai_ajax_get_property_list');

if (!function_exists('naigai_ajax_get_property_list')) {
    function naigai_ajax_get_property_list()
    {
        $rows = naigai_build_property_list_rows();
        $html = naigai_render_property_list_html($rows);

        wp_send_json_success([
            'html'  => $html,
            'count' => count($rows),
        ]);
    }
}

/* =========================================================
 * AJAX: Reservation Modal
 * ========================================================= */
if (!function_exists('naigai_get_reservation_modal_payload')) {
    function naigai_get_reservation_modal_payload($post_id)
    {
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            return null;
        }

        $meta = naigai_get_viewing_meta($post_id);

        $title     = html_entity_decode((string) get_the_title($post_id), ENT_QUOTES, 'UTF-8');
        $permalink = get_permalink($post_id);
        $post_type = get_post_type($post_id);

        $thumb = get_the_post_thumbnail_url($post_id, 'medium');
        if (!$thumb) {
            $thumb = get_template_directory_uri() . '/images/noimage.gif';
        }

        $end_date     = !empty($meta['end_date']) ? $meta['end_date'] : $meta['start_date'];
        $period_label = naigai_format_period_label($meta['start_date'], $end_date);
        $time_label   = naigai_format_time_range($meta['time_start'], $meta['time_end']);

        $price = (string) ($meta['price'] ?? '');
        if ($price === '') {
            $price = '売却済';
        }

        return [
            'post_id'      => (int) $post_id,
            'post_type'    => $post_type,
            'title'        => $title,
            'permalink'    => $permalink,
            'thumbnail'    => $thumb,
            'price'        => $price,
            'staff'        => (string) ($meta['staff'] ?? ''),
            'period_label' => $period_label,
            'time_label'   => $time_label,
            'options'      => (string) ($meta['options'] ?? ''),
            'area'         => (string) ($meta['area'] ?? ''),
            'type'         => (string) ($meta['type'] ?? ''),
        ];
    }
}

add_action('wp_ajax_naigai_get_reservation_modal_data', 'naigai_ajax_get_reservation_modal_data');
add_action('wp_ajax_nopriv_naigai_get_reservation_modal_data', 'naigai_ajax_get_reservation_modal_data');

if (!function_exists('naigai_ajax_get_reservation_modal_data')) {
    function naigai_ajax_get_reservation_modal_data()
    {
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(['message' => 'post_id が不正です']);
        }

        $payload = naigai_get_reservation_modal_payload($post_id);
        if (!$payload) {
            wp_send_json_error(['message' => 'データが見つかりません']);
        }

        wp_send_json_success($payload);
    }
}
