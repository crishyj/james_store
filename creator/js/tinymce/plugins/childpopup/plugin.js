/**
 * plugin.js
 *
 * Copyright, VAN STEIN & GROENTJES B.V.
 *
 */

/*jshint unused:false */
/*global tinymce:true */

tinymce.PluginManager.add('childpopup', function(editor, url) {
	// Add a button that opens a window
	editor.addButton('childpopup', {
		text: 'Insert Dialog',
		icon: 'link',
		onclick: function() {
			// Open window
			editor.windowManager.open({
				title: 'Insert popup dialog',
				body: [
					{type: 'textbox', name: 'linktext', label: 'Link text:'},
					{type: 'textbox', name: 'popuptitle', label: 'Popup title'},
					{type: 'textbox', multiline:'true', name: 'popuptext', label: 'Popup content (html)'}
				],
				onsubmit: function(e) {
					var choicenumber = 0;
					var id = "openModal";
					
					while ( editor.dom.get(id+choicenumber)!= null ){
						choicenumber += 1;
					}
					// Insert content when the window form is submitted
					var newdialog = '<a href="#'+id+choicenumber+'">'+e.data.linktext+'</a>'+
									'<div id="'+id+choicenumber+'" class="modalDialog"> '+
										'<div>'+
											'<div class="close"> X </div>'+
											'<h2>'+e.data.popuptitle+'</h2>'+
											'<p>'+e.data.popuptext+'</p>'+
										'</div>'+
									'</div>';
					editor.insertContent(newdialog);
				}
			});
		}
	});

});