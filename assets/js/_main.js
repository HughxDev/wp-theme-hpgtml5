/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 * ======================================================================== */

var Roots = {
  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages
    }
  },
  // Home page
  home: {
    init: function() {
      // JavaScript to be fired on the home page
    }
  },
  // About page
  about: {
    init: function() {
      // JavaScript to be fired on the about page
    }
  },
  product_template_default: {
    init: function() {
      var $productImage = $('#product-image');
      var productImageContainer = $productImage.parent();
      var fixed = false;

      // console.log( '$productImage.get(0)', $productImage.get(0) );
      // console.log( 'typeof $productImage.get(0)', typeof $productImage.get(0) );

      function toggleFixedSticky() {
        // One-column layout
        if ( matchMedia( '(max-width: 991px)' ).matches ) {
          if ( fixed ) {
            // $productImage
            //   .fixedsticky( 'destroy' )
            //   .Stickyfill()
            //   .css( 'position', 'static' )
            // ;
            Stickyfill.remove( $productImage.get(0) );

            fixed = false;
          }

          $productImage.css( 'max-width', '' );
        // Two-column layout
        } else {
          if ( !fixed ) {
            // $productImage
            //   .css( 'position', '' )
            //   .fixedsticky()
            // ;
            Stickyfill.add( $productImage.get(0) );

            fixed = true;
          }

          $productImage.css( 'max-width', productImageContainer.width() );
        }
      } // toggleFixedSticky

      // FixedSticky.tests.sticky = false;

      // $productImage.fixedsticky();
      // $('#product-image').Stickyfill();

      toggleFixedSticky();

      $( window ).resize(function windowResize() {
        $productImage.css( 'max-width', productImageContainer.width() );

        toggleFixedSticky();
      });
    }
  }
};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);
