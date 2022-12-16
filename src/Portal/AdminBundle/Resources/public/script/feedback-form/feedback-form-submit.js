// $(document).ready(function () {
//     $('form[name="admin_feedbackform"]').on('submit', function (e) {
//         e.preventDefault();
//         var route = $('#btn_edit').data('formAction') == 'edit' ?
//             Routing.generate('admin_instance_feedback_form_edit', { instanceCode:$('#instanceCode').val(),
//                 id:$('#btn_edit').data('formId') }) : Routing.generate('admin_instance_feedback_form_create');
//         console.log(route);
//         var sort = '';
//         $('#sortable li').each(function () {
//             sort += '&sort%5B%5D=' + (this.id);
//         });
//         $.post(route,
//             $(this).serialize()+sort,
//             function (response) {
//                 if (response.status === true) {
//                     $(location).attr('href', response.urlredirect);
//                 } else {
//                     $('form[name="admin_feedbackform"]').html(response.content);
//                     $('body').find('select').chosen();
//                     setTimeout(function () {
//                         startForm();
//                     }, 100)
//                 }
//             }, 'json'
//         )
//     });
// });
