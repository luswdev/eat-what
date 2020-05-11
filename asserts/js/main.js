'use strict';

$(document).ready( function () {
    /* fixed wheater mobile 100vh will overflow */
    set_vh()
    window.addEventListener('resize', set_vh);

    /* initial all modals */
    $('#restaurant-result').modal();
    $('#setting-modal').modal({
        onCloseEnd: function () {
            window.location.href = '/';
        }
    });

    /* check type by time default */
    let time = new Date().getHours()
    if (time > 16 || time < 2) {
        $('#which-picker span:last-child input').prop("checked", true);
    } else {
        $('#which-picker span:first-child input').prop("checked", true);
    }

    /* get data from JSON */
    $.getJSON(`data/food.json?nocache=${(new Date()).getTime()}`, function(json) {
        let food = json;

        /* initial brunch list */
        let brunch = [];
        for (let i = 0; i < food.brunch.length; i++) {
            brunch.push({tag: food.brunch[i]});
        }

        /* initial dinner list */
        let dinner = [];
        for (let i = 0; i < food.dinner.length; i++) {
            dinner.push({'tag': food.dinner[i]});
        }

        $('#list-brunch').chips({
            data: brunch,
            placeholder: '你想吃什麼',
            secondaryPlaceholder: '+早餐啦',
            onChipAdd: add_chip_callback,
            onChipDelete: delete_chip_callback
        });

       $('#list-dinner').chips({
            data: dinner,
            placeholder: '你想吃什麼',
            secondaryPlaceholder: '+晚餐啦',
            onChipAdd: add_chip_callback,
            onChipDelete: delete_chip_callback
        });

        $(".choose-btn").on("click", function () {
            /* get right restaurants pool */
            var which_index = $("#which-picker input").index($("#which-picker input:checked"));
            var pool = which_index ? food.dinner : food.brunch;;
    
            /* pool need to be exist and length must greater 0 */
            if (!pool || !pool.length) {
                $("#result span").text("我也不知道吃啥，呵呵");
                $("#result b").text("");
                $("#restaurant-result .modal-close").text("幹");

                return;
            }    

            /* get random reataurant */
            shuffle(pool);
            var pool_index = random_range(pool.length)
            var rastaurant = pool[pool_index];
            
            /* popup a result modal */
            $("#result span").text("，你說好不好？");
            $("#result b").text(rastaurant);
            $("#restaurant-result .modal-close").text("好");
        });
    }); 
});

/* fixed vh for mobile via css */
function set_vh() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

/* get a random range number rather than 0..1 */
function random_range(max) {
    return Math.floor(Math.random() * Math.floor(max));
}

/* shuffle a array */
function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

/* use to highlight result */
function split_str_add_dot(str) {
    let new_str = "";
    for (let i = 0; i < str.length - 1; i++) {
        new_str += str[i];
        new_str += "·";
    }
    new_str += str[str.length - 1];

    return new_str;
}

/* general on chip add callback function */
function add_chip_callback(e, chip) {
    let newest = chip.innerHTML.replace('<i class="material-icons close">close</i>',"");
    let type = $(e).is("#list-brunch") ? 'brunch' : "dinner";

    /* execute add chip */
    $.ajax({
        type: "POST",
        url: "/exec/add-entry.php",
        data: { "new": newest, "type": type, "code": "add-chip-code"},
        success: function (res) {
            console.log(res);
        }
    });
}

/* general on chip delete callback function */
function delete_chip_callback(e, chip) {
    let del = chip.innerHTML.replace('<i class="material-icons close">close</i>',"");
    let type = $(e).is("#list-brunch") ? 'brunch' : "dinner";

    /* execute delete chip */
    $.ajax({
        type: "POST",
        url: "/exec/delete-entry.php",
        data: { "del": del, "type": type, "code": "delete-chip-code"},
        success: function (res) {
            console.log(res);
            $(".chips").find("input").blur();
        }
    });
}