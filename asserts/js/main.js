'use strict';

$(document).ready( function () {
    /* initial all modals */
    $('.modal').modal();

    /* get data from JSON */
    let food;
    $.getJSON("/data/food.json", function (json) {
        food = json;
    });

    $(".choose-btn").on("click", function () {
        var which_index = $("#which-picker input").index($("#which-picker input:checked"));

        /* get right restaurants pool */
        var pool;
        if (which_index == 0) {
            pool = food.brunch;
        } else {
            pool = food.dinner;
        }

        /* pool need to be exist and length must greater 0 */
        if (!pool || !pool.length) {
            $("#result").text("我也不知道吃啥，呵呵");
            $("#restaurant-result .modal-close").text("幹");
        }

        /* create a random index number */
        var pool_length = pool.length;
        var pool_index = random_range(pool_length)

        /* get current reataurant */
        shuffle(pool);
        var rastaurant = pool[pool_index];
        
        /* popup a result modal */
        $("#result b").text(split_str_add_dot(rastaurant));
    });
});

function random_range(max) {
    return Math.floor(Math.random() * Math.floor(max));
}

function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

function split_str_add_dot(str) {
    let new_str = "";
    for (let i = 0; i < str.length - 1; i++) {
        new_str += str[i];
        new_str += "·";
    }
    new_str += str[str.length - 1];

    return new_str;
}