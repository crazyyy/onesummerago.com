<?php
	get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-medium">
				<article class="hentry page page-404">
					<header class="entry-header">
						<h1 class="entry-title"><?php echo __( 'PAGE NOT FOUND', 'read' ); ?></h1>
					</header>
					
					<div class="entry-content">
						<div class="http-alert">
							<h1><i class="pw-icon-cancel-circled-outline"></i></h1>
						</div>
						
						
						<h3 class="section-title center"><?php echo __( 'YOU CAN SEARCH FOR IT', 'read' ); ?></h3>
						
						<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<input type="text" id="search-big" name="s" required="required" placeholder="<?php echo __( 'enter keyword', 'read' ); ?>">
						</form>
					</div>
				</article>
			 </div>
		</div>
	</div>
</div>


<?php
	get_footer();
?>