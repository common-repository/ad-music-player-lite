(function() {
    tinymce.create('tinymce.plugins.Ad_Music_Player', {


        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            // executes this when the DOM is ready
            jQuery(function(){

                function amplayer_popup_insert_shortcode(){

                    //default shortocode options
                    var default_options = {
												'tracks' : ''
                    };

                    // get form id
                    var form_id = jQuery('#amplayer-popup-form');


                    var shortcode = '[admplayer';

                    for(var key in default_options) {
                        //get default value
                        var val_default=default_options[key];
												var val_new = new Array();
												jQuery('input.amplayer_tracks:checked',form_id).each(function() {
													val_new.push(jQuery(this).attr('value'));		
												});												
										//	console.log(jQuery(this).attr('value'));
                        //if new value from form isn't the same as default value - insert it into shortcode
                        if((val_new!='')&&(val_new!=val_default)){
                            shortcode += ' ' + key + '="' + val_new.join(",") + '"';
                        }
                    }

                    shortcode += ']';

                    // inserts the shortcode into the active editor
                    ed.execCommand('mceInsertContent', 0, shortcode);

                    // closes Thickbox
                    tb_remove();
                }

                jQuery.ajax({
                    url: url+"/plugin-tinymce-form-handler.php",
										type: "POST",
										data: amplayer_shortcode_tracks,
                    success: function (data) {
                        jQuery(data).appendTo('body').hide();
                        jQuery('#amplayer-form-submit').bind('click',amplayer_popup_insert_shortcode);
                    },
                    dataType: 'html'
                });
            });
            ed.addCommand('amplayer_tinymce_form', function() {

                //reset popup form to default input values
								if(jQuery('#amplayer-popup-form').length > 0){
									jQuery('#amplayer-popup-form')[0].reset();
								}
								

                // triggers the thickbox
                var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                W = W - 80;
                H = H - 114;
                tb_show( 'Ad Music Player', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=amplayer-popup-wrapper' );
								//add class to thickbox for style scrollbars
								jQuery("#TB_window").addClass("amplayer_shortcode_popup_form");


            });


            ed.addButton('amplayer_tinymce_form', {
                title : 'Add honey contact form',
                cmd : 'amplayer_tinymce_form',
                image : url + '/plugin-shortcode-tinymce-btn.png'
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                    longname : 'Ad Music Player Shortcode Buttons',
                    author : 'Circlewaves Team',
                    authorurl : 'http://cirlewaves.com',
                    version : '1.0'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('amplayer_tinymce_btns', tinymce.plugins.Ad_Music_Player);

})();