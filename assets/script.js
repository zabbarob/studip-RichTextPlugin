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
    var dir = getScriptDir();

    // make sure code is only called after DOM structure is fully loaded
    $(function() {

        // initialize and configure editor
        var editor = new wysihtml5.Editor('wysihtml5-editor', {
            toolbar:     'wysihtml5-editor-toolbar',
            stylesheets: [
                'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css',
                dir + 'editor.css'],
            parserRules: wysihtml5ParserRules
        });

        // give user the option to undo clicking 'cancel' button
        $('input[name="cancel"]').click(function(e){
            // TODO internationalize warning message
            var warning = 'If you select "OK" your edits will not be saved! Select "Cancel" to continue editing.';
            if (!confirm(warning)) {
                e.preventDefault();
            }
        });

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

        var textarea = $('#wysihtml5-editor');
        //textarea = $('#dropbox');
        //textarea.on('dragover', ignoreEvent);
        //textarea.on('dragenter', ignoreEvent);
        
        editor.on('change_view', function() {
            var editor_body = $($(".wysihtml5-sandbox")[0].contentWindow.document.body);
            var html = editor_body.html();
            var tags = /<br>|<\/h\d>|<[ou]l>|<\/[ou]l>|<\/li>/gi;
            html = html.replace(tags, function(match) {
                return match + '\n';
            }).replace(/\n\n/g,'\n');
            editor_body.html(html);
        });

        var dropHandler = function(event) {
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
            textarea.addClass('uploading');

            var url = $('#post_files_url').val(); // must be set in view/edit template
            
            $.ajax({
                'url': url,
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

                            // console.log(file);

                            // NOTE StudIP sends SVGs as application/octet-stream
                            if (isImage(file.type) && !isSVG(file.type)) {

                                // console.log('insert image:');

                                editor.composer.commands.exec('insertImage', {
                                    src: file.url,
                                    alt: file.name,
                                    title: file.name
                                });

                                // NOTE workaround: if wysihtml is in "show HTML"
                                // mode then editor.*.exec('insertHTML') does not
                                // work
                                if (that == textarea[0]) {
                                    var html = $('<div>').append($('<img>', {
                                        src: file.url,
                                        alt: file.name,
                                        title: file.name
                                    })).html();
                                    textarea.val(textarea.val() + html);
                                }
                            } else {
                                var html = $('<div>').append($('<a>', {
                                    target: '_blank',
                                    rel: 'nofollow',
                                    text: file.name,
                                    type: file.type,
                                    href: file.url
                                })).html();

                                // console.log(html);

                                // NOTE workaround: if wysihtml is in "show HTML"
                                // mode then editor.*.exec('insertHTML') does not
                                // work
                                if (that == textarea[0]) {
                                    textarea.val(textarea.val() + html);
                                }

                                // insert link
                                editor.focus();
                                editor.composer.commands.exec('insertHTML', html);
                            }
                        });
                    }
                    if (typeof json.errors === 'object') {
                        alert(json.errors.join('\n'));
                    } else if (typeof json.inserts !== 'object') {
                        alert('Das Hochladen der Datei(en) ist fehlgeschlagen.');
                    }
                    textarea.trigger('keydown');
                },
                'complete': function() {
                    textarea.removeClass('hovered uploading');
                }
            }); // $.ajax
        }; // dropHandler

        //editor.on('paste', dropHandler); // doesn't work
        //editor.on('drop', dropHandler); // doesn't work
        textarea.on('drop', dropHandler);
        var editor_body = $(".wysihtml5-sandbox")[0].contentWindow.document.body;
        $(editor_body).on('drop', dropHandler);
    }); // $(function() {
}(jQuery));

