<?php
/****************************************
  Template Name: page-gallery-room.php
*****************************************/
get_header('77');
?>

<!-- Swiper CSS（CDN版） -->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">-->

<style>
/* ===== デスクトップ用（レイアウト変更なし） ===== */
.mason-grid-slider {
    --masonGap: 1.25rem;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: var(--masonGap);
}
.mason-grid-item {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: var(--masonGap);
}
.mason-grid-item .mason-box:not(.small) {
    grid-column: span 2;
}
.mason-grid-item .mason-box {
    aspect-ratio: 1 / 1;
    position: relative;
    border-radius: 1.88rem;
    overflow: hidden;
    width:100%;
    height:100%;
}
.mason-grid-item .mason-box.full {
    aspect-ratio: unset;
    /*height: 600px;*/
    height:100%;
}



.mason-grid-item .mason-box > img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mason-grid-item .mason-box-up {
    position: absolute;
    bottom: 2.5rem;
    left: 2.5rem;
    right: 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-start;
    z-index: 2;
    color: #fff;
}

.full-width-image-carousel {
    background-color: rgba(235, 235, 235, .5);
}

.full-width-image-carousel .img-item {
    position: relative;
    width: auto; /* 🔥 Swiper のスライド数に応じて自動調整 */
    flex: 0 0 auto; /* 🔥 Swiper の `slidesPerView` に合わせる */
    user-select: none;
    border-radius: 1.88rem;
    overflow: hidden;
}


.full-width-image-carousel .img-item img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.full-width-image-carousel .img-item>a {
    /*position: absolute;*/
    left: 0;
    top: 0;
    display: block;
    height: 100%;
    width: 100%;
    border-radius: inherit;
}

.full-width-image-carousel .img-item::before {
    content: "";
    display: block;
    padding-top: 100%;
}

/* ===== モバイル用（Swiper適用） ===== */
@media (max-width: 768px) {
  .mason-grid-wrap {
    position: relative;
  }
  /* Mason Grid セクション：グリッド→flexに変更（横並び） */
  .mason-grid-slider {
    display: flex;
    overflow: hidden;
  }
  .swiper-wrapper {
    display: flex;
  }
  /* 各グループ（.swiper-slide）は1カラムに */
  .swiper-slide {
    flex: 0 0 100%;
    width: 100%;
    display: flex;
  }
  /* グループ内では、最初のリンクのみ表示 */
  .mason-grid-item.swiper-slide > a:not(:first-of-type) {
    display: none;
  }
  /* 画像ボックスのサイズ統一（高さ300px） */
  .mason-grid-item .mason-box {
    aspect-ratio: 1 / 1;
    height: 300px;
    width: 100%;
  }
  .mason-grid-item .mason-box.full {
    height: 300px;
    width: 100%;
  }
  .mason-box > img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  /* Swiperスクロールバーの調整 */
  .swiper-scrollbar {
    position: absolute;
    bottom: 10px;
    left: 10px;
    width: calc(100% - 20px);
    background: rgba(0, 0, 0, 0.2);
    height: 4px;
    border-radius: 2px;
  }
  .swiper-scrollbar-drag {
    background: #fff;
    height: 100%;
    border-radius: 2px;
  }
}

.full-width-image-carousel .fwiimg-carousel {
    padding: 0 1.25rem;
}

.mason-grid-community-list {
    margin-bottom:20px;
}


.img-carousel-wrap.fwi-carousel {
    display: flex;
    overflow: hidden;
}
/* ===== Full Width Image Carousel セクション ===== */
.full-width-image-carousel .sec-heading {
    text-align: center;
}
.full-width-image-carousel h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}
@media (max-width: 768px) {
  .img-carousel-wrap.fwi-carousel {
    overflow: hidden;
  }
  .img-wrap.swiper-wrapper {
    display: flex;
  }
  .img-item.swiper-slide {
    flex: 0 0 100%;
    width: 100%;
    margin-right: 10px;
  }
  .img-item img {
    width: 100%;
    height: auto;
    object-fit: cover;
    display: block;
  }
}

/* 🎬 全幅メディアセクション */
.full-width-media,
.youtube-container {
    width: 100%;
    margin: 0 auto;
    padding: 20px 0;
}

/* 🎞 MP4動画 */
.full-width-media video {
    width: 100%;
    height: auto;
    display: block;
    max-height: 500px;
    object-fit: cover;
}

/* 🌀 YouTube Swiper全体 */
.youtube-container .swiper {
    width: 100%;
    padding-bottom: 2rem;
}

/* 各スライド */
.youtube-container .swiper-slide {
    flex-shrink: 0;
    width: 100%;
    box-sizing: border-box;
}

/* 📺 YouTube iframeラッパー */
.youtube-container .youtube-video {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    overflow: hidden;
    border-radius: 8px;
}

/* 📺 YouTube iframe自体 */
.youtube-container .youtube-video iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}


/* モバイル対応（小さい画面用） */
@media screen and (max-width: 768px) {
    .full-width-media video {
        max-height: 300px; /* モバイルでは高さを少し小さく */
    }
}


/* ナビゲーションボタン全体（初期は非表示） */
.fwi-button-prev,
.fwi-button-next {
  display: none;
}

/* ================================
   📱 モバイル（～768px）用 Swiperナビボタン
================================== */
@media (max-width: 768px) {
  /* ナビゲーションボタン全体（丸い背景付き） */
  .fwi-button-prev,
  .fwi-button-next {
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 30px;               /* 背景サイズ */
    height: 30px;
    background: rgba(0, 0, 0, 0.6);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    padding: 0;
    box-sizing: content-box;  /* 万が一padding入った時の保険 */
  }

  .fwi-button-prev {
    left: 10px;
  }

  .fwi-button-next {
    right: 10px;
  }

  /* SVGアイコン（IcoMoon）のサイズ・色調整 */
  .fwi-button-prev .icon-circle-left,
  .fwi-button-next .icon-circle-right {
    width: 30px;
    height: 30px;
    fill: #fff;
    display: block;
    pointer-events: none;
  }
}


.full-width-page p {
    padding:5px;
}


/* ✅ サムネイル表示用（メインスライダーの下に並べる） */
.main-slider-thumbnails {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 1rem;
  flex-wrap: wrap;
  padding: 0 1rem;
}

.main-slider-thumbnails .thumb-item {
  width: 100px;
  height: 70px;
  overflow: hidden;
  border-radius: 8px;
}

.main-slider-thumbnails .thumb-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border: 2px solid #ccc;
  transition: border-color 0.2s ease;
}

.main-slider-thumbnails .thumb-item img:hover {
  border-color: #555;
}

/* ✅ gallery-room 専用 - h1, h2, h3, p の共通調整 */
.post-container h1,
.post-container .content h2,
.post-container .content h3,
.post-container .content p {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
  box-sizing: border-box;
  line-height: 1.8;
  word-break: break-word;
}

/* ✅ h1の追加スタイル（見出し強調） */
.post-container .content h1 {
  font-size: 2.5rem;
  font-weight: bold;
  text-align: center;
  margin: 2rem auto 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #ccc;
}

/* ✅ モバイル対応 */
@media screen and (max-width: 1200px) {
  .post-container .content h1,
  .post-container .content h2,
  .post-container .content h3,
  .post-container .content p {
    max-width: 100%;
  }

  .post-container .content h1 {
    font-size: 2rem;
  }
}



</style>

<article class="post-container">

<!-- ✅ Swiper 全画面スライダー -->
<div class="swiper-container" style="width: 100%; max-width: none; height: 500px; position: relative; z-index: 10; overflow: hidden;">
  <div class="swiper-wrapper">
    <?php
    for ($i = 1; $i <= 20; $i++) {
        $image_id = get_post_meta(get_the_ID(), "slider_image_$i", true);
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'large');
            echo '<div class="swiper-slide">';
            echo '<img src="' . esc_url($image_url) . '" alt="" style="width:100%; height:100%; object-fit:cover;">';
            echo '</div>';
        }
    }
    ?>
  </div>
  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
  <div class="swiper-pagination"></div>
</div>

<!-- ✅ 🔻🔻🔻 サムネイル一覧：indexを追加 🔻🔻🔻 -->
<div class="main-slider-thumbnails">
  <?php
  $thumb_index = 0;
  for ($i = 1; $i <= 20; $i++) {
      $image_id = get_post_meta(get_the_ID(), "slider_image_$i", true);
      if ($image_id) {
          $thumb_url = wp_get_attachment_image_url($image_id, 'thumbnail');
          echo '<div class="thumb-item" data-index="' . esc_attr($thumb_index) . '">';
          echo '<img src="' . esc_url($thumb_url) . '" alt="">';
          echo '</div>';
          $thumb_index++;
      }
  }
  ?>
</div>


<!-- Swiper JSと初期化 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Swiperの初期化
  const mainSwiper = new Swiper('.swiper-container', {
    loop: true,
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },
    autoplay: {
      delay: 3000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev'
    }
  });

  // ✅ サムネイルをクリックしたときの切り替え
  const thumbs = document.querySelectorAll('.main-slider-thumbnails .thumb-item');
  thumbs.forEach((thumb, index) => {
    thumb.addEventListener('click', () => {
      mainSwiper.slideToLoop(index); // 🔁 ループ有効時は slideToLoop が必要
    });
  });
});
</script>



    <h1><?php the_title(); ?></h1>
    <div class="content">
        <?php the_content(); ?>

        <!-- MP4動画 -->
        <section class="full-width-media">
            <h2>北米住宅仕様</h2>
            <?php display_media_mp4(); ?>  
        </section>

        <!-- YouTube動画 -->
        <section class="youtube-container">
            <h2>分譲地 紹介</h2>
            <?php display_media_youtube(); ?> 
        </section>
    </div>
</article>


<!-- Mason Grid セクション（投稿クエリによる画像グループ） -->
<section class="mason-grid-community-list">
    <div class="sec-heading d-flex flex-nowrap align-items-center justify-content-between">
      <div class="comm-heading">
        <h2 class="comm-name"></h2>
        <div class="comm-location"></div>
      </div>
      <h2>Explore <span>these communities</span></h2>
    </div>
    <div class="mason-grid-wrap main-mason">
<div class="mason-grid-slider">
        <?php
        $args = array(
          'post_type'      => array('post', 'house'),
          'posts_per_page' => 7,
        );
        $query = new WP_Query($args);

        // グループ化用クラスシーケンス（デスクトップレイアウト用）
        $class_sequence = array(
          'mason-box',          // 1件目
          'mason-box small',    // 2件目
          'mason-box small',    // 3件目
          'mason-box full',     // 4件目
          'mason-box small',    // 5件目
          'mason-box small',    // 6件目
          'mason-box'           // 7件目
        );

        if ( $query->have_posts() ) :
          $counter = 0;
          while ( $query->have_posts() ) : $query->the_post();
            $counter++;
            $a_class = isset($class_sequence[$counter - 1]) ? $class_sequence[$counter - 1] : 'mason-box small';

            // グループ開始：投稿番号が 1, 4, 5 のときに開始（各グループの最初の画像）
            if ( in_array( $counter, array(1, 4, 5) ) ) {
              echo '<div class="mason-grid-item swiper-slide">';
            }

            $post_url  = get_permalink();
            $thumbnail = get_the_post_thumbnail_url( get_the_ID(), '' );

            $type     = get_post_meta( get_the_ID(), 'page_featured_type', true );
            $video_id = get_post_meta( get_the_ID(), 'page_video_id', true );
            if ( $type === 'youtube' && !empty( $video_id ) ) {
              $thumbnail = 'https://i.ytimg.com/vi/' . esc_attr( $video_id ) . '/hqdefault.jpg';
            } elseif ( $type === 'vimeo' && !empty( $video_id ) ) {
              $thumbnail = 'https://vumbnail.com/' . esc_attr( $video_id ) . '.jpg';
            }

            echo '<a href="' . esc_url( $post_url ) . '" class="' . esc_attr( $a_class ) . '" style="position: relative;">';
            echo '    <img src="' . esc_url( $thumbnail ) . '" alt="' . esc_attr( get_the_title() ) . '">';

            /* ✅ ここに `overlay` を追加 */
            echo '    <div class="thumbnail-overlay">';
            echo '        <h3>' . esc_html( get_the_title() ) . '</h3>'; // 🔥 記事タイトルを表示
            echo '    </div>';

            echo '    <div class="mason-box-up">';
            echo '        <i data-icon="a"></i>';
            echo '    </div>';
            echo '</a>';

            // グループ終了：投稿番号が 3, 4, 7 のときに終了
            if ( in_array( $counter, array(3, 4, 7) ) ) {
              echo '</div>';
            }
          endwhile;
        endif;
        wp_reset_postdata();
        ?>
      </div> <!-- .mason-grid-slider  -->
      <!-- Swiper Scrollbar -->
      <div class="swiper-scrollbar swiper-scrollbar-horizontal"></div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // ✅ モバイル画面のみスライダーを適用（768px以下）
  if (window.innerWidth > 768) return;

  // ✅ .mason-grid-item（1つの投稿グループ）をすべて取得
  const allItems = document.querySelectorAll('.mason-grid-item');

  // ✅ Swiper用の .swiper-wrapper を作成（これにスライド要素を追加していく）
  const swiperWrapper = document.createElement('div');
  swiperWrapper.classList.add('swiper-wrapper');

  let found = false; // スライダー対象の要素が1つでも見つかったかどうかのフラグ

  // ✅ すべての .mason-grid-item に対してループ
  allItems.forEach(item => {
    // 🔥 この中から .mason-box か .mason-box.full のみを抜き出す（.small は除外）
    const targets = item.querySelectorAll('.mason-box:not(.small)');

    if (targets.length > 0) {
      // ✅ スライド1枚分（swiper-slide）を作成
      const slide = document.createElement('div');
      slide.classList.add('swiper-slide');

      // ✅ 該当の画像リンクをすべてクローンしてslideに追加
      targets.forEach(box => {
        slide.appendChild(box.cloneNode(true)); // HTMLそのまま複製
      });

      swiperWrapper.appendChild(slide);
      found = true;
    }
  });

  if (found) {
    // ✅ Swiperの親コンテナを作成
    const swiperContainer = document.createElement('div');
    swiperContainer.classList.add('swiper', 'mason-mobile-swiper');
    swiperContainer.appendChild(swiperWrapper);

    // ✅ スクロールバーを追加
    const scroll = document.createElement('div');
    scroll.classList.add('swiper-scrollbar');
    swiperContainer.appendChild(scroll);

    // ✅ 表示場所に差し替え（既存HTMLを削除し、新Swiper構造を追加）
    const targetSection = document.querySelector('.mason-grid-wrap');
    targetSection.innerHTML = ''; // すべて消す
    targetSection.appendChild(swiperContainer);

    // ✅ Swiper の初期化処理（モバイル向け1枚表示）
    new Swiper('.mason-mobile-swiper', {
      slidesPerView: 1,
      spaceBetween: 16,
      scrollbar: {
        el: '.swiper-scrollbar',
        draggable: true
      }
    });
  }
});
</script>












<!-- 20枚の画像 Full Width Image Carousel セクション（カスタムフィールドから画像取得） -->
<?php
// カスタムフィールド "room_gallery_images" から画像IDの配列を取得（最大20枚）
// もし値が文字列（カンマ区切り）で保存されている場合は、explode() を利用
$gallery_images = get_post_meta(get_the_ID(), 'room_gallery_images', true);
if (!empty($gallery_images)) {
    if (!is_array($gallery_images)) {
        $gallery_images = explode(',', $gallery_images);
    }
    $gallery_images = array_slice($gallery_images, 0, 20);
}
?>
<!-- Full Width Image Carousel セクション -->
<section class="full-width-image-carousel space-sm">
    <div class="sec-heading text-center">
        <h2>Get inspired, <span>follow us contact@naigaicorp.net</span></h2>

    </div>

    <div class="fwiimg-carousel">
        <div class="img-carousel-wrap fwi-carousel swiper">

              <div class="swiper-pagination fwi-pagination"></div>


<!-- 下部のカルーセル専用ナビゲーション -->
<button class="fwi-button-prev" tabindex="0" aria-label="Previous slide">
  <svg class="icon icon-circle-left" aria-hidden="true">
    <use xlink:href="#icon-circle-left"></use>
  </svg>
</button>
<button class="fwi-button-next" tabindex="0" aria-label="Next slide">
  <svg class="icon icon-circle-right" aria-hidden="true">
    <use xlink:href="#icon-circle-right"></use>
  </svg>
</button>



            <div class="img-wrap swiper-wrapper">
                <?php if (!empty($gallery_images)) : ?>
                    <?php foreach ($gallery_images as $index => $image_id) : ?>
                        <?php
                        $img_data = wp_get_attachment_image_src($image_id, 'full');
                        $image_url = $img_data ? $img_data[0] : '';
                        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        ?>
                        <div class="img-item swiper-slide">
                            <a href="<?php echo esc_url($image_url); ?>" title="<?php echo esc_attr($alt_text); ?>" target="_blank">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt_text); ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</section>




<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ Swiper initialization started...");

    /**
     * 📌 画面サイズに応じてスライド数を決定する関数
     * - 1200px 以上: 10枚
     * - 992px 以上: 5枚
     * - 768px 以上: 3枚
     * - 768px 未満: 1枚
     */
    function getSlidesPerView() {
        if (window.innerWidth >= 1200) return 10;
        if (window.innerWidth >= 992) return 5;
        if (window.innerWidth >= 768) return 3;
        return 1;
    }

    /**
     * 📌 Swiper インスタンスを初期化する関数
     * - 既存のインスタンスを破棄して、新しい設定で再作成する
     */
    function initSwiper(selector, options) {
        var element = document.querySelector(selector);
        if (!element) {
            console.error(`❌ Swiper failed: ${selector} not found.`);
            return null;
        }

        // **既存のSwiperインスタンスを破棄**
        if (element.swiper) {
            element.swiper.destroy(true, true);
            console.log(`🔄 Destroyed previous Swiper instance for ${selector}`);
        }

        // **クラスを削除して再初期化**
        element.classList.remove('swiper-initialized', 'swiper-horizontal', 'swiper-backface-hidden');

        // **新しいSwiperインスタンスを作成**
        var swiper = new Swiper(element, options);
        console.log(`✅ Swiper initialized for ${selector}`);
        return swiper;
    }

    /**
     * 📌 Full Width Carousel Swiper（全デバイス対応）
     * - 最初のレンダリング時から適切なスライド数を適用
     */
    var fullWidthSwiper;

    function setupFullWidthSwiper() {
        if (fullWidthSwiper) {
            fullWidthSwiper.destroy(true, true);
            console.log("🔄 Destroyed previous Full Width Swiper instance.");
        }

        fullWidthSwiper = initSwiper('.img-carousel-wrap.fwi-carousel', {
            slidesPerView: getSlidesPerView(), // 🔥 初期スライド数を正しく適用
            spaceBetween: 10,
            loop: true, // 🔥 最初からループを適用
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            },
            //observer: true,
            //observeParents: true,
navigation: {
  nextEl: '.fwi-button-next',
  prevEl: '.fwi-button-prev'
},


            pagination: {
                el: '.fwi-pagination', // ← ここを変更！
                clickable: true
            },
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true
            },
            breakpoints: {
                768: { slidesPerView: 3, spaceBetween: 10 },
                992: { slidesPerView: 5, spaceBetween: 10 },
                1200: { slidesPerView: 10, spaceBetween: 10 }
            }
        });

        console.log(`✅ Swiper initialized with slidesPerView: ${getSlidesPerView()}`);
    }

    // **DOMが完全に読み込まれた後にSwiperを初期化**
    requestAnimationFrame(setupFullWidthSwiper);

    /**
     * 📌 ウィンドウサイズ変更時に適切なスライド数を適用
     */
    window.addEventListener('resize', function () {
        let newSlides = getSlidesPerView();
        if (fullWidthSwiper && fullWidthSwiper.params.slidesPerView !== newSlides) {
            fullWidthSwiper.params.slidesPerView = newSlides;
            fullWidthSwiper.update();
            console.log(`🔄 Resized Swiper updated for slidesPerView: ${newSlides}`);
        }
    });
});

</script>

<?php get_footer(); ?>
