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
?>

<input type="hidden" id="post_files_url" value="<?=PluginEngine::getLink('richtextplugin/post_file')?>">

<!-- the editor -->
<form enctype="multipart/form-data" style="padding:10px" id="edit_box" action="<?=PluginEngine::getLink('richtextplugin/show')?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <textarea id="nicedit-editor" spellcheck="false" wrap="off" autofocus placeholder="Enter text..." name="body"><?=htmlReady($body);?></textarea>
    <br>
    <p style="margin:10px">
        <?= makeButton('uebernehmen', 'input', false, 'save') ?>
        <?= makeButton('abbrechen', 'input', false, 'cancel') ?>
    </p>
</form>

<!-- initialize NicEdit -->

<!-- <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script> -->
<script type="text/javascript">
bkLib.onDomLoaded(/*nicEditors.allTextAreas*/function(){
    new nicEditor({
        fullPanel: true,
        iconsPath: window.nicEditorIcons // set in script.js
    }).panelInstance('nicedit-editor');
});
</script>

