<?php
/**
 * Ad Music Player Lite
 *
 * @package   Ad_Music_Player_Lite
 * @author    Circlewaves Team <support@circlewaves.com>
 * @license   GPL-2.0+
 * @link      http://circlewaves.com
 * @copyright 2014 Circlewaves Team <support@circlewaves.com>
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `ad-music-player-admin.php`
 *
 *
 * @package   Ad_Music_Player_Lite
 * @author    Circlewaves Team <support@circlewaves.com>
 */
class Ad_Music_Player_Lite {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ad-music-player-lite';
	
	/**
	 * Form ID for shortcode.
	 *
	 * Allows to use several players on one page
	 *
	 * @since    1.0.0
	 *
	 * @var      int
	 */
	protected $amplayer_form_uid = 0;	

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Ad Music Player Taxonomy
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const MAIN_TAXONOMY = 'amplayer_track';		
	
	/**
	 * Plugin Settings, used on Plugin Settings page
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	public static $pluginSettings=array(
		array(
			'name'=>'amplayer-plugin-version',
			'hidden'=>1
		),	
		array(
			'name'=>'amplayer-plugin-html',
			'hidden'=>1
		),			
		array(
			'name'=>'amplayer-player-enable', // enbable/disable player
			'title'=>'Enable Player',
			'section'=>'main-section',
			'field'=>array(
				'type'=>'checkbox'
			)
		),
		array(
			'name'=>'amplayer-autoplay', 
			'hidden'=>1		
		),			
		array(
			'name'=>'amplayer-volume-value', 
			'title'=>'Initial Volume',
			'section'=>'main-section',
			'field'=>array(
				'type'=>'text',
				'class'=>'small-text',
				'description'=>'%'
			)	
		),
		array(
			'name'=>'amplayer-ad-enable', // enbable/disable ad
			'title'=>'Enable Ad',
			'section'=>'ad-section',
			'field'=>array(
				'type'=>'checkbox'
			)
		),
		array(
			'name'=>'amplayer-ad-width', 
			'title'=>'Ad Area Width',
			'section'=>'ad-section',
			'field'=>array(
				'type'=>'text',
				'class'=>'small-text',
				'description'=>'px'
			)				
		),		
		array(
			'name'=>'amplayer-ad-height', 
			'title'=>'Ad Area Height',
			'section'=>'ad-section',
			'field'=>array(
				'type'=>'text',
				'class'=>'small-text',
				'description'=>'px'
			)				
		),
		array(
			'name'=>'amplayer-ad-code', // Ad Code
			'title'=>'Ad Code',
			'section'=>'ad-section',
			'field'=>array(
				'type'=>'textarea',
				'description'=>'Shortcodes are supported in the <a href="http://codecanyon.net/item/ad-music-player/7496936?ref=circlewaves" target="_blank">FULL version</a>'
			)
		),				
		array(
			'name'=>'amplayer-playlist-repeat', 
			'title'=>'Repeat playlist',
			'section'=>'playlist-section',
			'field'=>array(
				'type'=>'checkbox',
				'description'=>'Automatically repeat playlist'
			)
		),
		array(
			'name'=>'amplayer-playlist-order', 
			'title'=>'Playlist sort order',
			'section'=>'playlist-section',
			'field'=>array(
				'type'=>'dropdown',
				'options'=>array(
					'ASC'=>'Asc',
					'DESC'=>'Desc'
				),
				'description'=>'Sort tracks in accordance to "Sort Order" parameter. Shuffle is available in the <a href="http://codecanyon.net/item/ad-music-player/7496936?ref=circlewaves" target="_blank">FULL version</a>'
			)			
		),
		array(
			'name'=>'amplayer-style-color-background', 
			'title'=>'Background color',
			'section'=>'style-section',
			'field'=>array(
				'type'=>'colorpicker',
				'default-color'=>'#565555'
			)	
		),
		array(
			'name'=>'amplayer-style-color-main-normal', 
			'title'=>'Normal button color',
			'section'=>'style-section',
			'field'=>array(
				'type'=>'colorpicker',
				'default-color'=>'#b1ada8'
			)	
		),
		array(
			'name'=>'amplayer-style-color-main-active', 
			'title'=>'Active button color',
			'section'=>'style-section',
			'field'=>array(
				'type'=>'colorpicker',
				'default-color'=>'#ffc600'
			)	
		),
		array(
			'name'=>'amplayer-style-color-text', 
			'title'=>'Text color',
			'section'=>'style-section',
			'field'=>array(
				'type'=>'colorpicker',
				'default-color'=>'#ffffff'
			)	
		),		
		array(
			'name'=>'amplayer-style-track-button-color-normal', 
			'title'=>'Track extra button color',
			'section'=>'style-section',
			'field'=>array(
				'type'=>'colorpicker',
				'default-color'=>'#ffc600'
			)	
		)	
	);		
	
	/**
	 * Plugin Settings Values
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	public static $pluginDefaultSettings=array(
		'plugin-version'=>array(
			'name'=>'amplayer-plugin-version',
			'value'=>'1.0.0'
		),
		'plugin-html'=>array(
			'name'=>'amplayer-plugin-html',
			'value'=>'
								<div class="amplayer-container" id="amplayer-container-[AMPLAYER_UID]" data-amplayer-uid="[AMPLAYER_UID]">
									<div class="amplayer-container-inside">
										<div class="amplayer-row-content">
											<div class="amplayer-details-container">
												<div class="amplayer-details-track">
													<div class="amplayer-track-cover"></div>
													<div class="amplayer-track-info">
														<div class="amplayer-track-name"></div>
														<div class="amplayer-track-artist"></div>
													</div>
												</div>
												<div class="amplayer-details-ad"></div>
											</div>
											<div class="amplayer-controls-container">
												<div class="amplayer-btn amplayer-btn-prev"></div>
												<div class="amplayer-btn amplayer-btn-play"></div>
												<div class="amplayer-btn amplayer-btn-next"></div>
											</div>
										</div>
										<div class="amplayer-row-footer">
											<div class="amplayer-links-container"></div>
											<div class="amplayer-volume-container"><span class="amplayer-volume-tooltip"></span><div class="amplayer-volume-slider"></div><span class="amplayer-volume-power"></span></div>
										</div>
									</div>
									<div class="amplayer-ad-container">
										[AMPLAYER_AD_CONTAINER]
									</div>
								</div>
			'
		),		
		'plugin-player-enable'=>array(
			'name'=>'amplayer-player-enable',
			'value'=>0
		),	
		'plugin-autoplay'=>array(
			'name'=>'amplayer-autoplay',
			'value'=>0
		),	
		'plugin-volume'=>array(
			'name'=>'amplayer-volume-value',
			'value'=>80
		),	
		'plugin-ad-enable'=>array(
			'name'=>'amplayer-ad-enable',
			'value'=>0
		),	
		'plugin-ad-width'=>array(
			'name'=>'amplayer-ad-width',
			'value'=>234
		),
		'plugin-ad-height'=>array(
			'name'=>'amplayer-ad-height',
			'value'=>60
		),		
		'plugin-ad-code'=>array(
			'name'=>'amplayer-ad-code',
			'value'=>''
		),		
		'plugin-playlist-repeat'=>array(
			'name'=>'amplayer-playlist-repeat',
			'value'=>1
		),
		'plugin-playlist-order'=>array(
			'name'=>'amplayer-playlist-order',
			'value'=>'ASC'
		),	
		'plugin-style-color-background'=>array(
			'name'=>'amplayer-style-color-background',
			'value'=>'#565555'
		),
		'plugin-style-color-main-normal'=>array(
			'name'=>'amplayer-style-color-main-normal',
			'value'=>'#b1ada8'
		),
		'plugin-style-color-main-active'=>array(
			'name'=>'amplayer-style-color-main-active',
			'value'=>'#ffc600'
		),		
		'plugin-style-color-text'=>array(
			'name'=>'amplayer-style-color-text',
			'value'=>'#ffffff'
		),
		'plugin-style-color-track-button-normal'=>array(
			'name'=>'amplayer-style-track-button-color-normal',
			'value'=>'#ffc600'
		)
	);

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );
		
		/*
		 *	Actions and filters for main plugin taxonomy and taxonomy metabox
		 */
		add_action( 'init', array( $this, 'create_plugin_post_type' ) );		
		//Add Cover Image Size
		add_action( 'init',  array( $this, 'add_plugin_image_size' ) );
		
		add_action( 'save_post', array($this,'main_taxonomy_save_post') );
		add_action( 'admin_notices', array( $this, 'main_taxonomy_notice' ) );
		add_filter( 'enter_title_here', array( $this, 'backend_change_default_title') );
		add_action('do_meta_boxes', array($this,'amplayer_change_imagebox_title'));
		// Change Admin Columns for Tracks post type
		add_filter( 'manage_edit-amplayer_track_columns', array( $this, 'amplayer_track_edit_columns') );
		add_action( 'manage_amplayer_track_posts_custom_column', array( $this, 'amplayer_track_columns'), 10, 2 );		
		// Make Admin Columns sortable
		add_filter( 'manage_edit-amplayer_track_sortable_columns', array( $this, 'amplayer_track_sortable_columns') );
		add_action( 'pre_get_posts', array( $this, 'amplayer_track_orderby') );  		
		
		

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
			//Add plugin options (it does nothing if option already exists)
			foreach(self::$pluginDefaultSettings as $k=>$v){
				add_option( self::$pluginDefaultSettings[$k]['name'], self::$pluginDefaultSettings[$k]['value'] );	
			}		
			
			//Always update plugin version
			update_option( self::$pluginDefaultSettings['plugin-version']['name'], self::$pluginDefaultSettings['plugin-version']['value'] );
			update_option( self::$pluginDefaultSettings['plugin-html']['name'], self::$pluginDefaultSettings['plugin-html']['value'] );

			flush_rewrite_rules();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
			flush_rewrite_rules();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}


	/**
	 * Register and enqueues public-facing JavaScript files and styles.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_and_scripts() {
	/*
		$amplayer_options=self::amplayer_get_settings();		
		if($amplayer_options['amplayer-player-enable']==1){
				wp_register_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
				wp_register_script( $this->plugin_slug . '-soundmanager2-script', plugins_url( 'soundmanager/soundmanager2-nodebug-jsmin.js', dirname(__FILE__) ), array( 'jquery' ), self::VERSION,true );				
				wp_register_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery','jquery-ui-slider',$this->plugin_slug . '-soundmanager2-script' ), self::VERSION,true );	
		}
		*/
	}
	
	/**
	 * Get plugin settings
	 *
	 * @since    1.0.0
	 */	
 	function amplayer_get_settings($is_js_friendly=0) {
		$amplayer_options=array();
		foreach(self::$pluginSettings as $setting){
			$option_key=$is_js_friendly?(str_replace('-','_',$setting['name'])):$setting['name']; // Replace "-" to "_" in array key to make array js friendly (to use it in localize_script)
			$amplayer_options[$option_key] = get_option( $setting['name'] );
		}
		return $amplayer_options;
	}	 
	
	/**
	 * Get all tracks
	 * 
	 * Params:
	 * $args['order'] - tracks sort order
	 * $args['tracklist'] - array of tracks ID's that should be returned, used with posts playlists
	 *
	 * @since    1.0.0
	 */	
 	function amplayer_get_tracks($args=null) {
//		global $post;
	//	global $wp_query;
		$amplayer_playlist=array();
		
		// WP_Query arguments
		$query_args = array (
			'post_type'              => self::MAIN_TAXONOMY,
			'post_status'            => 'publish',
			'meta_query'             => array(
																					array(
																						'key'       => 'amplayer_track_url',
																					)
			)				
		);	
		
		$args['order']=$args['order']?$args['order']:'ASC';
		if($args['order']=='rand'){
			$query_args['orderby']='rand';
		}else{
			$query_args['order']=$args['order'];
			$query_args['orderby']='menu_order';			
		}
		if((isset($args['tracklist']))&&(is_array($args['tracklist']))){
			$query_args['post__in']=$args['tracklist'];
		}
		// The Query
		$tracks_query = new WP_Query( $query_args );

		// The Loop
		if ( $tracks_query->have_posts() ) {
			while ( $tracks_query->have_posts() ) {
				$tracks_query->the_post();
				$post_id=get_the_ID();
				$track_url=get_post_meta( $post_id, 'amplayer_track_url', true );
				$track_title= get_the_title($post_id);
				$track_artist=get_post_meta( $post_id, 'amplayer_track_artist', true );
				$track_cover=get_the_post_thumbnail($post_id, 'amplayer-track-cover');
				$track_button=get_post_meta( $post_id, 'amplayer_track_button', true );
				if($track_button['name']&&$track_button['link']){
					$track_button='<a class="amplayer-track-link" href="'.$track_button['link'].'" title="'.$track_button['name'].'" target="_blank">'.$track_button['name'].'</a>';
				}
				array_push($amplayer_playlist,array('id'=>$post_id,'title'=>$track_title,'track'=>$track_url,'artist'=>$track_artist,'cover'=>$track_cover,'button'=>$track_button));
			}
		} else {
			// no posts found
		}

		// Restore original Post Data
		wp_reset_postdata();	
		
		return $amplayer_playlist;
	}	 	
		
	
	
	/**
	 * Add plugin image size
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_image_size() {
	add_image_size( 'amplayer-track-cover', 60, 60, true ); 
	}	
	
	/**
	 * Create plugin post type
	 *
	 * @since    1.0.0
	 */
	public function create_plugin_post_type() {
	
	
	
			$labels = array(
					'name' => 'Ad Tracks',
					'singular_name' => 'Track',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Track',
					'edit_item' => 'Edit Track',
					'new_item' => 'New Track',
					'view_item' => 'View Track',
					'search_items' => 'Search Tracks',
					'not_found' =>  'No Tracks found',
					'not_found_in_trash' => 'No Tracks in the trash',
					'parent_item_colon' => '',
			);
	 
			register_post_type( self::MAIN_TAXONOMY, array(
					'labels' => $labels,
					'public' => false,
					'publicly_queryable' => false,
					'show_ui' => true,
					'exclude_from_search' => true,
					'query_var' => false,
					'capability_type' => 'post',
					'has_archive' => false,
					'hierarchical' => false,
					'menu_position' => 100,
					'menu_icon' => 'dashicons-feedback',
					'supports' => array( 'title','thumbnail','page-attributes'),
		      'register_meta_box_cb' => array($this, 'tracks_taxonomy_metabox') // Callback function for custom metaboxes
					) 
			);
			
		// refresh rewrite rules to solve 404 error (use soft flush)
			flush_rewrite_rules(false);
	}
	
	
	/**
	 * Add metabox to Projects
	 *
	 * @since    1.0.0
	 */
	public function tracks_taxonomy_metabox() {
			add_meta_box( 'ad_music_player_track_metabox', __('Track Details',$this->plugin_slug), array($this,'main_taxonomy_metabox_form'), self::MAIN_TAXONOMY, 'normal', 'high' );
	}		
	
	/**
	 * Render metabox
	 *
	 * @since    1.0.0
	 */
	function main_taxonomy_metabox_form() {
    $post_id = get_the_ID();
		$the_post = get_post($post_id );
		$current_user = wp_get_current_user();
		
    $track_url = get_post_meta( $post_id, 'amplayer_track_url', true );
		$track_url = isset( $track_url ) ? esc_attr( $track_url ) : '';  
		
		$track_artist = get_post_meta( $post_id, 'amplayer_track_artist', true );
		$track_artist = isset( $track_artist ) ? esc_attr( $track_artist ) : '';  		

		$track_button = get_post_meta( $post_id, 'amplayer_track_button', true );
		$track_button['name'] = isset( $track_button['name'] ) ? esc_attr( $track_button['name'] ) : '';  
		$track_button['link'] = isset( $track_button['link'] ) ? esc_attr( $track_button['link'] ) : '';  
 
    wp_nonce_field( 'amplayer_track_save', 'amplayer_track_nonce' );
    ?>
    <p>
        <label for="amplayer_track_url"><?php _e('Track URL',$this->plugin_slug);?><br /><small><?php _e('If you want to stream online radio, you should put "/;" at the end of your stream URL after port number, eg: <strong>http://example.com:80/;</strong>',$this->plugin_slug);?></small></label><br />
        <input id="amplayer_track_url" type="text" value="<?php echo $track_url; ?>" name="amplayer_track_url" size="40" placeholder="<?php _e('http://example.com/tracks/new-track.mp3',$this->plugin_slug);?>" />
				<input id="upload_track_button" type="button" class="button" value="<?php _e( 'Upload Track',$this->plugin_slug ); ?>" />
    </p>	
		<div id="amplayer-track-preview-wrapper" class="<?php echo $track_url?'amplayer-visible':'amplayer-hidden'?>">
			<ul class="graphic"><li><a id="amplayer-track-preview" class="sm2_link" href="<?php echo $track_url;?>" type="audio/mpeg"><?php _e('Play',$this->plugin_slug);?></a></li></ul>		
		</div>
    <p>
        <label for="amplayer_track_artist"><?php _e('Artist',$this->plugin_slug);?></label><br />
        <input id="amplayer_track_artist" type="text" value="<?php echo $track_artist; ?>" name="amplayer_track_artist" size="40" placeholder="<?php _e('Artist Name',$this->plugin_slug);?>" />
    </p>
    <p>
        <label><?php _e('Extra Button',$this->plugin_slug);?></label><br />
        <input id="amplayer_track_button_name" type="text" value="<?php echo $track_button['name']; ?>" name="amplayer_track_button[name]" size="40" placeholder="<?php _e('Button Text, eg: Buy this track',$this->plugin_slug);?>" />
				<input id="amplayer_track_button_link" type="text" value="<?php echo $track_button['link']; ?>" name="amplayer_track_button[link]" size="40" placeholder="<?php _e('Button Link, eg: http://example.com/buy',$this->plugin_slug);?>" />
    </p>		


    <?php
	}	
	
	/**
	 * Save track metabox data
	 *
	 * @since    1.0.0
	 */
	function main_taxonomy_save_post( $post_id ) {
		global $wpdb;
				
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
 
    if ( !isset($_POST['amplayer_track_nonce']) || !wp_verify_nonce( $_POST['amplayer_track_nonce'], 'amplayer_track_save' ) )
        return;
 
    if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;
    }
 
 
		// Handle post saving
    if ( ! wp_is_post_revision( $post_id ) && get_post_type( $post_id )==self::MAIN_TAXONOMY ) {
			remove_action( 'save_post', array($this, 'main_taxonomy_save_post') ); 	
			
				// Track title
				$track_title=$_POST['post_title']?$_POST['post_title']:__('Track',$this->plugin_slug).' '.$post_id;	
				// Menu Order
				$menu_order=$_POST['menu_order']?$_POST['menu_order']:$wpdb->get_var("SELECT MAX(`menu_order`)+1 AS `menu_order` FROM `".$wpdb->posts."` WHERE post_type='".self::MAIN_TAXONOMY."'");				
				
				wp_update_post( array(
					'ID' => $post_id,
					'post_title' =>$track_title,
					'menu_order'=>$menu_order
				) );	
				
				
				if(empty( $_POST['amplayer_track_url'] )){
					// set a transient to show the users an admin message
					set_transient( 'amplayer_track_message', 'track_url_error' );
					
					// update the post set it to draft
						wp_update_post( array(
							'ID' => $post_id,
							'post_status' => 'draft'				
						) );						
						add_filter('redirect_post_location',array($this,'hide_default_post_save_message'));
							
				}else{
					delete_transient( 'amplayer_track_message' );
				}
			
			add_action( 'save_post', array($this,'main_taxonomy_save_post') );
		}
		
		if ( !empty( $_POST['amplayer_track_url'] ) ) {
			$track_url=(isset($_POST['amplayer_track_url']))?esc_attr($_POST['amplayer_track_url']):'';
			update_post_meta( $post_id, 'amplayer_track_url', $track_url );
		} else {
			delete_post_meta( $post_id, 'amplayer_track_url' );
		}
		
		if ( !empty( $_POST['amplayer_track_artist'] ) ) {
			$track_artist=(isset($_POST['amplayer_track_artist']))?esc_attr($_POST['amplayer_track_artist']):'';
			update_post_meta( $post_id, 'amplayer_track_artist', $track_artist );
		} else {
			delete_post_meta( $post_id, 'amplayer_track_artist' );
		}		
		
		if ( !empty( $_POST['amplayer_track_button'] ) ) {
			$track_button['name']=(isset($_POST['amplayer_track_button']['name']))?esc_attr($_POST['amplayer_track_button']['name']):'';
			$track_button['link']=(isset($_POST['amplayer_track_button']['link']))?esc_attr($_POST['amplayer_track_button']['link']):'';
			update_post_meta( $post_id, 'amplayer_track_button', $track_button );
		} else {
			delete_post_meta( $post_id, 'amplayer_track_button' );
		}		
		
	}	
	
	/**
	 * Show admin notice 
	 *
	 * @since    1.0.0
	 */
	function main_taxonomy_notice() {
		if ( get_transient( 'amplayer_track_message' ) == 'track_url_error' ) {
			?>
			<div class="error">
				<p><?php _e( 'Please enter Track URL',$this->plugin_slug); ?></p>
			</div>	
			<?php 
			delete_transient( 'amplayer_track_message' );
		}
		?>
			<?php
	}	

	/**
	 * Hide default message function
	 *
	 * @since    1.0.0
	 */	
	function hide_default_post_save_message($loc) {
	 return add_query_arg( 'message', 999, $loc );
	}  	
	
	/**
	 * Change Post Title placeholder for plugin taxonomy
	 *
	 * @since    1.0.0
	 */
	public function backend_change_default_title( $title ){
			$screen = get_current_screen();
			if ( $screen->post_type==self::MAIN_TAXONOMY ){
					$title = 'Track Title';
			}
			return $title;
	}	
	
	/**
	 * Change Featured Image metabox title
	 *
	 * @since    1.0.0
	 */
	public function amplayer_change_imagebox_title(){
			remove_meta_box( 'postimagediv', self::MAIN_TAXONOMY, 'side' );
			add_meta_box('postimagediv', __('Track Cover',$this->plugin_slug), 'post_thumbnail_meta_box', self::MAIN_TAXONOMY, 'advanced', 'high');
	}		

	/**
	 * Customize tracks list view (column titles)
	 *
	 * @since    1.0.0
	 */
	function amplayer_track_edit_columns( $columns ) {
			$columns = array(
					'cb' => '<input type="checkbox" />',
					'title' => 'Title',
					'track-url' => 'Track',
					'track-artist' => 'Artist',
					'track-cover' => 'Cover',
					'menu-order' => 'Sort Order'
			);
	 
			return $columns;
	}	
	
	/**
	 * Customize tracks list view (table content)
	 *
	 * @since    1.0.0
	 */
	function amplayer_track_columns( $column, $post_id ) {
			$track_url = get_post_meta( $post_id, 'amplayer_track_url', true );
			$track_artist = get_post_meta( $post_id, 'amplayer_track_artist', true );
			$the_post = get_post($post_id );
			switch ( $column ) {						
					case 'track-url':
							if ( ! empty( $track_url ) ){?>
								<ul class="graphic"><li><a class="sm2_link" href="<?php echo $track_url;?>#id=<?php echo $post_id?>" type="audio/mpeg"><?php _e('Play',$this->plugin_slug);?></a></li></ul>
								<?php	
							}					
					break;
					case 'track-artist':
							if ( ! empty( $track_artist ) ){
								echo $track_artist;
							}					
					break;		
					case 'track-cover':
							if ( has_post_thumbnail() ){
								echo the_post_thumbnail( 'amplayer-track-cover' );
							}else{
								echo '<img src="'.plugins_url( 'public/assets/img/ad-player-cover.png',dirname(__FILE__)).'" />';
							}					
					break;					
					case 'menu-order':
							echo (int)$the_post->menu_order;
					break;						
			}
	}
	
	
	
	/**
	 * Define sortable columns
	 *
	 * @since    1.0.0
	 */
	function amplayer_track_sortable_columns( $columns ) {
		$columns = array(
				'menu-order' => 'menu-order',
				'title' => 'title'
		);
		return $columns;
	}
	
	/**
	 * Handle sortable columns
	 *
	 * @since    1.0.0
	 */
 	function amplayer_track_orderby( $query ) {  
		if(!is_admin()){
			return;  
		}
		$post_type = $query->query['post_type'];

    if ( $post_type == self::MAIN_TAXONOMY) {
			$orderby = $query->get('orderby'); 

			if((!$orderby)||($orderby=='menu-order')){  
					$query->set('orderby','menu_order');  
					if(!$orderby){
						$query->set('order','ASC');  
					}
			}
		}  		
	}  		
	
	
	
	/**
	 * Init Player by Shortcode
	 *
	 * @since    1.0.0
	 */
 	public function amplayer_shortcode_init($atts=null) {  
	
		extract(shortcode_atts(array(
			'tracks' => ''
		), $atts));	
	
		$amplayer_options=self::amplayer_get_settings();


		if($amplayer_options['amplayer-player-enable']==1){
		
			$this->amplayer_form_uid++;
			$amplayer_form_uid=$this->amplayer_form_uid;
			
			//Add Ad-containers
			$amplayer_ad_container='';
			//Main ad-container
			if($amplayer_options['amplayer-ad-code']){
				$amplayer_ad_container.='<div class="amplayer-ad-main">'.$amplayer_options['amplayer-ad-code'].'</div>';
			}
		
			$amplayer_html=$amplayer_options['amplayer-plugin-html'];
			$amplayer_html=str_replace('[AMPLAYER_UID]', $amplayer_form_uid, $amplayer_html);		
			
			//add custom variables to public.js, use plugin_options.var_name 
			$plugin_options = self::amplayer_get_settings(1);				
			
			$tracks_args=array(
				'order'=>$amplayer_options['amplayer-playlist-order']
			);

			if($tracks){
				$tracks_args['tracklist']=explode(',',$tracks);
			}
								

				$plugin_options['amplayer_playlist']=self::amplayer_get_tracks($tracks_args);		
				
				//if playlist is empty - return;
				if(!$plugin_options['amplayer_playlist'] || !is_array($plugin_options['amplayer_playlist'])){
					return null;
				}

				$plugin_options['amplayer_plugin_url']=plugins_url('', dirname(__FILE__) );		
				$plugin_options['amplayer_uid']=$amplayer_form_uid;
				
				$amplayer_html=str_replace('[AMPLAYER_AD_CONTAINER]', $amplayer_ad_container, $amplayer_html);	

				//if autoplay is enabled - enable this option only for first player
				if($amplayer_form_uid>1){
					$plugin_options['amplayer_autoplay']=0;
				}
				//don't pass player html code into js (no needed)
				unset($plugin_options['amplayer_plugin_html']);
				//don't pass ad code into js (no needed)
				unset($plugin_options['amplayer_ad_code']);
				
				
				wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
				wp_enqueue_script( $this->plugin_slug . '-soundmanager2-script', plugins_url( 'soundmanager/soundmanager2-nodebug-jsmin.js', dirname(__FILE__) ), array( 'jquery' ), self::VERSION,true );				
				wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery','jquery-ui-slider',$this->plugin_slug . '-soundmanager2-script' ), self::VERSION,true );
				wp_localize_script( $this->plugin_slug . '-plugin-script', 'amplayer_plugin_options_'.$amplayer_form_uid, $plugin_options );				
			
		
			return $amplayer_html;
		}
	}



}

//Register shortcode [amplayer]
add_shortcode('admplayer', array( Ad_Music_Player_Lite::get_instance(),'amplayer_shortcode_init'));