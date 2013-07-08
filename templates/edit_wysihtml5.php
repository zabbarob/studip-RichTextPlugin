<?
/**
 * edit_wysihtml5.php - Template for editing contents with WysiHTML5.
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
?>
<!-- the toolbar -->

<div id="wysihtml5-toolbar" style="padding-left:10px; position:fixed; z-index:10000; display:none;">
  <header>
    <ul class="commands">
      <li data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command"></li>
      <li data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"></li>
      <li data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command"></li>
      <li data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command"></li>
      <li data-wysihtml5-command="createLink" title="Insert a link" class="command"></li>
      <li data-wysihtml5-command="insertImage" title="Insert an image" class="command"></li>
      <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Insert headline 1" class="command"></li>
      <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command"></li>
      <li data-wysihtml5-command-group="foreColor" class="fore-color" title="Color the selected text" class="command">
        <ul>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy"></li>
          <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue"></li>
        </ul>
      </li>
      <li data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"></li>
      <li data-wysihtml5-action="change_view" title="Show HTML" class="action"></li>
    </ul>
  </header>
  <div data-wysihtml5-dialog="createLink" style="display: none;">
    <label>
      Link:
      <input data-wysihtml5-dialog-field="href" value="http://">
    </label>
    <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
  </div>

  <div data-wysihtml5-dialog="insertImage" style="display: none;">
    <label>
      Image:
      <input data-wysihtml5-dialog-field="src" value="http://">
    </label>
    <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
  </div>
</div>

<div id="editor_position"></div>
<?
include 'common_edit.php';
?>

<!-- initialize WysiHTML5 -->

<script type="text/javascript">
// make sure code is only called after DOM structure is fully loaded
jQuery(function() {
    // initialize and configure editor
    var editor = new wysihtml5.Editor('richtext-editor', {
        toolbar:     'wysihtml5-toolbar',
        stylesheets: [
            'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css',
            richTextPlugin.dir + 'editor.css',
            richTextPlugin.dir + 'wysihtml5-colors.css'],
        parserRules: wysihtml5ParserRules
    });

    // only show toolbar when editor gets focused
    var toolbar = $("#wysihtml5-toolbar"); // TODO figure out how to get toolbar directly from wysihtml5.editor
    editor.on('focus', function() {
        toolbar.show();
    });
    editor.on('blur', function() {
        toolbar.hide();
    });

    // update the toolbar position to always keep the toolbar on screen
    var jq_window = $(window);
    var editor_position = $("#editor_position"); // this is a hack since getting position of #richtext-editor returns values relative to the iframe
    function updateToolbarPosition() {
        var editor_top = editor_position.offset().top;
        var window_top = jq_window.scrollTop();
        toolbar.offset({
            top: Math.max(0, editor_top - window_top - toolbar.height())
        });
    }
    updateToolbarPosition();
    jq_window.scroll(updateToolbarPosition);
    jq_window.on('resize', updateToolbarPosition);

    // insert newlines after <br>, <h1> (<h2>, <h3>, ...), <ol>, <ul>, <li>
    editor.on('change_view', function() {
        var editor_body = $($(".wysihtml5-sandbox")[0].contentWindow.document.body);
        var html = editor_body.html();
        var tags = /<br>|<\/h\d>|<[ou]l>|<\/[ou]l>|<\/li>/gi;
        html = html.replace(tags, function(match) {
            return match + '\n';
        }).replace(/\n\n/g,'\n');
        editor_body.html(html);
    });

    // call-backs for drag'n'drop event handler
    var textarea = $('#richtext-editor'); 
    var callback = {
        startUpload: function() {
            textarea.addClass('uploading');
        },
        stopUpload: function() {
            textarea.trigger('keydown');
            textarea.removeClass('uploading');
        },
        insertImage: function(that, file) {
            var image = {
                src: file.url,
                alt: file.name,
                title: file.name
            };

            editor.composer.commands.exec('insertImage', image);

            // NOTE workaround: if wysihtml is in "show HTML" mode then 
            // editor.composer.commands.exec('insertImage') does not work
            if (that == textarea[0]) {
                var html = $('<div>').append($('<img>', image)).html();
                textarea.val(textarea.val() + html);
            }
        },
        insertLink: function(that, file) {
            var html = $('<div>').append($('<a>', {
                target: '_blank',
                rel: 'nofollow',
                text: file.name,
                type: file.type,
                href: file.url
            })).html();

            editor.focus();
            editor.composer.commands.exec('insertHTML', html);

            // NOTE workaround: if wysihtml is in "show HTML" mode then 
            // editor.composer.commands.exec('insertHTML') does not work
            if (that == textarea[0]) {
                textarea.val(textarea.val() + html);
            }

        }
    };

    // handle drag'n'drop events
    var dropHandler = richTextPlugin.getDropHandler(callback);

    //editor.on('drop', dropHandler); // doesn't work
    //editor.on('paste', dropHandler); // doesn't work
    var editor_body = $(".wysihtml5-sandbox")[0].contentWindow.document.body;
    $(editor_body).on('drop', dropHandler);

    // in HTML view mode editor_body doesn't get drop event
    //textarea.on('dragover', ignoreEvent);
    //textarea.on('dragenter', ignoreEvent);
    textarea.on('drop', dropHandler);
});
</script>

