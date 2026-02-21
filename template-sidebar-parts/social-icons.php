<!--social-nav-->
  <div class="social-nav sns_button mhl ptl">

    <?php
    $social_icons = array(
      array(
        'class' => 'icon-twitter-fill',
        'url'   => 'https://x.com/naigaicorp',
        'icon'  => 'twitter',
      ),
      array(
        'class' => 'icon-facebook2-fill',
        'url'   => 'https://www.facebook.com/profile.php?id=61574911269185',
        'icon'  => 'facebook2',
      ),
      array(
        'class' => 'icon-instagram-fill',
        'url'   => 'https://www.instagram.com/naigaicorp/',
        'icon'  => 'instagram',
      ),
      array(
        'class' => 'icon-youtube-fill',
        'url'   => 'https://www.youtube.com/@%E5%86%85%E5%A4%96%E5%9C%9F%E5%9C%B0%E9%96%8B%E7%99%BA%E6%A0%AA%E5%BC%8F-k7n',
        'icon'  => 'youtube',
      ),
      array(
        'class' => 'icon-rss2-fill',
        'url'   => 'https://naigaicorp.net/feed',
        'icon'  => 'rss2',
      ),
    );

    foreach ($social_icons as $icon) :
    ?>
      <div class="icon-container">
        <a class="<?php echo $icon['class']; ?>" href="<?php echo esc_url($icon['url']); ?>" target="_blank" rel="noopener noreferrer">
          <div class="icon <?php echo $icon['icon']; ?>">
            <svg class="sns-icon <?php echo $icon['class']; ?>"><use xlink:href="#icon-<?php echo $icon['icon']; ?>"></use></svg><span class="name"></span>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
 </div>
  <!--social-nav End-->