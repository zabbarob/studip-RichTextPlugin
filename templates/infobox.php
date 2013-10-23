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
function text($text, $icon=null) {
    if ($icon != null) {
        $icon = 'icons/16/black/' . $icon . '.png';
    }
    return array('text' => \utf8_decode($text), 'icon' => $icon);
}

function url($url, $html) {
    return '<a href="' . $url . '" target="_blank">' . $html . '</a>';
}

// initialize content of infobox
$infobox_content[] = array(
    'kategorie' => \utf8_decode(_('Über den Rich Text Editor:')),
    'eintrag'   => array(
        text(_('Um den angezeigten Text zu bearbeiten, wählen Sie aus dem'
               . ' Navigationsmenü <b>Bearbeiten</b> aus. Sollte dort als'
               . ' einzige Option <b>Lesen</b> angezeigt werden, dann bitten'
               . ' sie ihren Kursleiter um Schreibrechte.'), 'info'),
        text(_('Dateien können auch per Drag\'n\'Drop hochgeladen werden.'),
             'upload'),
        text(_('Stud.IP-Wiki Links sind am Wiki-Icon erkennbar: <a href="'
               . URLHelper::getURL('wiki.php')
               . '" class="wiki-link">Stud.IP-Wiki</a>.'), 'wiki'),
        text(_('Die Werkzeugleiste kann beliebig verschoben werden. Sie wird'
               . ' automatisch ein- und ausgeblendet.'), 'hash'),
        text(_('<small id="sources">'
               . url('https://github.com/zabbarob/studip-RichTextPlugin',
                     'RichText plugin source on GitHub. <img id="github-fork" '
                     . 'src="https://s3.amazonaws.com/github/ribbons/'
                     . 'forkme_right_green_007200.png" alt="Fork me on GitHub">')
               . '<br>'
               . 'Powered by ' . url('http://ckeditor.com', 'CKEditor')
               . ' and ' . url('http://htmlpurifier.org', 'HTML Purifier')
               . '. Source view autoresize by '
               . url('http://www.jacklmoore.com/autosize/', 'jQuery Autosize')
               . '. File upload by '
               . url('http://blueimp.github.io/jQuery-File-Upload',
                     'jQuery File Upload')
               . '.</small>'))));

// initialize infobox
$infobox = array('picture' => 'infobox/board1.jpg',
                 'content' => $infobox_content);
