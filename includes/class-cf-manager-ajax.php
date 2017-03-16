<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * CF_Ajax class.
 */
class CF_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'add_endpoint') );
		add_action( 'template_redirect', array( __CLASS__, 'do_cf_ajax'), 0 );

		// JM Ajax endpoints
		add_action( 'cf_ajax_upload_file', array( $this, 'upload_file' ) );

		// BW compatible handlers
		add_action( 'wp_ajax_cf_upload_file', array( $this, 'upload_file' ) );
                add_action( 'wp_ajax_nopriv_cf_upload_file', array( $this, 'upload_file' ) );
                add_action( 'wp_ajax_cf_send_form', array( $this, 'send_form' ) );
                add_action( 'wp_ajax_nopriv_cf_send_form', array( $this, 'send_form' ) );
	}

	/**
	 * Add our endpoint for frontend ajax requests
	 */
	public static function add_endpoint() {
		add_rewrite_tag( '%cf-ajax%', '([^/]*)' );
		add_rewrite_rule( 'cf-ajax/([^/]*)/?', 'index.php?cf-ajax=$matches[1]', 'top' );
		add_rewrite_rule( 'index.php/cf-ajax/([^/]*)/?', 'index.php?cf-ajax=$matches[1]', 'top' );
	}

	/**
	 * Get JM Ajax Endpoint
	 * @param  string $request Optional
	 * @param  string $ssl     Optional
	 * @return string
	 */
	public static function get_endpoint( $request = '%%endpoint%%', $ssl = null ) {
		if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
			$endpoint = trailingslashit( home_url( '/index.php/cf-ajax/' . $request . '/', 'relative' ) );
		} elseif ( get_option( 'permalink_structure' ) ) {
			$endpoint = trailingslashit( home_url( '/cf-ajax/' . $request . '/', 'relative' ) );
		} else {
			$endpoint = add_query_arg( 'cf-ajax', $request, trailingslashit( home_url( '', 'relative' ) ) );
		}
		return esc_url_raw( $endpoint );
	}

	/**
	 * Check for WC Ajax request and fire action
	 */
	public static function do_cf_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['cf-ajax'] ) ) {
			 $wp_query->set( 'cf-ajax', sanitize_text_field( $_GET['cf-ajax'] ) );
		}

   		if ( $action = $wp_query->get( 'cf-ajax' ) ) {
   			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			// Not home - this is an ajax endpoint
			$wp_query->is_home = false;

   			do_action( 'cf_ajax_' . sanitize_text_field( $action ) );
   			die();
   		}
	}	

	/**
	 * Upload file via ajax
	 *
	 * No nonce field since the form may be statically cached.
	 */
	public function upload_file() {
		$data = array( 'files' => array() );

		if ( ! empty( $_FILES ) ) {
			foreach ( $_FILES as $file_key => $file ) {
				$files_to_upload = cf_prepare_uploaded_files( $file );
				foreach ( $files_to_upload as $file_to_upload ) {
					$uploaded_file = cf_upload_file( $file_to_upload, array( 'file_key' => $file_key ) );

					if ( is_wp_error( $uploaded_file ) ) {
						$data['files'][] = array( 'error' => $uploaded_file->get_error_message() );
					} else {
						$data['files'][] = $uploaded_file;
					}
				}
			}
		}

		wp_send_json( $data );
	}
        
        /**
	 * Send form via ajax
	 *
	 */
	public function send_form() {
		$data = array( 'result' => false, 'html' => '' );
                
                $car_name                    = !empty($_POST['car_name']) ? sanitize_text_field($_POST['car_name']) : '';//for example 'Alfa Romeo' -> 'alfa-romeo'

		$parent_id                  = !empty($_POST['car_brand']) ? (int)$_POST['car_brand'] : '';
		$displacement               = !empty($_POST['displacement']) ? sanitize_text_field($_POST['displacement']) : '';
		$power                      = !empty($_POST['power']) ? (int)$_POST['power'] : '';
		$year                       = !empty($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
		$mileage                    = !empty($_POST['mileage']) ? sanitize_text_field($_POST['mileage']) : '';
		$car_model                  = !empty($_POST['car_model']) ? sanitize_text_field($_POST['car_model']) : '';
		$tank_type_size             = !empty($_POST['tank_type_size']) ? sanitize_text_field($_POST['tank_type_size']) : '';
		$type_of_gas_system         = !empty($_POST['type_of_gas_system']) ? (int)$_POST['type_of_gas_system'] : '';
		$range_in_gas_mode          = !empty($_POST['range_in_gas_mode']) ? sanitize_text_field($_POST['range_in_gas_mode']) : '';
		$car_year                   = !empty($_POST['car_year']) ? sanitize_text_field($_POST['car_year']) : '';
		$car_month                  = !empty($_POST['car_month']) ? sanitize_text_field($_POST['car_month']) : '';
		$description                = !empty($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
		$arr_imgs                   = !empty($_POST['imgs']) ? $_POST['imgs'] : '';
                
                if( $parent_id && $displacement && $power && $year && $mileage && $car_model && $tank_type_size && $type_of_gas_system && $range_in_gas_mode && $car_year && $car_month && $arr_imgs ){
                        $postTitle = 'autogas-galerie-' . sanitize_title($car_name) . '-' .sanitize_title($car_model);
                        $forLink = '/galerie-lpg/image/gallery/'.sanitize_title($car_name).'/'.sanitize_title($car_model);
                        
                        $imagesHtml = '';
                        
                        $cnt = 1;
                        $title_img = 'your image title';
                        $title_img_mini = 'your image title again for graceful degradation';
                        for ( $i = 0; $i < count($arr_imgs); ++$i ) {
                            
                                $this->save_image($_POST['car_name'], $car_model, $cnt, $arr_imgs[$i]);
                                                                
                                $imagesHtml .= '<li>';
                                $imagesHtml .= '<a class="thumb" title="'. $title_img .'" href="/galerie-lpg/image/gallery/'. $_POST['car_name'] .'/'. $car_model .'/'. $cnt .'.jpg">';
                                $imagesHtml .= '<img src="/galerie-lpg/image/gallery/'. $_POST['car_name'] .'/'. $car_model .'/mini/'. $cnt .'.jpg" alt="'. $title_img_mini .'" />';
                                $imagesHtml .= '</a>&nbsp;';
                                $imagesHtml .= '<div class="caption"></div>';
                                $imagesHtml .= '</li>';
                                $cnt++;
                        }
                        $object_brc = get_post($type_of_gas_system);
                        if( $object_brc !== NULL ){
                            $brc_title = $object_brc->post_title;
                            $brc_guid = get_permalink($object_brc);
                        }else{
                            $brc_title = $brc_guid = '';
                        }
                        
                        ob_start();
                        require_once( CAR_FORM_DIR . '/includes/view/content.php' );
                        $content = ob_get_clean();

                        $post_data = array(
                                'post_title'	=> $car_name . ' ' . $car_model,
                                'post_name'	=> $postTitle,
                                'post_content'	=> $content,
                                'post_status'	=> 'publish',
                                'comment_status'=> 'closed',
                                'post_type'	=> 'page',
                                'post_author'	=> is_user_logged_in() ? get_current_user_id() : 1,//because admin with id=1
                                'post_parent'	=> $parent_id
                        );

                        $post_id = wp_insert_post( $post_data );
                        if ( is_wp_error( $post_id ) ) {
                                $data['html'] = $post_id->get_error_message();
                        }else{
                                update_post_meta($post_id, 'show-page-header', 'false');
                                
                                $termMenu = get_term_by('name', $car_name, 'nav_menu');

                                $args = array(
                                        'menu-item-object'      => 'page',                                    
                                        'menu-item-type'        => 'post_type',
                                        'menu-item-object-id'   => $post_id,
                                        'menu-item-status'      => 'publish',
                                );
                                $menu_id = wp_update_nav_menu_item($termMenu->term_id, 0, $args);
                                //Sort new list menu
                                $count = 1;
                                //(Version #1)
//                                $menu_items = wp_get_nav_menu_items( $car_name, array( 'orderby'=> 'title', 'output' => ARRAY_A, 'output_key' => 'title') );
                               
//                                foreach ($menu_items as $m_k => $m_val) {
//                                        $menu_post = array( 'ID' => $m_val->ID, 'menu_order' => $count );
//                                        wp_update_post( $menu_post );
//
//                                        $count++;
//                                }
                                
                                //(Version #2)
                                $menu_items = get_objects_in_term( $termMenu->term_id, 'nav_menu' );
                                if( !empty($menu_items) ){
                                        global $wpdb;
                                        $sql = "SELECT PO.ID AS nav_id, (SELECT PO1.post_title AS wp_post_title FROM {$wpdb->posts} PO1 WHERE PO1.ID = wpPm.meta_value) AS car_title FROM {$wpdb->posts} AS PO INNER JOIN {$wpdb->prefix}postmeta AS wpPm ON PO.ID=wpPm.post_id WHERE PO.ID IN(". implode(",", $menu_items) .") AND wpPm.meta_key = '_menu_item_object_id' ORDER BY car_title;";
                                        $new_items = $wpdb->get_results( $sql, ARRAY_A );
                                        if( !empty($new_items) ){
                                            foreach ($new_items as $key => $row) {
                                                $car_title[$key] = $row['car_title'];
                                            }
                                            array_multisort($car_title, SORT_ASC, SORT_NATURAL, $new_items);
                                            if( !empty($new_items) ){
                                                foreach ($new_items as $key => $val) {
                                                        $menu_post = array( 'ID' => (int)$val['nav_id'], 'menu_order' => $count );
                                                        wp_update_post( $menu_post );

                                                        $count++;
                                                }
                                            }
                                        }
                                }
                                
                                $data['result'] = true;
                                $data['html'] = sprintf( __('Your page is successfully created. You can go %sthere%s.'), '<a href="'. get_permalink($post_id) .'">', '</a>' );
                        }
                }else{
                        $data['html'] = __('Not all fields are filled.', TEXT_DOMAIN);
                }                
                
                exit(json_encode($data));
	}
        
        /**
	 * Save image to server and create folders
	 * @param  string  $car_name
	 * @param  string  $car_model
	 * @return boolean
	 */
	public function save_image( $car_name, $car_model, $file_name = '1', $old_path ) {
                if( !$car_name || !$car_model || !$old_path ){
                    return false;
                }
                $image_path = cf_image_path();
            
		$path = $image_path . 'gallery/'.$car_name.'/';
      
		if ( !file_exists($path) ) {
			mkdir($path, 0755, true);
		}
		$path .= $car_model.'/';
		if ( !file_exists($path) ) {
			mkdir($path, 0755);
		}
		$pathMini = $path.'/mini/';
		if ( !file_exists($pathMini) ) {
			$res = mkdir($pathMini, 0755);
		}

                $arr_images = getimagesize($old_path);//$arr_images[0] - width, $arr_images[1] - height
                $height = 375;
                
                $width = $arr_images[0] > $arr_images[1] ? 500 : 282;//For horizontal or vertical
                
		$this->resize_image($path.$file_name.'.jpg', $old_path, $width, $height, 100, true);//500, 375
		$this->resize_image($pathMini.$file_name.'.jpg', $old_path, $arr_images[0], $arr_images[1], 100, false);//92, 72
                return true;
	}
        
        /**
	 * Resize Image
	 * @param  string  $newPath
	 * @param  string  $oldPath
	 * @param  integer $width
	 * @param  integer $height 
	 * @param  integer $quality
	 * @param  boolean $addStamp
	 * @return void          
	 */
	private function resize_image($newPath = '', $oldPath = '', $width = 92, $height = 72, $quality = 100, $addStamp = false) {
                if(exif_imagetype($oldPath) == IMAGETYPE_JPEG){
			$im = imagecreatefromjpeg($oldPath);
		} else {
			$im = imagecreatefrompng($oldPath);
		}
		$im1 = imagecreatetruecolor($width,$height);
		imagecopyresampled($im1,$im,0,0,0,0,$width,$height,imagesx($im),imagesy($im));

		if ( $addStamp === true ) {
			$stamp = imagecreatefrompng(CAR_FORM_DIR . '/assets/img/stamp.png');
			imagecopy($im1, $stamp, imagesx($im1) - imagesx($stamp) - 10, imagesy($im1) - imagesy($stamp) - 10, 0, 0, imagesx($stamp), imagesy($stamp));
		}

		imagejpeg($im1,$newPath,$quality);
		imagedestroy($im);
		imagedestroy($im1);
	}
}

new CF_Ajax();