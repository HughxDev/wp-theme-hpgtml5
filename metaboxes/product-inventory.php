<div class="product_control">
	<?php
		$selected = ' selected="selected"';
		$checked = ' checked="checked"';
	?>
	<dl>
		<?php $mb->the_field( 'sku' ); ?>
		<dt><label for="sku">SKU</label></dt>
		<dd><input id="sku" name="<?php $mb->the_name(); ?>" type="text" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'currency' ); ?>
		<dt><label for="currency">Currency</label></dt>
		<dd>
			<select id="currency">
				<option value="USD"<?php if ( $mb->get_the_value() == 'USD' ) echo $selected; ?>>United States Dollars</option>
				<option value="CAD"<?php if ( $mb->get_the_value() == 'CAD' ) echo $selected; ?>>Canadian Dollars</option>
				<option value="GBP"<?php if ( $mb->get_the_value() == 'GBP' ) echo $selected; ?>>Great British Pounds</option>
				<option value="JPY"<?php if ( $mb->get_the_value() == 'JPY' ) echo $selected; ?>>Japanese Yen</option>
			</select>
		</dd>

		<?php $mb->the_field( 'price' ); ?>
		<dt><label for="price">Price</label></dt>
		<dd><input id="price" type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" min="0" /></dd>

		<?php $mb->the_field( 'on_sale' ); ?>
		<dt><label for="on-sale"><input id="on-sale" name="<?php $mb->the_name(); ?>" type="checkbox" value="true"<?php if ( $mb->get_the_value() == 'true' ) echo $checked; ?> /> On Sale</label></dt>
		<dd>
			<?php $mb->the_field( 'sale_price' ); ?>
			<dl>
				<dt><label for="sale-price">Sale Price</label></dt>
				<dd><input id="sale-price" type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" min="0" /></dd>
			</dl>
		</dd>

		<?php $mb->the_field( 'stock_level' ); ?>
		<dt><label for="stock-level">Stock Level</label></dt>
		<dd><input id="stock-level" type="number" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'show_stock_level' ); ?>
		<dd><label for="show-stock-level"><input id="show-stock-level" name="<?php $mb->the_name(); ?>" type="checkbox" value="true"<?php if ( $mb->get_the_value() == 'true' ) echo $checked; ?> /> Show Stock Level</label></dd>

		<?php
			$mb->the_field( 'low_stock_threshold' );

			$lowStockThreshold = $mb->get_the_value();

			if ( !isset( $lowStockThreshold ) ) {
				$lowStockThreshold = 5;
			}
		?>
		<dt><label for="low-stock-threshold">Low Stock Threshold</label></dt>
		<dd><input id="low-stock-threshold" type="number" name="<?php $mb->the_name(); ?>" value="<?php echo $lowStockThreshold; ?>" min="1" /></dd>

		<?php $mb->the_field( 'out_of_stock_threshold' ); ?>
		<dt><label for="out-of-stock-threshold">Out of Stock Threshold</label></dt>
		<?php
			$outOfStockThreshold = $mb->get_the_value();

			if ( !is_numeric( $outOfStockThreshold ) ) {
				$outOfStockThreshold = 0;
			}
		?>
		<dd><input id="out-of-stock-threshold" type="number" name="<?php $mb->the_name(); ?>" value="<?php echo $outOfStockThreshold; ?>" min="0" /></dd>

		<?php $mb->the_field( 'stock_status' ); ?>
		<dt><label for="stock_status">Stock Status</label></dt>
		<dd>
			<?php /*
				0 (Unlimited)
				1 (In Stock),
				2 (Low Stock),
				3 (Out Of Stock),
				4 (More Stock Ordered),
				5 (Discontinued)
			*/ ?>
			<select id="stock_status" name="<?php $mb->the_name(); ?>">
				<option value=""<?php if ( $mb->get_the_value() == '' ) echo $selected; ?>>Automatic</option>
				<option value="0"<?php if ( $mb->get_the_value() == '0' ) echo $selected; ?>>Unlimited</option>
				<option value="1"<?php if ( $mb->get_the_value() == '1') echo $selected; ?>>In Stock</option>
				<option value="2"<?php if ( $mb->get_the_value() == '2' ) echo $selected; ?>>Low Stock</option>
				<option value="3"<?php if ( $mb->get_the_value() == '3') echo $selected; ?>>Out of Stock</option>
				<option value="4"<?php if ( $mb->get_the_value() == '4' ) echo $selected; ?>>More Stock Ordered</option>
				<option value="5"<?php if ( $mb->get_the_value() == '5' ) echo $selected; ?>>Discontinued</option>
			</select>
		</dd>

		<?php $mb->the_field( 'thumbnail_url '); ?>
		<dt><label for="thumbnail_url">Thumbnail URL (If omitted Featured Image will be used.)</label></dt>
		<dd><input id="thumbnail_url" type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></dd>
	</dl>
</div>