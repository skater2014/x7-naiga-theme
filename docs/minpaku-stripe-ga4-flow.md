# 民泊 Stripe → GTM → GA4 学習メモ

このファイルは、民泊予約のオンライン決済と
GA4購入計測の流れを初心者向けに整理したものです。

---

# 1. 全体の流れ

利用者が宿泊日・人数を選択

↓

JavaScriptで料金を表示

↓

PHPへ予約内容を送信

↓

PHPでもう一度料金を計算

↓

PHPからStripe APIへPaymentIntent作成を依頼

↓

Stripeからclient_secretを受け取る

↓

JavaScriptでStripe Payment Elementを表示

↓

利用者がカード情報を入力

↓

JavaScriptのconfirmPayment()で決済確定

↓

Stripeから

status = succeeded

が返る

↓

サンクス画面表示

↓

dataLayer.push()

↓

event = minpaku_purchase

↓

Google Tag Manager

↓

GA4 purchase

↓

GA4で

・購入件数
・売上金額
・取引ID
・宿泊施設名

を確認する


---

# 2. 主に見るファイル

## JavaScript

wp-content/themes/x7-naigaicorp-20260713/minpaku/common/js/minpaku-single.js

役割:

- 宿泊日・人数の操作
- 料金表示
- Stripe Payment Element
- Stripe confirmPayment()
- 決済成功確認
- サンクス表示
- dataLayer.push()
- minpaku_purchase 発火


## PHP

wp-content/themes/x7-naigaicorp-20260713/minpaku/inc/common-core.php

役割:

- 民泊施設情報
- Stripe決済ON/OFF
- Stripe秘密鍵取得
- PHP側で宿泊料金を再計算
- Stripe PaymentIntent作成
- Stripe APIとの通信


## PHP → JavaScript

wp-content/themes/x7-naigaicorp-20260713/minpaku/inc/frontend/localize.php

役割:

PHP側の情報をJavaScriptへ渡す。

主な値:

- paymentEnabled
- ajaxUrl
- nonce
- Stripe publishable key


## Google Tag Manager

wp-content/themes/x7-naigaicorp-20260713/header-77.php

役割:

- GTMをWebページへ読み込む
- ローカル用GTM
- 本番用GTM

を環境によって切り替える。


---

# 3. PHP側の決済処理

common-core.php の

11. PaymentIntent 作成

を見る。


関数:

mnpk_create_payment_intent()


大まかな流れ:

JavaScript

↓

WordPress admin-ajax.php

↓

mnpk_create_payment_intent()

↓

PHPで料金再計算

↓

Stripe API

https://api.stripe.com/v1/payment_intents

↓

Stripe PaymentIntent作成

↓

client_secretをJavaScriptへ返す


---

# 4. PHPでStripeへ送っている情報

主なデータ:

amount
= 支払金額

currency
= jpy

description
= 宿泊施設名 + ご宿泊料金

metadata[post_id]
= WordPress投稿ID

metadata[stay_title]
= 宿泊施設名

metadata[checkin]
= チェックイン日

metadata[checkout]
= チェックアウト日

metadata[adults]
= 大人人数

metadata[children]
= 子ども人数

metadata[nights]
= 宿泊日数

metadata[name]
= 予約者名

metadata[email]
= メールアドレス


---

# 5. JavaScript側の決済確定

minpaku-single.js の

stripeInstance.confirmPayment()

を見る。


流れ:

Stripe Payment Element

↓

confirmPayment()

↓

Stripe

↓

paymentIntent.status === 'succeeded'

↓

決済成功


---

# 6. GA4購入計測

決済成功後、

pushMinpakuPurchaseEvent(paymentIntent)

が実行される。


dataLayerへ次の情報を送る。


event
= minpaku_purchase


ecommerce.transaction_id
= Stripe PaymentIntent ID


ecommerce.value
= 決済総額


ecommerce.currency
= JPY


ecommerce.items[].item_id
= WordPress民泊投稿ID


ecommerce.items[].item_name
= 宿泊施設名


ecommerce.items[].quantity
= 1


---

# 7. GTMからGA4

JavaScriptはGA4へ直接送信しない。


JavaScript

↓

dataLayer

↓

Google Tag Manager

↓

GA4


GTMでは、

カスタムイベント

minpaku_purchase

をトリガーにして、

GA4イベント

purchase

を送信する。


---

# 8. transaction_id

StripeのPaymentIntent IDを使う。


例:

pi_xxxxxxxxxxxxxxxxx


GA4では購入取引IDになる。


1決済ごとに異なるIDなので、
購入の重複確認にも使える。


---

# 9. value

今回の決済総額。

例:

399997

GA4では

¥399,997

として表示される。


---

# 10. item_name

民泊施設の名前。

現在はcheckout画面の

data-checkout-stay-title

に表示されている文字を取得している。


例:

test minpaku1


本番で正式な物件名になれば、

那須○○貸別荘

などがGA4の「アイテム名」に表示される。


---

# 11. Stripe決済ON / OFF

common-core.php の

mnpk_is_payment_feature_enabled()

で環境全体の決済可否を判定する。


ローカル:

127.0.0.1
localhost

は開発用としてON。


本番:

明示的にONにするまでOFF。


本番で正式にStripe決済を開始する場合:

wp-config.php 等で

define('NAIGAI_MINPAKU_PAYMENT_ENABLED', true);

を設定する。


---

# 12. 覚えておく流れ

PHP
PaymentIntentを作る

↓

JavaScript
confirmPaymentする

↓

Stripe
決済成功

↓

JavaScript
dataLayerへpurchase情報を入れる

↓

GTM
purchaseイベントへ変換

↓

GA4
購入件数・売上を記録
