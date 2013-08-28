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
                    "Page name cannot be empty"),
                setup: function(link) {
                    this.setValue(link.getText());
                },
                commit: function(link) {
                    link.setText(this.getValue());
                    link.setAttribute('href',
                        STUDIP.URLHelper.getURL('wiki.php', {
                            cid: getParameterByName('cid'),
                            keyword: this.getValue()
                        })
                    );
                }
            }]
        }],
        onShow: function() {
            var element = editor.getSelection().getStartElement();
            if (element) {
                element = element.getAscendant('a', true);
            }

            this.insertMode = !element || element.getName() != 'a';
            if (this.insertMode) {
                element = editor.document.createElement('a');
            } else {
                this.setupContent(element);
            }

            this.link = element;
        },
        onOk: function() { // this == dialog
            this.commitContent(this.link);
            if (this.insertMode) {
                editor.insertElement(this.link);
            }
        }
    };
});
