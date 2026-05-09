# iezukuri HTML items audit

- generated: 2026-05-02T00:29:22
- files scanned: 13

## hub/inc/customhome-company-info.php

- page/key: `ch-company-map-section`

### functions
- `naigai_ch_company_is_target()`
- `naigai_ch_company_find_meta()`
- `naigai_ch_company_source_page_id()`
- `naigai_ch_company_get_access_data()`
- `naigai_ch_company_allowed_iframe()`
- `naigai_ch_company_info_box()`
- `naigai_ch_company_info_add_box()`
- `naigai_ch_company_info_save()`
- `naigai_ch_render_company_access_section()`

### meta keys already used
- `_ch_company_address`
- `_ch_company_info_source`
- `_ch_company_map_iframe`
- `_ch_company_map_text`
- `_ch_company_map_title`
- `_ch_company_source_page_id`
- `_ch_subpage_template`

### HTML blocks / layout classes
- line 124: `<div class="naigai-admin-guide">`
- line 129: `<div class="naigai-admin-section">`
- line 159: `<div class="naigai-admin-section">`
- line 173: `<div class="naigai-admin-section">`
- line 251: `<section class="ch-section-block ch-company-map-section">`
- line 252: `<div class="ch-section-head">`
- line 266: `<div class="ch-company-map-embed">`

### visible text items
- line 125: `<strong>` → 会社情報の取得方法
- line 126: `<p>` → 住所・Google Mapを /company から参照するか、このページ専用で入力するか選びます。
- line 130: `<h3>` → 会社情報の取得方法
- line 132: `<p>` → /company の会社情報を使う
- line 139: `<p>` → このページ専用に入力する
- line 146: `<p>` → 参照元ページ 自動: /company
- line 160: `<h3>` → 表示文言
- line 162: `<p>` → 見出し
- line 167: `<p>` → 説明文
- line 174: `<h3>` → このページ専用の会社情報
- line 175: `<p class="description">` → 「このページ専用に入力する」を選んだ場合だけ使われます。
- line 177: `<p>` → 住所
- line 182: `<p>` → Google Map iframe
- line 253: `<span class="ch-eyebrow">` → ACCESS

## hub/inc/customhome-contact-sections.php

- page/key: `ch-contact-flow-section`

### functions
- `naigai_ch_meta()`
- `naigai_ch_contact_source_page_id()`
- `naigai_ch_render_contact_flow_section()`
- `naigai_ch_contact_form_source_html()`
- `naigai_ch_render_contact_form_section()`
- `naigai_ch_contact_flow_shortcode()`
- `naigai_ch_contact_form_shortcode()`

### meta keys already used
- `_ch_contact_form_shortcode`
- `_ch_contact_form_source_page_id`

### HTML blocks / layout classes
- line 83: `<section class="ch-section ch-section--white ch-contact-flow-section">`
- line 84: `<div class="ch-shell">`
- line 85: `<div class="ch-section-head ch-section-head--center">`
- line 93: `<div class="ch-contact-flow">`
- line 95: `<article class="ch-contact-flow__item">`
- line 140: `<div class="ch-contact-form-fallback">`
- line 159: `<section class="ch-section ch-section--white ch-contact-form-section">`
- line 160: `<div class="ch-shell">`
- line 161: `<div class="ch-section-head ch-section-head--center">`
- line 169: `<div class="ch-contact-form-shell">`

### visible text items
- line 86: `<span class="ch-eyebrow">` → FLOW
- line 141: `<p>` → 既存のお問い合わせフォームは下記ページからご利用いただけます。
- line 142: `<a class="ch-btn ch-btn--primary">` → お問い合わせフォームを開く
- line 162: `<span class="ch-eyebrow">` → CONTACT FORM

## hub/inc/customhome-phase2.php

- page/key: `customhome-phase2`

### functions
- `naigai_customhome_phase2_is_target()`
- `naigai_customhome_phase2_enqueue_front()`
- `naigai_customhome_phase2_enqueue_admin()`
- `naigai_customhome_phase2_hide_legacy_admin()`
- `naigai_customhome_phase2_add_meta_box()`
- `naigai_customhome_phase2_get_attachment_preview()`
- `naigai_customhome_phase2_render_meta_box()`
- `naigai_customhome_phase2_save()`

### meta keys already used
- `_hub_ch_cta_media_items`
- `_hub_ch_hero_company`
- `_hub_ch_hero_company_position`
- `_hub_ch_work_items`
- `naigai_ch_cta_media_items[<?php echo (int) $index; ?>][attachment_id]`
- `naigai_ch_cta_media_items[<?php echo (int) $index; ?>][type]`
- `naigai_ch_cta_media_items[__INDEX__][attachment_id]`
- `naigai_ch_cta_media_items[__INDEX__][type]`
- `naigai_ch_work_items[<?php echo (int) $index; ?>][attachment_id]`
- `naigai_ch_work_items[<?php echo (int) $index; ?>][text]`
- `naigai_ch_work_items[<?php echo (int) $index; ?>][title]`
- `naigai_ch_work_items[__INDEX__][attachment_id]`
- `naigai_ch_work_items[__INDEX__][text]`
- `naigai_ch_work_items[__INDEX__][title]`

### HTML blocks / layout classes
- line 256: `<div class="naigai-ch-phase2">`
- line 257: `<div class="naigai-ch-phase2__section">`
- line 259: `<div class="naigai-ch-phase2__hero-grid">`
- line 260: `<div class="naigai-ch-phase2__field">`
- line 265: `<div class="naigai-ch-phase2__field">`
- line 276: `<div class="naigai-ch-phase2__section">`
- line 280: `<div class="naigai-ch-phase2__list">`
- line 287: `<div class="naigai-ch-phase2__item">`
- line 288: `<div class="naigai-ch-phase2__item-head">`
- line 293: `<div class="naigai-ch-phase2__grid">`
- line 295: `<div class="naigai-ch-phase2__preview">`
- line 303: `<div class="naigai-ch-phase2__buttons">`
- line 310: `<div class="naigai-ch-phase2__fields">`
- line 311: `<div class="naigai-ch-phase2__field">`
- line 316: `<div class="naigai-ch-phase2__field">`
- line 325: `<div class="naigai-ch-phase2__item naigai-ch-phase2__item-template">`
- line 326: `<div class="naigai-ch-phase2__item-head">`
- line 331: `<div class="naigai-ch-phase2__grid">`
- line 333: `<div class="naigai-ch-phase2__preview">`
- line 337: `<div class="naigai-ch-phase2__buttons">`
- line 344: `<div class="naigai-ch-phase2__fields">`
- line 345: `<div class="naigai-ch-phase2__field">`
- line 350: `<div class="naigai-ch-phase2__field">`
- line 359: `<div class="naigai-ch-phase2__actions">`
- line 364: `<div class="naigai-ch-phase2__section">`
- line 368: `<div class="naigai-ch-phase2__list">`
- line 374: `<div class="naigai-ch-phase2__item">`
- line 375: `<div class="naigai-ch-phase2__item-head">`
- line 380: `<div class="naigai-ch-phase2__grid">`
- line 382: `<div class="naigai-ch-phase2__preview">`
- line 392: `<div class="naigai-ch-phase2__buttons">`
- line 399: `<div class="naigai-ch-phase2__fields">`
- line 400: `<div class="naigai-ch-phase2__field">`
- line 412: `<div class="naigai-ch-phase2__item naigai-ch-phase2__item-template">`
- line 413: `<div class="naigai-ch-phase2__item-head">`
- line 418: `<div class="naigai-ch-phase2__grid">`
- line 420: `<div class="naigai-ch-phase2__preview">`
- line 424: `<div class="naigai-ch-phase2__buttons">`
- line 431: `<div class="naigai-ch-phase2__fields">`
- line 432: `<div class="naigai-ch-phase2__field">`
- line 444: `<div class="naigai-ch-phase2__actions">`

### visible text items
- line 145: `<p>` → この設定は注文住宅ページ専用です。
- line 258: `<h3>` → Hero 会社名
- line 277: `<h3>` → 施工事例カード
- line 278: `<p class="naigai-ch-phase2__desc">` → 画像・タイトル・説明文です。＋で必要な分だけ増やします。
- line 289: `<h4 class="naigai-ch-phase2__item-title">` → 施工事例
- line 290: `<button class="button-link-delete">` → 削除
- line 299: `<span>` → 未選択
- line 305: `<button class="button">` → 画像を選択
- line 306: `<button class="button">` → クリア
- line 327: `<h4 class="naigai-ch-phase2__item-title">` → 施工事例 __INDEX_LABEL__
- line 328: `<button class="button-link-delete">` → 削除
- line 334: `<span>` → 未選択
- line 339: `<button class="button">` → 画像を選択
- line 340: `<button class="button">` → クリア
- line 360: `<button class="button button-primary">` → ＋ 施工事例を追加
- line 365: `<h3>` → CTA メディア
- line 366: `<p class="naigai-ch-phase2__desc">` → 通常のメディア選択です。複数あれば Swiper、1件ならそのまま表示します。
- line 376: `<h4 class="naigai-ch-phase2__item-title">` → CTA メディア
- line 377: `<button class="button-link-delete">` → 削除
- line 388: `<span>` → 未選択
- line 394: `<button class="button">` → ">メディアを選択
- line 395: `<button class="button">` → クリア
- line 414: `<h4 class="naigai-ch-phase2__item-title">` → CTA メディア __INDEX_LABEL__
- line 415: `<button class="button-link-delete">` → 削除
- line 421: `<span>` → 未選択
- line 426: `<button class="button">` → メディアを選択
- line 427: `<button class="button">` → クリア
- line 445: `<button class="button button-primary">` → ＋ CTA メディアを追加
- line 457: `<span>` → 未選択
- line 462: `<span>` → 未選択
- line 469: `<span>` → 未選択
- line 529: `<span>` → 未選択

## hub/inc/customhome-plan-tabs.php

- page/key: `plan-layout`

### functions
- `naigai_plan_tabs_is_target_page()`
- `naigai_plan_tabs_add_meta_box()`
- `naigai_plan_tabs_render_text_field()`
- `naigai_plan_tabs_render_textarea_field()`
- `naigai_plan_tabs_render_media_field()`
- `naigai_plan_tabs_render_meta_box()`
- `naigai_plan_tabs_save_meta_box()`

### meta keys already used
- `_ch_plan_{$key}_area`
- `_ch_plan_{$key}_build_area`
- `_ch_plan_{$key}_desc`
- `_ch_plan_{$key}_detail_url`
- `_ch_plan_{$key}_hero`
- `_ch_plan_{$key}_label`
- `_ch_plan_{$key}_plan`
- `_ch_plan_{$key}_point1`
- `_ch_plan_{$key}_point2`
- `_ch_plan_{$key}_point3`
- `_ch_plan_{$key}_type`
- `_ch_subpage_template`

### HTML blocks / layout classes
- none

### visible text items
- line 49: `<p>` → '; echo ' ' . esc_html($label) . ' '; echo ' '; echo '
- line 59: `<p>` → '; echo ' ' . esc_html($label) . ' '; echo ' ' . esc_textarea($value) . ' '; echo '
- line 75: `<button class="button button-secondary naigai-plan-media-button">` → 画像を選択
- line 76: `<button class="button naigai-plan-media-remove">` → 画像を削除
- line 82: `<span>` → 画像未設定
- line 97: `<p>` → design-office 用の平屋プラン一覧を編集します。
- line 130: `<h3>` → ' . esc_html($label) . '
- line 179: `<span>` → 画像未設定

## hub/inc/customhome-section-builder.php

- page/key: `customhome-section-builder`

### functions
- `naigai_ch_builder_is_target()`
- `naigai_ch_builder_sections_catalog()`
- `naigai_ch_builder_default_sections()`
- `naigai_ch_builder_get_sections()`
- `naigai_ch_builder_box()`
- `naigai_ch_builder_add_meta_box()`
- `naigai_ch_builder_save()`
- `naigai_ch_render_builder_faq()`
- `naigai_ch_render_builder_sections()`
- `naigai_ch_builder_shortcode()`
- `naigai_ch_builder_mode_box()`
- `naigai_ch_builder_mode_add_box()`
- `naigai_ch_builder_mode_save()`

### meta keys already used
- `_ch_builder_mode_enabled`
- `_ch_builder_sections_json`
- `_ch_faq_`
- `_ch_subpage_template`

### HTML blocks / layout classes
- line 106: `<div class="naigai-ch-builder">`
- line 107: `<div class="naigai-ch-builder__toolbar">`
- line 117: `<div class="naigai-ch-builder__list">`
- line 335: `<section class="ch-section ch-section--white ch-subpage-faq-section">`
- line 336: `<div class="ch-shell">`
- line 337: `<div class="ch-subpage-faq-placeholder">`
- line 341: `<div class="ch-faq-answer">`

### visible text items
- line 100: `<p>` → このページに表示するセクションを上から順番に並べます。文章・画像・CTAはこのページ自身のメタから表示します。
- line 113: `<button class="button button-primary">` → セクションを追加
- line 114: `<button class="button">` → 初期構成に戻す
- line 169: `<strong>` → ' + sectionLabel(type) + '
- line 175: `<button class="button">` → 上へ
- line 176: `<button class="button">` → 下へ
- line 177: `<button class="button">` → 複製
- line 178: `<button class="button button-link-delete">` → 削除
- line 438: `<p>` → company / design-office / plan-tabs / FAQ / CTA などを、構成ビルダーの順番で合算表示します。

## hub/pages/iezukuri/templates/page-builder.php

- page/key: `builder`

### HTML blocks / layout classes
- none

### visible text items
- none

## hub/pages/iezukuri/templates/page-company.php

- page/key: `ch-company-page`

### meta keys already used
- `_ch_company_address`
- `_ch_company_map_iframe`
- `_ch_company_map_text`
- `_ch_company_map_title`

### HTML blocks / layout classes
- line 38: `<div class="ch-page-stack ch-company-page">`
- line 39: `<section class="ch-page-intro">`
- line 42: `<div class="ch-intro-prose">`
- line 58: `<section class="ch-split">`
- line 59: `<div class="ch-split__body">`
- line 99: `<section class="ch-section-block ch-company-map-section">`
- line 100: `<div class="ch-section-head">`
- line 113: `<div class="ch-company-map-embed">`

### visible text items
- line 63: `<a class="ch-btn ch-btn--primary">` → ">
- line 101: `<span class="ch-eyebrow">` → ACCESS

## hub/pages/iezukuri/templates/page-concept.php

- page/key: `ch-concept-page`

### HTML blocks / layout classes
- line 41: `<div class="ch-concept-page">`
- line 42: `<div class="ch-page-band ch-page-band--white">`
- line 43: `<div class="ch-page-inner">`
- line 44: `<section class="ch-page-intro">`
- line 48: `<div class="ch-intro-prose">`
- line 57: `<section class="ch-card-grid ch-card-grid--3">`
- line 65: `<article class="ch-surface-card">`
- line 77: `<div class="ch-page-band ch-page-band--sand">`
- line 78: `<div class="ch-page-inner">`
- line 79: `<section class="ch-split">`
- line 80: `<div class="ch-split__body">`

### visible text items
- line 85: `<a class="ch-btn ch-btn--primary">` → ">

## hub/pages/iezukuri/templates/page-contact.php

- page/key: `ch-contact-page`

### HTML blocks / layout classes
- line 31: `<div class="ch-page-stack ch-contact-page">`
- line 32: `<section class="ch-page-intro">`
- line 36: `<div class="ch-intro-prose">`
- line 66: `<section class="ch-section-block">`
- line 67: `<div class="ch-section-head">`
- line 71: `<div class="ch-card-grid ch-card-grid--2">`
- line 78: `<article class="ch-surface-card">`

### visible text items
- line 68: `<h2>` → よくあるご質問

## hub/pages/iezukuri/templates/page-design-office.php

- page/key: `ch-design-office-page`

### meta keys already used
- `_ch_design_office_split_image_id`
- `_ch_show_plan_tabs`

### HTML blocks / layout classes
- line 67: `<div class="ch-page-stack ch-design-office-page">`
- line 69: `<section class="ch-page-intro">`
- line 79: `<div class="ch-intro-prose">`
- line 83: `<div class="ch-intro-prose">`
- line 90: `<section class="ch-split">`
- line 91: `<div class="ch-split__body">`
- line 128: `<section class="ch-section-block ch-design-office-plan-section">`
- line 129: `<div class="ch-section-head ch-section-head--center">`

### visible text items
- line 105: `<a class="ch-btn ch-btn--primary">` → ">

## hub/pages/iezukuri/templates/page-design-policy.php

- page/key: `ch-design-policy-page`

### HTML blocks / layout classes
- line 44: `<div class="ch-page-stack ch-design-policy-page">`
- line 45: `<section class="ch-page-intro">`
- line 48: `<div class="ch-intro-prose">`
- line 69: `<div class="ch-section-head">`
- line 74: `<div class="ch-card-grid ch-card-grid--5">`
- line 81: `<article class="ch-surface-card ch-step-card">`
- line 93: `<section class="ch-split">`
- line 94: `<div class="ch-split__body">`

### visible text items
- line 99: `<a class="ch-btn ch-btn--primary">` → ">

## hub/pages/iezukuri/templates/page-nasu-house.php

- page/key: `ch-nasu-shot-page`

### HTML blocks / layout classes
- line 76: `<div class="ch-nasu-shot-page">`
- line 78: `<section id="intro" class="ch-nasu-shot-intro">`
- line 79: `<div class="ch-nasu-shot-intro__header">`
- line 87: `<section id="body" class="ch-nasu-shot-body">`
- line 88: `<div class="ch-nasu-shot-body__inner">`
- line 90: `<div class="ch-nasu-shot-body__content">`
- line 97: `<section id="cards" class="ch-nasu-shot-cards-wrap">`
- line 100: `<div class="ch-nasu-shot-cards">`
- line 101: `<article class="ch-nasu-shot-card">`
- line 102: `<div class="ch-nasu-shot-card__icon">`
- line 110: `<article class="ch-nasu-shot-card">`
- line 111: `<div class="ch-nasu-shot-card__icon">`
- line 119: `<article class="ch-nasu-shot-card">`
- line 120: `<div class="ch-nasu-shot-card__icon">`
- line 130: `<section id="feature" class="ch-nasu-shot-feature">`
- line 131: `<div class="ch-nasu-shot-feature__text">`
- line 144: `<section id="values" class="ch-nasu-shot-values">`
- line 147: `<div class="ch-nasu-shot-values__grid">`
- line 148: `<article class="ch-nasu-shot-value">`
- line 149: `<div class="ch-nasu-shot-value__icon">`
- line 154: `<article class="ch-nasu-shot-value">`
- line 155: `<div class="ch-nasu-shot-value__icon">`
- line 160: `<article class="ch-nasu-shot-value">`
- line 161: `<div class="ch-nasu-shot-value__icon">`
- line 166: `<article class="ch-nasu-shot-value">`
- line 167: `<div class="ch-nasu-shot-value__icon">`

### visible text items
- line 35: `<span>` → 3つの視点
- line 136: `<a class="ch-btn ch-btn--primary">` → ">

## page-construction-hub-sub.php

- page/key: `construction-hub-sub`

### meta keys already used
- `_ch_back_url`
- `_ch_common_form_shortcode`
- `_ch_contact_url`
- `_ch_faq_`
- `_ch_hero_image_id`
- `_ch_hero_kicker`
- `_ch_hero_lead`
- `_ch_hero_primary_label`
- `_ch_hero_secondary_label`
- `_ch_hero_title`
- `_ch_layout_mode`
- `_ch_show_common_faq`
- `_ch_show_common_form`
- `_ch_show_customhome_cta`
- `_ch_subpage_template`
- `_ch_use_parent_flow`
- `_ch_use_parent_works`

### HTML blocks / layout classes
- line 184: `<section class="ch-hero ch-fullbleed ch-subpage-hero">`
- line 185: `<div class="ch-hero__media">`
- line 189: `<div class="ch-hero__overlay">`
- line 192: `<div class="ch-shell ch-hero__inner">`
- line 193: `<div class="ch-hero__content">`
- line 194: `<div class="ch-kicker">`
- line 201: `<div class="ch-hero__actions">`
- line 211: `<div class="ch-shell">`
- line 237: `<section class="ch-section ch-section--white ch-subpage-section">`
- line 238: `<div class="ch-shell">`
- line 239: `<article class="ch-subpage-main ch-subpage-main--fallback">`
- line 279: `<section class="ch-section ch-section--white ch-subpage-form-section">`
- line 280: `<div class="ch-shell">`
- line 281: `<div class="ch-subpage-form-placeholder">`
- line 289: `<section class="ch-section ch-section--white ch-subpage-faq-section">`
- line 290: `<div class="ch-shell">`
- line 291: `<div class="ch-subpage-faq-placeholder">`
- line 295: `<div class="ch-faq-answer">`

### visible text items
- line 202: `<a class="ch-btn ch-btn--primary">` → ">
- line 203: `<a class="ch-btn ch-btn--ghost">` → ">
- line 223: `<li>` → 注文住宅の考え方
- line 224: `<li>` → 設計姿勢
- line 225: `<li>` → 那須での家づくり
- line 226: `<li>` → デザインと設計
- line 227: `<li>` → 会社概要
- line 228: `<li>` → ご相談・資料請求
