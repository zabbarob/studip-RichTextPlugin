/**
 * script.js - JavaScript code for RichText plugin.
 *
 * This code is needed for initializing the wysihtml5 editor component.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Robert Costa <zabbarob@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
(function ($) {
    var getScriptDir = function() {
        // get URL path of currently executed script, without filename
        // http://stackoverflow.com/a/2161748
        var scripts = document.getElementsByTagName('script');
        var path = scripts[scripts.length - 1].src.split('?')[0]; // remove ?query
        return path.split('/').slice(0, -1).join('/') + '/'; // remove filename
    };

    // needs to be executed when script is loaded to get the script's own dir
    var dir = getScriptDir();

    // make sure code is only called after DOM structure is fully loaded
    $(function() {
        // initialize and configure editor
        var editor = new wysihtml5.Editor("wysihtml5-editor", {
            toolbar:     "wysihtml5-editor-toolbar",
            stylesheets: [
                "http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css",
                dir + "editor.css"],
            parserRules: wysihtml5ParserRules
        });

        // give user the option to undo clicking 'cancel' button
        $('input[name="cancel"]').click(function(e){
            var warning = 'If you select "OK" your edits will not be saved! Select "Cancel" to continue editing.';
            if (!confirm(warning)) {
                e.preventDefault();
            }
        });
    });
}(jQuery));
