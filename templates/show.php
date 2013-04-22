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

// show infobox
$infobox_content[] = array(
    'kategorie' => _('&Uuml;ber den Rich Text Editor:'),
    'eintrag'   => array(
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Teilnehmer k&ouml;nnen den RichText Editor verwenden, um gemeinsam Informationen zum Kurs zu editieren.')),
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Folgende HTML Elemente sind erlaubt: <i>strong, b, em, i, a, span</i>.')),
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Links erhalten automatisch die Attribute <i>target="_blank"</i> und <i>rel="nofollow"</i>.')),
        array(
            'text' => _('<small>powered by <a href="https://github.com/xing/wysihtml5" target="_blank">wysihtml5</a></small>'))));

$infobox = array(
    'picture' => 'infobox/board1.jpg',
    'content' => $infobox_content);

// show the current text from the database
?>
<div id="body"><?=$body?></div>
<form id="edit_box" action="<?= URLHelper::getLink('/studip/plugins.php/richtextplugin/edit') ?>" method="POST">
    <?= makeButton('bearbeiten', 'input', false, 'edit') ?>
</form>
<div><?=
htmlReady($nothing) // contains message if there is no text in database
?></div>

