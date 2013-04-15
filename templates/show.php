<?
/**
 * show.php - Template for displaying the RichText plugin.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Robert Costa <rcosta@uos.de>
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

// show the toolbar and the actual editor component
?>
<form>
  <div id="wysihtml5-toolbar" style="display: none;">
    <a data-wysihtml5-command="bold" title="CTRL+B">bold</a> |
    <a data-wysihtml5-command="italic" title="CTRL+I">italic</a>
    ||

    <!-- Some wysihtml5 commands require extra parameters -->
    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red">red</a> |
    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green">green</a> |
    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue">blue</a>
    ||

    <!-- Some wysihtml5 commands like 'createLink' require extra paramaters specified by the user (eg. href) -->
    <a data-wysihtml5-command="createLink">insert link</a>
    <div data-wysihtml5-dialog="createLink" style="display: none;">
      <label>Link: <input data-wysihtml5-dialog-field="href" value="http://" class="text"></label>
      <a data-wysihtml5-dialog-action="save">OK</a>
      <a data-wysihtml5-dialog-action="cancel">Cancel</a>
    </div>
    ||

    <a data-wysihtml5-action="change_view">switch to html view</a>
  </div>
  <textarea id="wysihtml5-textarea" placeholder="Enter text ..."></textarea>
</form>

