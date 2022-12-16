$(document).ready(function () {
    var $collectionQuestionHolder = $('.questions');
    var addAnswer=function ($question) {
        var prototypeAnswer = $collectionQuestionHolder.data('prototype-answer');
        var index;
        var indexQuestion = $question.data('questionIndex');
        var $answerForms = $question.find('.answer-form');
        if ($answerForms.length) {
            index = $answerForms.eq($answerForms.length - 1).data('answerIndex') + 1;
        } else {
            index = 0;
        }
        var newFormAnswer = prototypeAnswer
            .replace(/__prot_answer__/g, index)
            .replace(/__name__/g, indexQuestion);
        var $newFormAnswerDiv = $('<div class="answer-form" data-answer-index="' + index + '"></div>').append(newFormAnswer);
        if (index >= Number($('#num_min_answers').data('numMinAnswers'))) {
            $newFormAnswerDiv.append('<button type="button" class="remove-answer btn btn-warning btn-xs">' +
                Translator.trans('interview_answer_form.btn_remove_answer', {}, 'messages') + '</button>');
        }
        $question.find('.add_answer_link').before($newFormAnswerDiv);
    };
    $('body').on('click', '.add_question_link', function () {
        var isCreate = $('#form-body').data('action') === 'create' ? true : false;
        var prototype = $collectionQuestionHolder.data('prototype');
        var index;
        var $questionForms = $('.question-form');
        if ($questionForms.length) {
            index = $questionForms.eq($questionForms.length - 1).data('questionIndex') + 1;

        } else {
            index = 0;
        }
        var newForm = prototype.replace(/__name__/g, index);
        var $newFormDiv = $('<div class="question-form well row" data-question-index="' + index + '"></div>')
            .append(newForm)
            .append('<div class="answers"></div>');
        $collectionQuestionHolder.append($newFormDiv);
        if (isCreate) {
                $newFormDiv.find('.question-dependent-checked').addClass('hidden-question-dependent-checked');
                $newFormDiv.find('.question-dependent-label').addClass('hidden-question-dependent-checked');
        }
        $newFormDiv.find('select').chosen();
        CKEDITOR.replace($newFormDiv.find('textarea')[0]);
        addAnswer($newFormDiv);
        addAnswer($newFormDiv);
    });
    $('body').on('click', '.remove-question', function() {
        $(this).closest('.question-form').remove();
    });
    $('body').on('click', '.remove-answer', function() {
        $(this).closest('.answer-form').remove();
    });
    $('body').on('click', '.add_answer_link', function (){
        addAnswer($(this).closest('.question-form'));
    });
    $('body').on('change', '.select-question-type', function (){
        if (Number($(this).val()) === Number($('#const_text_type_select').data('textType'))) {
            $(this).closest('.question-form').find('.answer-form').remove();
            $(this).closest('.question-form').find('.add_answer_link').attr('disabled', 'disabled');
            $(this).closest('.question-form').find('.question-required-checked').attr('disabled', 'disabled').removeAttr('checked');
        } else {
            if ($(this).closest('.question-form').find('.answer-form').length < 2) {
                $(this).closest('.question-form').find('.add_answer_link').removeAttr('disabled');
                $(this).closest('.question-form').find('.question-required-checked').removeAttr('disabled');
                addAnswer($(this).closest('.question-form'));
                addAnswer($(this).closest('.question-form'));
            }
        }
    });
});
