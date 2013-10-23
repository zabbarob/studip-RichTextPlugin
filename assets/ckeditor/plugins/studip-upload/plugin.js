CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    init: function(editor){
        // utilities
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
                console.log('handleuploads');
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

        // actual file upload handler
        // NOTE depends on jQuery File Upload plugin being loaded beforehand!
        // TODO integrate jQuery File Upload plugin into studip-upload
        $('<input id="fileupload" type="file" name="files[]" multiple style="display: none" />').appendTo(document.body);

        var input = $('#fileupload');
        input.fileupload({
            url: editor.config.studipUpload_url,
            singleFileUploads: false,
            dataType: 'json',
            done: function(e, data){
                console.log('upload done');
                handleUploads(data.result.files);
            }
        });

        // drag'n'drop handler
        var textarea = $('#richtext-editor'),
            editorArea = textarea.siblings('#cke_richtext-editor');

        editorArea.bind('drop', function(event){
            event.preventDefault();
            input.fileupload('add', {
                files: $.makeArray(event.originalEvent.dataTransfer.files)
            });
            return false;
        });

        // ckeditor
        editor.addCommand('upload', {    // command handler
            exec: function(editor){
                // NOTE if input variable is used instead selector upload works
                // only the first time
                $('#fileupload').click();
            }
        });
        editor.ui.addButton('upload', {  // toolbar button
            label: 'Datei hochladen',
            command: 'upload',
            toolbar: 'insert,80'
        });
    }
});
