//Init functions needed on every page
$(document).ready(function () {
    if($('.tree').length > 0) {
        init_tree();
    }
    init_main_functions();
    initTop();
    initCancel();
    initCustomSelect();
    initDisableButtonsOnSubmit();
    initDel();
    initFilter();
    initDropdownButton();
    initModalFocus();
    initSaveKeyListener();
    initSidenavSize();
});

//JS-tree
function init_tree() {
    var topLevelTreeElements = "";
    $('.tree > ul > li').each(function() {
        topLevelTreeElements = topLevelTreeElements + $(this).attr('id'); + ",";
    });
    $('.tree').jstree({
        "plugins" : [ "themes", "html_data", "dnd", "crrm", "types", "search" ] ,
        "themes" : {
            "theme" : "OMNext",
            "dots" : true,
            "icons" : true
        },
        "core": {
            "animation": 0,
            "open_parents": true,
            "initially_open": [topLevelTreeElements]
        },
        "crrm":{
            "move": {
                // Move only to current parent folder
                "check_move": function(m){
                    return m.op.is(m.np);
                }
            }
        },
        "dnd" : {
            "drag_target": false,
            "drop_target": false
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
    }).bind("move_node.jstree", function(e, jstreeData){

            var $parentNode = jstreeData.args[0].np,
                url = $(this).data('reorder-url'),
                params = {
                    nodes : []
                };

            $parentNode.find(" > ul > li").each(function(){
                var id = $(this).attr("id").replace(/node-/,'');
                params.nodes.push(id);
            });

            $.post(
                url,
                params,
                function(result){
                    // Could display a success message from here.
                }
            );
        }
    );

    $("#treeform").submit(function() {
        $('.tree').jstree('search', $('#treeform #searchVal').val());
        return false;
    });
    $('#treeform #searchVal').keyup(function() {
        $('.tree').jstree('search', $('#treeform #searchVal').val());
    });
    $('.pagestree').jstree({
        "plugins" : [ "themes", "html_data", "dnd", "crrm", "types", "search" ] ,
        "themes" : {
            "theme" : "OMNext",
            "dots" : true,
            "icons" : true
        },"core": {
                    "animation": 0,
                    "open_parents": true,
                    "initially_open": [topLevelTreeElements]
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
    });
    $("#pagestreeform").submit(function() {
        $('.pagestree').jstree('search', $('#pagestreeform #searchVal').val());
        return false;
    });
    $('#pagestreeform #searchVal').keyup(function() {
        $('.pagestree').jstree('search', $('#pagestreeform #searchVal').val());
    });
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
    });
    $("#mediatreeform").submit(function() {
        $('.mediatree').jstree('search', $('#mediatreeform #searchVal').val());
        return false;
    });
    $('#mediatreeform #searchVal').keyup(function() {
        $('.mediatree').jstree('search', $('#mediatreeform #searchVal').val());
    });
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
    $('.thumbnails .del').hover(
        function() {
            $(this).parent().find('.thumbnail').addClass('warning');
            $(this).parent().find('.helper').html('Delete').addClass('warning');
        },
        function() {
            $(this).parent().find('.thumbnail').removeClass('warning');
            $(this).parent().find('.helper').html('Click to edit').removeClass('warning');
        }
    );
}

function initCancel() {
    $('.close_modal').on('click', function (e) {
        e.preventDefault();
        $(this).parents('.modal').modal('hide');
    });
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
}

//Custom Select
function initCustomSelect() {
    $('select.chzn-select').each(function() {
        $(this).chosen({
            search_contains: true,
            allow_single_deselect: $(this).attr('data-allowempty'),
            width: ($(this).attr('data-chznwidth') ? $(this).data('chznwidth') : '365px')
        });
    });
}

//Custom Select
function initDisableButtonsOnSubmit() {
    $("#pageadminform").submit(function(){
        $(".main_actions").find("button, a").attr('disabled','disabled').addClass("disabled");
    });
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
    var line = $(el).parent("li"),
        uniqueid = calculateUniqueFilterId(),
        newitem = $('<li>').attr('class', 'filterline').append($("#filterdummyline").html());

    if(hide === true){
        line.addClass("hidden");
    }

    if(hide === true){
        line.after(newitem);
    } else {
        line.before(newitem);
    }

    newitem.find(".uniquefilterid").val(uniqueid);
    newitem.find(".filterdummy").val(line.find(".filterselect").val());
    updateOptions(newitem.find(".filterdummy"), options);

    if(hide === true){
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

function removeThisFilter(el){
    if($("#filtermap").find(".filterline").length === 2){
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
        var value = parseInt(jQuery(this).val(), 10);
        if(result <= value){
            result = value + 1;
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
        var name = $(this).attr("name");
        var bracketPos = name.indexOf("[");
        if (bracketPos !== -1) {
            var arrayName = name.substr(0, bracketPos);
            var arrayIndex = name.substr(bracketPos);
            $(this).attr("name", arrayName + "_" + uniqueid + arrayIndex);
        } else {
            $(this).attr("name", $(this).attr("name") + "_" + uniqueid);
        }
        if($(this).hasClass("datepick")){
            $(this).datepicker(options);
        }
    });
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
        });
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
    }).disableSelection();
});

function initSidenavSize() {
    $('#adjust_sidebar').on('click', function () {
        $('#main_content, #sidebar').toggleClass('full_view');
        $(this).toggleClass('sidebar_hidden');
    });
}

function initModalFocus() {
    $('.modal').on('shown', function () {
        var inputs = jQuery(this).find('input[type=text]');
        if(inputs.length>0) {
            inputs[0].focus();
        } else {
            var btns = jQuery(this).find('.btn-primary, .btn-danger');
            if(btns.length>0){
                btns[0].focus();
            } else {
                btns = jQuery(this).find('.btn');
                if(btns.length>0){
                    btns[0].focus();
                } else {
                    btns = jQuery(this).find('button');
                    if(btns.length>0){
                        btns[0].focus();
                    }
                }
            }
        }
    });
}

function performSaveAction() {
    var btns = jQuery('.btn-save');
    if(btns.length>0) {
        btns[0].click();
    }
}

function initSaveKeyListener() {
    var code;
    if(document.all){
        document.onkeyup = function() {
            if (window.event.ctrlKey) {
                if (window.event.keyCode == 83) {
                    performSaveAction();
                    return false;
                }
            }
        };
    } else {
        document.onkeydown = document.onkeypress = function (evt) {
            // check voor ctrl-s key
            if (evt.ctrlKey) {
                if (evt.keyCode)
                    code = evt.keyCode;
                else if (evt.which)
                    code = evt.which;
                if (code=="117" || code == "83") {
                    performSaveAction();
                    return false;
                }
            }
        };
    }
}

/**
 * Logic for showing nested forms in pageparts.
 */
function PagepartSubForm(collectionHolder, addButtonLi, removeButtonHtml, allowAdd, allowDelete, min, max, sortable) {

    this.collectionHolder = collectionHolder;
    this.addButtonLi = addButtonLi;
    this.removeButtonHtml = removeButtonHtml;
    this.allowAdd = allowAdd;
    this.allowDelete = allowDelete;
    this.min = min;
    this.max = max;
    this.sortable = sortable;

    this.init = function() {
        var self = this;

        // Count the current entity form we already have
        var nrEntityForms = this.getNumberOfFormEntities();

        // Use that as the new index when inserting a new entity form
        this.collectionHolder.data('index', nrEntityForms);

        // Add the "add new" button and li to the ul
        this.collectionHolder.append(this.addButtonLi);

        // For each of the entity forms add a delete button
        this.collectionHolder.find('.nested_form_item').each(function() {
            self.addEntityFormDeleteButton($(this));
        });

        // Make sure we have at least as many entity forms than minimally required
        if (this.min > 0 && this.min > nrEntityForms) {
            var newNeeded = this.min - nrEntityForms;
            for (var i=0; i<newNeeded; i++) {
                this.addNewEntityForm();
            }
        }

        // Add listerners on add button
        this.addButtonLi.find('.add-btn').on('click', function(e) {
            // Prevent the link from creating a "#" on the URL
            e.preventDefault();

            // Add a new entity form
            self.addNewEntityForm();
        });

        // Check that we need to show/hide the add/delete buttons
        this.recalculateShowAddDeleteButtons();
    };

    this.getNumberOfFormEntities = function() {
        return this.collectionHolder.find('li.nested_form_item').length;
    };

    this.recalculateShowAddDeleteButtons = function() {
        var nrEntityForms = this.getNumberOfFormEntities();

        if (this.allowAdd && (this.max === false || nrEntityForms < this.max)) {
            this.collectionHolder.find('li .add-btn').show();
        } else {
            this.collectionHolder.find('li .add-btn').hide();
        }

        if (this.allowDelete && nrEntityForms > this.min) {
            this.collectionHolder.find('li .del-btn').show();
        } else {
            this.collectionHolder.find('li .del-btn').hide();
        }
    };

    this.addNewEntityForm = function() {
        // Get the data-prototype
        var prototype = this.collectionHolder.data('prototype');

        // Get the new index
        var index = this.collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to a number based on how many entity forms we have
        var newForm = prototype.replace(/__name__/g, index);

        // Increase the index with one for the next item
        this.collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add new" link li
        var newLiMarkup = '<li class="nested_form_item"';
        // If sortable add extra attribute
        if (this.sortable) {
            newLiMarkup = newLiMarkup + ' data-sortkey="' +
                this.collectionHolder.data('sortkey').replace(/__name__/g, index) + '"';
        }
        newLiMarkup = newLiMarkup + '>';
        if (this.sortable) {
            newLiMarkup = newLiMarkup + '<div class="prop_bar"><i class="icon-move"></i></div>';
        }
        newLiMarkup = newLiMarkup + '</li>';
        var $newFormLi = $(newLiMarkup).append(newForm);

        this.addButtonLi.before($newFormLi);

        // Add a delete button
        this.addEntityFormDeleteButton($newFormLi);

        // Check that we need to show/hide the add/delete buttons
        this.recalculateShowAddDeleteButtons();

        // Reorder if sortable
        if (this.sortable) {
            this.collectionHolder.trigger('sortupdate');
        }

        // Quickfix to trigger CK editors on sub-entities - there should be a better way to do this...
        if (typeof enableCKEditors !== "undefined") {
            disableCKEditors();
            enableCKEditors();
        }
    }

    this.addEntityFormDeleteButton = function($entityFormLi) {
        var self = this;
        var $removeLink = $(this.removeButtonHtml);

        if(sortable) {
            $entityFormLi.find('.prop_bar .actions').append($removeLink);
        } else {
            $entityFormLi.prepend($removeLink);
        }

        $removeLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            var delKey = $entityFormLi.attr("delkey");
            if (typeof delKey === "undefined") {
                // We don't need to do anything, the entity was not yet saved in the database
            } else {
                var form = $entityFormLi.parents('form:first');
                $("<input type='hidden' name='" + delKey + "' value='1' />").appendTo(form);
            }

            // remove the li for the tag form
            $entityFormLi.remove();

            // Check that we need to show/hide the add/delete buttons
            self.recalculateShowAddDeleteButtons();

            // Reorder if sortable
            if (self.sortable) {
                self.collectionHolder.trigger('sortupdate');
            }
        });
    }
}
