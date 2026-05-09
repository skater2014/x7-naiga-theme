<?php 
/** 関数化したパンくずリスト（CHAPTER 16）*/
function breadcrumb() {
  global $post;
  $separater  = '<li>&gt;</li>';
  $str    = '';
  if ( ! is_home() && ! is_admin() ) {  /** ! is_admin は管理ページ以外という条件分岐です */
    $str .= '<div id="breadcrumb" class="clearfix">';
    $str .= '<ul>';
    $str .= '<li><a href="' . esc_url( home_url( '/' ) ) . '">HOME</a></li>';
    $str .= $separater;
    if ( is_search() ) {        /** 検索結果ページ */
      $str .= '<li>「' . esc_html( get_search_query() ) . '」で検索した結果</li>';
    } elseif ( is_tag() ) {
      $str .= '<li>タグ : ' . single_tag_title( '' , false ) . '</li>';
    } elseif ( is_404() ) {       /** 404ページ */
      $str .= '<li>404 Not found</li>';
    } elseif ( is_date() ) {      /** 日付アーカーブ */
      if ( is_day() ) {         /** 日別アーカイブ */
        $str .= '<li><a href="' . get_year_link( get_query_var( 'year' ) ) . '">' . get_query_var( 'year' ) . '年</a></li>';
        $str .= $separater;
        $str .= '<li><a href="' . get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ) . '">' . get_query_var( 'monthnum' ) . '月</a></li>';
        $str .= $separater;
        $str .= '<li>' . get_query_var( 'day' ) . '日</li>';
      } elseif ( is_month() ) {     /** 月別アーカイブ */
        $str .= '<li><a href="' . get_year_link( get_query_var( 'year' ) ) . '">' . get_query_var( 'year' ) . '年</a></li>';
        $str .= $separater;
        $str .= '<li>' . get_query_var( 'monthnum' ) . '月</li>';
      } elseif ( is_year() ) {    /** 年別アーカイブ */
        $str .= '<li>' . get_query_var( 'year' ) . '年</li>';
      }
    } elseif ( is_category() ) {    /** カテゴリーアーカイブ */
      $cat = get_queried_object();
      if ( $cat->parent != 0 ) {
        $ancestors = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
        foreach ( $ancestors as $ancestor ) {
          $str .= '<li><a href="' . esc_url( get_category_link( $ancestor ) ) . '">' . esc_html( get_cat_name( $ancestor ) ) . '</a></li>';
          $str .= $separater;
        }
      }
      $str .= '<li>' . $cat->name . '</li>';
    } elseif ( is_author() ) {      /** 投稿者アーカイブ */
      $str .='<li>投稿者 : ' . esc_html( get_the_author_meta( 'display_name', get_query_var( 'author' ) ) ) . '</li>';
    } elseif ( is_page() ) {      /** 固定ページ */
      if ( $post->post_parent != 0 ) {
        $ancestors = array_reverse( get_ancestors( $post->ID, 'page' ) );
        foreach ( $ancestors as $ancestor) {
          $str .= '<li><a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a></li>';
          $str .= $separater;
        }
      }
      $str .= '<li>' . esc_html( $post->post_title ) . '</li>';

    } elseif ( is_attachment() ) {    /** 添付ファイルページ */
      if ( $post->post_parent != 0 ) {
        $str .= '<li><a href="' . esc_url( get_permalink( $post->post_parent ) ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a></li>';
        $str .= $separater;
      }
      $str .= '<li>' . esc_html( $post->post_title ) . '</li>';
    } elseif ( is_single() ) {      /** ブログ記事ページ */
      $categories = get_the_category( $post->ID );
      $cat    = $categories[0];
      if ( $cat->parent != 0 ) {
        $ancestors = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
        foreach ( $ancestors as $ancestor ) {
          $str .= '<li><a href="' . esc_url( get_category_link( $ancestor ) ) . '">' . esc_html( get_cat_name( $ancestor ) ) . '</a></li>';
          $str .= $separater;
        }
      }
      $str .= '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . $cat->cat_name . '</a></li>';
      $str .= $separater;
      $str .= '<li>' . esc_html( $post->post_title ) . '</li>';
    } else{               /** その他のページ */
      $str .= '<li>' . wp_title( '', true ) . '</li>';
    }
    $str .= '</ul>';
    $str .= '</div>';
  }
  echo $str;
}