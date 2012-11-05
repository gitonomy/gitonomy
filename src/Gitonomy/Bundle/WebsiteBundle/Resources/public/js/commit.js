$(document).ready(function () {

    var toggleChangeset = function(isOld, isNew, file)
    {
        if (isOld) {
            file.find(".commit-diff .old").show();
        } else {
            file.find(".commit-diff .old").hide();
        }

        if (isOld && !isNew) {
            file.find("a.show-old").attr('disabled', 'disabled');
        } else {
            file.find("a.show-old").removeAttr('disabled');
        }

        if (isNew) {
            file.find(".commit-diff .new").show();
            file.find("a.show-new").attr('disabled', 'disabled');
        } else {
            file.find(".commit-diff .new").hide();
            file.find("a.show-new").removeAttr('disabled');
        }

        if (isNew && !isOld) {
            file.find("a.show-new").attr('disabled', 'disabled');
        } else {
            file.find("a.show-new").removeAttr('disabled');
        }

        if (!isOld || !isNew) {
            file.find("a.show-diff").removeAttr('disabled');
        } else {
            file.find("a.show-diff").attr('disabled', 'disabled');
        }
    };



    $(".changeset .file").on('click', 'a.show-old', function (event) {
        event.preventDefault();
        toggleChangeset(true, false, $(event.target).parents(".file"));
    });
    $(".changeset .file").on('click', 'a.show-new', function (event) {
        event.preventDefault();
        toggleChangeset(false, true, $(event.target).parents(".file"));
    });
    $(".changeset .file").on('click', 'a.show-diff', function (event) {
        event.preventDefault();
        toggleChangeset(true, true, $(event.target).parents(".file"));
    });
});
