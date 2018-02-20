jQuery( document ).ready( function( $ )
{
	var optionTypeClass = 'fw-option-type-custom-background';
	var eventNamePrefix = 'fw:option-type:custom-background:';

	fw.options.register( 'custom-background',
	{
		startListeningForChanges: jQuery.noop,
		getValue: function( optionDescriptor )
		{
			return {
				value: getValueForEl( optionDescriptor.el ),
				optionDescriptor: optionDescriptor
			};
		}
	} );

	fwEvents.on( 'fw:options:init', function( data )
	{
		var $options = data.$elements.find( '.' + optionTypeClass + ':not(.initialized)' );
		
		$options.toArray( ).map( function( el )
		{
			/**
			 * Here we start listening to events triggered by inner option
			 * types. We may receive events from 3 nested option types here:
			 *
			 * 1. radio
			 * 2. image-picker
			 * 3. upload
			 */
			fw.options.on.changeByContext( el, function( optionDescriptor )
			{
				var selectedType = $( el ).find( '.type input[type="radio"]:checked' ).val( );
				
				if( optionDescriptor.type === 'radio' )
				{
					var $predefined = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.predefined' );
					var $custom = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.custom' );
					var $customVideo = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.custom-video' );
					var $gradient = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.gradient' );
			
					getValueForEl( el ).then( function( value )
					{
						$predefined.hide( );
						$custom.hide( );
						$customVideo.hide( );
						$gradient.hide( );

						if( value.type === 'image' )
						{
							$custom.show( );
						}
						else if( value.type === 'video' )
						{
							$customVideo.show( );
						} 
						else if( value.type === 'gradient' )
						{
							$gradient.show( );
						} 
						else
						{
							$predefined.show( );
						}
					} );
				}
				else if( optionDescriptor.type === 'upload' )
				{
					var $customOptions = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.custom-options' );
					var $customVideoOptions = $( optionDescriptor.context ).closest( '.fw-inner' ).find( '.custom-video-options' );

					getValueForEl( el ).then( function( value )
					{
						if( value.type === 'image' )
						{
							$customOptions.toggle( ( value.image.url !== undefined && value.image.url !== '' ) );
						}
						else if( value.type === 'video' )
						{
							$customVideoOptions.toggle( ( value.video.url !== undefined && value.video.url !== '' ) );
						}
					} );
					
					// console.log( optionDescriptor );
				}

				triggerChangeAndInferValueFor(
					// Here we refer to the optionDescriptor.context
					// as to the `background-image` option type container
					optionDescriptor.context
				);
			} );
			
			/*fw.options.on( 'fw:option-type:upload:change', function( )
			{
				
			} );
			
			fw.options.on( 'fw:option-type:upload:clear', function( )
			{
				
			} );*/
		} );

		// route inner image-picker events as this option events
		{
			$options.on(
					'fw:option-type:image-picker:clicked',
					'.fw-option-type-image-picker',
					function( e, data ) { jQuery( this ).trigger( eventNamePrefix + 'clicked', data ); }
			);

			$options.on(
					'fw:option-type:image-picker:changed',
					'.fw-option-type-image-picker',
					function( e, data ) { jQuery( this ).trigger( eventNamePrefix + 'changed', data ); }
			);
	
			$options.on( 
					'fw:option-type:upload:change',
					'.fw-option-type-upload',
					function( e, data ) { jQuery( this ).trigger( eventNamePrefix + 'changed', data ); console.log( 'fw:option-type:upload:change' ); }
			);
	
			$options.on( 
					'fw:option-type:upload:clear',
					'.fw-option-type-upload',
					function( e, data ) { jQuery( this ).trigger( eventNamePrefix + 'changed', data ); console.log( 'fw:option-type:upload:clear' ); }
			);
		}

		$options.addClass( 'initialized' );

		/*
		 * 
		 * @param {type} el
		 * @returns {undefined}
		 */
		function triggerChangeAndInferValueFor( el )
		{
			getValueForEl( el ).then( function( value ) { fw.options.trigger.changeForEl( el, { value: value } ); } );
		}
	} );

	/*
	 * 
	 */
	function getValueForEl( el )
	{
		var promise = $.Deferred( );
		var optionDescriptor = fw.options.getOptionDescriptor( el );

		fw.options.getContextValue( optionDescriptor.el ).then( function( value ) { promise.resolve( value.value ); } );

		return promise;
	}
} );
