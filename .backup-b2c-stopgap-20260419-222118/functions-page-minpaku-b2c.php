<?php
if (!defined('ABSPATH')) {
    exit;
}

/* =========================================================
 * 民泊B2C共通LP メタボックス
 * =========================================================
 *
 * このファイルの役割:
 * - page-minpaku-b2c.php 用の管理画面入力欄を出す
 * - slugごとの初期文書を返す
 * - 画像選択UIを管理画面で使えるようにする
 * - 保存処理をまとめる
 * - フロント側で B2C 用 CSS を読む
 *
 * 今回の修正方針:
 * - 下部導線ボタン（bottom nav）機能は完全削除
 * - それに関わる meta_key / helper / 入力欄を削除
 * - PHP 構文エラーの原因だった「関数内の生HTML断片」をなくす
 * - 管理画面用 helper は echo / printf で安全に出力する
 * ========================================================= */

/* =========================================================
 * 対象テンプレート判定
 * ========================================================= */
if (!function_exists('mpb_is_target_template')) {
    function mpb_is_target_template($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === 'page-minpaku-b2c.php';
    }
}

/* =========================================================
 * 固定ページ slug 取得
 * ========================================================= */
if (!function_exists('mpb_get_page_slug')) {
    function mpb_get_page_slug($post_id)
    {
        $post = get_post($post_id);

        if (!$post instanceof WP_Post || $post->post_type !== 'page') {
            return '';
        }

        return (string) $post->post_name;
    }
}

/* =========================================================
 * slug から固定ページIDを探す
 * ========================================================= */
if (!function_exists('mpb_find_page_id_by_slug')) {
    function mpb_find_page_id_by_slug($slug)
    {
        if ($slug === '') {
            return 0;
        }

        $page = get_page_by_path($slug, OBJECT, 'page');

        return ($page instanceof WP_Post) ? (int) $page->ID : 0;
    }
}

/* =========================================================
 * slug ごとの初期文書
 * 保存値が空のときだけ使う
 * ========================================================= */
if (!function_exists('mpb_default_content_map')) {
    function mpb_default_content_map()
    {
        return array(
            'minpaku-guide' => array(
                '_mpb_hero_eyebrow'   => 'MINPAKU STAY',
                '_mpb_hero_title'     => '那須での過ごし方と宿泊の楽しみ方',
                '_mpb_hero_lead'      => '自然の中でゆっくり過ごしたい方、家族やグループで気兼ねなく滞在したい方へ。那須の民泊・一棟貸しを選ぶ前に、過ごし方のイメージをわかりやすく整理します。',
                '_mpb_hero_btn1_text' => 'よくある質問を見る',
                '_mpb_hero_btn2_text' => 'ご利用案内を見る',

                '_mpb_intro_eyebrow' => 'Guide',
                '_mpb_intro_title'   => '宿泊先選びの前に、那須でどう過ごしたいかを考える',
                '_mpb_intro_text'    => '宿泊先の設備や料金だけでなく、滞在中にどんな時間を過ごしたいかを先に整理しておくと、自分たちに合う民泊・一棟貸しを選びやすくなります。',

                '_mpb_feature1_eyebrow' => 'Nature',
                '_mpb_feature1_title'   => '自然の中でゆっくり滞在する',
                '_mpb_feature1_text'    => '那須は、観光だけでなく、静かな時間を過ごしたい方にも向いています。朝の空気や景色を楽しみながら、宿でゆっくり過ごす滞在にも相性のよいエリアです。',

                '_mpb_feature2_eyebrow' => 'Family & Group',
                '_mpb_feature2_title'   => '家族やグループで使いやすい宿泊スタイル',
                '_mpb_feature2_text'    => '一棟貸しや貸別荘タイプなら、複数人でも空間を使いやすく、食事やくつろぎの時間もまとめて過ごしやすくなります。',

                '_mpb_feature3_eyebrow' => 'Workation',
                '_mpb_feature3_title'   => '前泊やワーケーションにも対応しやすい',
                '_mpb_feature3_text'    => '観光の前後泊だけでなく、自然の中で仕事と滞在を両立したい方にも、民泊や一棟貸しは選択肢になります。',

                '_mpb_cta_title'     => '那須での過ごし方に合う宿泊先を探す',
                '_mpb_cta_text'      => '過ごし方のイメージが固まったら、宿泊施設一覧やご利用案内もあわせてご確認ください。',
                '_mpb_cta_btn1_text' => 'よくある質問を見る',
                '_mpb_cta_btn2_text' => 'ご利用案内を見る',
            ),

            /* guide系ページは guide と同じ初期文書で使えるようにしておく */
            'minpaku-family' => array(
                '_mpb_hero_eyebrow'   => 'MINPAKU STAY',
                '_mpb_hero_title'     => '家族で那須の民泊・一棟貸しに泊まる',
                '_mpb_hero_lead'      => '家族旅行や三世代旅行でも、周囲に気をつかいすぎずに過ごしやすい宿泊スタイルを整理します。',
                '_mpb_hero_btn1_text' => 'よくある質問を見る',
                '_mpb_hero_btn2_text' => 'ご利用案内を見る',
            ),
            'minpaku-group' => array(
                '_mpb_hero_eyebrow'   => 'MINPAKU STAY',
                '_mpb_hero_title'     => 'グループで那須の民泊・一棟貸しに泊まる',
                '_mpb_hero_lead'      => '友人同士や複数人の旅行で、まとまって過ごしやすい宿泊スタイルを整理します。',
                '_mpb_hero_btn1_text' => 'よくある質問を見る',
                '_mpb_hero_btn2_text' => 'ご利用案内を見る',
            ),
            'minpaku-workation' => array(
                '_mpb_hero_eyebrow'   => 'MINPAKU STAY',
                '_mpb_hero_title'     => '那須でワーケーション滞在を考える',
                '_mpb_hero_lead'      => '自然の中で仕事と滞在を両立したい方に向けて、民泊・一棟貸しの使い方を整理します。',
                '_mpb_hero_btn1_text' => 'よくある質問を見る',
                '_mpb_hero_btn2_text' => 'ご利用案内を見る',
            ),

            'minpaku-campaign' => array(
                '_mpb_hero_eyebrow'   => 'MINPAKU STAY',
                '_mpb_hero_title'     => '那須の民泊・一棟貸しのお得情報',
                '_mpb_hero_lead'      => '宿泊先を選ぶときは、料金だけでなく、定員、設備、滞在日数、使い方まで見ながら比較することが大切です。予約前に見ておきたいポイントを整理します。',
                '_mpb_hero_btn1_text' => '違いを見る',
                '_mpb_hero_btn2_text' => 'よくある質問を見る',

                '_mpb_intro_eyebrow' => 'Campaign',
                '_mpb_intro_title'   => '予約前に確認しておきたい比較ポイント',
                '_mpb_intro_text'    => 'お得に見える宿泊先でも、人数や設備、必要な滞在条件によって使いやすさは変わります。宿泊料金だけでなく、全体の条件を見ながら選ぶのがおすすめです。',

                '_mpb_feature1_eyebrow' => 'Capacity',
                '_mpb_feature1_title'   => '人数と広さのバランスを確認する',
                '_mpb_feature1_text'    => '家族やグループで泊まる場合は、最大人数だけでなく、寝室数やベッド数も見ておくと、実際の使いやすさを判断しやすくなります。',

                '_mpb_feature2_eyebrow' => 'Equipment',
                '_mpb_feature2_title'   => '設備と滞在スタイルを見比べる',
                '_mpb_feature2_text'    => 'キッチン、駐車場、チェックイン条件など、必要な設備や条件を先に整理しておくと、滞在中のミスマッチを防ぎやすくなります。',

                '_mpb_feature3_eyebrow' => 'Season',
                '_mpb_feature3_title'   => '季節や目的に応じた選び方を意識する',
                '_mpb_feature3_text'    => '自然を楽しみたい時期、家族旅行、短期滞在など、目的に応じて宿泊先の選び方は変わります。時期に合わせた比較も大切です。',

                '_mpb_cta_title'     => '条件を見ながら宿泊先を比較する',
                '_mpb_cta_text'      => '施設一覧ページでは、定員や設備を見ながら宿泊先を比較できます。ご利用案内やFAQもあわせてご確認ください。',
                '_mpb_cta_btn1_text' => 'よくある質問を見る',
                '_mpb_cta_btn2_text' => 'ご利用案内を見る',
            ),

            'minpaku-faq' => array(
                '_mpb_hero_eyebrow'   => 'FAQ',
                '_mpb_hero_title'     => 'よくあるご質問',
                '_mpb_hero_lead'      => '那須の民泊・一棟貸し・貸別荘を検討している方から、宿泊前によくいただく質問をまとめました。予約前の確認にご活用ください。',
                '_mpb_hero_btn1_text' => 'ご利用案内を見る',
                '_mpb_hero_btn2_text' => '予約の流れを見る',

                '_mpb_intro_eyebrow' => 'Questions',
                '_mpb_intro_title'   => '予約前によくいただく内容をまとめています',
                '_mpb_intro_text'    => '宿泊スタイルの違い、人数や設備の見方、予約から決済までの流れなど、宿泊前に気になりやすい内容を整理しています。',

                '_mpb_faq_eyebrow' => 'FAQ',
                '_mpb_faq_title'   => '那須の民泊・一棟貸し よくある質問',
                '_mpb_faq_text'    => 'ご予約前に確認したい内容を中心にまとめています。詳細な条件は各宿泊詳細ページでもご確認いただけます。',

                '_mpb_faq_1_q'  => '民泊と一棟貸し、貸別荘は同じですか？',
                '_mpb_faq_1_a'  => '似た使い方をすることはありますが、運用形態や見せ方が異なる場合があります。このサイトでは、宿泊者が比較しやすいように整理してご案内しています。',
                '_mpb_faq_2_q'  => '人数や寝室数はどこで確認できますか？',
                '_mpb_faq_2_a'  => '各宿泊詳細ページで、最大人数、寝室数、ベッド数、最低宿泊数などを確認できるようにしています。',
                '_mpb_faq_3_q'  => '予約の流れはどうなりますか？',
                '_mpb_faq_3_a'  => '宿泊先を選んだあと、詳細ページで日付や人数を確認し、予約内容を確認したうえでオンライン決済へ進む流れです。',
                '_mpb_faq_4_q'  => '宿泊先が少ない場合でも比較できますか？',
                '_mpb_faq_4_a'  => '一覧ページだけでなく、違いや利用案内、FAQなどの説明ページもあわせて見ることで、自分たちに合う宿泊先を選びやすくなります。',
                '_mpb_faq_5_q'  => '家族やグループ利用にも向いていますか？',
                '_mpb_faq_5_a'  => '一棟貸しや貸別荘タイプは、複数人での滞在や家族利用とも相性がよく、空間をまとめて使いやすい点が特徴です。',
                '_mpb_faq_6_q'  => 'ご利用前に確認しておくべきことはありますか？',
                '_mpb_faq_6_a'  => 'チェックイン・チェックアウト、キャンセル条件、設備利用、人数条件などを事前に確認しておくと安心です。',
                '_mpb_faq_7_q'  => 'クレジットカードで支払えますか？',
                '_mpb_faq_7_a'  => 'はい、クレジットカードでのお支払いに対応しています。決済完了後に予約が確定します。',
                '_mpb_faq_8_q'  => 'Wi-Fi はありますか？',
                '_mpb_faq_8_a'  => '施設ごとにインターネット環境や回線状況が異なります。詳細は各宿泊詳細ページをご確認ください。',
                '_mpb_faq_9_q'  => 'キッチンや調理器具は使えますか？',
                '_mpb_faq_9_a'  => '施設によって利用可否や設備内容が異なります。調理を希望する場合は、宿泊先ページの設備案内をご確認ください。',
                '_mpb_faq_10_q' => '食材や飲み物の持ち込みはできますか？',
                '_mpb_faq_10_a' => '持ち込みの可否は施設方針によって異なる場合があります。事前に宿泊先の案内をご確認ください。',

                '_mpb_cta_title'     => '不明点を整理して宿泊先を検討する',
                '_mpb_cta_text'      => 'よくある質問を確認したうえで、ご利用案内や予約の流れもあわせてご覧ください。',
                '_mpb_cta_btn1_text' => 'ご利用案内を見る',
                '_mpb_cta_btn2_text' => '予約の流れを見る',
            ),

            'minpaku-rules' => array(
                '_mpb_hero_eyebrow'   => 'Rules',
                '_mpb_hero_title'     => 'ご利用案内・利用規約',
                '_mpb_hero_lead'      => '宿泊前に確認しておきたいチェックイン・チェックアウト、キャンセル条件、設備利用、注意事項などをまとめています。',
                '_mpb_hero_btn1_text' => '予約の流れを見る',
                '_mpb_hero_btn2_text' => 'よくある質問を見る',

                '_mpb_intro_eyebrow' => 'Guide',
                '_mpb_intro_title'   => '宿泊前に確認しておきたい基本事項',
                '_mpb_intro_text'    => '民泊や一棟貸しは、一般的なホテル滞在と異なる点がある場合があります。事前にルールや利用条件を確認しておくことで、滞在中も安心して過ごしやすくなります。',

                '_mpb_feature1_eyebrow' => 'Check In / Out',
                '_mpb_feature1_title'   => 'チェックイン・チェックアウトを確認する',
                '_mpb_feature1_text'    => '入室可能時間、退出時間、鍵の受け渡し方法など、滞在当日に迷わないように事前確認が大切です。',

                '_mpb_feature2_eyebrow' => 'Policy',
                '_mpb_feature2_title'   => 'キャンセル条件や人数条件を確認する',
                '_mpb_feature2_text'    => '予約変更やキャンセルに関する条件、最大人数、最低宿泊日数などは、宿泊先ごとに確認が必要です。',

                '_mpb_feature3_eyebrow' => 'Equipment',
                '_mpb_feature3_title'   => '設備利用や滞在中の注意事項を把握する',
                '_mpb_feature3_text'    => 'キッチンや備品の使い方、近隣への配慮、ゴミ出しなど、滞在中の基本的なルールを事前に把握しておくと安心です。',

                '_mpb_faq_eyebrow' => 'Rules',
                '_mpb_faq_title'   => '主なご利用条件',
                '_mpb_faq_text'    => 'キャンセル・返金・人数・禁止事項など、利用前に確認したい条件をまとめています。',

                '_mpb_faq_1_q'  => 'キャンセルはできますか？',
                '_mpb_faq_1_a'  => '可能です。受付日により返金条件が変わるため、詳細はキャンセル・返金ポリシーをご確認ください。',
                '_mpb_faq_2_q'  => '返金はどのように行われますか？',
                '_mpb_faq_2_a'  => '返金の可否や金額はキャンセル条件に基づいて判断します。返金方法や時期はポリシーに従います。',
                '_mpb_faq_3_q'  => 'チェックインとチェックアウトの条件はありますか？',
                '_mpb_faq_3_a'  => '入室可能時間、退出時間、鍵の受け渡し方法などは施設案内に従ってください。',
                '_mpb_faq_4_q'  => '申告人数を超えて利用できますか？',
                '_mpb_faq_4_a'  => 'ご予約時に申告された人数の範囲でご利用ください。無断での人数追加はお断りします。',
                '_mpb_faq_5_q'  => '第三者への又貸しはできますか？',
                '_mpb_faq_5_a'  => 'できません。予約者本人または申告された利用者以外への又貸し・名義貸しは禁止しています。',
                '_mpb_faq_6_q'  => '室内で気をつけることはありますか？',
                '_mpb_faq_6_a'  => '禁煙、騒音配慮、設備や備品の取扱い、ゴミ分別など、施設案内および利用規約に従ってご利用ください。',
                '_mpb_faq_7_q'  => '設備や備品を破損した場合はどうなりますか？',
                '_mpb_faq_7_a'  => '状況確認のうえ、必要に応じて実費等をご負担いただくことがあります。',
                '_mpb_faq_8_q'  => '騒音や迷惑行為があった場合はどうなりますか？',
                '_mpb_faq_8_a'  => '近隣への迷惑行為やルール違反があった場合は、利用停止や今後の利用制限の対象となることがあります。',
                '_mpb_faq_9_q'  => '正式な条件はどこで確認できますか？',
                '_mpb_faq_9_a'  => '利用規約およびキャンセル・返金ポリシーのページで正式な条件をご確認ください。',
                '_mpb_faq_10_q' => '利用できないケースはありますか？',
                '_mpb_faq_10_a' => '規約違反、申告内容との相違、迷惑行為などがある場合は利用をお断りすることがあります。',

                '_mpb_cta_title'     => 'ご利用前に確認したうえで宿泊先を選ぶ',
                '_mpb_cta_text'      => '宿泊先一覧や予約の流れもあわせて確認しながら、安心して滞在準備を進めてください。',
                '_mpb_cta_btn1_text' => '予約の流れを見る',
                '_mpb_cta_btn2_text' => 'よくある質問を見る',
            ),

            'minpaku-flow' => array(
                '_mpb_hero_eyebrow'   => 'Flow',
                '_mpb_hero_title'     => '宿泊予約からオンライン決済までの流れ',
                '_mpb_hero_lead'      => '宿泊施設の比較から、詳細確認、予約内容の確認、オンライン決済までの流れを整理しています。初めての方も、手順を確認しながら進められます。',
                '_mpb_hero_btn1_text' => 'ご利用案内を見る',
                '_mpb_hero_btn2_text' => 'よくある質問を見る',

                '_mpb_intro_eyebrow' => 'Booking Flow',
                '_mpb_intro_title'   => '宿泊前の流れを先に確認しておく',
                '_mpb_intro_text'    => '先に予約から決済までの流れを把握しておくと、宿泊先選びの段階でも必要な情報を確認しやすくなります。',

                '_mpb_flow_eyebrow' => 'Flow',
                '_mpb_flow_title'   => '予約から決済までの基本ステップ',
                '_mpb_flow_text'    => '一覧ページから宿泊先を比較し、詳細ページで条件を確認したあと、予約内容を確認してオンライン決済へ進みます。',

                '_mpb_flow_1_title' => '宿泊先を比較する',
                '_mpb_flow_1_text'  => '一覧ページで、定員、設備、滞在スタイルを見ながら候補を整理します。',
                '_mpb_flow_2_title' => '詳細ページで条件を確認する',
                '_mpb_flow_2_text'  => '写真、設備、人数、最低宿泊数などを確認し、宿泊先を決定します。',
                '_mpb_flow_3_title' => '日付と予約内容を確認する',
                '_mpb_flow_3_text'  => '宿泊日程や人数を確認し、予約内容に問題がないかを確認します。',
                '_mpb_flow_4_title' => 'オンライン決済へ進む',
                '_mpb_flow_4_text'  => '確認画面の内容を見たうえで、オンライン決済を進めます。',

                '_mpb_cta_title'     => '流れを確認して宿泊先を探す',
                '_mpb_cta_text'      => '予約の流れを把握したうえで、宿泊施設一覧やご利用案内もあわせてご確認ください。',
                '_mpb_cta_btn1_text' => 'ご利用案内を見る',
                '_mpb_cta_btn2_text' => 'よくある質問を見る',
            ),

            'minpaku-difference' => array(
                '_mpb_hero_eyebrow'   => 'Difference',
                '_mpb_hero_title'     => '民泊・一棟貸し・貸別荘の違い',
                '_mpb_hero_lead'      => '似ているように見える宿泊スタイルでも、使い方やイメージは少しずつ異なります。違いを整理して、自分たちに合う滞在先を選びやすくします。',
                '_mpb_hero_btn1_text' => '宿泊の楽しみ方を見る',
                '_mpb_hero_btn2_text' => 'よくある質問を見る',

                '_mpb_intro_eyebrow' => 'Difference',
                '_mpb_intro_title'   => '似ている宿泊スタイルをわかりやすく整理する',
                '_mpb_intro_text'    => '民泊、一棟貸し、貸別荘は、宿泊者から見ると近い選択肢になることがあります。違いを整理しておくと、一覧ページでも比較しやすくなります。',

                '_mpb_feature1_eyebrow' => 'Style',
                '_mpb_feature1_title'   => '宿泊スタイルの見え方が異なる',
                '_mpb_feature1_text'    => '民泊は運用形態の表現として使われることがあり、一棟貸しや貸別荘は、宿泊者が利用する空間の使い方として理解されやすい傾向があります。',

                '_mpb_feature2_eyebrow' => 'Use Case',
                '_mpb_feature2_title'   => '家族・グループ利用との相性を見る',
                '_mpb_feature2_text'    => '一棟貸しや貸別荘は、複数人でまとまって過ごしたい方に向いており、民泊という表現の中でもそのような滞在スタイルが含まれることがあります。',

                '_mpb_feature3_eyebrow' => 'Selection',
                '_mpb_feature3_title'   => '違いを知ると比較しやすくなる',
                '_mpb_feature3_text'    => '名称だけで判断するのではなく、定員、設備、滞在条件、使い方を見ながら比較すると、自分たちに合う宿泊先を選びやすくなります。',

                '_mpb_cta_title'     => '違いを整理して宿泊先を探す',
                '_mpb_cta_text'      => '違いを確認したうえで、宿泊施設一覧や予約の流れ、ご利用案内もあわせてご覧ください。',
                '_mpb_cta_btn1_text' => '予約の流れを見る',
                '_mpb_cta_btn2_text' => 'よくある質問を見る',
            ),
        );
    }
}

if (!function_exists('mpb_default_value')) {
    function mpb_default_value($post_id, $meta_key, $fallback = '')
    {
        $slug = mpb_get_page_slug($post_id);
        $map  = mpb_default_content_map();

        if ($slug && isset($map[$slug]) && array_key_exists($meta_key, $map[$slug])) {
            return $map[$slug][$meta_key];
        }

        return $fallback;
    }
}

/* =========================================================
 * slug ごとの初期リンク
 * 重要:
 * - 固定ページだけを自動候補にする
 * ========================================================= */
if (!function_exists('mpb_default_page_links')) {
    function mpb_default_page_links()
    {
        return array(
            'minpaku-guide' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-faq',
                '_mpb_hero_btn2_page_id' => 'minpaku-rules',
                '_mpb_cta_btn1_page_id'  => 'minpaku-faq',
                '_mpb_cta_btn2_page_id'  => 'minpaku-rules',
            ),
            'minpaku-family' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-faq',
                '_mpb_hero_btn2_page_id' => 'minpaku-rules',
                '_mpb_cta_btn1_page_id'  => 'minpaku-faq',
                '_mpb_cta_btn2_page_id'  => 'minpaku-rules',
            ),
            'minpaku-group' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-faq',
                '_mpb_hero_btn2_page_id' => 'minpaku-rules',
                '_mpb_cta_btn1_page_id'  => 'minpaku-faq',
                '_mpb_cta_btn2_page_id'  => 'minpaku-rules',
            ),
            'minpaku-workation' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-faq',
                '_mpb_hero_btn2_page_id' => 'minpaku-rules',
                '_mpb_cta_btn1_page_id'  => 'minpaku-faq',
                '_mpb_cta_btn2_page_id'  => 'minpaku-rules',
            ),
            'minpaku-campaign' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-difference',
                '_mpb_hero_btn2_page_id' => 'minpaku-faq',
                '_mpb_cta_btn1_page_id'  => 'minpaku-faq',
                '_mpb_cta_btn2_page_id'  => 'minpaku-rules',
            ),
            'minpaku-faq' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-rules',
                '_mpb_hero_btn2_page_id' => 'minpaku-flow',
                '_mpb_cta_btn1_page_id'  => 'minpaku-rules',
                '_mpb_cta_btn2_page_id'  => 'minpaku-flow',
            ),
            'minpaku-rules' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-flow',
                '_mpb_hero_btn2_page_id' => 'minpaku-faq',
                '_mpb_cta_btn1_page_id'  => 'minpaku-flow',
                '_mpb_cta_btn2_page_id'  => 'minpaku-faq',
            ),
            'minpaku-flow' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-rules',
                '_mpb_hero_btn2_page_id' => 'minpaku-faq',
                '_mpb_cta_btn1_page_id'  => 'minpaku-rules',
                '_mpb_cta_btn2_page_id'  => 'minpaku-faq',
            ),
            'minpaku-difference' => array(
                '_mpb_hero_btn1_page_id' => 'minpaku-guide',
                '_mpb_hero_btn2_page_id' => 'minpaku-faq',
                '_mpb_cta_btn1_page_id'  => 'minpaku-flow',
                '_mpb_cta_btn2_page_id'  => 'minpaku-faq',
            ),
        );
    }
}

if (!function_exists('mpb_default_page_id')) {
    function mpb_default_page_id($post_id, $meta_key)
    {
        $slug = mpb_get_page_slug($post_id);
        $map  = mpb_default_page_links();

        if ($slug && isset($map[$slug]) && isset($map[$slug][$meta_key])) {
            return mpb_find_page_id_by_slug($map[$slug][$meta_key]);
        }

        return 0;
    }
}

if (!function_exists('mpb_admin_default_text')) {
    function mpb_admin_default_text($post_id, $meta_key, $fallback = '')
    {
        $value = get_post_meta($post_id, $meta_key, true);

        if ($value !== '' && $value !== null) {
            return $value;
        }

        return mpb_default_value($post_id, $meta_key, $fallback);
    }
}

if (!function_exists('mpb_admin_default_page_id')) {
    function mpb_admin_default_page_id($post_id, $meta_key)
    {
        $value = absint(get_post_meta($post_id, $meta_key, true));

        if ($value > 0) {
            return $value;
        }

        return absint(mpb_default_page_id($post_id, $meta_key));
    }
}

if (!function_exists('mpb_plain_text_fields')) {
    function mpb_plain_text_fields()
    {
        return array(
            '_mpb_hero_eyebrow',
            '_mpb_hero_title',
            '_mpb_hero_btn1_text',
            '_mpb_hero_btn2_text',

            '_mpb_intro_eyebrow',
            '_mpb_intro_title',

            '_mpb_feature1_eyebrow',
            '_mpb_feature1_title',
            '_mpb_feature1_btn1_text',
            '_mpb_feature1_btn2_text',

            '_mpb_feature2_eyebrow',
            '_mpb_feature2_title',
            '_mpb_feature2_btn1_text',
            '_mpb_feature2_btn2_text',

            '_mpb_feature3_eyebrow',
            '_mpb_feature3_title',
            '_mpb_feature3_btn1_text',
            '_mpb_feature3_btn2_text',

            '_mpb_flow_eyebrow',
            '_mpb_flow_title',
            '_mpb_flow_1_title',
            '_mpb_flow_2_title',
            '_mpb_flow_3_title',
            '_mpb_flow_4_title',

            '_mpb_faq_eyebrow',
            '_mpb_faq_title',

            '_mpb_cta_title',
            '_mpb_cta_btn1_text',
            '_mpb_cta_btn2_text',
        );
    }
}

if (!function_exists('mpb_textarea_fields')) {
    function mpb_textarea_fields()
    {
        return array(
            '_mpb_hero_lead',

            '_mpb_intro_text',

            '_mpb_feature1_text',
            '_mpb_feature2_text',
            '_mpb_feature3_text',

            '_mpb_flow_text',
            '_mpb_flow_1_text',
            '_mpb_flow_2_text',
            '_mpb_flow_3_text',
            '_mpb_flow_4_text',

            '_mpb_faq_text',
            '_mpb_faq_1_q',
            '_mpb_faq_1_a',
            '_mpb_faq_2_q',
            '_mpb_faq_2_a',
            '_mpb_faq_3_q',
            '_mpb_faq_3_a',
            '_mpb_faq_4_q',
            '_mpb_faq_4_a',
            '_mpb_faq_5_q',
            '_mpb_faq_5_a',
            '_mpb_faq_6_q',
            '_mpb_faq_6_a',
            '_mpb_faq_7_q',
            '_mpb_faq_7_a',
            '_mpb_faq_8_q',
            '_mpb_faq_8_a',
            '_mpb_faq_9_q',
            '_mpb_faq_9_a',
            '_mpb_faq_10_q',
            '_mpb_faq_10_a',

            '_mpb_cta_text',
        );
    }
}

if (!function_exists('mpb_image_fields')) {
    function mpb_image_fields()
    {
        return array(
            '_mpb_hero_image_id',
            '_mpb_intro_image_id',
            '_mpb_feature1_image_id',
            '_mpb_feature2_image_id',
            '_mpb_feature3_image_id',
            '_mpb_cta_image_id',
        );
    }
}

if (!function_exists('mpb_page_id_fields')) {
    function mpb_page_id_fields()
    {
        return array(
            '_mpb_hero_btn1_page_id',
            '_mpb_hero_btn2_page_id',

            '_mpb_feature1_btn1_page_id',
            '_mpb_feature1_btn2_page_id',

            '_mpb_feature2_btn1_page_id',
            '_mpb_feature2_btn2_page_id',

            '_mpb_feature3_btn1_page_id',
            '_mpb_feature3_btn2_page_id',

            '_mpb_cta_btn1_page_id',
            '_mpb_cta_btn2_page_id',
        );
    }
}

/* =========================================================
 * 民泊系固定ページ一覧
 * 条件:
 * - page のみ
 * - page_uri に "minpaku" を含むものだけ
 * - 現在編集中のページは除外可能
 * ========================================================= */
/* =========================================================
 * 追加: レイアウト種別
 * =========================================================
 *
 * 役割:
 * - 各固定ページがどの基本レイアウトを使うかを保存する
 * - slug ではなく、この値で guide / difference / standard などを判定する
 *
 * 値:
 * - guide
 * - difference
 * - campaign
 * - faq
 * - flow
 * - rules
 * - standard
 */
if (!function_exists('mpb_layout_type_options')) {
    function mpb_layout_type_options()
    {
        return array(
            'guide'      => 'Guide',
            'difference' => 'Difference',
            'campaign'   => 'Campaign',
            'faq'        => 'FAQ',
            'flow'       => 'Flow',
            'rules'      => 'Rules',
            'standard'   => 'Standard',
        );
    }
}

if (!function_exists('mpb_get_layout_type')) {
    function mpb_get_layout_type($post_id)
    {
        $value = get_post_meta($post_id, '_mpb_layout_type', true);

        if ($value === '') {
            return 'standard';
        }

        $allowed = array_keys(mpb_layout_type_options());

        return in_array($value, $allowed, true) ? $value : 'standard';
    }
}

/* =========================================================
 * 追加: 共通コア field
 * =========================================================
 *
 * 役割:
 * - どのページでも使う入力欄をまとめる
 * - hero / intro / cta のような共通部品
 */
if (!function_exists('mpb_common_field_groups')) {
    function mpb_common_field_groups()
    {
        return array(
            'plain' => array(
                '_mpb_layout_type',

                '_mpb_hero_eyebrow',
                '_mpb_hero_title',
                '_mpb_hero_btn1_text',
                '_mpb_hero_btn2_text',

                '_mpb_intro_eyebrow',
                '_mpb_intro_title',

                '_mpb_cta_title',
                '_mpb_cta_btn1_text',
                '_mpb_cta_btn2_text',
            ),
            'textarea' => array(
                '_mpb_hero_lead',
                '_mpb_intro_text',
                '_mpb_cta_text',
            ),
            'image' => array(
                '_mpb_hero_image_id',
                '_mpb_intro_image_id',
                '_mpb_cta_image_id',
            ),
            'page_id' => array(
                '_mpb_hero_btn1_page_id',
                '_mpb_hero_btn2_page_id',
                '_mpb_cta_btn1_page_id',
                '_mpb_cta_btn2_page_id',
            ),
        );
    }
}

/* =========================================================
 * 追加: guide パーツ field
 * =========================================================
 *
 * 役割:
 * - guide カード専用
 * - 今後、画像 optional にしやすいように image も持たせる
 */
if (!function_exists('mpb_guide_part_fields')) {
    function mpb_guide_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_guide_card1_title',
                '_mpb_guide_card2_title',
                '_mpb_guide_card3_title',
                '_mpb_guide_card4_title',
            ),
            'textarea' => array(
                '_mpb_guide_card1_text',
                '_mpb_guide_card2_text',
                '_mpb_guide_card3_text',
                '_mpb_guide_card4_text',
            ),
            'image' => array(
                '_mpb_guide_card1_image_id',
                '_mpb_guide_card2_image_id',
                '_mpb_guide_card3_image_id',
                '_mpb_guide_card4_image_id',
            ),
            'page_id' => array(),
        );
    }
}

/* =========================================================
 * 追加: difference パーツ field
 * =========================================================
 *
 * 役割:
 * - 比較カード / 比較表 用
 */
if (!function_exists('mpb_difference_part_fields')) {
    function mpb_difference_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_diff_card1_title',
                '_mpb_diff_card2_title',
                '_mpb_diff_card3_title',
                '_mpb_compare_table_title',
            ),
            'textarea' => array(
                '_mpb_diff_card1_text',
                '_mpb_diff_card2_text',
                '_mpb_diff_card3_text',
                '_mpb_compare_table_html',
            ),
            'image' => array(
                '_mpb_diff_card1_image_id',
                '_mpb_diff_card2_image_id',
                '_mpb_diff_card3_image_id',
            ),
            'page_id' => array(),
        );
    }
}

/* =========================================================
 * 追加: feature パーツ field
 * =========================================================
 *
 * 役割:
 * - standard / campaign / rules などで使う特徴セクション
 */
if (!function_exists('mpb_feature_part_fields')) {
    function mpb_feature_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_feature1_eyebrow',
                '_mpb_feature1_title',
                '_mpb_feature1_btn1_text',
                '_mpb_feature1_btn2_text',
                '_mpb_feature2_eyebrow',
                '_mpb_feature2_title',
                '_mpb_feature2_btn1_text',
                '_mpb_feature2_btn2_text',
                '_mpb_feature3_eyebrow',
                '_mpb_feature3_title',
                '_mpb_feature3_btn1_text',
                '_mpb_feature3_btn2_text',
            ),
            'textarea' => array(
                '_mpb_feature1_text',
                '_mpb_feature2_text',
                '_mpb_feature3_text',
            ),
            'image' => array(
                '_mpb_feature1_image_id',
                '_mpb_feature2_image_id',
                '_mpb_feature3_image_id',
            ),
            'page_id' => array(
                '_mpb_feature1_btn1_page_id',
                '_mpb_feature1_btn2_page_id',
                '_mpb_feature2_btn1_page_id',
                '_mpb_feature2_btn2_page_id',
                '_mpb_feature3_btn1_page_id',
                '_mpb_feature3_btn2_page_id',
            ),
        );
    }
}

/* =========================================================
 * 追加: flow パーツ field
 * ========================================================= */
if (!function_exists('mpb_flow_part_fields')) {
    function mpb_flow_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_flow_eyebrow',
                '_mpb_flow_title',
                '_mpb_flow_1_title',
                '_mpb_flow_2_title',
                '_mpb_flow_3_title',
                '_mpb_flow_4_title',
            ),
            'textarea' => array(
                '_mpb_flow_text',
                '_mpb_flow_1_text',
                '_mpb_flow_2_text',
                '_mpb_flow_3_text',
                '_mpb_flow_4_text',
            ),
            'image' => array(),
            'page_id' => array(),
        );
    }
}

/* =========================================================
 * 追加: faq パーツ field
 * ========================================================= */
if (!function_exists('mpb_faq_part_fields')) {
    function mpb_faq_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_faq_eyebrow',
                '_mpb_faq_title',
            ),
            'textarea' => array(
                '_mpb_faq_text',
                '_mpb_faq_1_q',
                '_mpb_faq_1_a',
                '_mpb_faq_2_q',
                '_mpb_faq_2_a',
                '_mpb_faq_3_q',
                '_mpb_faq_3_a',
                '_mpb_faq_4_q',
                '_mpb_faq_4_a',
                '_mpb_faq_5_q',
                '_mpb_faq_5_a',
                '_mpb_faq_6_q',
                '_mpb_faq_6_a',
                '_mpb_faq_7_q',
                '_mpb_faq_7_a',
                '_mpb_faq_8_q',
                '_mpb_faq_8_a',
                '_mpb_faq_9_q',
                '_mpb_faq_9_a',
                '_mpb_faq_10_q',
                '_mpb_faq_10_a',
            ),
            'image' => array(),
            'page_id' => array(),
        );
    }
}

/* =========================================================
 * 追加: layout_type ごとの有効パーツ
 * =========================================================
 *
 * 役割:
 * - 管理画面で、どのパーツ入力欄を出すか決める
 */

/* =========================================================
 * compare_table パーツ field
 * =========================================================
 *
 * 役割:
 * - 比較表見出し / 本文(HTML含む) を扱う
 */
if (!function_exists('mpb_compare_table_part_fields')) {
    function mpb_compare_table_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_compare_table_title',
            ),
            'textarea' => array(
                '_mpb_compare_table_html',
            ),
            'image' => array(),
            'page_id' => array(),
        );
    }
}

/* =========================================================
 * related_links パーツ field
 * =========================================================
 *
 * 役割:
 * - いまは将来用の空定義
 * - 後で関連リンクの繰り返し項目を追加する
 */
if (!function_exists('mpb_related_links_part_fields')) {
    function mpb_related_links_part_fields()
    {
        return array(
            'plain' => array(
                '_mpb_related_links_title',
                '_mpb_related_link1_text',
                '_mpb_related_link2_text',
                '_mpb_related_link3_text',
                '_mpb_related_link4_text',
                '_mpb_related_link5_text',
                '_mpb_related_link6_text',
            ),
            'textarea' => array(
                '_mpb_related_links_text',
            ),
            'image' => array(),
            'page_id' => array(
                '_mpb_related_link1_page_id',
                '_mpb_related_link2_page_id',
                '_mpb_related_link3_page_id',
                '_mpb_related_link4_page_id',
                '_mpb_related_link5_page_id',
                '_mpb_related_link6_page_id',
            ),
        );
    }
}

/* =========================================================
 * パーツ名から field 一覧を返す helper
 * =========================================================
 *
 * 役割:
 * - feature / flow / faq / compare_table / related_links など
 *   パーツ名から対応する field 群を返す
 */
if (!function_exists('mpb_part_fields_by_name')) {
    function mpb_part_fields_by_name($part_name)
    {
        if ($part_name === 'guide') {
            return mpb_guide_part_fields();
        }

        if ($part_name === 'difference') {
            return mpb_difference_part_fields();
        }

        if ($part_name === 'feature') {
            return mpb_feature_part_fields();
        }

        if ($part_name === 'flow') {
            return mpb_flow_part_fields();
        }

        if ($part_name === 'faq') {
            return mpb_faq_part_fields();
        }

        if ($part_name === 'compare_table') {
            return mpb_compare_table_part_fields();
        }

        if ($part_name === 'related_links') {
            return mpb_related_links_part_fields();
        }

        return array(
            'plain' => array(),
            'textarea' => array(),
            'image' => array(),
            'page_id' => array(),
        );
    }
}



/* =========================================================
 * 民泊固定ページで使うテンプレート一覧
 * =========================================================
 *
 * 役割:
 * - 固定ページは post_type=page のまま維持する
 * - そのうえで民泊メニュー配下の専用一覧だけに絞り込む
 */
if (!function_exists('mpb_minpaku_page_template_slugs')) {
    function mpb_minpaku_page_template_slugs()
    {
        return array(
            'page-minpaku-b2c.php',
            'page-minpaku-b2c-guide.php',
            'page-minpaku-b2c-difference.php',
            'page-minpaku-b2c-campaign.php',
            'page-minpaku-b2c-faq.php',
            'page-minpaku-b2c-flow.php',
            'page-minpaku-b2c-rules.php',
            'page-minpaku-support.php',
        );
    }
}

if (!function_exists('mpb_is_minpaku_template_page')) {
    function mpb_is_minpaku_template_page($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id || get_post_type($post_id) !== 'page') {
            return false;
        }

        $template = get_page_template_slug($post_id);
        return in_array($template, mpb_minpaku_page_template_slugs(), true);
    }
}

/* =========================================================
 * page の抜粋を有効化
 * =========================================================
 *
 * 役割:
 * - 抜粋を独立表示するために page で excerpt を使えるようにする
 */
if (!function_exists('mpb_enable_page_excerpt_support')) {
    function mpb_enable_page_excerpt_support()
    {
        add_post_type_support('page', 'excerpt');
    }
}
add_action('init', 'mpb_enable_page_excerpt_support');

/* =========================================================
 * 旧トップレベル「民泊固定ページ」メニューがあれば消す
 * ========================================================= */
if (!function_exists('mpb_remove_old_fixed_pages_menu')) {
    function mpb_remove_old_fixed_pages_menu()
    {
        remove_menu_page('mpb-minpaku-pages');
    }
}
add_action('admin_menu', 'mpb_remove_old_fixed_pages_menu', 999);

/* =========================================================
 * 民泊メニュー配下に「固定ページ一覧 / 新規追加」を出す
 * ========================================================= */
if (!function_exists('mpb_minpaku_pages_list_redirect')) {
    function mpb_minpaku_pages_list_redirect()
    {
        if (!current_user_can('edit_pages')) {
            wp_die('権限がありません。');
        }

        wp_safe_redirect(admin_url('edit.php?post_type=page&mpb_minpaku_pages=1'));
        exit;
    }
}

if (!function_exists('mpb_minpaku_pages_new_redirect')) {
    function mpb_minpaku_pages_new_redirect()
    {
        if (!current_user_can('edit_pages')) {
            wp_die('権限がありません。');
        }

        wp_safe_redirect(admin_url('post-new.php?post_type=page&mpb_minpaku_new=1'));
        exit;
    }
}


/* =========================================================
 * 民泊メニュー配下の固定ページ一覧 / 新規追加へ強制リダイレクト
 * =========================================================
 *
 * 役割:
 * - add_submenu_page の callback に依存せず、
 *   管理画面URLを開いた時点で確実に飛ばす
 * - 空画面のまま止まる問題を防ぐ
 */
if (!function_exists('mpb_force_minpaku_fixed_pages_redirect')) {
    function mpb_force_minpaku_fixed_pages_redirect()
    {
        if (!is_admin()) {
            return;
        }

        if (!current_user_can('edit_pages')) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        if ($page === 'mpb-minpaku-fixed-pages') {
            wp_safe_redirect(admin_url('edit.php?post_type=page&mpb_minpaku_pages=1'));
            exit;
        }

        if ($page === 'mpb-minpaku-fixed-pages-new') {
            wp_safe_redirect(admin_url('post-new.php?post_type=page&mpb_minpaku_new=1'));
            exit;
        }
    }
}
add_action('admin_init', 'mpb_force_minpaku_fixed_pages_redirect', 1);


if (!function_exists('mpb_register_minpaku_page_submenus')) {
    function mpb_register_minpaku_page_submenus()
    {
        add_submenu_page(
            'edit.php?post_type=minpaku',
            'B2C民泊固定ページ一覧',
            'B2C民泊固定ページ',
            'edit_pages',
            'mpb-minpaku-fixed-pages',
            'mpb_minpaku_pages_list_redirect'
        );

        add_submenu_page(
            'edit.php?post_type=minpaku',
            'B2C民泊固定ページを追加',
            'B2C民泊固定ページ追加',
            'edit_pages',
            'mpb-minpaku-fixed-pages-new',
            'mpb_minpaku_pages_new_redirect'
        );
    }
}
add_action('admin_menu', 'mpb_register_minpaku_page_submenus', 30);

/* =========================================================
 * 民泊固定ページ一覧だけに絞る
 * ========================================================= */

/* =========================================================
 * 民泊固定ページ page ID 一覧
 * =========================================================
 *
 * 役割:
 * - _wp_page_template が民泊固定ページ対象テンプレートの page ID を返す
 * - 民泊専用一覧では post__in
 * - 通常の固定ページ一覧では post__not_in
 *   に使う
 */
if (!function_exists('mpb_get_minpaku_fixed_page_ids')) {
    function mpb_get_minpaku_fixed_page_ids()
    {
        global $wpdb;

        $templates = mpb_minpaku_page_template_slugs();

        if (empty($templates)) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($templates), '%s'));

        $sql = $wpdb->prepare(
            "SELECT DISTINCT pm.post_id
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_wp_page_template'
               AND pm.meta_value IN ($placeholders)
               AND p.post_type = 'page'
               AND p.post_status NOT IN ('trash', 'auto-draft')",
            ...$templates
        );

        $ids = $wpdb->get_col($sql);

        return array_map('intval', $ids);
    }
}


if (!function_exists('mpb_filter_minpaku_fixed_pages_list_query')) {
    function mpb_filter_minpaku_fixed_pages_list_query($query)
    {
        global $pagenow;

        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($pagenow !== 'edit.php') {
            return;
        }

        if ($query->get('post_type') !== 'page') {
            return;
        }

        if (empty($_GET['mpb_minpaku_pages'])) {
            return;
        }

        $ids = mpb_get_minpaku_fixed_page_ids();

        if (empty($ids)) {
            $ids = array(0);
        }

        $query->set('post__in', $ids);
        $query->set('orderby', 'post__in');
    }
}
add_action('pre_get_posts', 'mpb_filter_minpaku_fixed_pages_list_query');

/* =========================================================
 * 通常の固定ページ一覧から B2C民泊固定ページを除外
 * =========================================================
 *
 * 役割:
 * - 通常の固定ページ一覧では不動産系や通常ページだけ見せる
 * - 民泊B2C / support は民泊メニュー配下で管理する
 */
if (!function_exists('mpb_hide_minpaku_fixed_pages_from_normal_page_list')) {
    function mpb_hide_minpaku_fixed_pages_from_normal_page_list($query)
    {
        global $pagenow;

        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($pagenow !== 'edit.php') {
            return;
        }

        if ($query->get('post_type') !== 'page') {
            return;
        }

        if (!empty($_GET['mpb_minpaku_pages'])) {
            return;
        }

        $ids = mpb_get_minpaku_fixed_page_ids();

        if (empty($ids)) {
            return;
        }

        $query->set('post__not_in', $ids);
    }
}
add_action('pre_get_posts', 'mpb_hide_minpaku_fixed_pages_from_normal_page_list', 20);


/* =========================================================
 * 編集中も民泊メニューを開いた状態にする
 * ========================================================= */
if (!function_exists('mpb_keep_minpaku_menu_active')) {
    function mpb_keep_minpaku_menu_active($parent_file)
    {
        global $pagenow;

        if (is_admin() && $pagenow === 'post.php' && !empty($_GET['post'])) {
            $post_id = absint($_GET['post']);
            if (mpb_is_minpaku_template_page($post_id)) {
                return 'edit.php?post_type=minpaku';
            }
        }

        if (is_admin() && $pagenow === 'edit.php' && !empty($_GET['mpb_minpaku_pages'])) {
            return 'edit.php?post_type=minpaku';
        }

        return $parent_file;
    }
}
add_filter('parent_file', 'mpb_keep_minpaku_menu_active');

if (!function_exists('mpb_keep_minpaku_submenu_active')) {
    function mpb_keep_minpaku_submenu_active($submenu_file)
    {
        global $pagenow;

        if (is_admin() && $pagenow === 'post.php' && !empty($_GET['post'])) {
            $post_id = absint($_GET['post']);
            if (mpb_is_minpaku_template_page($post_id)) {
                return 'mpb-minpaku-fixed-pages';
            }
        }

        if (is_admin() && $pagenow === 'edit.php' && !empty($_GET['mpb_minpaku_pages'])) {
            return 'mpb-minpaku-fixed-pages';
        }

        return $submenu_file;
    }
}
add_filter('submenu_file', 'mpb_keep_minpaku_submenu_active');

/* =========================================================
 * 通常の固定ページ一覧では B2C列を消す
 * =========================================================
 *
 * 役割:
 * - 不動産系や通常固定ページ一覧を散らかさない
 * - 民泊固定ページ専用一覧でだけ補助列を見せる
 */
if (!function_exists('mpb_cleanup_b2c_columns_from_normal_page_list')) {
    function mpb_cleanup_b2c_columns_from_normal_page_list($columns)
    {
        if (is_admin() && empty($_GET['mpb_minpaku_pages'])) {
            unset($columns['mpb_template']);
            unset($columns['mpb_layout']);
            unset($columns['mpb_admin_type']);
        }

        return $columns;
    }
}
add_filter('manage_page_posts_columns', 'mpb_cleanup_b2c_columns_from_normal_page_list', 100);

/* =========================================================
 * 民泊固定ページ新規追加画面に案内を出す
 * ========================================================= */
if (!function_exists('mpb_minpaku_new_page_notice')) {
    function mpb_minpaku_new_page_notice()
    {
        global $pagenow;

        if (!is_admin() || $pagenow !== 'post-new.php') {
            return;
        }

        if (empty($_GET['mpb_minpaku_new']) || ($_GET['post_type'] ?? '') !== 'page') {
            return;
        }

        echo '<div class="notice notice-info"><p>';
        echo '<strong>B2C民泊固定ページの作り方:</strong> テンプレートで「民泊B2C共通LP」を選んでください。';
        echo '</p></div>';
    }
}
add_action('admin_notices', 'mpb_minpaku_new_page_notice');


if (!function_exists('mpb_enabled_parts_by_layout')) {
    function mpb_enabled_parts_by_layout($layout_type)
    {
        $map = array(
            'guide'      => array('guide'),
            'difference' => array('difference'),
            'campaign'   => array('feature', 'flow'),
            'faq'        => array('faq'),
            'flow'       => array('flow'),
            'rules'      => array('feature', 'faq'),
            'standard'   => array('feature', 'flow', 'faq'),
        );

        return isset($map[$layout_type]) ? $map[$layout_type] : array('feature');
    }
}

/* =========================================================
 * 追加: 保存対象 field をまとめる
 * =========================================================
 *
 * 役割:
 * - 共通コアは常に保存
 * - パーツは layout_type に応じて保存
 */
if (!function_exists('mpb_collect_save_fields')) {
    function mpb_collect_save_fields($layout_type)
    {
        $fields = mpb_common_field_groups();
        $enabled_parts = mpb_enabled_parts_by_layout($layout_type);

        foreach ($enabled_parts as $part_name) {
            $part_fields = array(
                'plain'    => array(),
                'textarea' => array(),
                'image'    => array(),
                'page_id'  => array(),
            );

            if ($part_name === 'guide') {
                $part_fields = mpb_guide_part_fields();
            } elseif ($part_name === 'difference') {
                $part_fields = mpb_difference_part_fields();
            } elseif ($part_name === 'feature') {
                $part_fields = mpb_feature_part_fields();
            } elseif ($part_name === 'flow') {
                $part_fields = mpb_flow_part_fields();
            } elseif ($part_name === 'faq') {
                $part_fields = mpb_faq_part_fields();
            }

            foreach ($part_fields as $type => $list) {
                $fields[$type] = array_merge($fields[$type], $list);
            }
        }

        return $fields;
    }
}



/* =========================================================
 * 追加パーツON/OFF field
 * =========================================================
 *
 * 役割:
 * - ベース layout_type とは別に
 *   このページで追加したいパーツを個別にON/OFFできるようにする
 *
 * 重要:
 * - ページごとに meta_key 名は変えない
 * - 同じ meta_key を全ページで使い、
 *   値だけページごとに変える
 */
if (!function_exists('mpb_optional_part_switch_fields')) {
    function mpb_optional_part_switch_fields()
    {
        return array(
            '_mpb_use_feature',
            '_mpb_use_flow',
            '_mpb_use_faq',
            '_mpb_use_compare_table',
            '_mpb_use_related_links',
        );
    }
}

/* =========================================================
 * 追加パーツON/OFF を統合した最終パーツ一覧
 * =========================================================
 *
 * 役割:
 * - まず layout_type の基本パーツを取る
 * - その上に ON/OFF の追加パーツを足す
 * - 最終的に、このページで使うパーツ一覧を返す
 */
if (!function_exists('mpb_enabled_parts_for_post')) {
    function mpb_enabled_parts_for_post($post_id)
    {
        $layout_type = mpb_get_layout_type($post_id);
        $parts       = mpb_enabled_parts_by_layout($layout_type);

        $switch_map = array(
            '_mpb_use_feature'       => 'feature',
            '_mpb_use_flow'          => 'flow',
            '_mpb_use_faq'           => 'faq',
            '_mpb_use_compare_table' => 'compare_table',
            '_mpb_use_related_links' => 'related_links',
        );

        foreach ($switch_map as $meta_key => $part_name) {
            $enabled = get_post_meta($post_id, $meta_key, true);

            if ($enabled === '1' && !in_array($part_name, $parts, true)) {
                $parts[] = $part_name;
            }
        }

        return $parts;
    }
}


if (!function_exists('mpb_get_minpaku_page_options')) {
    function mpb_get_minpaku_page_options($exclude_post_id = 0)
    {
        $pages = get_pages(array(
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
            'post_status' => 'publish',
        ));

        $options = array();

        foreach ($pages as $page) {
            if (!$page instanceof WP_Post) {
                continue;
            }

            if ((int) $page->ID === (int) $exclude_post_id) {
                continue;
            }

            $uri = (string) get_page_uri($page->ID);

            if ($uri === '' || strpos($uri, 'minpaku') === false) {
                continue;
            }

            $options[$page->ID] = sprintf(
                '%s  (%s)',
                $page->post_title,
                '/' . ltrim($uri, '/')
            );
        }

        return $options;
    }
}

if (!function_exists('mpb_admin_enqueue_scripts')) {
    function mpb_admin_enqueue_scripts($hook)
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        $screen = get_current_screen();

        if (!$screen || $screen->post_type !== 'page') {
            return;
        }

        $post_id = 0;

        if (!empty($_GET['post'])) {
            $post_id = absint($_GET['post']);
        } elseif (!empty($_POST['post_ID'])) {
            $post_id = absint($_POST['post_ID']);
        }

        if ($hook === 'post.php' && $post_id && !mpb_is_target_template($post_id)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery');

        $inline_js = <<<JS
jQuery(function($){
    function openMpbMediaFrame(target, preview) {
        var frame = wp.media({
            title: '画像を選択',
            button: { text: 'この画像を使用' },
            multiple: false
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + target).val(attachment.id);

            if (preview) {
                $('#' + preview).html('<img src="' + attachment.url + '" style="max-width:240px;height:auto;display:block;">');
            }
        });

        frame.open();
    }

    $(document).on('click', '.mpb-upload-image', function(e){
        e.preventDefault();
        openMpbMediaFrame($(this).data('target'), $(this).data('preview'));
    });

    $(document).on('click', '.mpb-remove-image', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        var preview = $(this).data('preview');
        $('#' + target).val('');
        $('#' + preview).html('');
    });
});
JS;

        wp_add_inline_script('jquery', $inline_js);
    }
}
add_action('admin_enqueue_scripts', 'mpb_admin_enqueue_scripts');

if (!function_exists('mpb_add_meta_boxes')) {
    function mpb_add_meta_boxes()
    {
        global $post;

        if (!$post || $post->post_type !== 'page' || !mpb_is_target_template($post->ID)) {
            return;
        }

        add_meta_box(
            'mpb_page_settings',
            '民泊B2C共通LP 設定',
            'mpb_render_page_settings_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'mpb_add_meta_boxes');

/* =========================================================
 * 管理画面 UI helper
 * 重要:
 * - 以前はここに生HTML断片が混ざって parse error になっていた
 * - 今回は echo / printf で安全に出力する
 * ========================================================= */
if (!function_exists('mpb_render_image_field')) {
    function mpb_render_image_field($post_id, $meta_key, $label, $field_id)
    {
        $attachment_id = absint(get_post_meta($post_id, $meta_key, true));
        $url           = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
        $preview_id    = $field_id . '_preview';

        echo '<div style="margin-bottom:18px;">';
        echo '<p><strong>' . esc_html($label) . '</strong></p>';

        printf(
            '<input type="hidden" id="%1$s" name="%2$s" value="%3$s">',
            esc_attr($field_id),
            esc_attr($meta_key),
            esc_attr($attachment_id)
        );

        printf(
            '<div id="%1$s" style="margin:8px 0;">',
            esc_attr($preview_id)
        );

        if ($url) {
            printf(
                '<img src="%1$s" style="max-width:240px;height:auto;display:block;">',
                esc_url($url)
            );
        }

        echo '</div>';

        printf(
            '<button type="button" class="button mpb-upload-image" data-target="%1$s" data-preview="%2$s">画像を選択</button> ',
            esc_attr($field_id),
            esc_attr($preview_id)
        );

        printf(
            '<button type="button" class="button mpb-remove-image" data-target="%1$s" data-preview="%2$s">画像を削除</button>',
            esc_attr($field_id),
            esc_attr($preview_id)
        );

        echo '</div>';
    }
}

if (!function_exists('mpb_text_input')) {
    function mpb_text_input($post_id, $meta_key, $label)
    {
        $value = mpb_admin_default_text($post_id, $meta_key, '');

        echo '<p style="margin-bottom:16px;">';
        echo '<label>';
        echo '<strong>' . esc_html($label) . '</strong><br>';
        printf(
            '<input type="text" name="%1$s" value="%2$s" style="width:100%%;max-width:100%%;">',
            esc_attr($meta_key),
            esc_attr($value)
        );
        echo '</label>';
        echo '</p>';
    }
}

if (!function_exists('mpb_textarea_input')) {
    function mpb_textarea_input($post_id, $meta_key, $label, $rows = 4)
    {
        $value = mpb_admin_default_text($post_id, $meta_key, '');

        echo '<p style="margin-bottom:16px;">';
        echo '<label>';
        echo '<strong>' . esc_html($label) . '</strong><br>';
        printf(
            '<textarea name="%1$s" rows="%2$s" style="width:100%%;max-width:100%%;">%3$s</textarea>',
            esc_attr($meta_key),
            esc_attr($rows),
            esc_textarea($value)
        );
        echo '</label>';
        echo '</p>';
    }
}

if (!function_exists('mpb_page_select_input')) {
    function mpb_page_select_input($post_id, $meta_key, $label)
    {
        $selected = mpb_admin_default_page_id($post_id, $meta_key);
        $options  = mpb_get_minpaku_page_options($post_id);

        echo '<p style="margin-bottom:16px;">';
        echo '<label>';
        echo '<strong>' . esc_html($label) . '</strong><br>';
        printf(
            '<select name="%1$s" style="width:100%%;max-width:100%%;">',
            esc_attr($meta_key)
        );
        echo '<option value="0">選択してください</option>';

        foreach ($options as $page_id => $page_label) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($page_id),
                selected((int) $selected, (int) $page_id, false),
                esc_html($page_label)
            );
        }

        echo '</select>';
        echo '</label>';
        echo '</p>';
    }
}

/* =========================================================
 * メタボックス本体
 * 重要:
 * - 下部導線ボタンセクションは削除済み
 * - hero / intro / feature / flow / faq / cta だけを残す
 * ========================================================= */

/* =========================================================
 * layout_type 選択UI
 * =========================================================
 *
 * 役割:
 * - 管理画面で guide / difference / campaign などを選べるようにする
 * - slug ではなく、この選択値を使って表示パーツを切り替える
 */
if (!function_exists('mpb_layout_type_select')) {
    function mpb_layout_type_select($post_id, $meta_key, $label)
    {
        $selected = mpb_get_layout_type($post_id);
        $options  = mpb_layout_type_options();

        echo '<p style="margin-bottom:16px;">';
        echo '<label>';
        echo '<strong>' . esc_html($label) . '</strong><br>';
        echo '<select name="' . esc_attr($meta_key) . '" style="width:100%;max-width:100%;">';

        foreach ($options as $value => $text) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($selected, $value, false) . '>' . esc_html($text) . '</option>';
        }

        echo '</select>';
        echo '</label>';
        echo '</p>';
    }
}



/* =========================================================
 * チェックボックスUI
 * =========================================================
 *
 * 役割:
 * - 管理画面で追加パーツを ON/OFF できるようにする
 */
if (!function_exists('mpb_checkbox_input')) {
    function mpb_checkbox_input($post_id, $meta_key, $label)
    {
        $value = get_post_meta($post_id, $meta_key, true);

        echo '<p style="margin-bottom:10px;">';
        echo '<label>';
        echo '<input type="checkbox" name="' . esc_attr($meta_key) . '" value="1" ' . checked($value, '1', false) . '> ';
        echo esc_html($label);
        echo '</label>';
        echo '</p>';
    }
}


if (!function_exists('mpb_render_page_settings_metabox')) {
    function mpb_render_page_settings_metabox($post)
    {
        wp_nonce_field('mpb_save_meta', 'mpb_meta_nonce');

        $layout_type = mpb_get_layout_type($post->ID);

        /**
         * いま保存済みのベース layout_type + 追加パーツON/OFF を統合した
         * 最終パーツ一覧を使う
         *
         * 重要:
         * - 追加パーツは「更新後の再読み込み」で反映する
         * - その場で即時切替ではない
         */
        $enabled_parts = function_exists('mpb_enabled_parts_for_post')
            ? mpb_enabled_parts_for_post($post->ID)
            : mpb_enabled_parts_by_layout($layout_type);

        /**
         * =====================================================
         * 基本設定
         * =====================================================
         *
         * 役割:
         * - ページの土台を決める
         * - まずここで layout_type と追加パーツを選ぶ
         */
        echo '<h3>基本設定</h3>';
        mpb_layout_type_select($post->ID, '_mpb_layout_type', 'レイアウト種別');

        echo '<p style="margin:0 0 16px;color:#666;">';
        echo '現在のレイアウト種別: <strong>' . esc_html($layout_type) . '</strong>';
        echo '</p>';

        echo '<hr><h3>追加パーツ</h3>';
        mpb_checkbox_input($post->ID, '_mpb_use_feature', '特徴セクションを追加');
        mpb_checkbox_input($post->ID, '_mpb_use_flow', 'Flow パーツを追加');
        mpb_checkbox_input($post->ID, '_mpb_use_faq', 'FAQ パーツを追加');
        mpb_checkbox_input($post->ID, '_mpb_use_compare_table', '比較表を追加');
        mpb_checkbox_input($post->ID, '_mpb_use_related_links', '関連リンクを追加');

        echo '<p style="margin:8px 0 0;color:#666;">';
        echo '本文は上のブロックエディタ本文欄で編集します。ここでは本文の前後に出すパーツを設定します。';
        echo '</p>';

        /**
         * =====================================================
         * HERO
         * =====================================================
         */
        echo '<hr><h3>HERO</h3>';
        mpb_text_input($post->ID, '_mpb_hero_eyebrow', 'HERO アイブロー');
        mpb_text_input($post->ID, '_mpb_hero_title', 'HERO タイトル');
        mpb_textarea_input($post->ID, '_mpb_hero_lead', 'HERO 説明');
        mpb_render_image_field($post->ID, '_mpb_hero_image_id', 'HERO 画像', 'mpb_hero_image_id');
        mpb_text_input($post->ID, '_mpb_hero_btn1_text', 'HERO ボタン1 文言');
        mpb_page_select_input($post->ID, '_mpb_hero_btn1_page_id', 'HERO ボタン1 遷移先');
        mpb_text_input($post->ID, '_mpb_hero_btn2_text', 'HERO ボタン2 文言');
        mpb_page_select_input($post->ID, '_mpb_hero_btn2_page_id', 'HERO ボタン2 遷移先');

        /**
         * =====================================================
         * 導入
         * =====================================================
         */
        echo '<hr><h3>導入</h3>';
        mpb_text_input($post->ID, '_mpb_intro_eyebrow', '導入 アイブロー');
        mpb_text_input($post->ID, '_mpb_intro_title', '導入 見出し');
        mpb_textarea_input($post->ID, '_mpb_intro_text', '導入 本文');
        mpb_render_image_field($post->ID, '_mpb_intro_image_id', '導入 画像', 'mpb_intro_image_id');

        /**
         * =====================================================
         * ベース本体: Guide
         * =====================================================
         */
        if (in_array('guide', $enabled_parts, true)) {
            echo '<hr><h3>Guide カード</h3>';

            for ($i = 1; $i <= 4; $i++) {
                mpb_text_input($post->ID, '_mpb_guide_card' . $i . '_title', 'Guide カード' . $i . ' 見出し');
                mpb_textarea_input($post->ID, '_mpb_guide_card' . $i . '_text', 'Guide カード' . $i . ' 本文', 3);
                mpb_render_image_field($post->ID, '_mpb_guide_card' . $i . '_image_id', 'Guide カード' . $i . ' 画像', 'mpb_guide_card' . $i . '_image_id');
            }
        }

        /**
         * =====================================================
         * ベース本体: Difference
         * =====================================================
         */
        if (in_array('difference', $enabled_parts, true)) {
            echo '<hr><h3>Difference 比較</h3>';

            for ($i = 1; $i <= 3; $i++) {
                mpb_text_input($post->ID, '_mpb_diff_card' . $i . '_title', '比較カード' . $i . ' 見出し');
                mpb_textarea_input($post->ID, '_mpb_diff_card' . $i . '_text', '比較カード' . $i . ' 本文', 3);
                mpb_render_image_field($post->ID, '_mpb_diff_card' . $i . '_image_id', '比較カード' . $i . ' 画像', 'mpb_diff_card' . $i . '_image_id');
            }
        }

        /**
         * =====================================================
         * ベース本体: 特徴セクション
         * =====================================================
         */
        if (in_array('feature', $enabled_parts, true)) {
            echo '<hr><h3>特徴セクション</h3>';

            for ($i = 1; $i <= 3; $i++) {
                mpb_text_input($post->ID, '_mpb_feature' . $i . '_eyebrow', '特徴' . $i . ' アイブロー');
                mpb_text_input($post->ID, '_mpb_feature' . $i . '_title', '特徴' . $i . ' 見出し');
                mpb_textarea_input($post->ID, '_mpb_feature' . $i . '_text', '特徴' . $i . ' 本文');
                mpb_render_image_field($post->ID, '_mpb_feature' . $i . '_image_id', '特徴' . $i . ' 画像', 'mpb_feature' . $i . '_image_id');
                mpb_text_input($post->ID, '_mpb_feature' . $i . '_btn1_text', '特徴' . $i . ' ボタン1 文言');
                mpb_page_select_input($post->ID, '_mpb_feature' . $i . '_btn1_page_id', '特徴' . $i . ' ボタン1 遷移先');
                mpb_text_input($post->ID, '_mpb_feature' . $i . '_btn2_text', '特徴' . $i . ' ボタン2 文言');
                mpb_page_select_input($post->ID, '_mpb_feature' . $i . '_btn2_page_id', '特徴' . $i . ' ボタン2 遷移先');
            }
        }

        /**
         * =====================================================
         * 比較表
         * =====================================================
         */
        if (in_array('compare_table', $enabled_parts, true) || in_array('difference', $enabled_parts, true)) {
            echo '<hr><h3>比較表</h3>';
            mpb_text_input($post->ID, '_mpb_compare_table_title', '比較表 見出し');
            mpb_textarea_input($post->ID, '_mpb_compare_table_html', '比較表 テキスト / HTML', 8);
        }

        /**
         * =====================================================
         * Flow
         * =====================================================
         */
        if (in_array('flow', $enabled_parts, true)) {
            echo '<hr><h3>Flow パーツ</h3>';

            mpb_text_input($post->ID, '_mpb_flow_eyebrow', 'Flow アイブロー');
            mpb_text_input($post->ID, '_mpb_flow_title', 'Flow 見出し');
            mpb_textarea_input($post->ID, '_mpb_flow_text', 'Flow 説明');

            for ($i = 1; $i <= 4; $i++) {
                mpb_text_input($post->ID, '_mpb_flow_' . $i . '_title', 'STEP ' . $i . ' 見出し');
                mpb_textarea_input($post->ID, '_mpb_flow_' . $i . '_text', 'STEP ' . $i . ' 本文', 3);
            }
        }

        /**
         * =====================================================
         * FAQ
         * =====================================================
         */
        if (in_array('faq', $enabled_parts, true)) {
            echo '<hr><h3>FAQ パーツ</h3>';

            mpb_text_input($post->ID, '_mpb_faq_eyebrow', 'FAQ アイブロー');
            mpb_text_input($post->ID, '_mpb_faq_title', 'FAQ 見出し');
            mpb_textarea_input($post->ID, '_mpb_faq_text', 'FAQ 説明');

            for ($i = 1; $i <= 10; $i++) {
                mpb_textarea_input($post->ID, '_mpb_faq_' . $i . '_q', 'FAQ ' . $i . ' 質問', 2);
                mpb_textarea_input($post->ID, '_mpb_faq_' . $i . '_a', 'FAQ ' . $i . ' 回答', 3);
            }
        }

        /**
         * =====================================================
         * 関連リンク
         * =====================================================
         */
        if (in_array('related_links', $enabled_parts, true)) {
            echo '<hr><h3>関連リンク</h3>';
            mpb_text_input($post->ID, '_mpb_related_links_title', '関連リンク 見出し');
            mpb_textarea_input($post->ID, '_mpb_related_links_text', '関連リンク 説明', 3);

            for ($i = 1; $i <= 6; $i++) {
                mpb_text_input($post->ID, '_mpb_related_link' . $i . '_text', '関連リンク' . $i . ' 文言');
                mpb_page_select_input($post->ID, '_mpb_related_link' . $i . '_page_id', '関連リンク' . $i . ' 遷移先');
            }
        }

        /**
         * =====================================================
         * CTA
         * =====================================================
         */
        echo '<hr><h3>CTA</h3>';
        mpb_text_input($post->ID, '_mpb_cta_title', 'CTA 見出し');
        mpb_textarea_input($post->ID, '_mpb_cta_text', 'CTA 本文');
        mpb_render_image_field($post->ID, '_mpb_cta_image_id', 'CTA 画像', 'mpb_cta_image_id');
        mpb_text_input($post->ID, '_mpb_cta_btn1_text', 'CTA ボタン1 文言');
        mpb_page_select_input($post->ID, '_mpb_cta_btn1_page_id', 'CTA ボタン1 遷移先');
        mpb_text_input($post->ID, '_mpb_cta_btn2_text', 'CTA ボタン2 文言');
        mpb_page_select_input($post->ID, '_mpb_cta_btn2_page_id', 'CTA ボタン2 遷移先');
    }
}


/* =========================================================
 * 保存処理
 * ========================================================= */
if (!function_exists('mpb_save_meta')) {
    function mpb_save_meta($post_id)
    {
        /**
         * =====================================================
         * nonce / autosave / 権限 / 対象テンプレートの確認
         * =====================================================
         */
        if (
            !isset($_POST['mpb_meta_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mpb_meta_nonce'])), 'mpb_save_meta')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'page' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!mpb_is_target_template($post_id)) {
            return;
        }

        /**
         * =====================================================
         * 現在の layout_type を取得
         * =====================================================
         *
         * 役割:
         * - guide / difference / campaign / faq / flow / rules / standard
         *   のどれを保存対象にするか決める
         * - 不正値は standard に戻す
         */
        $layout_type = isset($_POST['_mpb_layout_type'])
            ? sanitize_text_field(wp_unslash($_POST['_mpb_layout_type']))
            : 'standard';

        $allowed = array_keys(mpb_layout_type_options());

        if (!in_array($layout_type, $allowed, true)) {
            $layout_type = 'standard';
        }

        /**
         * =====================================================
         * 共通コア + 有効パーツだけを保存
         * =====================================================
         */
        $fields = mpb_collect_save_fields($layout_type);

        /**
         * =====================================================
         * POSTされた追加パーツON/OFF も保存対象に反映
         * =====================================================
         *
         * 役割:
         * - guide + faq
         * - difference + flow
         * - standard + compare_table
         * のような組み合わせ時に
         * そのパーツ用 field も保存対象へ追加する
         */
        $optional_switch_map = array(
            '_mpb_use_feature'       => 'feature',
            '_mpb_use_flow'          => 'flow',
            '_mpb_use_faq'           => 'faq',
            '_mpb_use_compare_table' => 'compare_table',
            '_mpb_use_related_links' => 'related_links',
        );

        foreach ($optional_switch_map as $switch_key => $part_name) {
            $is_enabled = isset($_POST[$switch_key]) && sanitize_text_field(wp_unslash($_POST[$switch_key])) === '1';

            if (!$is_enabled) {
                continue;
            }

            $part_fields = function_exists('mpb_part_fields_by_name')
                ? mpb_part_fields_by_name($part_name)
                : array(
                    'plain' => array(),
                    'textarea' => array(),
                    'image' => array(),
                    'page_id' => array(),
                );

            foreach ($part_fields as $type => $list) {
                if (!isset($fields[$type])) {
                    $fields[$type] = array();
                }

                $fields[$type] = array_values(array_unique(array_merge($fields[$type], $list)));
            }
        }

        foreach ($fields['plain'] as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        foreach ($fields['textarea'] as $field) {
            $value = isset($_POST[$field]) ? sanitize_textarea_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        foreach ($fields['image'] as $field) {
            $value = isset($_POST[$field]) ? absint(wp_unslash($_POST[$field])) : 0;
            update_post_meta($post_id, $field, $value);
        }

        foreach ($fields['page_id'] as $field) {
            $value = isset($_POST[$field]) ? absint(wp_unslash($_POST[$field])) : 0;
            update_post_meta($post_id, $field, $value);
        }

        /**
         * =====================================================
         * 追加パーツON/OFF保存
         * =====================================================
         *
         * 役割:
         * - checkbox は未チェック時に POST に来ない
         * - そのため 1 / 0 を明示保存する
         */
        foreach (mpb_optional_part_switch_fields() as $field) {
            $value = isset($_POST[$field]) ? '1' : '0';
            update_post_meta($post_id, $field, $value);
        }
    }
}

add_action('save_post_page', 'mpb_save_meta');

/* =========================================================
 * フロント側 CSS 読み込み
 * ========================================================= */
if (!function_exists('mpb_enqueue_front_assets')) {
    function mpb_enqueue_front_assets()
    {
        if (!is_page()) {
            return;
        }

        $post_id = get_queried_object_id();

        if (!$post_id || !mpb_is_target_template($post_id)) {
            return;
        }

        $rel  = '/minpaku/b2c/css/minpaku-b2c.css';
        $path = get_template_directory() . $rel;

        if (!file_exists($path)) {
            return;
        }

        $ver = function_exists('naigai_asset_ver') ? naigai_asset_ver($rel) : filemtime($path);

        wp_enqueue_style(
            'minpaku-b2c-css',
            get_template_directory_uri() . $rel,
            array(),
            $ver
        );
    }
}
add_action('wp_enqueue_scripts', 'mpb_enqueue_front_assets');
