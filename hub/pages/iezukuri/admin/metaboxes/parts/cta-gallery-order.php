<?php

/**
 * CTA Swiper画像の並び順修正。
 *
 * 管理画面で選択した順番を、
 * hidden値・プレビュー・Swiper表示順で統一する。
 */

if (!defined('ABSPATH')) {
    return;
}

if (!function_exists('naigai_iez_admin_cta_gallery_order_script')) {

    function naigai_iez_admin_cta_gallery_order_script()
    {
        ?>
        <script>
        (function () {
            'use strict';

            function initCtaGalleryOrder() {

                const input =
                    document.querySelector(
                        'input[name="_hub_ch_cta_gallery_ids"]'
                    );

                if (
                    !input ||
                    !window.wp ||
                    !wp.media
                ) {
                    return;
                }

                const cell = input.closest('td');

                if (!cell) {
                    return;
                }

                const getLabel = function (element) {
                    return (
                        element.textContent ||
                        element.value ||
                        ''
                    ).trim();
                };

                const buttons =
                    Array.from(
                        cell.querySelectorAll(
                            'button, input[type="button"], a.button'
                        )
                    );

                const selectButton =
                    buttons.find(function (element) {
                        return getLabel(element)
                            .includes('画像を複数選択');
                    });

                const clearButton =
                    buttons.find(function (element) {
                        return getLabel(element)
                            .includes('クリア');
                    });

                if (!selectButton) {
                    return;
                }

                /*
                 * この修正専用のプレビュー。
                 * hidden値の順番をそのまま左→右へ表示する。
                 */
                let preview =
                    cell.querySelector(
                        '[data-naigai-cta-order-preview]'
                    );

                if (!preview) {

                    preview =
                        document.createElement('div');

                    preview.setAttribute(
                        'data-naigai-cta-order-preview',
                        '1'
                    );

                    preview.style.display = 'flex';
                    preview.style.flexWrap = 'wrap';
                    preview.style.gap = '12px';
                    preview.style.marginTop = '12px';

                    cell.appendChild(preview);
                }

                function currentIds() {

                    return input.value
                        .split(',')
                        .map(function (value) {
                            return parseInt(
                                value.trim(),
                                10
                            );
                        })
                        .filter(Boolean);
                }

                /*
                 * 旧プレビューを消す。
                 * 「ID: 123」のカードだけを対象にする。
                 */
                function removeOldPreview() {

                    Array.from(
                        cell.querySelectorAll('img')
                    ).forEach(function (image) {

                        if (
                            image.closest(
                                '[data-naigai-cta-order-preview]'
                            )
                        ) {
                            return;
                        }

                        let node = image;

                        while (
                            node.parentElement &&
                            node.parentElement !== cell
                        ) {

                            const parent =
                                node.parentElement;

                            if (
                                /ID:\s*\d+/.test(
                                    parent.textContent || ''
                                )
                            ) {
                                parent.remove();
                                break;
                            }

                            node = parent;
                        }
                    });
                }

                function render(order) {

                    removeOldPreview();

                    preview.innerHTML = '';

                    order.forEach(function (id) {

                        const card =
                            document.createElement('div');

                        card.style.width = '180px';
                        card.style.border =
                            '1px solid #ccd0d4';
                        card.style.borderRadius = '6px';
                        card.style.overflow = 'hidden';
                        card.style.background = '#fff';

                        const image =
                            document.createElement('img');

                        image.style.width = '100%';
                        image.style.height = '125px';
                        image.style.objectFit = 'cover';
                        image.style.display = 'block';

                        const idLabel =
                            document.createElement('div');

                        idLabel.textContent =
                            'ID: ' + id;

                        idLabel.style.padding =
                            '5px 8px';

                        card.appendChild(image);
                        card.appendChild(idLabel);

                        preview.appendChild(card);

                        const attachment =
                            wp.media.attachment(id);

                        attachment
                            .fetch()
                            .then(function () {

                                const data =
                                    attachment.toJSON();

                                let source =
                                    data.url || '';

                                if (
                                    data.sizes &&
                                    data.sizes.thumbnail
                                ) {
                                    source =
                                        data.sizes.thumbnail.url;
                                }

                                image.src = source;
                            });
                    });
                }

                /*
                 * 選択順をhidden値へそのまま保存する。
                 */
                function writeOrder(order) {

                    input.value =
                        order.join(',');

                    input.dispatchEvent(
                        new Event(
                            'input',
                            { bubbles: true }
                        )
                    );

                    input.dispatchEvent(
                        new Event(
                            'change',
                            { bubbles: true }
                        )
                    );

                    render(order);
                }

                let frame = null;

                /*
                 * 既存の複数選択処理をこのCTAだけ置き換える。
                 */
                selectButton.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();

                        if (!frame) {

                            frame = wp.media({
                                title:
                                    'CTA Swiper画像',
                                button: {
                                    text:
                                        'この順番で使用'
                                },
                                library: {
                                    type: 'image'
                                },
                                multiple: true
                            });

                            frame.on(
                                'open',
                                function () {

                                    const selection =
                                        frame
                                            .state()
                                            .get(
                                                'selection'
                                            );

                                    selection.reset();

                                    currentIds()
                                        .forEach(
                                            function (id) {

                                                selection.add(
                                                    wp.media
                                                        .attachment(
                                                            id
                                                        )
                                                );
                                            }
                                        );
                                }
                            );

                            frame.on(
                                'select',
                                function () {

                                    const selection =
                                        frame
                                            .state()
                                            .get(
                                                'selection'
                                            );

                                    const order =
                                        selection
                                            .map(
                                                function (
                                                    attachment
                                                ) {
                                                    return parseInt(
                                                        attachment.id,
                                                        10
                                                    );
                                                }
                                            )
                                            .filter(Boolean);

                                    writeOrder(order);
                                }
                            );
                        }

                        frame.open();
                    },
                    true
                );

                if (clearButton) {

                    clearButton.addEventListener(
                        'click',
                        function (event) {

                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();

                            writeOrder([]);
                        },
                        true
                    );
                }

                /*
                 * 初期表示もhidden値の順番に統一する。
                 */
                render(
                    currentIds()
                );
            }

            if (
                document.readyState ===
                'loading'
            ) {
                document.addEventListener(
                    'DOMContentLoaded',
                    initCtaGalleryOrder
                );
            } else {
                initCtaGalleryOrder();
            }

        }());
        </script>
        <?php
    }
}

add_action(
    'admin_footer-post.php',
    'naigai_iez_admin_cta_gallery_order_script',
    100
);

add_action(
    'admin_footer-post-new.php',
    'naigai_iez_admin_cta_gallery_order_script',
    100
);
