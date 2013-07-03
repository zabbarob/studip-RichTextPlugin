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
include 'infobox.php'; // show infobox
include 'errors.php'; // show errors
?>

<input type="hidden" id="post_files_url" value="<?=PluginEngine::getLink('richtextplugin/post_file')?>">

<!-- the editor -->
<form enctype="multipart/form-data" style="padding:10px" id="edit_box" action="<?=PluginEngine::getLink('richtextplugin/show')?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <textarea id="richtext-editor" spellcheck="false" wrap="off" autofocus placeholder="Enter text..." name="body"><?=htmlReady($body);?></textarea>
    <br>
    <p style="margin:10px">
        <?= makeButton('uebernehmen', 'input', false, 'save') ?>
        <?= makeButton('abbrechen', 'input', false, 'cancel') ?>
    </p>
</form>

<!-- initialize NicEdit -->

<script type="text/javascript">
bkLib.onDomLoaded(function(){
    // initialize editor
    var nic = new nicEditor({
        fullPanel: true,
        iconsPath: richTextPlugin.dir + 'nicEditorIcons.gif' // set in script.js
    });
    nic.panelInstance('richtext-editor');
    var editor = nic.instanceById('richtext-editor');

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

