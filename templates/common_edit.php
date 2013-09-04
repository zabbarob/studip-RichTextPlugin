<?
/**
 * common_edit.php - Common code used by all "edit_*.php" templates.
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
<!-- turn off MathJax in edit mode -->
<script>MathJax.Hub.queue.pending = 1;</script>

<!-- store url to which files are posted for drag'n'drop in editor -->
<input type="hidden" id="post_files_url" value="<?=PluginEngine::getLink('richtextplugin/post_file')?>">

<!-- the editor -->
<form enctype="multipart/form-data" style="padding:10px" id="edit-form" action="<?=PluginEngine::getLink('richtextplugin/show')?>" method="POST" accept-charset="utf-8">
    <?= CSRFProtection::tokenTag() ?>
    <textarea id="richtext-editor" spellcheck="false" wrap="off" autofocus placeholder="Enter text..." name="body"><?=htmlReady($body);?></textarea>
    <br>
    <p style="margin:10px">
        <?= makeButton('uebernehmen', 'input', false, 'save') ?>
        <?= makeButton('abbrechen', 'input', false, 'cancel') ?>
    </p>
</form>

