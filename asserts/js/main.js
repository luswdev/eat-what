$(document).ready( function () {
    let food;
    $.getJSON("/data/food.json", function (json) {
        food = json;
    });

    $(".choose-btn").on("click", function () {
        $(".main-inner").append("test");
    });
});