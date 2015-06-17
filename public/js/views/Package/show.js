$('form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        type: 'post',
        url: $('form').attr('action'),
        data: $('form').serialize(),
        success: function(result) {
            alert(result);
        }
    });
});