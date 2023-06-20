$(function() {
    $('[data-toggle="tooltip"]').tooltip({
        title: function() {
            return $(this).data('tooltip');
        }
    });
});