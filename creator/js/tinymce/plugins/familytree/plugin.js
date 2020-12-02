/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*jshint unused:false */
/*global tinymce:true */

/**
 * Example plugin that adds a toolbar button and menu item.
 */
tinymce.PluginManager.add('familytree', function(editor, url) {
	// Add a button that opens a window
	editor.addButton('Family Tree', {
		text: 'Insert Family Tree',
		icon: false,
		onclick: function() {
			// Open window
			editor.windowManager.open({
				title: 'Insert Family Tree',
				body: [
					{type: 'textbox', name: 'family', label: 'Familyscript or GEDCOM'}
				],
				onsubmit: function(e) {
					// Insert content when the window form is submitted
					var newtree = ' <iframe id="familytree"></iframe>'+
				'<script>'+
                'var iframe = document.getElementById(\'familytree\'),'+
                'iframedoc = iframe.contentDocument || iframe.contentWindow.document;'+

                'iframedoc.body.innerHTML = '+
                   ' \'<div class="tm" style="">\'+ '+
                    
                    
                       ' \'<textarea class="tm" name="family" cols="80" rows="8" style="display:none; width:100%">'+e.data.family+'</textarea>\'+'+
                        
                       ' \'<input name="operation" value="temp_view" type="hidden">\'+'+
                       ' \'<input type="hidden" name="format" value="redirect">\'+'+
                            
                        
                       ' \'<p class="td"><input value="Show tree" type="submit"></p>\'+'+
                    
                    '\'</div>\';'+
                '</script>'
					editor.insertContent(newtree);
				}
			});
		}
	});

	/* Adds a menu item to the tools menu
	editor.addMenuItem('example', {
		text: 'Example plugin',
		context: 'tools',
		onclick: function() {
			// Open window with a specific url
			editor.windowManager.open({
				title: 'TinyMCE site',
				url: url + '/dialog.html',
				width: 600,
				height: 400,
				buttons: [
					{
						text: 'Insert',
						onclick: function() {
							// Top most window object
							var win = editor.windowManager.getWindows()[0];

							// Insert the contents of the dialog.html textarea into the editor
							editor.insertContent(win.getContentWindow().document.getElementById('content').value);

							// Close the window
							win.close();
						}
					},

					{text: 'Close', onclick: 'close'}
				]
			});
		}
	});
*/
});