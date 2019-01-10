window.$ = window.jQuery = require('jquery');

$(document).ready(function () {
    console.log("DOCUMENT READY");
});
$(window).on("resize", function () {
    console.log("RESIZE");
});