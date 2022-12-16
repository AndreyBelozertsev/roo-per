
$(document).ready(function(){
//    $(".alert").addClass("in").fadeOut(15500);

    /* swap open/close side menu icons */
    $('[data-toggle=collapse]').click(function(){
  	// toggle icon
  	$(this).find("i").toggleClass("fa-angle-down fa-angle-up");
    });
});