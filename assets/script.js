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
    // make sure code is only called after DOM structure is fully loaded
    $(function() {
        var editor = new wysihtml5.Editor("wysihtml5-editor", {
            toolbar:     "wysihtml5-editor-toolbar",
            stylesheets: ["http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css", "/studip/plugins_packages/virtUOS/RichTextPlugin/assets/editor.css"],
            parserRules: wysihtml5ParserRules
        });
    });
}(jQuery));
