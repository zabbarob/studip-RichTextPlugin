CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    init: function (editor) {
        var input = $('<input type="file" name="files" data-url="'
                    + $('#post_files_url').val() + '" multiple />')
                .css('display', 'none')
                .appendTo(document.body);

        input.fileupload({
            dataType: 'json',
            done: function(e, data) {
                json = data.result;
                if (typeof json.inserts === 'object') {
                    $.each(json.inserts, function(index, file) {
                        // NOTE StudIP sends SVGs as application/octet-stream
                        console.log(file);
                    });
                }
                if (typeof json.errors === 'object') {
                    var message = 'Es konnten nicht alle Dateien'
                        + ' hochgeladen werden.\n\n';
                    alert(message + json.errors.join('\n'));
                } else if (typeof json.inserts !== 'object') {
                    alert('Das Hochladen der Datei(en) ist fehlgeschlagen.');
                }

                /*$.each(data.result.files, function(index, file) {
                    console.log('each file');
                    console.log(file.name);
                    //$('<p/>').text(file.name).appendTo(document.body);
                });*/
            }
        });

        editor.addCommand('upload', { // command for uploading files
            exec: function(editor) {
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
