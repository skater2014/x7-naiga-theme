	<footer id="foot">
			<div class="main-foot">
				<div class="container">
					<div class="foot-col">
						<?php dynamic_sidebar('footer-1'); ?>
					</div>
					<div class="foot-col">
						<?php dynamic_sidebar('footer-2'); ?>
					</div>
					<div class="foot-col">
						<?php dynamic_sidebar('footer-3'); ?>
					</div>
					
				</div>
			</div>
			<div class="bottom-foot">
				<div class="container">
					<div class="copyright">
						<p class="credits"><?php echo ( dess_setting('dess_copyright') !='' ? dess_setting('dess_copyright') : __('2016 Copyright. Powered by WordPress','creator') ); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</footer>
		<?php wp_footer(); ?>
	</body>
</html>