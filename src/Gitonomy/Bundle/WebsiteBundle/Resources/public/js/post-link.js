/**
 * Sample usage:
 *
 * <a href="/blog/48/delete" data-method="POST">delete this post</a>
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
$(document).ready(function () {
    $(document).on('click', 'a[data-confirm]', function (event) {
        event.stopImmediatePropagation();
        event.preventDefault();

        var target     = $(event.currentTarget);
        var oldContent = target.html();
        var oldConfirm = target.attr('data-confirm');

        target.html(oldConfirm);
        target.removeAttr('data-confirm');

        setTimeout(function () {
            target.attr('data-confirm', oldConfirm);
            target.html(oldContent);
        }, 5000);

        return false;
    });

    $(document).on('click', 'a[data-method]', function (event) {
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
