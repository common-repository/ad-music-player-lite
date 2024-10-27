

<style>
	.amplayer_shortcode_popup_form{overflow:hidden!important;}
	.amplayer_shortcode_popup_form #TB_ajaxContent{overflow-y:scroll!important;}
  .amplayer_shortcode_popup_form-popup-form-wrapper{}
  .amplayer-popup-form-wrapper .fieldset-wrapper{margin-bottom:10px;}
  .amplayer-popup-form-wrapper a{color:#21759b !important;}
  .amplayer-popup-form-wrapper a:hover{color:#333 !important;text-decoration: none !important;}
  .amplayer-popup-form-wrapper fieldset{padding:4px 9px 7px 9px;border:1px solid #ccc;}
  .amplayer-popup-form-wrapper .field-row{margin:7px 0px;}
  .amplayer-popup-form-wrapper .field-help{font-size:0.8em}
  .amplayer-popup-form-wrapper .field-descr{font-size:0.9em}	
</style>
<div id="amplayer-popup-wrapper">
  <h2>Insert Ad Music Player</h2>
<?php
if(($_POST['track_list']) && (!empty($_POST['track_list'])) && (is_array($_POST['track_list']))){
$track_list=$_POST['track_list'];
?>


  <div class="amplayer-popup-form-wrapper">
    <p class="field-descr">
      You can insert ad music player using the form below.
    </p>
    <form id="amplayer-popup-form">
      <div class="fieldset-wrapper">
          <fieldset>
            <legend>Select tracks</legend>
						<p class="field-descr">If none of this tracks is selected &mdash; all the tracks will be played.						

				<?php
					foreach($track_list as $k){	
						$track_title=$k['title'];
						$track_title.=$k['artist']?(' - '.$k['artist']):'';
					?>
						<div class="field-row"><input class="amplayer_tracks" type="checkbox" id="tinymce_amplayer_track_<?php echo $k['id'];?>" name="amplayer_tracks[<?php echo $k['id'];?>]" value="<?php echo $k['id'];?>" /><label for="tinymce_amplayer_track_<?php echo $k['id'];?>"><?php echo $track_title; ?></label></div>
					<?php	} ?>

          </fieldset>
      </div>		

      <div class="submit">
        <input type="button" id="amplayer-form-submit" class="button-primary" value="Insert Player" name="submit" />
      </div>
    </form>
  </div>
<?php }else{?>
    <p class="field-descr">
      Sorry, no tracks found
    </p>
<?php } ?>	
</div>