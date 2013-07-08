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

// show current text from database
?><div id="body"><?=$body?></div><br><hr><?

// show buttons to open one of the editors
function addEditorButton($title) {
    ?><form action="<?
    echo PluginEngine::getLink('richtextplugin/edit_' . strtolower($title));
    ?>" method="POST"><?
    echo CSRFProtection::tokenTag();
    echo makeButton('bearbeiten', 'input', false, 'edit');
    ?><span style="vertical-align:top">with <?
    echo $title;
    ?></span></form><?
}

addEditorButton('WysiHTML5');
addEditorButton('TinyMCE');
addEditorButton('NicEdit');
addEditorButton('Aloha');

// show message if no text is in database
if (!$body) {
    ?><div><?
    echo htmlReady(_('Bisher wurde noch kein Text eingetragen.'));
    ?></div><?
}

