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
    var toolbarId = 'cktoolbar';
    var toolbar = $('<span id="' + String(toolbarId) + '"></span>');
    toolbar.insertBefore(textarea);

    CKEDITOR.replace(textarea[0], {
        customConfig: '',
        uiColor: '#7788AA',  // same as studip's tab navigation background
        removePlugins: 'about,anchor,bidi,blockquote,div,elementspath,flash'
                       + ',forms,iframe,maximize,newpage,preview,resize'
                       + ',showblocks,stylescombo,templates',
        extraPlugins: 'autogrow,divarea,sharedspace,studip-wiki',
        autoGrow_onStartup: true,
        autoGrow_bottomSpace: 50,
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
        ]
    });

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
            }
        });

        var editorArea = textarea.siblings('#cke_richtext-editor');
        editor.on('focus', function(event){
            // add shadow / glow effect (same color as CKEditor uiColor)
            editorArea.css('box-shadow', '0 0 3px #7788AA');
        });
        editor.on('blur', function(event){
            // remove shadow / glow effect (same color as CKEditor uiColor)
            editorArea.css('box-shadow', '');
        });

        // let the toolbar float, make it draggable from everywhere
        // and hide the dialog's parent window
        var toolbar_offset = 5;
        toolbar.fadeIn(1000).draggable().offset({
            top: editorArea.offset().top - toolbar.height() + toolbar_offset,
            left: editorArea.offset().left + toolbar_offset
                  + editorArea.width() - toolbar.width()
        });

        editor.focus();
    });
});
</script>
