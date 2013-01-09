// Author: Indri & Ibe

// Init functions needed on every page
$(document).ready(function () {
	// init_tree();
	init_main_functions();
	initTop();
	initCancel();
	initCustomSelect();
	initDel();
	initFilter();
	initTimePicker();
	initDatePicker();
	initDropdownButton();
				// initStickyColumns();
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
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-57px -57px" 
					}
				},
				"page_offline" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-57px -74px" 
					}
				},			
				//Site
				"site" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-75px -38px" 
					}
				},
				//Settings
				"settings" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-57px -37px" 
					}
				},
				//Image
				"image" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-20px -74px" 
					}
				},
				//Video
				"video" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-75px -55px" 
					}
				},
				//Slideshow
				"slideshow" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
						"position" : "-2px -72px" 
					}
				},
				//Files
				"files" : {
					"icon" : {
						"image" : $.jstree._themes + "OMNext/d.png",
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

// Media-Grid Helper
function initDel() {
	$('.media-grid .del').live("mouseover mouseout", function(e) {
		if (e.type == "mouseover") {
			$(this).parent().find('.item').addClass('warning');
			$(this).parent().find('.helper').html('Delete').addClass('warning');
		} else {
			$(this).parent().find('.item').removeClass('warning');
			$(this).parent().find('.helper').html('Click to edit').removeClass('warning');
		}
	});
}

function initCancel() {
	$('.close_modal').on('click', function (e) {
		e.preventDefault();
		$(this).parents('.modal').modal('hide');
	});
}

// Datepicker
function init_datePicker() {
	$('.date-pick').datePicker();
}

// Toggle Properties
function init_toggleProp() {
	$("#toggle-properties").click(function(e){
		e.preventDefault();
		if ($(this).hasClass('big')) {
			$("#prop_wrp").slideUp("slow").animate({opacity: 0},{queue: false, duration: "slow"}).addClass('small_prop').removeClass('big_prop');
			$(this).removeClass('big').addClass('small').html('Show Properties');
			
			
					
		}
		else {
			$("#prop_wrp").slideDown("slow").animate({opacity: 1},{queue: false, duration: "slow"}).addClass('small_prop').removeClass('big_prop');
			$(this).removeClass('small').addClass('big').html('Hide Properties');
		}
	});
}

//Twipsy
function init_twipsy() {
	$("a[rel=twipsy]").twipsy({live:true});
};

// Custom Select
function initCustomSelect() {
	$('.chzn-select').chosen();
}

//Filter
function initFilter() {
	var checked = $("#filter_on_off").attr("checked");
	
	if (checked) {
		$(".all").removeClass("active");
		$(".filters_wrp").addClass("active");
	} else {
		$(".all").addClass("active");
	}
	
	$("#filter_on_off").iphoneStyle({
		checkedLabel: '',
		uncheckedLabel: '',
		resizeHandle: true,
		resizeContainer: true,
		dragThreshold: 0,
		handleMargin: 5,
		handleRadius: 12,
		containerRadius: 5,
		onChange: function (e, value) {
			if(value){
				$(".all").removeClass("active");
				$(".filters_wrp").addClass("active");				
			} else {
				$(".all").addClass("active");
				$(".filters_wrp").removeClass("active");
			}
		}
	});
}

function initDatePicker() {
	// http://www.eyecon.ro/bootstrap-datepicker/
	if($('.form_datepicker').length > 0) {
		$(".form_datepicker").datepicker({
			'format': 'dd/mm/yyyy'
		});
	}
}


function initTimePicker() {
	// http://jdewit.github.com/bootstrap-timepicker/
	if($('.form_timepicker').length > 0) {
		$(".form_timepicker").timepicker({
			'showMeridian': false,
			'minuteStep': 1
		});
	}
}

function initDropdownButton() {
	var $el = $('.main_actions .btn.dropdown-toggle');

	if($el.is(':nth-child(2)')) {
		$el.css({
			'-webkit-border-top-right-radius': 0,
			'-webkit-border-bottom-right-radius': 0,
			'-moz-border-radius-topright': 0,
			'-moz-border-radius-bottomright': 0,
			'border-top-right-radius': 0,
			'border-bottom-right-radius': 0
		})
	}

	$el.on('click', function() {
		var offset = $el.offset().left - $('.main_actions').offset().left - $('.main_actions .dropdown-menu').outerWidth() + $(this).outerWidth();
		$el.next('.dropdown-menu').css('left', offset);
	});
}




// UI SORTABLE
$(function() {
    var sortableClicked = false;
    $('.sortable').mousedown(
        function() {
            sortableClicked = true;
            var scope = $(this).data('scope');
            $('.sortable[data-scope~=' + scope + ']')
                .addClass('connectedSortable')
                .sortable('option', 'connectWith', '.connectedSortable');
            $('.sortable:not([data-scope~=' + scope + '])')
                .sortable('disable')
                .sortable('option', 'connectWith', false)
                .parent().addClass('region-disabled');
            $('.template-block-content').not('.sortable')
                .parent().addClass('region-disabled');
        }
    );
    $('body').mouseup(
        function() {
            if (sortableClicked) {
                // Enable all sortable regions again
                $('.sortable')
                    .sortable('enable')
                    .sortable('option', 'connectWith', false)
                    .parent().removeClass('region-disabled');
                $('.template-block-content').not('.sortable')
                    .parent().removeClass('region-disabled');
                sortableClicked = false;
            }
        }
    );
    $( ".sortable" ).sortable({
        connectWith: ".connectedSortable",
        placeholder: "sortable-placeholder",
        tolerance:"pointer",
        revert: 300
/*
        stop: function(e, ui) {
            // Enable all
            $('.sortable')
                .sortable('enable')
                .sortable('option', 'connectWith', false)
                .parent().removeClass('region-disabled');
        }
*/
    }).disableSelection();
});