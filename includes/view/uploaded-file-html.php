<div class="cf-uploaded-file">
	<?php
        if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	if ( is_numeric( $value ) ) {
		$image_src = wp_get_attachment_image_src( absint( $value ) );
		$image_src = $image_src ? $image_src[0] : '';
	} else {
		$image_src = $value;
	}
	$extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );

	if ( 3 !== strlen( $extension ) || in_array( $extension, array( 'jpg', 'gif', 'png', 'jpeg', 'jpe' ) ) ) : ?>
		<span class="cf-uploaded-file-preview"><img src="<?php echo esc_url( $image_src ); ?>" /> <a class="cf-remove-uploaded-file" href="#">[<?php _e( 'remove', TEXT_DOMAIN ); ?>]</a></span>
	<?php else : ?>
		<span class="cf-uploaded-file-name"><code><?php echo esc_html( basename( $image_src ) ); ?></code> <a class="cf-remove-uploaded-file" href="#">[<?php _e( 'remove', TEXT_DOMAIN ); ?>]</a></span>
	<?php endif; ?>

	<input type="hidden" class="input-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
</div>
