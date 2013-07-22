<?
/**
 * replace_textareas.php - Pseudo-Template: Insert JS code to activate visual 
 * editing of textarea contents.
 *
 * This file contains the plugin's main class.
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
jQuery(function($){
/*
// replace original .show() method
    var _oldShow = $.fn.show;

  $.fn.show = function(speed, oldCallback) {
    return $(this).each(function() {
      var obj         = $(this),
          newCallback = function() {
            if ($.isFunction(oldCallback)) {
              oldCallback.apply(obj);
            }
            obj.trigger('afterShow');
          };

      // you can trigger a before show if you want
      obj.trigger('beforeShow');

      // now use the old function to show the element passing the new callback
      _oldShow.apply(obj, [speed, newCallback]);
    });
  }

$('textarea.add_toolbar')
    .bind('beforeShow', function() {
      alert('beforeShow');
    }) 
    .bind('afterShow', function() {
      alert('afterShow');
    })
    .show(1000, function() {
      alert('in show callback');
    })
    .show();
*/

    $('textarea.add_toolbar').on('focus', function(){
        $('.editor_toolbar > .buttons').remove();
        CKEDITOR.replace(this);
    });

//    editors = $('textarea.add_toolbar');
//    if (editors.length >= 1) {
//        CKEDITOR.replace(editors[0]); // NOTE doesn't work in forum
/*
        // helper for inserting a new DOM node in CKEditor
        var insertNode = function(jq_node) {
            CKEDITOR.instances['richtext-editor'].insertHtml(
                jQuery("<div>").append(jq_node).html());
        };

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
                insertNode(jQuery('<img />').attr({
                    src: file.url,
                    alt: file.name,
                    title: file.name
                }));
            },
            insertLink: function(that, file) {
                insertNode(jQuery('<a>').attr({
                    href: file.url,
                    type: file.type,
                    target: '_blank',
                    rel: 'nofollow'
                }).append(file.name));
            }
        };

        // handle drag'n'drop events
        var dropHandler = richTextPlugin.getDropHandler(callback);
        CKEDITOR.on('instanceReady', function(readyEvent){
            readyEvent.editor.document.on('drop', function(dropEvent){
                dropHandler(jQuery.Event(dropEvent.data.$));
            });
        });
*/
//    }
});
