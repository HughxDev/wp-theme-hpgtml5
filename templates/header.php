<header class="banner container" role="banner">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="h"><a class="brand" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
      <p class="tagline"><?php bloginfo('description'); ?></p>
      <nav class="nav-main" role="navigation">
        <?php
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(
              array(
                'theme_location' => 'primary_navigation',
                'menu_class' => 'list-inline',
              )
            );
          endif;
        ?>
      </nav>
    </div><!--/.col-->
  </div><!--/.row-->
</header>
