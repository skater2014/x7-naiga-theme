 		</div><!-- page-wrap -->
 		<footer class="bg-light text-center text-lg-start">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase">						
        	<?php dynamic_sidebar('footer-1'); ?>
		</h5>
      </div>
      <!--/Grid column-->

      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase mb-0">
        	<?php dynamic_sidebar('footer-2'); ?>
		</h5>
      </div>
      <!--/Grid column-->

      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase">
        	<?php dynamic_sidebar('footer-3'); ?>
		</h5>
	  </div>
	  <!--/Grid column-->
	</div>
	<!--/Grid row-->
 </div>
 <!-- /Grid container -->

  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(248, 187, 208);">
    © 2020 Copyright:
    <a class="text-dark"href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php bloginfo( 'name' ); ?></span></a>

  </div>
  <!-- /Copyright -->


</footer>
		<?php wp_footer(); ?>
	</body>
</html>