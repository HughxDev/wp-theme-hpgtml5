<div class="product_control">
	<?php
		$selected = ' selected="selected"';
		$checked = ' checked="checked"';
	?>
	<dl>
		<?php $mb->the_field( 'weight' ); ?>
		<dt><label for="weight">Weight</label></dt>
		<dd><input id="weight" name="<?php $mb->the_name(); ?>" type="number" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'width' ); ?>
		<dt><label for="width">Width</label></dt>
		<dd><input id="width" name="<?php $mb->the_name(); ?>" type="number" value="<?php $mb->the_value(); ?>" /></dd>
	
		<?php $mb->the_field( 'height' ); ?>
		<dt><label for="height">Height</label></dt>
		<dd><input id="height" name="<?php $mb->the_name(); ?>" type="number" value="<?php $mb->the_value(); ?>" /></dd>
	
		<?php $mb->the_field( 'depth' ); ?>
		<dt><label for="depth">Depth</label></dt>
		<dd><input id="depth" name="<?php $mb->the_name(); ?>" type="number" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'requires_shipping' ); ?>
		<dt><label for="requires-shipping"><input id="requires-shipping" name="<?php $mb->the_name(); ?>" type="checkbox" value="yes"<?php if ( $mb->get_the_value() == 'yes' ) echo $checked; ?> /> Requires Shipping</label></dt>
	</dl>
</div>