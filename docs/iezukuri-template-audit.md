# iezukuri template audit

- generated: 2026-05-02T00:22:33
- files scanned: 41

## 台帳パーツ候補

### company_access

#### files
- `hub/inc/customhome-company-info.php`
- `hub/pages/iezukuri/templates/page-company.php`

#### functions
- `naigai_ch_company_allowed_iframe()`
- `naigai_ch_company_find_meta()`
- `naigai_ch_company_get_access_data()`
- `naigai_ch_company_info_add_box()`
- `naigai_ch_company_info_box()`
- `naigai_ch_company_info_save()`
- `naigai_ch_company_is_target()`
- `naigai_ch_company_source_page_id()`
- `naigai_ch_render_company_access_section()`

#### meta keys
- `_ch_company_address`
- `_ch_company_info_source`
- `_ch_company_map_iframe`
- `_ch_company_map_text`
- `_ch_company_map_title`
- `_ch_company_source_page_id`
- `_ch_subpage_template`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-company-map-address`
- `ch-company-map-embed`
- `ch-company-map-section`
- `ch-company-page`
- `ch-eyebrow`
- `ch-intro-prose`
- `ch-page-intro`
- `ch-page-stack`
- `ch-section-block`
- `ch-section-head`
- `ch-split`
- `ch-split__body`
- `ch-split__media`
- `naigai-admin-guide`
- `naigai-admin-section`

### contact

#### files
- `hub/inc/customhome-contact-admin-fields.php`
- `hub/inc/customhome-contact-form-bridge.php`
- `hub/inc/customhome-contact-sections.php`
- `hub/pages/iezukuri/templates/page-contact.php`

#### functions
- `naigai_ch_contact_admin_add_box()`
- `naigai_ch_contact_admin_box()`
- `naigai_ch_contact_admin_is_target()`
- `naigai_ch_contact_admin_page_options()`
- `naigai_ch_contact_admin_page_select()`
- `naigai_ch_contact_admin_save()`
- `naigai_ch_contact_admin_text()`
- `naigai_ch_contact_flow_shortcode()`
- `naigai_ch_contact_form_shortcode()`
- `naigai_ch_contact_form_source_html()`
- `naigai_ch_contact_source_page_id()`
- `naigai_ch_existing_contact_form_shortcode()`
- `naigai_ch_meta()`
- `naigai_ch_render_contact_flow_section()`
- `naigai_ch_render_contact_form_section()`
- `naigai_ch_render_existing_contact_form_bridge()`

#### meta keys
- `_ch_contact_form_shortcode`
- `_ch_contact_form_source_page_id`
- `_ch_subpage_template`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-card-grid`
- `ch-card-grid--2`
- `ch-contact-flow`
- `ch-contact-flow-section`
- `ch-contact-flow__item`
- `ch-contact-flow__num`
- `ch-contact-form-embedded`
- `ch-contact-form-embedded--inline`
- `ch-contact-form-fallback`
- `ch-contact-form-missing`
- `ch-contact-form-section`
- `ch-contact-form-shell`
- `ch-contact-page`
- `ch-eyebrow`
- `ch-intro-prose`
- `ch-page-intro`
- `ch-page-stack`
- `ch-section`
- `ch-section--white`
- `ch-section-block`
- `ch-section-head`
- `ch-section-head--center`
- `ch-shell`
- `ch-surface-card`
- `naigai-admin-field`
- `naigai-admin-guide`
- `naigai-admin-guide--contact`
- `naigai-admin-section`
- `naigai-admin-subcard`

### cta

#### files
- `hub/inc/customhome-cta.php`
- `hub/inc/customhome-extra-settings.php`
- `hub/inc/customhome-meta.php`
- `hub/inc/customhome-phase2.php`
- `hub/inc/customhome-shortcode.php`
- `hub/inc/customhome-subpage-controls.php`
- `hub/inc/customhome-subpage-cta-fields.php`
- `hub/partials/section-cta.php`
- `hub/templates/construction.php`

#### functions
- `naigai_ch_get_iezukuri_page_options()`
- `naigai_ch_is_iezukuri_child_page()`
- `naigai_ch_subpage_cleanup_meta_boxes()`
- `naigai_ch_subpage_controls_add_meta_boxes()`
- `naigai_ch_subpage_controls_enqueue_media()`
- `naigai_ch_subpage_controls_save()`
- `naigai_ch_subpage_cta_background_box()`
- `naigai_ch_subpage_cta_box()`
- `naigai_ch_subpage_cta_fields_add_box()`
- `naigai_ch_subpage_cta_fields_enqueue()`
- `naigai_ch_subpage_cta_fields_save()`
- `naigai_ch_subpage_cta_fields_target()`
- `naigai_ch_subpage_cta_page_options()`
- `naigai_ch_subpage_cta_page_row()`
- `naigai_ch_subpage_cta_page_select()`
- `naigai_ch_subpage_cta_text_input()`
- `naigai_ch_subpage_cta_text_row()`
- `naigai_ch_subpage_display_controls_box()`
- `naigai_customhome_add_meta_box()`
- `naigai_customhome_admin_enqueue_media()`
- `naigai_customhome_admin_ui_assets()`
- `naigai_customhome_attachment_url()`
- `naigai_customhome_block_asset_needles()`
- `naigai_customhome_concept_is_target()`
- `naigai_customhome_concept_render_meta_box()`
- `naigai_customhome_concept_save_meta_box()`
- `naigai_customhome_cta_media_items()`
- `naigai_customhome_cta_media_tag()`
- `naigai_customhome_cta_meta()`
- `naigai_customhome_cta_shortcode()`
- `naigai_customhome_cta_url()`
- `naigai_customhome_defaults()`
- `naigai_customhome_enqueue_assets()`
- `naigai_customhome_extra_add_meta_box()`
- `naigai_customhome_extra_admin_enqueue()`
- `naigai_customhome_extra_fields()`
- `naigai_customhome_extra_is_target()`
- `naigai_customhome_extra_render_media_field()`
- `naigai_customhome_extra_render_meta_box()`
- `naigai_customhome_extra_save_meta_box()`
- `naigai_customhome_filter_output_assets()`
- `naigai_customhome_get_media_preview_html()`
- `naigai_customhome_meta_box()`
- `naigai_customhome_meta_value()`
- `naigai_customhome_page_matches()`
- `naigai_customhome_phase2_add_meta_box()`
- `naigai_customhome_phase2_enqueue_admin()`
- `naigai_customhome_phase2_enqueue_front()`
- `naigai_customhome_phase2_get_attachment_preview()`
- `naigai_customhome_phase2_hide_legacy_admin()`
- `naigai_customhome_phase2_is_target()`
- `naigai_customhome_phase2_render_meta_box()`
- `naigai_customhome_phase2_save()`
- `naigai_customhome_register_menu_location()`
- `naigai_customhome_remove_registered_assets()`
- `naigai_customhome_render_media_row()`
- `naigai_customhome_render_text_row()`
- `naigai_customhome_request_matches()`
- `naigai_customhome_save_meta_box()`
- `naigai_customhome_seed_menu()`
- `naigai_customhome_shortcode()`
- `naigai_customhome_shortcode_defaults()`
- `naigai_customhome_start_buffer()`
- `naigai_customhome_subpage_cleanup_meta_boxes()`
- `naigai_customhome_subpage_common_add_meta_boxes()`
- `naigai_customhome_subpage_common_checkbox_row()`
- `naigai_customhome_subpage_common_is_target()`
- `naigai_customhome_subpage_common_render_meta_box()`
- `naigai_customhome_subpage_common_save_meta_box()`
- `naigai_customhome_subpage_common_select_row()`
- `naigai_is_customhome_target_page()`

#### meta keys
- `_ch_show_customhome_cta`
- `_ch_show_plan_tabs`
- `_ch_subpage_template`
- `_hub_ch_brand_logo_id`
- `_hub_ch_brand_subtext`
- `_hub_ch_brand_text`
- `_hub_ch_cta_image_id`
- `_hub_ch_cta_image_ids`
- `_hub_ch_cta_image_items`
- `_hub_ch_cta_media_items`
- `_hub_ch_cta_secondary_override_label`
- `_hub_ch_cta_secondary_override_page_id`
- `_hub_ch_cta_secondary_override_url`
- `_hub_ch_cta_swiper_autoplay`
- `_hub_ch_cta_swiper_enabled`
- `_hub_ch_cta_swiper_nav`
- `_hub_ch_cta_swiper_pagination`
- `_hub_ch_cta_video_controls`
- `_hub_ch_cta_video_ids`
- `_hub_ch_cta_video_items`
- `_hub_ch_header_menu_style`
- `_hub_ch_hero_company`
- `_hub_ch_hero_company_position`
- `_hub_ch_hero_video_mp4_id`
- `_hub_ch_hero_video_poster_id`
- `_hub_ch_hero_video_webm_id`
- `_hub_ch_localnav_mode`
- `_hub_ch_page_menu_style`
- `_hub_ch_work_items`
- `_hub_ch_work_{$i}_image_id`
- `_wp_attachment_image_alt`
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

#### classes
- `ch-btn`
- `ch-btn--ghost`
- `ch-btn--ghost-light`
- `ch-btn--primary`
- `ch-cta`
- `ch-cta-swiper`
- `ch-cta__actions`
- `ch-cta__body`
- `ch-cta__grid`
- `ch-cta__image`
- `ch-cta__media`
- `ch-cta__text`
- `ch-cta__title`
- `ch-cta__video`
- `ch-customhome-cta`
- `ch-customhome-cta--<?php`
- `ch-customhome-cta__image`
- `ch-customhome-cta__media-item`
- `ch-customhome-cta__video`
- `ch-eyebrow`
- `ch-eyebrow--light`
- `ch-feature-card`
- `ch-feature-card__icon`
- `ch-feature-card__text`
- `ch-feature-card__title`
- `ch-feature-grid`
- `ch-flow-item`
- `ch-flow-item__icon`
- `ch-flow-item__num`
- `ch-flow-item__text`
- `ch-flow-item__title`
- `ch-flow-list`
- `ch-fullbleed`
- `ch-head`
- `ch-head--with-link`
- `ch-hero`
- `ch-hero__actions`
- `ch-hero__brand-logo`
- `ch-hero__brand-logo-wrap`
- `ch-hero__company`
- `ch-hero__company-sub`
- `ch-hero__content`
- `ch-hero__image`
- `ch-hero__inner`
- `ch-hero__lead`
- `ch-hero__media`
- `ch-hero__overlay`
- `ch-hero__title`
- `ch-hero__video`
- `ch-inline-link`
- `ch-kicker`
- `ch-localnav`
- `ch-localnav--<?php`
- `ch-localnav__list`
- `ch-more-btn`
- `ch-more-btn__label`
- `ch-more-btn__loading`
- `ch-more-panel`
- `ch-more-toggle`
- `ch-nasu-shot-cta`
- `ch-nasu-shot-cta__content`
- `ch-nasu-shot-cta__media`
- `ch-nasu-shot-cta__overlay`
- `ch-nasu-shot-cta__swiper`
- `ch-section`
- `ch-section--white`
- `ch-section-title`
- `ch-shell`
- `ch-work-card`
- `ch-work-card__body`
- `ch-work-card__image`
- `ch-work-card__text`
- `ch-work-card__thumb`
- `ch-work-card__title`
- `ch-works-grid`
- `ch-works-grid--more`
- `hub-container`
- `hub-cta-banner`
- `hub-cta-banner__content`
- `hub-cta-button`

### customhome-subpage_body_admin-cleanup

#### files
- `hub/inc/customhome-subpage-admin-cleanup.php`

#### functions
- `naigai_ch_admin_is_contact_subpage()`
- `naigai_ch_admin_is_customhome_subpage()`
- `naigai_ch_admin_organize_metaboxes()`
- `naigai_ch_is_customhome_subpage_editor()`
- `naigai_ch_remove_parent_metaboxes_for_subpage()`
- `naigai_ch_subpage_admin_enqueue_style()`

#### meta keys
- `_ch_subpage_template`

### flow

#### files
- `hub/partials/section-flow.php`

#### classes
- `hub-container`
- `hub-flow-list`
- `hub-flow-step`
- `hub-flow-text`
- `hub-flow-title`
- `hub-heading`
- `hub-lead`
- `hub-section`
- `hub-section--flow`
- `hub-section-head`
- `hub-section__inner`

### layout_fields

#### files
- `hub/inc/customhome-subpage-layout-fields.php`

#### functions
- `naigai_ch_all_page_options()`
- `naigai_ch_is_subpage_layout_builder_target()`
- `naigai_ch_layout_options()`
- `naigai_ch_render_layout_field()`
- `naigai_ch_subpage_layout_fields_add_meta_box()`
- `naigai_ch_subpage_layout_fields_box()`
- `naigai_ch_subpage_layout_fields_config()`
- `naigai_ch_subpage_layout_fields_enqueue()`
- `naigai_ch_subpage_layout_fields_save()`

#### meta keys
- `_ch_subpage_template`

#### classes
- `naigai-ch-layout-field-group`
- `naigai-ch-layout-field-group-empty`
- `naigai-ch-layout-image-preview`

### page_body_builder

#### files
- `hub/pages/iezukuri/templates/page-builder.php`

### page_body_concept

#### files
- `hub/pages/iezukuri/templates/page-concept.php`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-card-grid`
- `ch-card-grid--3`
- `ch-concept-page`
- `ch-eyebrow`
- `ch-intro-prose`
- `ch-page-band`
- `ch-page-band--sand`
- `ch-page-band--white`
- `ch-page-inner`
- `ch-page-intro`
- `ch-split`
- `ch-split__body`
- `ch-split__media`
- `ch-surface-card`
- `ch-surface-card__media`

### page_body_design-office

#### files
- `hub/pages/iezukuri/templates/page-design-office.php`

#### meta keys
- `_ch_design_office_split_image_id`
- `_ch_show_plan_tabs`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-design-office-page`
- `ch-design-office-plan-section`
- `ch-eyebrow`
- `ch-intro-prose`
- `ch-page-intro`
- `ch-page-stack`
- `ch-section-block`
- `ch-section-head`
- `ch-section-head--center`
- `ch-split`
- `ch-split__body`
- `ch-split__media`

### page_body_design-policy

#### files
- `hub/pages/iezukuri/templates/page-design-policy.php`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-card-grid`
- `ch-card-grid--5`
- `ch-design-policy-page`
- `ch-eyebrow`
- `ch-intro-prose`
- `ch-page-intro`
- `ch-page-stack`
- `ch-section-head`
- `ch-split`
- `ch-split__body`
- `ch-split__media`
- `ch-step-card`
- `ch-step-card__num`
- `ch-surface-card`

### page_body_nasu-house

#### files
- `hub/pages/iezukuri/templates/page-nasu-house.php`

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-eyebrow`
- `ch-nasu-shot-body`
- `ch-nasu-shot-body__content`
- `ch-nasu-shot-body__inner`
- `ch-nasu-shot-card`
- `ch-nasu-shot-card__icon`
- `ch-nasu-shot-card__image`
- `ch-nasu-shot-cards`
- `ch-nasu-shot-cards-wrap`
- `ch-nasu-shot-feature`
- `ch-nasu-shot-feature__image`
- `ch-nasu-shot-feature__text`
- `ch-nasu-shot-intro`
- `ch-nasu-shot-intro__header`
- `ch-nasu-shot-page`
- `ch-nasu-shot-section-title`
- `ch-nasu-shot-value`
- `ch-nasu-shot-value__icon`
- `ch-nasu-shot-values`
- `ch-nasu-shot-values__grid`

### plan_layout

#### files
- `hub/inc/customhome-plan-tabs.php`
- `hub/partials/section-plan-tabs.php`

#### functions
- `naigai_plan_tabs_add_meta_box()`
- `naigai_plan_tabs_is_target_page()`
- `naigai_plan_tabs_render_media_field()`
- `naigai_plan_tabs_render_meta_box()`
- `naigai_plan_tabs_render_text_field()`
- `naigai_plan_tabs_render_textarea_field()`
- `naigai_plan_tabs_save_meta_box()`

#### meta keys
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

#### classes
- `ch-btn`
- `ch-btn--primary`
- `ch-eyebrow`
- `ch-head`
- `ch-plan-tabs`
- `ch-plan-tabs__box`
- `ch-plan-tabs__detail`
- `ch-plan-tabs__hero-image`
- `ch-plan-tabs__lead`
- `ch-plan-tabs__media`
- `ch-plan-tabs__nav`
- `ch-plan-tabs__panel`
- `ch-plan-tabs__panel--<?php`
- `ch-plan-tabs__panel-grid`
- `ch-plan-tabs__panels`
- `ch-plan-tabs__plan-image`
- `ch-plan-tabs__points`
- `ch-plan-tabs__radio`
- `ch-plan-tabs__stats`
- `ch-plan-tabs__summary-badge`
- `ch-plan-tabs__summary-card`
- `ch-plan-tabs__summary-head`
- `ch-plan-tabs__tab`
- `ch-plan-tabs__tab-badge`
- `ch-plan-tabs__tab-type`
- `ch-plan-tabs__text`
- `ch-plan-tabs__thumb`
- `ch-plan-tabs__thumb-meta`
- `ch-plan-tabs__thumbs`
- `ch-section`
- `ch-section--white`
- `ch-section-title`
- `ch-shell`
- `naigai-plan-media-button`
- `naigai-plan-media-remove`

### section_builder

#### files
- `hub/inc/customhome-section-builder.php`

#### functions
- `naigai_ch_builder_add_meta_box()`
- `naigai_ch_builder_box()`
- `naigai_ch_builder_default_sections()`
- `naigai_ch_builder_get_sections()`
- `naigai_ch_builder_is_target()`
- `naigai_ch_builder_mode_add_box()`
- `naigai_ch_builder_mode_box()`
- `naigai_ch_builder_mode_save()`
- `naigai_ch_builder_save()`
- `naigai_ch_builder_sections_catalog()`
- `naigai_ch_builder_shortcode()`
- `naigai_ch_render_builder_faq()`
- `naigai_ch_render_builder_sections()`

#### meta keys
- `_ch_builder_mode_enabled`
- `_ch_builder_sections_json`
- `_ch_faq_`
- `_ch_subpage_template`

#### classes
- `ch-faq-answer`
- `ch-faq-item`
- `ch-section`
- `ch-section--white`
- `ch-shell`
- `ch-subpage-faq-placeholder`
- `ch-subpage-faq-section`
- `naigai-ch-builder`
- `naigai-ch-builder__list`
- `naigai-ch-builder__toolbar`

### subpage_shell

#### files
- `page-construction-hub-sub.php`

#### meta keys
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

#### classes
- `ch-btn`
- `ch-btn--ghost`
- `ch-btn--primary`
- `ch-faq-answer`
- `ch-faq-item`
- `ch-fullbleed`
- `ch-hero`
- `ch-hero__actions`
- `ch-hero__content`
- `ch-hero__image`
- `ch-hero__inner`
- `ch-hero__lead`
- `ch-hero__media`
- `ch-hero__overlay`
- `ch-hero__title`
- `ch-kicker`
- `ch-localnav`
- `ch-localnav--construction`
- `ch-localnav__list`
- `ch-section`
- `ch-section--white`
- `ch-shell`
- `ch-subpage-faq-placeholder`
- `ch-subpage-faq-section`
- `ch-subpage-form-placeholder`
- `ch-subpage-form-section`
- `ch-subpage-hero`
- `ch-subpage-main`
- `ch-subpage-main--fallback`
- `ch-subpage-section`
- `hub-customhome-page`
- `hub-customhome-subpage`
- `hub-customhome-subpage--<?php`

### unknown

#### files
- `hub/inc/customhome-admin-order-final.php`
- `hub/inc/customhome-guard.php`
- `hub/inc/customhome-menus.php`
- `hub/inc/customhome-section-control-admin.php`
- `hub/inc/customhome-section-registry.php`
- `hub/pages/iezukuri/templates/nasu-house.php`
- `hub/partials/customhome-line-icons.php`
- `hub/partials/front/post-template-copy.php`
- `hub/partials/section-hero.php`
- `hub/partials/section-intro-hero.php`
- `hub/partials/section-links.php`
- `hub/partials/section-support-grid.php`
- `hub/templates/front.php`
- `hub/templates/realestate.php`

#### functions
- `naigai_ch_admin_order_enqueue_css()`
- `naigai_ch_admin_order_is_contact()`
- `naigai_ch_admin_order_is_subpage()`
- `naigai_ch_admin_order_metaboxes()`
- `naigai_ch_final_admin_reset_boxes()`
- `naigai_ch_get_section_controls()`
- `naigai_ch_hide_old_section_builder_box()`
- `naigai_ch_icon_svg()`
- `naigai_ch_section_control_add_box()`
- `naigai_ch_section_control_box()`
- `naigai_ch_section_control_current()`
- `naigai_ch_section_control_is_target()`
- `naigai_ch_section_control_map()`
- `naigai_ch_section_control_save()`
- `naigai_ch_section_default_keys()`
- `naigai_ch_section_enabled()`
- `naigai_ch_section_registry()`
- `naigai_customhome_ensure_menu()`
- `naigai_customhome_find_page_url()`
- `naigai_customhome_register_nav_locations()`
- `naigai_customhome_remove_meta_box_on_other_pages()`
- `naigai_customhome_seed_nav_menus()`
- `naigai_fudo_area_label()`
- `naigai_fudo_clean_media_ids()`
- `naigai_fudo_filter_class()`
- `naigai_fudo_format_price_label()`
- `naigai_fudo_get_card_data()`
- `naigai_fudo_get_card_thumb_url()`
- `naigai_fudo_get_customizer_categories()`
- `naigai_fudo_get_map_html()`
- `naigai_fudo_get_post_term_classes()`
- `naigai_fudo_get_price_raw()`
- `naigai_fudo_has_term_name_like()`
- `naigai_fudo_icon()`
- `naigai_fudo_is_sold()`
- `naigai_fudo_meta_first()`
- `naigai_fudo_numeric_value()`
- `naigai_fudo_page_meta()`
- `naigai_fudo_render_card_badges()`
- `naigai_fudo_render_card_facts()`
- `naigai_fudo_render_card_media()`
- `naigai_fudo_render_hero_media()`
- `naigai_fudo_render_map_button()`
- `naigai_fudo_youtube_id_from_value()`

#### meta keys
- `BuildingArea`
- `GoogleEmbedcode`
- `LandArea`
- `Location`
- `NewBuildingArea`
- `NewGoogleEmbedcode`
- `NewLandArea`
- `NewLocation`
- `NewPrice`
- `Price`
- `_ch_nasu_card1_image_id`
- `_ch_nasu_card1_text`
- `_ch_nasu_card1_title`
- `_ch_nasu_card2_image_id`
- `_ch_nasu_card2_text`
- `_ch_nasu_card2_title`
- `_ch_nasu_card3_image_id`
- `_ch_nasu_card3_text`
- `_ch_nasu_card3_title`
- `_ch_nasu_cta_text`
- `_ch_nasu_cta_title`
- `_ch_nasu_feature_image_id`
- `_ch_nasu_feature_text`
- `_ch_nasu_feature_title`
- `_ch_nasu_hero_image_id`
- `_ch_nasu_intro_text`
- `_ch_nasu_intro_title`
- `_ch_nasu_point1_text`
- `_ch_nasu_point1_title`
- `_ch_nasu_point2_text`
- `_ch_nasu_point2_title`
- `_ch_nasu_point3_text`
- `_ch_nasu_point3_title`
- `_ch_nasu_point4_text`
- `_ch_nasu_point4_title`
- `_ch_section_controls_json`
- `_ch_subpage_template`
- `_front_slide_{$i}_enabled`
- `_front_slide_{$i}_image_id`
- `_front_slide_{$i}_label`
- `_front_slide_{$i}_text`
- `_front_slide_{$i}_title`
- `_front_slide_{$i}_url`
- `_front_slide_{$i}_video_id`
- `_front_slide_{$i}_video_type`
- `_fudo_hero_image_ids`
- `_fudo_hero_mp4_id`
- `_fudo_hero_youtube_id`
- `_google_map_iframe_1`
- `_hub_feature_post_id`
- `_hub_hero_video_id`
- `_wp_attachment_image_alt`
- `naigai_ch_sections[<?php echo esc_attr($key); ?>][enabled]`
- `naigai_ch_sections[<?php echo esc_attr($key); ?>][order]`
- `page_featured_type`
- `page_video_id`
- `sold-out`

#### classes
- `ch-btn`
- `ch-btn--ghost-light`
- `ch-btn--primary`
- `ch-eyebrow`
- `ch-eyebrow--light`
- `ch-hero`
- `ch-hero__actions`
- `ch-hero__content`
- `ch-hero__image`
- `ch-hero__inner`
- `ch-hero__lead`
- `ch-hero__media`
- `ch-hero__overlay`
- `ch-hero__title`
- `ch-localnav`
- `ch-localnav--underline`
- `ch-localnav__list`
- `ch-nasu-shot-card`
- `ch-nasu-shot-card__icon`
- `ch-nasu-shot-card__image`
- `ch-nasu-shot-cards`
- `ch-nasu-shot-cards-wrap`
- `ch-nasu-shot-feature`
- `ch-nasu-shot-feature__image`
- `ch-nasu-shot-feature__text`
- `ch-nasu-shot-intro`
- `ch-nasu-shot-intro__header`
- `ch-nasu-shot-page`
- `ch-nasu-shot-section-title`
- `ch-nasu-shot-value`
- `ch-nasu-shot-value__icon`
- `ch-nasu-shot-values`
- `ch-nasu-shot-values__grid`
- `ch-shell`
- `ch-subpage-hero`
- `ch-subpage-section`
- `hub-btn`
- `hub-card__img`
- `hub-card__media`
- `hub-card__media--slider`
- `hub-container`
- `hub-customhome-page`
- `hub-customhome-subpage`
- `hub-customhome-subpage--<?php`
- `hub-front-hero`
- `hub-front-hero__facts`
- `hub-front-hero__facts-shell`
- `hub-front-hero__intro`
- `hub-front-hero__kicker`
- `hub-front-hero__lead`
- `hub-front-hero__map-action`
- `hub-front-hero__summary`
- `hub-front-hero__title`
- `hub-front-media__nav`
- `hub-front-media__nav--next`
- `hub-front-media__nav--prev`
- `hub-heading`
- `hub-hero-section<?php`
- `hub-hero__body`
- `hub-hero__grid<?php`
- `hub-hero__img`
- `hub-hero__inner`
- `hub-hero__lite-vimeo`
- `hub-hero__lite-youtube`
- `hub-hero__media`
- `hub-hero__media--slider`
- `hub-hero__media<?php`
- `hub-hero__slide-card<?php`
- `hub-hero__slide-info`
- `hub-hero__slide-link`
- `hub-hero__slide-media`
- `hub-hero__slide-title`
- `hub-intro-hero`
- `hub-intro-hero__actions`
- `hub-intro-hero__body`
- `hub-intro-hero__inner`
- `hub-intro-hero__lead`
- `hub-intro-hero__media`
- `hub-intro-hero__nav`
- `hub-intro-hero__nav-link`

## ファイル別詳細

### hub/inc/customhome-admin-order-final.php
- inferred part: `unknown`
- functions: `naigai_ch_admin_order_is_subpage()`, `naigai_ch_admin_order_is_contact()`, `naigai_ch_admin_order_metaboxes()`, `naigai_ch_admin_order_enqueue_css()`, `naigai_ch_hide_old_section_builder_box()`, `naigai_ch_final_admin_reset_boxes()`
- meta keys: `_ch_subpage_template`

### hub/inc/customhome-company-info.php
- inferred part: `company_access`
- functions: `naigai_ch_company_is_target()`, `naigai_ch_company_find_meta()`, `naigai_ch_company_source_page_id()`, `naigai_ch_company_get_access_data()`, `naigai_ch_company_allowed_iframe()`, `naigai_ch_company_info_box()`, `naigai_ch_company_info_add_box()`, `naigai_ch_company_info_save()`, `naigai_ch_render_company_access_section()`
- meta keys: `_ch_company_address`, `_ch_company_info_source`, `_ch_company_map_iframe`, `_ch_company_map_text`, `_ch_company_map_title`, `_ch_company_source_page_id`, `_ch_subpage_template`
- classes: `ch-company-map-address`, `ch-company-map-embed`, `ch-company-map-section`, `ch-eyebrow`, `ch-section-block`, `ch-section-head`, `naigai-admin-guide`, `naigai-admin-section`

### hub/inc/customhome-contact-admin-fields.php
- inferred part: `contact`
- functions: `naigai_ch_contact_admin_is_target()`, `naigai_ch_contact_admin_page_options()`, `naigai_ch_contact_admin_text()`, `naigai_ch_contact_admin_page_select()`, `naigai_ch_contact_admin_box()`, `naigai_ch_contact_admin_add_box()`, `naigai_ch_contact_admin_save()`
- meta keys: `_ch_subpage_template`
- classes: `naigai-admin-field`, `naigai-admin-guide`, `naigai-admin-guide--contact`, `naigai-admin-section`, `naigai-admin-subcard`

### hub/inc/customhome-contact-form-bridge.php
- inferred part: `contact`
- functions: `naigai_ch_render_existing_contact_form_bridge()`, `naigai_ch_existing_contact_form_shortcode()`
- classes: `ch-contact-form-embedded`, `ch-contact-form-embedded--inline`, `ch-contact-form-missing`

### hub/inc/customhome-contact-sections.php
- inferred part: `contact`
- functions: `naigai_ch_meta()`, `naigai_ch_contact_source_page_id()`, `naigai_ch_render_contact_flow_section()`, `naigai_ch_contact_form_source_html()`, `naigai_ch_render_contact_form_section()`, `naigai_ch_contact_flow_shortcode()`, `naigai_ch_contact_form_shortcode()`
- meta keys: `_ch_contact_form_shortcode`, `_ch_contact_form_source_page_id`
- classes: `ch-btn`, `ch-btn--primary`, `ch-contact-flow`, `ch-contact-flow-section`, `ch-contact-flow__item`, `ch-contact-flow__num`, `ch-contact-form-fallback`, `ch-contact-form-section`, `ch-contact-form-shell`, `ch-eyebrow`, `ch-section`, `ch-section--white`, `ch-section-head`, `ch-section-head--center`, `ch-shell`

### hub/inc/customhome-cta.php
- inferred part: `cta`
- functions: `naigai_customhome_cta_meta()`, `naigai_customhome_cta_url()`, `naigai_customhome_cta_media_items()`, `naigai_customhome_cta_media_tag()`, `naigai_customhome_cta_shortcode()`
- meta keys: `_hub_ch_cta_image_items`, `_hub_ch_cta_media_items`, `_hub_ch_cta_video_items`, `_wp_attachment_image_alt`
- classes: `ch-btn`, `ch-btn--ghost`, `ch-btn--primary`, `ch-cta-swiper`, `ch-customhome-cta`, `ch-customhome-cta--<?php`, `ch-customhome-cta__image`, `ch-customhome-cta__media-item`, `ch-customhome-cta__video`, `ch-eyebrow`, `ch-eyebrow--light`, `ch-hero__actions`, `ch-nasu-shot-cta`, `ch-nasu-shot-cta__content`, `ch-nasu-shot-cta__media`, `ch-nasu-shot-cta__overlay`, `ch-nasu-shot-cta__swiper`

### hub/inc/customhome-extra-settings.php
- inferred part: `cta`
- functions: `naigai_customhome_extra_is_target()`, `naigai_customhome_extra_fields()`, `naigai_customhome_extra_admin_enqueue()`, `naigai_customhome_extra_add_meta_box()`, `naigai_customhome_extra_render_media_field()`, `naigai_customhome_extra_render_meta_box()`, `naigai_customhome_extra_save_meta_box()`
- meta keys: `_hub_ch_brand_subtext`, `_hub_ch_brand_text`, `_hub_ch_cta_secondary_override_label`, `_hub_ch_cta_secondary_override_page_id`, `_hub_ch_cta_secondary_override_url`, `_hub_ch_header_menu_style`, `_hub_ch_localnav_mode`, `_hub_ch_page_menu_style`
- classes: `naigai-ch-extra`, `naigai-ch-extra-media`, `naigai-ch-extra-media-clear`, `naigai-ch-extra-media-open`, `naigai-ch-extra-media__controls`, `naigai-ch-extra-media__preview`, `naigai-ch-extra-select`

### hub/inc/customhome-guard.php
- inferred part: `unknown`
- functions: `naigai_customhome_remove_meta_box_on_other_pages()`

### hub/inc/customhome-menus.php
- inferred part: `unknown`
- functions: `naigai_customhome_find_page_url()`, `naigai_customhome_register_nav_locations()`, `naigai_customhome_ensure_menu()`, `naigai_customhome_seed_nav_menus()`

### hub/inc/customhome-meta.php
- inferred part: `cta`
- functions: `naigai_is_customhome_target_page()`, `naigai_customhome_register_menu_location()`, `naigai_customhome_seed_menu()`, `naigai_customhome_defaults()`, `naigai_customhome_render_text_row()`, `naigai_customhome_get_media_preview_html()`, `naigai_customhome_render_media_row()`, `naigai_customhome_admin_ui_assets()`, `naigai_customhome_meta_box()`, `naigai_customhome_add_meta_box()`, `naigai_customhome_admin_enqueue_media()`, `naigai_customhome_save_meta_box()`, `naigai_customhome_subpage_common_is_target()`, `naigai_customhome_concept_is_target()`, `naigai_customhome_subpage_common_select_row()`, `naigai_customhome_subpage_common_checkbox_row()`, `naigai_customhome_subpage_common_add_meta_boxes()`, `naigai_customhome_subpage_cleanup_meta_boxes()`, `naigai_customhome_subpage_common_render_meta_box()`, `naigai_customhome_concept_render_meta_box()`, `naigai_customhome_subpage_common_save_meta_box()`, `naigai_customhome_concept_save_meta_box()`
- meta keys: `_ch_subpage_template`, `_hub_ch_cta_image_id`, `_hub_ch_cta_image_ids`, `_hub_ch_cta_image_items`, `_hub_ch_cta_media_items`, `_hub_ch_cta_swiper_autoplay`, `_hub_ch_cta_swiper_enabled`, `_hub_ch_cta_swiper_nav`, `_hub_ch_cta_swiper_pagination`, `_hub_ch_cta_video_controls`, `_hub_ch_cta_video_ids`, `_hub_ch_cta_video_items`
- classes: `naigai-ch-admin`, `naigai-ch-admin-card`, `naigai-ch-admin-card--full`, `naigai-ch-admin-card__title`, `naigai-ch-admin-cards`, `naigai-ch-admin-cta-grid`, `naigai-ch-admin-cta-item`, `naigai-ch-admin-cta-media-actions`, `naigai-ch-admin-cta-name`, `naigai-ch-admin-cta-thumb`, `naigai-ch-admin-section`, `naigai-ch-admin-section__title`, `naigai-ch-admin-split`, `naigai-ch-admin-topnav`, `naigai-ch-media-buttons`, `naigai-ch-media-clear`, `naigai-ch-media-controls`, `naigai-ch-media-empty`, `naigai-ch-media-field`, `naigai-ch-media-open`, `naigai-ch-media-preview`, `naigai-ch-media-preview-image`, `naigai-ch-media-preview-video`, `naigai-ch-media-url`

### hub/inc/customhome-phase2.php
- inferred part: `cta`
- functions: `naigai_customhome_phase2_is_target()`, `naigai_customhome_phase2_enqueue_front()`, `naigai_customhome_phase2_enqueue_admin()`, `naigai_customhome_phase2_hide_legacy_admin()`, `naigai_customhome_phase2_add_meta_box()`, `naigai_customhome_phase2_get_attachment_preview()`, `naigai_customhome_phase2_render_meta_box()`, `naigai_customhome_phase2_save()`
- meta keys: `_hub_ch_cta_media_items`, `_hub_ch_hero_company`, `_hub_ch_hero_company_position`, `_hub_ch_work_items`, `naigai_ch_cta_media_items[<?php echo (int) $index; ?>][attachment_id]`, `naigai_ch_cta_media_items[<?php echo (int) $index; ?>][type]`, `naigai_ch_cta_media_items[__INDEX__][attachment_id]`, `naigai_ch_cta_media_items[__INDEX__][type]`, `naigai_ch_work_items[<?php echo (int) $index; ?>][attachment_id]`, `naigai_ch_work_items[<?php echo (int) $index; ?>][text]`, `naigai_ch_work_items[<?php echo (int) $index; ?>][title]`, `naigai_ch_work_items[__INDEX__][attachment_id]`, `naigai_ch_work_items[__INDEX__][text]`, `naigai_ch_work_items[__INDEX__][title]`
- classes: `naigai-ch-phase2`, `naigai-ch-phase2__actions`, `naigai-ch-phase2__buttons`, `naigai-ch-phase2__desc`, `naigai-ch-phase2__field`, `naigai-ch-phase2__fields`, `naigai-ch-phase2__grid`, `naigai-ch-phase2__hero-grid`, `naigai-ch-phase2__item`, `naigai-ch-phase2__item-head`, `naigai-ch-phase2__item-template`, `naigai-ch-phase2__item-title`, `naigai-ch-phase2__list`, `naigai-ch-phase2__preview`, `naigai-ch-phase2__section`

### hub/inc/customhome-plan-tabs.php
- inferred part: `plan_layout`
- functions: `naigai_plan_tabs_is_target_page()`, `naigai_plan_tabs_add_meta_box()`, `naigai_plan_tabs_render_text_field()`, `naigai_plan_tabs_render_textarea_field()`, `naigai_plan_tabs_render_media_field()`, `naigai_plan_tabs_render_meta_box()`, `naigai_plan_tabs_save_meta_box()`
- meta keys: `_ch_plan_{$key}_area`, `_ch_plan_{$key}_build_area`, `_ch_plan_{$key}_desc`, `_ch_plan_{$key}_detail_url`, `_ch_plan_{$key}_hero`, `_ch_plan_{$key}_label`, `_ch_plan_{$key}_plan`, `_ch_plan_{$key}_point1`, `_ch_plan_{$key}_point2`, `_ch_plan_{$key}_point3`, `_ch_plan_{$key}_type`, `_ch_subpage_template`
- classes: `naigai-plan-media-button`, `naigai-plan-media-remove`

### hub/inc/customhome-section-builder.php
- inferred part: `section_builder`
- functions: `naigai_ch_builder_is_target()`, `naigai_ch_builder_sections_catalog()`, `naigai_ch_builder_default_sections()`, `naigai_ch_builder_get_sections()`, `naigai_ch_builder_box()`, `naigai_ch_builder_add_meta_box()`, `naigai_ch_builder_save()`, `naigai_ch_render_builder_faq()`, `naigai_ch_render_builder_sections()`, `naigai_ch_builder_shortcode()`, `naigai_ch_builder_mode_box()`, `naigai_ch_builder_mode_add_box()`, `naigai_ch_builder_mode_save()`
- meta keys: `_ch_builder_mode_enabled`, `_ch_builder_sections_json`, `_ch_faq_`, `_ch_subpage_template`
- classes: `ch-faq-answer`, `ch-faq-item`, `ch-section`, `ch-section--white`, `ch-shell`, `ch-subpage-faq-placeholder`, `ch-subpage-faq-section`, `naigai-ch-builder`, `naigai-ch-builder__list`, `naigai-ch-builder__toolbar`

### hub/inc/customhome-section-control-admin.php
- inferred part: `unknown`
- functions: `naigai_ch_section_control_is_target()`, `naigai_ch_section_control_current()`, `naigai_ch_section_control_map()`, `naigai_ch_section_control_box()`, `naigai_ch_section_control_add_box()`, `naigai_ch_section_control_save()`
- meta keys: `_ch_section_controls_json`, `_ch_subpage_template`, `naigai_ch_sections[<?php echo esc_attr($key); ?>][enabled]`, `naigai_ch_sections[<?php echo esc_attr($key); ?>][order]`
- classes: `naigai-admin-guide`, `naigai-section-order-row`, `naigai-section-order-row__check`, `naigai-section-order-row__desc`, `naigai-section-order-row__name`, `naigai-section-order-row__order`, `naigai-section-order-table`, `naigai-section-order-table__head`

### hub/inc/customhome-section-registry.php
- inferred part: `unknown`
- functions: `naigai_ch_section_registry()`, `naigai_ch_section_default_keys()`, `naigai_ch_get_section_controls()`, `naigai_ch_section_enabled()`
- meta keys: `_ch_section_controls_json`, `_ch_subpage_template`

### hub/inc/customhome-shortcode.php
- inferred part: `cta`
- functions: `naigai_customhome_page_matches()`, `naigai_customhome_request_matches()`, `naigai_customhome_shortcode_defaults()`, `naigai_customhome_meta_value()`, `naigai_customhome_attachment_url()`, `naigai_customhome_block_asset_needles()`, `naigai_customhome_enqueue_assets()`, `naigai_customhome_remove_registered_assets()`, `naigai_customhome_filter_output_assets()`, `naigai_customhome_start_buffer()`, `naigai_customhome_shortcode()`
- classes: `ch-btn`, `ch-btn--ghost`, `ch-btn--ghost-light`, `ch-btn--primary`, `ch-cta`, `ch-cta__actions`, `ch-cta__body`, `ch-cta__grid`, `ch-cta__image`, `ch-cta__media`, `ch-cta__text`, `ch-cta__title`, `ch-eyebrow`, `ch-eyebrow--light`, `ch-feature-card`, `ch-feature-card__text`, `ch-feature-card__title`, `ch-feature-grid`, `ch-flow-item`, `ch-flow-item__num`, `ch-flow-item__text`, `ch-flow-item__title`, `ch-flow-list`, `ch-fullbleed`, `ch-head`, `ch-hero`, `ch-hero__actions`, `ch-hero__content`, `ch-hero__image`, `ch-hero__inner`, `ch-hero__lead`, `ch-hero__media`, `ch-hero__overlay`, `ch-hero__title`, `ch-hero__video`, `ch-kicker`, `ch-localnav`, `ch-localnav__list`, `ch-section`, `ch-section--white`

### hub/inc/customhome-subpage-admin-cleanup.php
- inferred part: `customhome-subpage_body_admin-cleanup`
- functions: `naigai_ch_is_customhome_subpage_editor()`, `naigai_ch_remove_parent_metaboxes_for_subpage()`, `naigai_ch_subpage_admin_enqueue_style()`, `naigai_ch_admin_is_customhome_subpage()`, `naigai_ch_admin_is_contact_subpage()`, `naigai_ch_admin_organize_metaboxes()`
- meta keys: `_ch_subpage_template`

### hub/inc/customhome-subpage-controls.php
- inferred part: `cta`
- functions: `naigai_ch_is_iezukuri_child_page()`, `naigai_ch_get_iezukuri_page_options()`, `naigai_ch_subpage_controls_add_meta_boxes()`, `naigai_ch_subpage_cleanup_meta_boxes()`, `naigai_ch_subpage_display_controls_box()`, `naigai_ch_subpage_cta_text_input()`, `naigai_ch_subpage_cta_page_select()`, `naigai_ch_subpage_cta_background_box()`, `naigai_ch_subpage_controls_enqueue_media()`, `naigai_ch_subpage_controls_save()`
- meta keys: `_ch_show_customhome_cta`, `_ch_show_plan_tabs`, `_ch_subpage_template`, `_hub_ch_cta_image_ids`, `_hub_ch_cta_image_items`, `_hub_ch_cta_swiper_autoplay`, `_hub_ch_cta_swiper_enabled`, `_hub_ch_cta_swiper_nav`, `_hub_ch_cta_swiper_pagination`, `_hub_ch_cta_video_controls`, `_hub_ch_cta_video_ids`, `_hub_ch_cta_video_items`
- classes: `naigai-ch-sub-cta-preview`, `naigai-ch-sub-cta-preview__item`

### hub/inc/customhome-subpage-cta-fields.php
- inferred part: `cta`
- functions: `naigai_ch_subpage_cta_fields_target()`, `naigai_ch_subpage_cta_page_options()`, `naigai_ch_subpage_cta_text_row()`, `naigai_ch_subpage_cta_page_row()`, `naigai_ch_subpage_cta_box()`, `naigai_ch_subpage_cta_fields_add_box()`, `naigai_ch_subpage_cta_fields_enqueue()`, `naigai_ch_subpage_cta_fields_save()`
- meta keys: `_ch_show_customhome_cta`, `_hub_ch_cta_image_ids`, `_hub_ch_cta_image_items`, `_hub_ch_cta_swiper_autoplay`, `_hub_ch_cta_swiper_enabled`, `_hub_ch_cta_swiper_nav`, `_hub_ch_cta_swiper_pagination`, `_hub_ch_cta_video_controls`, `_hub_ch_cta_video_ids`, `_hub_ch_cta_video_items`
- classes: `naigai-ch-sub-cta-preview`, `naigai-ch-sub-cta-preview__item`

### hub/inc/customhome-subpage-layout-fields.php
- inferred part: `layout_fields`
- functions: `naigai_ch_is_subpage_layout_builder_target()`, `naigai_ch_layout_options()`, `naigai_ch_all_page_options()`, `naigai_ch_subpage_layout_fields_config()`, `naigai_ch_render_layout_field()`, `naigai_ch_subpage_layout_fields_box()`, `naigai_ch_subpage_layout_fields_add_meta_box()`, `naigai_ch_subpage_layout_fields_enqueue()`, `naigai_ch_subpage_layout_fields_save()`
- meta keys: `_ch_subpage_template`
- classes: `naigai-ch-layout-field-group`, `naigai-ch-layout-field-group-empty`, `naigai-ch-layout-image-preview`

### hub/pages/iezukuri/templates/nasu-house.php
- inferred part: `unknown`
- meta keys: `_ch_nasu_card1_image_id`, `_ch_nasu_card1_text`, `_ch_nasu_card1_title`, `_ch_nasu_card2_image_id`, `_ch_nasu_card2_text`, `_ch_nasu_card2_title`, `_ch_nasu_card3_image_id`, `_ch_nasu_card3_text`, `_ch_nasu_card3_title`, `_ch_nasu_cta_text`, `_ch_nasu_cta_title`, `_ch_nasu_feature_image_id`, `_ch_nasu_feature_text`, `_ch_nasu_feature_title`, `_ch_nasu_hero_image_id`, `_ch_nasu_intro_text`, `_ch_nasu_intro_title`, `_ch_nasu_point1_text`, `_ch_nasu_point1_title`, `_ch_nasu_point2_text`, `_ch_nasu_point2_title`, `_ch_nasu_point3_text`, `_ch_nasu_point3_title`, `_ch_nasu_point4_text`, `_ch_nasu_point4_title`
- classes: `ch-btn`, `ch-btn--ghost-light`, `ch-btn--primary`, `ch-eyebrow`, `ch-eyebrow--light`, `ch-hero`, `ch-hero__actions`, `ch-hero__content`, `ch-hero__image`, `ch-hero__inner`, `ch-hero__lead`, `ch-hero__media`, `ch-hero__overlay`, `ch-hero__title`, `ch-localnav`, `ch-localnav--underline`, `ch-localnav__list`, `ch-nasu-shot-card`, `ch-nasu-shot-card__icon`, `ch-nasu-shot-card__image`, `ch-nasu-shot-cards`, `ch-nasu-shot-cards-wrap`, `ch-nasu-shot-feature`, `ch-nasu-shot-feature__image`, `ch-nasu-shot-feature__text`, `ch-nasu-shot-intro`, `ch-nasu-shot-intro__header`, `ch-nasu-shot-page`, `ch-nasu-shot-section-title`, `ch-nasu-shot-value`, `ch-nasu-shot-value__icon`, `ch-nasu-shot-values`, `ch-nasu-shot-values__grid`, `ch-shell`, `ch-subpage-hero`, `ch-subpage-section`, `hub-customhome-page`, `hub-customhome-subpage`, `hub-customhome-subpage--<?php`

### hub/pages/iezukuri/templates/page-builder.php
- inferred part: `page_body_builder`

### hub/pages/iezukuri/templates/page-company.php
- inferred part: `company_access`
- meta keys: `_ch_company_address`, `_ch_company_map_iframe`, `_ch_company_map_text`, `_ch_company_map_title`
- classes: `ch-btn`, `ch-btn--primary`, `ch-company-map-address`, `ch-company-map-embed`, `ch-company-map-section`, `ch-company-page`, `ch-eyebrow`, `ch-intro-prose`, `ch-page-intro`, `ch-page-stack`, `ch-section-block`, `ch-section-head`, `ch-split`, `ch-split__body`, `ch-split__media`

### hub/pages/iezukuri/templates/page-concept.php
- inferred part: `page_body_concept`
- classes: `ch-btn`, `ch-btn--primary`, `ch-card-grid`, `ch-card-grid--3`, `ch-concept-page`, `ch-eyebrow`, `ch-intro-prose`, `ch-page-band`, `ch-page-band--sand`, `ch-page-band--white`, `ch-page-inner`, `ch-page-intro`, `ch-split`, `ch-split__body`, `ch-split__media`, `ch-surface-card`, `ch-surface-card__media`

### hub/pages/iezukuri/templates/page-contact.php
- inferred part: `contact`
- classes: `ch-card-grid`, `ch-card-grid--2`, `ch-contact-page`, `ch-eyebrow`, `ch-intro-prose`, `ch-page-intro`, `ch-page-stack`, `ch-section-block`, `ch-section-head`, `ch-surface-card`

### hub/pages/iezukuri/templates/page-design-office.php
- inferred part: `page_body_design-office`
- meta keys: `_ch_design_office_split_image_id`, `_ch_show_plan_tabs`
- classes: `ch-btn`, `ch-btn--primary`, `ch-design-office-page`, `ch-design-office-plan-section`, `ch-eyebrow`, `ch-intro-prose`, `ch-page-intro`, `ch-page-stack`, `ch-section-block`, `ch-section-head`, `ch-section-head--center`, `ch-split`, `ch-split__body`, `ch-split__media`

### hub/pages/iezukuri/templates/page-design-policy.php
- inferred part: `page_body_design-policy`
- classes: `ch-btn`, `ch-btn--primary`, `ch-card-grid`, `ch-card-grid--5`, `ch-design-policy-page`, `ch-eyebrow`, `ch-intro-prose`, `ch-page-intro`, `ch-page-stack`, `ch-section-head`, `ch-split`, `ch-split__body`, `ch-split__media`, `ch-step-card`, `ch-step-card__num`, `ch-surface-card`

### hub/pages/iezukuri/templates/page-nasu-house.php
- inferred part: `page_body_nasu-house`
- classes: `ch-btn`, `ch-btn--primary`, `ch-eyebrow`, `ch-nasu-shot-body`, `ch-nasu-shot-body__content`, `ch-nasu-shot-body__inner`, `ch-nasu-shot-card`, `ch-nasu-shot-card__icon`, `ch-nasu-shot-card__image`, `ch-nasu-shot-cards`, `ch-nasu-shot-cards-wrap`, `ch-nasu-shot-feature`, `ch-nasu-shot-feature__image`, `ch-nasu-shot-feature__text`, `ch-nasu-shot-intro`, `ch-nasu-shot-intro__header`, `ch-nasu-shot-page`, `ch-nasu-shot-section-title`, `ch-nasu-shot-value`, `ch-nasu-shot-value__icon`, `ch-nasu-shot-values`, `ch-nasu-shot-values__grid`

### hub/partials/customhome-line-icons.php
- inferred part: `unknown`
- functions: `naigai_ch_icon_svg()`

### hub/partials/front/post-template-copy.php
- inferred part: `unknown`
- meta keys: `BuildingArea`, `GoogleEmbedcode`, `LandArea`, `Location`, `NewBuildingArea`, `NewGoogleEmbedcode`, `NewLandArea`, `NewLocation`, `NewPrice`, `Price`, `_google_map_iframe_1`, `page_featured_type`, `page_video_id`, `sold-out`

### hub/partials/section-cta.php
- inferred part: `cta`
- classes: `hub-container`, `hub-cta-banner`, `hub-cta-banner__content`, `hub-cta-button`, `hub-cta-button--secondary`, `hub-cta-buttons`, `hub-heading`, `hub-kicker`, `hub-lead`, `hub-section`, `hub-section--cta`, `hub-section__inner`

### hub/partials/section-flow.php
- inferred part: `flow`
- classes: `hub-container`, `hub-flow-list`, `hub-flow-step`, `hub-flow-text`, `hub-flow-title`, `hub-heading`, `hub-lead`, `hub-section`, `hub-section--flow`, `hub-section-head`, `hub-section__inner`

### hub/partials/section-hero.php
- inferred part: `unknown`
- meta keys: `GoogleEmbedcode`, `NewGoogleEmbedcode`, `_front_slide_{$i}_enabled`, `_front_slide_{$i}_image_id`, `_front_slide_{$i}_label`, `_front_slide_{$i}_text`, `_front_slide_{$i}_title`, `_front_slide_{$i}_url`, `_front_slide_{$i}_video_id`, `_front_slide_{$i}_video_type`, `_google_map_iframe_1`, `_hub_feature_post_id`, `_hub_hero_video_id`, `_wp_attachment_image_alt`, `page_featured_type`, `page_video_id`
- classes: `hub-btn`, `hub-container`, `hub-front-hero`, `hub-front-hero__facts`, `hub-front-hero__facts-shell`, `hub-front-hero__intro`, `hub-front-hero__kicker`, `hub-front-hero__lead`, `hub-front-hero__map-action`, `hub-front-hero__summary`, `hub-front-hero__title`, `hub-front-media__nav`, `hub-front-media__nav--next`, `hub-front-media__nav--prev`, `hub-hero-section<?php`, `hub-hero__body`, `hub-hero__grid<?php`, `hub-hero__img`, `hub-hero__lite-vimeo`, `hub-hero__lite-youtube`, `hub-hero__media`, `hub-hero__media--slider`, `hub-hero__media<?php`, `hub-hero__slide-card<?php`, `hub-hero__slide-info`, `hub-hero__slide-link`, `hub-hero__slide-media`, `hub-hero__slide-title`, `hub-kicker`, `hub-lead`, `hub-section`, `hub-section__inner`, `hub-title`

### hub/partials/section-intro-hero.php
- inferred part: `unknown`
- classes: `hub-btn`, `hub-hero__inner`, `hub-intro-hero`, `hub-intro-hero__actions`, `hub-intro-hero__body`, `hub-intro-hero__inner`, `hub-intro-hero__lead`, `hub-intro-hero__media`, `hub-intro-hero__nav`, `hub-intro-hero__nav-link`, `hub-intro-hero__panel`, `hub-intro-hero__panel-label`, `hub-intro-hero__panel-text`, `hub-intro-hero__panel-title`, `hub-intro-hero__side`, `hub-intro-hero__title`, `hub-kicker`, `hub-section`, `hub-section--construction-intro-hero`, `hub-section--intro-hero`

### hub/partials/section-links.php
- inferred part: `unknown`
- classes: `hub-card__img`, `hub-card__media`, `hub-card__media--slider`, `hub-container`, `hub-heading`, `hub-link-item`, `hub-link-item--card<?php`, `hub-link-item__action`, `hub-link-item__body`, `hub-link-item__cta`, `hub-link-item__cta-arrow`, `hub-link-item__cta-label`, `hub-link-item__main`, `hub-link-item__text`, `hub-link-item__title`, `hub-link-list`, `hub-section`, `hub-section__inner`

### hub/partials/section-plan-tabs.php
- inferred part: `plan_layout`
- classes: `ch-btn`, `ch-btn--primary`, `ch-eyebrow`, `ch-head`, `ch-plan-tabs`, `ch-plan-tabs__box`, `ch-plan-tabs__detail`, `ch-plan-tabs__hero-image`, `ch-plan-tabs__lead`, `ch-plan-tabs__media`, `ch-plan-tabs__nav`, `ch-plan-tabs__panel`, `ch-plan-tabs__panel--<?php`, `ch-plan-tabs__panel-grid`, `ch-plan-tabs__panels`, `ch-plan-tabs__plan-image`, `ch-plan-tabs__points`, `ch-plan-tabs__radio`, `ch-plan-tabs__stats`, `ch-plan-tabs__summary-badge`, `ch-plan-tabs__summary-card`, `ch-plan-tabs__summary-head`, `ch-plan-tabs__tab`, `ch-plan-tabs__tab-badge`, `ch-plan-tabs__tab-type`, `ch-plan-tabs__text`, `ch-plan-tabs__thumb`, `ch-plan-tabs__thumb-meta`, `ch-plan-tabs__thumbs`, `ch-section`, `ch-section--white`, `ch-section-title`, `ch-shell`

### hub/partials/section-support-grid.php
- inferred part: `unknown`
- classes: `hub-container`, `hub-heading`, `hub-lead`, `hub-section`, `hub-section--support`, `hub-section-head`, `hub-section__inner`, `hub-support-card`, `hub-support-card__label`, `hub-support-card__text`, `hub-support-card__title`, `hub-support-grid`

### hub/templates/construction.php
- inferred part: `cta`
- meta keys: `_hub_ch_brand_logo_id`, `_hub_ch_brand_subtext`, `_hub_ch_brand_text`, `_hub_ch_cta_image_id`, `_hub_ch_cta_media_items`, `_hub_ch_cta_secondary_override_label`, `_hub_ch_cta_secondary_override_page_id`, `_hub_ch_cta_secondary_override_url`, `_hub_ch_cta_swiper_autoplay`, `_hub_ch_cta_swiper_enabled`, `_hub_ch_cta_swiper_nav`, `_hub_ch_cta_swiper_pagination`, `_hub_ch_cta_video_controls`, `_hub_ch_header_menu_style`, `_hub_ch_hero_company`, `_hub_ch_hero_video_mp4_id`, `_hub_ch_hero_video_poster_id`, `_hub_ch_hero_video_webm_id`, `_hub_ch_localnav_mode`, `_hub_ch_page_menu_style`, `_hub_ch_work_items`, `_hub_ch_work_{$i}_image_id`
- classes: `ch-btn`, `ch-btn--ghost`, `ch-btn--ghost-light`, `ch-btn--primary`, `ch-cta`, `ch-cta-swiper`, `ch-cta__actions`, `ch-cta__body`, `ch-cta__grid`, `ch-cta__image`, `ch-cta__media`, `ch-cta__text`, `ch-cta__title`, `ch-cta__video`, `ch-eyebrow`, `ch-eyebrow--light`, `ch-feature-card`, `ch-feature-card__icon`, `ch-feature-card__text`, `ch-feature-card__title`, `ch-feature-grid`, `ch-flow-item`, `ch-flow-item__icon`, `ch-flow-item__num`, `ch-flow-item__text`, `ch-flow-item__title`, `ch-flow-list`, `ch-fullbleed`, `ch-head`, `ch-head--with-link`, `ch-hero`, `ch-hero__actions`, `ch-hero__brand-logo`, `ch-hero__brand-logo-wrap`, `ch-hero__company`, `ch-hero__company-sub`, `ch-hero__content`, `ch-hero__image`, `ch-hero__inner`, `ch-hero__lead`

### hub/templates/front.php
- inferred part: `unknown`

### hub/templates/realestate.php
- inferred part: `unknown`
- functions: `naigai_fudo_page_meta()`, `naigai_fudo_clean_media_ids()`, `naigai_fudo_youtube_id_from_value()`, `naigai_fudo_render_hero_media()`, `naigai_fudo_meta_first()`, `naigai_fudo_numeric_value()`, `naigai_fudo_has_term_name_like()`, `naigai_fudo_is_sold()`, `naigai_fudo_get_price_raw()`, `naigai_fudo_format_price_label()`, `naigai_fudo_area_label()`, `naigai_fudo_render_card_badges()`, `naigai_fudo_render_card_facts()`, `naigai_fudo_get_map_html()`, `naigai_fudo_render_map_button()`, `naigai_fudo_get_customizer_categories()`, `naigai_fudo_get_post_term_classes()`, `naigai_fudo_render_card_media()`, `naigai_fudo_get_card_thumb_url()`, `naigai_fudo_icon()`, `naigai_fudo_get_card_data()`, `naigai_fudo_filter_class()`
- meta keys: `BuildingArea`, `LandArea`, `Location`, `Price`, `_fudo_hero_image_ids`, `_fudo_hero_mp4_id`, `_fudo_hero_youtube_id`, `_google_map_iframe_1`, `page_featured_type`, `page_video_id`, `sold-out`
- classes: `hub-page`, `hub-page-realestate`

### page-construction-hub-sub.php
- inferred part: `subpage_shell`
- meta keys: `_ch_back_url`, `_ch_common_form_shortcode`, `_ch_contact_url`, `_ch_faq_`, `_ch_hero_image_id`, `_ch_hero_kicker`, `_ch_hero_lead`, `_ch_hero_primary_label`, `_ch_hero_secondary_label`, `_ch_hero_title`, `_ch_layout_mode`, `_ch_show_common_faq`, `_ch_show_common_form`, `_ch_show_customhome_cta`, `_ch_subpage_template`, `_ch_use_parent_flow`, `_ch_use_parent_works`
- classes: `ch-btn`, `ch-btn--ghost`, `ch-btn--primary`, `ch-faq-answer`, `ch-faq-item`, `ch-fullbleed`, `ch-hero`, `ch-hero__actions`, `ch-hero__content`, `ch-hero__image`, `ch-hero__inner`, `ch-hero__lead`, `ch-hero__media`, `ch-hero__overlay`, `ch-hero__title`, `ch-kicker`, `ch-localnav`, `ch-localnav--construction`, `ch-localnav__list`, `ch-section`, `ch-section--white`, `ch-shell`, `ch-subpage-faq-placeholder`, `ch-subpage-faq-section`, `ch-subpage-form-placeholder`, `ch-subpage-form-section`, `ch-subpage-hero`, `ch-subpage-main`, `ch-subpage-main--fallback`, `ch-subpage-section`, `hub-customhome-page`, `hub-customhome-subpage`, `hub-customhome-subpage--<?php`
