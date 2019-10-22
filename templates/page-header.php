<?php
$headerClass = '';

if ( is_front_page() || is_home() ) {
  $headerClass .= ' sr-only';
} else {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  if ( is_plugin_active('advanced-custom-fields/acf.php') ) {
    $headerClass .= ( get_field( 'show_title' ) ? '' : ' sr-only' ); // implied
  }
  //var_dump(get_option( 'active_plugins', array() ));
}
?>
<div class="page-header<?php echo $headerClass; ?>">
  <h1 class="h"><?php echo roots_title(); ?></h1>
</div>