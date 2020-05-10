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

        /* create a random index number */
        var pool_length = pool.length;
        var pool_index = random_range(pool_length)

        /* get current reataurant */
        shuffle(pool);
        var rastaurant = pool[pool_index];
        
        /* popup a result modal */
        $("#result").text(rastaurant + '，你說好不好？');
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