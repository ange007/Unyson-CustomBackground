<?php if( !defined( 'FW' ) ) { die( 'Forbidden' ); }

/**
 * Custom Background Image
 */
class FW_Option_Type_Custom_Background_Image extends FW_Option_Type
{

	public function get_type( )
	{
		return 'custom-background';
	}

	/**
	 * @internal
	 */
	protected function _get_defaults( )
	{
		return [
			'value' => '',
			'choices' => [ ],
			'params' => [
				'predefined' => [
					'attach' => true,
					'paralax' => false,
				],
				'image' => [
					'size'	=> true,
					'repeat' => true,
					'position' => true,
					'attach' => true,
					'paralax' => false,
				],
				'video' => [
					'attach' => false,
					'paralax' => false,
				],
				'gradient' => [
					'animate' => true,
					'attach' => true,
					'paralax' => false,
				]
			]
		];
	}

	protected function _get_data_for_js( $id, $option, $data = [ ] )
	{
		return false;
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data )
	{
		/*
		 * Styles
		 */
		wp_enqueue_style(
			'fw-option-' . $this->get_type( ),
			get_template_directory_uri( ) . '/inc/includes/option-types/' . $this->get_type( ) . '/static/css/styles.css',
			[ ], fw( )->manifest->get_version( ) );

		/*
		 * Scripts
		 */
		wp_enqueue_script(
			'fw-option-' . $this->get_type( ),
			get_template_directory_uri( ) . '/inc/includes/option-types/' . $this->get_type( ) . '/static/js/scripts.js',
			[ 'jquery', 'fw-events' ], fw( )->manifest->get_version( ), true );

		/*
		 * ensures that the static of option type upload
		 * and image-picker is enqueued
		 */
		fw( )->backend->enqueue_options_static( [
			'custom-background-dummy-upload' => [
				'type' => 'upload'
			],
			'custom-background-dummy-image-picker' => [
				'type' => 'image-picker'
			],
		] );
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data )
	{
		$option = $this->check_parameters( $option );
		$data = $this->check_data( $option, $data );

		return fw_render_view( get_template_directory( ) . '/inc/includes/option-types/' . $this->get_type( ) . '/view.php', [
			'id' => $id,
			'option' => $option,
			'data' => $data
		] );
	}

	/*
	 *
	 */
	private function check_parameters( $option )
	{
		if( empty( $option[ 'choices' ] ) || !is_array( $option[ 'choices' ] ) )
		{
			$option[ 'choices' ] = [ ];
		}

		if( empty( $option[ 'value' ] ) || !in_array( $option[ 'value' ], array_keys( $option[ 'choices' ] ) ) )
		{
			$option[ 'value' ] = '';
		}

		if( empty( $option[ 'params' ] ) || !is_array( $option[ 'params' ] ) )
		{
			$option[ 'params' ] = [ ];
		}
		else
		{
			$option[ 'params' ] = array_replace_recursive( self::_get_defaults( )[ 'params' ], $option[ 'params' ] );
		}

		return $option;
	}

	/*
	 *
	 */
	private function check_data( $option, $data )
	{
		$return_value = ( !empty( $option[ 'value' ] ) ) ? $option[ 'value' ] : '';
		unset( $option[ 'value' ] );

		$outData = [ ];
		if( !empty( $option[ 'choices' ][ $return_value ][ 'css' ] ) ) { $outData = $option[ 'choices' ][ $return_value ][ 'css' ]; }
		else if( !empty( $option[ 'choices' ][ $return_value ][ 'html' ] ) ) { $outData = $option[ 'choices' ][ $return_value ][ 'html' ]; }

		$data[ 'value' ] = array_replace_recursive( [
			'type'	=> $return_value,
			'data'	=> $outData,
			'predefined' => '',
			'image' => '',
			'video' => '',
			'gradient' => ''
		], ( is_array( $data[ 'value' ] ) ? $data[ 'value' ] : [ ] ) );

		return $data;
	}

	/**
	 *
	 */
	protected function _handle_scroll_angle_change( $newAngle )
	{
		$scrollAngle = fmod( 360, $newAngle );

		if( ( $scrollAngle < 45 )
			or ( ( 135 < $scrollAngle ) && ( $scrollAngle < 225 ) )
			or ( 315 < $scrollAngle ) )
		{
			$mathStuff = tan( deg2rad( 180 - $scrollAngle ) );
			$startX = 0;
			$startY = 100 - intval( ( 50 * $mathStuff ) + 50 );
			$endX = 100;
			$endY = 100 - intval( 50 - ( 50 * $mathStuff ) );
		}
		else
		{
			$mathStuff = tan( deg2rad( $scrollAngle - 90 ) );
			$startX = 100 - intval( ( 50 * $mathStuff ) + 50 );
			$startY = 0;
			$endX = 100 - intval( 50 - ( 50 * $mathStuff ) );
			$endY = 100;
		}

		$startX = min( 100, $startX );
		$startY = min( 100, $startY );
		$endX = min( 100, $endX );
		$endY = min( 100, $endY );

		return "granimate{id} { 0%{ background-position: {$startX}% {$startY}% }"
				. "50%{ background-position: {$endX}% {$endY}% }"
				. "100%{ background-position: {$startX}% {$startY}% } }";
	}

	/**
	 * 
	 */
	protected function _copy_option_values( $option_key, $params, $input_value, &$return_value )
	{
		if( !is_array( $params ) || !isset( $return_value ) )
		{
			return;
		}

		foreach( $params as $key )
		{
			$full_key = $option_key . $key;
			if( !isset( $input_value[ $full_key ] ) ) { continue; }

			$return_value[ $full_key ] = $input_value[ $full_key ];
			
			if( isset( $return_value[ $full_key ][ 'value' ] ) && is_string( $return_value[ $full_key ][ 'value' ] ) )
			{
				$return_value[ $full_key ][ 'value' ] = json_decode( $input_value[ $full_key ][ 'value' ], true );
			}
		}
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value )
	{
		if( !is_array( $input_value ) )
		{
			return $option[ 'value' ];
		}

		/*
		*/
		$type = $input_value[ 'type' ];
		$return_value = [ 'type' => $type, $type => '' ];

		/*
		 * Image
		 */
		if( $type === 'image' )
		{
			/*
			* Copy values
			*/
			self::_copy_option_values( 'image', [ '', '-repeat', '-position', '-size', '-attach' ], $input_value, $return_value );

			/*
			*
			*/
			if( $attachment_id = intval( $input_value[ 'image' ] ) )
			{
				/*
				*
				*/
				$attachment_url = wp_get_attachment_url( $attachment_id );
				$css = [ ];
				$css_data = '';
				$html = '';
				$data = '';

				if( $return_value[ 'image-attach' ][ 'value' ] === 'yes' )
				{
					if( !empty( $return_value[ 'image-attach' ][ 'yes' ][ 'parallax-speed' ] ) )
					{
						$data = "data-paroller-factor=\"{$return_value[ 'image-attach' ][ 'yes' ][ 'parallax-speed' ]}\""
								. " data-paroller-direction=\"{$return_value[ 'image-attach' ][ 'yes' ][ 'parallax-position' ]}\"";
					}
					else
					{
						$css[ 'background-attachment' ] = 'fixed';
						$css_data .= ' fixed';
					}
				}

				if( !empty( $return_value[ 'image-repeat' ] ) && ( $return_value[ 'image-repeat' ] !== 'default' ) )
				{
					$css[ 'background-repeat' ] = $return_value[ 'image-repeat' ];
					$css_data .= ' ' . $return_value[ 'image-repeat' ];
				}

				if( !empty( $return_value[ 'image-position' ] ) && ( $return_value[ 'image-position' ] !== 'default' ) )
				{
					$css[ 'background-position' ] = $return_value[ 'image-position' ];
					$css_data .= '; background-position: ' . $return_value[ 'image-position' ];
				}

				if( !empty( $return_value[ 'image-size' ] ) && ( $return_value[ 'image-size' ] !== 'default' ) )
				{
					$css[ 'background-size' ] = $return_value[ 'image-size' ];
					$css_data .=  '; background-size: ' . $return_value[ 'image-size' ];
				}

				$css[ 'background-image' ] = 'url(' . $attachment_url . ')';
				$css[ 'background' ] = $css[ 'background-image' ] . $css_data;

				/*
				 *
				 */
				$return_value[ 'data' ] = [
					'icon'	=> $attachment_url,
					'css'	=> $css,
					'data'	=> $data
				];
			}
			else
			{
				$return_value[ 'data' ] = [
					'icon'	=> '',
					'css'	=> [ ],
					'data'	=> '',
				];
			}
		}
		/*
		 * Video
		 */
		else if( $input_value[ 'type' ] === 'video' )
		{
			/*
			* Copy values
			*/
			self::_copy_option_values( 'video', [ '', '-attach' ], $input_value, $return_value );

			/*
			*
			*/
			if( $attachment_id = intval( $input_value[ 'video' ] ) )
			{
				/*
				*
				*/
				$attachment_url = wp_get_attachment_url( $attachment_id );
				$custom_class = ( $input_value[ 'attach' ] ? ' background-video-fixed' : '' );
				$custom_data = '';

				/*
				 * Parallax
				 */
				if( !empty( $return_value[ 'video-attach' ][ 'yes' ][ 'parallax-speed' ] ) )
				{
					$custom_data = ( !empty( $parallax_speed ) ? " data-paroller-factor=\"{$return_value[ 'video-attach' ][ 'yes' ][ 'parallax-speed' ]}\" data-paroller-type=\"foreground\""
																. " data-paroller-direction=\"{$return_value[ 'video-attach' ][ 'yes' ][ 'parallax-position' ]}\"" : '' ) ;
				}

				/*
				 *
				 */
				$html = "<video class=\"background-video{$custom_class}\"{$custom_data} playsinline autoplay muted loop>"
							. "<source src=\"{$attachment_url}\" type=\"video/mp4\">"
						. "</video>";

				/*
				 *
				 */
				$return_value[ 'attach' ] = $value;
				$return_value[ 'data' ] = [
					'video' => $attachment_url,
					'html' => $html
				];
			}
			else
			{
				$return_value[ 'data' ] = [
					'icon' => '',
					'css' => [ ]
				];
			}
		}
		/*
		 * Gradient
		 */
		else if( $input_value[ 'type' ] === 'gradient' )
		{
			/*
			* Copy values
			*/
			self::_copy_option_values( 'gradient', [ '', '-animate', '-attach', '-angle' ], $input_value, $return_value );

			/*
			 *
			 */
			$return_value[ 'data' ] = [
				'css' => [ ],
				'functions' => [ ]
			];

			/*
			 *
			 */
			if( !empty( $input_value[ 'gradient' ] ) )
			{
				/*
				*
				*/
				$functions = [ ];
				$css = [ 'background' => "linear-gradient({$return_value[ 'gradient-angle' ]}deg, " . implode( $return_value[ 'gradient' ], ',' ) . ') center center / 200%' ];

				/*
				*
				*/
				if( !empty( $return_value[ 'gradient-animate' ] ) && $return_value[ 'gradient-animate' ][ 'value' ] === 'on' )
				{
					$animate_data = $return_value[ 'gradient-animate' ][ 'on' ];
					$css[ 'animation' ] = "granimate{id} {$animate_data[ 'gradient-animate-speed' ]}s ease infinite";
					$functions[ 'keyframes' ] = self::_handle_scroll_angle_change( $animate_data[ 'gradient-animate-angle' ] );
				}

				/*
				 *
				 */
				if( !empty( $return_value[ 'gradient-attach' ] ) && ( $return_value[ 'gradient-attach' ][ 'value' ] === 'yes' ) )
				{
					if( !empty( $return_value[ 'gradient-attach' ][ 'yes' ][ 'parallax-speed' ] ) )
					{
						$return_value[ 'data' ][ 'data' ] = "data-paroller-factor=\"{$return_value[ 'gradient-attach' ][ 'yes' ][ 'parallax-speed' ]}\""
															. " data-paroller-direction=\"{$return_value[ 'gradient-attach' ][ 'yes' ][ 'parallax-position' ]}\"";
					}
					else
					{
						$css[ 'background-attachment' ] = 'fixed';
						$css[ 'background' ] .= ' fixed';
					}
				}

				/*
				 *
				 */
				foreach( [ '', '-o-', '-moz-', '-webkit-' ] as $prefix )
				{
					foreach( $css as $key => $return_value ) { $return_value[ 'data' ][ 'css' ][ $prefix . $key ] = $return_value; }
					foreach( $functions as $key => $return_value ) { $return_value[ 'data' ][ 'functions' ][ ] = '@' . $prefix . $key . ' ' . $return_value; }
				}
			}
		}
		/*
		 * Predefined
		 */
		else
		{
			/*
			* Copy values
			*/
			self::_copy_option_values( 'predefined', [ '', '-attach' ], $input_value, $return_value );

			/*
			 *
			 */
			$data = ( !empty( $option[ 'choices' ][ $return_value[ 'predefined' ] ] ) ) ? $option[ 'choices' ][ $return_value[ 'predefined' ] ] : [ ];

			/*
			 *
			 */
			if( !empty( $data[ 'css' ] ) && $return_value[ 'predefined-attach' ] )
			{
				$data[ 'css' ][ 'background' ] = $data[ 'css' ][ 'background' ] . ' fixed';
				$data[ 'css' ][ 'background-attachment' ] = 'fixed';

				if( !empty( $return_value[ 'predefined-attach' ][ 'yes' ][ 'parallax-speed' ] ) )
				{
					$data[ 'data' ] = "data-paroller-factor=\"{$return_value[ 'predefined-attach' ][ 'yes' ][ 'parallax-speed' ]}\""
									. " data-paroller-direction=\"{$return_value[ 'predefined-attach' ][ 'yes' ][ 'parallax-position' ]}\"";
				}
			}

			/*
			 *
			 */
			// ?? $input_value[ 'attach' ] = $input_value[ 'predefined-attach' ];
			$return_value[ 'predefined' ] = ( isset( $return_value[ 'predefined' ] ) ) ? $return_value[ 'predefined' ] : $option[ 'value' ];
			$return_value[ 'data' ] = $data;
		}

		return $return_value;
	}

}

/*
 * Register
 */
FW_Option_Type::register( 'FW_Option_Type_Custom_Background_Image' );