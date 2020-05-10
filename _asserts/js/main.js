$(document).ready( function () {
    /* fixed wheater mobile 100vh will overflow */
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    window.addEventListener('resize', () => {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });

    /* initial all modals */
    $('#restaurant-result').modal();
    $('#setting-modal').modal({
        onCloseEnd: function () {
            window.location.reload();
        }
    });

    /* get data from JSON */
    let food;
    $.getJSON("_data/food.json", function(json) {
        food = json;

        /* initial brunch list */
        let brunch = [];
        for (let i = 0; i < food.brunch.length; i++) {
            brunch.push({tag: food.brunch[i]});

        }

        $('#list-brunch').chips({
            data: brunch,
            onChipAdd: function (e, chip){
                let newest = chip.innerHTML.replace('<i class="material-icons close">close</i>',"")
                let type = "brunch";

                $.ajax({
                    type: "POST",
                    url: "/_exec/add-entry.php",
                    data: { "new": newest, "type": type, "code": "add-chip-code"},
                    success: function (res) {
                        console.log(res);
                    }
                });
            },
            onChipDelete: function (e, chip){
                let del = chip.innerHTML.replace('<i class="material-icons close">close</i>',"")
                let type = "brunch";
                
                $.ajax({
                    type: "POST",
                    url: "/_exec/remove-entry.php",
                    data: { "del": del, "type": type, "code": "remove-chip-code"},
                    success: function (res) {
                        console.log(res);
                        $("#list-brunch").find("input")[0].blur()
                    }
                });
            }
        });

        /* initial dinner list */
        let dinner = [];
        for (let i = 0; i < food.dinner.length; i++) {
            dinner.push({'tag': food.dinner[i]});
        }

        $('#list-dinner').chips({
            data: dinner,
            onChipAdd: function (){
                let chips = $(this)[0].$chips;
                let newest = chips[chips.length - 1].innerHTML.replace('<i class="material-icons close">close</i>',"")
                let type = "dinner";
            
                $.ajax({
                    type: "POST",
                    url: "/_exec/add-entry.php",
                    data: { "new": newest, "type": type, "code": "add-chip-code"},
                    success: function (res) {                        
                        console.log(res);
                    }
                });
            },
            onChipDelete: function (e, chip){
                let del = chip.innerHTML.replace('<i class="material-icons close">close</i>',"")
                let type = "dinner";
                
                $.ajax({
                    type: "POST",
                    url: "/_exec/remove-entry.php",
                    data: { "del": del, "type": type, "code": "remove-chip-code"},
                    success: function (res) {
                        console.log(res);
                        $("#list-dinner").find("input")[0].blur()
                    }
                });
            }
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
                $("#result span").text("我也不知道吃啥，呵呵");
                $("#result b").text("");
                $("#restaurant-result .modal-close").text("幹");

                return;
            }
    
            /* create a random index number */
            var pool_length = pool.length;
            var pool_index = random_range(pool_length)
    
            /* get current reataurant */
            shuffle(pool);
            var rastaurant = pool[pool_index];
            
            /* popup a result modal */
            $("#result span").text("，你說好不好？");
            $("#result b").text(split_str_add_dot(rastaurant));
            $("#restaurant-result .modal-close").text("好");
        });
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
