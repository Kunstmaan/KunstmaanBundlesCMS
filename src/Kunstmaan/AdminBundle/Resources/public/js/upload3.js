$(function () {
    $('#drop-area').fileupload({
        dataType: 'json',
        url: 'php/index.php',
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        }
    });
});