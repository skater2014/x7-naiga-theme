<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$portal = function_exists('naigai_get_minpaku_archive_content') ? naigai_get_minpaku_archive_content() : array();
$post_count_obj  = wp_count_posts('minpaku');
$published_count = isset($post_count_obj->publish) ? (int) $post_count_obj->publish : 0;

if (!function_exists('naigai_minpaku_archive_page_url')) {
    function naigai_minpaku_archive_page_url($portal, $page_id_key, $fallback = '')
    {
        $page_id = isset($portal[$page_id_key]) ? absint($portal[$page_id_key]) : 0;
        if ($page_id > 0) {
            $url = get_permalink($page_id);
            if ($url) {
                return $url;
            }
        }
        return $fallback;
    }
}


if (!function_exists('naigai_mnpk_archive_icon_svg')) {
    /**
     * 民泊アーカイブ特集カード用の線アイコン。
     *
     * 役割:
     * - 管理画面の featured_x_icon で指定したキーをSVGに変換する。
     * - SVGはテーマ内固定のホワイトリストだけを返す。
     * - 色はCSSの currentColor で制御する。
     *
     * 対応キー:
     * - bbq
     * - group
     * - work
     * - stay
     * - nature
     * - activity
     * - food
     * - facility
     */
    function naigai_mnpk_archive_icon_svg($icon)
    {
        $icon = sanitize_key((string) $icon);

        $svg = array(
            'bbq' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 9h14"/><path d="M7 9a5 5 0 0 0 10 0"/><path d="M8 15l-2 5"/><path d="M16 15l2 5"/><path d="M12 15v5"/><path d="M9 5c0-1 1-1.5 1-2.5"/><path d="M14 5c0-1 1-1.5 1-2.5"/></svg>',
            'group' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M16 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M3.5 20a5 5 0 0 1 9 0"/><path d="M11.5 20a5 5 0 0 1 9 0"/></svg>',
            'work' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 6h16v10H4z"/><path d="M2 19h20"/><path d="M8 10h8"/><path d="M9 3h6"/><path d="M10 3v3"/><path d="M14 3v3"/></svg>',
            'stay' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="6" y="7" width="12" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M9 11h.01"/><path d="M15 11h.01"/><path d="M9 20v1"/><path d="M15 20v1"/></svg>',
            'nature' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 19h18"/><path d="M5 17l5-8 4 6 2-3 4 5"/><path d="M7 19v-4"/><path d="M7 15l-2-2"/><path d="M7 15l2-2"/></svg>',
            'activity' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="8"/><path d="M4.5 9.5c4 1 7 3.5 9.5 9"/><path d="M19.5 9.5c-4 1-7 3.5-9.5 9"/><path d="M8 4.8c2 3 6 3 8 0"/></svg>',
            'food' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 3v8"/><path d="M4 3v4"/><path d="M8 3v4"/><path d="M6 11v10"/><path d="M17 3v18"/><path d="M14 3h5v8a3 3 0 0 1-3 3h-2"/></svg>',
            'facility' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/><path d="M18 3l.7 1.6L20.5 5l-1.8.4L18 7l-.7-1.6L15.5 5l1.8-.4Z"/></svg>',
        );

        return isset($svg[$icon]) ? $svg[$icon] : $svg['facility'];
    }
}

$featured_pages = array();
for ($i = 1; $i <= 8; $i++) {
    $featured_pages[] = array(
        'badge' => isset($portal['featured_' . $i . '_badge']) ? $portal['featured_' . $i . '_badge'] : '',
        'icon'  => isset($portal['featured_' . $i . '_icon']) ? sanitize_key($portal['featured_' . $i . '_icon']) : '',
        'title' => isset($portal['featured_' . $i . '_title']) ? $portal['featured_' . $i . '_title'] : '',
        'text'  => isset($portal['featured_' . $i . '_text']) ? $portal['featured_' . $i . '_text'] : '',
        'url'   => naigai_minpaku_archive_page_url($portal, 'featured_' . $i . '_page_id', ''),
    );
}

$difference_button_url = naigai_minpaku_archive_page_url($portal, 'difference_button_page_id', home_url('/minpaku-difference/'));
$support_button_url    = naigai_minpaku_archive_page_url($portal, 'support_button_page_id', home_url('/minpaku/'));

$archive_copy = array(
    'difference_lead' => isset($portal['difference_lead']) && trim((string) $portal['difference_lead']) !== ''
        ? (string) $portal['difference_lead']
        : '民泊・一棟貸し・貸別荘の違いを先に知っておくと、一覧ページでも比較しやすくなります。',

    'support_title' => isset($portal['support_title']) && trim((string) $portal['support_title']) !== ''
        ? (string) $portal['support_title']
        : '民泊運営サポート',

    'support_lead' => isset($portal['support_lead']) && trim((string) $portal['support_lead']) !== ''
        ? (string) $portal['support_lead']
        : '所有物件を宿泊事業に活用したい方向けに、運営準備・掲載・予約導線・清掃管理の入口をまとめています。',

    'support_button_text' => isset($portal['support_button_text']) && trim((string) $portal['support_button_text']) !== ''
        ? (string) $portal['support_button_text']
        : '民泊運営サポートを見る',

    'stay_list_title' => isset($portal['stay_list_title']) && trim((string) $portal['stay_list_title']) !== ''
        ? (string) $portal['stay_list_title']
        : '現在ご案内中の宿泊先',

    'stay_list_lead' => isset($portal['stay_list_lead']) && trim((string) $portal['stay_list_lead']) !== ''
        ? (string) $portal['stay_list_lead']
        : '設備・人数・雰囲気を見ながら、目的に合う宿泊先を比較できます。',
);

$archive_nav_items = array(
    array(
        'href'  => '#featured-pages',
        'label' => !empty($portal['intro_nav_label']) ? (string) $portal['intro_nav_label'] : '過ごし方',
    ),
    array(
        'href'  => '#difference',
        'label' => !empty($portal['difference_nav_label']) ? (string) $portal['difference_nav_label'] : '違い',
    ),
    array(
        'href'  => '#stay-list',
        'label' => !empty($portal['stay_list_nav_label']) ? (string) $portal['stay_list_nav_label'] : '宿泊施設',
    ),
    array(
        'href'  => '#support',
        'label' => !empty($portal['support_nav_label']) ? (string) $portal['support_nav_label'] : '運営サポート',
    ),
);

$archive_section_notes = array(
    'intro' => !empty($portal['intro_note_text'])
        ? (string) $portal['intro_note_text']
        : '',

    'difference' => !empty($portal['difference_note_text'])
        ? (string) $portal['difference_note_text']
        : (!empty($portal['difference_text']) ? (string) $portal['difference_text'] : ''),

    'stay_list' => !empty($portal['stay_list_note_text'])
        ? (string) $portal['stay_list_note_text']
        : '',

    'support' => !empty($portal['support_note_text'])
        ? (string) $portal['support_note_text']
        : '',
);


$mnpk_archive_page_url = static function ($slug) {
    $page = get_page_by_path($slug);
    return ($page && $page->post_status === 'publish') ? get_permalink($page->ID) : '';
};

$archive_section_actions = array(
    'difference' => array(
        'url'   => $mnpk_archive_page_url('minpaku-difference'),
        'label' => '詳しく見る',
    ),
    'support' => array(
        'url'   => $mnpk_archive_page_url('minpaku'),
        'label' => '詳しく見る',
    ),
    'stay_list' => array(
        'url'   => $mnpk_archive_page_url('minpaku-guide'),
        'label' => '詳しく見る',
    ),
);

?>
<main id="primary" class="mnpk-archive-page">
    <div class="mnpk-archive-shell">

        <?php
        /*
         * ========================================================
         * 民泊宿泊施設一覧「前のページに戻る」
         * ========================================================
         *
         * この一覧には従来back linkが無かったため、
         * 民泊共通部品を読み込む。
         *
         * .mnpk-back-wrap / .mnpk-back-link の
         * 既存デザインを使用し、CSSは追加しない。
         */
        get_template_part(
            'template-parts/common/minpaku-internal-back-link'
        );
        ?>

        <section class="mnpk-archive-hero" <?php if (!empty($portal['hero_image_url'])) : ?>style="background-image:url('<?php echo esc_url($portal['hero_image_url']); ?>');" <?php endif; ?>>
            <div class="mnpk-archive-hero__overlay"></div>
            <div class="mnpk-archive-hero__inner">
                <p class="mnpk-archive-kicker">Minpaku Stay in Nasu</p>
                <h1 class="mnpk-archive-title"><?php echo esc_html($portal['hero_title']); ?></h1>
                <p class="mnpk-archive-lead"><?php echo nl2br(esc_html($portal['hero_lead'])); ?></p>
                <div class="mnpk-archive-meta">
                    <span class="mnpk-archive-meta__pill">公開中 <?php echo esc_html($published_count); ?>件</span>
                    <span class="mnpk-archive-meta__pill">那須の一棟貸し・貸別荘</span>
                    <span class="mnpk-archive-meta__pill">家族・グループ・ワーケーション</span>
                </div>
                <?php if (!empty($portal['hero_note'])) : ?>
                    <p class="mnpk-archive-note"><?php echo nl2br(esc_html($portal['hero_note'])); ?></p>
                <?php endif; ?>

            </div>
        </section>

        <div class="mnpk-archive-hero-actions-outside">
            <?php if (!empty($portal['hero_primary_text']) && !empty($portal['hero_primary_anchor'])) : ?>
                <a class="mnpk-archive-button" href="<?php echo esc_attr($portal['hero_primary_anchor']); ?>"><?php echo esc_html($portal['hero_primary_text']); ?></a>
            <?php endif; ?>
            <?php if (!empty($portal['hero_secondary_text']) && !empty($portal['hero_secondary_url'])) : ?>
                <a class="mnpk-archive-button mnpk-archive-button--ghost" href="<?php echo esc_url($portal['hero_secondary_url']); ?>"><?php echo esc_html($portal['hero_secondary_text']); ?></a>
            <?php endif; ?>
        </div>

        <nav class="mnpk-archive-localnav" aria-label="民泊ポータル内ナビ">
            <?php foreach ($archive_nav_items as $nav_item) : ?>
                <?php if (empty($nav_item['label']) || empty($nav_item['href'])) continue; ?>
                <a href="<?php echo esc_attr($nav_item['href']); ?>">
                    <?php echo esc_html($nav_item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <section id="featured-pages" class="mnpk-archive-section">
            <div class="mnpk-archive-section__head">
                <h2><?php echo esc_html($portal['intro_title']); ?></h2>
                <p><?php echo nl2br(esc_html($portal['intro_text'])); ?></p>
            </div>

            <?php if (!empty($archive_section_notes['intro'])) : ?>
                <div class="mnpk-archive-section__note">
                    <?php echo wp_kses_post(wpautop($archive_section_notes['intro'])); ?>
                </div>
            <?php endif; ?>

            <div class="mnpk-featured-grid">
                <?php foreach ($featured_pages as $page) : ?>
                    <?php if (empty($page['url'])) continue; ?>
                    <?php if ($page['title'] === '' && $page['text'] === '') continue; ?>
                    <article class="mnpk-featured-card">
                        <div class="mnpk-featured-card__top">
                            <span class="mnpk-featured-card__icon" aria-hidden="true">
                                <?php echo naigai_mnpk_archive_icon_svg(!empty($page['icon']) ? $page['icon'] : 'facility'); ?>
                            </span>
                            <?php if ($page['badge']) : ?>
                                <span class="mnpk-featured-card__badge"><?php echo esc_html($page['badge']); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo esc_html($page['title']); ?></h3>
                        <p><?php echo esc_html($page['text']); ?></p>
                        <?php if ($page['url']) : ?>
                            <a href="<?php echo esc_url($page['url']); ?>" class="mnpk-campaign-card__link">詳しく見る</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <section id="difference" class="mnpk-archive-section">
            <div class="mnpk-archive-section__head">
                <h2><?php echo esc_html($portal['difference_title']); ?></h2>
                <p><?php echo esc_html($archive_copy['difference_lead']); ?></p>
            </div>

            <?php if (!empty($archive_section_notes['difference'])) : ?>
                <div class="mnpk-archive-section__note">
                    <div class="mnpk-archive-section__note-body">
                        <?php echo wp_kses_post(wpautop($archive_section_notes['difference'])); ?>
                    </div>
                    <?php if (!empty($archive_section_actions['difference']['url'])) : ?>
                        <p class="mnpk-archive-section__note-action">
                            <a class="mnpk-archive-button mnpk-archive-button--ghost" href="<?php echo esc_url($archive_section_actions['difference']['url']); ?>">
                                <?php echo esc_html($archive_section_actions['difference']['label']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <section id="support" class="mnpk-archive-section mnpk-archive-section--support">
            <div class="mnpk-archive-section__head">
                <h2><?php echo esc_html($archive_copy['support_title']); ?></h2>
                <p><?php echo esc_html($archive_copy['support_lead']); ?></p>
            </div>

            <?php if (!empty($archive_section_notes['support'])) : ?>
                <div class="mnpk-archive-section__note">
                    <div class="mnpk-archive-section__note-body">
                        <?php echo wp_kses_post(wpautop($archive_section_notes['support'])); ?>
                    </div>
                    <?php if (!empty($archive_section_actions['support']['url'])) : ?>
                        <p class="mnpk-archive-section__note-action">
                            <a class="mnpk-archive-button mnpk-archive-button--ghost" href="<?php echo esc_url($archive_section_actions['support']['url']); ?>">
                                <?php echo esc_html($archive_section_actions['support']['label']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>


        <section id="stay-list" class="mnpk-archive-section">
            <div class="mnpk-archive-section__head">
                <h2><?php echo esc_html($archive_copy['stay_list_title']); ?> <?php echo esc_html($published_count); ?>件</h2>
                <p><?php echo esc_html($archive_copy['stay_list_lead']); ?></p>
            </div>

            <?php if (!empty($archive_section_notes['stay_list'])) : ?>
                <div class="mnpk-archive-section__note">
                    <div class="mnpk-archive-section__note-body">
                        <?php echo wp_kses_post(wpautop($archive_section_notes['stay_list'])); ?>
                    </div>
                    <?php if (!empty($archive_section_actions['stay_list']['url'])) : ?>
                        <p class="mnpk-archive-section__note-action">
                            <a class="mnpk-archive-button mnpk-archive-button--ghost" href="<?php echo esc_url($archive_section_actions['stay_list']['url']); ?>">
                                <?php echo esc_html($archive_section_actions['stay_list']['label']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (have_posts()) : ?>
                <div class="mnpk-stay-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $post_id       = get_the_ID();
                        $nightly_price   = (float) get_post_meta($post_id, '_mnpk_nightly_price', true);
                        $weekend_price   = (float) get_post_meta($post_id, '_mnpk_weekend_price', true);
                        $cleaning_fee    = (float) get_post_meta($post_id, '_mnpk_cleaning_fee', true);
                        $capacity        = max(1, (int) get_post_meta($post_id, '_mnpk_capacity', true));
                        $bedrooms        = (int) get_post_meta($post_id, '_mnpk_bedrooms', true);
                        $beds            = (int) get_post_meta($post_id, '_mnpk_beds', true);
                        $min_nights      = max(1, (int) get_post_meta($post_id, '_mnpk_min_nights', true));
                        $base_guests     = max(1, (int) get_post_meta($post_id, '_mnpk_base_guests', true));
                        $extra_guest_fee = (float) get_post_meta($post_id, '_mnpk_extra_guest_fee', true);
                        $checkin_time    = trim((string) get_post_meta($post_id, '_mnpk_checkin_time', true));
                        $checkout_time   = trim((string) get_post_meta($post_id, '_mnpk_checkout_time', true));
                        $detail_url      = get_permalink($post_id);
                        $checkout_url    = trailingslashit($detail_url) . 'checkout/';
                        $thumb_url       = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'large') : get_template_directory_uri() . '/images/noimage.gif';
                        $lead            = has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags(get_the_content()), 36, '…');

                        if ($weekend_price <= 0) {
                            $weekend_price = $nightly_price;
                        }

                        if ($base_guests > $capacity) {
                            $base_guests = $capacity;
                        }

                        if ($checkin_time === '') {
                            $checkin_time = '15:00';
                        }

                        if ($checkout_time === '') {
                            $checkout_time = '10:00';
                        }

                        $calendar_payload = function_exists('mnpk_get_calendar_payload')
                            ? mnpk_get_calendar_payload($post_id)
                            : array(
                                'open_start_date'      => '',
                                'cleaning_buffer_days' => 0,
                                'cleaning_note'        => '',
                                'events'               => array(),
                            );
                        ?>
                        <article class="mnpk-stay-card">
                            <a href="<?php echo esc_url($detail_url); ?>" class="mnpk-stay-card__image-link">
                                <div class="mnpk-stay-card__image" style="background-image:url('<?php echo esc_url($thumb_url); ?>');"></div>
                            </a>
                            <div class="mnpk-stay-card__body">
                                <h3 class="mnpk-stay-card__title"><a href="<?php echo esc_url($detail_url); ?>"><?php the_title(); ?></a></h3>
                                <div class="mnpk-stay-card__meta">
                                    <?php if ($capacity > 0) : ?><span>最大 <?php echo esc_html($capacity); ?>名</span><?php endif; ?>
                                    <?php if ($bedrooms > 0) : ?><span>寝室 <?php echo esc_html($bedrooms); ?>室</span><?php endif; ?>
                                    <?php if ($beds > 0) : ?><span>ベッド <?php echo esc_html($beds); ?>台</span><?php endif; ?>
                                    <span>最低 <?php echo esc_html($min_nights); ?>泊</span>
                                </div>
                                <?php if ($lead !== '') : ?><p class="mnpk-stay-card__excerpt"><?php echo esc_html($lead); ?></p><?php endif; ?>
                                <div class="mnpk-stay-card__footer">
                                    <div class="mnpk-stay-card__price">
                                        <?php if ($nightly_price > 0) : ?>
                                            <strong>¥<?php echo number_format($nightly_price); ?></strong><span>/ 泊〜</span>
                                        <?php else : ?>
                                            <strong>料金要確認</strong>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mnpk-stay-card__actions">
                                        <a href="<?php echo esc_url($detail_url); ?>" class="mnpk-stay-card__button">宿泊詳細を見る</a>
                                        <button
                                            type="button"
                                            class="mnpk-stay-card__button mnpk-stay-card__button--reserve"
                                            data-archive-booking-open
                                            data-post-id="<?php echo esc_attr($post_id); ?>"
                                            data-stay-title="<?php echo esc_attr(get_the_title($post_id)); ?>"
                                            data-detail-url="<?php echo esc_url($detail_url); ?>"
                                            data-checkout-url="<?php echo esc_url($checkout_url); ?>"
                                            data-nightly-price="<?php echo esc_attr($nightly_price); ?>"
                                            data-weekend-price="<?php echo esc_attr($weekend_price); ?>"
                                            data-cleaning-fee="<?php echo esc_attr($cleaning_fee); ?>"
                                            data-capacity="<?php echo esc_attr($capacity); ?>"
                                            data-base-guests="<?php echo esc_attr($base_guests); ?>"
                                            data-extra-guest-fee="<?php echo esc_attr($extra_guest_fee); ?>"
                                            data-min-nights="<?php echo esc_attr($min_nights); ?>"
                                            data-checkin-time="<?php echo esc_attr($checkin_time); ?>"
                                            data-checkout-time="<?php echo esc_attr($checkout_time); ?>"
                                            data-open-start-date="<?php echo esc_attr($calendar_payload['open_start_date']); ?>"
                                            data-cleaning-buffer-days="<?php echo esc_attr($calendar_payload['cleaning_buffer_days']); ?>"
                                            data-cleaning-note="<?php echo esc_attr($calendar_payload['cleaning_note']); ?>"
                                            data-calendar-events="<?php echo esc_attr(wp_json_encode($calendar_payload['events'], JSON_UNESCAPED_UNICODE)); ?>"
                                        >日付を選ぶ</button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <div class="mnpk-archive-pagination">
                    <?php echo paginate_links(array('prev_text' => '←', 'next_text' => '→', 'type' => 'list')); ?>
                </div>
            <?php else : ?>
                <div class="mnpk-archive-empty">
                    <p>現在公開中の宿泊先はありません。</p>
                </div>
            <?php endif; ?>
        </section>

        <?php
        $mnpk_footer_nav = get_template_directory() . '/minpaku/common/templates/minpaku-footer-nav.php';
        if (file_exists($mnpk_footer_nav)) {
            include $mnpk_footer_nav;
        }
        ?>
    </div>
</main>
<?php
/**
 * =========================================================
 * Archive booking modal
 * /minpaku-stay の「日付を選ぶ」共通モーダル
 * =========================================================
 */
?>
<div class="mnpk-modal mnpk-archive-booking-modal" id="mnpk-archive-booking-modal" aria-hidden="true">
    <div class="mnpk-modal__backdrop" data-close-modal data-archive-booking-close></div>

    <div class="mnpk-modal__dialog mnpk-modal__dialog--calendar" role="dialog" aria-modal="true" aria-labelledby="mnpk-archive-date-modal-title">
        <button type="button" class="mnpk-modal__close" data-close-modal data-archive-booking-close aria-label="閉じる">×</button>

        <div class="mnpk-modal__header">
            <div class="mnpk-modal__header-main">
                <p class="mnpk-modal__eyebrow">DATE &amp; GUESTS</p>
                <h2 id="mnpk-archive-date-modal-title">日付を選択</h2>
                <p class="mnpk-modal__lead" data-archive-stay-title></p>
            </div>
        </div>

        <div class="mnpk-modal__body">
            <div class="mnpk-date-summary mnpk-calendar-selected" aria-label="選択中の日付">
                <button type="button" class="mnpk-date-summary__box mnpk-date-summary__button is-active" data-date-field="checkin" aria-pressed="true">
                    <span>チェックイン</span>
                    <strong data-calendar-checkin-label data-archive-checkin-label>未選択</strong>
                </button>

                <button type="button" class="mnpk-date-summary__box mnpk-date-summary__button" data-date-field="checkout" aria-pressed="false">
                    <span>チェックアウト</span>
                    <strong data-calendar-checkout-label data-archive-checkout-label>未選択</strong>
                </button>
            </div>

            <div class="mnpk-calendar-toolbar">
                <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-prev data-archive-calendar-prev aria-label="前の月へ">‹</button>
                <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-next data-archive-calendar-next aria-label="次の月へ">›</button>
            </div>

            <div class="mnpk-calendar-legend">
                <span><i class="mnpk-calendar-mark mnpk-calendar-mark--available"></i> 空き</span>
                <span><i class="mnpk-calendar-mark mnpk-calendar-mark--reserved">×</i> 予約済み</span>
                <span><i class="mnpk-calendar-mark mnpk-calendar-mark--cleaning">清</i> 清掃</span>
                <span><i class="mnpk-calendar-mark mnpk-calendar-mark--blocked">—</i> 停止 / 営業前</span>
            </div>

            <div class="mnpk-calendar-grid mnpk-calendar-months">
                <div class="mnpk-calendar-month" data-calendar-month="0" data-archive-calendar-month="0"></div>
                <div class="mnpk-calendar-month" data-calendar-month="1" data-archive-calendar-month="1"></div>
            </div>

            <input type="hidden" id="mnpk-archive-checkin-input" name="checkin" value="">
            <input type="hidden" id="mnpk-archive-checkout-input" name="checkout" value="">

            <p class="mnpk-form-help" data-calendar-help data-archive-calendar-help>営業日として開いている日だけ選択できます。</p>
            <p class="mnpk-booking-error" data-calendar-error data-archive-calendar-error hidden></p>
        </div>

        <div class="mnpk-modal__actions">
            <button type="button" class="mnpk-button mnpk-button--ghost" data-calendar-clear data-archive-calendar-clear>選択をクリア</button>
            <button type="button" class="mnpk-button mnpk-button--ghost" data-close-modal data-archive-booking-close>閉じる</button>
            <button type="button" class="mnpk-button" data-archive-booking-submit>この日程で確認とお支払いへ</button>
        </div>
    </div>
</div>

<?php get_footer();
