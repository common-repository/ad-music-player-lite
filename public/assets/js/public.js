(function ( $ ) {
	"use strict";

	$(function () {
	
	$('.amplayer-container').each(function( index ) {
		//get uid of current player
		var amplayer_current=$(this);
		var amplayer_uid=amplayer_current.data('amplayer-uid');
		var plugin_options=window['amplayer_plugin_options_'+amplayer_uid];
		

		/* 
		* Player Styling
		*/
		var amplayer_styles={};
		
		// player container
		if(plugin_options.amplayer_style_color_background){
			amplayer_styles['background-color']=plugin_options.amplayer_style_color_background;
		}
		// player text color
		if(plugin_options.amplayer_style_color_text){
			amplayer_styles['color']=plugin_options.amplayer_style_color_text;
		}		
		
		// Append Ad Sizes
		//Width
		if(plugin_options.amplayer_ad_width){
			var amplayer_ad_width=parseInt(plugin_options.amplayer_ad_width);
			$('.amplayer-details-container, .amplayer-details-ad').css('width',amplayer_ad_width);
			amplayer_styles['width']=amplayer_ad_width+130+'px';
		}		
		//Height 
		if(plugin_options.amplayer_ad_height){
			var amplayer_ad_height=parseInt(plugin_options.amplayer_ad_height);
			amplayer_ad_height=amplayer_ad_height+'px';
			$('.amplayer-details-container, .amplayer-details-ad').css('height',amplayer_ad_height);
		}
		// End Ad Sizes
		$('.amplayer-container').css(amplayer_styles);
		

		//buttons
		if(plugin_options.amplayer_style_color_main_normal){
			$('.amplayer-btn').css('background-color',plugin_options.amplayer_style_color_main_normal);
		}		
		//buttons active
		if(plugin_options.amplayer_style_color_main_active){
			$('.amplayer-btn').hover(
				function(){
					$(this).css('background-color',plugin_options.amplayer_style_color_main_active);
				},
				function(){
					$(this).css('background-color',plugin_options.amplayer_style_color_main_normal);
				}		
			);		
		}		
	
			
		
	
		
		/* End Player Styling */		


		
		/* 
		* Player init
		*/		
		var currentTrackObj=null;		
		var playerCurrentState=parseInt(plugin_options.amplayer_autoplay)?1:0;
		var playerVolume =plugin_options.amplayer_volume_value?parseInt(plugin_options.amplayer_volume_value):70;
		function AdMusicPlayer(){

			var self = this;
			var player = this;
			var sm = soundManager; // soundManager instance
			//track and playlist
			var playlist = plugin_options.amplayer_playlist;
			var playlistCount = playlist.length;
			var currentTrack = 0;
			var currentTrackUID = '';

			//player controls and buttons
			var btnPlay = $('.amplayer-btn-play',amplayer_current);
			var btnPrev = $('.amplayer-btn-prev',amplayer_current);
			var btnNext = $('.amplayer-btn-next',amplayer_current);
			
			var containerTrack=$('.amplayer-details-track',amplayer_current);
			var detailsAd=$('.amplayer-details-ad',amplayer_current);
			var containerAd=$('.amplayer-ad-container',amplayer_current);
			
			var trackCover=$('.amplayer-track-cover',amplayer_current);
			var trackName=$('.amplayer-track-name',amplayer_current);
			var trackArtist=$('.amplayer-track-artist',amplayer_current);
			var trackButton=$('.amplayer-links-container',amplayer_current);
			
			
			//if playlist contain only 1 item - hide btnPrev and btnNext
			if(playlistCount<1){return false;}
			if(playlistCount==1){
				btnPrev.hide();
				btnNext.hide();
			}
			// ==
				
			this.playerChangeState = function(){
				btnPlay.toggleClass('amplayer-btn-pause');
				self.playerHandleAd();
				playerCurrentState=playerCurrentState?0:1;
			}
			
			this.playSound = function(){
				if(currentTrackObj){
				currentTrackObj.destruct();
				}
				
				currentTrackUID='amplayer_'+amplayer_uid+'_'+playlist[currentTrack].id;
				currentTrackObj = soundManager.createSound({
				 // optional id, for getSoundById() look-ups etc. If omitted, an id will be generated.
				 id: currentTrackUID,
				 url: playlist[currentTrack].track,
				 // optional sound parameters here, see Sound Properties for full list
				 volume: playerVolume,
				 autoPlay: playerCurrentState,				 
				 onfinish:self.playNextSound
				});		
				
				// Track details
				trackCover.html(playlist[currentTrack].cover);
				trackName.html(playlist[currentTrack].title);
				trackArtist.html(playlist[currentTrack].artist);
				trackButton.html(playlist[currentTrack].button);		
				//Ad Handling: is post ad exist - display it, else - display track ad (if exist); else - display main ad(if exist)
				detailsAd.empty();
				if(containerAd.children('.amplayer-ad-post').length>0){
					detailsAd.append(containerAd.children('.amplayer-ad-post').html());
				}else if(containerAd.children('.amplayer-ad-track-'+playlist[currentTrack].id).length>0){
					detailsAd.append(containerAd.children('.amplayer-ad-track-'+playlist[currentTrack].id).html());
				}else if(containerAd.children('.amplayer-ad-main').length>0){
					detailsAd.append(containerAd.children('.amplayer-ad-main').html());
				}
				// End Ad
				
				// track extra button
				if(plugin_options.amplayer_style_track_button_color_normal){
					$('a.amplayer-track-link',amplayer_current).css('color',plugin_options.amplayer_style_track_button_color_normal);
				}
				// END DEBUG
			}
			
			this.playNextSound = function(){
					currentTrack++;
					currentTrack=(currentTrack>=playlistCount)?0:currentTrack;
					if(currentTrack==0 && !plugin_options.amplayer_playlist_repeat && playerCurrentState==1){
						self.playerChangeState();
					}
						self.playSound();					
			}
			
			this.playPrevSound = function(){
				currentTrack--;
				currentTrack=(currentTrack<0)?(playlistCount-1):currentTrack;
				self.playSound();		
			}			
			
			btnPlay.click(function() {
				currentTrackObj.togglePause();
				self.playerChangeState();
			});	

			btnNext.click(function() {
				self.playNextSound();
			});	
				
			btnPrev.click(function() {
				self.playPrevSound();
			});

		/*
		* AD
		*/
			this.playerHandleAd = function(){
				if(plugin_options.amplayer_ad_enable){
					containerTrack.toggle();
					detailsAd.toggle();
				}
			}		
			/*
		* END AD
		*/
			
		/*
		* Volume
		*/

//Store frequently elements in variables
			var slider  = $('.amplayer-volume-slider',amplayer_current),
					volume = $('.amplayer-volume-power',amplayer_current),
				tooltip = $('.amplayer-volume-tooltip',amplayer_current);
				
			//Hide the Tooltip at first
			tooltip.hide();

			//Call the Slider
			slider.slider({
				//Config
				range: "min",
				min: 0,
				value: playerVolume,

				start: function(event,ui) {
				    tooltip.fadeIn('fast');
				},

				//Slider Event
				slide: function(event, ui) { //When the slider is sliding

					var value  = slider.slider('value');
				
					playerVolume=value;
					soundManager.setVolume(currentTrackUID,playerVolume);
						
					tooltip.css('left', value).text(ui.value);  //Adjust the tooltip accordingly

					if(value <= 5) { 
						volume.css('background-position', '0 -192px');
					} 
					else if (value <= 25) {
						volume.css('background-position', '0 -224px');
					} 
					else if (value <= 75) {
						volume.css('background-position', '0 -256px');
					} 
					else {
						volume.css('background-position', '0 -288px');
					};

				},

				stop: function(event,ui) {
				    tooltip.fadeOut('fast');
				},
			});

		/* End Volume*/			
			

			this.init = function() {
			if(playerCurrentState){
				btnPlay.toggleClass('amplayer-btn-pause');	
				self.playerHandleAd();				
			}			
				self.playSound();
			}		
				
			this.init();		
		}	
		
			

			soundManager.setup({
				// disable or enable debug output
				debugMode: false,
				// path to directory containing SM2 SWF
				url: plugin_options.amplayer_plugin_url+'/soundmanager/swf/',
				// optional: enable MPEG-4/AAC support (requires flash 9)
				flashVersion: 9
			});

			// ----

			soundManager.onready(function() {
				// soundManager.createSound() etc. may now be called
				var adMusicPlayer = null;		
					adMusicPlayer = new AdMusicPlayer();

			});		
		/* End Player init */		
		

		


	});			
	
	
	});

}(jQuery));