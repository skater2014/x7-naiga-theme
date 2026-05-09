<?php
/*
 * Template Name: AdSense Data
 * Description: Template for displaying Google AdSense data.
 */

// WordPressヘッダーを取得
get_header();





// 開始日と終了日のデフォルト値を設定
$start_date_default = '2024-01-01';
$end_date_default = date('Y-m-d');

// 開始日と終了日の値を取得（デフォルト値を使用）
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : $start_date_default;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $end_date_default;




// WordPressデータベースオブジェクトを取得
global $wpdb;

// Google AdSenseデータを取得
$results = $wpdb->get_results("SELECT date, estimated_earnings, page_views, page_impression_earnings, impressions, impression_earnings, active_view_visibility, clicks FROM new_adcense_report_partitioned WHERE date BETWEEN '$start_date' AND '$end_date'");

// フィルターで選択されたメトリックスを取得（デフォルトはページビューとクリック数）
$selected_metric_left = isset($_GET['left_metric']) ? $_GET['left_metric'] : 'page_views';
$selected_metric_right = isset($_GET['right_metric']) ? $_GET['right_metric'] : 'clicks';

// 左右のメトリックスに基づいてデータを準備
$left_data = [];
$right_data = [];

// データを処理して左右のデータを作成
foreach ($results as $result) {
    // 選択されたメトリックに応じてデータを取得し、左右のデータを準備
    $left_data[] = $result->{$selected_metric_left} ?: 'null';
    $right_data[] = $result->{$selected_metric_right} ?: 'null';
}

?>





<!-- グラフ描画用のCanvas要素 https://www.chartjs.org/docs/latest/getting-started/-->
<canvas id="myChart" width="800" height="400"></canvas>

<!-- Chart.jsライブラリの読み込み -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Canvas要素を取得
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart;

    // グラフを描画する関数
    function drawChart(leftData, rightData, labels) {
        if (myChart) {
            myChart.destroy(); // 以前のグラフを破棄
        }

        // 日付文字列からDateオブジェクトに変換
    var dateLabels = labels.map(function(label) {
        return new Date(label);
    });
        // Chart.jsを使ってグラフを描画する
        myChart = new Chart(ctx, {
            type: 'line', // グラフのタイプ（折れ線グラフ）
            data: {
    			labels: dateLabels, // 修正が必要
                datasets: [{
                    label: '<?php echo $selected_metric_left; ?>', // 左側のメトリックスのラベル
                    data: leftData, // 左側のデータ
                    fill: false, // 下の領域を塗りつぶさない
                    backgroundColor: 'rgba(54, 162, 235, 0.2)', // 折れ線の色
                    borderColor: 'rgba(54, 162, 235, 1)', // 折れ線の色
                    borderWidth: 1 // 折れ線の幅
                }, {
                    label: '<?php echo $selected_metric_right; ?>', // 右側のメトリックスのラベル
                    data: rightData, // 右側のデータ
                    fill: false,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
			options: {
			    responsive: true, // グラフのレスポンシブ対応を無効にする
			    scales: {
			        y: {
			            beginAtZero: true // Y軸を0から開始する
			        },
			        x: {
			            type: 'category', // X軸をカテゴリー軸（日付軸）に設定
			            labels: labels // 日付のラベルデータを指定
			        }
			    }
			}

        });
    }

    // 開始日と終了日の値を取得（デフォルト値を使用）
    var start_date = '<?php echo $start_date; ?>';
    var end_date = '<?php echo $end_date; ?>';

    // ページが読み込まれた時に実行される処理
    document.addEventListener('DOMContentLoaded', function() {
        // 初期のグラフを描画
        drawChart(<?php echo json_encode($left_data); ?>, <?php echo json_encode($right_data); ?>, <?php echo json_encode(array_column($results, 'date')); ?>);

        // 日付フィルターが変更された時に実行される処理
        document.getElementById('start_date').addEventListener('change', function() {
            // 開始日を更新
            start_date = this.value;
            // グラフを更新
            updateChart();
        });

        document.getElementById('end_date').addEventListener('change', function() {
            // 終了日を更新
            end_date = this.value;
            // グラフを更新
            updateChart();
        });

		// グラフを更新する関数
		//function updateChart() {
		    // 選択されたメトリックスに基づいてデータを再取得
		    //fetch('data.php?start_date=' + start_date + '&end_date=' + end_date)
		        //.then(response => {
		            //if (!response.ok) {
		                //throw new Error('Network response was not ok');
		            //}
		            //return response.json();
		        //})
		        //.then(data => {
		            // グラフのデータを更新して再描画
		            //drawChart(data.leftData, data.rightData, data.labels);
		        //})
		        //.catch(error => {
		            //console.error('Error fetching chart data:', error);
		        //});
		//}


    });
</script>

<!-- 日付フィルター -->
<form method="get">
	<label for="start_date" class="date-label">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">

	<label for="end_date" class="date-label">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">


	<label for="left_metric" class="metric-label">Left Metric:</label>
    <select name="left_metric" id="left_metric">
        <option value="estimated_earnings" <?php if ($selected_metric_left === 'estimated_earnings') echo 'selected="selected"'; ?>>Estimated Earnings</option>
        <option value="page_views" <?php if ($selected_metric_left === 'page_views') echo 'selected="selected"'; ?>>Page Views</option>
        <option value="page_impression_earnings" <?php if ($selected_metric_left === 'page_impression_earnings') echo 'selected="selected"'; ?>>Page Impression Earnings</option>
        <option value="impressions" <?php if ($selected_metric_left === 'impressions') echo 'selected="selected"'; ?>>Impressions</option>
        <option value="impression_earnings" <?php if ($selected_metric_left === 'impression_earnings') echo 'selected="selected"'; ?>>Impression Earnings</option>
        <option value="active_view_visibility" <?php if ($selected_metric_left === 'active_view_visibility') echo 'selected="selected"'; ?>>Active View Visibility</option>
    </select>

	<label for="right_metric" class="metric-label">Right Metric:</label>
    <select name="right_metric" id="right_metric">
        <option value="estimated_earnings" <?php if ($selected_metric_right === 'estimated_earnings') echo 'selected="selected"'; ?>>Estimated Earnings</option>
        <option value="page_views" <?php if ($selected_metric_right === 'page_views') echo 'selected="selected"'; ?>>Page Views</option>
        <option value="page_impression_earnings" <?php if ($selected_metric_right === 'page_impression_earnings') echo 'selected="selected"'; ?>>Page Impression Earnings</option>
        <option value="impressions" <?php if ($selected_metric_right === 'impressions') echo 'selected="selected"'; ?>>Impressions</option>
        <option value="impression_earnings" <?php if ($selected_metric_right === 'impression_earnings') echo 'selected="selected"'; ?>>Impression Earnings</option>
        <option value="active_view_visibility" <?php if ($selected_metric_right === 'active_view_visibility') echo 'selected="selected"'; ?>>Active View Visibility</option>
    </select>
    <input type="submit" value="Apply Filter" class="submit submit-button">
</form>

<link href="https://cdn.datatables.net/v/bs5/dt-2.0.5/datatables.min.css" rel="stylesheet">


<?php

// Google AdSenseデータを取得
$results = $wpdb->get_results("SELECT date, estimated_earnings, page_views, page_impression_earnings, impressions, impression_earnings, active_view_visibility, clicks FROM new_adcense_report WHERE date != '0000-00-00'");

// $table_dataを初期化
$table_data = [];

// データを処理して$table_dataに格納
foreach ($results as $result) {
    $table_data[] = [
        'date' => $result->date,
        'estimated_earnings' => $result->estimated_earnings,
        'page_views' => $result->page_views,
        'page_impression_earnings' => $result->page_impression_earnings,
        'impressions' => $result->impressions,
        'impression_earnings' => $result->impression_earnings,
        'active_view_visibility' => $result->active_view_visibility,
        'clicks' => $result->clicks
    ];
}

?>

<?php
// 合計値の初期化
$total_estimated_earnings = 0;
$total_page_views = 0;
$total_page_impression_earnings = 0;
$total_impressions = 0;
$total_impression_earnings = 0;
$total_active_view_visibility = 0;
$total_clicks = 0;

// データを処理して合計値を計算
foreach ($table_data as $row) {
    $total_estimated_earnings += $row['estimated_earnings'];
    $total_page_views += $row['page_views'];
    $total_page_impression_earnings += $row['page_impression_earnings'];
    $total_impressions += $row['impressions'];
    $total_impression_earnings += $row['impression_earnings'];
    $total_active_view_visibility += $row['active_view_visibility'];
    $total_clicks += $row['clicks'];
}
?>
<div class="custom-table-responsive">
    <table id="dataTable" class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Estimated Earnings</th>
                <th>Page Views</th>
                <th>Page Impression Earnings</th>
                <th>Impressions</th>
                <th>Impression Earnings</th>
                <th>Active View Visibility</th>
                <th>Clicks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($table_data as $index => $row): ?>
                <?php if ($row['date'] !== 'Total'): ?>
                    <!-- 通常のデータ行 -->
                    <tr>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['estimated_earnings']; ?></td>
                        <td><?php echo $row['page_views']; ?></td>
                        <td><?php echo $row['page_impression_earnings']; ?></td>
                        <td><?php echo $row['impressions']; ?></td>
                        <td><?php echo $row['impression_earnings']; ?></td>
                        <td><?php echo $row['active_view_visibility']; ?></td>
                        <td><?php echo $row['clicks']; ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
              <!-- 合計行を挿入 -->
            <tr>
                <td>Total</td>
                <td><?php echo $total_estimated_earnings; ?></td>
                <td><?php echo $total_page_views; ?></td>
                <td><?php echo $total_page_impression_earnings; ?></td>
                <td><?php echo $total_impressions; ?></td>
                <td><?php echo $total_impression_earnings; ?></td>
                <td>N/A</td>
                <td><?php echo $total_clicks; ?></td>
            </tr>
        </tbody>
    </table>
</div>





<?php
// WordPressフッターを取得
get_footer();
?>
