<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Car_Form
 *
 * Admin class
 *
 * @since 1.0
 */
class Car_Form_Admin {
    /**
     * Setup admin class
     */
    public function __construct() {
        // Add menu
        add_action('admin_menu', array(&$this, 'add_menu_items'));
    }
    function add_menu_items() {
        add_menu_page(__('Settings', TEXT_DOMAIN), __('Car Form', TEXT_DOMAIN), 'manage_options', 'car-form', array($this, 'car_form_settings_page'), '', 83);
        add_submenu_page('car-form', __('Car Form', TEXT_DOMAIN), __('Car Form Settings', TEXT_DOMAIN), 'manage_options', 'car-form', array($this, 'car_form_settings_page'));    
    }
    
    public function car_form_settings_page(){
	?>
        <div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<?php 
                if( !empty( $_POST['is_car_settings'] ) ){
                    $data['parent_page_id'] = !empty($_POST['car_pages']) ? $_POST['car_pages'] : '';
                    $data['brc_page_id'] = !empty($_POST['brc_pages']) ? $_POST['brc_pages'] : '';
                    $data['image_path'] = !empty($_POST['image_path']) ? $_POST['image_path'] : '';
                    update_option('_cf_options', $data);
                }

                $arr_cf_options = get_option( '_cf_options' ) ? get_option( '_cf_options' ) : array();
                $page_id_car = !empty($arr_cf_options['parent_page_id']) ? $arr_cf_options['parent_page_id'] : '';
                $page_id_brc = !empty($arr_cf_options['brc_page_id']) ? $arr_cf_options['brc_page_id'] : '';
                $image_path = !empty($arr_cf_options['image_path']) ? $arr_cf_options['image_path'] : '';
                
                $pages = get_pages(array( 'parent' => 0 )); 
		?>

		<form action="" method="post">
                    <p><label><?php _e('Parent car page', TEXT_DOMAIN); ?>:</label>
                    <select name="car_pages">
                        <?php 
                            echo '<option value="">'. esc_attr( __( 'Select page', TEXT_DOMAIN ) ) .'</option>';
                            
                            foreach ( $pages as $page ) {
                                  echo '<option value="' . $page->ID . '" '. selected($page->ID, $page_id_car, false) .'>'.$page->post_title.'</option>';
                            }
                        ?>
                    </select></p>
                    
                    <p><label><?php _e('Path to the image folder', TEXT_DOMAIN); ?>:</label>
                    <input type='text' name='image_path' value="<?php echo $image_path; ?>" placeholder="<?php _e('Upload folder');?>"><br/>
                    <small>(For example: <?php echo $_SERVER['DOCUMENT_ROOT']; ?>)</small></p>
                    
                    <p><label><?php _e('BRC page', TEXT_DOMAIN); ?>:</label>
                    <select name="brc_pages">
                        <?php 
                            echo '<option value="">'. esc_attr( __( 'Select page', TEXT_DOMAIN ) ) .'</option>';
                            $pages = get_pages(); 
                            foreach ( $pages as $page ) {
                                  echo '<option value="' . $page->ID . '" '. selected($page->ID, $page_id_brc, false) .'>'.$page->post_title.'</option>';
                            }
                        ?>
                    </select></p>
                    
                    <input type="hidden" name="is_car_settings" value="1"/>
                    <?php echo get_submit_button(__('Save Changes', TEXT_DOMAIN), 'primary', '', true ); ?>
		</form>
	</div>
        <?php
    }
}

new Car_Form_Admin();
// end \Car_Form_Admin class