$('#selecctall').on('click', function (event) {
    let arrData = {};
    arrData.instanceCode = $('input[name="instanceCode"]').val();
    arrData.filterSort = $('#filterSort').val();
    arrData.filterExtension = $('#filterExtension').val();
    arrData.filterType = $('#filterType').val();
    let $this = $(event.target);
    let route = $this.data('route');

    document.location.href = Routing.generate(route, arrData);
});
