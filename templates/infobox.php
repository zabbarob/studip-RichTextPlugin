<?
/**
 * infobox.php - Configuration of the RichText plugin's info box.
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

/**
 * Map a UTF8 encoded text to associated array as needed for the infobox
 * content.
 *
 * @param string  The UTF8 encoded text.
 * @returns array Entry for the info box.
 */
function info($text) {
    return array('icon' => 'icons/16/black/info.png',
                 'text' => \utf8_decode($text));
}

// initialize content of infobox
$infobox_content[] = array(
    'kategorie' => \utf8_decode(_('Über den Rich Text Editor:')),
    'eintrag'   => array(
        info(_('Teilnehmer können den RichText Editor verwenden, um gemeinsam'
               . ' Informationen zum Kurs zu editieren.')),
        info(_('Links erhalten automatisch die Attribute'
               . ' <i>target="_blank"</i> und <i>rel="nofollow"</i>.')),
        info(_('Dateien können per Drag\'n\'Drop hochgeladen werden')),
        info(_('Das RichText-Plugin bietet im Augenblick zu Testzwecken'
               . ' verschiedene Editoren an. In der endgültigen Version wird'
               . ' jedoch nur noch einer dieser Editoren enthalten sein.')),
        info(_('<small id="sources">'
               . '<a href="https://github.com/zabbarob/studip-RichTextPlugin"'
               . ' target="_blank">RichText plugin source on GitHub.</a>'
               . '<a href="https://github.com/zabbarob/studip-RichTextPlugin"'
               . ' target="_blank"><img id="github-fork"'
               . ' src="https://s3.amazonaws.com/github/ribbons/'
               . 'forkme_right_green_007200.png" alt="Fork me on GitHub"></a>'
               . '<br>Powered by '
               . '<a href="https://github.com/xing/wysihtml5"'
               . ' target="_blank">WysiHTML5</a>'
               . ', <a href="http://www.tinymce.com/" target="_blank">TinyMCE</a>'
               . ', <a href="http://nicedit.com/" target="_blank">NicEdit</a>'
               . ', <a href="http://www.aloha-editor.org/">Aloha</a>'
               . ', <a href="http://ckeditor.com/">CKEditor</a>'
               . ', and <a href="http://htmlpurifier.org/" target="_blank">'
               . 'HTML Purifier</a>.</small>'))));

// initialize infobox
$infobox = array('picture' => 'infobox/board1.jpg',
                 'content' => $infobox_content);
