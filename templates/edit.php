<?
/**
 * edit_ckeditor.php - Template for editing contents with CKEditor.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Robert Costa <zabbarob@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
include 'common_edit.php';
?>
<script type="text/javascript" charset="utf-8">
jQuery(function($){
    var textarea = $('#richtext-editor');
    var uiColor = '#7788AA';  // same as studip's tab navigation background
    var toolbarId = 'cktoolbar';
    var toolbar = $('<div>').attr('id', toolbarId);
    /*var toolbarHandle = $('<div>').html('&#9776;&nbsp;').attr({
        id: 'toolbar-handle',
        title: 'Werkzeugleiste verschieben'
    });
toolbar.append(toolbarHandle);*/
    var anchor = $('<div></div>');
    anchor.insertBefore(textarea);
    toolbar.insertBefore(textarea);

    CKEDITOR.replace(textarea[0], {
        customConfig: '',
        uiColor: uiColor,
        removePlugins: 'about,anchor,bidi,blockquote,div,elementspath,flash'
                       + ',forms,iframe,maximize,newpage,preview,resize'
                       + ',showblocks,stylescombo,templates,save,smiley',
        extraPlugins: 'autogrow,divarea,sharedspace,studip-wiki',
        autoGrow_onStartup: true,
        sharedSpaces: {
			top: toolbarId
        },
        toolbarGroups: [
            {name: 'document',    groups: ['mode', 'document', 'doctools']},
            {name: 'clipboard',   groups: ['clipboard', 'undo']},
            {name: 'editing',     groups: ['find', 'selection', 'spellchecker']},
            {name: 'forms'},
            '/',
            {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
            {name: 'paragraph',   groups: ['list', 'indent', 'blocks', 'align']},
            {name: 'links'},
            '/',
            {name: 'styles'},
            {name: 'colors'},
            {name: 'tools'},
            {name: 'insert'},
            {name: 'others'},
            {name: 'about'}
        ],

        // convert special chars to html entities
        // NOTE use entities_additional: '#1049,...' for other chars
        entities: true,
        basicEntities: true,
        entities_greek: true,
        entities_latin: true,
        entities_processNumerical: true,

        // configure list of special characters
        // NOTE 17 characters fit in one row of special characters dialog
        specialChars: [].concat(
            [   "&Agrave;", "&Aacute;", "&Acirc;", "&Atilde;", "&Auml;",
                "&Aring;", "&AElig;", "&Egrave;", "&Eacute;", "&Ecirc;", "&Euml;",
                "&Igrave;", "&Iacute;", "&Iuml;", "&Icirc;", "", "&Yacute;",

                "&agrave;", "&aacute;", "&acirc;", "&atilde;", "&auml;",
                "&aring;", "&aelig;", "&egrave;", "&eacute;", "&ecirc;", "&euml;",
                "&igrave;", "&iacute;", "&iuml;", "&icirc;", "", "&yacute;",

                "&Ograve;", "&Oacute;", "&Ocirc;", "&Otilde;", "&Ouml;",
                "&Oslash;", "&OElig;", "&Ugrave;", "&Uacute;", "&Ucirc;", "&Uuml;",
                "", "&Ccedil;", "&Ntilde;", "&#372;", "", "&#374",

                "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;",
                "&oslash;", "&oelig;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;",
                "", "&ccedil;", "&ntilde;", "&#373", "", "&#375;",

                "&szlig;", "&ETH;", "&eth;", "&THORN;", "&thorn;", "", "",
                "`", "&acute;", "^", "&uml;", "", "&cedil;", "~", "&asymp;", "",
                "&yuml;"
            ],
            (function() {
                greek = [];
                for (var i = 913; i <= 929; i++) { // 17 uppercase characters
                    greek.push("&#" + String(i));
                }
                for (var i = 945; i <= 962; i++) { // 17 lowercase characters
                    greek.push("&#" + String(i));
                }
                // NOTE character #930 is not assigned!!
                for (var i = 931; i <= 937; i++) { // remaining upercase
                    greek.push("&#" + String(i));
                }
                greek.push('');
                for (var i = 963; i <= 969; i++) { // remaining lowercase
                    greek.push("&#" + String(i));
                }
                greek.push('');
                return greek;
            })(),
            [   "&ordf;", "&ordm;", "&deg;", "&sup1;", "&sup2;", "&sup3;",
                "&frac14;", "&frac12;", "&frac34;",
                "&lsquo;", "&rsquo;", "&ldquo;", "&rdquo;", "&laquo;", "&raquo;",
                "&iexcl;", "&iquest;",

                '@', "&sect;", "&para;", "&micro;",
                "[", "]", '{', '}',
                '|', "&brvbar;", "&ndash;", "&mdash;", "&macr;",
                "&sbquo;", "&#8219;", "&bdquo;", "&hellip;",

                "&euro;", "&cent;", "&pound;", "&yen;", "&curren;",
                "&copy;", "&reg;", "&trade;",

                "&not;", "&middot;", "&times;", "&divide;",

                "&#9658;", "&bull;",
                "&rarr;", "&rArr;", "&hArr;",
                "&diams;",

                "&#x00B1", // ±
                "&#x2229", // ∩ INTERSECTION
                "&#x222A", // ∪ UNION
                "&#x221E", // ∞ INFINITY
                "&#x2107", // ℇ EULER CONSTANT
                "&#x2200", // ∀ FOR ALL
                "&#x2201", // ∁ COMPLEMENT
                "&#x2202", // ∂ PARTIAL DIFFERENTIAL
                "&#x2203", // ∃ THERE EXISTS
                "&#x2204", // ∄ THERE DOES NOT EXIST
                "&#x2205", // ∅ EMPTY SET
                "&#x2206", // ∆ INCREMENT
                "&#x2207", // ∇ NABLA
                "&#x2282", // ⊂ SUBSET OF
                "&#x2283", // ⊃ SUPERSET OF
                "&#x2284", // ⊄ NOT A SUBSET OF
                "&#x2286", // ⊆ SUBSET OF OR EQUAL TO
                "&#x2287", // ⊇ SUPERSET OF OR EQUAL TO
                "&#x2208", // ∈ ELEMENT OF
                "&#x2209", // ∉ NOT AN ELEMENT OF
                "&#x2227", // ∧ LOGICAL AND
                "&#x2228", // ∨ LOGICAL OR
                "&#x2264", // ≤ LESS-THAN OR EQUAL TO
                "&#x2265", // ≥ GREATER-THAN OR EQUAL TO
                "&#x220E", // ∎ END OF PROOF
                "&#x220F", // ∏ N-ARY PRODUCT
                "&#x2211", // ∑ N-ARY SUMMATION
                "&#x221A", // √ SQUARE ROOT
                "&#x222B", // ∫ INTEGRAL
                "&#x2234", // ∴ THEREFORE
                "&#x2235", // ∵ BECAUSE
                "&#x2260", // ≠ NOT EQUAL TO
                "&#x2262", // ≢ NOT IDENTICAL TO
                "&#x2263", // ≣ STRICTLY EQUIVALENT TO
                "&#x22A2", // ⊢ RIGHT TACK
                "&#x22A3", // ⊣ LEFT TACK
                "&#x22A4", // ⊤ DOWN TACK
                "&#x22A5", // ⊥ UP TACK
                "&#x22A7", // ⊧ MODELS
                "&#x22A8", // ⊨ TRUE
                "&#x22AC", // ⊬ DOES NOT PROVE
                "&#x22AD", // ⊭ NOT TRUE
                "&#x22EE", // ⋮ VERTICAL ELLIPSIS
                "&#x22EF", // ⋯ MIDLINE HORIZONTAL ELLIPSIS
                "&#x29FC", // ⧼ LEFT-POINTING CURVED ANGLE BRACKET
                "&#x29FD", // ⧽ RIGHT-POINTING CURVED ANGLE BRACKET
                "&#x207F", // ⁿ SUPERSCRIPT LATIN SMALL LETTER N
                "&#x2295", // ⊕ CIRCLED PLUS
                "&#x2297", // ⊗ CIRCLED TIMES
                "&#x2299", // ⊙ CIRCLED DOT OPERATOR
            ]
        )
    }); // CKEDITOR.replace(textarea[0], {

    // helper for inserting a new DOM node in CKEditor
    var insertNode = function(jq_node) {
        CKEDITOR.instances['richtext-editor'].insertHtml(
            jQuery('<div>').append(jq_node).html());
    };

    // call-backs for drag'n'drop event handler
    var callback = {
        startUpload: function() {
            //$('#richtext-editor').addClass('uploading');
        },
        stopUpload: function() {
            //$('#richtext-editor').trigger('keydown');
            //$('#richtext-editor').removeClass('uploading');
        },
        insertImage: function(that, file) {
            insertNode(jQuery('<img />').attr({
                src: file.url,
                alt: file.name,
                title: file.name
            }));
        },
        insertLink: function(that, file) {
            insertNode(jQuery('<a>').attr({
                href: file.url,
                type: file.type,
                target: '_blank',
                rel: 'nofollow'
            }).append(file.name));
        }
    };

    // handle drag'n'drop events
    var dropHandler = richTextPlugin.getDropHandler(callback);
    CKEDITOR.on('instanceReady', function(event){
        var editor = event.editor;
        editor.document.on('drop', function(dropEvent){
            dropHandler(jQuery.Event(dropEvent.data.$));
        });
        editor.on('mode', function(event) {
            if (event.editor.mode === 'source') {
                source = $(event.editor.container.$).find('.cke_source');
                source.addClass('animated-height-change');
                source.autosize();
                source.focus();
            } else {
                editor.focus();
            }
        });

        var fadeTime = 300;
        var editorArea = textarea.siblings('#cke_richtext-editor');
        editor.on('focus', function(event){
            // add editor area shadow
            editorArea.css('box-shadow', '0 0 15px ' + uiColor);
//            toolbar.fadeIn(fadeTime);
        });
        editor.on('blur', function(event){
            // remove editor area shadow
            editorArea.css('box-shadow', '');
            if (toolbar.has(':focus').length > 0) {
                editor.focus();
            } else {
//                toolbar.fadeOut(fadeTime);
            }
        });

        // let the toolbar float, make it draggable from everywhere
        // and hide the dialog's parent window
/*        var toolbar_offset = 5;
        toolbar.draggable().offset({
            top: editorArea.offset().top - toolbar.height() + toolbar_offset,
            left: editorArea.offset().left + toolbar_offset
                  + editorArea.width() - toolbar.width()
        });
 */
        // do not scroll toolbar out of viewport
        var stickyTools = function() {
            var MARGIN = 30;
            if($(window).scrollTop() + MARGIN > anchor.offset().top) {
                toolbar.css({
                    position: 'fixed',
                    top: MARGIN
                });
            } else {
                toolbar.css({
                    position: 'relative',
                    top: ''
                });
            }
        };
        $(window).scroll(stickyTools);
        stickyTools();

        // focus the editor so the user can immediately hack away...
        editor.focus();
    });
});
</script>
