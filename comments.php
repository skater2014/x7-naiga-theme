<?php
/****************************************

	comments.php

	コメント一覧や、コメントフォームを表示するための
	テンプレートファイルです。

	カスタマイズしたcomments.php のコードに関しては、
	CHAPTER 19,20 で詳しく解説しています。

*****************************************/
?>
<!-- comments.php -->
<div id="comment-area" class="no-comment-css">
	<?php if ( have_comments() ): /** コメントがあったら */ ?>
		<h3 id="comments">Comment</h3>
		<?php /** コメント一覧の表示 CHAPTER 19 */
		$args = array(
			'type'		=> 'comment',			/** コメントのタイプを comment のみに指定 */
			'callback'	=> 'my_comment_list',	/** my_comment_list()関数は、functions.php に記述 */
		); ?>
		<ol class="comments-list" id="custom-comments">
			<?php wp_list_comments( $args ); ?>
		</ol>
		<?php /** コメントページャーの表示 */
		if ( $wp_query->max_num_comment_pages > 1 ) : ?>
			<div class="comment-page-link">
				<?php $args = array(
					'prev_text' => '&laquo; Prev',
					'next_text' => 'Next &raquo;',
				);
				paginate_comments_links( $args ); ?>
			</div>
		<?php endif;

		/** ここからピンバックを表示 */
		$str  = '<h3 id="trackbacks">Trackback</h3>';
		$str .= '<ol class="trackback-list" id="custom-trackback">';
		$i = 0;
		foreach ( $comments as $comment ) {
			if ( get_comment_type() != 'comment' ) {
				$str .= '<li class="clearfix" id="comment-' . get_comment_ID() . '">';
					$str .= '<div class="trackback-author">';
						$str .= '<p class="comment-author-name">';
						$comment_author_url = $comment->comment_author_url;
						$comment_author = $comment->comment_author;
						if ( $comment_author_url ) {
							$str .= '<a href="' . esc_url( $comment_author_url ) . '" target="_blank" title="' . esc_attr( $comment_author ) . '">';
							$str .= esc_html( $comment_author );
							$str .= '</a>';
						} else {
							$str .= esc_html( $comment_author );
						}
						$str .= '</p>';
						$str .= '<p class="comment-meta">' . '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . get_comment_date() .'<span>'. get_comment_time() . '</span><a class="edit" href="' . get_edit_comment_link() . '">（編集）</a></span></p>';
					$str .='</div>';
					$str .= '<div class="trackback-body">';
						if ( $comment->comment_approved == '0' ) {
							$str .= '<p class="attention"><em>あなたのトラックバックは承認待ちです。</em></p>';
						}
						$str .= '<p>' . esc_html( get_comment_text() ) . '</p>';
					$str .= '</div>';
				$str .= '</li>';
				$i++;
			}
		}
		$str .= '</ol>';
		if ( $i > 0 ) {
			echo $str;
		}	/** ピンバックを表示 ここまで */

	endif; /** if ( have_comments() ): ここまで */
	/** ここからコメントフォーム CHAPTER 20 */
	if (comments_open()):
		comment_form();
	else: ?>
		<p>現在コメントは受け付けておりません。</p>
	<?php endif;
	if ( pings_open() ) : ?>
		<h3 id="trackback-url">Trackback URL</h3>
		<p><input id="trackback_url" readonly="readonly" value=" <?php trackback_url(true); ?>" type="text" onclick = "this.select();" /></p>
	<?php else : ?>
		<p>現在トラックバックは受け付けておりません。</p>
	<?php endif; ?>
</div>
<!-- /comments.php -->