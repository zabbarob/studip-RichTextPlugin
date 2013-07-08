<?
/**
 * edit_tinymce.php - Template for editing contents with TinyMCE.
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
include 'common_edit.php';
?>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste fullpage textcolor"],

    toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
    toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

    menubar: false,
    toolbar_items_size: 'small',

    style_formats: [
        {title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}],

    templates: [
        {title: 'Test template 1', content: 'Test 1'},
        {title: 'Test template 2', content: 'Test 2'}],

    relative_urls: false,
    remove_script_host: true,

    setup: function(ed) {
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
                var image = ed.getDoc().createElement("img");
                image.src = file.url;
                image.alt = file.name;
                image.title = file.name;
                ed.selection.getRng().insertNode(image);
            },
            insertLink: function(that, file) {
                var link = ed.getDoc().createElement("a");
                link.href = file.url;
                link.text = file.name;
                link.type = file.type;
                link.target = '_blank';
                link.rel = 'nofollow';
                ed.selection.getRng().insertNode(link);
            }
        };

        // handle drag'n'drop events
        var dropHandler = richTextPlugin.getDropHandler(callback);
        ed.on('drop', function(e) { return dropHandler(jQuery.Event(e)); });
    }
});
</script>

