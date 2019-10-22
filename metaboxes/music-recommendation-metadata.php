<div class="music-metadata_control">
	<dl>
		<?php $mb->the_field('track-name'); ?>
		<dt><label for="track-name">Track Name</label></dt>
		<dd><input id="track-name" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" type="text" /></dd>

		<?php $mb->the_field('artist-name'); ?>
		<dt><label for="artist-name">Artist Name</label></dt>
		<dd><input id="artist-name" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" type="text" /></dd>

		<?php $mb->the_field('album-name'); ?>
		<dt><label for="album-name">Album Name</label></dt>
		<dd><input id="album-name" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" type="text" /></dd>		

		<?php $mb->the_field('release-year'); ?>
		<dt><label for="release-year">Release Year</label></dt>
		<dd><input id="release-year" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" type="number" /></dd>

		<?php $mb->the_field('youtube-url'); ?>
		<dt><label for="youtube-url">YouTube URL</label></dt>
		<dd><input id="youtube-url" name="<?php $mb->the_name();?>" value="<?php $mb->the_value(); ?>" type="url" /></dd>

		<?php $mb->the_field('spotify-url'); ?>
		<dt><label for="spotify-url">Spotify URL</label></dt>
		<dd><input name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" type="url" /></dd>

		<?php $mb->the_field('soundcloud-url'); ?>
		<dt><label for="soundcloud-url">Soundcloud URL</label></dt>
		<dd><input id="soundcloud-url" name="<?php $mb->the_name();?>" value="<?php $mb->the_value(); ?>" type="url" /></dd>

		<?php $mb->the_field('itunes-url'); ?>
		<dt><label for="itunes-url">iTunes URL</label></dt>
		<dd><input id="itunes-url" name="<?php $mb->the_name();?>" value="<?php $mb->the_value(); ?>" type="url" /></dd>

		<?php $mb->the_field('direct-purchase-url'); ?>
		<dt><label for="direct-purchase-url">Direct Purchase URL</label></dt>
		<dd><input id="direct-purchase-url" name="<?php $mb->the_name();?>" value="<?php $mb->the_value(); ?>" type="url" /></dd>
	</dl>
</div>