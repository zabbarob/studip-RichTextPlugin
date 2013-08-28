CKEDITOR.plugins.add('studip-wiki', {
    icons: 'wikilink',
    init: function (editor) {
        // add toolbar button and dialog for editing Stud.IP wiki links
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

        // add context menu for existing Stud.IP wiki links
        if (editor.contextMenu) {
            editor.addMenuGroup('studipGroup');
            editor.addMenuItem('wikilinkItem', {
                label: 'Edit Stud.IP Wiki Link',
                icon: this.path + 'icons/wikilink.png', // same as plugin icon
                command: 'wikiDialog',
                group: 'studipGroup'
            });
            editor.contextMenu.addListener(function(element) {
                if (element.getAscendant('a', true)) {
                    return {
                        wikilinkItem: CKEDITOR.TRISTATE_OFF
                    };
                }
            });
        }
    }
});
