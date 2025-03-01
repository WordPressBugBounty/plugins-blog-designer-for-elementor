( function( $ ){
    $( document ).ready( function(){
      $( '.bdfe-rswpbs-install' ).on( 'click', function( e ) {
          e.preventDefault();
          $( this ).html( 'Processing.. Please wait' ).addClass( 'updating-message' );
          $.post( bdfe_ajax_object.ajax_url, { 'action' : 'install_rswpbs_only' }, function( response ){
              location.href = 'edit.php?post_type=book&page=import-books-from-json#rswpbs-nav-wrapper';
          } );
      } );
    } );
}( jQuery ) )