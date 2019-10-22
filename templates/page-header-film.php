<?php
  global $post;
  global $showTitle;
  $releaseStatus = wp_get_post_terms( $post->ID, 'release_status' )[0];
  $releaseName = $releaseStatus->name;
  $releaseSlug = $releaseStatus->slug;
  //var_dump($releaseStatus);
  if ( $releaseStatus && $showTitle ):
?><h2 class="h"><?php echo $releaseName; ?></h2><?php endif; ?>