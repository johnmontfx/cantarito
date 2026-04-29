<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * Template Name: fxg home
 * Description: home page template
 */

$page = get_query_var('page');

if( $page ){
  $page = get_query_var('page');
}else {
  $page = 1;
}
$paged = $page;

 $args = [
      'post_type' => ['fxfeatured', 'quicktakes','fxpodcasts','thevfxshow'],
      'orderby' => 'date',
      'paged' => $paged

  ];

 $context          = Timber::context();
 $templates        = array( 'index.twig' );

 $context['posts'] = Timber::get_posts( $args );
 $context['page'] = $paged;

 Timber::render( $templates, $context );
