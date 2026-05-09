<?php

/**
 * =========================================================
 * template-parts/hub/section-links.php
 * hub 用のリンクカード一覧セクション
 * =========================================================
 *
 * このファイルの役割:
 * - front-page.php などから get_template_part() で呼ばれる
 * - hub 用の「導線カード一覧」をまとめて描画する
 * - 各カードのタイトル / 説明 / URL / 画像を組み立てる
 * - 画像が複数あるときは Swiper 用のHTMLを出す
 *
 * 想定する呼び出し例:
 * get_template_part(
 *   'template-parts/hub/section-links',
 *   null,
 *   array(
 *     'post_id'       => $page_id,
 *     'prefix'        => '_hub_link',
 *     'count'         => 5,
 *     'title_key'     => '_hub_gateway_title',
 *     'section_class' => 'hub-purpose-links hub-purpose-links--realestate',
 *   )
 * );
 *
 * 重要:
 * - このファイル単体では動かない
 * - front-page.php などから args 付きで呼ばれてはじめて使われる
 * - naigai_hub_get() / naigai_hub_get_items() / naigai_hub_get_item_images()
 *   が読み込まれている前提
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * ---------------------------------------------------------
 * 呼び出し元から渡された post_id を取得
 * ---------------------------------------------------------
 * これが 0 以下なら、どのページのメタ情報を読むのか不明なので終了
 */
/**
 * ---------------------------------------------------------
 * 呼び出し元から渡された page_id / post_id を取得
 * ---------------------------------------------------------
 * 旧 front は post_id、新 Hub 構成は page_id を渡す
 */
$source_page_id = isset($args['page_id']) ? (int) $args['page_id'] : 0;
if ($source_page_id <= 0 && isset($args['post_id'])) {
  $source_page_id = (int) $args['post_id'];
}
if ($source_page_id <= 0) {
  return;
}

/**
 * ---------------------------------------------------------
 * 描画設定を args から取得
 * ---------------------------------------------------------
 * prefix:
 *   メタキーの接頭辞
 *   例: _hub_link_1_title, _hub_link_1_text など
 *
 * count:
 *   最大何件までリンクカードを読むか
 *
 * title_key:
 *   セクション見出しのメタキー
 *
 * section_class:
 *   section に付けるクラス
 *
 * defaults:
 *   メタが空だったときの予備データ
 */
$prefix        = isset($args['prefix']) ? (string) $args['prefix'] : '_hub_link';
$count         = isset($args['count']) ? (int) $args['count'] : 5;
$title_key     = isset($args['title_key']) ? (string) $args['title_key'] : '_hub_links_title';
$section_class = isset($args['section_class']) ? (string) $args['section_class'] : 'hub-purpose-links';
$defaults      = (isset($args['defaults']) && is_array($args['defaults'])) ? $args['defaults'] : array();

/**
 * ---------------------------------------------------------
 * セクション見出しを取得
 * ---------------------------------------------------------
 * 例:
 * - _hub_gateway_title
 * - _hub_links_title
 */
$heading = function_exists('naigai_hub_get')
  ? naigai_hub_get($source_page_id, $title_key, '')
  : '';

/**
 * ---------------------------------------------------------
 * リンク項目の元データを取得
 * ---------------------------------------------------------
 * この段階では title / text / url / page_id などの配列を想定
 */
$items = function_exists('naigai_hub_get_items')
  ? naigai_hub_get_items($source_page_id, $prefix, $count)
  : array();

/**
 * ---------------------------------------------------------
 * 実際に描画するための整形済み配列
 * ---------------------------------------------------------
 */
$resolved_items = array();

/**
 * ---------------------------------------------------------
 * count の件数ぶんループして、
 * 各リンクカードの情報を整理する
 * ---------------------------------------------------------
 */
for ($i = 0; $i < $count; $i++) {
  $item     = isset($items[$i]) ? $items[$i] : array();
  $default  = isset($defaults[$i]) ? $defaults[$i] : array();

  /**
   * title / text / url は
   * まずメタデータ側を優先し、
   * 空なら defaults を使う
   */
  $title    = !empty($item['title']) ? $item['title'] : (isset($default['title']) ? $default['title'] : '');
  $text     = !empty($item['text']) ? $item['text'] : (isset($default['text']) ? $default['text'] : '');
  $url      = !empty($item['url']) ? $item['url'] : (isset($default['url']) ? $default['url'] : '');
  $linked_page_id  = !empty($item['page_id']) ? (int) $item['page_id'] : 0;

  /**
   * 例:
   * _hub_link_1
   * _hub_link_2
   * ...
   */
  $base_key = "{$prefix}_" . ($i + 1);

  /**
   * -----------------------------------------------------
   * 画像群を取得
   * -----------------------------------------------------
   * page_id がある場合は関連ページ側の画像も拾える設計を想定
   * 戻り値は配列:
   * [
   *   [
   *     'src'    => '...',
   *     'alt'    => '...',
   *     'srcset' => '...',
   *     'sizes'  => '...',
   *   ],
   *   ...
   * ]
   */
  $images = function_exists('naigai_hub_get_item_images')
    ? naigai_hub_get_item_images($source_page_id, $base_key, $linked_page_id, 'large')
    : array();

  /**
   * -----------------------------------------------------
   * タイトルまたはURLがない項目は描画しない
   * -----------------------------------------------------
   * カードとして成立しないためスキップ
   */
  if ($title === '' || $url === '') {
    continue;
  }

  /**
   * 描画用配列に追加
   */
  $resolved_items[] = array(
    'title'  => $title,
    'text'   => $text,
    'url'    => $url,
    'images' => $images,
  );
}

/**
 * ---------------------------------------------------------
 * 描画対象が1件もなければ何も出さず終了
 * ---------------------------------------------------------
 */
if (empty($resolved_items)) {
  return;
}
?>
<section class="hub-section <?php echo esc_attr($section_class); ?>">
  <div class="hub-section__inner hub-container">

    <?php if ($heading !== '') : ?>
      <h2 class="hub-heading is-lg"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>

    <ul class="hub-link-list">
      <?php foreach ($resolved_items as $item) : ?>
        <li>
          <article class="hub-link-item hub-link-item--card<?php echo empty($item['images']) ? ' is-no-media' : ''; ?>">

            <?php if (!empty($item['images'])) : ?>

              <?php if (count($item['images']) > 1) : ?>
                <!-- =====================================================
                     画像が複数ある場合
                     Swiper 用のマークアップを出す
                     JS 側で .js-hub-swiper を初期化する想定
                ====================================================== -->
                <div class="hub-card__media hub-card__media--slider swiper js-hub-swiper">
                  <div class="swiper-wrapper">
                    <?php foreach ($item['images'] as $image) : ?>
                      <div class="swiper-slide">
                        <img
                          class="hub-card__img"
                          src="<?php echo esc_url($image['src']); ?>"
                          alt="<?php echo esc_attr($image['alt']); ?>"
                          <?php if (!empty($image['srcset'])) : ?>srcset="<?php echo esc_attr($image['srcset']); ?>" <?php endif; ?>
                          <?php if (!empty($image['sizes'])) : ?>sizes="<?php echo esc_attr($image['sizes']); ?>" <?php endif; ?>
                          loading="lazy">
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <!-- ページネーション -->
                  <div class="swiper-pagination"></div>
                </div>

              <?php else : ?>
                <?php $image = $item['images'][0]; ?>

                <!-- =====================================================
                     画像が1枚だけの場合
                     普通の画像として表示
                ====================================================== -->
                <div class="hub-card__media">
                  <img
                    class="hub-card__img"
                    src="<?php echo esc_url($image['src']); ?>"
                    alt="<?php echo esc_attr($image['alt']); ?>"
                    <?php if (!empty($image['srcset'])) : ?>srcset="<?php echo esc_attr($image['srcset']); ?>" <?php endif; ?>
                    <?php if (!empty($image['sizes'])) : ?>sizes="<?php echo esc_attr($image['sizes']); ?>" <?php endif; ?>
                    loading="lazy">
                </div>
              <?php endif; ?>

            <?php endif; ?>

            <div class="hub-link-item__body">
              <div class="hub-link-item__main">
                <h3 class="hub-link-item__title"><?php echo esc_html($item['title']); ?></h3>

                <?php if (!empty($item['text'])) : ?>
                  <p class="hub-link-item__text"><?php echo esc_html($item['text']); ?></p>
                <?php endif; ?>
              </div>

              <p class="hub-link-item__action">
                <a class="hub-link-item__cta" href="<?php echo esc_url($item['url']); ?>">
                  <span class="hub-link-item__cta-arrow" aria-hidden="true">→</span>
                  <span class="hub-link-item__cta-label">詳しく見る</span>
                </a>
              </p>
            </div>

          </article>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>