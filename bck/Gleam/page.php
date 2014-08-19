<?php get_header(); ?>

<?php get_template_part('loop','page'); ?>
<?php if ( 'on' == get_option('gleam_show_pagescomments') ) comments_template('', true); ?>

<?php get_footer(); ?>