'use strict';

$(document).ready(function () {
    $(document).on('click', '.its-maxma-pre-spoiler button', function () {
        $(this).parent().toggleClass('open');
    })
});
