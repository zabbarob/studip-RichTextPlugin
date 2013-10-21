CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    init: function(editor){
        var isImage = function(mime_type){
                return (typeof mime_type) === 'string' && mime_type.match('^image');
            },
            isSVG = function(mime_type){
                return (typeof mime_type) === 'string' && mime_type === 'image/svg+xml';
            },
            insertNode = function($node){
                editor.insertHtml($('<div>').append($node).html());
            },
            insertImage = function(file){
                insertNode($('<img />').attr({
                    src: file.url,
                    alt: file.name,
                    title: file.name
                }));
            },
            insertLink = function(file){
                insertNode($('<a>').attr({
                    href: file.url,
                    type: file.type,
                    target: '_blank',
                    rel: 'nofollow'
                }).append(file.name));
            },
            insertFile = function(file){
                // NOTE StudIP sends SVGs as application/octet-stream
                if (isImage(file.type) && !isSVG(file.type)) {
                    insertImage(file);
                } else {
                    insertLink(file);
                }
            },
            handleUploads = function(fileList){
                if (!fileList) {
                    alert('Das Hochladen der Datei(en) ist fehlgeschlagen.');
                    return;
                }

                var errors = [];
                $.each(fileList, function(index, file){
                    if (file.error) {
                        errors.push(file.name + ': ' + file.error);
                    } else {
                        insertFile(file);
                    }
                });
                if (errors.length) {
                    var message = 'Es konnten nicht alle Dateien'
                        + ' hochgeladen werden.\n\n';
                    alert(message + errors.join('\n'));
                }
            };

        // handle drag'n'drop and display drop zone to user
        $('#fileupload').fileupload({
            url: editor.config.studipUpload_url,
            singleFileUploads: false,
            autoUpload: true,
            dataType: 'json',
            done: function(e, data){
                handleUploads(data.result.files);
            }
        });

        var textarea = $('#richtext-editor');
        var editorArea = textarea.siblings('#cke_richtext-editor');
        editorArea.bind("drop", function (e) {
            e.preventDefault();
            editorArea.removeClass('drag');
            var list = $.makeArray(e.originalEvent.dataTransfer.files);
            $('#fileupload').fileupload('add', { files: list });
            return false;
        });


        editor.addCommand('upload', { // command for uploading files
            exec: function(editor) {
                var input = $('<input type="file" name="files[]" multiple />')
                        .css('display', 'none')
                        .appendTo(document.body);

                input.fileupload({
                    url: editor.config.studipUpload_url,
                    singleFileUploads: false,
                    dataType: 'json',
                    done: function(e, data) {
                        handleUploads(data.result.files);
                        input.remove();
                    }
                });
                input.click();
            }
        });

        editor.ui.addButton('upload', { // add toolbar button
            label: 'Datei hochladen',
            command: 'upload',
            toolbar: 'insert,80'
        });
    }
});
