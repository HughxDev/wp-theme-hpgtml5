<div class="film_releasedate_control">
	
	<dl>
	<?php
		$mb->the_field( 'merchant_name' );
		$id = 'merchant_name-' . $mb->get_the_index();
	?>
		<dt><label for="<?php echo $id; ?>">Merchant Name</label></dt>
		<dd>
			<input id="<?php echo $id; ?>" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" list="merchant-list" />
			<datalist id="merchant-list">
				<option>Woodward Publishing</option>
				<option>Amazon</option>
			</datalist>
		</dd>
	
	<?php
		$mb->the_field( 'url' );
		$id = 'url-' . $mb->get_the_index();
	?>
		<dt><label for="<?php echo $id; ?>">Merchant URL</label></dt>
		<dd>
			<input id="<?php echo $id; ?>" type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" list="merchant-urls" />
			<datalist id="merchant-urls">
				<option>http://www.wardwoodpublishing.co.uk/</option>
				<option>http://www.amazon.co.uk/</option>
			</datalist>
		</dd>
	</dl>
</div>
<div class="film_releasedate_control">
	<h4>Quotes</h4>
	<!--dl>
		<dt>Name, no Publication</dt>
		<dd><samp>—John Smith</samp></dd>
		
		<dt>Publication, no Name</dt>
		<dd><samp>—<cite>Acme Monthly</cite></samp></dd>

		<dt>Name and Publication</dt>
		<dd><samp>—John Smith, <cite>Acme Monthly</cite></samp></dd>

		<dt>Name, Publication, and Format</dt>
		<dd><samp>—John Smith, Editor-in-Chief of <cite>Acme Monthly</cite></samp></dd>
	</dl-->
	<table>
		<tr>
			<th></th>
			<th>Text</th>
			<th>Attribution</th>
			<th>Actions</th>
		</tr>
	<?php while($mb->have_fields_and_multi('quotes', 3)): ?>
	<?php $mb->the_group_open('tr'); ?>
			<td><?php echo ( $mb->get_the_index() + 1 ) . '.'; ?></td>
			<td>
				<textarea name="<?php $mb->the_name('text'); ?>"><?php $mb->the_value('text'); ?></textarea>
			</td>
			<td>
				<?php
					$mb->the_field('attribution_name');
					$id = 'attribution-name-' . $mb->get_the_index();
				?>
				<dl>
					<dt><label for="<?php echo $id ?>">Name</label></dt>
					<dd><input id="<?php echo $id ?>" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="John Smith"/></dd>
					
				<?php
					$mb->the_field('attribution_publication');
					$id = 'attribution-publication-' . $mb->get_the_index();
				?>
					<dt><label for="<?php echo $id; ?>">Publication</label></dt>
					<dd><input id="<?php echo $id; ?>" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="Acme Monthly"/></dd>

				<?php
					$mb->the_field('attribution_publication_format');
					$id = 'attribution-publication-format-' . $mb->get_the_index();
				?>
					<dt><label for="<?php echo $id; ?>">Relation to Publication</label></dt>
					<dd><input id="<?php echo $id; ?>" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="editor of"/></dd>
				</dl>
			</td>
			<td>
				<?php $mb->the_field('hide'); ?>
				<p><label><input type="checkbox" name="<?php $mb->the_name(); ?>" value="1"<?php if ( $mb->get_the_value() ) echo ' checked="checked"'; ?> /> Hide</label></p>
				<p><a href="#" class="dodelete button">Remove</a></p>
			</td>
	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
	</table>
	<a href="#" class="docopy-quotes button">Add Quote</a>
</div>