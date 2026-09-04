<?php
if (!defined('ABSPATH')) {
    exit;
}

$internal_nav_items = array(
    array('label' => '3つの住まい',       'href' => '#customhome-services'),
    array('label' => 'Site Reading',       'href' => '#customhome-site-reading'),
    array('label' => '暮らしのポイント', 'href' => '#customhome-works'),
    array('label' => '家づくりの流れ',   'href' => '#customhome-flow'),
    array('label' => '相談する',           'href' => '#customhome-contact'),
);
?>

<nav class="iez-top-internal-nav" aria-label="このページの目次" data-iez-internal-nav="1">
    <div class="ch-shell">
        <ul class="iez-top-internal-nav__list">
            <?php foreach ($internal_nav_items as $item) : ?>
                <li>
                    <a href="<?php echo esc_url($item['href']); ?>">
                        <?php echo esc_html($item['label']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
