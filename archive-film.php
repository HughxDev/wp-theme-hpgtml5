<?php
global $post;
global $film_metadata;
$film_metadata->the_meta();
$old_post = $post; // necessary?
$showTitle = true;

function get_film_template_part_as_archive( $slug, $name ) {
  global $films;
  global $post;

  // $isArchive = is_archive();

  // if ( !$isArchive || !isset( $films ) || !$films->have_posts() ) {
  //   $films = $post;
  // }

  while ( $films->have_posts() ) : $films->the_post();
    get_template_part( $slug, $name );
  endwhile;
}

// Upcoming Films
$upcomingFilmsQuery = array(
  'post_type' => 'film',
  'tax_query' => array(
    //'relation' => 'OR',
    array(
      'taxonomy' => 'release_status',
      'field' => 'slug',
      'terms' => array( 'upcoming' ),
      //'operator' => 'NOT IN'
    )
  ),
  // http://www.farinspace.com/forums/topic/sorting-query-based-on-meta-box-value/#post-1918
  'meta_key' => $film_metadata->get_the_name('release_date'),
  'order' => 'DESC',
  'orderby' => 'meta_value',
  'posts_per_page' => -1
);

$films = new WP_Query( $upcomingFilmsQuery );

//var_dump($films);

if ( $films->have_posts() ) {
  //echo 'first pass';
  get_film_template_part_as_archive( 'templates/content', 'film' );
} else {
  $showTitle = false;
}

wp_reset_postdata();

$films = $wp_query;

//echo 'second pass';
get_template_part('templates/page', 'header');
get_film_template_part_as_archive( 'templates/content', 'film' );
?>