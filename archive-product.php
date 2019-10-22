<?php
global $post;
$showTitle = true;

function get_product_template_part_as_archive( $slug, $name ) {
  global $products;
  global $post;

  // $isArchive = is_archive();

  // if ( !$isArchive || !isset( $products ) || !$products->have_posts() ) {
  //   $products = $post;
  // }

  while ( $products->have_posts() ) : $products->the_post();
    get_template_part( $slug, $name );
  endwhile;
}

$products = $wp_query;

//echo 'second pass';
get_template_part('templates/page', 'header');
get_product_template_part_as_archive( 'templates/content', 'product' );
?>