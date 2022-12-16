function getSiteMap(route) {
    $.post(Routing.generate('site_map'), {
            'route': route
        }, function (d) {
            $('#site-map').html(d.content).hide().slideDown();
        }, 'json'
    );
}
let linkInput = $('#site_map_template_form_link');

linkInput.on('change', function() {
    getSiteMap($(this).val());
});

getSiteMap(linkInput.val());
