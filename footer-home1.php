<?php if (is_home()) : ?>
  </div><!-- End/Page Wrapper -->
<?php endif; ?>

<?php
// アーカイブページの場合にのみ、divタグを閉じる
if (is_archive()) : ?>
  </div><!-- End/Page Wrapper -->
<?php endif; ?>


<?php if (is_singular('land') || is_page()) : //ok
?>
  </div><!-- End/Page Wrapper -->
<?php endif; ?>

<?php if (is_singular('house') || is_page()) : //pk
?>
  </div><!-- End/Page Wrapper -->
<?php endif; ?>


<!--Start Footer Wrapper-->
<footer id="foot" class="footer-wrapper">
  <div class="section">
    <div class="top-foot">
      <div class="foot-col">
        <?php dynamic_sidebar('footer-1'); ?>
      </div>
      <div class="foot-col">
        <?php dynamic_sidebar('footer-2'); ?>
      </div>
      <div class="foot-col">
        <?php dynamic_sidebar('footer-3'); ?>
      </div>
      <div class="clear"></div>
    </div><!-- top-foot -->


    <!-- CHATGPTモーダルの呼び出し -->
    <?php naigai_render_chatgpt_modal(); ?>

    <div class="bottom-foot">
      <div class="copyright">
        <p id="copyright">
          &copy; <?php bloginfo('name'); ?> © 2020 All Rights Reserved.
          | <a href="<?php echo esc_url(function_exists('naigai_footer_privacy_policy_url') ? naigai_footer_privacy_policy_url() : home_url('/privacypolicy/')); ?>">Privacy Policy</a>
        </p>
      </div>
      <div class="foot-socials">
        <ul>
          <?php
          $socials = array('twitter', 'facebook', 'google-plus', 'instagram', 'pinterest', 'vimeo', 'youtube', 'linkedin', 'phone');
          $iconSize = 24; // Adjust the image size as needed

          for ($i = 0; $i < count($socials); $i++) {
            $url = '';
            $s = $socials[$i];
            $url = dess_setting('dess_' . $s);

            // Check if the URL is not empty
            if ($url != '') {
              $symbolId = 'icon-' . $s;
              $iconHtml = '<svg class="icon icon-' . $s . '"><use xlink:href="#' . $symbolId . '"></use></svg>';
              echo '<li><a target="_blank" href="' . $url . '">' . $iconHtml . '</a></li>';
            }
          }
          ?>
        </ul>
      </div><!-- /foot-socials -->
    </div><!-- /bottom-foot -->
  </div><!-- /section -->
</footer><!-- /End Footer-Wrapper-->


<?php if (is_home()) : ?>
  <!-- ホームページ用の div タグ -->
  </div><!-- End/Wrapper -->
<?php endif; ?>

<?php if (is_singular('land') || is_category('land') || is_page('land')) : ?>

  </div><!-- End/Wrapper Tekken-->
<?php endif; ?>

<?php if (is_singular('naigai-construction') || is_category('naigai-construction') || is_page('genshin-impact')) : ?>
  </div><!-- End/Wrapper Genshin-->
<?php endif; ?>


<?php
// ページのテンプレートが "予約フォームページ" かどうかを確認
if (!is_page_template('page-store-reservation.php')) :
  // store-reservation-modalテンプレートを表示
  if (
    !is_page('iezukuri')
    && !is_singular('iez_plan')
    && !(function_exists('naigai_iezukuri_is_target_request') && naigai_iezukuri_is_target_request())
) {
    get_template_part('templates/store-reservation-modal');
}
endif;
?>

<?php wp_footer(); ?>

<!-- SVG FILE https://icomoon.io/ file -->
<?php include_once get_template_directory() . '/images/symbol-defs.svg'; ?>
<!-- SVG FILE End -->

</body>

</html>