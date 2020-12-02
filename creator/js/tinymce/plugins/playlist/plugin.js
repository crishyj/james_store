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
tinymce.PluginManager.add('playlist', function(editor, url) {

	function openmanager() {
        var win, data, dom = e.dom,
            imgElm = e.selection.getNode();
        var width, height, imageListCtrl;
        win = e.windowManager.open({
            title: 'Audio Manager',
            data: data,
            classes: 'filemanager',
            file: tinyMCE.baseURL + '/plugins/filemanager/dialog.php?type=5&editor=' + e.id + '&lang=' + tinymce.settings.language + '&subfolder=' + tinymce.settings.subfolder,
            filetype: 'audio',
            width: 900,
            height: 600,
            inline: 1
        })
    }

    var p = [{
            type: 'container',
            layout: 'flex',
            classes: 'combobox has-open',
            label: 'Source',
            direction: 'row',
            items: [{
                name: 'src',
                type: 'textbox',
                filetype: 'audio',
                size: 35,
                classes: 'music_' + e.id,
                autofocus: true
            }, {
                name: 'upl_audio',
                type: 'button',
                classes: 'btn open special_uploadbutton',
                icon: 'browse',
                onclick: openmanager,
                tooltip: 'Upload audio'
            }]
        },
        u,
        {
            name: "alt",
            type: "textbox",
            label: "Song title"
        }];


	// Add a button that opens a window
	editor.addButton('playlist', {
		text: 'Add to playlist',
		icon: false,
		onclick: function() {
			// Open window
			editor.windowManager.open({
				title: 'Add song to playlist',
				body: p,
				onsubmit: function(e) {
					// Insert content when the window form is submitted
					editor.insertContent('Title: ' + e.data.title);
				}
			});
		}
	});
});