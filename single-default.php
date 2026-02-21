<?php
/****************************************

	single-default.php

	個別記事ページを表示するための
	テンプレートファイルです。
	 single.php のコピーです。
	（CHAPTER 17）

*****************************************/
?>


	<?php get_header(); ?>
	<!-- ぱんくずリストの追加 -->
			<div id="breadcrumbs">
				<?php breadcrumb1(); ?>
			</div>
	<div id="main" class="border-line">
		<?php if ( have_posts() ) : /** WordPress ループ（メインループ） */
			while ( have_posts() ) : the_post(); /** 繰り返し処理開始 */ ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="post-title"><?php the_title(); ?></h1>
				<p class="post-meta">

					<svg class="icon icon-clock">
						<use xlink:href="#icon-clock">
							<span class="post-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
						</use>
					</svg>

					<svg class="icon icon-price-tags">
						<use xlink:href="#icon-price-tags">
							<span class="category"><!--Category--><?php the_category( ', ' ) ?></span>
						</use>
					</svg>
					<span class="sidebar-comment-num">
						<?php comments_popup_link( '<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %' ); ?>
					</span>
						<script src="https://apis.google.com/js/platform.js"></script>

						<div class="g-ytsubscribe" data-channelid="UC7bt7kQHhaphJjt3GthBzIA" data-layout="default" data-count="default"></div>
				</p>

				<?php the_content();
				$args = array(
					'before'	  => '<div class="page-link">',
					'after'		  => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				);
				wp_link_pages( $args ); ?>
				<p class="footer-post-meta">
					<?php the_tags( 'Tag : ', ', ' ); ?>
					<span class="post-author">Author<!--作成者--> : <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></span>
				</p>
			</div>

			<!--new pager-->
			<?php require get_template_directory().'/pager.php';?>
			<!--end new pager-->

			<!--post_adjacent_nav written in function.pphp for pager-->
			<?php //post_adjacent_nav(); ?>
			<!--end_adjacent_nav written in function.pphp for pager-->

			<?php
			/** ここから関連記事の表示 */
			/** カテゴリーIDの取得 */
			$categories 	= get_the_category( $post->ID );
			$category_ID	= array();
			foreach ( $categories as $category ) {
				array_push( $category_ID, $category->cat_ID );
			}
			/** WordPressオブジェクトの作成 */
			$args = array(
				'post__not_in'		=> array( $post->ID ),
				'category__in'		=> $category_ID,
				'posts_per_page'	=> 3,
				'orderby'			=> 'rand',
			);
			$my_query = new WP_Query( $args );
			?>
			<div class="related-posts">
				<h2 class="side-title">Related Post</h2>
				<?php /** サブループ開始 */
				if ( $my_query->have_posts() ) : ?>
					<ul id="related-posts">
						<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
							<li class="clearfix">
								<div class="content-box">
									<a href="<?php the_permalink(); ?>" class="badge badge-primary"><?php the_title(); //Bootstrap色 Bootstrap見出しを大きくh4?></a>
									<p class="post-meta">
										<svg class="icon icon-clock">
											<use xlink:href="#icon-clock">
												<span class="post-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
											</use>
										</svg>
										<svg class="icon icon-price-tags">
											<use xlink:href="#icon-price-tags">
												<span class="category"><!--Category--><?php the_category( ', ' ) ?></span>
											</use>
										</svg>
										<span class="sidebar-comment-num">
											<?php comments_popup_link( '<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %' ); ?>
										</span>
									</p>
									<?php echo dess_get_excerpt(260); ?>
									<p class="more-link">
										<a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read"><span class="badge badge-success">Read &raquo;</span></a>
									</p>
								</div>
								<div class="blog-thumbnail-box">
									<a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
										<?php
										$days = 7;  // マークを表示する日数
										$now = date_i18n('U');  // 今の時間
										$entry = get_the_time('U');  // 投稿日の時間
										$term = date('U',($now - $entry)) / 86400;
										if( $days > $term ){
											echo '<div class="ribbon ribbon-top-left">';
											echo '<span>New</span>';
											echo '</div>';
										}
										 if( has_post_thumbnail() ) :

						                    $blog_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array(600,600) ); 
						                    $noimage = get_template_directory_uri() . '/images/noimage.gif';


	                                      echo '<div class="blog-post-image" style="background-image:url('.$blog_thumbnail[0].')"></a>';
	                                      echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
	                                      echo '</div>';;
										endif;
										?>
									</a>
								</div>
							</li>
						<?php endwhile; ?>
					</ul>
				<?php else : ?>
					<p>関連する記事はありませんでした ...</p>
				<?php endif; ?>
			</div><!-- /related-posts -->
			<?php wp_reset_postdata(); /** サブループここまで */
			comments_template(); /** コメント欄の表示（CHAPTER 19） */
			endwhile; /** メインループの繰り返し処理ここまで */
		else :	?>
			<div class="post">
				<h2>記事はありません</h2>
				<p>お探しの記事は見つかりませんでした。</p>
			</div>
		<?php endif; /** メインループここまで */?>
	</div><!-- /main -->

	<?php
	// tekken genshin ブログ記事別・カテゴリー でサイドバー表示 分岐
	?>
	<div id="single">
		<?php if (in_category('house')) : ?> <?//カスタム投稿タイプ「news」の場合?>
			<?php get_template_part( 'sidebar-house' ); ?>
		<?php elseif (in_category('land')): ?> <?//*カテゴリー「trend」の場合?>
			<?php get_template_part( 'sidebar-land' ); ?>
		<?php else : ?> <?//それ以外のページの場合?>
			<?php get_template_part( 'sidebar' ); ?><?//昔のサイドバーコードがやや違う?>
		<?php endif; ?>
	</div>

<!--スクリプトの表示-->
<?php get_footer('home1'); ?>
