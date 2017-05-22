<form role="search" id="searchform" class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo __( 'Search for:', 'read' ); ?></span>

		<input type="search" id="s" name="s" class="search-field" required="required" placeholder="<?php echo __( 'Введите параметры поиска...', 'read' ); ?>">
	</label>

	<input type="submit" id="searchsubmit" class="search-submit" value="<?php echo __( 'Поиск', 'read' ); ?>">
</form>
