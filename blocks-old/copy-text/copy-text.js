$(document).ready(function () {
    function addLink() {
        var body = document.getElementsByTagName('body')[0];
        var selection = window.getSelection();
        var pageLink = $(body).data('copy-message') +
            '<a href="' + document.location.href + '">' + document.location.href + '</a>';
        var newDiv = document.createElement('div');
        newDiv.style.position = 'absolute';
        newDiv.style.left = '-99999px';
        body.appendChild(newDiv);
        newDiv.innerHTML = selection + pageLink;
        selection.selectAllChildren(newDiv);
        window.setTimeout(function () {
            body.removeChild(newDiv);
        }, 0);
    }

    document.oncopy = addLink;
});
