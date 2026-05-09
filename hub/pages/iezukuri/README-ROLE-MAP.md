# /iezukuri ROLE MAP

## 大原則

- テーマ直下 functions.php は触らない。
- /iezukuri 関連は hub/pages/iezukuri/ 内に集約する。
- CSS は base / common / top / subpage / page-styles に分ける。
- template-parts は使わない。テンプレート部品は templates/ に集約する。

## inc

- inc/loader.php : /iezukuri 機能の入口。enqueue は書かない。
- inc/functions-iezukuri.php : /iezukuri の主要 function。現状 CSS/JS 読み込みもここに残る。
- inc/helpers.php : 共通ヘルパー。
- inc/plan-* : 間取りプラン関連。

## admin

- admin/metaboxes/ : WordPress管理画面・編集画面・メタボックス用PHP。
- admin/assets/css/ : 管理画面専用CSS。
- admin/assets/js/ : 管理画面専用JS。

## CSS

- css/base/base.css : 全体共通。色・変数だけ。レイアウト禁止。
- css/common/hero.css : hero共通。トップ/サブページで使う。
- css/common/nav.css : header/nav共通。
- css/top/top.css : /iezukuri トップページの骨組み。
- css/subpage/subpage.css : サブページ共通の骨組み。
- css/page-styles/*.css : ページ別CSS。本文PHPとは別。

## JS

- js/iezukuri-nav.js : 共通ナビ。
- js/iezukuri.js : /iezukuri トップ。
- js/iezukuri-subpage.js : サブページ。

## templates

- templates/top/ : /iezukuri トップ専用。旧 front。
- templates/content/ : ページ本文PHP。
- templates/shared/ : 複数ページで使う共通セクション。
- templates/subpage/ : サブページ共通の hero / localnav / cta。
- templates/components/ : 小さい再利用部品。
- templates/template-*.php : ページテンプレート本体。
- templates/pdf-plan.php : PDF用テンプレート。
