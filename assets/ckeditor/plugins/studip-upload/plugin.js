CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    init: function (editor) {
        var handleUploads = function(fileList){
            if (!fileList) {
                alert('Das Hochladen der Datei(en) ist fehlgeschlagen.');
                return;
            }

            var errors = [];
            $.each(fileList, function(index, file){
                console.log(file);
                if (file.error) {
                    errors.push(file.name + ': ' + file.error);
                } else {
                    // NOTE StudIP sends SVGs as application/octet-stream
                }
            });
            if (errors.length) {
                var message = 'Es konnten nicht alle Dateien'
                    + ' hochgeladen werden.\n\n';
                alert(message + errors.join('\n'));
            }
        }

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
