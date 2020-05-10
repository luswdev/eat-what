'use strict';

$(document).ready( function () {
    $('.modal').modal();

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
        
        $("#result").text(rastaurant + '，你說好不好？');
        var elem = document.getElementById('restaurant-result');
        var instance = M.Modal.getInstance(elem);
        instance.open();
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