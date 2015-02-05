var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.filter = (function($, window, undefined) {

    var init,
        _getElements, _calculateUniqueFilterId,
        clearAllFilters, createFilter;

    var $appFilter = $('#app__filter'),
        $clearAllFiltersBtn, $applyAllFiltersBtn,
        $addFirstFilterSelect, $addFilterBtn;


    init = function() {

        if($appFilter) {
            _getElements();

            $addFirstFilterSelect.on('change', function() {
                createFilter($(this), true);
            });

            $addFilterBtn.on('click', function() {
                createFilter($(this), false);
            });

            $clearAllFiltersBtn.on('click', function() {
                clearAllFilters();
            });
        }
    };



    _getElements = function() {
        $clearAllFiltersBtn = $('#clear-all-filters');
        $applyAllFiltersBtn = $('#apply-all-filters');

        $addFirstFilterSelect = $('#add-first-filter');
        $addFilterBtn = $('#add-filter');
    };



    createFilter = function($this, first) {
        console.log('start create filter');

        var uniqueid = _calculateUniqueFilterId();

        if(first) {
            var currentLine = $this.parents('.js-filter-line');
        }



        // var line = $(el).parent("li"),
        //     uniqueid = calculateUniqueFilterId(),
        //     newitem = $('<li>').attr('class', 'filterline').append($("#filterdummyline").html());

        // if(hide === true){
        //     line.addClass("hidden");
        // }

        // if(hide === true){
        //     line.after(newitem);
        // } else {
        //     line.before(newitem);
        // }

        // newitem.find(".uniquefilterid").val(uniqueid);
        // newitem.find(".filterdummy").val(line.find(".filterselect").val());
        // updateOptions(newitem.find(".filterdummy"), options);

        // if(hide === true){
        //     newitem.removeClass("hidden");
        //     line.find("select").val("");
        // } else {
        //     newitem.slideDown();
        // }

        // return false;
    };


    _calculateUniqueFilterId = function() {
        var result = 1;

        $(".js-unique-filter-id").each(function() {
            var value = parseInt($(this).val(), 10);

            if(result <= value){
                result = value + 1;
            }
        });

        return result;
    };



    clearAllFilters = function() {
        $('.app__filter__line').remove();

        $applyAllFiltersBtn.trigger('click');
    };



    return {
        init: init
    };

}(jQuery, window));


/* OLD


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



*/
