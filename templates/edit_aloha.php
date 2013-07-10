<?
/**
 * edit_aloha.php - Template for editing contents with Aloha.
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
<!-- load jQuery and require.js libraries -->
<script type="text/javascript" src="http://cdn.aloha-editor.org/latest/lib/require.js"></script>
<!-- 
<script type="text/javascript" src="http://cdn.aloha-editor.org/latest/lib/vendor/jquery-1.7.2.js"></script>
-->
<!-- load Aloha Editor core and some plugins -->
<script src="http://cdn.aloha-editor.org/latest/lib/aloha.js"
    data-aloha-plugins="common/ui,
                        common/format,
                        common/list,
                        common/link,
                        common/highlighteditables">
</script>

<!-- load the Aloha Editor CSS styles -->
<!-- <link href="http://cdn.aloha-editor.org/latest/css/aloha.css" rel="stylesheet" type="text/css" /> -->

<!-- make all elements with class="editable" editable with Aloha Editor -->
<script type="text/javascript">
// initialize and configure editor
Aloha.ready(function() {
    var $ = Aloha.jQuery;
    $('#richtext-editor').aloha();
});
</script>
