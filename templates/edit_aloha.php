<?
/**
 * edit_aloha.php - Template for editing contents with Aloha.
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
<!-- load jQuery and require.js libraries -->
<script type="text/javascript" src="http://cdn.aloha-editor.org/latest/lib/require.js"></script>
<!-- 
<script type="text/javascript" src="http://cdn.aloha-editor.org/latest/lib/vendor/jquery-1.7.2.js"></script>
-->
<!-- load Aloha Editor core and some plugins -->
<script src="http://cdn.aloha-editor.org/latest/lib/aloha.js"
    data-aloha-plugins="common/ui,
                        common/format,
                        common/list,
                        common/link,
                        common/highlighteditables">
</script>

<!-- load the Aloha Editor CSS styles -->
<!-- <link href="http://cdn.aloha-editor.org/latest/css/aloha.css" rel="stylesheet" type="text/css" /> -->

<!-- make all elements with class="editable" editable with Aloha Editor -->
<script type="text/javascript">
// initialize and configure editor
Aloha.ready(function() {
    var $ = Aloha.jQuery;
    $('#richtext-editor').aloha();

    // helper for inserting a new DOM node in Aloha
    var insertNode = function(jquery_node){
        if (!Aloha.activeEditable) {
            Aloha.editables[0].activate();
            Aloha.activeEditable.obj[0].focus();
            Aloha.Selection.updateSelection();
        }

        var range = Aloha.Selection.getRangeObject();
        GENTICS.Utils.Dom.insertIntoDOM(
            jquery_node,
            range,
            jQuery(Aloha.activeEditable.obj),
            true
        );
        range.select();
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
            insertNode($("<img />").attr({
                src: file.url,
                alt: file.name,
                title: file.name
            }));
        },
        insertLink: function(that, file) {
            insertNode($("<a>").attr({
                href: file.url,
                type: file.type,
                target: '_blank',
                rel: 'nofollow'
            }).append(file.name));
        }
    };

    // handle drag'n'drop events
    $('#richtext-editor-aloha').on(
        'drop', richTextPlugin.getDropHandler(callback));
});
</script>
