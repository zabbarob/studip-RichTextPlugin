CKEDITOR.plugins.add('studip-upload', {
    icons: 'upload',
    init: function (editor) {
        editor.addCommand('upload', { // command for uploading files
            exec: function(editor) {
                console.log('upload exec');
                var url = $('#post_files_url').val();
                var obj = '<input type="file" name="files[]" data-url="'
                            + url + '" multiple>';
                console.log(obj);
                var input = $(obj);

                console.log('fileupload');
                $('#fileupload').fileupload({
                    dataType: 'json',
                    done: function(e, data) {
                        console.log('done')
                        $.each(data.result.files, function(index, file) {
                            console.log('each file');
                            console.log(file.name);
                            //$('<p/>').text(file.name).appendTo(document.body);
                        });
                    }
                });
                $('#fileupload').click();
            }
        });
        editor.ui.addButton('upload', { // add toolbar button
            label: 'Datei hochladen',
            command: 'upload',
            toolbar: 'insert,80'
        });
    }
});
