<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* 
 * Helpers.
 */

//Debug
function vardump($str) {
    var_dump('<pre>');
    var_dump($str);
    var_dump('</pre>');
}

/**
 * Prepare files for upload by standardizing them into an array. This adds support for multiple file upload fields.
 * @param  array $file_data
 * @return array
 */
function cf_prepare_uploaded_files( $file_data ) {
	$files_to_upload = array();

	if ( is_array( $file_data['name'] ) ) {
		foreach( $file_data['name'] as $file_data_key => $file_data_value ) {
			if ( $file_data['name'][ $file_data_key ] ) {
				$type              = wp_check_filetype( $file_data['name'][ $file_data_key ] ); // Map mime type to one WordPress recognises
				$files_to_upload[] = array(
					'name'     => $file_data['name'][ $file_data_key ],
					'type'     => $type['type'],
					'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
					'error'    => $file_data['error'][ $file_data_key ],
					'size'     => $file_data['size'][ $file_data_key ]
				);
			}
		}
	} else {
		$type              = wp_check_filetype( $file_data['name'] ); // Map mime type to one WordPress recognises
		$file_data['type'] = $type['type'];
		$files_to_upload[] = $file_data;
	}

	return $files_to_upload;
}

/**
 * Upload a file using WordPress file API.
 * @param  array $file_data Array of $_FILE data to upload.
 * @param  array $args Optional arguments
 * @return stdClass|WP_Error Object containing file information, or error
 */
function cf_upload_file( $file, $args = array() ) {
	global $cf_upload, $cf_uploading_file;

	include_once( ABSPATH . 'wp-admin/includes/file.php' );
	include_once( ABSPATH . 'wp-admin/includes/media.php' );

	$args = wp_parse_args( $args, array(
		'file_key'           => '',
		'file_label'         => '',
		'allowed_mime_types' => '',
	) );

	$cf_upload         = true;
	$cf_uploading_file = $args['file_key'];
	$uploaded_file              = new stdClass();
	if ( '' === $args['allowed_mime_types'] ) {
		$allowed_mime_types = cf_get_allowed_mime_types( $cf_uploading_file );
	} else {
		$allowed_mime_types = $args['allowed_mime_types'];
	}

	/**
	 * Filter file configuration before upload
	 *
	 * This filter can be used to modify the file arguments before being uploaded, or return a WP_Error
	 * object to prevent the file from being uploaded, and return the error.
	 *
	 * @since 1.25.2
	 *
	 * @param array $file               Array of $_FILE data to upload.
	 * @param array $args               Optional file arguments
	 * @param array $allowed_mime_types Array of allowed mime types from field config or defaults
	 */
	$file = apply_filters( 'cf_upload_file_pre_upload', $file, $args, $allowed_mime_types );

	if ( is_wp_error( $file ) ) {
		return $file;
	}

	if ( ! in_array( $file['type'], $allowed_mime_types ) ) {
		if ( $args['file_label'] ) {
			return new WP_Error( 'upload', sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', TEXT_DOMAIN ), $args['file_label'], $file['type'], implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		} else {
			return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', TEXT_DOMAIN ), implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		}
	} else {
		$upload = wp_handle_upload( $file, apply_filters( 'submit_job_wp_handle_upload_overrides', array( 'test_form' => false ) ) );
		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'upload', $upload['error'] );
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->file      = $upload['file'];
			$uploaded_file->name      = basename( $upload['file'] );
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
		}
	}

	$cf_upload         = false;
	$cf_uploading_file = '';

	return $uploaded_file;
}

/**
 * Allowed Mime types specifically for CF.
 * @param   string $field Field used.
 * @return  array  Array of allowed mime types
 */
function cf_get_allowed_mime_types( $field = '' ){
	$allowed_mime_types = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

	/**
	 * Mime types to accept in uploaded files.
	 *
	 * Default is image, pdf, and doc(x) files.
	 *
	 * @since 1.0
	 *
	 * @param array  {
	 *     Array of allowed file extensions and mime types.
	 *     Key is pipe-separated file extensions. Value is mime type.
	 * }
	 * @param string $field The field key for the upload.
	 */
	return apply_filters( 'cf_mime_types', $allowed_mime_types, $field );
}

/**
 * Filters the upload dir when $cf_upload is true
 * @param  array $pathdata
 * @return array
 */
function cf_upload_dir( $pathdata ) {
	global $cf_upload, $cf_uploading_file;

	if ( ! empty( $cf_upload ) ) {
		$dir = untrailingslashit( apply_filters( 'cf_upload_dir', 'cf-uploads/gallery_images' , sanitize_key( $cf_uploading_file ) ) );
 
		if ( empty( $pathdata['subdir'] ) ) {
			$pathdata['path']   = $pathdata['path'] . '/' . $dir;
			$pathdata['url']    = $pathdata['url'] . '/' . $dir;
			$pathdata['subdir'] = '/' . $dir;
		} else {
			$new_subdir         = '/' . $dir . $pathdata['subdir'];
			$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
			$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
			$pathdata['subdir'] = $new_subdir;
		}
	}

	return $pathdata;
}
add_filter( 'upload_dir', 'cf_upload_dir' );

/**
 * Get months
 * @return array
 */
function cf_translate_months(){
        $arr_months = array(
            '01' => __('January', TEXT_DOMAIN),
            '02' => __('February', TEXT_DOMAIN),
            '03' => __('March', TEXT_DOMAIN),
            '04' => __("April", TEXT_DOMAIN),
            '05' => __("May", TEXT_DOMAIN),
            '06' => __("June", TEXT_DOMAIN),
            '07' => __("July", TEXT_DOMAIN),
            '08' => __("August", TEXT_DOMAIN),
            '09' => __("September", TEXT_DOMAIN),
            '10' => __("October", TEXT_DOMAIN),
            '11' => __("November", TEXT_DOMAIN),
            '12' => __("December", TEXT_DOMAIN)
        );
        return $arr_months;
}

/**
 * Get path to image folder
 * @return string
 */
function cf_image_path(){
    $arr_cf_options = get_option( '_cf_options' ) ? get_option( '_cf_options' ) : array();
    if(!empty($arr_cf_options['image_path'])){
            $image_path = $arr_cf_options['image_path'];
    }else{
            $arr = wp_upload_dir();
            $image_path = $arr['path'];
    }
    return $image_path;
}