<?php
/**
 * =========================================================
 * template-parts/contact/customer-info-form-inner.php
 *
 * 既存 Customer Information Form のフォーム本体。
 *
 * 使用箇所:
 * - /contact
 * - /iezukuri/contact
 *
 * 注意:
 * - header/footer はここでは出さない。
 * - CSS/JS は inc/functions/functions-customer-info-form.php で読み込む。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}







if (!function_exists('naigai_customer_info_form_is_iezukuri_contact')) {
    function naigai_customer_info_form_is_iezukuri_contact() {
        $path = isset($_SERVER['REQUEST_URI'])
            ? trim((string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')
            : '';

        return $path === 'iezukuri/contact';
    }
}

if (!function_exists('naigai_customer_info_form_rule_url')) {
    function naigai_customer_info_form_rule_url() {
        if (naigai_customer_info_form_is_iezukuri_contact()) {
            $page = get_page_by_path('iezukuri/rule');
            return $page ? get_permalink($page) : home_url('/iezukuri/rule/');
        }

        $page = get_page_by_path('rule');
        return $page ? get_permalink($page) : home_url('/rule/');
    }
}

if (!function_exists('naigai_customer_info_form_privacy_url')) {
    function naigai_customer_info_form_privacy_url() {
        if (naigai_customer_info_form_is_iezukuri_contact()) {
            $page = get_page_by_path('iezukuri/privacypolicy');
            return $page ? get_permalink($page) : home_url('/iezukuri/privacypolicy/');
        }

        $page = get_page_by_path('privacypolicy');
        return $page ? get_permalink($page) : home_url('/privacypolicy/');
    }
}

$jobs = function_exists('naigai_customer_info_job_options')
    ? naigai_customer_info_job_options()
    : array(
        ''   => '',
        '1'  => '社長 / 代表者',
        '2'  => '会社員',
        '99' => 'その他',
    );

$prefectures = array(
    '北海道',
    '青森県',
    '岩手県',
    '宮城県',
    '秋田県',
    '山形県',
    '福島県',
    '茨城県',
    '栃木県',
    '群馬県',
    '埼玉県',
    '千葉県',
    '東京都',
    '神奈川県',
    '新潟県',
    '富山県',
    '石川県',
    '福井県',
    '山梨県',
    '長野県',
    '岐阜県',
    '静岡県',
    '愛知県',
    '三重県',
    '滋賀県',
    '京都府',
    '大阪府',
    '兵庫県',
    '奈良県',
    '和歌山県',
    '鳥取県',
    '島根県',
    '岡山県',
    '広島県',
    '山口県',
    '徳島県',
    '香川県',
    '愛媛県',
    '高知県',
    '福岡県',
    '佐賀県',
    '長崎県',
    '熊本県',
    '大分県',
    '宮崎県',
    '鹿児島県',
    '沖縄県',
);
?>

<div class="customer-info-page">
    <div class="customer-info-form-wrap">

        <section id="customer-info-step-input" class="customer-info-step">
            <div class="customer-info-card">
                <div class="customer-info-card__header">
                    <p class="customer-info-card__eyebrow">Customer Information</p>
                    <h2>お客様情報入力</h2>
                    <p>必要事項をご入力のうえ、確認画面へお進みください。</p>
                </div>

                <form id="customer-info-form" action="" method="post" novalidate>
                    <div class="customer-info-grid">
                        <div class="customer-info-field is-full">
                            <label for="name">名前 <span class="required">必須</span></label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="customer-info-field is-full">
                            <label for="email">メールアドレス <span class="required">必須</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="customer-info-field is-full">
                            <label for="phone">電話番号</label>
                            <input type="tel" id="phone" name="phone">
                        </div>

                        <div class="customer-info-field">
                            <label for="prefecture">都道府県 <span class="required">必須</span></label>
                            <select name="prefecture" id="prefecture" required>
                                <option value="">都道府県を選択してください</option>
                                <?php foreach ($prefectures as $prefecture) : ?>
                                    <option value="<?php echo esc_attr($prefecture); ?>"><?php echo esc_html($prefecture); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="customer-info-field">
                            <label for="city">市区町村 <span class="required">必須</span></label>
                            <input type="text" id="city" name="city" required>
                        </div>

                        <div class="customer-info-field is-full">
                            <label for="address_number">番地 <span class="required">必須</span></label>
                            <input type="text" id="address_number" name="address_number" required>
                        </div>

                        <div class="customer-info-field">
                            <label for="residence_years">居住年数 <span class="required">必須</span></label>
                            <input type="number" id="residence_years" name="residence_years" min="1" required>
                        </div>

                        <div class="customer-info-field">
                            <label for="job">職業</label>
                            <select name="job" id="job">
                                <?php foreach ($jobs as $job_value => $job_label) : ?>
                                    <option value="<?php echo esc_attr($job_value); ?>"><?php echo esc_html($job_label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="customer-info-field">
                            <label for="age">年齢</label>
                            <input type="number" id="age" name="age" min="0">
                        </div>

                        <fieldset class="customer-info-field customer-info-field--gender">
                            <legend>性別 <span class="required">必須</span></legend>
                            <div class="gender-selection">
                                <label class="gender-selection__item">
                                    <input type="radio" name="gender" value="male" required>
                                    <span>男性</span>
                                </label>
                                <label class="gender-selection__item">
                                    <input type="radio" name="gender" value="female" required>
                                    <span>女性</span>
                                </label>
                            </div>
                        </fieldset>

                        <div class="customer-info-field is-full">
                            <label for="other_address">メッセージ</label>
                            <textarea
                                id="other_address"
                                name="other_address"
                                rows="5"
                                maxlength="500"
                                placeholder="ご自由にご記入ください（500文字以内）"></textarea>
                            <p class="customer-info-help">※ご相談内容・ご要望などをご自由にご記入ください（500文字以内）</p>
                            <p class="customer-info-count"><span class="js-message-count">0</span>/500文字</p>
                        </div>
                    </div>

                    <p class="customer-info-note">
                        ※<a href="<?php echo esc_url(naigai_customer_info_form_rule_url()); ?>" target="_blank" rel="noopener noreferrer">利用規約</a>
                        および
                        <a href="<?php echo esc_url(naigai_customer_info_form_privacy_url()); ?>" target="_blank" rel="noopener noreferrer">プライバシーポリシー</a>
                        をご確認のうえ、確認画面へお進みください。
                    </p>

                    <div class="customer-info-actions">
                        <button type="button" class="btn button2 js-customer-info-open-confirm">上記に同意して確認画面へ</button>
                    </div>

                    <p class="customer-info-subnote">
                        ※メールアドレスに誤りがある場合は、お電話でご連絡する場合がございます。
                    </p>

                    <div class="js-customer-info-error customer-info-message" aria-live="polite"></div>
                </form>
            </div>
        </section>

        <section id="customer-info-step-confirm" class="customer-info-step" hidden>
            <div class="customer-info-card customer-info-card--confirm">
                <div class="customer-info-card__header">
                    <p class="customer-info-card__eyebrow">Confirmation</p>
                    <h2>入力内容の確認</h2>
                    <p>内容をご確認のうえ、問題なければ送信してください。</p>
                </div>

                <dl class="customer-info-confirm__list">
                    <div class="customer-info-confirm__row">
                        <dt>名前</dt>
                        <dd data-confirm="name"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>メールアドレス</dt>
                        <dd data-confirm="email"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>電話番号</dt>
                        <dd data-confirm="phone"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>都道府県</dt>
                        <dd data-confirm="prefecture"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>市区町村</dt>
                        <dd data-confirm="city"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>番地</dt>
                        <dd data-confirm="address_number"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>居住年数</dt>
                        <dd data-confirm="residence_years"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>職業</dt>
                        <dd data-confirm="job"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>年齢</dt>
                        <dd data-confirm="age"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>性別</dt>
                        <dd data-confirm="gender"></dd>
                    </div>
                    <div class="customer-info-confirm__row">
                        <dt>メッセージ</dt>
                        <dd data-confirm="other_address"></dd>
                    </div>
                </dl>

                <div class="customer-info-confirm__actions">
                    <button type="button" class="btn button2 js-customer-info-send">この内容で送信する</button>
                    <button type="button" class="btn button2 is-secondary js-customer-info-back">入力画面に戻る</button>
                </div>

                <div class="js-customer-info-message customer-info-message" aria-live="polite"></div>
            </div>
        </section>

        <section id="customer-info-step-thanks" class="customer-info-step" hidden>
    <?php
    /**
     * 共通サンクスUI。
     *
     * 送信成功の判定自体は、
     * 今まで通り customer-info-form.js が担当する。
     */
    get_template_part(
        'template-parts/common/thanks-state',
        null,
        array(
            'variant'      => 'contact',
            'heading_tag'  => 'h2',
            'title'        => 'ありがとうございます',
            'message'      => '送信を受け付けました。<br>内容を確認のうえ、担当者よりご連絡いたします。',
            'button_label' => '入力画面へ戻る',
            'button_type'  => 'button',
            'button_class' => 'btn button2 is-secondary js-customer-info-reset',
        )
    );
    ?>
</section>

    </div>
</div>
