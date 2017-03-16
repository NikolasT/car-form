<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * CF_Shortcodes class.
 */
class CF_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {                
		add_shortcode( 'show_car_form', array( $this, 'submit_car_form' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}
        
        /**
	 * Register and enqueue scripts and css
	 */
	public function frontend_scripts() {
            wp_enqueue_style( 'car_form_css', CAR_FORM_URL.'/assets/css/main.css', array(), '1.0' );
            $ajax_url         = CF_Ajax::get_endpoint();
            wp_register_script( 'jquery-iframe-transport', CAR_FORM_URL . '/assets/js/jquery-fileupload/jquery.iframe-transport.js', array( 'jquery' ), '1.8.3', true );
            wp_register_script( 'jquery-fileupload', CAR_FORM_URL . '/assets/js/jquery-fileupload/jquery.fileupload.js', array( 'jquery', 'jquery-iframe-transport', 'jquery-ui-widget' ), '9.11.2', true );
            wp_register_script( 'cf-front-js', CAR_FORM_URL . '/assets/js/front.min.js', array( 'jquery', 'jquery-fileupload' ), CF_VERSION, true );
            
            $args = array( 'name' => '', 'value' => '', 'extension' => 'jpg' );
            ob_start();
            require_once( CAR_FORM_DIR . '/includes/view/uploaded-file-html.php' );
            
            $js_field_html_img = ob_get_clean();
            $image_limit = 10;
            wp_localize_script( 'cf-front-js', 'CFParams', array(
				'ajax_url'              => $ajax_url,
				'default_ajax_url'      => admin_url('admin-ajax.php'),
				'js_field_html_img'     => esc_js( str_replace( "\n", "", $js_field_html_img ) ),
				'image_limit'           => $image_limit,
				'i18n_invalid_file_type'=> __( 'Invalid file type. Accepted types:', TEXT_DOMAIN ),
				'text_limit'            => __( 'You can only upload a maximum of '.$image_limit.' files', TEXT_DOMAIN ),
                                'required_image'        => __('Image is required', TEXT_DOMAIN),
                                'invalid_year'          => __('Year is invalid.', TEXT_DOMAIN),
                                'pending'               => __('Pending...', TEXT_DOMAIN),
            ) );
        }
        
	/**
	 * Show the car form
	 */
	public function submit_car_form($atts) {
                wp_enqueue_script('cf-front-js');
  
		$arr_cf_options = get_option( '_cf_options' ) ? get_option( '_cf_options' ) : array();

		ob_start();
                
                require_once( CAR_FORM_DIR . '/includes/view/form.php' );		
		return ob_get_clean();
	}
}

new CF_Shortcodes();