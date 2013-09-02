CKEDITOR.dialog.add('wikiDialog', function (editor) {
    function getParameterByName(name) {
        // http://stackoverflow.com/a/901144/641481
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(
            results[1].replace(/\+/g, " "));
    }
    function encode_utf8(s) {
        return unescape(encodeURIComponent(s));
    }
    function decode_utf8(s) {
        return decodeURIComponent(escape(s));
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
                // Stud.IP Wiki Link Definition
                // * allowed characters: a-z.-:()_§/@# äöüß
                // * enclose in double-brackets: [[wiki link]]
                // * test: ß
                // extended:
                // * add | followed by one or more characters for alt text
                // * characters can be anything but ]
                validate: CKEDITOR.dialog.validate.regex(
                    /^[\w\.\-\:\(\)§\/@# ÄÖÜäöüß]+$/i, // TODO ÄÖÜäöüß not working :(
                    "Page name must contain at least one character.\n"
                    + "Following characters are allowed:\n"
                    // TODO special chars §ÄÖÜäöüß are not displayed correctly
                    + "a-z 0-9 .-:( )_/@# and space."),
                setup: function(link) {
                    this.setValue(decodeURIComponent(link.getText()));
                },
                commit: function(link) {
                    link.setText(this.getValue());
                    link.setAttribute('href',
                        STUDIP.URLHelper.getURL('wiki.php', {
                            cid: getParameterByName('cid'),
                            keyword: encodeURIComponent(this.getValue())
                        })
                    );
                    link.setAttribute('class', 'wiki-link');
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
