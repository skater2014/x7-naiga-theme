<?php

/**
 * blog-feature-badges.php
 *
 * blog 投稿に紐づいた「ブログジャンル（blog_genre）」タームを
 * バッジ形式で表示するテンプレートパーツ。
 *
 * このファイルでは、
 * - 現在の投稿に付いている blog_genre タームを取得
 * - 先頭から最大6件だけを表示
 * - 各バッジをターム一覧ページへのリンクにする
 *
 * という処理を行う。
 */

// 現在の投稿に付いている blog_genre タームを取得
$genre_terms = get_the_terms(get_the_ID(), 'blog_genre');

// タームが存在し、かつエラーでない場合のみ表示
if ($genre_terms && !is_wp_error($genre_terms)) : ?>
    <div class="blog-feature-badges">
        <?php
        /**
         * array_slice($genre_terms, 0, 6)
         *
         * 先頭から最大6件だけ表示する。
         *
         * 理由:
         * - タームを付けすぎた場合でも一覧カードの見た目を崩しにくくするため
         * - バッジが増えすぎるとタイトルや抜粋より目立ってしまうため
         * - 一覧画面では情報量を絞り、詳細は個別ページやターム一覧で補うため
         *
         * もし件数を増やしたい場合は、最後の「6」を変更する。
         * 例:
         * - 4件にする → array_slice($genre_terms, 0, 4)
         * - 8件にする → array_slice($genre_terms, 0, 8)
         */
        foreach (array_slice($genre_terms, 0, 6) as $genre_term) : ?>
            <a href="<?php echo esc_url(get_term_link($genre_term)); ?>" class="blog-feature-badge">
                <?php echo esc_html($genre_term->name); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>