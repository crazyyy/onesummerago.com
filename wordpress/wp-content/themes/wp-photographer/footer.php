		<!-- Root element of PhotoSwipe. Must have class pswp. -->
		<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="pswp__bg"></div>
			<div class="pswp__scroll-wrap">
				<div class="pswp__container">
					<div class="pswp__item"></div>
					<div class="pswp__item"></div>
					<div class="pswp__item"></div>
				</div>
				<div class="pswp__ui pswp__ui--hidden">
					<div class="pswp__top-bar">
						<div class="pswp__counter"></div>
						<button class="pswp__button pswp__button--close" title="<?php echo __( 'Close (Esc)', 'read' ); ?>"></button>
						<button class="pswp__button pswp__button--share" title="<?php echo __( 'Share', 'read' ); ?>"></button>
						<button class="pswp__button pswp__button--fs" title="<?php echo __( 'Toggle fullscreen', 'read' ); ?>"></button>
						<button class="pswp__button pswp__button--zoom" title="<?php echo __( 'Zoom in/out', 'read' ); ?>"></button>
						<div class="pswp__preloader">
							<div class="pswp__preloader__icn">
								<div class="pswp__preloader__cut">
									<div class="pswp__preloader__donut"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
						<div class="pswp__share-tooltip"></div>
					</div>
					<button class="pswp__button pswp__button--arrow--left" title="<?php echo __( 'Previous (arrow left)', 'read' ); ?>"></button>
					<button class="pswp__button pswp__button--arrow--right" title="<?php echo __( 'Next (arrow right)', 'read' ); ?>"></button>
					<div class="pswp__caption">
						<div class="pswp__caption__center"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- Root element of PhotoSwipe -->


		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="layout-medium">
                <div class="footer-social">
					<?php
						if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'pixelwars_footer_sidebar' ) ) :
						endif;
					?>
                </div>

				<div class="site-info">
					<p>
						<?php
							$copyright_text = stripcslashes( get_option( 'copyright_text', "" ) );

							echo $copyright_text;
						?>
					</p>
				</div>
			</div>
		</footer>
	</div>


	<?php
		wp_footer();
	?>
</body>
</html>
