<?
$infobox_content[] = array(
    'kategorie' => _('&Uuml;ber den Rich Text Editor:'),
    'eintrag'   => array(
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Teilnehmer k&ouml;nnen den RichText Editor verwenden, um gemeinsam Informationen zum Kurs zu editieren.')),
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Links erhalten automatisch die Attribute <i>target="_blank"</i> und <i>rel="nofollow"</i>.')),
        array(
            'icon' => 'icons/16/black/info.png',
            'text' => _('Dateien k&ouml;nnen per Drag\'n\'Drop hochgeladen werden')),
        array(
            'text' => _('<small>'
            . '<a href="https://github.com/zabbarob/studip-RichTextPlugin" target="_blank">RichText plugin source on GitHub.</a>'
            . '<a href="https://github.com/zabbarob/studip-RichTextPlugin" target="_blank">'
            . '<img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png" alt="Fork me on GitHub">'
            . '</a>'
            . '<br>Powered by '
            . '<a href="https://github.com/xing/wysihtml5" target="_blank">WysiHTML5</a>'
            . ', <a href="http://www.tinymce.com/" target="_blank">TinyMCE</a>'
            . ', <a href="http://nicedit.com/" target="_blank">NicEdit</a>'
            . ' and <a href="http://htmlpurifier.org/" target="_blank">HTML Purifier</a>.'
            . '</small>'))));

$infobox = array(
    'picture' => 'infobox/board1.jpg',
    'content' => $infobox_content);

