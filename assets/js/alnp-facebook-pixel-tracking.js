;( function ( $, window, document, undefined ) {

  $('body').on( 'alnp-post-changed', function( e, post_title, post_url, post_id ) {
    fbq('track', 'PageView', {
      "source": "auto-load-next-post",
      "version": "1.4.9",
      "pluginVersion": alnp_fb_pixel.pluginVersion
    });
  });

})( jQuery, window, document );
