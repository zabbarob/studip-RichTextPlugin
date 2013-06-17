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

    // drop files
    var isImage = function(mime_type) {
        return (typeof mime_type) === 'string' && mime_type.match('^image');
    };
    var isSVG = function(mime_type) {
        return (typeof mime_type) === 'string' && mime_type === 'image/svg+xml';
    };
    var ignoreEvent = function(e) {
        e.preventDefault();
        e.stopPropagation();
    };

    var getDropHandler = function(callBack) {
        return function(event) {
            var that = this;
            ignoreEvent(event);

            var files = 0;
            var file_info = event.originalEvent.dataTransfer.files || {};
            var data = new FormData();

            $.each(file_info, function (index, file) {
                if (file.size > 0) {
                    data.append(index, file);
                    files += 1;
                }
            });

            if (files <= 0) {
                return;
            }

            // post dropped files to server
            callBack.startUpload();

            $.ajax({
                'url': $('#post_files_url').val(), // must be set in edit template
                'data': data,
                'cache': false,
                'contentType': false,
                'processData': false,
                'type': 'POST',
                'xhr': function() {
                    var xhr = $.ajaxSettings.xhr();
                    // workaround for FF<4
                    // https://github.com/francois2metz/html5-formdata
                    if (data.fake) {
                        xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + data.boundary);
                        xhr.send = xhr.sendAsBinary;
                    }
                    return xhr;
                },
                'success': function(json) {
                    if (typeof json.inserts === 'object') {
                        $.each(json.inserts, function(index, file) {
                            // NOTE StudIP sends SVGs as application/octet-stream
                            if (isImage(file.type) && !isSVG(file.type)) {
                                callBack.insertImage(that, file);
                            } else {
                                callBack.insertLink(that, file);
                            }
                        });
                    }
                    if (typeof json.errors === 'object') {
                        alert(json.errors.join('\n'));
                    } else if (typeof json.inserts !== 'object') {
                        alert('Das Hochladen der Datei(en) ist fehlgeschlagen.');
                    }
                },
                'complete': function() {
                    callBack.stopUpload();
                }
            }); // $.ajax
        }; // dropHandler
    }; // getDropHandler

    // needs to be executed when script is loaded to get the script's own dir
    window.richTextPlugin = {
        dir: getScriptDir(),
        getDropHandler: getDropHandler
    }

    // make sure code is only called after DOM structure is fully loaded
    $(function() {
        // give user the option to undo clicking 'cancel' button
        $('input[name="cancel"]').click(function(e){
            // TODO internationalize warning message
            var warning = 'If you select "OK" your edits will not be saved! Select "Cancel" to continue editing.';
            if (!confirm(warning)) {
                e.preventDefault();
            }
        });
    }); // $(function() {
}(jQuery));

