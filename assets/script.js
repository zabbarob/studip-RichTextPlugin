(function ($) {
    $(function() {
        var editor = new wysihtml5.Editor("textarea", {
            toolbar:      "toolbar",
            parserRules:  wysihtml5ParserRules
        });

        var log = document.getElementById("log");

        editor
            .on("load", function() {
                log.innerHTML += "<div>load</div>";
            })
            .on("focus", function() {
                log.innerHTML += "<div>focus</div>";
            })
            .on("blur", function() {
                log.innerHTML += "<div>blur</div>";
            })
            .on("change", function() {
                log.innerHTML += "<div>change</div>";
            })
            .on("paste", function() {
                log.innerHTML += "<div>paste</div>";
            })
            .on("newword:composer", function() {
                log.innerHTML += "<div>newword:composer</div>";
            })
            .on("undo:composer", function() {
                log.innerHTML += "<div>undo:composer</div>";
            })
            .on("redo:composer", function() {
                log.innerHTML += "<div>redo:composer</div>";
            });
    });
}(jQuery));
