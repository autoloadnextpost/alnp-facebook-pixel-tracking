<?php
/*
 * Plugin Name: Auto Load Next Post: Facebook Pixel Tracking
 * Plugin URI: https://github.com/AutoLoadNextPost/alnp-facebook-pixel-tracking
 * Version: 1.0.0
 * Description: Track your page views using Facebook Pixel with Auto Load Next Post.
 * Author: Auto Load Next Post
 * Author URI: https://autoloadnextpost.com
 * Developer: Sébastien Dumont
 * Developer URI: https://sebastiendumont.com
 * GitHub Plugin URI: https://github.com/AutoLoadNextPost/alnp-facebook-pixel-tracking
 *
 * Text Domain: alnp-facebook-pixel-tracking
 * Domain Path: /languages/
 *
 * Requires at least: 4.5
 * Tested up to: 4.9.2
 *
 * Copyright: © 2018 Sébastien Dumont
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'ALNP_FB_Pixel_Tracking' ) ) {
	class ALNP_FB_Pixel_Tracking {

		/**
		 * Plugin Version
		 *
		 * @access public
		 * @static
		 * @since  1.0.0
		 */
		public static $version = '1.0.0';

		/**
		 * Required Auto Load Next Post Version
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public $required_alnp = '1.4.8';

		/**
		 * @var ALNP_FB_Pixel_Tracking - the single instance of the class.
		 *
		 * @access protected
		 * @static
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main ALNP_FB_Pixel_Tracking Instance.
		 *
		 * Ensures only one instance of ALNP_FB_Pixel_Tracking is loaded or can be loaded.
		 *
		 * @access public
		 * @static
		 * @since  1.0.0
		 * @see    ALNP_FB_Pixel_Tracking()
		 * @return ALNP_FB_Pixel_Tracking - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'alnp-facebook-pixel-tracking' ), self::$version );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'alnp-facebook-pixel-tracking' ), self::$version );
		}

		/**
		 * Load the plugin.
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Initialize hooks.
		 *
		 * @access private
		 * @since  1.0.0
		 */
		private function init_hooks() {
			add_action( 'plugin_loaded', array( $this, 'check_alnp_installed' ) );
			add_action( 'init', array( $this, 'load_text_domain' ), 0 );

			add_action( 'wp_enqueue_scripts', array( $this, 'alnp_enqueue_scripts' ) );
		} // END init_hooks()

		/**
		 * Checks if Auto Load Next Post is installed.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return bool
		 */
		public function check_alnp_installed() {
			if ( ! defined( 'AUTO_LOAD_NEXT_POST_VERSION' ) || version_compare( AUTO_LOAD_NEXT_POST_VERSION, $this->required_alnp, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'alnp_not_installed' ) );
				return false;
			}
		} // END check_alnp_installed()

		/**
		 * Auto Load Next Post is Not Installed Notice.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function alnp_not_installed() {
			echo '<div class="error"><p>' . sprintf( __( 'Auto Load Next Post: Facebook Pixel Tracking requires $1%s v$2%s or higher to be installed.', 'alnp-facebook-pixel-tracking' ), '<a href="https://autoloadnextpost.com/" target="_blank">Auto Load Next Post</a>', $this->required_alnp ) . '</p></div>';
		} // END alnp_not_installed()

		/**
		 * Get the Plugin URL.
		 *
		 * @access public
		 * @static
		 * @since  1.0.0
		 * @return string
		 */
		public static function plugin_url() {
			return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
		} // END plugin_url()

		/**
		 * Load the plugin text domain once the plugin has initialized.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'alnp-facebook-pixel-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		} // END load_text_domain()

		/**
		 * Load JS only on the front end for a single post.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function alnp_enqueue_scripts() {
			if ( is_singular() && in_array( get_post_type(), $this->allowed_post_types() ) ) {
				wp_register_script( 'alnp-facebook-pixel-tracking', $this->plugin_url() . '/assets/js/alnp-facebook-pixel-tracking.js', array( 'jquery' ), '1.0.0' );
				wp_enqueue_script( 'alnp-facebook-pixel-tracking' );

				wp_localize_script( 'alnp-facebook-pixel-tracking', 'alnp_fb_pixel', array(
					'alnpVersion'   => AUTO_LOAD_NEXT_POST_VERSION,
					'pluginVersion' => self::$version,
				));
			}
		} // END alnp_enqueue_scripts()

		/**
		 * Returns allowed post types to track page views.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return array
		 */
		public function allowed_post_types() {
			return array( 'post' );
		} // END allowed_post_types()

	} // END class

} // END if class exists

return ALNP_FB_Pixel_Tracking::instance();
