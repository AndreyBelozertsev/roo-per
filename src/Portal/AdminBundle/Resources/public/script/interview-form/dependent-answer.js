$(document).ready(function () {
    function getSelect(questionForm) {
        var selectAnswerList = '';
        selectAnswerList += '<select class="select-answer-dependent">';
        $('.question-form').each(function () {
            if ($(this)[0] !== questionForm[0]) {
                var questionContent = $(this).find('.question-content').val();
                if (questionContent) {
                    selectAnswerList += '<optgroup label="' + questionContent + '">';
                    $(this).find('.answer-form').each(function () {
                        var valueContent = $(this).find('.answer-content').val();
                        var answerId = $(this).closest('.answer-form').data('answerId');
                        var isSelected = questionForm.data('dependentAnswerId') === answerId ? 'selected' : '';
                        if (valueContent) {
                            selectAnswerList += '<option value="' + answerId +'" ' + isSelected + '>' + valueContent + '</option>';
                        }
                    });
                    selectAnswerList += '</optgroup>';
                }
            } else {
                return false;
            }
        });
        selectAnswerList += '</select>';
        return selectAnswerList;
    }
    $('body').on('change', '.question-dependent-checked', function (){
        var thisQuestionForm = $(this).closest('.question-form');
        if ($(this).is(':checked')) {
            var selectAnswerList = getSelect(thisQuestionForm);
            $(this).closest('.question-form').find('.question-dependent').html(selectAnswerList);
            $(this).closest('.question-form').find('.select-answer-dependent').chosen();
            $(this).closest('.question-form').find('.question-answer-dependent-id').val($(this).closest('.question-form').find('.select-answer-dependent').val());
        } else {
            $(this).closest('.question-form').find('.question-dependent').empty();
            $(this).closest('.question-form').find('.question-answer-dependent-id').val('');
        }
    });
    $('body').on('change', '.select-answer-dependent', function (){
       $(this).closest('.question-form').find('.question-answer-dependent-id').val($(this).val());
    });
    $('.question-form').each(function () {
        var checkbox = $(this).find('.question-dependent-checked');
        if ($(this).data('dependentAnswerId')) {
            if (!checkbox.is(':checked')) {
                checkbox.trigger('click');
            }
        }
    });
});
