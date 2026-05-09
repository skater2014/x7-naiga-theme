<?php
/**
 * hub/pages/iezukuri/inc/icon-catalog.php
 *
 * 家づくり 固定ページ用 SVG アイコンカタログ
 *
 * 役割:
 * - トップページの「家づくり入口カード」などで使う導線アイコンを管理する。
 * - CPT名や固定ページスラッグではなく、アイコン選択用の内部キー。
 *
 * 注意:
 * - 間取り詳細 iez_plan の特徴アイコンは inc/plan-feature-catalog.php 側。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_icon_choices')) {
    function naigai_iez_icon_choices() {
        return array(
            'new-house'       => '新築住宅',
            'hiraya'          => '平屋',
            'two-story'       => '二階建て住宅',
            'two-family'      => '二世帯住宅',
            'renovation'      => '住宅リフォーム',
            'used-renovation' => '中古住宅リフォーム',
            'dual-life'       => '二拠点生活',
            'consultation'    => '相談',
        );
    }
}

if (!function_exists('naigai_iez_icon')) {
    function naigai_iez_icon($key, $class = 'iez-icon') {
        $key = sanitize_key($key);

        $icons = array(
            'new-house'       => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 30 32 10l24 20v24a4 4 0 0 1-4 4H12a4 4 0 0 1-4-4V30Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M24 58V38h16v20" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'hiraya'          => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M6 34 32 16l26 18" fill="none" stroke="currentColor" stroke-width="4"/><path d="M12 34h40v20H12z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M22 54V40h20v14" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'two-story'       => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 28 32 10l22 18v30H10V28Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M18 34h28M18 44h28M24 58V44h16v14" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'two-family'      => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 30 24 16l16 14v28H8V30Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M28 30 44 16l12 11v31H28" fill="none" stroke="currentColor" stroke-width="4"/><path d="M18 58V42h12v16M42 58V42h8v16" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'renovation'      => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 32 32 14l22 18v24H10V32Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M40 12 52 24M45 7l12 12M20 56V42h16v14" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'used-renovation' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 32 32 12l24 20v24H8V32Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M22 56V42h14v14M42 40l10 10M52 40 42 50" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'dual-life'       => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 36 24 22l16 14v20H8V36Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M34 28 46 18l10 10v28H34" fill="none" stroke="currentColor" stroke-width="4"/><path d="M20 56V44h10v12M44 56V44h8v12" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
            'consultation'    => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M12 14h40v30H26L14 54V44h-2V14Z" fill="none" stroke="currentColor" stroke-width="4"/><path d="M22 26h20M22 34h14" fill="none" stroke="currentColor" stroke-width="4"/></svg>',
        );

        if (empty($icons[$key])) {
            $key = 'new-house';
        }

        return '<span class="' . esc_attr($class) . ' ' . esc_attr($class . '--' . $key) . '">' . $icons[$key] . '</span>';
    }
}
