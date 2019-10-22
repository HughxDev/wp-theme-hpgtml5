<?php
// WPAlchemy MetaBox
global $film_metadata;
global $film_fests;

$film_metadata->the_meta();
$film_fests->the_meta();

$film = get_post();
$slug = $film->post_name;
$bookmark = /*'#' .*/ $slug;

$releaseDate = $film_metadata->get_the_value('release_date');
$releaseYear = array_shift(explode('-', $releaseDate));
$hasReleaseDate = !empty( $releaseDate );

$contentUrl = htmlentities($film_metadata->get_the_value('content_url'));

$thumbnailUrl = $film_metadata->get_the_value('thumbnail_url'); // or featured image

$embedUrl = $film_metadata->get_the_value('embed_url');

$resolution = $film_metadata->get_the_value('resolution');
$aspectRatio = $film_metadata->get_the_value('aspect_ratio');

if ( !empty( $resolution ) ) {
  $resParts = explode('×', $resolution); // also split on: x
  if ( empty( $resParts ) ) {
    $resParts = explode('x', $resolution);
  }

  $hRes = $resParts[0];
  $vRes = $resParts[1];

  if ( empty( $aspectRatio ) ) {
    $displayRatio = $hRes / $vRes;
    
    switch ( $displayRatio ) {
      case ( 16/9 ):
        $aspectRatio = '16:9';
      break;
      
      case ( 4/3 ):
        $aspectRatio = '4:3';
      break;

      case ( 1.85 ):
        $aspectRatio = '1.85:1';
      break;

      case ( 2.35 ):
        $aspectRatio = '2.35:1';
      break;

      case ( 2.39 ):
        $aspectRatio = '2.39:1';
      break;

      case ( 2.4 ):
        $aspectRatio = '2.40:1';
      break;

      default:
        # code...
      break;
    }
  }
}

$aspectRatioClass = 'ratio-' . str_replace(array(':', '.'), array('-', '_'), $aspectRatio);

//$genre = $film_metadata->get_the_value('genre'); // m
$hasGenre = !empty( $film_metadata->meta['genre']);

$runtime = $film_metadata->get_the_value('runtime');
$runtimeParts = explode(':', $runtime);

$runtimeHrs = $runtimeParts[0];
$runtimeHrsPlain = intval($runtimeHrs);
$runtimeHasHrs = $runtimeHrsPlain > 0;

$runtimeMins = $runtimeParts[1];
$runtimeMinsPlain = intval($runtimeMins);
$runtimeHasMins = $runtimeMinsPlain > 0;

$runtimeSecs = $runtimeParts[2];
$runtimeSecsPlain = intval($runtimeSecs);
$runtimeHasSecs = $runtimeSecsPlain > 0;

$runtimeDuration =
  'PT' .
  ( $runtimeHasHrs ? $runtimeHrsPlain . 'H' : '' ) .
  ( $runtimeHasMins ? $runtimeMinsPlain . 'M' : '' ) .
  $runtimeSecs . 'S';

$runtimeTitle =
  ( $runtimeHasHrs ? $runtimeHrsPlain . ' hours, ' : '' ) .
  ( $runtimeHasMins ? $runtimeMinsPlain . ' minutes, ' : '' ) .
  $runtimeSecs . ' seconds';

$director = $film_metadata->get_the_value('director'); // or post author
$hasDirector = !empty($director);

$logline = $film_metadata->get_the_value('logline');

if ( $films->current_post == 0 && !is_paged() ) {
  get_template_part( 'templates/page', 'header-film' );
}
?>
<article id="<?php echo $slug; ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/VideoObject">
  <h3 class="h entry-title"><a href="<?php echo $bookmark; ?>" rel="bookmark"><span itemprop="name"><?php the_title(); ?></span><?php if ( $hasReleaseDate ): ?> (<time class="release updated" itemprop="datePublished" datetime="<?php echo $releaseDate; ?>"><?php echo $releaseYear; ?></time>)<?php endif; ?></a></h3>
  <meta itemprop="contentUrl" content="<?php echo $contentUrl; ?>" />
  <meta itemprop="thumbnailUrl" content="<?php echo $thumbnailUrl; ?>" />
  <div class="media-responsive <?php echo $aspectRatioClass; ?>">
    <iframe class="entry-content" itemprop="embedUrl" src="<?php echo $embedUrl; ?>" width="<?php echo $hRes; ?>" height="<?php echo $vRes; ?>" allowfullscreen="allowfullscreen"></iframe><!--webkitallowfullscreen="webkitallowfullscreen" mozallowfullscreen="mozallowfullscreen"-->
  </div>
  <?php if ( $hasGenre || $runtimeHasSecs || $hasDirector ): ?>
  <p>
    <?php while ( $film_metadata->have_fields( 'genre' ) ): ?><span itemprop="genre"><?php $film_metadata->the_value(); ?></span><?php if ( !$film_metadata->is_last() ): ?>/<?php else: ?>, <?php endif; ?><?php endwhile; ?>
    <?php if ( $runtimeHasSecs ): ?>
    <time title="<?php echo $runtimeTitle; ?>" aria-label="<?php echo $runtimeTitle; ?>" itemprop="duration" datetime="<?php echo $runtimeDuration; ?>"><?php echo $runtime; ?></time>.
    <?php endif; ?>
    <?php if ( $hasDirector ): ?>
    <abbr title="Director" aria-label="Director">Dir.</abbr> <span class="author vcard" itemprop="author"><span class="fn"><?php echo $director; ?></span></span>
    <?php endif; ?>
  </p>
  <?php endif; ?>
  <div class="entry-summary">
    <p itemprop="description"><?php echo $logline; ?></p>
    <?php if ( !empty( $film_fests->meta ) ): ?>
    <ul>
    <?php
      while ( $film_fests->have_fields( 'festival' ) ):
        $festAward = $film_fests->get_the_value('award');
        $festSite = $film_fests->get_the_value('site');
        $festName = $film_fests->get_the_value('name');
        $screeningDate = $film_fests->get_the_value('screening_date');
        $screeningYear = array_shift(explode('-', $screeningDate));
        $screeningLocation = $film_fests->get_the_value('screening_location');
    ?>
      <li><?php echo $festAward; ?>, <a href="<?php echo $festSite; ?>"><?php echo $festName; ?> <time datetime="<?php echo $screeningDate; ?>"><?php echo $screeningYear; ?></time></a> - <?php echo $screeningLocation; ?></li>    
    <?php endwhile; ?>
    </ul>
    <?php endif; ?>
  </div>
</article>