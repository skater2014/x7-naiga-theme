
<?php
function bread3_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment-wrap">
            <div class="comment-meta">
                <div class="comment-author vcard">
                    <?php
                    if ( $args['avatar_size'] != 0 ) {
                        echo get_avatar( $comment, $args['avatar_size'] );
                    }
                    ?>
                    <?php printf( __( '<cite class="fn">%s</cite>', 'xiaoyu' ), get_comment_author_link() ); ?>
                </div>
                <div class="comment-metadata">
                    <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                        <?php
                        /* translators: 1: date, 2: time */
                        printf( esc_html__( '%1$s at %2$s', 'xiaoyu' ), get_comment_date(), get_comment_time() );
                        ?>
                    </a>
                    <?php
                    edit_comment_link( esc_html__( '(Edit)', 'xiaoyu' ), '  ', '' );
                    ?>
                </div>
            </div>
            <?php if ( '0' == $comment->comment_approved ) : ?>
                <em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'xiaoyu' ); ?></em>
                <br />
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <div class="reply">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'depth' => $depth,
                            'max_depth' => $args['max_depth'],
                        )
                    )
                );
                ?>
            </div>

            <?php
            $output = '';
            $comments_by_type = separate_comments( $comments );
            if ( isset( $comments_by_type['pings'] ) ) {
                $ping_count = count( $comments_by_type['pings'] );
                $output    .= sprintf( _n( 'One ping', '%s pings', $ping_count, 'xiaoyu' ), $ping_count ) . ' ';
            }

            if ( '0' != $args['depth'] ) {
                $output .= '<div class="comment-author-url">';  // 修正行：開始タグを追加
                // Modified code starts here (修正されたコードがここから始まります)
                if ( isset( $comment->comment_author_url ) && $comment->comment_author_url != '' ) {
                    $output .= '<a href="' . esc_url( $comment->comment_author_url ) . '" rel="external nofollow" class="url">' . esc_html( $comment->comment_author_url ) . '</a>';
                } else {
                    $output .= esc_html( $comment->comment_author_url );  // 追加行：URLが未定義の場合にエスケープして表示
                }
                // Modified code ends here (修正されたコードがここで終わります)
                $output .= '</div>';  // 修正行：終了タグを追加
            }

            $output .= '<span class="comment-reply">';
            $output .= get_comment_reply_link(
                array_merge(
                    $args,
                    array(
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'],
                    )
                )
            );
            $output .= '</span>';
            echo wp_kses_post( $output );
            ?>
        </div>
    </li>
    <?php
}
