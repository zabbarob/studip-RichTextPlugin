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
?>
<span id="cktoolbar"></span>
<?include 'common_edit.php';?>
<script type="text/javascript">
jQuery(function($){
    CKEDITOR.replace('richtext-editor', {
        customConfig: '',
        uiColor: '#7788AA',  // same as studip's tab navigation background
        removePlugins: 'about,anchor,bidi,blockquote,div,elementspath,flash'
                       + ',forms,iframe,maximize,newpage,preview,resize'
                       + ',showblocks,stylescombo,templates',
        extraPlugins: 'autogrow,divarea,sharedspace',
        autoGrow_onStartup: true,
        autoGrow_bottomSpace: 50,
        sharedSpaces: {
			top: 'cktoolbar'
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
        editor.on('focus', function(event){
            // add shadow / glow effect (same color as CKEditor uiColor)
            $('#cke_richtext-editor').css('box-shadow', '0 0 3px #7788AA');
        });
        editor.on('blur', function(event){
            // remove shadow / glow effect (same color as CKEditor uiColor)
            $('#cke_richtext-editor').css('box-shadow', '');
        });

        // let the toolbar float, make it draggable from everywhere
        // and hide the dialog's parent window
        $('#cktoolbar').dialog({
            dialogClass: 'cktoolbar',
            draggable: false,
            resizable: false,
            minWidth: 0,
            minHeight: 0,
            show: 'fade'
        }).parent().draggable().css({
            width: 0,
            height: 0,
            border: 'none',
            margin: 0,
            padding: 0
        });

        editor.focus();
    });
});
</script>
