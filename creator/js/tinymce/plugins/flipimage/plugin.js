/**
 * plugin.js
 *
 * Copyright, VAN STEIN & GROENTJES B.V.
 *
 */

/*jshint unused:false */
/*global tinymce:true */

tinymce.PluginManager.add('flipimage', function(editor, url) {
	// Add a button that opens a window
	editor.addButton('flipimage', {
		text: 'Flip image',
		icon: 'image',
		onclick: function() {
			// Open window
			editor.windowManager.open({
				title: 'Insert flip image',
				body: [
					{type:"filepicker",filetype:"image", name: 'front', label: 'Front:'},
					{type:"filepicker",filetype:"image", name: 'back', label: 'Back:'}
				],
				onsubmit: function(e) {
					var newflip = '<div class="flip-container" ontouchstart="this.classList.toggle(\'hover\');">'+
						'<div class="flipper">'+
							'<div class="front">'+
							'<img src="'+e.data.front+'"/>'+
							'<p>Front card (change text in Tools-sourcecode)</p>'+
							'</div>'+
							'<div class="back">'+
							'<img src="'+e.data.back+'"/>'+
							'<p>Back card</p>'+
							'</div>'+
						'</div>'+
					'</div>';
					editor.insertContent(newflip);
				}
			});
		}
	});

});