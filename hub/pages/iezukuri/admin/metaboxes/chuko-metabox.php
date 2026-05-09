<?php
/**
 * hub/pages/iezukuri/admin/metaboxes/chuko-metabox.php
 *
 * /iezukuri/chuko 専用入力
 *
 * 役割:
 * - 中古住宅・修理ページの本文入力だけを管理する。
 * - トップページには出さない。
 * - JSON入力欄は使わない。
 * - フロント描画はここではしない。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_admin_is_chuko_page')) {
    function naigai_iez_admin_is_chuko_page($post) {
        return $post && $post->post_type === 'page' && get_page_uri($post) === 'iezukuri/chuko';
    }
}

if (!function_exists('naigai_iez_admin_chuko_defaults')) {
    function naigai_iez_admin_chuko_defaults() {
        return array(
            '_ch_chuko_repair_kicker' => 'USED HOUSE REPAIR',
            '_ch_chuko_repair_title'  => '住み慣れた家は、見た目より先に「傷みの原因」を確認する。',
            '_ch_chuko_repair_text'   => '築年数が経過したお住まいの修理は、壁紙や床をきれいにする前に、雨漏り・屋根・外壁・床下・水回りなど、建物を傷める原因を確認することが大切です。原因を残したまま内装だけ直すと、後から修理範囲が広がり、費用も増えやすくなります。',

            '_ch_chuko_repair_card_1_title' => '雨漏り・屋根修理',
            '_ch_chuko_repair_card_1_text'  => '天井のシミ、屋根材の割れ、板金の浮き、雨樋の詰まりを確認します。雨漏りは内装より先に直す必要があります。',
            '_ch_chuko_repair_card_2_title' => '外壁・ひび割れ補修',
            '_ch_chuko_repair_card_2_text'  => '外壁の割れ、塗装の劣化、コーキング切れを確認します。水が入る前に補修して、構造部分の傷みを防ぎます。',
            '_ch_chuko_repair_card_3_title' => '床下・基礎の確認',
            '_ch_chuko_repair_card_3_text'  => '床の沈み、基礎のひび、湿気、白蟻、土台の腐食を確認します。住みながら直す場合も、床下確認は重要です。',
            '_ch_chuko_repair_card_4_title' => '水回り修理',
            '_ch_chuko_repair_card_4_text'  => 'キッチン、浴室、洗面、トイレ、給排水管の劣化を確認します。漏水や配管の古さは、内装より優先します。',
            '_ch_chuko_repair_card_5_title' => '断熱・窓まわり',
            '_ch_chuko_repair_card_5_text'  => '寒さ、結露、すきま風、窓の劣化を確認します。断熱や窓の改善は、暮らしやすさと光熱費に関わります。',
            '_ch_chuko_repair_card_6_title' => '内装・建具の補修',
            '_ch_chuko_repair_card_6_text'  => '壁、床、天井、ドア、収納、階段の傷みを確認します。表面の修理は、雨漏りや水回りの確認後に進めます。',

            '_ch_chuko_first_kicker' => 'FIRST CHECK',
            '_ch_chuko_first_title'  => '先に確認する場所',
            '_ch_chuko_first_text'   => '住宅リフォームを考える前に、まず「水が入る場所」「建物を支える場所」「生活に必要な設備」を確認します。この3つを見ずに内装工事を始めると、後からやり直しになることがあります。',
            '_ch_chuko_first_item_1_label' => '水が入る場所',
            '_ch_chuko_first_item_1_text'  => '屋根 / 外壁 / 窓 / 雨樋 / ベランダ',
            '_ch_chuko_first_item_2_label' => '建物を支える場所',
            '_ch_chuko_first_item_2_text'  => '基礎 / 土台 / 柱 / 床下 / 白蟻被害',
            '_ch_chuko_first_item_3_label' => '生活に必要な設備',
            '_ch_chuko_first_item_3_text'  => '給排水管 / 浴室 / トイレ / キッチン / 電気',

            '_ch_chuko_priority_kicker' => 'PRIORITY',
            '_ch_chuko_priority_title'  => '修理は、見た目ではなく優先順位で決める。',
            '_ch_chuko_priority_text'   => '全部を一度に直す必要はありません。ただし、放置すると建物に影響する部分は先に対応します。「今すぐ直す場所」と「後で直せる場所」を分けることが大切です。',
            '_ch_chuko_priority_item_1_badge' => '最優先',
            '_ch_chuko_priority_item_1_title' => '雨漏り・漏水・構造の傷み',
            '_ch_chuko_priority_item_1_text'  => '放置すると修理範囲が広がるため、最初に確認します。',
            '_ch_chuko_priority_item_2_badge' => '次に確認',
            '_ch_chuko_priority_item_2_title' => '屋根・外壁・床下・水回り',
            '_ch_chuko_priority_item_2_text'  => '生活への影響と建物への負担を見て、順番を決めます。',
            '_ch_chuko_priority_item_3_badge' => '計画して進める',
            '_ch_chuko_priority_item_3_title' => '断熱・内装・間取り変更',
            '_ch_chuko_priority_item_3_text'  => '暮らしやすさを上げる工事は、予算に合わせて段階的に進めます。',

            '_ch_chuko_flow_kicker' => 'FLOW',
            '_ch_chuko_flow_title'  => '住まいの修理の進め方',
            '_ch_chuko_flow_text'   => '現地確認から、修理箇所の整理、優先順位、見積もり、工事までを段階的に進めます。',
            '_ch_chuko_flow_step_1_title' => '困っている場所を確認',
            '_ch_chuko_flow_step_1_text'  => '雨漏り、床の沈み、水回り、寒さ、外壁の傷みなどを聞き取ります。',
            '_ch_chuko_flow_step_2_title' => '現地で建物を確認',
            '_ch_chuko_flow_step_2_text'  => '屋根、外壁、室内、床下、水回り、建具などを確認します。',
            '_ch_chuko_flow_step_3_title' => '修理の優先順位を整理',
            '_ch_chuko_flow_step_3_text'  => '今すぐ必要な修理と、後からでもよい修理を分けます。',
            '_ch_chuko_flow_step_4_title' => '予算に合わせて工事内容を決める',
            '_ch_chuko_flow_step_4_text'  => '全部直すのか、段階的に直すのか、費用と内容を整理します。',

            '_ch_chuko_cost_kicker' => 'COST PLAN',
            '_ch_chuko_cost_title'  => '修理費用は、優先順位で整理する。',
            '_ch_chuko_cost_text'   => '経年劣化が気になる住まいは、ひとつの修理だけで終わらないことがあります。だから、屋根・外壁・水回り・内装を別々に考えるのではなく、先に全体を確認して「今すぐ直す工事」と「後でよい工事」に分けます。',
            '_ch_chuko_cost_item_1_title' => 'すぐ直す工事',
            '_ch_chuko_cost_item_1_text'  => '雨漏り、漏水、構造の傷みなど、放置すると被害が広がる工事。',
            '_ch_chuko_cost_item_2_title' => '近いうちに必要な工事',
            '_ch_chuko_cost_item_2_text'  => '屋根、外壁、水回り、床下など、劣化の進み方を見て計画する工事。',
            '_ch_chuko_cost_item_3_title' => '後からでもよい工事',
            '_ch_chuko_cost_item_3_text'  => '内装、収納、建具など、生活しながら段階的に進められる工事。',

            '_ch_chuko_prep_kicker' => 'BEFORE CONSULTATION',
            '_ch_chuko_prep_title'  => '相談前に、修理したい場所を整理する。',
            '_ch_chuko_prep_text'   => '住まいの修理は、現地確認の前に「どこが気になるか」「いつから症状があるか」を整理しておくと、必要な修理と後回しにできる工事を判断しやすくなります。',
            '_ch_chuko_prep_item_1_title' => '気になる症状',
            '_ch_chuko_prep_item_1_text'  => '雨漏り、床の沈み、外壁のひび、水回りの漏水、寒さ、結露など。',
            '_ch_chuko_prep_item_2_title' => '症状が出た時期',
            '_ch_chuko_prep_item_2_text'  => 'いつから気になっているか、雨の日だけか、冬だけかなどを整理します。',
            '_ch_chuko_prep_item_3_title' => '過去の修理履歴',
            '_ch_chuko_prep_item_3_text'  => '屋根、外壁、水回り、給排水管、白蟻対策など、過去に直した場所を確認します。',
            '_ch_chuko_prep_item_4_title' => '予算と優先順位',
            '_ch_chuko_prep_item_4_text'  => '全部直すのか、今必要な場所だけ直すのか、段階的に進めるのかを考えます。',

            '_ch_chuko_compare_kicker' => 'REPAIR OR RENOVATION',
            '_ch_chuko_compare_title'  => '修理とリノベーションは分けて考える。',
            '_ch_chuko_compare_text'   => '年数が経過したお住まいでは、まず建物を守るための「修理」を優先します。その上で、暮らしやすさを変える「リノベーション」を計画します。同時に考えることはできますが、目的と優先順位は分けて整理します。',
            '_ch_chuko_compare_row_1_label'      => '目的',
            '_ch_chuko_compare_row_1_repair'     => '傷みや不具合を直して、家を安全に使える状態に戻す。',
            '_ch_chuko_compare_row_1_renovation' => '間取り・内装・設備を変えて、暮らしやすさを上げる。',
            '_ch_chuko_compare_row_2_label'      => '優先度',
            '_ch_chuko_compare_row_2_repair'     => '雨漏り、漏水、床下、屋根、外壁などは先に確認する。',
            '_ch_chuko_compare_row_2_renovation' => '修理が必要な場所を確認した後、予算に合わせて計画する。',
            '_ch_chuko_compare_row_3_label'      => '対象',
            '_ch_chuko_compare_row_3_repair'     => '屋根、外壁、基礎、床下、水回り、配管、断熱、建具。',
            '_ch_chuko_compare_row_3_renovation' => '間取り変更、内装、収納、キッチン交換、浴室交換、デザイン変更。',
            '_ch_chuko_compare_row_4_label'      => '判断基準',
            '_ch_chuko_compare_row_4_repair'     => '放置すると被害が広がるか。生活に支障があるか。',
            '_ch_chuko_compare_row_4_renovation' => '暮らし方に合うか。使いやすくなるか。将来の使い方に合うか。',
            '_ch_chuko_compare_row_5_label'      => '進め方',
            '_ch_chuko_compare_row_5_repair'     => '現地確認 → 劣化箇所の整理 → 優先順位 → 必要な修理。',
            '_ch_chuko_compare_row_5_renovation' => '希望整理 → 修理範囲確認 → 予算調整 → 改修内容を決定。',
            '_ch_chuko_compare_note' => '※ 先に修理が必要な場所を確認すると、リノベーションのやり直しや追加費用を防ぎやすくなります。',
        );
    }
}

if (!function_exists('naigai_iez_admin_chuko_field_map')) {
    function naigai_iez_admin_chuko_field_map() {
        $fields = array();

        foreach (array_keys(naigai_iez_admin_chuko_defaults()) as $key) {
            if (preg_match('/(_text|_note|_repair|_renovation)$/', $key)) {
                $fields[$key] = 'textarea';
            } else {
                $fields[$key] = 'text';
            }
        }

        return $fields;
    }
}

add_filter('naigai_iez_admin_fixed_page_fields', function ($fields, $post) {
    if (!naigai_iez_admin_is_chuko_page($post)) {
        return $fields;
    }

    return array_merge($fields, naigai_iez_admin_chuko_field_map());
}, 10, 2);

if (!function_exists('naigai_iez_admin_chuko_value')) {
    function naigai_iez_admin_chuko_value($post, $key) {
        $value = get_post_meta($post->ID, $key, true);

        if ($value !== '') {
            return $value;
        }

        $defaults = naigai_iez_admin_chuko_defaults();

        return isset($defaults[$key]) ? $defaults[$key] : '';
    }
}

if (!function_exists('naigai_iez_admin_chuko_text_input')) {
    function naigai_iez_admin_chuko_text_input($post, $key, $label) {
        naigai_iez_admin_text_input($key, $label, naigai_iez_admin_chuko_value($post, $key));
    }
}

if (!function_exists('naigai_iez_admin_chuko_textarea')) {
    function naigai_iez_admin_chuko_textarea($post, $key, $label, $rows = 3) {
        naigai_iez_admin_textarea($key, $label, naigai_iez_admin_chuko_value($post, $key), $rows);
    }
}

if (!function_exists('naigai_iez_admin_chuko_section_start')) {
    function naigai_iez_admin_chuko_section_start($post, $prefix, $title) {
        ?>
        <details class="naigai-iez-admin-subsection" open>
            <summary><strong><?php echo esc_html($title); ?></strong></summary>
            <table class="form-table naigai-iez-admin-table">
                <tbody>
                    <?php
                    naigai_iez_admin_chuko_text_input($post, "_ch_chuko_{$prefix}_kicker", 'キッカー');
                    naigai_iez_admin_chuko_text_input($post, "_ch_chuko_{$prefix}_title", '見出し');
                    naigai_iez_admin_chuko_textarea($post, "_ch_chuko_{$prefix}_text", '本文', 4);
                    ?>
                </tbody>
            </table>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_render_chuko_input')) {
    function naigai_iez_admin_render_chuko_input($post) {
        ?>
        <div class="naigai-iez-admin-section">
            <h3>中古住宅・修理本文</h3>
            <p class="description">/iezukuri/chuko 専用。</p>

            <?php naigai_iez_admin_chuko_section_start($post, 'repair', '01 修理チェック'); ?>
                <?php for ($i = 1; $i <= 6; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_repair_card_{$i}_title", "カード{$i} 見出し");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_repair_card_{$i}_text", "カード{$i} 本文", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'first', '02 先に確認する場所'); ?>
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_first_item_{$i}_label", "項目{$i} 名");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_first_item_{$i}_text", "項目{$i} 内容", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'priority', '03 優先順位'); ?>
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_priority_item_{$i}_badge", "項目{$i} ラベル");
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_priority_item_{$i}_title", "項目{$i} 見出し");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_priority_item_{$i}_text", "項目{$i} 本文", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'flow', '04 進め方'); ?>
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_flow_step_{$i}_title", "STEP{$i} 見出し");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_flow_step_{$i}_text", "STEP{$i} 本文", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'cost', '05 費用整理'); ?>
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_cost_item_{$i}_title", "項目{$i} 見出し");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_cost_item_{$i}_text", "項目{$i} 本文", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'prep', '06 相談前準備'); ?>
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_prep_item_{$i}_title", "項目{$i} 見出し");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_prep_item_{$i}_text", "項目{$i} 本文", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>
            </details>

            <?php naigai_iez_admin_chuko_section_start($post, 'compare', '07 修理とリノベーション比較'); ?>
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <table class="form-table naigai-iez-admin-table"><tbody>
                        <?php
                        naigai_iez_admin_chuko_text_input($post, "_ch_chuko_compare_row_{$i}_label", "行{$i} 項目");
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_compare_row_{$i}_repair", "行{$i} 修理・補修", 2);
                        naigai_iez_admin_chuko_textarea($post, "_ch_chuko_compare_row_{$i}_renovation", "行{$i} リノベーション", 2);
                        ?>
                    </tbody></table>
                <?php endfor; ?>

                <table class="form-table naigai-iez-admin-table"><tbody>
                    <?php naigai_iez_admin_chuko_textarea($post, '_ch_chuko_compare_note', '注釈', 2); ?>
                </tbody></table>
            </details>
        </div>
        <?php
    }
}
