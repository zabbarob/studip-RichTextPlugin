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
(function($) { // prevent global namespace pollution
    var getScriptDir = function() {
        // get URL path of currently executed script, without filename
        // http://stackoverflow.com/a/2161748
        var scripts = document.getElementsByTagName('script');
        var path = scripts[scripts.length - 1].src.split('?')[0]; // remove ?query
        return path.split('/').slice(0, -1).join('/') + '/'; // remove filename
    };

    // needs to be executed when script is loaded to get the script's own dir
    window.richTextPlugin = {
        dir: getScriptDir(),
    }
}(jQuery));
