<?php
/*
* Plugin Name:       Car Form
* Plugin URI:        http://selfit.org/
* Description:       Create form for upload image and add info for car
* Version:           1.0
* Author:            Nikolay Toloknov
* Author URI:        http://selfit.org/
* Text Domain:       car-form, uploadfile
* Domain Path:       /languages
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists('Car_From') ) {
    
    register_activation_hook(   __FILE__, array( 'Car_From', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Car_From', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'Car_From', 'uninstall' ) );
    
    class Car_From {
        
        /** @var Car_From single instance of this plugin */
        protected static $instance;
        
        public function __construct() { 
            
            $this->define_constants();
            $this->includes();
            // Switch theme
            add_action( 'after_switch_theme', array( 'CF_Ajax', 'add_endpoint' ), 10 );
            add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );
            
            // Internationalize the text strings used.
            add_action('plugins_loaded', array( $this, 'load_translation'));
        }
        
        /* Set the constants needed by the plugin. */
        private function define_constants() {
            /** plugin version number */
            $this->define('CF_VERSION', '1.0');
            $this->define('CAR_FORM_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
            $this->define('CAR_FORM_URL', untrailingslashit(plugins_url('/', __FILE__)));
            $this->define('TEXT_DOMAIN', 'car-form');
        }
        
        /**
        * Define constant if not already set.
        *
        * @param  string $name
        * @param  string|bool $value
        */
        private function define($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
        
        /**
        * Include required core files used in admin and on the frontend.
        */
        public function includes() {
            require_once( CAR_FORM_DIR . '/includes/cf-helpers.php' );
            require_once( CAR_FORM_DIR . '/includes/class-cf-manager-ajax.php' );
            require_once( CAR_FORM_DIR . '/includes/class-cf-shortcodes.php' );
            
            if( is_admin() ){
                require_once( CAR_FORM_DIR . '/includes/admin/class-car-form-admin.php' );
            }
        }
    
        /**
         * Do things on plugin activation.
         */
        public static function activate() {
            CF_Ajax::add_endpoint();
            flush_rewrite_rules();
        }
        
        /**
         * Flush permalinks on plugin deactivation.
         */
        public static function deactivate() {
            flush_rewrite_rules();
        }        
                
        public static function uninstall() {
            if (!current_user_can('activate_plugins')){
                return;
            }
            check_admin_referer('bulk-plugins');
            if (__FILE__ != WP_UNINSTALL_PLUGIN){
                return;
            }
        }
        
        /**
         * Main Super Car_From Instance, ensures only one instance is/can be loaded
         * @return Car_From
         */
        public static function instance() {
                if ( is_null( self::$instance ) ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }
        

        /**
         * @return __FILE__
         */
        protected function get_file() {
                return __FILE__;
        } 
        
        /**
         * Load plugin text domain.
         */
        public function load_translation() {                
                load_plugin_textdomain( 'car-form', false, dirname( plugin_basename( $this->get_file() ) ) . '/languages' );
        }

    }
    
    /**
    * Returns the One True Instance of Car_From
    */
    function car_form() {
           return Car_From::instance();
    }
    
    car_form();
}