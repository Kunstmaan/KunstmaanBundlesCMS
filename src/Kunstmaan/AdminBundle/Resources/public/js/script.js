//Author: Indri & Ibe

//Init functions needed on every page
$(document).ready(function () {
	init_main_functions();
	initTop();
	initCustomSelect();
	initDel();
	initFilter();
    initDatePicker();
});

//JS-tree
function init_tree() {
	$('.tree').jstree({
		"plugins" : [ "themes", "html_data", "types", "search" ] ,
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
            "show_only_matches" : true
        }
	});
    $("#treeform").submit(function() {
        $('.tree').jstree('search', $('#treeform #searchVal').val());
        return false;
    });
    $('#treeform #searchVal').keyup(function() {
        $('.tree').jstree('search', $('#treeform #searchVal').val());
    })

	$('.pagestree').jstree({
		"plugins" : [ "themes", "html_data", "dnd", "crrm", "types", "search" ] ,
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
		"crrm" : { 
			"move" : {
				"check_move" : function (m) { 
					var p = this._get_parent(m.o);
					if(!p) return false;
					p = p == -1 ? this.get_container() : p;
					if(p === m.np) return true;
					if(p[0] && m.np[0] && p[0] === m.np[0]) return true;
					return false;
				}
			}
		},
		"dnd" : {
			"drop_target" : false,
			"drag_target" : false
		},
		// Configuring the search plugin
		"search" : {
            "show_only_matches" : true
		}
	})
    $("#pagestreeform").submit(function() {
        $('.pagestree').jstree('search', $('#pagestreeform #searchVal').val());
        return false;
    });
    $('#pagestreeform #searchVal').keyup(function() {
        $('.pagestree').jstree('search', $('#pagestreeform #searchVal').val());
    })

	$('.mediatree').jstree({
		"plugins" : [ "themes", "html_data", "dnd", "crrm", "types", "search" ] ,
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
		"crrm" : { 
			"move" : {
				"check_move" : function (m) { 
					var p = this._get_parent(m.o);
					if(!p) return false;
					p = p == -1 ? this.get_container() : p;
					if(p === m.np) return true;
					if(p[0] && m.np[0] && p[0] === m.np[0]) return true;
					return false;
				}
			}
		},
		"dnd" : {
			"drop_target" : false,
			"drag_target" : false
		},
        // Configuring the search plugin
        "search" : {
            "show_only_matches" : true
        }
	})
    $("#mediatreeform").submit(function() {
        $('.mediatree').jstree('search', $('#mediatreeform #searchVal').val());
        return false;
    });
    $('#mediatreeform #searchVal').keyup(function() {
        $('.mediatree').jstree('search', $('#mediatreeform #searchVal').val());
    })
}

//Drag and Drop
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

 

//Drop down main_actions
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

//Toplink
function initTop() {
	$(".up").click(function(e) {
		e.preventDefault();
		$('html, body').animate({scrollTop:0}, 700);
	});
}

//Thumbnails Helper
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

//Datepicker
function init_datePicker() {
	$('.date-pick').datePicker();
}

//Twipsy
function init_twipsy() {
	$("a[rel=twipsy]").twipsy({live:true});
};

//Custom Select
function initCustomSelect() {
	$('select.chzn-select').chosen();
}



////FILTERS
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
				resetFilters();
			}
		
		}
	});
}






function createFilter(el, hide, options){
    console.log("Create filter init");
    
    var line = $(el).parent("li");
    
    if(hide == true){
        line.addClass("hidden");
    }
  
    var uniqueid = calculateUniqueFilterId();
    var newitem = $('<li>').attr('class', 'filterline').append($("#filterdummyline").html());
    
    if(hide == true){
    	line.after(newitem);
    } else {
    	line.before(newitem);
    }
    
    
    
    newitem.find(".uniquefilterid").val(uniqueid);
    newitem.find(".filterdummy").val(line.find(".filterselect").val());
    updateOptions(newitem.find(".filterdummy"), options);
    
    if(hide == true){
        newitem.removeClass("hidden");
        line.find("select").val("");
    } else {
        newitem.slideDown();
    }

    return false;
}



function resetFilters(){
    $(".filterline").remove();
    $(".apply_filter").click();
    return false;
}



function removeFilter(el){
    if($("#filtermap").find(".filterline").length==2){
        $(el).parent(".filterline").remove();
        $("#addline").removeClass("hidden");
    } else {
        $(el).parent(".filterline").slideUp(function(){
            $(this).remove();
        });
    }
    return false;
}



function calculateUniqueFilterId(){
    var result = 1;
    $("input.uniquefilterid").each(function(){
        var value = parseInt( jQuery(this).val() );
        if(result <= value){
            result = value+1;
        }
    });
    return result;
}



function updateOptions(el, options){
    var val = $(el).val();
    val = val.replace('.',  '_');
    var uniqueid = $(el).parent(".filterline").find(".uniquefilterid").val();
    $(el).parent(".filterline").find(".filteroptions").html($('#filterdummyoptions_'+ val).html());
    $(el).parent(".filterline").find("input, select").each(function(){
    	var fieldName = $(this).attr("name");
    	if (fieldName.substr(0, 7) != "filter_") {
    		$(this).attr("name", "filter_" + $(this).attr("name"));
    	}
    });    
    $(el).parent(".filterline").find(".filteroptions").find("input:not(.uniquefilterid), select").each(function(){
        $(this).attr("name", $(this).attr("name") + "_" + uniqueid);
		if($(this).hasClass("datepick")){
			$(this).datePicker(options);				
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