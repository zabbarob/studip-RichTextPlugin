CKEDITOR.dialog.add('wikiDialog', function (editor) {
    function getParameterByName(name) {
        // http://stackoverflow.com/a/901144/641481
        name = name.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)'),
            results = regex.exec(location.search);
        return results == null ? '' : decodeURIComponent(
            results[1].replace(/\+/g, ' '));
    }
    function windows1252(text) {
        // replace special chars with windows 1252 encoding
        // test string: azAZ09_-. #()/:@§ÄÖÜßäöü
        return text.replace(/[ #()/:@§ÄÖÜßäöü]/g, function(match) {
            return {
                ' ': '%20', '#': '%23', '(': '%28', ')': '%29',
                '/': '%2F', ':': '%3A', '@': '%40', '§': '%A7',
                'Ä': '%C4', 'Ö': '%D6', 'Ü': '%DC', 'ß': '%DF',
                'ä': '%E4', 'ö': '%F6', 'ü': '%FC'
            }[match];
      });
    }
    return {
        title: "Stud.IP-Wiki Link",
        minWidth: 400,
        minHeight: 200,
        contents: [{
            id: 'tab-link',
            label: "Stud.IP-Wiki Link",
            elements: [{
                type: 'text',
                id: 'wikipage',
                label: "Titel der Wiki-Seite",
                // Stud.IP Wiki Link Definition
                // * allowed characters: a-z.-:()_§/@# äöüß
                // * enclose in double-brackets: [[wiki link]]
                // * test: ß
                // extended:
                // * add | followed by one or more characters for alt text
                // * characters can be anything but ]
                validate: CKEDITOR.dialog.validate.regex(
                    /^[\w\.\-\:\(\)§\/@# ÄÖÜäöüß]+$/i,
                    "Der Seitenname muss aus mindestens einem Zeichen bestehen"
                    + " und darf nur folgende Zeichen enthalten:"
                    + " a-z A-Z ÄÖÜ äöü ß 0-9 -_:.( )/@#§ und das Leerzeichen."),
                setup: function(link) {
                    //var wikipage = link.getAttribute('href');
                    var wikipage = link.getText(); // TODO should get 'keyword' param from 'href' attribute
                    this.setValue(wikipage);
                },
                commit: function(link) {
                    var wikipage = this.getValue();
                    link.setText(wikipage);

                    var href = STUDIP.URLHelper.getURL('wiki.php', {
                        cid: getParameterByName('cid')
                    }) + '&keyword=' + windows1252(wikipage);

                    link.setAttribute('href', href);
                    link.setAttribute('data-cke-saved-href', href);
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
