<?
/**
 * edit_nicedit.php - Template for editing contents with NicEdit.
 * http://nicedit.com
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

<div id="nicedit-toolbar" style="padding-left:10px; width:98%; z-index:100000;"></div>

<div id="editor-position"> </div>
<?
include 'common_edit.php';
?>

<script type="text/javascript">
//MathJax.Hub.config.skipStartupTypeset = true;
bkLib.onDomLoaded(function(){
    // setting MathJax queue to pending somehow doesn't work with NicEdit
    // MathJax is deleted to prevent typesetting formulas in the editor
    // this leads to error messages concerning MathJax, but editing works
    //delete MathJax.Hub.Queue;
    delete MathJax.Hub;
    delete MathJax;

    // initialize editor
    var nic = new nicEditor({
        fullPanel: true,
        iconsPath: richTextPlugin.dir + 'nicEditorIcons.gif' // set in script.js
    });
    nic.setPanel('nicedit-toolbar');
    nic.addInstance('richtext-editor');

    var editor = nic.instanceById('richtext-editor');

    // resize NicEdit toolbar and editor window
    var nic_main = $('.nicEdit-main');
    var nic_panel = $('.nicEdit-panelContain');
    $(window).on('resize', function(){
        nic_main.parent().width('100%');
        nic_main.width('100%');
        nic_panel.parent().width(nic_main.parent().width());
        nic_panel.width(nic_main.parent().width());
    });

    // update the toolbar position to always keep the toolbar on screen
    var toolbar = $('#nicedit-toolbar');
    var editor_position = $('#editor-position');// this is a hack since getting position of #richtext-editor returns values relative to the iframe
    var jq_window = $(window);
    var updateToolbarPosition = function(){
        var editor_top = editor_position.offset().top;
        var window_top = jq_window.scrollTop();
        toolbar.offset({
            top: Math.max(0, editor_top - window_top - toolbar.height()) + window_top
        });
    };
    updateToolbarPosition();
    jq_window.scroll(updateToolbarPosition);
    jq_window.on('resize', updateToolbarPosition);

    // helpers
    var insertHtml = function(html) {
        var range = editor.getRng();
        var content = editor.getContent();
        editor.setContent(
            content.substring(0, range.startOffset)
            + html
            + content.substring(range.endOffset, content.length));
    };
    var insertNode = function(node) {
        insertHtml(jQuery('<div>').append(node).html());
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
            insertNode(jQuery('<img>', {
                src: file.url,
                alt: file.name,
                title: file.name
            }));

        },
        insertLink: function(that, file) {
            insertNode(jQuery('<a>', {
                target: '_blank',
                rel: 'nofollow',
                text: file.name,
                type: file.type,
                href: file.url
            }));
        }
    };

    // handle drag'n'drop events
    var dropHandler = richTextPlugin.getDropHandler(callback);
    editor.instanceDoc.ondrop = function(e) {
        return dropHandler(jQuery.Event(e));
    };
});
</script>

