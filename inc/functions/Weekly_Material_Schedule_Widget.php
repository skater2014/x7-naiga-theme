
<?php
if (!defined('ABSPATH')) exit;

/**
 * GW Weekly Schedule REST API
 * /wp-json/gw/v1/weekly-schedule
 */
add_action('rest_api_init', function () {
    register_rest_route('gw/v1', '/weekly-schedule', [
        'methods'  => 'GET',
        'callback' => 'gw_weekly_schedule_api',
        'permission_callback' => '__return_true',
    ]);
});

function gw_weekly_schedule_api(WP_REST_Request $req) {

    // timezone
    $tz = $req->get_param('tz') ?: 'Asia/Tokyo';
    try {
        date_default_timezone_set($tz);
    } catch (Throwable $e) {
        date_default_timezone_set('Asia/Tokyo');
        $tz = 'Asia/Tokyo';
    }

    // ウィジェットと同じデータ関数を使う
    if (
        !function_exists('get_weekly_materials_data') ||
        !function_exists('get_weekly_weapon_materials_data')
    ) {
        return new WP_REST_Response([
            'ok' => false,
            'error' => 'weekly data functions are missing',
        ], 500);
    }

    $today = date('l');

    $materials = get_weekly_materials_data();
    $weapons   = get_weekly_weapon_materials_data();

    // 今日分だけ抽出（Sundayは全表示）
    $materials_today = [];
    foreach ($materials as $set) {
        $days = explode(', ', $set['days']);
        if ($today === 'Sunday' || in_array($today, $days, true)) {
            $materials_today[] = $set;
        }
    }

    $weapons_today = [];
    foreach ($weapons as $w) {
        $days = explode(', ', $w['days']);
        if ($today === 'Sunday' || in_array($today, $days, true)) {
            $weapons_today[] = $w;
        }
    }

    return new WP_REST_Response([
        'ok'           => true,
        'timezone'     => $tz,
        'today'        => $today,
        'generated_at' => gmdate('c'),
        'materials'    => array_values($materials_today),
        'weapons'      => array_values($weapons_today),
    ], 200);
}


class Weekly_Material_Schedule_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'weekly-material-schedule',
            'Weekly Material Schedule',
            array('description' => 'Display the weekly material farming schedule.')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        if ($instance['title']) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        date_default_timezone_set('Asia/Tokyo');
        $today = date('l');
        $weekly_materials_data = get_weekly_materials_data();
        $weekly_weapon_materials_data = get_weekly_weapon_materials_data();
        echo "<script>const today = '{$today}';</script>";
        echo '<div id="farmingSchedule"></div>';
        echo '<div class="farming-schedule">';

        display_weekly_materials($weekly_materials_data, $today);
        display_weekly_weapon_materials($weekly_weapon_materials_data, $today);

            echo '
    <script>
        document.addEventListener(\'DOMContentLoaded\', function() {
            function updateDateTime() {
                const farmingScheduleElement = document.getElementById(\'farmingSchedule\');
                if (farmingScheduleElement) {
                    const now = new Date();
                    const options = {
                        weekday: \'long\',
                        year: \'numeric\',
                        month: \'long\',
                        day: \'numeric\',
                        hour: \'numeric\',
                        minute: \'numeric\',
                        second: \'numeric\',
                        timeZoneName: \'short\'
                    };
                    // 曜日を英語で取得するためにen-US ja-JPは日本語
                    const localDateTime = now.toLocaleString(undefined, options);
                    const dayOfWeek = now.toLocaleString(\'en-US\', { weekday: \'long\' });

                    // タイトルを曜日に応じて変更
                    farmingScheduleElement.innerHTML = \'<h2>Farmable \' + dayOfWeek + \'</h2><p>Today is \' + localDateTime + \'</p>\';
                }
            }

            setInterval(updateDateTime, 1000);
            updateDateTime();
        });
    </script>
';



        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'text_domain'); ?>
            </label>
            <input
                class="widefat"
                id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

function display_weekly_materials($data, $today) {
    $displayedIcons = [];
    $farmingSectionDisplayed = false;

    foreach ($data as $set) {
        if ($today === 'Sunday' || in_array($today, explode(', ', $set['days']))) {
            if (!$farmingSectionDisplayed) {
                echo "<div class='farming-section characters'>";
                $farmingSectionDisplayed = true;
                echo "  <div class='farming-list'>";
            }

            echo "    <div class='farming-list-item'>";
            echo "      <div class='farming-icon-wrapper'>";
            echo "        <img alt='{$set['name']}' class='farming-icon' src='https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Farming/{$set['name']}.png'>";
            echo "        <p>{$set['name']}</p>";
            echo "      </div>";
            echo "      <div class='farming-characters'>";

            foreach ($set['characters'] as $character) {
                echo "<a href='https://gamewidth.net/genshin-impact-" . lcfirst(str_replace(' ', '-', $character['name'])) . "-best-build' class='tierlist-portrait' style='order:-{$character['rarity']}'>";
                echo "            <img alt='{$character['name']}' class='tierlist-icon rarity-{$character['rarity']}' src='{$character['image']}'>";
                echo "            <img alt='{$character['element']}' class='tierlist-type' src='https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Characters/Element_{$character['element']}.png'>";
                echo "          </a>";
            }

            echo "      </div>";
            echo "    </div>";

            $iconKey = $set['name'];
            $displayedIcons[] = $iconKey;
        }
    }

    if ($farmingSectionDisplayed) {
        echo "  </div>";
        echo "</div>";
    }
}

function display_weekly_weapon_materials($data, $today) {

    // ===== 武器アセンション素材マスターデータ（そのまま表示） =====
    $weaponMaterials = [
        ['name' => 'Branch', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Branch.png'],
        ['name' => 'Decarabian', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Decarabian.png'],
        ['name' => 'Guyun', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Guyun.png'],
        ['name' => 'Talisman of the Forest Dew', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Talisman_of_the_Forest_Dew.png'],
        ['name' => 'Boreal Wolf', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Boreal_Wolf.png'],
        ['name' => 'Mist Veiled Elixir', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Mist_Veiled_Elixir.png'],
        ['name' => 'Narukami', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Narukami.png'],
        ['name' => 'Oasis Garden', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Oasis_Garden.png'],
        ['name' => 'Aerosiderite', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Aerosiderite.png'],
        ['name' => 'Dandelion Gladiator', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Dandelion_Gladiator.png'],
        ['name' => 'Mask', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Mask.png'],
        ['name' => 'Scorching Might', 'image' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/Scorching_Might.png'],
    ];

    echo "<div class='farming-section weapons'>";
    echo "<div class='farming-list'>";

    foreach ($weaponMaterials as $weapon) {
        echo '<div class="farming-list-item">';
        echo '  <div class="farming-icon-wrapper">';
        echo '    <img class="farming-icon" src="' . esc_url($weapon['image']) . '" alt="' . esc_attr($weapon['name']) . '">';
        echo '    <p>' . esc_html($weapon['name']) . '</p>';
        echo '  </div>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}

