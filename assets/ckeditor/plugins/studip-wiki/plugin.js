CKEDITOR.plugins.add('studip-wiki', {
    icons: 'wikilink',
    init: function (editor) {
        editor.addCommand('insertWikiLink', {
            exec: function(editor) {
                var now = new Date();
                editor.insertHtml('Date: ' + now.toString());
            }
        });
        editor.addCommand('wikiDialog', new CKEDITOR.dialogCommand('wikiDialog'));
        editor.ui.addButton('wikilink', {
                label: 'Insert Stud.IP Wiki link',
                command: 'wikiDialog',
                toolbar: 'insert'
        });
        CKEDITOR.dialog.add('wikiDialog', this.path + 'dialogs/wikilink.js' );
    }
});
