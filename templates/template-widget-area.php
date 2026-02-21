<?php 
/*

  Template Name: template-widget-area.php

*/
?>

<div class="widget">
      <h2 class="side-title">Xiaoyu</h2>

      <?php $args = array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'category_name' => 'xiaoyu',/*xiaoy category*/
    );

      $my_query = new WP_Query( $args );


      if ( $my_query->have_posts() ) : /** サブループ */ ?>

        <ul id="sidebar-recent-posts" class="sidebar-posts">

          <?php while ( $my_query->have_posts() ) : $my_query->the_post(); $counter++; ?>

            <?php if ($counter <= 1): ?>

        <!--1st Image　-->

              <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                  
                   
                      <?php /** アイキャッチ画像（CHAPTER 14）*/ ?>

                      <div class="recent-thumbnail-box-big-image">
                        <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                          <?php 

                              $days = 14;  // マークを表示する日数より　NEWリボン　が表示される
                              $now = date_i18n('U');  // 今の時間
                              $entry = get_the_time('U');  // 投稿日の時間
                              $term = date('U',($now - $entry)) / 86400;
                              if( $days > $term ){
                                  echo '<div class="box">';
                                  //echo '<div class="ribon">';
                                  //echo '<div class="caption"><span>New</span></div>';
                                   echo '<div class="ribbon ribbon-top-left">';
                                   echo '<span>New</span>';
                                   echo '</div>';
                                   echo '</div>'; 
                                  
                              }
                    
                          ?>

                          <?php 
                           if( has_post_thumbnail() ) :

                            $blog_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID)); 
                            //$noimage = get_template_directory_uri() . '/images/noimage.jpg';
                            //$noimage = '<img src"' . get_template_directory_uri() . '/images/noimage.jpg />';
                            //$noimage = '<div class="thumbnail-box" style="background-image:url(' . get_template_directory_uri() . '/images/noimage.jpg);">';


                                              echo '<div class="recent-post-image" style="background-image:url('.$blog_thumbnail[0].')"></a>';
                                              echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
                                              echo '</div>';

                          
                                              
                            else : ?> '<div class="recent-post-image" style="background-image:url('https://kaztokyo.sakura.ne.jp/wordpress/wp-content/uploads/2021/12/No_image_available.svg')"></a></div>';

                             

                          <?php endif; ?> 
                        </a>
                      </div>
                  <!--/recent-thumbnail-box-big-image end-->

    <!--1st image　End　-->
    <!-- 抜粋　-->

                  <div class="recent-big-content-box">

                        <a href="<?php the_permalink(); ?>"class="badge badge-primary"><?php the_title(); //Bootstrap色 Bootstrap見出しを大きくh4?></a>

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
                                        
                                      </svg>                          <!--<p class="sidebar-comment-num"><?php //comments_popup_link( 'Comment : 0', 'Comment : 1', 'Comments : %' ); ?></p>-->
                          <span class="sidebar-comment-num">
                            <?php comments_popup_link( '<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %' ); ?>
                          </span>

                        </p>

                        
                      </a>
                        <?php //the_excerpt(); /** 抜粋（CHAPTER 14）*/
                        /** Readリンク */ ?>
                        <?php echo dess_get_excerpt(150); ?>
                        <p class="more-link"> 
                          <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read"><span class="badge badge-success">Read &raquo;</span></a>
                        </p>
                  </div>
          <!--/recent-big-content-box end-->              
        <!--/抜粋　End　-->

              </div>
        <!--/1st image　End　-->

        <?php else:?>

            <!--2nd image--> 

              <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                  
                    
                      <?php /** アイキャッチ画像（CHAPTER 14）*/ ?>

                      <div class="recent-thumbnail-box">
                        <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                          <?php 

                              $days = 14;  // マークを表示する日数よりNEWが表示される
                              $now = date_i18n('U');  // 今の時間
                              $entry = get_the_time('U');  // 投稿日の時間
                              $term = date('U',($now - $entry)) / 86400;
                              if( $days > $term ){
                                  echo '<div class="box">';
                                  //echo '<div class="ribon">';
                                  //echo '<div class="caption"><span>New</span></div>';
                                   echo '<div class="ribbon ribbon-top-left">';
                                   echo '<span>New</span>';
                                   echo '</div>';
                                   echo '</div>'; 
                                  
                              }
                    
                          ?>

                          <?php 
                           if( has_post_thumbnail() ) :

                            $blog_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array(600,600) ); 
                            //$noimage = get_template_directory_uri() . '/images/noimage.jpg';
                            //$noimage = '<img src"' . get_template_directory_uri() . '/images/noimage.jpg />';
                            //$noimage = '<div class="thumbnail-box" style="background-image:url(' . get_template_directory_uri() . '/images/noimage.jpg);">';


                                              echo '<div class="recent-post-image" style="background-image:url('.$blog_thumbnail[0].')"></a>';
                                              echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
                                              echo '</div>';

                          
                                              
                            else : ?> '<div class="blog-post-image" style="background-image:url('https://kaztokyo.sakura.ne.jp/wordpress/wp-content/uploads/2021/12/No_image_available.svg')"></a></div>';



                          <?php endif; ?> 

                        </a>
                      </div>
               </div>       
            <!--/recent-thumbnail-boend-->
          <!--/2nd image end-->         

            <!--抜粋　-->

                  <div class="recent-content-box">

                        <a href="<?php the_permalink(); ?>"class="badge badge-primary"><?php the_title(); //Bootstrap色 Bootstrap見出しを大きくh4?></a>

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
                                        
                                      </svg>                          <!--<p class="sidebar-comment-num"><?php //comments_popup_link( 'Comment : 0', 'Comment : 1', 'Comments : %' ); ?></p>-->
                          <span class="sidebar-comment-num">
                            <?php comments_popup_link( '<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %' ); ?>
                          </span>

                        </p>

                        
                      </a>
                        <?php //the_excerpt(); /** 抜粋（CHAPTER 14）*/
                        /** Readリンク */ ?>
                        <?php echo dess_get_excerpt(50); ?>
                        <p class="more-link"> 
                          <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read"><span class="badge badge-success">Read &raquo;</span></a>
                        </p>
                  </div>
          <!--/recent-content-box end-->              
        <!--/抜粋　End　-->

        </ul>


          <?php endif;?>

          <?php endwhile; ?>
      <?php endif; /** サブループ終了 */
      wp_reset_postdata(); ?>
    </div>
    
  <!-- /Recent Posts -->