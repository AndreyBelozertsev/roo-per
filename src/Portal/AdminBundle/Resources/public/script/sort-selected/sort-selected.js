var sortArray = [];
var esiaFields;
var isCheckbox;
var selectedEsiaFields;

function createVals() {
    $('.sort-selected :not(option:selected)').each(function () {
        $('#sortable li[value="' + this.value + '"]').remove();
        if ($.inArray(this.value, sortArray) !== -1) {
            sortArray.splice(sortArray.indexOf(this.value), 1);
        }
    });
    $('.sort-selected option:selected').each(function () {
        if ($.inArray(this.value, sortArray) === -1) {
            isCheckbox = '';
                if ($.inArray(this.value, esiaFields) !== -1 && $('#admin_feedbackform_isRegisteredUser').is(':checked')) {
                isCheckbox = ' <input type="checkbox" name="checkEsia['+ this.value +']" class="sort-checkbox" checked="checked"> ';
            }
            $('#sortable').append('<li id="' + this.value + '" value="' + this.value +
                '">' + this.text +
                isCheckbox +
                '</li>'
            );
            sortArray.push(this.value);
        }
    });
    changeSort();
}

function createNotEmptySorted() {
    var fieldList = '';
    $('#sortable li').each(function () {
        isCheckbox = '';
        var isCecked = '';
        if ($.inArray(this.id, selectedEsiaFields) !== -1) {
            isCecked = ' checked="checked" ';
        }
        if ($.inArray(this.id, esiaFields) !== -1 && $('#admin_feedbackform_isRegisteredUser').is(':checked')) {
            isCheckbox = ' <input type="checkbox" name="checkEsia['+ this.id +']"  class="sort-checkbox" ' + isCecked + ' > ';
        }
        fieldList += '<li id="' + this.id + '" value="' + this.id + '">' +
            $('.sort-selected option[value="' + this.id + '"]').text() +
            isCheckbox +
        '</li>';
        if ($.inArray(this.id, sortArray) === -1) {
            sortArray.push(this.id);
        }
        $(this).remove();
    });
    $('#sortable').html(fieldList);
    createVals();
}

function startForm(){
    $('.sort-selected').chosen();
    esiaFields = $('#fields_esia_list').data('fieldsEsia').split(',');
    selectedEsiaFields = $('#fields_esia_selected_list').data('fieldsSelectedEsia').split(',');
    createNotEmptySorted();
    $(function () {
        $('#sortable').sortable({
            axis: 'y',
            stop: function( event, ui ) { changeSort() }
        }).disableSelection();
    });
    $('.sort-selected').change(createVals);
}

function changeSort() {
    var sort = [];
    var checkEsiaField = [];
    $('#sortable li').each(function () {
        if (this.id) {
            sort.push(this.id);
            if ($('input[name="checkEsia[' + this.id + ']"]').is(':checked')) {
                checkEsiaField.push(this.id)
            }
        }
    });
    if (sort.length === 0) {
        $('#selectedError').removeClass('selected-no-error');
        $('#btn_edit').attr('disabled', 'disabled');
    } else {
        $('#selectedError').addClass('selected-no-error');
        $('#btn_edit').removeAttr('disabled');
    }
    $('#esia_fields').val(checkEsiaField);
    $('#sort').val(sort);
}

$(document).ready(function () {
    startForm();
    $('#admin_feedbackform_isRegisteredUser').click(function() {
        createNotEmptySorted();
    });
    $('body').on('click', '.sort-checkbox', function () {
        changeSort();
    })
});
