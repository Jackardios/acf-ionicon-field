(function($){
	
	function update_preview( value, parent ) {
		$( '.icon_preview', parent ).html( '<ion-icon size="large" name="' + value + '"></ion-icon>' );
	}

	function select2_init_args( element, parent ) {
		return {
			key			: $( parent ).data('key'),
			allowNull	: $( element ).data('allow_null'),
			ajax		: 1,
			ajaxAction	: 'acf/fields/ionicon/query'
		}
	}

	function select2_init( ionicon_field ) {
		var $select = $( ionicon_field );
		var parent = $( $select ).closest('.acf-field-ionicon');

		update_preview( $select.val(), parent );

		acf.select2.init( $select, select2_init_args( ionicon_field, parent ), parent );
	}

	acf.add_action( 'select2_init', function( $input, args, settings, $field ) {
		if ( $field instanceof jQuery && $field.hasClass('ionicon-edit') ) {
			$field.addClass('select2_initalized');
		}
	});

	// Update FontAwesome field previews in field create area
	acf.add_action( 'open_field/type=ionicon', function( $el ) {
		var $field_objects = $('.acf-field-object[data-type="ionicon"]');

		$field_objects.each( function( index, field_object ) {
			update_preview( $( 'select.ionicon-create', field_object ).val(), field_object );
		});
	});

	// Uncheck standard icon set choices if 'custom icon set' is checked, and show the custom icon set select box
	$( document ).on( 'change', '.acf-field[data-name="icon_sets"] input[type="checkbox"]', function() {
		var parent = $( this ).closest('.acf-field-object-ionicon');
		if ( $( this ).is('[value="custom"]') && $( this ).is(':checked') ) {
			$( 'input[type="checkbox"]:not([value="custom"])', parent ).prop('checked', false);
			$( '.acf-field-setting-custom_icon_set', parent ).show();
		} else {
			$( 'input[type="checkbox"][value="custom"]', parent ).prop('checked', false);
			$( '.acf-field-setting-custom_icon_set', parent ).hide();
		}
	});

	// Handle new menu items with FontAwesome fields assigned to them
	$( document ).on( 'menu-item-added', function( event, $menuMarkup ) {
		var $ionicon_fields = $( 'select.ionicon-edit:not(.select2_initalized)', $menuMarkup );

		if ( $ionicon_fields.length ) {
			$ionicon_fields.each( function( index, ionicon_field ) {
				select2_init( ionicon_field );
			});
		}
	});

	// Update FontAwesome field previews and init select2 in field edit area
	acf.add_action( 'ready_field/type=ionicon append_field/type=ionicon show_field/type=ionicon', function( $el ) {
		var $ionicon_fields = $( 'select.ionicon-edit:not(.select2_initalized)', $el );

		if ( $ionicon_fields.length ) {
			$ionicon_fields.each( function( index, ionicon_field ) {
				select2_init( ionicon_field );
			});
		}
	});

	// Update FontAwesome field previews when value changes
	$( document ).on( 'select2:select', 'select.select2-ionicon', function() {
		var $input = $( this );

		if ( $input.hasClass('ionicon-create') ) {
			update_preview( $input.val(), $input.closest('.acf-field-object') );
		}

		if ( $input.hasClass('ionicon-edit') ) {
			update_preview( $input.val(), $input.closest('.acf-field-ionicon') );
		}
	});

})(jQuery);