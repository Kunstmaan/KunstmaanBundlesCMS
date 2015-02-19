var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.nestedForm = (function(window, undefined) {

    var init;

    init = function() {

    };

    return {
        init: init
    };

}(window));


// OLD
// $(document).ready(function () {
//     var collectionHolder = $('ul.ul_{{ form.vars.id }}');
//     var $addButtonLi = $('<li><a href="#" class="btn btn-mini add-btn"><i class="icon-plus-sign"></i> add new</a></li>');
//     {% if sortable %}
//         var removeButtonHtml = '<a href="#" class="del red"><i class="icon-trash"></i></a>';
//     {% else %}
//         var removeButtonHtml = '<a href="#" class="btn btn-mini del-btn"><i class="icon-trash"></i> delete</a>';
//     {% endif %}

//     var allow_add = {{ form.vars.allow_add|default('0') }};
//     var allow_delete = {{ form.vars.allow_delete|default('0') }};
//     var min = {{ attr['nested_form_min']|default('0') }};
//     var max = {{ attr['nested_form_max']|default('false') }};
//     var sortable = {{ attr['nested_sortable']|default('false') }};

//     new PagepartSubForm(collectionHolder, $addButtonLi, removeButtonHtml, allow_add, allow_delete, min, max, sortable).init();

//     if (sortable) {
//         collectionHolder.sortable({
//             handle: '.prop_bar',
//             cursor: 'move',
//             placeholder: "placeholder",
//             forcePlaceholderSize: true,
//             tolerance:"pointer",
//             revert: 100
//         }).bind('sortupdate', function() {
//             // Adapt weight field
//             var weight = 0;
//             $('.ul_{{ form.vars.id }} li').each(function() {
//                 var fieldId = $(this).data('sortkey');
//                 if (fieldId === undefined) {
//                     return;
//                 }
//                 $('#' + fieldId).val(weight++);
//             });
//         });
//     }
// });

// function PagepartSubForm(collectionHolder, addButtonLi, removeButtonHtml, allowAdd, allowDelete, min, max, sortable) {

//     this.collectionHolder = collectionHolder;
//     this.addButtonLi = addButtonLi;
//     this.removeButtonHtml = removeButtonHtml;
//     this.allowAdd = allowAdd;
//     this.allowDelete = allowDelete;
//     this.min = min;
//     this.max = max;
//     this.sortable = sortable;

//     this.init = function() {
//         var self = this;

//         // Count the current entity form we already have
//         var nrEntityForms = this.getNumberOfFormEntities();

//         // Use that as the new index when inserting a new entity form
//         this.collectionHolder.data('index', nrEntityForms);

//         // Add the "add new" button and li to the ul
//         this.collectionHolder.append(this.addButtonLi);

//         // For each of the entity forms add a delete button
//         this.collectionHolder.find('.nested_form_item').each(function() {
//             self.addEntityFormDeleteButton($(this));
//         });

//         // Make sure we have at least as many entity forms than minimally required
//         if (this.min > 0 && this.min > nrEntityForms) {
//             var newNeeded = this.min - nrEntityForms;
//             for (var i=0; i<newNeeded; i++) {
//                 this.addNewEntityForm();
//             }
//         }

//         // Add listerners on add button
//         this.addButtonLi.find('.add-btn').on('click', function(e) {
//             // Prevent the link from creating a "#" on the URL
//             e.preventDefault();

//             // Add a new entity form
//             self.addNewEntityForm();
//         });

//         // Check that we need to show/hide the add/delete buttons
//         this.recalculateShowAddDeleteButtons();
//     };

//     this.getNumberOfFormEntities = function() {
//         return this.collectionHolder.find('li.nested_form_item').length;
//     };

//     this.recalculateShowAddDeleteButtons = function() {
//         var nrEntityForms = this.getNumberOfFormEntities();

//         if (this.allowAdd && (this.max === false || nrEntityForms < this.max)) {
//             this.collectionHolder.find('li .add-btn').show();
//         } else {
//             this.collectionHolder.find('li .add-btn').hide();
//         }

//         if (this.allowDelete && nrEntityForms > this.min) {
//             this.collectionHolder.find('li .del-btn').show();
//         } else {
//             this.collectionHolder.find('li .del-btn').hide();
//         }
//     };

//     this.addNewEntityForm = function() {
//         // Get the data-prototype
//         var prototype = this.collectionHolder.data('prototype');

//         // Get the new index
//         var index = this.collectionHolder.data('index');

//         // Replace '__name__' in the prototype's HTML to a number based on how many entity forms we have
//         var newForm = prototype.replace(/__name__/g, index);

//         // Increase the index with one for the next item
//         this.collectionHolder.data('index', index + 1);

//         // Display the form in the page in an li, before the "Add new" link li
//         var newLiMarkup = '<li class="nested_form_item"';
//         // If sortable add extra attribute
//         if (this.sortable) {
//             newLiMarkup = newLiMarkup + ' data-sortkey="' +
//                 this.collectionHolder.data('sortkey').replace(/__name__/g, index) + '"';
//         }
//         newLiMarkup = newLiMarkup + '>';
//         if (this.sortable) {
//             newLiMarkup = newLiMarkup + '<div class="prop_bar"><i class="icon-move"></i></div>';
//         }
//         newLiMarkup = newLiMarkup + '</li>';
//         var $newFormLi = $(newLiMarkup).append(newForm);

//         this.addButtonLi.before($newFormLi);

//         // Add a delete button
//         this.addEntityFormDeleteButton($newFormLi);

//         // Check that we need to show/hide the add/delete buttons
//         this.recalculateShowAddDeleteButtons();

//         // Reorder if sortable
//         if (this.sortable) {
//             this.collectionHolder.trigger('sortupdate');
//         }

//         // Quickfix to trigger CK editors on sub-entities - there should be a better way to do this...
//         if (typeof enableCKEditors !== "undefined") {
//             disableCKEditors();
//             enableCKEditors();
//         }
//     }

//     this.addEntityFormDeleteButton = function($entityFormLi) {
//         var self = this;
//         var $removeLink = $(this.removeButtonHtml);

//         if(sortable) {
//             $entityFormLi.find('.prop_bar .actions').append($removeLink);
//         } else {
//             $entityFormLi.prepend($removeLink);
//         }

//         $removeLink.on('click', function(e) {
//             // prevent the link from creating a "#" on the URL
//             e.preventDefault();

//             var delKey = $entityFormLi.attr("delkey");
//             if (typeof delKey === "undefined") {
//                 // We don't need to do anything, the entity was not yet saved in the database
//             } else {
//                 var form = $entityFormLi.parents('form:first');
//                 $("<input type='hidden' name='" + delKey + "' value='1' />").appendTo(form);
//             }

//             // remove the li for the tag form
//             $entityFormLi.remove();

//             // Check that we need to show/hide the add/delete buttons
//             self.recalculateShowAddDeleteButtons();

//             // Reorder if sortable
//             if (self.sortable) {
//                 self.collectionHolder.trigger('sortupdate');
//             }
//         });
//     }
// }
