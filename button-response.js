//beaware that the little difference between WP-JQ and JQuery , that matters
//https://developer.wordpress.org/plugins/javascript/jquery/

//if like pressed
jQuery(document).ready(function($) {
    $("#zl-like").click(function () {
        alert("like");
        var this2 = this;                      //use in callback
        $.post(zl_press_action.ajax_url, {         //POST request
           _ajax_nonce: zl_press_action.nonce,     //nonce
            action: "zl_like_press",            //action
            count: this.value                  //data
        }, function(data) {                    //callback
            alert("like-cb");
            // this2.nextSibling.remove();        //remove current title
            // $(this2).after(data);              //insert server response
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

