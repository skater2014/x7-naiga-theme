<?php

/* Price_Range_Widgetクラス */
class Price_Range_Widget extends WP_Widget
{

    /* コンストラクタを定義 */
    public function __construct()
    {
        parent::__construct(
            'price_range_widget', // ウィジェットのID
            'TEST価格範囲サイドバー', // ウィジェットの名称（管理画面で表示される）
            array(
                'description' => '価格範囲を絞り込むスライダーを表示します' // ウィジェットの説明（管理画面で表示される）
            )
        );
    }

    /* モバイルデバイスかどうか判定するメソッド */
    public function is_mobile_device()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // モバイル端末を示すユーザーエージェントを含んでいない場合はPC
        if (preg_match('/(iPhone|iPod|Android.*Mobile|Windows Phone|BlackBerry|IEMobile|Opera Mini)/i', $user_agent)) {
            return true; // モバイル端末
        }
        return false; // PC
    }

    /* ウィジェットの出力 */
    public function widget($args, $instance)
    {
        // モバイルでも価格帯を出力する
echo $args['before_widget'];

        // GETパラメータから価格範囲を取得
        $min_price = isset($_GET['min_price']) ? (int) $_GET['min_price'] : (isset($instance['min_price']) ? $instance['min_price'] : 300);
        $max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : (isset($instance['max_price']) ? $instance['max_price'] : 4000);

        /*
         * 価格帯の中身HTML:
         * sidebar-land.php の dynamic_sidebar('price-range') から呼ばれる。
         * fieldset.price-filter がカード本体。
         * input はスライダー本体なので、CSSでは fieldset と input を分けて扱う。
         */
?>
        <fieldset class="price-filter">
            <legend>価格範囲で絞り込む</legend>
            <div>
                <label for="price_range">価格範囲: ￥<span id="price_range_label_min"><?php echo number_format($min_price); ?>万円</span> 〜 ￥<span id="price_range_label_max"><?php echo number_format($max_price); ?>万円</span></label>
                <input type="range"
                    id="price-range-slider"
                    class="price-filter-range"
                    min="0"
                    max="10000"
                    step="100"
                    value="<?php echo $min_price; ?>"
                    onchange="updatePriceRange();"
                    oninput="updatePriceRange();"
                    style="width: 100%">
            </div>
            <!-- リセットボタン -->
            <button type="button" onclick="resetPriceRange();">リセット</button>
        </fieldset>
    <?php
        echo $args['after_widget'];
    }
}

/* フッターでJavaScriptを出力 */
/* フッターでJavaScriptを出力 */
function add_price_range_widget_script()
{
    // フロントのみ
    if (is_admin()) return;

    // 今いる場所（カテゴリならslug取れる）
    $context = [
        'type' => '',
        'slug' => '',
        'min'  => 300,
        'max'  => 4000,
        'step' => 100,
        'window' => 1000, // min に +1000 して max を作る
        'limitMax' => 10000,
    ];

    if (is_category()) {
        $term = get_queried_object();
        $context['type'] = 'category';
        $context['slug'] = $term && !is_wp_error($term) ? $term->slug : '';
    } elseif (is_post_type_archive('house')) {
        $context['type'] = 'post_type_archive';
        $context['slug'] = 'house';
    } elseif (is_search()) {
        $context['type'] = 'search';
        $context['slug'] = 'search';
    }

    // カテゴリ/画面ごとのデフォルト
    $defaultsMap = [
        'naigai-construction' => ['min' => 1000, 'max' => 4000],
        'naigai-tochi'        => ['min' => 100,  'max' => 3000],
        // house をカテゴリとして扱わないなら消していい（CPTアーカイブ用途なら残す）
        'house'               => ['min' => 1000, 'max' => 4000],
    ];
    if (!empty($context['slug']) && isset($defaultsMap[$context['slug']])) {
        $context['min'] = (int)$defaultsMap[$context['slug']]['min'];
        $context['max'] = (int)$defaultsMap[$context['slug']]['max'];
    }

    ?>
    <script>
        (function() {
            const slider = document.getElementById('price-range-slider');
            if (!slider) return;

            const minLabel = document.getElementById('price_range_label_min');
            const maxLabel = document.getElementById('price_range_label_max');

            const ctx = <?php echo wp_json_encode($context, JSON_UNESCAPED_UNICODE); ?>;

            const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

            function stripPagedPath(urlObj) {
                // /xxx/page/2/ を /xxx/ に戻す（WPのパス型ページネーション対策）
                urlObj.pathname = urlObj.pathname.replace(/\/page\/\d+\/?$/, '/');
                // 二重スラッシュ整理
                urlObj.pathname = urlObj.pathname.replace(/\/{2,}/g, '/');
                return urlObj;
            }

            // 初期表示：GET があればそれ、なければデフォルト
            const url = new URL(window.location.href);

            let min = parseInt(url.searchParams.get('min_price'), 10);
            let max = parseInt(url.searchParams.get('max_price'), 10);

            if (Number.isNaN(min)) min = ctx.min;
            if (Number.isNaN(max)) max = ctx.max;

            min = clamp(min, 0, ctx.limitMax);
            max = clamp(max, min, ctx.limitMax);

            slider.min = 0;
            slider.max = ctx.limitMax;
            slider.step = ctx.step;
            slider.value = min;

            if (minLabel) minLabel.textContent = min + '万円';
            if (maxLabel) maxLabel.textContent = max + '万円';

            // onclick 属性から呼ばれるので global に出す
            window.updatePriceRange = function() {
                const cur = new URL(window.location.href);
                stripPagedPath(cur);

                const minValue = clamp(parseInt(slider.value, 10) || 0, 0, ctx.limitMax);
                const maxValue = clamp(minValue + ctx.window, minValue, ctx.limitMax);

                if (minLabel) minLabel.textContent = minValue + '万円';
                if (maxLabel) maxLabel.textContent = maxValue + '万円';

                cur.searchParams.set('min_price', minValue);
                cur.searchParams.set('max_price', maxValue);

                // クエリ型 paged を使ってる環境も潰す（念のため）
                cur.searchParams.delete('paged');

                window.location.href = cur.toString();
            };

            window.resetPriceRange = function() {
                const cur = new URL(window.location.href);
                stripPagedPath(cur);

                cur.searchParams.delete('min_price');
                cur.searchParams.delete('max_price');
                cur.searchParams.delete('paged');

                window.location.href = cur.toString();
            };
        })();
    </script>
<?php
}
add_action('wp_footer', 'add_price_range_widget_script');


?>