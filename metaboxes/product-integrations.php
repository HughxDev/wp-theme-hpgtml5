<?php
global $printful;
$selected = ' selected="selected"';
$checked = ' checked="checked"';

// http://stackoverflow.com/a/9082318/214325
function sortClothingSizes( $a, $b ) {
	$sizes = array(
		'2XS' => 0,
		'XXS' => 0,
		'XS' => 1,
		'S' => 2,
		'M' => 3,
		'L' => 4,
		'XL' => 5,
		'2XL' => 6,
		'XXL' => 6,
		'3XL' => 7,
		'XXXL' => 7
	);

	$asize = $sizes[$a];
	$bsize = $sizes[$b];

	if ( $asize == $bsize ) {
		return 0;
	}

	return ( $asize > $bsize ) ? 1 : -1;
}

// http://php.net/manual/en/function.array-multisort.php#100534
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function endsIn( $string, $letter ) {
	return strrpos( $string, $letter ) === ( strlen( $string ) - 1 );
}

function formatPrintfulProductTypeText( $productType ) {
	$productTypeName = str_replace( '-', ' ', $productType );
	$productTypeName = strtolower( $productTypeName );
	$productTypeName = ucwords( $productTypeName );
	$productTypeName = str_replace( 'T Shirt', 'T-shirt', $productTypeName );

	$endsInS = endsIn( $productTypeName, 's' );

	$endsInY = endsIn( $productTypeName, 'y' );

	if ( $endsInS ) {
		return $productTypeName . 'es';
	} else if ( $endsInY ) {
		// return str_replace( 'y', 'ies', $productTypeName );
		return $productTypeName;
	}

	return $productTypeName . 's';
}

function buildOptionsListFromPrintfulProductTypes( $productTypesByKey, $currentValue ) {
	// idk why global doesn't work here
	$selected = ' selected="selected"';
	$checked = ' checked="checked"';
	$options = '<option value="">---</option>';

	foreach ( $productTypesByKey as $productTypeKey => $products ) {
		$options .= '<optgroup label="' . formatPrintfulProductTypeText( $products[0]['type'] ) . '">';

		foreach ( $products as $productKey => $product ) {
		// var_dump( $product['id'] );
		// var_dump( $currentValue == $product['id'] );

			$options .= '<option value="' . $product['id'] . '"' . ( ( $currentValue == $product['id'] ) ? $selected : '' ) . '>' . $product['brand'] . ' ' . $product['model'] . '</option>';
		}

		$options .= '</optgroup>';
	}

	return $options;
}

function getPrintfulVariantDimensionList( $variants, $dimension ) {
	$variantDimensionList = array();

	foreach ( $variants as $variantIndex => $variant ) {
		if ( !in_array( $variant[$dimension], $variantDimensionList ) ) {
			$variantDimensionList[] = $variant[$dimension];
		}
	}

	// natsort( $variantDimensionList );

	return $variantDimensionList;
}

function getPrintfulVariantColors( $variants ) {
	// return getPrintfulVariantDimensionList( $variants, 'color' );
	$variantColors = array();

	foreach ( $variants as $variantIndex => $variant ) {
		if ( !array_key_exists( $variant['color'], $variantColors ) ) {
			$variantColors[$variant['color']] = array( 'color_code' => $variant['color_code'], 'image' => $variant['image'] );
		}
	}

	return $variantColors;
}

function getPrintfulVariantSizes( $variants ) {
	$variantSizes = getPrintfulVariantDimensionList( $variants, 'size' );

	usort( $variantSizes, 'sortClothingSizes' );

	return $variantSizes;
}

$printfulProductTypes = $printful->get( 'products' );
$productTypesByKey = array();

foreach ( $printfulProductTypes as $productTypeIndex => $productType ) {
	$productTypeLowercase = strtolower( $productType['type'] );

	// if ( isset( $productType['brand'] ) ) {
	// 	$productBrandLowercase = strtolower( $productType['brand'] );
	// } else {
	// 	$productBrandLowercase = 'null';
	// }

	// type = T-SHIRT, POSTER, etc.
	if ( !array_key_exists( $productTypeLowercase, $productTypesByKey ) ) {
		$productTypesByKey[$productTypeLowercase] = array();
	}

	$productTypesByKey[$productTypeLowercase][] = $productType;
	
	// if ( !array_key_exists( $productTypesByKey[$productTypeLowercase], $productBrandLowercase ) ) {
	// 	$productTypesByKey[$productTypeLowercase][$productBrandLowercase] = array();
	// }

	// $productTypesByKey[$productTypeLowercase][$productBrandLowercase][] = $productType;
}

foreach ( $productTypesByKey as $productTypeKey => $productType ) {
	$productTypesByKey[$productTypeKey] = array_orderby( $productTypesByKey[$productTypeKey], 'brand', SORT_ASC, 'model', SORT_ASC );
}

?>
<div class="product_control">
	<dl>
		<?php $mb->the_field( 'printful_file_id_front' ); ?>
		<dt><label for="printful-file-id-front">Printful File ID (Front)</label></dt>
		<dd><input id="printful-file-id-front" name="<?php $mb->the_name(); ?>" type="text" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'printful_file_id_back' ); ?>
		<dt><label for="printful-file-id-back">Printful File ID (Back)</label></dt>
		<dd><input id="printful-file-id-back" name="<?php $mb->the_name(); ?>" type="text" value="<?php $mb->the_value(); ?>" /></dd>

		<?php $mb->the_field( 'moltin_product_id' ); ?>
		<dt><label for="moltin-product-id">Moltin Product ID</label></dt>
		<dd><input id="moltin-product-id" name="<?php $mb->the_name(); ?>" type="text" value="<?php $mb->the_value(); ?>" /></dd>
	
		<?php $mb->the_field( 'printful_product_id' ); ?>
		<dt><label for="printful-product-id">Printful Product Type</label></dt>
		<dd>
			<select id="printful-product-id" name="<?php $mb->the_name(); ?>">
			<?php
				$selectedPrintfulProductType = $mb->get_the_value();
				$variants = $printful->get( 'products/' . $selectedPrintfulProductType )['variants'];

				echo buildOptionsListFromPrintfulProductTypes( $productTypesByKey, $selectedPrintfulProductType );
			?>
			</select>
			<dl>
				<dt>Sizes</dt>
				<dd>
					<ul class="list-inline">
					<?php
						$variantSizes = getPrintfulVariantSizes( $variants );

						foreach ( $variantSizes as $sizeIndex => $size ) : ?>
						<?php $mb->the_field( 'printful_product_sizes', WPALCHEMY_FIELD_HINT_CHECKBOX_MULTI ); ?>
						<li><label><input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $size; ?>"<?php $mb->the_checkbox_state( $size ); ?> /> <abbr><?php echo $size; ?></abbr></label></li>
						<?php endforeach; ?>
					</ul>
				</dd> 

				<dt>Colors</dt>
				<dd>
					<ul class="list-inline">
					<?php
						$variantColors = getPrintfulVariantColors( $variants );

						foreach ( $variantColors as $colorName => $colorData ): $json = json_encode( array( 'code' => $colorData['color_code'], 'name' => $colorName ) ) ?>
						<?php $mb->the_field( 'printful_product_colors', WPALCHEMY_FIELD_HINT_CHECKBOX_MULTI ); ?>
						<li><label><input type="checkbox" name="<?php $mb->the_name(); ?>" value='<?php echo $json; ?>'<?php $mb->the_checkbox_state( $json ); ?> /> <span class="color-sample" style="background-color:<?php echo $colorData['color_code']; ?>"></span><span><?php echo $colorName; ?></span></label></li>
						<?php endforeach; ?>
					</ul>
				</dd>
			</dl>
		</dd>

		<?php // $mb->the_field( 'import' ); ?>
	</dl>
</div>