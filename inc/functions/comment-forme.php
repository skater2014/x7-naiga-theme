<?php
/** コメントフォームをカスタマイズ（CHAPTER 20）*/
add_filter( 'comment_form_default_fields', 'comment_form_custom_fields' );

/** お名前、メールアドレス、Webサイト部のマークアップ（CHAPTER 20） */
function comment_form_custom_fields( $fields ) {
  $commenter  = wp_get_current_commenter();
  $req    = get_option( 'require_name_email' );
  $aria_req   = ( $req ? " aria-required='true' required" : '' ); // 必須項目に "required" を追加
  
  /** 名前の項目 */
  $fields['author']   = '<p class="comment-form-author"><label for="author">お名前</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
  
  /** メールアドレスの項目 */
  $fields['email']  = '<p class="comment-form-email"><label for="email">メールアドレス</label> ' . ( $req ? '<span class="required">*</span> <span class="small">（メールアドレスは公開されません）</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
  
  /** ウェブサイトの項目 */
  $fields['url']    = ''; // URLフィールドを無効化
  
  return $fields;
}

/** コメントフォームのラベルをカスタマイズ（CHAPTER 20） */
add_filter( 'comment_form_defaults', 'comment_form_custom' );

function comment_form_custom( $form ) {
  global $user_identity, $post;
  $req      = get_option( 'require_name_email' );
  $required_text  = '<span class="required">*</span> が付いている項目は、必須項目です！';
  
  /** コメントフォーム textarea */
  $form['comment_field']      =  '<p class="comment-form-comment"><label for="comment">コメント</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';  // コメント欄も "required" を追加
  
  /** 要ログイン時 */
  $form['must_log_in']      = '<p class="must-log-in">' .  sprintf( 'コメントを残すには、<a href="%s">ログイン</a>してください。', wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>';
  
  /** ログイン時 */
  $form['logged_in_as']       = '<p class="logged-in-as">' . sprintf( '<a href="%1$s">%2$s</a> としてログインしています。<a href="%3$s" title="Log out of this account">ログアウト</a>しますか？', admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>';
  
  /** コメントフォームの前に表示するテキスト */
  $form['comment_notes_before']   = '<p class="comment-notes">' . ( $req ? $required_text : '' ) . '</p>';
  
  /** コメントフォームの後ろに表示するテキスト サンプルでは空文字をいれて非表示に */
  $form['comment_notes_after']  = '';
  
  /** form要素の id */
  $form['id_form']        = 'commentform';
  
  /** submit ボタンの id */
  $form['id_submit']        = 'submit';
  
  /** コメントフォームの見出しのタイトル */
  $form['title_reply']      = 'Leave a Reply';
  
  /** 返信コメント時のタイトル */
  $form['title_reply_to']     = 'Leave a Reply to %s';
  
  /** キャンセルリンクのテキスト */
  $form['cancel_reply_link']    = '(or Cancel)';
  
  /** 送信ボタンのラベル */
  $form['label_submit']       = 'Post Comment';
  
  return $form;
}

/** コメント一覧表示部分のコード（CHAPTER 19）*/
function my_comment_list( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <div class="clearfix">
            <div class="comment-author clearfix">
                <?php echo get_avatar( $comment->comment_author_email, 65 ); ?>
                <p class="comment-author-name"><?php comment_author_link(); ?><span class="says">says:</span></p>
                <p class="comment-meta"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_date(); ?>
                  <span><?php comment_time(); ?></span></a><br />
                  <?php edit_comment_link( '(編集)' ); ?>
                </p>
            </div>
            <div class="comment-body">
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <p><em>あなたのコメントは承認待ちです。</em></p>
                <?php endif;
                comment_text(); ?>
                <p class="reply">
                    <?php comment_reply_link( array_merge( $args, array(
                        'reply_text' => '返信',
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'],
                    ) ) ); ?>
                </p>
            </div>
        </div>
    </li> <!-- 修正した部分: <li> タグを閉じる位置を修正 -->
<?php }

?>