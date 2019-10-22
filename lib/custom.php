<?php
/**
 * Custom functions
 */
function remove_category_title($category_link){
   return preg_replace('/ title="[^"]+"/', '', $category_link);
}
// $cat_args = array('orderby' => 'name');
// $cat_args['title_li'] = '';
// $cat_args['exclude_tree'] = my_cat();

// wp_list_categories(apply_filters('widget_categories_args', $cat_args));

// remove_filter('category_description','my_cat_title');

// http://wordpress.org/support/topic/why-does-blog-become-current_page_parent-with-custom-post-type#post-2207357
// As of WP 3.1.1 addition of classes for css styling to parents of custom post types doesn't exist.
// We want the correct classes added to the correct custom post type parent in the wp-nav-menu for css styling and highlighting, so we're modifying each individually...
// The id of each link is required for each one you want to modify
// Place this in your WordPress functions.php file

function remove_parent_classes($class)
{
  // check for current page classes, return false if they exist.
  return ($class == 'active' || $class == 'current_page_item' || $class == 'current_page_parent' || $class == 'current_page_ancestor'  || $class == 'current-menu-item') ? FALSE : TRUE;
}

function add_class_to_wp_nav_menu($classes)
{
     switch (get_post_type())
     {
      case 'film':
        // we're viewing a custom post type, so remove the 'current_page_xxx and current-menu-item' from all menu items.
        $classes = array_filter($classes, "remove_parent_classes");

        // add the current page class to a specific menu item (replace ###).
        if (in_array('menu-films', $classes))
        {
           $classes[] = 'active';
         }
        break;

      // add more cases if necessary and/or a default
     }
  return $classes;
}

function property() {
  return 'property';
}

function wpseo_twitter_domain_bugfix() {
  //return false;
  return str_replace('http://', '', get_bloginfo('url'));
}

// https://gist.github.com/DrewAPicture/2243601
function dap_responsive_img_caption_filter( $val, $attr, $content = null ) {
  extract( shortcode_atts( array(
    'id' => '',
    'align' => '',
    'width' => '',
    'caption' => ''
    ), $attr
  ) );
  
  if ( 1 > (int) $width || empty( $caption ) )
    return $val;

  $new_caption = sprintf(
    '<figure id="%1$s" class="wp-caption %2$s">%4$s<figcaption class="wp-caption-text">%5$s</figcaption></figure>',
    esc_attr( $id ),
    esc_attr( $align ),
    ( 10 + (int) $width ),
    do_shortcode( $content ),
    $caption
  );

  return $new_caption;
}

// Modified from: https://wordpress.org/support/topic/blog-tab-gets-highlighted-in-nav-menu-for-custom-post-types#post-2711621
function custom_fix_blog_tab_on_cpt($classes,$item,$args) {
  if ( !is_home() && !is_singular('post') && !is_category() && !is_tag() ) {
    $blog_page_id = intval( get_option('page_for_posts') );

    if ( $item->object_id == $blog_page_id ) {
      unset( $classes[array_search('active', $classes)] );
    }
  }
  return $classes;
}

function fix_modern_tribe_permalinks( $url ) {
  return str_replace('event/events/', 'events/', $url );
}

function honor_ssl_for_attachments( $url ) {
  $http = site_url( FALSE, 'http' );
  $https = site_url( FALSE, 'https' );
  return ( $_SERVER['HTTPS'] == 'on' ) ? str_replace( $http, $https, $url ) : $url;
}

function custom_upload_mimes ( $existing_mimes=array() ) {
 // add your extension to the array
 $existing_mimes['svg'] = 'image/svg+xml';
 return $existing_mimes;
}

// http://wordpress.stackexchange.com/a/198558/37816
/**
 * Pass in a taxonomy value that is supported by WP's `get_taxonomy`
 * and you will get back the url to the archive view.
 * @param $taxonomy string|int
 * @return string
 */
function get_taxonomy_archive_link( $taxonomy ) {
  $tax = get_taxonomy( $taxonomy ) ;
  return get_bloginfo( 'url' ) . '/' . $tax->rewrite['slug'];
}

// function woocommerce_support() {
//     add_theme_support( 'woocommerce' );
// }

// remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
// remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
// add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

// function my_theme_wrapper_start() {
//   echo '<section id="woocommerce">';
// }

// function my_theme_wrapper_end() {
//   echo '</section><!--/#woocommerce-->';
// }

// add_action( 'after_setup_theme', 'woocommerce_support' );

add_filter( 'upload_mimes', 'custom_upload_mimes' );
add_filter( 'wp_get_attachment_url', 'honor_ssl_for_attachments' );
add_filter( 'wpseo_canonical', 'fix_modern_tribe_permalinks' );
add_filter( 'tribe_events_getLink', 'fix_modern_tribe_permalinks' ); // $eventUrl, $type, $secondary, $term
// tribe_events_rewrite_rules
//tribe_get_listview_link

add_filter( 'img_caption_shortcode', 'dap_responsive_img_caption_filter', 10, 3 );

add_filter( 'nav_menu_css_class', 'add_class_to_wp_nav_menu' );
add_filter( 'nav_menu_css_class','custom_fix_blog_tab_on_cpt', 10, 3 );

add_filter( 'wpseo_stopwords', '__return_empty_array' );
add_filter( 'the_category', 'remove_category_title' );
add_filter( 'wpseo_twitter_metatag_key', 'property' );
add_filter( 'wpseo_twitter_domain', 'wpseo_twitter_domain_bugfix' );
//picturefill_wp_add_image_size('small', 480, 480, false, 'medium');
//$image_size_array = array('small', 'thumbnail', 'medium', 'large', 'full');
/* All image sizes included in the $image_size_array
   must allready exist, either by default (thumbnail,
   medium, and large) or by the add_image_size function.
   Image sizes should be listed from smallest to largest
   and should not include '@2x' sizes, these will be
   added automatically. */
//picturefill_wp_set_responsive_image_sizes($image_size_array);
//picturefill_wp_remove_image_from_responsive_list('thumbnail');