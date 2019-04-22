// jQuery(document).ready(function($) {           //wrapper
//     $(".lzl_like_func_style_1").click(function() {             //event
//         var this2 = this;                      //use in callback
//         $.post(my_ajax_obj.ajax_url, {         //POST request
//            _ajax_nonce: my_ajax_obj.nonce,     //nonce
//             action: "my_tag_count",            //action
//             title: this.value                  //data
//         }, function(data) {                    //callback
//             // this2.nextSibling.remove();        //remove current title
//             // $(this2).after(data);              //insert server response
//             alert( "Handler for .click() called." );
//         });
//     });
// });
window.onload=function(){
    var onlike = document.getElementById('#like');
    onlike.addEventListener('click',function (e) {
    alert('1. Div capture ran');
    },true);
}

$(document).ready(function() {
  $(".lzl_like_func_style_1").click(function () {
    alert("Hello!");
  });
});

$(document).ready(function(){
  $('#like').on("click",function(){
    alert("段落被点击了。");
  });
});
