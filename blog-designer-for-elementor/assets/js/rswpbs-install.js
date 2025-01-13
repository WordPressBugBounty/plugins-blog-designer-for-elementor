(function($) {
    $(document).ready(function() {
        $('#install-rswpbs').on('click', function(e) {
            e.preventDefault();
            $(this).html('Processing.. Please wait').addClass('updating-message');
            $.post(bdfe_rswpbs_ajax_object.ajax_url, { 'action': 'install_rswpbs_plugin' }, function(response) {
                location.href = 'edit.php?post_type=book&page=rswpbs-tutorial';
            });
        });
    });
}(jQuery))