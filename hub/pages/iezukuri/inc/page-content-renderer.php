<?php
/**
 * ============================================================
 * 家づくり サブページ共通
 * サブコンテンツ renderer
 * 各コンテンツ個別レイアウト版
 * ============================================================
 *
 * 管理画面:
 *
 *     導入キッカー
 *     導入見出し
 *     導入本文
 *
 *     コンテンツ1
 *         画像
 *         見出し
 *         文章
 *         配置
 *
 *     コンテンツ2
 *         画像
 *         見出し
 *         文章
 *         配置
 *
 *
 * ============================================================
 * 保存
 * ============================================================
 *
 * _ch_content_items
 *
 * 各項目:
 *
 *     layout
 *     image_id
 *     title
 *     text
 *
 *
 * layout:
 *
 *     card
 *         画像上＋文章下
 *
 *     image-left
 *         画像左＋文章右
 *
 *     image-right
 *         文章左＋画像右
 *
 *
 * ============================================================
 * カード
 * ============================================================
 *
 * cardが連続した場合は、
 * そのcard同士だけを横並びグリッドにする。
 *
 * その前後に
 *
 *     image-left
 *     image-right
 *
 * があっても問題ない。
 *
 *
 * ============================================================
 * モバイル
 * ============================================================
 *
 * 全レイアウトを
 *
 *     画像
 *      ↓
 *     文章
 *
 * の1列にする。
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}


if (
    !function_exists(
        'naigai_iezukuri_render_page_content_from_meta'
    )
) {

    function naigai_iezukuri_render_page_content_from_meta(
        $post_id = 0
    ) {

        $post_id =
            $post_id
                ? absint($post_id)
                : get_queried_object_id();


        if (!$post_id) {
            return false;
        }


        /*
         * ====================================================
         * 導入
         * ====================================================
         */
        $intro_kicker =
            trim(
                (string) get_post_meta(
                    $post_id,
                    '_ch_intro_kicker',
                    true
                )
            );


        $intro_title =
            trim(
                (string) get_post_meta(
                    $post_id,
                    '_ch_intro_title',
                    true
                )
            );


        $intro_text =
            trim(
                (string) get_post_meta(
                    $post_id,
                    '_ch_intro_text',
                    true
                )
            );


        /*
         * H1と導入見出しが完全一致する場合は、
         * H1だけを残す。
         */
        $page_title =
            trim(
                wp_strip_all_tags(
                    (string) get_the_title(
                        $post_id
                    )
                )
            );


        if (
            $intro_title !== ''
            && $page_title !== ''
            && wp_strip_all_tags(
                $intro_title
            ) === $page_title
        ) {

            $intro_title =
                '';
        }


        /*
         * ====================================================
         * 旧全体レイアウト
         * ====================================================
         *
         * まだ個別layoutを保存していない
         * 旧データの互換だけに使う。
         */
        $legacy_layout =
            sanitize_key(
                (string) get_post_meta(
                    $post_id,
                    '_ch_content_layout',
                    true
                )
            );


        /*
         * ====================================================
         * コンテンツ一覧
         * ====================================================
         */
        $raw_items =
            get_post_meta(
                $post_id,
                '_ch_content_items',
                true
            );


        $items =
            array();


        $allowed_layouts =
            array(
                'card',
                'image-left',
                'image-right',
            );


        if (is_array($raw_items)) {

            foreach (
                $raw_items
                as $index => $raw_item
            ) {

                if (
                    !is_array(
                        $raw_item
                    )
                ) {
                    continue;
                }


                $layout =
                    sanitize_key(
                        (string) (
                            $raw_item['layout']
                            ?? ''
                        )
                    );


                /*
                 * =============================================
                 * 旧データ互換
                 * =============================================
                 *
                 * 各itemにlayoutがまだ存在しない場合だけ
                 * 以前の全体レイアウトを変換して使用する。
                 */
                if (
                    !in_array(
                        $layout,
                        $allowed_layouts,
                        true
                    )
                ) {

                    if (
                        $legacy_layout === 'cards'
                    ) {

                        $layout =
                            'card';

                    } elseif (
                        $legacy_layout === 'image-right'
                    ) {

                        $layout =
                            'image-right';

                    } elseif (
                        $legacy_layout === 'alternate'
                    ) {

                        $layout =
                            ($index % 2 === 0)
                                ? 'image-left'
                                : 'image-right';

                    } else {

                        $layout =
                            'image-left';
                    }
                }


                $item =
                    array(

                        'layout' =>
                            $layout,

                        'image_id' =>
                            absint(
                                $raw_item['image_id']
                                ?? 0
                            ),

                        'title' =>
                            trim(
                                sanitize_text_field(
                                    (string) (
                                        $raw_item['title']
                                        ?? ''
                                    )
                                )
                            ),

                        'text' =>
                            trim(
                                sanitize_textarea_field(
                                    (string) (
                                        $raw_item['text']
                                        ?? ''
                                    )
                                )
                            ),
                    );


                if (
                    !$item['image_id']
                    && $item['title'] === ''
                    && $item['text'] === ''
                ) {
                    continue;
                }


                $items[] =
                    $item;
            }
        }


        /*
         * 全部空ならsectionを出さない。
         */
        if (
            $intro_kicker === ''
            && $intro_title === ''
            && $intro_text === ''
            && !$items
        ) {

            return false;
        }


        /*
         * ====================================================
         * 1件分のHTMLを出す共通処理
         * ====================================================
         *
         * card / image-left / image-right
         * でHTMLそのものを複製しないための処理。
         */
        $render_item =
            static function ($item) {

                $layout =
                    $item['layout'];

                $has_media =
                    !empty(
                        $item['image_id']
                    );

                $has_copy =
                    $item['title'] !== ''
                    || $item['text'] !== '';

                ?>

                <article
                    class="
                        ch-sub-content__item
                        ch-sub-content__item--<?php echo esc_attr($layout); ?>
                        <?php echo $has_media ? 'has-media' : 'no-media'; ?>
                        <?php echo $has_copy ? 'has-copy' : 'no-copy'; ?>
                    "
                    data-iez-content-item
                    data-iez-item-layout="<?php echo esc_attr($layout); ?>"
                >


                    <?php if ($has_media) : ?>

                        <figure
                            class="ch-sub-content__media"
                        >

                            <?php
                            echo wp_get_attachment_image(
                                $item['image_id'],
                                'large',
                                false,
                                array(
                                    'loading' =>
                                        'lazy',

                                    'class' =>
                                        'ch-sub-content__image',
                                )
                            );
                            ?>

                        </figure>

                    <?php endif; ?>


                    <?php if ($has_copy) : ?>

                        <div
                            class="ch-sub-content__copy"
                        >


                            <?php
                            if (
                                $item['title'] !== ''
                            ) :
                            ?>

                                <h3
                                    class="ch-sub-content__item-title"
                                >
                                    <?php
                                    echo esc_html(
                                        $item['title']
                                    );
                                    ?>
                                </h3>

                            <?php endif; ?>


                            <?php
                            if (
                                $item['text'] !== ''
                            ) :
                            ?>

                                <div
                                    class="ch-sub-content__item-text"
                                >

                                    <?php
                                    echo wpautop(
                                        esc_html(
                                            $item['text']
                                        )
                                    );
                                    ?>

                                </div>

                            <?php endif; ?>


                        </div>

                    <?php endif; ?>


                </article>

                <?php
            };


        ?>

        <section
            class="
                ch-subpage-section
                ch-sub-content
            "
            data-iez-sub-content
        >

            <div class="ch-shell">


                <?php
                /*
                 * =============================================
                 * 導入
                 * =============================================
                 */
                if (
                    $intro_kicker !== ''
                    || $intro_title !== ''
                    || $intro_text !== ''
                ) :
                ?>

                    <div
                        class="ch-sub-content__intro"
                    >


                        <?php
                        if (
                            $intro_kicker !== ''
                        ) :
                        ?>

                            <p
                                class="ch-sub-content__kicker"
                            >
                                <?php
                                echo esc_html(
                                    $intro_kicker
                                );
                                ?>
                            </p>

                        <?php endif; ?>


                        <?php
                        if (
                            $intro_title !== ''
                        ) :
                        ?>

                            <h2
                                class="ch-sub-content__title"
                            >
                                <?php
                                echo esc_html(
                                    $intro_title
                                );
                                ?>
                            </h2>

                        <?php endif; ?>


                        <?php
                        if (
                            $intro_text !== ''
                        ) :
                        ?>

                            <div
                                class="ch-sub-content__lead"
                            >

                                <?php
                                echo wpautop(
                                    esc_html(
                                        $intro_text
                                    )
                                );
                                ?>

                            </div>

                        <?php endif; ?>


                    </div>

                <?php endif; ?>


                <?php if ($items) : ?>

                    <div
                        class="ch-sub-content__items"
                        data-iez-content-items
                    >

                        <?php

                        /*
                         * =========================================
                         * カードグループ制御
                         * =========================================
                         *
                         * cardが連続した場合:
                         *
                         *     card
                         *     card
                         *     card
                         *
                         * を同じ
                         *
                         *     .ch-sub-content__card-row
                         *
                         * で囲む。
                         *
                         * image-left / image-right が来たら
                         * card-rowを一度閉じる。
                         */

                        $card_row_open =
                            false;


                        foreach (
                            $items
                            as $item
                        ) {


                            if (
                                $item['layout']
                                === 'card'
                            ) {

                                if (
                                    !$card_row_open
                                ) {

                                    echo
                                        '<div class="ch-sub-content__card-row">';

                                    $card_row_open =
                                        true;
                                }


                                $render_item(
                                    $item
                                );


                                continue;
                            }


                            /*
                             * cardの連続が終了したので閉じる。
                             */
                            if (
                                $card_row_open
                            ) {

                                echo '</div>';

                                $card_row_open =
                                    false;
                            }


                            /*
                             * 画像左 / 画像右は
                             * 1段を丸ごと使用する。
                             */
                            $render_item(
                                $item
                            );
                        }


                        /*
                         * 最後がcardだった場合の閉じタグ。
                         */
                        if (
                            $card_row_open
                        ) {

                            echo '</div>';
                        }

                        ?>

                    </div>

                <?php endif; ?>


            </div>

        </section>

        <?php

        return true;
    }
}
