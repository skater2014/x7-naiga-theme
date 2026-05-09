<?php
require_once __DIR__ . '/vendor/autoload.php';

function generate_property_pdf() {
    if (!isset($_GET['download_pdf']) || $_GET['download_pdf'] !== '1') {
        return;
    }
    
    $post_id = get_the_ID();
    $title = get_the_title($post_id);
    $content = apply_filters('the_content', get_post_field('post_content', $post_id));
    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    
    $custom_fields = get_post_meta($post_id);
    
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4-L', // A4横向き
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10
    ]);
    
    $html = '<html><head><style>
        body { font-family: Arial, sans-serif; }
        .container { display: flex; flex-direction: row; }
        .left-column { width: 50%; padding: 10px; }
        .right-column { width: 50%; padding: 10px; }
        .property-title { font-size: 20px; font-weight: bold; }
        .property-image { width: 100%; max-height: 200px; object-fit: cover; }
        .property-details { font-size: 14px; margin-top: 10px; }
    </style></head><body>';
    
    $html .= '<h1 class="property-title">' . esc_html($title) . '</h1>';
    
    if ($image_url) {
        $html .= '<img src="' . esc_url($image_url) . '" class="property-image" />';
    }
    
    $html .= '<div class="container">';
    
    // 左カラム（概要とカスタムフィールド）
    $html .= '<div class="left-column">';
    $html .= '<h2>物件概要</h2>';
    foreach ($custom_fields as $key => $value) {
        if (!empty($value[0])) {
            $html .= '<p><strong>' . esc_html($key) . ':</strong> ' . esc_html($value[0]) . '</p>';
        }
    }
    $html .= '</div>';
    
    // 右カラム（本文と追加情報）
    $html .= '<div class="right-column">';
    $html .= '<h2>詳細情報</h2>';
    $html .= '<div class="property-details">' . $content . '</div>';
    $html .= '</div>';
    
    $html .= '</div></body></html>';
    
    $mpdf->WriteHTML($html);
    $mpdf->Output($title . '.pdf', 'D');
    exit;
}
add_action('init', 'generate_property_pdf');
