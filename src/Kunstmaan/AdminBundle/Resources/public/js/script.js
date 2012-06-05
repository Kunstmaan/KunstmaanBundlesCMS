// Author: Indri & Ibe

// Init functions needed on every page
$(document).ready(function () {
	init_tree();
	init_main_functions();
	initTop();
	initCustomSelect();
	initDel();
});

// JS-tree
function init_tree() {
	$('.tree').jstree({
		"plugins" : [ "themes", "html_data", "types", "search" ],
		"themes" : { 
			"theme" : "OMNext",
			"dots" : true,
			"icons" : true		
		},
		"types" : {
			"types" : {
				//Page
				"page" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-57px -57px" 
					}
				},
				"page_offline" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-57px -74px" 
					}
				},			
				//Site
				"site" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-75px -38px" 
					}
				},
				//Settings
				"settings" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-57px -37px" 
					}
				},
				//Image
				"image" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-20px -74px" 
					}
				},
				//Video
				"video" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-75px -55px" 
					}
				},
				//Slideshow
				"slideshow" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-2px -72px" 
					}
				},
				//Files
				"files" : {
					"icon" : {
						"image" : "js/libs/themes/OMNext/d.png",
						"position" : "-38px -72px" 
					}
				}
			}
		
		
		},
		// Configuring the search plugin
		"search" : {
			"ajax" : {
			    "url" : "", //<-- Link naar php
			    "data" : function (str) {
			        return {
			            "operation" : "search",
			            "search_str" : str
			        };
			    }
			}
		}
	});
	$("#search").click(function() {
		$(this).jstree("search", document.getElementById("searchVal").value);
	});
	
	
}

// Drag and Drop
function init_DragDrop() {
	$('#parts').sortable({  
	    handle: '.prop_bar',  
	    cursor: 'move',  
	    placeholder: 'placeholder',  
	    forcePlaceholderSize: true, 
		revert: 100,
	    //opacity: 0.4
	    opacity: 1,
		start: function(e, ui) {
			$('.draggable').css('opacity', ".4");
			$('.ui-sortable-helper .new_pagepart').slideUp("fast");
		},
		stop: function(e, ui) {
			$('.draggable').css('opacity', "1");
		}
	});
}

 

// Drop down main_actions
function init_main_functions() {
	$(window).scroll(
		function() {
			var scrollTop = $(this).scrollTop();
			if(scrollTop >= 180){
				$('#main_actions_top').addClass('slideDown');
			}
			
			if(scrollTop < 180){
				$('#main_actions_top').removeClass('slideDown');
			}		
		}
	);
}

// Toplink
function initTop() {
	$(".up").click(function(e) {
		e.preventDefault();
		$('html, body').animate({scrollTop:0}, 700);
	});
}

// Thumbnails Helper
function initDel() {
	$('.thumbnails .del').live("mouseover mouseout", function(e) {
		if (e.type == "mouseover") {
			$(this).parent().find('.thumbnail').addClass('warning');
			$(this).parent().find('.helper').html('Delete').addClass('warning');
		} else {
			$(this).parent().find('.thumbnail').removeClass('warning');
			$(this).parent().find('.helper').html('Click to edit').removeClass('warning');
		}
	});
}

// Datepicker
function init_datePicker() {
	$('.date-pick').datePicker();
}

//Twipsy
function init_twipsy() {
	$("a[rel=twipsy]").twipsy({live:true});
};

// Custom Select
function initCustomSelect() {
	$('.chzn-select').chosen();
}

