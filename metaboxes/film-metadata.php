<div class="film_control">
	<datalist id="genres">
		<option>Comedy</option>
		<option>Drama</option>
		<option>Horror</option>
		<option>Thriller</option>
		<option>Romantic</option>
	</datalist>
	<datalist id="resolutions">
		<option></option>
		<option>4096×2160</option>
		<option>3840×2160</option>
		<option>2048×1080</option>
		<option>1920×1080</option>
		<option>1280×720</option>
		<option>720×480</option>
		<option>640×360</option>
	</datalist>
	<dl>
		<?php
			$mb->the_field('embed_url');
			$selected = ' selected="selected"';
		?>
		<dt><label for="embed_url">Embed URL</label></dt>
		<dd><input id="embed_url" type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('resolution'); ?>
		<dt><label for="resolution">Maximum Resolution</label></dt>
		<dd><input id="resolution" name="<?php $mb->the_name(); ?>" type="text" value="<?php $mb->the_value(); ?>" placeholder="1920×1080" list="resolutions" /></dd>

		<?php $mb->the_field('aspect_ratio'); ?>
		<dt><label for="aspect_ratio">Aspect Ratio (If omitted, will be determined from Resolution)</label></dt>
		<dd>
			<select id="aspect_ratio" name="<?php $mb->the_name(); ?>">
				<option value=""<?php if ($mb->get_the_value() == '') echo $selected; ?>>---</option>
				<option<?php if ($mb->get_the_value() == "1:1") echo $selected; ?>>1:1</option>
				<option value="4:3"<?php if ($mb->get_the_value() == "4:3") echo $selected; ?>>4:3</option>
				<option value="1.375:1"<?php if ($mb->get_the_value() == "1.375:1") echo $selected; ?>>4:3 Academy</option>
				<option<?php if ($mb->get_the_value() == "16:9") echo $selected; ?>>16:9</option>
				<option<?php if ($mb->get_the_value() == "16:10") echo $selected; ?>>16:10</option>
				<option<?php if ($mb->get_the_value() == "1.85:1") echo $selected; ?>>1.85:1</option>
				<option<?php if ($mb->get_the_value() == "2.35:1") echo $selected; ?>>2.35:1</option>
				<option<?php if ($mb->get_the_value() == "2.39:1") echo $selected; ?>>2.39:1</option>
				<option<?php if ($mb->get_the_value() == "2.40:1") echo $selected; ?>>2.40:1</option>
			</select>
		</dd>

		<?php $mb->the_field('logline'); ?>
		<dt><label for="logline">Logline</label></dt>
		<dd><input id="logline" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('director'); ?>
		<dt><label for="director">Director (If omitted Post Author will be used.)</label></dt>
		<dd><input id="director" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('runtime'); ?>
		<dt><label for="runtime">Runtime (HH:MM:SS)</label></dt>
		<dd><input id="runtime" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('release_date'); ?>
		<dt><label for="release_date">Release Date</label></dt>
		<dd><input id="release-date" type="date" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('content_url'); ?>
		<dt><label for="content_url">Content URL (Highest quality direct download link)</label></dt>
		<dd><input id="content_url" type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field('thumbnail_url'); ?>
		<dt><label for="thumbnail_url">Thumbnail URL (If omitted Featured Image will be used.)</label></dt>
		<dd><input id="content_url" type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>
		
		<dt><label for="genre">Genre(s)</label></dt>
		<dd>
		<?php while($mb->have_fields_and_multi('genre')): ?>
		<?php $mb->the_group_open(); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" list="genres" />
		<a href="#" class="dodelete button" title="Remove" aria-label="Remove">&times;</a>
		<?php $mb->the_group_close(); ?>
		<?php endwhile; ?>
		<a href="#" class="docopy-genre button" title="Add" aria-label="Add">+</a>
		</dd>
	</dl>
</div>