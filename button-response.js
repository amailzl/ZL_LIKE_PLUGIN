//beaware that the little difference between WP-JQ and JQuery , that matters
//https://developer.wordpress.org/plugins/javascript/jquery/
//if like pressed
jQuery(document).ready(function($) {
	var QR = $('div').find('#theQR');//query class like_counts of the page
	var CM = $('div').find('#comment');//query class like_counts of the page
	$(QR).hide();
	$(CM).hide();
	
	$("#zl-like").click(function () {
		var counts = $(this).children('.like_counts'),  //query class like_counts of the page
			id = $(this).data("id");        //query data-id of the page
		jQuery.post(zl_press_action.ajax_url, {         //POST request
			_ajax_nonce: zl_press_action.nonce,     //nonce
			action: "zl_like_press",              //action
			post_id: id                 //id
		}, function(data) {
			//callback
			if(data == "done"){
				alert("你已经操作过了");
			}else{
				res = "(" + data + ")";
				$(counts).html(res);
			}//add suffix as the class content
		});
	});

	$("#zl-dislike").click(function () {
		var counts = $(this).children('.dislike_counts'), //query class like_counts of the page
			id = $(this).data("id");        //query data-id of the page
		jQuery.post(zl_press_action.ajax_url, {         //POST request
			_ajax_nonce: zl_press_action.nonce,     //nonce
			action: "zl_dislike_press",              //action
			post_id: id                 //id
		}, function(data) {
			//callback
			if(data == "done"){
				alert("你已经操作过了");
			}else{
				res = "(" + data + ")";
				$(counts).html(res);
			}//add suffix as the class content
		});
	});

	$("#zl-donate").click(function () {
		// 		var QR = $('div').find('#theQR'),//query class like_counts of the page
		var id = $(this).data("id");        //query data-id of the page
		jQuery.post(zl_press_action.ajax_url, {         //POST request
			_ajax_nonce: zl_press_action.nonce,     //nonce
			action: "zl_donate_press",              //action
			post_id: id                 //id
		}, function(data) {
			//callback
			if(data == "show"){
				$(QR).show();
				$(CM).show();
			}else{
				$(QR).hide();
				$(CM).hide();
			}
		});
	});
});

