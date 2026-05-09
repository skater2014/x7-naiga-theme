<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_icon_svg')) {
    function naigai_ch_icon_svg($name)
    {
        $icons = array(
            'home' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5.5 9.5V21h13V9.5"/><path d="M9 21v-6h6v6"/></svg>',
            'tree' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21V10"/><path d="M7 10h10l-5-7-5 7Z"/><path d="M6 15h12l-6-6-6 6Z"/></svg>',
            'map' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6.5 9 4l6 2.5L21 4v13.5L15 20l-6-2.5L3 20V6.5Z"/><path d="M9 4v13.5"/><path d="M15 6.5V20"/></svg>',
            'compass' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2.4 7-7 2.4 2.4-7 7-2.4Z"/></svg>',
            'layers' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 4 8 4-8 4-8-4 8-4Z"/><path d="m4 12 8 4 8-4"/><path d="m4 16 8 4 8-4"/></svg>',
            'chat' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 18.5 4 21l4.5-2.5H19a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5A2 2 0 0 0 3 6v10.5a2 2 0 0 0 2 2Z"/></svg>',
            'sun' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v3"/><path d="M12 19v3"/><path d="m4.9 4.9 2.1 2.1"/><path d="m17 17 2.1 2.1"/><path d="M2 12h3"/><path d="M19 12h3"/><path d="m4.9 19.1 2.1-2.1"/><path d="m17 7 2.1-2.1"/></svg>',
            'mountain' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 20 7-10 4 6 2-3 5 7H3Z"/></svg>',
            'office' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V6l8-3v18"/><path d="M12 8h8v13"/><path d="M8 8v2"/><path d="M8 13v2"/><path d="M16 12v2"/><path d="M16 16v2"/></svg>',
            'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>',
            'book' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H7a3 3 0 0 0-3 3V5.5Z"/><path d="M8 7h8"/><path d="M8 11h8"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M3 10h18"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 4h3l1.2 4.2-1.8 1.8a16 16 0 0 0 5.2 5.2l1.8-1.8L20 14.5v3A2.5 2.5 0 0 1 17.5 20 13.5 13.5 0 0 1 4 6.5 2.5 2.5 0 0 1 6.5 4Z"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M14 19a4.5 4.5 0 0 1 7 0"/></svg>',
        );

        return isset($icons[$name]) ? $icons[$name] : $icons['home'];
    }
}
