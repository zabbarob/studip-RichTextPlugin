<h1>wysihtml5 - Simple Editor Example</h1>

<p>
  Uses a custom rule set that allows the following elements: <em>strong, b, em, i, a, span</em><br>
  Links will automatically receive <i>target="_blank"</i> and <i>rel="nofollow"</i>. Check the source code of this page.
</p>

<form>
  <div id="toolbar" style="display: none;">
    <a data-wysihtml5-command="bold" title="CTRL+B">bold</a> |
    <a data-wysihtml5-command="italic" title="CTRL+I">italic</a>
    <a data-wysihtml5-action="change_view">switch to html view</a>
  </div>
  <textarea id="textarea" placeholder="Enter text ..."></textarea>
  <br><input type="reset" value="Reset form!">
</form>

<h2>Events:</h2>
<div id="log"></div>

<small>powered by <a href="https://github.com/xing/wysihtml5" target="_blank">wysihtml5</a></small>

