//beaware that the little difference between WP-JQ and JQuery , that matters
//https://developer.wordpress.org/plugins/javascript/jquery/
//if like pressed
jQuery(document).ready(function($) {
    $("#zl-like").click(function () {
        var counts = $(this).children('.like_counts'),  //query class like_counts of the page
            id = $(this).data("id");        //query data-id of the page
        jQuery.post(zl_press_action.ajax_url, {         //POST request
           _ajax_nonce: zl_press_action.nonce,     //nonce
            action: "zl_like_press",              //action
            post_id: id                 //id
        }, function(data) {
            //callback
            res = "(" + data + ")";
            $(counts).html(res);     //add suffix as the class content
        });
    });
});

//if dislike pressed
jQuery(document).ready(function($) {
    $("#zl-dislike").click(function () {
        alert("dislike");
    });
});

//if donate pressed
jQuery(document).ready(function($) {
    $("#zl-donate").click(function () {
        alert("donate");
    });
});

