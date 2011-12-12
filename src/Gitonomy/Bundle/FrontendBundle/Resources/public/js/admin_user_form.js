$(document).ready(function()
{
    // add new locale in item form
    $('#add-element').click(function() {
        var prototype       = $('#adminuser_userRoles').attr('data-prototype');
        var nbTranslations  = $("#adminuser_userRoles > div").length;

        // the level of the prototype is replaced by $$name$$
        // you might have have to change this to be coherent
        // with your row ordering.
        prototype = prototype.replace(/\$\$name\$\$/g, nbTranslations++);

        // Append the prototype to the DOM
        $('#adminuser_userRoles').append(prototype);
    });

    $('.discard').live('click', function() {
        $(this).parents('div.clearfix:first').remove();
    });
});

