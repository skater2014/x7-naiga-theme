<?php
if (!defined('ABSPATH')) exit;

$socials = [
  'twitter'   => 'twitter',
  'facebook'  => 'facebook2',
  'google'    => 'google',
  'instagram' => 'instagram',
  'pinterest' => 'pinterest',
  'vimeo'     => 'vimeo',
  'youtube'   => 'youtube',
  'linkedin'  => 'linkedin',
  'discord'   => 'discord',
];
?>

<div class="social-nav sns_button mhl ptl">
<?php foreach ($socials as $name => $icon) :
  $url = trim((string) get_theme_mod('footer_' . $name, ''));
  if ($url === '') continue;
?>
  <div class="icon-container">
    <a href="<?php echo esc_url($url); ?>"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="<?php echo esc_attr($name); ?>">
      <svg class="sns-icon" aria-hidden="true">
        <use href="#icon-<?php echo esc_attr($icon); ?>"></use>
      </svg>
    </a>
  </div>
<?php endforeach; ?>
</div>
