( function( $ ){
    $( document ).ready( function(){
      $( '.rswpbs-install' ).on( 'click', function( e ) {
          e.preventDefault();
          $( this ).html( 'Processing.. Please wait' ).addClass( 'updating-message' );
          $.post( blog_designer_for_elementor_rswpbs_ajax_object.ajax_url, { 'action' : 'install_rswpbs_plugin' }, function( response ){
              location.href = 'edit.php?post_type=book&page=rswpbs-tutorial';
          } );
      } );
    } );
}( jQuery ) )
document.addEventListener('DOMContentLoaded', function () {
    const closeButton = document.querySelector('.rswpbs-banner-close');
    const banner = document.getElementById('rswpbs-banner-ad');
    const fiveHours = 5 * 60 * 60 * 1000;

    // Check if the banner should be hidden
    const hideUntil = localStorage.getItem('rswpbs_hide_until');
    const currentTime = new Date().getTime();

    if (hideUntil && currentTime < hideUntil) {
        banner.style.display = 'none'; // Hide banner if still within 5-hour window
    }

    // Handle close button click
    closeButton.addEventListener('click', function () {
        banner.style.display = 'none'; // Hide banner

        // Set the time to hide the banner for the next 5 hours
        const hideUntilTime = new Date().getTime() + fiveHours;
        localStorage.setItem('rswpbs_hide_until', hideUntilTime);
    });
});