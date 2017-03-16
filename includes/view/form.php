<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/*
 * Form
 */
?>
<form id="car_form" name='car_form' method="post" action='' enctype="multipart/form-data">
    <div id="car_form_primary">
        <div class="wpv-grid grid-1-2">
            <label for="car_brand"><?php _e('Car brand', TEXT_DOMAIN); ?></label>
            <select name="car_brand" id="car_brand" required>
                <?php
                if (!empty($arr_cf_options['parent_page_id'])) {
                    $cars = get_children(array('post_parent' => $arr_cf_options['parent_page_id'], 'post_status' => 'publish', 'post_type' => 'page', 'order' => 'ASC'));
                    if (!empty($cars)) {
                        foreach ($cars as $car) {
                            echo '<option value="' . $car->ID . '">' . $car->post_title . '</option>';
                        }
                    }
                } else {
                    echo '<option value=""></option>';
                }
                ?>
            </select>
        </div>
        <div class="wpv-grid grid-1-2">
            <label for="car_model"><?php _e('Car model', TEXT_DOMAIN); ?></label>
            <input type="text" name="car_model" id="car_model" value='' required>
        </div>

        <div class="wpv-grid grid-1-2">
            <label for="displacement"><?php _e('Hubraum', TEXT_DOMAIN); ?> (cm<sup>3</sup>)</label>
            <input type="text" name="displacement" id="displacement" value='' required>
        </div>
        <div class="wpv-grid grid-1-2">
            <label for="tank_type_size"><?php _e('Gastank – Art u. Größe', TEXT_DOMAIN); ?></label>
            <input type="text" name="tank_type_size" id="tank_type_size" value='' required>
        </div>

        <div class="wpv-grid grid-1-2">
            <label for="power"><?php _e('Leistung', TEXT_DOMAIN); ?> (kW)</label>
            <input type="number" name="power" id="power" value='' required>
        </div>
        <div class="wpv-grid grid-1-2">
            <label for="type_of_gas_system"><?php _e('Art der Autogasanlage', TEXT_DOMAIN); ?></label>
            <select name="type_of_gas_system" id="type_of_gas_system" required>
                <?php
                if (!empty($arr_cf_options['brc_page_id'])) {
                    $brcs = get_pages(array('child_of' => $arr_cf_options['brc_page_id'], 'hierarchical' => 1, 'post_status' => 'publish', 'order' => 'ASC'));
                    if (!empty($cars)) {
                        foreach ($brcs as $brc) {
                            echo '<option value="' . $brc->ID . '">' . $brc->post_title . '</option>';
                        }
                    }
                } else {
                    echo '<option value=""></option>';
                }
                ?>
            </select>
        </div>

        <div class="wpv-grid grid-1-2">
            <label for="year"><?php _e('Baujahr', TEXT_DOMAIN); ?></label>
            <input type="number" name="year" id="year" min="1950" max="<?php echo date('Y'); ?>" step="1" value='' pattern="^[1-2][0-9]{3}$" title="<?php _e('Four-digit year', TEXT_DOMAIN); ?>" required>
        </div>
        <div class="wpv-grid grid-1-2">
            <label for="range_in_gas_mode"><?php _e('Reichweite im Gasbetrieb', TEXT_DOMAIN); ?></label>
            <input type="text" name="range_in_gas_mode" id="range_in_gas_mode" value='' required>
        </div>

        <div class="wpv-grid grid-1-2">
            <label for="mileage"><?php _e('Km-Laufleistung beim Autogasumbau', TEXT_DOMAIN); ?></label>
            <input type="text" name="mileage" id="mileage" value='' required>
        </div>
        <div class="wpv-grid grid-1-2 car_date">
            <div class="wpv-grid grid-1-2">
                <label for="car_month"><?php _e('Monat', TEXT_DOMAIN); ?></label>
                <select name="car_month" id="car_month" required>
                    <?php
                    $arr_months = cf_translate_months();
                    foreach ($arr_months as $key => $month) {
                        echo '<option value="' . $key . '">' . $month . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="wpv-grid grid-1-2">
                <label for="car_year"><?php _e('Jahr', TEXT_DOMAIN); ?></label>
                <input type="number" name="car_year" id="car_year" min="1950" max="<?php echo date('Y'); ?>" step="1" value='' pattern="^[1-2][0-9]{3}$" title="<?php _e('Four-digit year', TEXT_DOMAIN); ?>" required>
            </div>
        </div>
        <div class="wpv-grid grid-1-1" id="car_form_container_description">
            <label for="description"><?php _e('Beschreibungstext', TEXT_DOMAIN); ?> <small class='small-text'>(optional)</small></label>
            <textarea name="description" id="description"></textarea>
        </div>
        <div class="wpv-grid grid-1-1" id="car_form_conteiner_for_upload_image">
            <label for="cf_gallery_images"><?php _e('Gallery Images', TEXT_DOMAIN); ?></label>
            <?php
            $classes = array('input-text');
            $arr_allowed_mime_types = array(
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png'
            );
            $allowed_mime_types = array_keys(!empty($arr_allowed_mime_types) ? $arr_allowed_mime_types : get_allowed_mime_types() );

            $classes[] = 'cf-file-upload';
            ?>
            <div class="cf-uploaded-files">
                <?php
                if (!empty($field['value'])) {
                    if (is_array($field['value'])) {
                        foreach ($field['value'] as $value) {
                            include CAR_FORM_DIR . '/includes/view/uploaded-file-html.php';
                        }
                    } elseif ($value = $field['value']) {
                        include CAR_FORM_DIR . '/includes/view/uploaded-file-html.php';
                    }
                }
                ?>
            </div>

            <input type="file" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-file_types="<?php echo esc_attr(implode('|', $allowed_mime_types)); ?>" multiple name="cf_gallery_images[]" id="cf_gallery_images" title='' />
            <small class="small-text">
                <?php
                if (!empty($field['description'])) {
                    echo $field['description'];
                } else {
                    printf(__('Maximum file size: %s.', 'wp-job-manager'), size_format(wp_max_upload_size()));
                }
                ?>
            </small>
        </div>
        <?php wp_nonce_field('car_detail_form', 'action_save'); ?>
        <div class="wpv-grid grid-1-1" id="car_form_container_button">
            <input type="submit" id="send_car_form" name="send_car_form" value="<?php _e('Send', TEXT_DOMAIN); ?>" />
        </div>

    </div>
</form>