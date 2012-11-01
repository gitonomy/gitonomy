/**
 * Sample usage:
 *
 * <a href="/blog/48/delete" data-method="POST">delete this post</a>
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
$(document).ready(function () {
    $(document).find("a[data-method]").click(function (event) {
        var target = $(event.currentTarget);
        var method = target.attr('data-method');
        var action = target.attr('href');

        var form = $('<form/>', {
            style:  "display:none;",
            method: method,
            action: action,
        });

        form.appendTo(target);
        form.submit();

        return false;
    });
});
