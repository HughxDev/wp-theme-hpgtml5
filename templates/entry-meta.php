<?php
  $modifiedDate = get_the_modified_date();
  $date = get_the_date();
  $modifiedDateDifferent = ( $date !== $modifiedDate )
?>
<p><time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo $date; ?></time>
<?php if ( !$modifiedDateDifferent ): ?><span class="sr-only"><?php endif; ?>
(Updated <time class="updated" datetime="<?php echo get_the_modified_time('c'); ?>"><?php echo $modifiedDate; ?></time>)
<?php if ( !$modifiedDateDifferent ): ?></span><?php endif; ?>
<p class="byline author vcard"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p>
<p class="categories">Filed under: <?php echo get_the_category_list( ', ' ); ?></p>
<?php if ( has_post_thumbnail() ): ?>
<figure class="header-img<?php echo $featuredImgClasses; ?>" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
  <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); ?>"<?php echo $dimensions; ?> alt=" " itemprop="contentUrl" />
  <?php $thumbnail_caption = get_post(get_post_thumbnail_id())->post_excerpt;
  if ( !empty($thumbnail_caption) ): ?>
  <figcaption><small><?php echo
  preg_replace(
    array(
      "/(©) ([a-zA-Z_\-\s]+)( \/ )(Dollar Photo Club)/",
    ),
    array(
      '$1 <span itemprop="creator">$2</span>$3<a title="Usage terms" href="http://www.dollarphotoclub.com/Info/RoyaltyFreeLicense" itemprop="usageTerms"><span itemprop="copyrightHolder">$4</span></a>',
    ),
    $thumbnail_caption
  ); ?></small></figcaption>
  <?php endif; ?>
</figure>
<?php endif; ?>