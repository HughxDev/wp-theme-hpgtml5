<?php
// No Spoon Apparel FB App ID: 360331241025796

// WP Alchemy
global $product_inventory;
global $product_shipping;
global $product_integrations;
global $stripeTest;
global $stripeLive;
global $printful;
global $wpdb;
global $productize_table_name;
global $productize_dir;

$required = '<abbr title="(required)" aria-label="(required)">*</abbr>';

$product_inventory->the_meta();
$product_shipping->the_meta();
$product_integrations->the_meta();

$product = get_post();
$slug = $product->post_name;

$subtitle = trim( get_field( 'subtitle' ) );

$featuredImageUrl = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
$thumbnailUrl = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' )[0];
$featuredImageAltText = trim( strip_tags( get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) ) );

if ( trim( $featuredImageAltText ) == '' ) {
  $featuredImageAltText = ' ';
}

// var_dump( $thumbnailUrl );

// if ( $products->current_post == 0 && !is_paged() ) {
//   get_template_part( 'templates/page', 'header-product' );
// }

$currency = $product_inventory->get_the_value( 'currency' );

$price = $product_inventory->get_the_value( 'price' );

$sizes = $product_integrations->get_the_value( 'printful_product_sizes' );

$colors = $product_integrations->get_the_value( 'printful_product_colors' );

$printfulProductID = $product_integrations->get_the_value( 'printful_product_id' );

$printfulFileID_front = $product_integrations->get_the_value( 'printful_file_id_front' );
$printfulFileID_back = $product_integrations->get_the_value( 'printful_file_id_back' );

ob_start();

$size = $sizes[0];
$color = 'white'; // json_decode( $colors[0], true )['name'];

$noHeaders = true;
include( $productize_dir . '/api/printful-variant.php' );

$defaultVariant = json_decode( ob_get_clean(), true );

$defaultSize = 'S';

// var_dump( $defaultVariant );

$displayProduct = true;

/*
  @workaround
  @todo - https://trello.com/c/2CoS74Hb
  Printful apparently has a bug where Standard Flat Rate
  is converted to USPS First Class, which has a different
  price.
*/
// $defaultRates = getPrintfulShippingRates(
//   'US',
//   NULL,
//   NULL,
//   array(
//     array(
//       'variant_id' => $defaultVariant['id'],
//       'quantity' => 1
//     )
//   )
// );

if ( isset( $price ) ) {
  $price = (int) $price;
} else {
  $price = 0;
}

$requiresShipping = ( $product_shipping->get_the_value( 'requires_shipping' ) == 'yes' );

if ( $price == 0 ) {
  $priceFormatted = 'Free';
  $currency = 'XXX';
  $currencySymbol = '';
  $priceNormalized = $price;

  if ( $requiresShipping ) {
    $ctaText = 'Order';
  } else {
    $ctaText = 'Download';
  }
} else {
  switch ( $currency ) {
    case 'USD':
    default:
      setlocale( LC_MONETARY, 'en_US.UTF-8' );
      $priceFormatted = money_format( '%!.2n', $price );
      $currency = 'USD';
      $currencySymbol = '$';
      $priceNormalized = $price * 100; // (Dollars to cents)
      $ctaText = 'Purchase';
    break;
  }
}

$stockStatus = $product_inventory->get_the_value( 'stock_status' );

switch ( $stockStatus ) {
  case '0': // Unlimited
    if ( $requiresShipping ) {
      $availability = 'Made to Order';
    } else {
      $availability = 'Digital Download';
      $ctaText = 'Download';
    }
    $availabilitySchema = 'http://schema.org/InStock';
    break;

  case '1':
    $availability = 'In Stock';
    $availabilitySchema = 'http://schema.org/InStock';
    break;

  case '2':
    $availability = 'Low Stock';
    $availabilitySchema = 'http://schema.org/LimitedAvailability';
    break;

  case '4':
    $availability = 'More Stock Ordered';
    $availabilitySchema = 'http://schema.org/OutOfStock';
    // $availabilitySchema = 'http://schema.org/PreOrder';
    break;

  case '5':
    $availability = 'Discontinued';
    $availabilitySchema = 'http://schema.org/Discontinued';
    break;

  case '3':
  default:
    $availability = 'Out of Stock';
    $availabilitySchema = 'http://schema.org/OutOfStock';
    break;
}

$stockLevel = $product_inventory->get_the_value( 'stock_level' );
$showStockLevel = $product_inventory->get_the_value( 'show_stock_level' );

// $productType = array_pop( get_the_terms( $post->ID, 'product_type' ) );
// $productTypeLink = get_term_link( $productType );
// $productTypeNameSingular = substr( $productType->name, 0, -1 );

$productTerms = wp_get_post_terms( $post->ID, 'product_type', array( 'orderby' => 'term_id' ) );
$productBreadcrumbs = '';

// echo '<pre>';
// print_r( get_taxonomy( 'product_type' ) );
// echo '</pre>';

$lastProductTerm = end( $productTerms );
$upCount = $productTermCount = count( $productTerms );
$breadcrumbPosition = 1;

// http://wordpress.stackexchange.com/a/172136/37816
foreach ( $productTerms as $productTerm ) {
  $productBreadcrumbs .=
    '<span itemprop="itemListElement" itemscope="itemscope" itemtype="http://schema.org/ListItem">'
    . '<a itemprop="item" href="' . get_term_link( $productTerm ) . '" rel="' . trim( str_repeat( 'up ', $upCount ) ) . '">'
      . '<span itemprop="name">' . $productTerm->name . '</span>'
    . '</a>'
    . '<meta itemprop="position" content="' . $breadcrumbPosition . '" />'
  . '</span>'
  ;

  if ( $productTerm !== $lastProductTerm ) {
    $productBreadcrumbs .= ' <span aria-hidden="true">&gt;</span> ';
    // $productBreadcrumbs .= ' ▶ ';
  }

  --$upCount;
  ++$breadcrumbPosition;
} 

$bookmark = get_permalink();

$previouslyEnteredPostalAddress = '';

if ( isset( $_POST['stripeToken'] ) ) {
  $token  = $_POST['stripeToken'];
  $email = $_POST['stripeEmail'];
  $args = json_decode( stripslashes( $_POST['stripeArgs'] ), true );
  $shippingMethod = json_decode( stripslashes( $_POST['shippingMethod'] ), true );

  if ( isset( $_POST['postal'] ) ) {
    $previouslyEnteredPostalAddress = $_POST['postal'];
  }

  // echo '<pre>POST<br>';
  // var_export( $_POST );
  // echo '</pre><hr/>';

  // echo '<pre>Args<br>';
  // var_export( $args );
  // echo '</pre><hr/>';

  // var_dump( $customerParams );

  $existingCustomerQuery = "SELECT * FROM $productize_table_name WHERE email = '$email'";

  $existingCustomers = $wpdb->get_results( $existingCustomerQuery, OBJECT );

  if ( count( $existingCustomers ) == 0 ) {
    $customerParams = array(
      "source" => $token,
      "description" => '"The Kramper" customer'
    );

    $customer = \Stripe\Customer::create( $customerParams );

    $insert = $wpdb->insert( 
      $productize_table_name,
      array( 
        'email' => $email,
        'stripe_id' => $customer->id,
        'stripe_token' => $token
      ) 
    );
  } else {
    try {
      $customer = \Stripe\Customer::retrieve( $existingCustomers{0}->stripe_id );

      $customer->source = $token;
      $customer->save();

      $update = $wpdb->update( 
        $productize_table_name,
        array( 'stripe_token' => $token ),
        array( 
          'email' => $email,
        )
      );
    } catch ( Stripe\Error\InvalidRequest $exception ) {
      $displayFeedback = false;
      $displayProduct = true;
    }
  }

  $chargeParams = array(
    'customer' => $customer->id,
    'amount'   => $_POST['amount'],
    'currency' => strtolower( $currency ),
    'metadata' => $_POST['metadata']
  );

  // var_dump( $charge );
  try {
    $charge = \Stripe\Charge::create( $chargeParams );

    $items = array();

    $totalUnits = 0;

    foreach ( $_POST['batches'] as $batch ) {
      # $variant = getPrintfulVariantFromSpecs( $printfulProductID, $size, $color );
      
      $files = array();

      if ( !empty( $printfulFileID_front ) ) {
        $files[0] = array(
          'id' => $printfulFileID_front,
          'type' => 'default'
        );
      }

      if ( !empty( $printfulFileID_back ) ) {
        $files[1] = array(
          'id' => $printfuulFileID_back,
          'type' => 'back'
        );
      }

      // Mockup (Mirroring JavaScript regex)
      $files[2] = array(
        'url' => preg_replace(
          '/([A-Za-z0-9\-]+)(\.(?:png|jpg|jpeg|gif|svg))$/i',
          str_replace( ' ', '-', $batch['colorName'] ) . '$2',
          $featuredImageUrl
        ),
        'type' => 'preview'
      );

      $variantID = getPrintfulVariantFromSpecs( $printfulProductID, $batch['size'], $batch['color'] )['id'];

      // echo '<pre>Variant ID<br>';
      // var_export( $variantID );
      // echo '</pre><hr/>';

      $items[] = array(
        'variant_id' => $variantID,
        'quantity' => $batch['quantity'],
        'name' => get_the_title(),
        'retail_price' => $price,
        'files' => $files
      );

      $totalUnits += $batch['quantity'];
    }

    // echo '<pre>Shipping Method ID<br>';
    // var_export( $shippingMethod['id'] );
    // echo '</pre><hr/>';

    /* Start Printful */
    // Create an order
    try {
      $order = $printful->post(
        'orders',
        array(
          'shipping' => $shippingMethod['id'],
          'recipient' => array(
            'name' => $args['shipping_name'],
            'address1' => $args['shipping_address_line1'],
            'city' => $args['shipping_address_city'],
            'state_code' => $args['shipping_address_state'],
            'country_code' => $args['shipping_address_country_code'],
            'zip' => $args['shipping_address_zip']
          ),
          'items' => $items
        ),
        array( 'confirm' => 1 )
      ); // $order
    } catch ( Exception $printfulException ) {
      // @todo: Showing the customer the Printful Exception is probably not the best idea.
      // echo '<pre>Printful Exception Message<br>';
      // var_export( $printfulException->getMessage() );
      // echo '</pre><hr/>';
      $feedbackTitle = 'Sorry, looks like there was a problem with your order.';

      $feedback = $printfulException->getMessage();

      $displayProduct = true;

      $alertType = 'danger';
    }

    // Update Stock Level
    // get_post_meta( $post->ID, $key = '', $single = false )
    update_post_meta( $post->ID, '_productize_stock_level', ( $stockLevel - $totalUnits ), $stockLevel );
    
    // echo '<pre>Total Units<br>';
    // var_export( $totalUnits );
    // echo '</pre><hr/>';

    // echo '<pre>Order<br>';
    // var_export( $order );
    // echo '</pre><hr/>';
    /* End Prinful Order */

    $feedbackTitle = 'Thank you for your business!';

    $feedback = <<<EOT
<p>By buying this product, you are helping me earn a living independent of a <abbr title="Nine-to-Five">9–5</abbr>, and for that I am eternally grateful. This allows me to do what I love, which includes filmmaking, software engineering, business building, and going <abbr title="Hard As a Motherfucker">HAM</abbr> at the club. (All of which you can join me for on my <a href="/vlog/">vlog</a>.) If you have any questions about your order, please e-mail <a href="mailto:support@hughguiney.com"><code>support@hughguiney.com</code></a>.</p>
EOT;

    $displayProduct = true;

    $alertType = 'success';
  // } catch ( Stripe\Error\Card $exception ) {
  } catch ( Exception $exception ) {
    $feedbackTitle = 'Sorry, looks like there was a problem with your order.';

    $feedback = $exception->getMessage();

    $displayProduct = true;

    $alertType = 'danger';
  }

  $displayFeedback = true;

  echo '</pre>';
} else {
  $displayFeedback = false;
}

if ( $displayFeedback ) { ?>
  <div class="alert alert-<?php echo $alertType; ?>" role="alert">
    <h1 class="h h4 text-center" style="margin-top: 0;"><?php echo $feedbackTitle; ?></h1>
    <!-- <p>I know this is super weird but please <a href="mailto:hugh@hughguiney.com">e-mail me</a> with your size. This shopping cart is still in development and I haven't had time to build a size selector yet.</p> -->
    <?php echo $feedback; ?>
  </div>
<?php } ?>

<?php if ( $displayProduct ) { ?>
  <?php // while (have_posts()) : the_post(); ?>
  <article id="<?php echo $slug; ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/Product">
    <header class="entry-header hpg-product-header">
      <h1 class="h entry-title">
        <a href="<?php echo $bookmark; ?>" rel="bookmark">
          <span itemprop="name"><?php the_title(); ?></span>
        </a>
      </h1>
      <?php if ( !empty( $subtitle ) ) { ?>
      <p class="h h4 entry-subtitle"><?php echo $subtitle; ?>
      <?php }?>
      <nav aria-label="Breadcrumbs" itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
        <h2 class="sr-only">Product Type</h2>
        <p><?php echo $productBreadcrumbs; ?></p>
      </nav>
    </header>
    <div class="entry-content">
      <?php if ( has_post_thumbnail() ): ?>
      <link itemprop="thumbnailUrl" href="<?php echo $thumbnailUrl; ?>" />
      <div class="col-md-6" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
        <img id="product-image" class="hpg-product-image" src="<?php echo $featuredImageUrl ?>"<?php echo $dimensions; ?> alt="<?php echo $featuredImageAltText; ?>" itemprop="contentUrl" />
        <?php /* $thumbnail_caption = get_post(get_post_thumbnail_id())->post_excerpt;
        if ( !empty($thumbnail_caption) ): ?>
        <figcaption><?php echo $thumbnail_caption; ?></figcaption>
        <?php endif; */ ?>
      </div>
      <?php endif; ?>
      <div class="col-md-6 hpg-product-details">
        <?php the_content(); ?>
        <div itemprop="offers" itemscope="itemscope" itemtype="http://schema.org/Offer">
          <!--price is 1000, a number, with locale-specific thousands separator
          and decimal mark, and the $ character is marked up with the
          machine-readable code "USD" -->
          <dl style="margin-bottom:15px;">
            <dt class="sr-only">Price</label>
            <dd>
              <b class="hpg-product-price"><span itemprop="priceCurrency" content="<?php echo $currency; ?>"><?php echo $currencySymbol; ?></span><span itemprop="price" content="<?php echo $price; ?>"><?php echo $priceFormatted; ?></span></b>
            </dd>
            <dt class="sr-only">Availability</dt>
            <dd>
              <link itemprop="availability" href="<?php echo $availabilitySchema; ?>" /><?php echo $availability; ?>
              <?php if ( $showStockLevel == 'true' ) { ?>
              <span>– <span itemprop="inventoryLevel"><?php echo $stockLevel; ?></span> remaining</span>
              <?php } ?>
            </dd>
          </dl>
          <form
            role="form"
            id="checkout"
            class="hpg-wizard"
            action=""
            method="POST"
          >
            <input id="stripe-token" name="stripeToken" type="hidden" value="" />
            <input id="stripe-email" name="stripeEmail" type="hidden" value="" />
            <input id="stripe-args" name="stripeArgs" type="hidden" value="" />           
            <input id="amount" name="amount" type="hidden" value="<?php echo $priceNormalized; ?>" />
            <?php/*<input id="printful-product-id" name="printfulProductID" type="hidden" value="<?php echo $printfulProductID; ?>" />*/?>
            <?php/*<input id="product-name" name="productName" type="hidden" value="<?php the_title(); ?>" />*/?>
            <div id="your-order" class="repeater hpg-wizard__step hpg-wizard__step--active">
              <fieldset>
                <legend>Your Order</legend>
                <!-- <div class="table-responsive"> -->
                <!-- class="table" -->
                <table>
                  <!-- <label for="sizes">Size</label><label for="qty">Quantity</label> -->
                  <thead>
                    <tr>
                      <th scope="col">Size</th>
                      <th scope="col">Color</th>
                      <th scope="col"><abbr title="Quantity" aria-label="Quantity">Qty</abbr></th>
                      <th scope="col" style="color:transparent;">Actions</th>
                    </tr>
                  </thead>
                  <tbody data-repeater-list="batches">
                    <tr data-repeater-item="" class="form-group">
                      <td>
                        <select id="sizes" class="form-control clothing-sizes" name="size">
                        <?php foreach ( $sizes as $sizeIndex => $size ) : ?>
                          <option<?php echo ( $size == $defaultSize ) ? ' selected="selected"' : '' ?>><?php echo $size; ?></option>
                        <?php endforeach; ?>
                        </select>
                      </td>
                      <td>
                      <?php /*<!--<select id="color" class="selectpicker form-control select-color" name="color">
                        <?php foreach ( $colors as $colorIndex => $color ) : $color = json_decode( $color, true ); ?>
                          <option value="<?php echo $color['code']; ?>" style="background-color:<?php echo $color['code']; ?>"><?php echo $color['name']; ?></option>
                        <?php endforeach; ?>
                        </select>-->*/ ?>
                        <div id="color-select" class="dropdown color-select">
                          <button id="color-select-button" class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="hpg-btn-text">Select</span> 
                            <span class="hpg-btn-icon">
                              <span class="caret"></span>
                            </span>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="color-select-button">
                            <?php foreach ( $colors as $colorIndex => $color ) : $color = json_decode( $color, true ); ?>
                              <li><a class="color-select-option" data-select-color="<?php echo $color['code']; ?>"<?php if ( $color['code'] == '#ffffff' ) { echo ' data-select-default=""'; } ?> href=""><span class="color-sample" style="background-color:<?php echo $color['code']; ?>"></span><span><?php echo $color['name']; ?></span></a></option>
                            <?php endforeach; ?>
                          </ul>
                        </div><!--/.color-select-->
                        <input id="color-select" class="color-select-value" name="color" type="hidden" value="#ffffff" />
                        <input id="color-select-name" class="color-select-value-name" name="colorName" type="hidden" value="White" />
                      </td>
                      <td>
                        <input id="qty" class="form-control qty qty-sm" name="quantity" type="number" min="1" value="1" required="required" />
                      </td>
                      <td class="hpg-row-remove-row" scope="row">
                        <button data-repeater-delete="" type="button" class="btn btn-default hpg-btn-remove-row" title="Remove" aria-label="Remove">
                          <span aria-hidden="true">-</span>
                          <!-- <span class="sr-only">Remove</span> -->
                        </button>
                      </td>
                    </tr><!--/data-repeater-item-->
                  </tbody>
                </table><!--/data-repeater-list-->
                <!-- </div> -->
              </fieldset>
              <p><button data-repeater-create="" type="button" class="btn btn-default hpg-btn-add-row" title="Add More" aria-label="Add More">+</button></p>
              <p style="margin-top: .5em; margin-bottom: 0;">
                <button id="your-order-complete" type="button" class="btn btn-lg btn-primary hpg-wizard__step--next">Checkout</button>
              </p>
            </div>
            <div id="shipping" class="hpg-wizard__step">
              <fieldset class="form-inline">
                <legend>Shipping</legend>
                <div class="form-group">
                  <label for="country" class="control-label">Country</label>
                  <select id="country" class="form-control input-sm" name="country" required="required">
                    <option value="AU">Australia</option>
                    <option value="GB">United Kingdom</option>
                    <option value="US" selected="selected">United States</option>
                    <option value="CA">Canada</option>
                  </select>
                </div><!--/.form-group-->
                <div class="form-group">
                  <label for="postal" class="control-label">Postal Code</label>
                  <input id="postal" class="form-control input-sm hpg-input-postal" name="postal" type="text" pattern="[0-9]+" value="<?php echo $previouslyEnteredPostalAddress; ?>" />
                </div><!--/.form-group-->
                <div class="form-group">
                  <label for="shipping-method" class="control-label">Method</label>
                  <p id="shipping-method-help" class="help-block">Please allow an additional 2–7 business days for fulfillment. Revise Order or Shipping details to re-calculate options.</p>
                  <div class="hpg-product-shipping-method">
                    <img id="shipping-methods-recalculating" src="/wordpress/wp-content/uploads/2016/12/ripple.svg" width="39" height="39" alt="looping ripple animation" title="Recalculating…" style="opacity: 0;" aria-hidden="true" />
                    <select aria-describedby="shipping-method-help" id="shipping-method" class="form-control input-sm hpg-product-shipping-method__select" name="shippingMethod">
                      <?php/* foreach ( $defaultRates as $rateIndex => $rate ) : ?>
                        <option value='<?php echo json_encode( $rate ); ?>'><?php echo str_replace( ' after fulfillment', '', str_replace( '-', '–', $rate['name'] ) ); ?> [$<?php echo $rate['rate']; ?>]</option>
                      <?php endforeach; */?>
                      <option value="">Please enter a Postal Code</option>
                    </select>
                    <?/*<input id="shipping-rate" name="shippingRate" type="hidden" value="<?php echo $defaultRates[0]['rate']; ?>" />*/?>
                  </div><!--/.hpg-product-shipping-method-->
                  <!-- <button id="get-methods" class="btn btn-default" type="button">Get Methods</button> -->
                </div><!--/.form-group-->
              </fieldset>
              <p style="margin-top: .5em;">
                <button id="your-order-revise" class="btn btn-default hpg-wizard__step--prev" type="button">Revise Order</button>
                <button id="cta" class="btn btn-lg btn-primary" type="submit"><?php echo $ctaText; ?></button>
              </p>
            </div>
          </form>
        </div><!--/offers-->
        <script>
          // https://24ways.org/2010/calculating-color-contrast/
          // function getContrastYIQ( hexcolor ) {
          //   var r = parseInt(hexcolor.substr(0,2),16);
          //   var g = parseInt(hexcolor.substr(2,2),16);
          //   var b = parseInt(hexcolor.substr(4,2),16);
          //   var yiq = ((r*299)+(g*587)+(b*114))/1000;
          //   return (yiq >= 128) ? 'black' : 'white';
          // }

          function getPrintfulShippingRatesHTML( rates ) {
            /* Mirrors PHP foreach loop in template */
            var rate;
            var html = '';
            var i = rates.length - 1;

            for ( ; i >= 0; --i ) {
              rate = rates[i];

              // @workaround. @todo - https://trello.com/c/2CoS74Hb
              if ( !rate['name'].match( /flat rate(?:\s+\(.*\))?/i ) ) {
                html += "<option data-rate='" + rate.rate + "' value='" + JSON.stringify( rate ) + "'>" + rate['name'].replace( '-', '–' ).replace( ' after fulfillment', '' ) + " [$" + rate.rate + "]</option>";
              } else if ( i === 0 ) {
                html += '<option data-rate="" value="">Please enter a Postal Code</option>';
              }
            };

            return html;
          }

          var animationEvents = 'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd';
          var transitionEvents = 'transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd';

          $(document).ready(function documentReady() {
            function tallyAndUpdateAmount( event ) {
              var tally = 0;
              var $quantities = $('.qty');
              var shippingMethod = $shippingMethods.val();

              console.log( 'shippingMethod', shippingMethod );

              var shippingCost = ( parseFloat( JSON.parse( shippingMethod ).rate ) * 100 ) ;

              $quantities.each(function tallyQty() {
                var $qty = $(this);
                var qty = parseInt( $qty.val(), 10 );

                console.log( 'qty', qty );

                tally += qty;
              });

              console.log( tally );

              console.log( price );

              amount = ( price * tally ) + shippingCost;

              $amount.val( amount );

              console.log( 'New Amount: ', amount );
            }

            function repopulateShippingMethods( event ) {
              var postal;

              if ( !event ) {
                postal = $postal.val();
              } else {
                postal = $(this).val();
              }

              $shippingMethods.prop( 'disabled', true );
              // $shippingMethodsRecalculating.prop( 'hidden', false );
              $shippingMethodsRecalculating
                // .prop( 'hidden', false ) // make perceivable by screen readers
                .attr( 'aria-hidden', 'false' )
                .css( 'opacity', 1 )
              ;

              var checkoutFormJson = $checkoutForm.serializeJSON();

              var totalBatches = Object.keys( checkoutFormJson.batches ).length;

              console.log( 'totalBatches', totalBatches );

              var loopsCompleted = 0;

              var deferred = $.Deferred();

              for ( var batchNumber in checkoutFormJson.batches ) {
                var batch = checkoutFormJson.batches[batchNumber];

                console.log( 'batch', batch );

                (function getBatchVariantID( b ) {
                  // console.log( 'b', b );

                  var url = 'https://hughguiney.com/productize-api/printful-variant/?productID=<?php echo $printfulProductID; ?>&color=' + encodeURIComponent( b.color ) + '&size=' + b.size;

                  console.log( 'url', url );

                  $.get( url, function assignIdToBatch( variant ) {
                    b.variant_id = variant.id;

                    ++loopsCompleted;

                    console.log( 'variant', variant );

                    if ( loopsCompleted == totalBatches ) {
                      deferred.resolve();
                    }
                  }); // $.get
                })( batch ); // getBatchVariantID
              } // for

              deferred.then( function variantIdsResolved () {
                $.post(
                  '/productize-api/printful-shipping/',
                  {
                    "country-code": $country.val(),
                    "zip": postal,
                    "items": checkoutFormJson.batches
                  },
                  function ( rates, textStatus, xhr ) {
                    // console.log( data );
                    var ratesHTML = getPrintfulShippingRatesHTML( rates );
                    var minRate;

                    // console.log( 'ratesHTML', ratesHTML );

                    // for (var i = rates.length - 1; i >= 0; i--) {
                    //   rates[i]
                    // }

                    $shippingMethods
                      .html( $( ratesHTML ) )
                      .find( 'option[data-rate]' ).each(function getMinRate() {
                        var rate = $(this).attr( 'data-rate' );

                        rate = parseFloat( rate );

                        if ( minRate === undefined || minRate > rate ) {
                          minRate = rate;
                        }
                      })
                      .end()
                      .find( 'option[data-rate="' + minRate + '"]' )
                        .prop( 'selected', true )
                      .end()
                      .prop( 'disabled', false )
                    ;

                    $shippingMethodsRecalculating
                      .css( 'opacity', 0 )
                      // .prop( 'hidden', true )
                      .attr( 'aria-hidden', 'true' )
                    ;

                    tallyAndUpdateAmount();
                  }
                );
              });
            } // repopulateShippingMethods

            var $window = $(window);

            var price = <?php echo $priceNormalized; ?>;
            var amount = price;
            var $rows = $('[data-repeater-item]');
            var $row = $rows.eq(0);
            // var rowHeight = $row.height();

            // console.log( rowHeight );

            var rowSlideSpeed = 250;
            
            var $cta = $('#cta');
            var $stripeToken = $('#stripe-token');
            var $stripeEmail = $('#stripe-email');
            var $stripeArgs = $('#stripe-args');
            var $amount = $('#amount');
            // var $selectedColor = $('#color-select-value');
            var $colorSelectButton = $('#color-select-button');
            var $repeaterList = $('[data-repeater-list]');
            var $sizes = $('#sizes');
            var $country = $('#country');
            var $postal = $('#postal');
            var $shipping = $('#shipping');
            var $shippingMethods = $('#shipping-method');
            var $shippingMethodsRecalculating = $('#shipping-methods-recalculating');
            var $yourOrder = $('#your-order');
            var $checkoutForm = $('#checkout').height( $yourOrder.height() );
            var $step = $('.hpg-wizard__step');
            var $productImage = $('#product-image');

            // var $next = $('.hpg-wizard__step--next');
            // var $prev = $('.hpg-wizard__step--prev');

            fbq('track', 'ViewContent', {
              content_type: 'product'
            });

            $step.on('click', '.hpg-wizard__step--next, .hpg-wizard__step--prev', function onWizardPrevNext( event ) {
              event.preventDefault();

              // var $productImageContainer = $productImage.parent();\
              // $productImage.wrap( $( '<div/>' ) );
              /*
                max-width: 540px;
                position: fixed;
                left: 97.5px;
                right: 667.5px;
                bottom: auto;
                width: auto;
                margin-left: 0px;
                margin-right: 0px;
                margin-top: 0px;
              */

              // var $aestheticPerservingDiv = $productImage.parent();

              // $aestheticPerservingDiv.css({
              //   'max-width': $productImage.css( 'max-width' ),
              //   'position': $productImage.css( 'position' ),
              //   // 'top': '0',
              //   'left': $productImage.css( 'left' ),
              //   'right': $productImage.css( 'right' ),
              //   'bottom': $productImage.css( 'bottom' ),
              //   'width': $productImage.css( 'width' ),
              //   'margin-left': $productImage.css( 'margin-left' ),
              //   'margin-right': $productImage.css( 'margin-right' ),
              //   'margin-top': $productImage.css( 'margin-top' )
              // });
              // $aestheticPerservingDiv.css( 'position', 'fixed' );

              // Stickyfill.remove( $productImage.get(0) );

              // $productImage.css( 'position', 'absolute' );

              var $nextButton = $(this);
              var $thisStep = $(event.delegateTarget);
              var $nextStep = $thisStep.siblings('.hpg-wizard__step').eq(0);
              var $container = $thisStep.closest('.hpg-wizard');
              var nextStepHeight = $nextStep.height();

              // $productImageContainer.css( 'height', $productImage.height() );

              console.log( '$nextStep', $nextStep );

              if ( $nextStep.is( '#shipping' ) && ( $postal.val().length > 0 ) ) {
                repopulateShippingMethods();
              }

              $thisStep
                .on( transitionEvents, function onStepAnimationEnd( event ) {
                  console.log( transitionEvents );

                  $checkoutForm.height( nextStepHeight );

                  // setTimeout(function () {
                    // $productImage.css( 'bottom', 0 ).unwrap();

                    // Stickyfill.add( $productImage.get(0) );
                  // }, 1000);

                  // $productImageContainer.css( 'height', $productImageContainer.siblings('.hpg-product-details').eq(0).height() );

                  // $container.toggleClass( 'hpg-wizard--reverse' );

                  $nextStep.addClass( 'hpg-wizard__step--active' );

                  $thisStep.off( transitionEvents );
                })
                .removeClass( 'hpg-wizard__step--active' )
              ;
            });

            $cta.prop( 'disabled', false );

            $postal.on( 'change', repopulateShippingMethods );

            $postal.on( 'keyup', function onPostalKeyup() {
              if ( ( $country.val() === 'US' ) && ( $postal.val().length >= 5 ) ) {
                $postal.blur();
              }
            });

            $country.on( 'change', function countryChanged() {
              $postal.val( '' ).change();
            });

            $repeaterList.on( 'change', '.qty', tallyAndUpdateAmount );

            $shippingMethods.on( 'change', tallyAndUpdateAmount );

            var handler = StripeCheckout.configure({
              key: '<?php echo $stripeLive['publishable_key']; ?>',
              image: '<?php echo $thumbnailUrl; ?>',
              locale: 'auto',
              token: function ( token, args ) {
                // Use the token to create the charge with a server-side script.
                // You can access the token ID with `token.id`
                // console.log( 'token', token );

                // $.post( window.location, { 'stripeToken': token }, function ( data ) {
                //   console.log( data );

                //   $('html').html( data );
                // } );
                $stripeToken.val( token.id );
                $stripeEmail.val( token.email );

                $stripeArgs.val( JSON.stringify( args ) );

                fbq( 'track', 'Purchase', {
                  value: ( amount / 100 ),
                  currency: '<?php echo $currency; ?>',
                  content_type: 'product'
                } );

                ga( 'send', 'event', 'Products', 'Purchase', 'The Kramper' );
                
                $checkoutForm.submit();
              }
            });

            // description: '', // Category
            $cta.on('click', function( event ) {
              fbq( 'track', 'AddToCart', {
                value: ( amount / 100 ),
                currency: '<?php echo $currency; ?>',
                content_type: 'product'
              } );

              if ( $checkoutForm[0].checkValidity() ) {
                fbq( 'track', 'InitiateCheckout' );

                // Open Checkout with further options
                handler.open({
                  name: '<?php the_title(); ?>',
                  description: $('#product-details > dd:first-of-type').text(),
                  amount: amount,
                  currency: '<?php echo $currency; ?>',
                  bitcoin: true,
                  // panelLabel: '<?php echo $ctaText; ?> for ',
                  shippingAddress: true,
                  billingAddress: true
                });
              } else {
                var $invalid = $('input:invalid, select:invalid');

                var $firstInvalid = $invalid.eq(0);

                console.log( $invalid );

                var $invalidParent = $invalid.parent();

                $invalidParent.addClass('has-error');

                $invalid.on('keyup keypress blur change', function removeInvalid() {
                  var $formControl = $(this);
                  
                  if ( $formControl.is(':valid') ) {
                    $invalidParent.removeClass('has-error');

                    $formControl.off('keyup');
                  }
                });

                $firstInvalid.focus();
              }

              event.preventDefault();
            });

            // Close Checkout on page navigation
            $window.on('popstate', function() {
              handler.close();
            });

            $colorSelectButton.children('.hpg-btn-text').eq(0).html( $('[data-select-color][data-select-default]').html() );

            $('.repeater').repeater({
              defaultValues: {
                "size": "<?php echo $defaultSize; ?>",
                // "qty": "1"
                // "color-select": "#ffffff"
              },
              // show: slideDownRepeaterRow,
              show: function showRepeaterRow() {
                var $row = $(this);

                $checkoutForm.height( $checkoutForm.height() + $row.height() );

                $row.find('.qty').eq(0).val(1);
                $row.find('.color-select-value').eq(0).val('#ffffff');

                $row.slideDown( rowSlideSpeed, function () {
                  console.log('shit');
                });
              },
              // hide: slideUpRepeaterRow,
              hide: function hideRepeaterRow() {
                var $row = $(this);

                $checkoutForm.height( $checkoutForm.height() - $row.height() );

                $row.slideUp( rowSlideSpeed, function () {
                  // console.log('fuck');
                  $row.remove();
                });
              },
              isFirstItemUndeletable: true
            });

            $repeaterList.on('click.bs.dropdown', '[data-select-color]', function colorSelected( event ) {
              var $colorSelect = $(this);

              console.log( $colorSelect );

              var $menu = $colorSelect.closest('.dropdown-menu');
              var $button = $menu.siblings('.dropdown-toggle').eq(0);
              var $buttonText = $button.find('.hpg-btn-text').eq(0);
              var newColor = $colorSelect.attr('data-select-color');

              $buttonText.html( $colorSelect.html() );
              // $selectedColor.val( newColor );
              var $hiddenInput = $menu.parent('.dropdown').siblings('.color-select-value').eq(0);
              var $hiddenInputColorName = $hiddenInput.siblings('.color-select-value-name').eq(0);

              // console.log( '$hiddenInput', $hiddenInput );

              $hiddenInput.val( newColor );
              $hiddenInputColorName.val( $colorSelect.text() );

              $productImage.attr( 'src', $productImage.attr('src').replace(/([A-Za-z0-9\-]+)(\.(?:png|jpg|jpeg|gif|svg))$/, $buttonText.text().replace(' ', '-') + '$2') );

              event.preventDefault();
            });

            // $.get( 'https://hughguiney.com/productize-api/printful-variant-id/?productID=<?php echo $printfulProductID; ?>&color=' + $selectedColor.val() + '&size=' + $sizes.val(), function getPrintfulVariant( variant ) {
            //   console.log( variant );
            // } )

            function resizeCheckoutForm() {
              $checkoutForm.height( $checkoutForm.find('.hpg-wizard__step--active').eq(0).height() );
            }

            $window.resize(function windowResize() {
              // $checkoutForm.height(  );
              // var yourOrderHeight = $yourOrder.height();
              // var shippingHeight = $shipping.height();

              // if ( yourOrderHeight > shippingHeight ) {
              //   $checkoutForm.height( yourOrderHeight );
              // } else if ( shippingHeight > yourOrderHeight ) {
              //   $checkoutForm.height( shippingHeight );
              // } else {
                resizeCheckoutForm();
              // }
            });

            resizeCheckoutForm();
          });
        </script>
      </div><!--/.col-sm-6-->
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
  <?php // endwhile; ?>
<?php } ?>