(function ( $ ) {
	"use strict";

	$(function () {
	
	soundManager.setup({
		url: custom_js_var.plugin_url+'/soundmanager/swf/',
		debugMode: false,
		playNext: false,
		onready: function() {
		//	 console.log('SM2 ready!');
		},
		ontimeout: function() {
		//	 console.log('SM2 init failed!');
		},
		defaultOptions: {
			// set global default volume for all sound objects
			volume: 98
		}
	});	
		
	
	$('.field-colorpicker').wpColorPicker();
	$('.wp-picker-holder').click(function( event ) { event.preventDefault();})
	
	$('#upload_track_button').click(function() {
		window.send_to_editor = function(html) {	
			var trackurl = jQuery(html).attr('href');
			$('#amplayer_track_url').val(trackurl);
			$('#amplayer-track-preview').attr('href',trackurl);
			$('#amplayer-track-preview-wrapper').show();
	//		$('#submit_options_form').trigger('click');
			window.send_to_editor=window.original_send_to_editor;
			tb_remove();
		}	
		tb_show('Track Upload', 'media-upload.php?referer=homepage-settings&amp;type=audio&amp;TB_iframe=true&amp;post_id=0', false);
		return false;
	});

	});

}(jQuery));