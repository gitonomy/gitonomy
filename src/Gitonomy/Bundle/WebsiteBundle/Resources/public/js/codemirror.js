$(document).ready(function () {
    var loadCodeMirror = function (textarea) {
        var $textarea = $(textarea);

        CodeMirror.modeURL = $textarea.attr('data-mode-url');
        var editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            readOnly: true,
            lineNumberFormatter: function(ln) {
                return '<a name="L'+ ln +'"></a><a href="#L'+ ln +'">'+ ln +'</a>';
            }
        });

        var mode = $textarea.attr('data-mode');
        editor.setOption("mode", mode);
        CodeMirror.autoLoadMode(editor, mode);
    }

    $("textarea.CodeMirror").each(function (i, e) {
        loadCodeMirror(e);
    })
});
