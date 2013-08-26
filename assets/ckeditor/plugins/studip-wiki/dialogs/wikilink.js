CKEDITOR.dialog.add('wikiDialog', function (editor) {
    function getParameterByName(name) {
        // http://stackoverflow.com/a/901144/641481
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(
            results[1].replace(/\+/g, " "));
    }
    return {
        title: "Insert Stud.IP Wiki Link",
        minWidth: 400,
        minHeight: 200,
        contents: [{
            id: 'tab-link',
            label: "Stud.IP Wiki Link",
            elements: [{
                type: 'text',
                id: 'wikipage',
                label: "Wiki Page Name",
                validate: CKEDITOR.dialog.validate.notEmpty(
                    "Page name cannot be empty")
            }]
        }],
        onOk: function() {
            var link = editor.document.createElement('a');
            var wikipage = this.getValueOf('tab-link', 'wikipage');
            link.setText(wikipage);
            link.setAttribute('href', STUDIP.URLHelper.getURL('wiki.php', {
                cid: getParameterByName('cid'),
                keyword: wikipage
            }));
            editor.insertElement(link);
        }
    };
});
