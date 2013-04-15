(function ($) {
    $(function() {
        var editor = new wysihtml5.Editor("wysihtml5-textarea", {
            toolbar:      "wysihtml5-toolbar",
            parserRules:  wysihtml5ParserRules
        });
    });
}(jQuery));
