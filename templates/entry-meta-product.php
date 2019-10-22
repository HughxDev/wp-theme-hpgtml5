<?php
  $modifiedDate = get_the_modified_date();
  $date = get_the_date();
  $modifiedDateDifferent = ( $date !== $modifiedDate )
?>
<?php if ( has_post_thumbnail() ): ?>
<figure class="col-sm-6" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
  <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); ?>"<?php echo $dimensions; ?> alt=" " itemprop="contentUrl" />
  <?php $thumbnail_caption = get_post(get_post_thumbnail_id())->post_excerpt;
  if ( !empty($thumbnail_caption) ): ?>
  <figcaption><small><?php echo $thumbnail_caption; ?></small></figcaption>
  <?php endif; ?>
</figure>
<?php endif; ?>
<div class="col-sm-6">
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <!--price is 1000, a number, with locale-specific thousands separator
    and decimal mark, and the $ character is marked up with the
    machine-readable code "USD" -->
    <span itemprop="priceCurrency" content="USD">$</span><span
          itemprop="price" content="1000.00">1,000.00</span>
    <link itemprop="availability" href="http://schema.org/InStock" />In stock
  </div>
  <p class="categories">Filed under: <?php echo get_the_category_list( ', ' ); ?></p>
</div><!--/.col-sm-6-->
