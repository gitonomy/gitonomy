$(document).ready(function()
{
    $('#add-projectuserrole').click(function() {
        var wrapper   = $('#adminuser_projectUserRoles');
        var prototype = wrapper.attr('data-prototype');
        var nbOjects  = $("#adminuser_globalUserRoles > div").length;
        prototype     = prototype.replace(/\$\$name\$\$/g, nbOjects++);

        wrapper.append(prototype);
    });

    $('.discard').live('click', function() {
        $(this).parents('div.clearfix:first').remove();
    });
});

