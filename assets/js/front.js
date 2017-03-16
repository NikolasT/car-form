/* global CFParams */
jQuery(function($) {
	$('.cf-file-upload').each(function(){
		$(this).fileupload({
			dataType: 'json',
			dropZone: $(this),
			url: CFParams.ajax_url.toString().replace( '%%endpoint%%', 'upload_file' ),
			maxNumberOfFiles: CFParams.image_limit,
			formData: {
				script: true
			},
                        change: function (e, data) {
                            $('div.data_invalid').remove();
                            if(data.files.length >= CFParams.image_limit){
                                alert(CFParams.text_limit)
                                return false;
                            }
                        },
			add: function (e, data) {                            
				var $file_field     = $( this );
				var $form           = $file_field.closest( 'form' );
				var $uploaded_files = $file_field.parent().find('.cf-uploaded-files');
				var uploadErrors    = [];

				// Validate type
				var allowed_types = $(this).data('file_types');

				if ( allowed_types ) {
					var acceptFileTypes = new RegExp( '(\.|\/)(' + allowed_types + ')$', 'i' );

					if ( data.originalFiles[0].name.length && ! acceptFileTypes.test( data.originalFiles[0].name ) ) {
						uploadErrors.push( CFParams.i18n_invalid_file_type + ' ' + allowed_types );
					}
				}

				if ( uploadErrors.length > 0 ) {
					window.alert( uploadErrors.join( '\n' ) );
				} else {
					$form.find(':input[type="submit"]').attr( 'disabled', 'disabled' );
					data.context = $('<progress value="" max="100"></progress>').appendTo( $uploaded_files );
					data.submit();
				}
			},
			progress: function (e, data) {
				var progress        = parseInt(data.loaded / data.total * 100, 10);
				data.context.val( progress );
			},
			fail: function (e, data) {
				var $file_field     = $( this );
				var $form           = $file_field.closest( 'form' );

				if ( data.errorThrown ) {
					window.alert( data.errorThrown );
				}

				data.context.remove();

				$form.find(':input[type="submit"]').removeAttr( 'disabled' );
			},
			done: function (e, data) {
				var $file_field     = $( this );
				var $form           = $file_field.closest( 'form' );
				var $uploaded_files = $file_field.parent().find('.cf-uploaded-files');
				var multiple        = $file_field.attr( 'multiple' ) ? 1 : 0;
				var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ];

				data.context.remove();

				$.each(data.result.files, function(index, file) {
					if ( file.error ) {
						window.alert( file.error );
					} else {
						var html;
						if ( $.inArray( file.extension, image_types ) >= 0 ) {
							html = $.parseHTML( CFParams.js_field_html_img );
							$( html ).find('.cf-uploaded-file-preview img').attr( 'src', file.url );
						}

						$( html ).find('.input-text').val( file.url );
						$( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

						if ( multiple ) {
							$uploaded_files.append( html );
						} else {
							$uploaded_files.html( html );
						}
					}
				});

				$form.find(':input[type="submit"]').removeAttr( 'disabled' );
			}
		});
	});
        //Limit images
        $('#cf_gallery_images').on('click', function(e) {
		if ( $('.cf-uploaded-file-preview img').length >= CFParams.image_limit ) {
			alert(CFParams.text_limit);
			e.preventDefault();
		}
	});
        //Remove images
        $('body').on( 'click', '.cf-remove-uploaded-file', function() {
		$(this).closest( '.cf-uploaded-file' ).remove();
		return false;
	});
        
        $('#send_car_form').click(function(e) {
                e.preventDefault();
                
                $('select,input').removeClass('required-field');
                $('div.data_invalid').remove();
              
                var carBrandId = $('#car_brand').val();
                if( carBrandId == false ){
                    $('#car_brand').addClass('required-field');
                    return false;
                }
                var carModel = $('#car_model').val();
                if( $.trim(carModel) == false ){
                    $('#car_model').addClass('required-field');
                    return false;
                }
                var displacement = $('#displacement').val();
                if( $.trim(displacement) == false ){
                    $('#displacement').addClass('required-field').focus();
                    return false;
                }
                var tankTypeSize = $('#tank_type_size').val();
                if( $.trim(tankTypeSize) == false ){
                    $('#tank_type_size').addClass('required-field').focus();
                    return false;
                }
                var power = $('#power').val();
                if( $.trim(power) == false ){
                    $('#power').addClass('required-field').focus();
                    return false;
                }
                var typeOfGasSystem = $('#type_of_gas_system').val();
                if( $.trim(typeOfGasSystem) == false ){
                    $('#type_of_gas_system').addClass('required-field').focus();
                    return false;
                }
                
                var successYear = /^[1-2][0-9]{3}$/;
                
                var year = $('#year').val();
                if( $.trim(year) == false ){
                    $('#year').addClass('required-field').focus();
                    return false;
                }else{                     
                    if( ! successYear.test(year) ) {
                            $('#year').addClass('required-field').focus().after('<div class="data_invalid">'+ CFParams.invalid_year +'</div>');
                            return false;
                    }
                }
                var rangeInGasMode = $('#range_in_gas_mode').val();
                if( $.trim(rangeInGasMode) == false ){
                    $('#range_in_gas_mode').addClass('required-field').focus();
                    return false;
                }
                var mileage = $('#mileage').val();
                if( $.trim(mileage) == false ){
                    $('#mileage').addClass('required-field').focus();
                    return false;
                }
                var carMonth = $('#car_month').val();
                if( $.trim(carMonth) == false ){
                    $('#car_month').addClass('required-field');
                    return false;
                }
                var carYear = $('#car_year').val();
                if( $.trim(carYear) == false ){
                    $('#car_year').addClass('required-field');
                    return false;
                }else{                     
                    if( ! successYear.test(carYear) ) {
                            $('#car_year').addClass('required-field').focus().after('<div class="data_invalid">'+ CFParams.invalid_year +'</div>');
                            return false;
                    }
                }
           
		if ( $('.cf-uploaded-file').length == 0 ) {
                        $('#car_form_conteiner_for_upload_image').after('<div class="data_invalid" style="margin-left:15px;">'+ CFParams.required_image +'</div>');
			return false;
		}
                var arrImgs = [];
                
                $('.cf-uploaded-file-preview img').each(function() {
                        arrImgs.push($(this).attr("src"));
                });
                
                var data = {
                        action: 'cf_send_form',
                        car_brand: carBrandId,
                        car_name: $('#car_brand :selected').html(),
                        displacement: displacement,
                        power: power,
                        year: year,
                        mileage: mileage,
                        car_model: carModel,
                        tank_type_size: tankTypeSize,
                        type_of_gas_system: typeOfGasSystem,
                        range_in_gas_mode: rangeInGasMode,
                        car_month: carMonth,
                        car_year: carYear,
                        description: $('#description').val(),
                        imgs: arrImgs
                }
                var thisEl = $(this);
                var thisObj = {
                    'txt': $(this).val(),
                    'bgColor': thisEl.css('background-color'),
                    'color': thisEl.css('color')
                };
                $.ajax({
                        method: "POST",
                        dataType: 'json',
                        url: CFParams.default_ajax_url,
                        data: data,
                        beforeSend: function(){
                                thisEl.attr('disabled', 'disabled').css({'background':'#f5b3ab','color':'#901f11'}).val(CFParams.pending);
                        },
                        success: function (response) {
                                if( response.result ){
                                        $('#car_form_primary').html('<div class="car_notice success">'+ response.html +'</div>');
                                }else{
                                        $('#car_form_primary').prepend('<div class="car_notice error">'+ response.html +'</div>');
                                }     
                                thisEl.removeAttr("disabled").css({'background':thisObj.bgColor,'color':thisObj.color}).val(thisObj.txt);                           
			}
                });

        });

});