<?php
if (!defined('ABSPATH')) { exit; }

$service_items = array(
    array(
        'term' => 'one-family',
        'tax_terms' => array('one-family'),
        'num' => '01',
        'title' => '自分たちだけの間取りで建てる',
        'subtitle' => '新築プラン',
        'text' => '土地、駐車スペース、庭、収納、ワークスペースまで含めて、暮らし方から一から組み立てるプランです。',
        'url' => naigai_iez_top_page_url('iezukuri/new-house'),
        'plan_url' => naigai_iez_top_page_url('iezukuri/plans'),
        'image' => naigai_iez_top_page_image('iezukuri/new-house'),
        'story_title' => '土地と暮らし方から、間取りを組み立てる。',
        'story_text' => '在宅ワーク、趣味収納、車の台数、庭とのつながり、休日の過ごし方まで含めて、家族に合う間取りを整理します。那須で広めの住空間を取りたい方、自然やゴルフ、二拠点生活、サテライトオフィスを考えたい方にも向いています。',
        'features' => array('土地・庭・駐車場まで一体で計画', '書斎や趣味収納を入れやすい', '生活動線を一から整理できる'),
        'equipment' => array('断熱・窓・換気を最初から計画', 'コンセントや照明位置を暮らしに合わせる', '将来のメンテナンスを想定しやすい'),
    ),
    array(
        'term' => 'two-family',
        'tax_terms' => array('two-family'),
        'num' => '02',
        'title' => '家族構成に合わせて暮らす',
        'subtitle' => '二世帯・将来対応プラン',
        'text' => '収納量、トイレ台数、駐車スペース、水回り、建具、生活時間の違いを整理するプランです。',
        'url' => naigai_iez_top_page_url('iezukuri/two-family'),
        'plan_url' => naigai_iez_top_page_url('iezukuri/plans'),
        'image' => naigai_iez_top_page_image('iezukuri/two-family'),
        'story_title' => '家族の距離感から、間取りを考える。',
        'story_text' => '玄関、水回り、トイレの数、収納、駐車スペース、生活時間の違いまで整理します。子どもの勉強道具や書籍、アイロンや洗濯、送り迎え、将来の介護や独立まで見ながら、長く使える住まいを考えます。',
        'features' => array('玄関・水回りの共有/分離を整理', '収納とトイレ台数を家族構成に合わせる', 'バリアフリー建具や引き戸も検討'),
        'equipment' => array('生活時間差に配慮した設備計画', '家事効率を上げる水回り配置', '将来の介護や独立にも対応'),
    ),
    array(
        'term' => 'used-renovation',
        'tax_terms' => array('used-renovation'),
        'num' => '03',
        'title' => '今ある住まいを整える',
        'subtitle' => 'リフォーム・リノベーションプラン',
        'text' => '台所、収納、トイレ、水回り、湿気対策、壁塗り、外回りの修理など、今ある住まいを活かすプランです。',
        'url' => naigai_iez_top_page_url('iezukuri/renovation'),
        'plan_url' => naigai_iez_top_page_url('iezukuri/plans'),
        'image' => naigai_iez_top_page_image('iezukuri/renovation'),
        'story_title' => '修理だけで終わらせず、使える場所を増やす。',
        'story_text' => '古い部分をすべて壊すのではなく、必要な修理と暮らしやすくする改善を分けて考えます。使っていなかった部屋や収納を整えることで、趣味、作業、家族のためのスペースとして使える余白も生まれます。',
        'features' => array('既存構造を見ながら動線を改善', '台所・収納・トイレを使いやすくする', '使っていない空間を趣味や作業に活用'),
        'equipment' => array('水回り修理・補修を優先', '湿気対策や壁塗りを検討', 'メンテナンスしやすい状態に整える'),
    ),
);

$compare_rows = array(
    array('label' => '考え方', 'one-family' => '土地と暮らし方から一から設計', 'two-family' => '家族構成と距離感を整理', 'used-renovation' => '今ある家を活かして整える'),
    array('label' => 'ライフスタイル', 'one-family' => '自然、ゴルフ、二拠点生活、サテライトオフィス', 'two-family' => '家事効率、子育て、勉強、収納、親世帯との距離感', 'used-renovation' => '趣味部屋、収納改善、水回り修理、メンテナンス強化'),
    array('label' => '間取り', 'one-family' => '駐車場、庭、LDK、書斎、収納を自由に計画', 'two-family' => '玄関、水回り、トイレ台数、共有と分離を整理', 'used-renovation' => '既存構造を見ながら動線と収納を改善'),
    array('label' => '設備', 'one-family' => '断熱、窓、換気、コンセントを最初から計画', 'two-family' => '世帯ごとの使い方、生活時間差に配慮', 'used-renovation' => '台所、トイレ、浴室、湿気対策、壁塗りを優先'),
    array('label' => '将来性', 'one-family' => '家族の変化に合わせた余白を残す', 'two-family' => '介護、独立、バリアフリー建具、引き戸に対応', 'used-renovation' => '修理と改善を分けて、長く使いやすくする'),
);
