/*
 * jQuery based MtG Deck Builder
 * Version 0.1
 * 
 * Copyright (c) Andrew Ross (http://awross.me)
*/
var KEY = {
    BACKSPACE: 8,
    TAB: 9,
    ENTER: 13,
    ESCAPE: 27,
    SPACE: 32,
    PAGE_UP: 33,
    PAGE_DOWN: 34,
    END: 35,
    HOME: 36,
    LEFT: 37,
    UP: 38,
    RIGHT: 39,
    DOWN: 40,
    NUMPAD_ENTER: 108,
    COMMA: 188
};

var curr_pos = -1;
var input_box = $(".search");


$(document).keyup(function (event) {
	var previous_token;
	var next_token;

	switch(event.keyCode) {
		case KEY.LEFT:
		case KEY.UP:
			move_select_up();
			break;
		case KEY.RIGHT:
		case KEY.DOWN:
			move_select_down();
			//alert($(this).val());
			break;
		case KEY.ESCAPE:
			clearSearch();
			break;
		default:
			doSearch($(".search").val());
			break;
	}
});

function doSearch(q) {
	var dataString = 'q='+ q;
	curr_pos = -1;

	if(q!='')
	{
		$.ajax({
			type: "POST",
			url: "search.php",
			data: dataString,
			cache: false,
			success: function(j)
			{
				//alert(j);
				var items = jQuery.parseJSON(j);
				var t = "";
				$.each(items, function(index, item) {
					t += "<div class='display_box' align='left'>";
					t += "<image src='" + item['image'] + "' style='width:312px; float:right; margin-right:6px' />";
					t += "<br /><span style='font-size:22px; color:#999999'>" + item['name'] + "</span>";
					t += "<br /><span style='font-size:14px; color:#999999'>" + item['set_name'] + "</span>";
					t += "<br /><span style='font-size:18px;'>" + item['text'] + "</span>";
					t += "</div><br />";
				});
				if (t!="")
				{
					$("#display").html(t).show();
				}
				else
				{
					$("#display").html(t).hide();
				}
			}
		});
	}
}

function clearSearch() {
	
	$(".search").val("").focus();
	$("#display").html("").hide();
}

function move_select_up() {
	deselectItem(curr_pos);
	if(curr_pos >= $("#display div").length) {
		curr_pos = 0;
	}
	if(curr_pos <= 0) {
		curr_pos = $("#display div").length - 1;
	} else {
		curr_pos--;
	}
	selectItem(curr_pos);
}

function move_select_down() {
	deselectItem(curr_pos);
	curr_pos++;
	if(curr_pos >= $("#display div").length) {
		curr_pos = 0;
	}
	selectItem(curr_pos);
}

function selectItem(pos) {
	$("#display div").removeClass('selected');
	
	
	$("#display div").eq(curr_pos).addClass('selected');
}

function deselectItem(pos) {
	$("#display div").removeClass('selected');
	
	
	$("#display div").eq(curr_pos).addClass('selected');
}