$(document).ready(function () {
    var sortLst = $('#sortable');

    // set input value
    function setInputValue () {
        var arr = sortLst.find('li').map(function () {
            return $(this).attr('id');
        }).get();
        $('[name="sort_order"]').val(arr.join(','));
    }

    // change chosen value listener
    $('.sort-selected').on('change', function () {
        var chosenArray = $(this).val();
        // delete removed items
        sortLst.find('li').each(function () {
            var id = $(this).attr('id');
            if (!chosenArray || chosenArray.indexOf(id) === -1) {
                $(this).remove();
            }
        });
        // add new items
        if (chosenArray) {
            chosenArray.map(function (i) {
                if ($('#' + i).length === 0) {
                    var name = $('.sort-selected option[value="' + i + '"]').text();
                    sortLst.append('<li id="' + i + '">' + name + '</li>');
                }
            });
        }
        setInputValue();
    });

    // move sortable elements listener
    sortLst.sortable({
        stop: function() {
            setInputValue();
        }
    });
});
