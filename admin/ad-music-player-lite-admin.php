<?php
/**
 * Ad Music Player Lite
 *
 * @package   Ad_Music_Player_Lite_Admin
 * @author    Circlewaves Team <support@circlewaves.com>
 * @license   GPL-2.0+
 * @link      http://circlewaves.com
 * @copyright 2014 Circlewaves Team <support@circlewaves.com>
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package   Ad_Music_Player_Lite_Admin
 * @author    Circlewaves Team <support@circlewaves.com>
 */
class Ad_Music_Player_Lite_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Ad_Music_Player_Lite::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_options_init' ) );	
		add_action( 'admin_init', array( $this, 'popup_change_button_text_filter' ) );	

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		
		/*
		* Add TinyMCE button to handle player shortcode
		*/
		add_action( 'admin_init', array( $this, 'amplayer_tinymce_buttons' ) );		
		// Pass list of tracks into TinyMCE plugin
    add_action( "before_wp_tiny_mce", array($this,'amplayer_tinymce_pass_vars') );

		

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

/* 		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		} */

		$screen = get_current_screen();
	//	if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Ad_Music_Player_Lite::VERSION );
	//	}
		if (( $this->plugin_screen_hook_suffix == $screen->id )||($screen->post_type==Ad_Music_Player_Lite::MAIN_TAXONOMY)) {
		
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style('thickbox');
			//Include Inline player to preview tracks in admin
			wp_enqueue_style( $this->plugin_slug .'-soundmanager2-inline-player', plugins_url( 'assets/css/inline-player.css', __FILE__ ), array(), Ad_Music_Player_Lite::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if (( $this->plugin_screen_hook_suffix == $screen->id )||($screen->post_type==Ad_Music_Player_Lite::MAIN_TAXONOMY)) {
			wp_enqueue_script('jquery');
			
			wp_enqueue_script('thickbox');
				
			wp_enqueue_script( 'wp-color-picker' );
		
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery','media-upload','thickbox' ), Ad_Music_Player_Lite::VERSION );

			//add custom variables to admin.js, use custom_js_var.var_name 
			$custom_js_var = array( 'plugin_url' => plugins_url('', dirname(__FILE__) ) );
			wp_localize_script( $this->plugin_slug . '-admin-script', 'custom_js_var', $custom_js_var );	

			wp_enqueue_script( $this->plugin_slug . '-soundmanager2-script', plugins_url( 'soundmanager/soundmanager2-nodebug-jsmin.js', dirname(__FILE__) ), array(  $this->plugin_slug . '-admin-script' ), Ad_Music_Player_Lite::VERSION );
			
			//Include Inline player to preview tracks in admin
			wp_enqueue_script( $this->plugin_slug . '-soundmanager2-inline-player', plugins_url( 'assets/js/inline-player.js', __FILE__ ), array(  $this->plugin_slug . '-soundmanager2-script', $this->plugin_slug . '-admin-script' ), Ad_Music_Player_Lite::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
/* 		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Plugin Settings', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
		 */
		 
		/*
		 * Add a settings page for this plugin as Plugin Taxonomy sub-page
		 */		 
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'edit.php?post_type='.Ad_Music_Player_Lite::MAIN_TAXONOMY, 
			__( 'Ad Music Player Settings', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'edit.php?post_type='.Ad_Music_Player_Lite::MAIN_TAXONOMY.'&page='.$this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			array(
				'buy_full' => '<a href="http://codecanyon.net/item/ad-music-player/7496936?ref=circlewaves" target="_blank">' . __( 'Buy Full Version', $this->plugin_slug ) . '</a>'
			),			
			$links
		);

	}
	
  /**
   * Add buttons to TinyMCE and register TinyMCE plugin, see admin/assets/js/tinymce-plugins
   *
   * @since    1.0.0
   */
  public function amplayer_tinymce_buttons() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ){
			add_filter( 'mce_external_plugins', array( $this, 'amplayer_tinymce_add_buttons' ) );
			add_filter( 'mce_buttons', array( $this, 'amplayer_tinymce_register_buttons' ) );
		}
  }
	
  public function amplayer_tinymce_add_buttons($plugin_array) {
    $plugin_array['amplayer_tinymce_btns'] = plugins_url( 'assets/js/tinymce-plugins/tinymce-plugin.js', __FILE__ );
    return $plugin_array;
  }
	public function amplayer_tinymce_register_buttons($buttons) {
    array_push( $buttons, 'amplayer_tinymce_form' );
    return $buttons;
  }	


  /**
   * Pass variables into TinyMCE script
   *
   * @since    1.0.0
   */
	public function amplayer_tinymce_pass_vars(){
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ){
			$track_list=Ad_Music_Player_Lite::amplayer_get_tracks();
			?>
	<!-- TinyMCE A Plugin -->
	<script type='text/javascript'>
	var amplayer_shortcode_tracks={
		'track_list':<?php echo json_encode($track_list); ?>
	};
	</script>
	<!-- TinyMCE Shortcode Plugin -->
			<?php
		}	
	}	
	
	/**
	 * Init plugin options
	 *
	 * @since    1.0.0
	 */	
	public function admin_options_init() {
	

		// Sections
		add_settings_section( 'main-section', 'Player Options', array( $this, 'plugin_options_section_callback' ), 'ad-music-player' );
		add_settings_section( 'ad-section', 'Ad Options', array( $this, 'plugin_options_section_callback' ), 'ad-music-player' );
		add_settings_section( 'playlist-section', 'Playlist Options', array( $this, 'plugin_options_section_callback' ), 'ad-music-player' );
		add_settings_section( 'style-section', 'Player Style', array( $this, 'plugin_options_section_callback' ), 'ad-music-player' );

		// Handle plugin options
		foreach(Ad_Music_Player_Lite::$pluginSettings as $setting){
			if((isset($setting['hidden']))&&($setting['hidden']==1)){
				// Hidden option
			}else{
				// Register Settings
				register_setting( 'amplayer_settings', $setting['name'] );
				// Fields
				add_settings_field( $setting['name'], $setting['title'], array( $this, 'setting_field_callback' ), 'ad-music-player', $setting['section'], array('name'=>$setting['name'],'field'=>$setting['field']) );
			}
		}

	 
	}	

	/**
	 * Main section callback
	 *
	 * @since    1.0.0
	 */	
	public function plugin_options_section_callback($args) {
		$section_id=$args['id'];
		switch($section_id){
			case 'main-section':?>
				<p>Configure main player options</p>
			<?php
			break;
			case 'ad-section':?>
				<p>Configure ad</p>
			<?php
			break;			
			case 'playlist-section':?>
				<p>Set playlist options such as repeat and sort order</p>
			<?php
			break;
			case 'style-section':?>
				<p>Choose player colors</p>
			<?php
			break;							
		}
	}
	

 	/**
	 * Generate setting field
	 *
	 * @since    1.0.0
	 */	
	public function setting_field_callback($args) {
		$setting_value = esc_attr( get_option( $args['name'] ) );
		$field=$args['field'];
		if(isset($field['class'])){
			$field_class=$field['class']?$field['class']:'';
		}else{
			$field_class='';
		}
		if(isset($field['description'])){
			$field_descr=$field['description']?('<span class="description">'.$field['description'].'</span>'):"";		
		}else{
			$field_descr='';
		}
		switch($field['type']){
			case 'checkbox':
			?>
				<input class="<?php echo $field_class;?>" type="checkbox" name="<?php echo $args['name'];?>" value="1" <?php checked( $setting_value, '1', true);?> />
				<?php echo $field_descr;?>
			<?php
			break;
			case 'radio':
				if(is_array($field['options'])){
				?>
				<?php echo $field_descr;?>
				<?php
					foreach($field['options'] as $k=>$v){
					?>
						<label><input class="<?php echo $field_class;?>" type="radio" name="<?php echo $args['name'];?>" value="<?php echo $k;?>" <?php checked( $setting_value, $k, true);?> /> <span><?php echo $v;?></span></label><br />
					<?php
					}
				}
			break;			
			case 'radio-image':
				if(is_array($field['options'])){
				?>
					<?php echo $field_descr;?>
					<div class="radio-image-wrapper">
				<?php
					foreach($field['options'] as $k=>$v){
					?>
						<div class="radio-image-item">
							<label><input class="<?php echo $field_class;?>" type="radio" name="<?php echo $args['name'];?>" value="<?php echo $k;?>" <?php checked( $setting_value, $k, true);?> /><span><?php echo $v[0];?><br /><img src="<?php echo plugins_url( 'assets/img/'.$v[1], __FILE__ );?>" /></span><label>
						</div>
					<?php
					}
				?>
					</div>
				<?php
				}
			break;			
			case 'dropdown':
				if(is_array($field['options'])){
				?>
					<select class="<?php echo $field_class;?>" name="<?php echo $args['name'];?>">
				<?php
					foreach($field['options'] as $k=>$v){
					?>
						<option value="<?php echo $k;?>" <?php selected( $setting_value, $k, true);?>><?php echo $v;?></option>
					<?php
					}				
				?>
					</select>
					<?php echo $field_descr;?>
				<?php
				}
			break;
			case 'text':
			?>
				<input class="<?php echo $field_class;?>" type="text" name="<?php echo $args['name'];?>" value="<?php echo $setting_value;?>" />
				<?php echo $field_descr;?>
			<?php	
			break;
			case 'textarea':
			?>
				<textarea class="<?php echo $field_class;?>" name="<?php echo $args['name'];?>"><?php echo $setting_value;?></textarea>
				<?php echo $field_descr;?>
			<?php	
			break;			
			case 'colorpicker':
				$default_color=$field['default-color']?('data-default-color="'.$field['default-color'].'"'):"";
			?>
				<input class="field-colorpicker <?php echo $field_class;?>" type="text" name="<?php echo $args['name'];?>" value="<?php echo $setting_value;?>" <?php echo $default_color;?> />
				<?php echo $field_descr;?>
			<?php
			break;			
			default:
			?>
				<input class="regular-text" type="text" name="<?php echo $args['name'];?>" value="<?php echo $setting_value;?>" />
				<?php echo $field_descr;?>
			<?php
			break;
		}
	}	

 	/**
	 * Add filter for change text for 'Insert into Post' button inside of Thickbox 
	 *
	 * @since    1.0.0
	 */	
	public function popup_change_button_text_filter() {
		global $pagenow;
		if ('media-upload.php' == $pagenow || 'async-upload.php' == $pagenow) {
			// Now we'll replace the 'Insert into Post Button inside Thickbox' 
			add_filter( 'gettext', array($this, 'popup_change_button_text') , 1, 2 );
		}
	}
	
 	/**
	 * Change text for 'Insert into Post' button inside of Thickbox 
	 *
	 * @since    1.0.0
	 */		
	function popup_change_button_text($translated_text, $text ) {	
		if ( 'Insert into Post' == $text ) {
			$referer = strpos( wp_get_referer(), 'homepage-settings' );
			if ( $referer != '' ) {
				return __('Use this track',$this->plugin_slug );
			}
		}

		return $translated_text;
	}	
	
}
