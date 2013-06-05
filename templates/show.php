<?
/**
 * show.php - Template for displaying the RichText plugin.
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

// show the current text from the database
?>
<div id="body"><?=$body?></div>
<br>
<hr>
<form id="edit_box" action="<?= PluginEngine::getLink('richtextplugin/edit_wysihtml5') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <?= makeButton('bearbeiten', 'input', false, 'edit') ?>
    <span style="vertical-align:top">with WysiHTML5</span>
</form>
<form action="<?= PluginEngine::getLink('richtextplugin/edit_tinymce') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <?= makeButton('bearbeiten', 'input', false, 'edit') ?>
    <span style="vertical-align:top">with TinyMCE</span>
</form>
<form action="<?= PluginEngine::getLink('richtextplugin/edit_nicedit') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <?= makeButton('bearbeiten', 'input', false, 'edit') ?>
    <span style="vertical-align:top">with NicEdit</span>
</form>
<div><?=
htmlReady($nothing) // contains message if there is no text in database
?></div>

