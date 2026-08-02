<?php

if (!defined('ABSPATH')) {
    exit;
}

if (
    !empty(
        $GLOBALS[
            'naigai_iezukuri_back_link_output_done'
        ]
    )
) {
    return;
}

$GLOBALS[
    'naigai_iezukuri_back_link_output_done'
] = true;
?>

<nav
    class="iez-plan-single-back-nav iezukuri-common-back-nav"
    data-iezukuri-common-back-link
    aria-label="前のページへ戻る"
>
    <a
        class="iez-plan-single-back-nav__link"
        href="#"
        onclick="window.history.go(-1); return false;"
    >
        ← 前のページへ戻る
    </a>
</nav>
