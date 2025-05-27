$(document).ready(function() {
    $('form').on('beforeSubmit', function() {
        $('#loading-overlay').removeClass('d-none').addClass('d-flex');
        return true;
    });

    $(window).on('load', function() {
        $('#loading-overlay').addClass('d-none').removeClass('d-flex');
    });

    $(document).ajaxComplete(function() {
        $('#loading-overlay').addClass('d-none').removeClass('d-flex');
    });
});