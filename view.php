<?php if ( !defined( 'FW' ) ) { die( 'Forbidden' ); }
/**
 * @var string $id
 * @var  array $option
 * @var  array $data
 */

{
	$wrapper_attr = $option[ 'attr' ];
	unset( $wrapper_attr[ 'value' ], $wrapper_attr[ 'name' ] );
}

/*
 * 
 */
if( empty( $option[ 'choices' ] ) )
{
	$option[ 'choices' ] = [ ];
}

/*
 * 
 */
if( empty( $option[ 'choices' ] ) )
{
	$wrapper_attr[ 'class' ] .= ' no-choices';
}

/*
 * ToDo: 
 * * Add https://www.gradient-animator.com/
 * * Add YouTube/Vimeo video
 * * 
 */

/*
 * 
 */
if( !function_exists( 'get_parallax_options' ) )
{
	function get_parallax_options( $type, $data )
	{
		return [
			'group' => [
				'type' => 'group',
				'options' => [
					'parallax-speed' => [
						'type'	=> 'short-text',
						'label'	=> false,
						'desc'	=> 'Parallax Speed',
						'value'	=> '-0.1',	
					],
					'parallax-position' => [
						'type'		=> 'radio',
						'label'		=> false,
						'choices'	=> [ 
							'vertical' => __( 'Vertical', 'default' ),
							'horizontal' => __( 'Horizontal', 'default' )
						],
						'value'		=> 'vertical',
						'inline'	=> true,
					]
				]
			]
		];
	}
}

/*
 * 
 */
$choices = [ ];
foreach( $option[ 'choices' ] as $choice_key => $choice_value )
{
	$choices[ $choice_key ] = [
		'small' => [
			'src' => $choice_value[ 'icon' ],
			'height' => ( !empty( $choice_value[ 'size' ] ) && !empty( $choice_value[ 'size' ][ 'height' ] ) ? $choice_value[ 'size' ][ 'height' ] : 50 ),
			'width' => ( !empty( $choice_value[ 'size' ] ) && !empty( $choice_value[ 'size' ][ 'width' ] ) ? $choice_value[ 'size' ][ 'width' ] : 'auto' )
		],
		'data' => [
			'css' => $choice_value['css']
		]
	];
}

/*
 * 
 */
$show_predefined_options = false;
$show_image_options = false;
$show_video_options = false;
?>

<div <?php echo fw_attr_to_html( $wrapper_attr ) ?>>
	<div class="type">
		<?php
			/*
			*/
			$types = [ ];
			if( !empty( $option[ 'params' ][ 'predefined' ] ) && $option[ 'params' ][ 'predefined' ] && count( $choices ) > 0 ) { $types[ 'predefined' ] = __( 'Predefined', 'fw' ); }
			if( !empty( $option[ 'params' ][ 'image' ] ) && $option[ 'params' ][ 'image' ] ) { $types[ 'image' ] = __( 'Custom Image', 'fw' ); }
			if( !empty( $option[ 'params' ][ 'video' ] ) && $option[ 'params' ][ 'video' ] ) { $types[ 'video' ] = __( 'Custom Video', 'fw' ); }
			if( !empty( $option[ 'params' ][ 'gradient' ] ) && $option[ 'params' ][ 'gradient' ] ) { $types[ 'gradient' ] = __( 'Gradient', 'fw' ); }

			/*
			*/
			$selected_type = !empty( $data[ 'value' ][ 'type' ] ) ? $data[ 'value' ][ 'type' ] : key( $types );
			
			/*
			*/
			echo fw( )->backend->option_type( 'radio' )->render(
				'type',
				[
					'type'		=> 'radio',
					'label'		=> false,
					'choices'	=> $types,
					'value'		=> $selected_type,
					'inline'	=> true,
				],
				[
					'value'   		=> !empty( $data[ 'value' ][ 'type' ] ) ? $data[ 'value' ][ 'type' ] : key( $types ),
					'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
					'name_prefix' 	=> $data[ 'name_prefix' ] . '[' . $id . ']',
				]
			);

			/*
			*/
			if( array_key_exists( 'predefined', $types ) )
			{
				foreach( $option[ 'params' ][ 'predefined' ] as $key => $value )
				{
					if( $value )
					{
						$show_predefined_options = true;
						break;
					}
				}
			}

			/*
			*/
			if( array_key_exists( 'image', $types ) )
			{
				foreach( $option[ 'params' ][ 'image' ] as $key => $value )
				{
					if( $value )
					{
						$show_image_options = true;
						break;
					}
				}
			}
			
			/*
			*/
			if( array_key_exists( 'video', $types ) )
			{
				foreach( $option[ 'params' ][ 'video' ] as $key => $value )
				{
					if( $value )
					{
						$show_video_options = true;
						break;
					}
				}
			}
			
			/*
			*/
			if( array_key_exists( 'gradient', $types ) )
			{
				foreach( $option[ 'params' ][ 'gradient' ] as $key => $value )
				{
					if( $value )
					{
						$show_gradient_options = true;
						break;
					}
				}
			}
		?>
	</div>

	<?php if( !empty( $option[ 'params' ][ 'predefined' ] ) && $option[ 'params' ][ 'predefined' ] && count( $choices ) > 0 ): ?>
		<div class="predefined" <?php if( $selected_type !== 'predefined' ): ?>style="display: none;"<?php endif; ?>>
			<?php
				echo fw( )->backend->option_type( 'image-picker' )->render(
					'predefined',
					[
						'type'		=> 'image-picker',
						'label'		=> false,
						'desc'		=> __( 'Predefined', 'default' ),
						'value'		=> $option[ 'value' ],
						'choices'	=> $choices,
						'blank'		=> true,
					],
					[
						'value'       => ( $selected_type === 'predefined' ) ? $data[ 'value' ][ 'predefined' ] : '',
						'id_prefix'   => $data[ 'id_prefix' ] . $id . '-',
						'name_prefix' => $data[ 'name_prefix' ] . '[' . $id . ']',
					]
						
				);
			
				if( $show_predefined_options ):
			?>
				<div class="predefined-options" <?php if( empty( $data[ 'value' ][ 'predefined' ] ) ): ?>style="display: none;"<?php endif; ?>>	
					<?php
						if( $option[ 'params' ][ 'predefined' ] && $option[ 'params' ][ 'predefined' ][ 'attach' ] )
						{
							echo fw( )->backend->option_type( 'multi-picker' )->render(
								'predefined-attach',
								[
									'type'  => 'multi-picker',
									'label' => false,
									'desc'  => false,
									'picker' => [
										'value' => [
											'type'  => 'switch',
											'label' => __( 'Scroll with Page', 'default' ),
											'left-choice' => [
												'value' => 'no',
												'label' => __( 'No', 'default' ),
											],
											'right-choice' => [
												'value' => 'yes',
												'label' => __( 'Yes', 'default' ),
											],
										]
									],
									'choices' => [
										'yes' => get_parallax_options( 'predefined', $data )
									]
								],
								[ 
									'value'			=> ( $selected_type === 'predefined' ? $data[ 'value' ][ 'predefined-attach' ] : false ),
									'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
									'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
								]
							);
						}
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php if( !empty( $option[ 'params' ][ 'image' ] ) && $option[ 'params' ][ 'image' ] ): ?>
		<div class="custom" <?php if( $selected_type !== 'image' ): ?>style="display: none;"<?php endif; ?>>
			<?php
				echo fw( )->backend->option_type( 'upload' )->render(
					'image',
					[ 
						'type'			=> 'upload',
						'label'			=> false,
						'desc'			=> __( 'Image', 'default' ),
						'images_only'	=> true
					],
					[
						'value'       => ( $selected_type === 'image' ) ? [ 'attachment_id' => $data[ 'value' ][ 'image' ] ] : '',
						'id_prefix'   => $data[ 'id_prefix' ] . $id . '-',
						'name_prefix' => $data[ 'name_prefix' ] . '[' . $id . ']',
					]
				);
		
				if( $show_image_options ):
			?>
			
			<div class="custom-options" <?php if( empty( $data[ 'value' ][ 'image' ] ) ): ?>style="display: none;"<?php endif; ?>>
				<?php	
					if( $option[ 'params' ][ 'image' ] && $option[ 'params' ][ 'image' ][ 'size' ] )
					{
						echo fw( )->backend->option_type( 'select' )->render(
							'image-size',
							[
								'type'		=> 'select',
								'label'		=> false,
								'desc'		=> __( 'Size', 'default' ),
								'value'		=> 'auto',
								'choices'	=> [ 
									'auto'		=> __( 'Original', 'default' ),
									'cover'		=> __( 'Fill Screen', 'default' ),
									'contain'	=> __( 'Fit to Screen', 'default' )
								],
							], 
							[ 	
								'value'		  => ( $selected_type === 'image' ? $data[ 'value' ][ 'image-size' ] : '' ),
								'id_prefix'   => $data[ 'id_prefix' ] . $id . '-',
								'name_prefix' => $data[ 'name_prefix' ] . '[' . $id . ']', 
							]
						);
					}

					if( $option[ 'params' ][ 'image' ] && $option[ 'params' ][ 'image' ][ 'repeat' ] )
					{
						echo fw( )->backend->option_type( 'select' )->render(
							'image-repeat',
							[
								'type'		=> 'select',
								'label'		=> false,
								'desc'		=> __( 'Repeat', 'default' ),
								'value'		=> 'no-repeat',
								'choices'	=> [ 
									'no-repeat' => __( 'No Repeat', 'default' ),
									'repeat'	=> __( 'Repeat', 'default' ),
									'repeat-x'	=> __( 'Repeat Horizontally', 'default' ),
									'repeat-y'	=> __( 'Repeat Vertically', 'default' )
								],
							], 
							[ 	
								'value'			=> ( $selected_type === 'image' ? $data[ 'value' ][ 'image-repeat' ] : '' ),
								'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
								'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
							]
						);
					}

					if( $option[ 'params' ][ 'image' ] && $option[ 'params' ][ 'image' ][ 'position' ] )
					{
						echo fw( )->backend->option_type( 'select' )->render(
							'image-position',
							[
								'type'		=> 'select',
								'label'		=> false,
								'desc'		=> __( 'Position', 'default' ),
								'value'		=> 'top left',
								'choices'	=> [ 
									'top center' 	=> __( 'Top', 'default' ),
									'top left' 		=> __( 'Top Left', 'default' ),
									'top right'		=> __( 'Top Right', 'default' ),	

									'bottom center' => __( 'Bottom', 'default' ),
									'bottom left' 	=> __( 'Bottom Left', 'default' ),
									'bottom right' 	=> __( 'Bottom Right', 'default' ),

									'left center' 	=> __( 'Left', 'default' ),
									'right center' 	=> __( 'Right', 'default' ),
									'center center' => __( 'Center', 'default' ),
								],
							], 
							[ 	
								'value'			=>  ( $selected_type === 'image' ? $data[ 'value' ][ 'image-position' ] : '' ),
								'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
								'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
							]
						);
					}

					if( $option[ 'params' ][ 'image' ] && $option[ 'params' ][ 'image' ][ 'attach' ] )
					{
						echo fw( )->backend->option_type( 'multi-picker' )->render(
							'image-attach',
							[
								'type'  => 'multi-picker',
								'label' => false,
								'desc'  => false,
								'picker' => [
									'value' => [
										'type'  => 'switch',
										'label' => __( 'Scroll with Page', 'default' ),
										'left-choice' => [
											'value' => 'no',
											'label' => __( 'No', 'default' ),
										],
										'right-choice' => [
											'value' => 'yes',
											'label' => __( 'Yes', 'default' ),
										],
									]
								],
								'choices' => [
									'yes' => get_parallax_options( 'image', $data )
								]
							],
							[ 	
								'value'			=> ( $selected_type === 'image' ? $data[ 'value' ][ 'image-attach' ] : false ),
								'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
								'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
							]
						);
					}
				?>
			</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php if( !empty( $option[ 'params' ][ 'video' ] ) && $option[ 'params' ][ 'video' ] ): ?>
		<div class="custom-video" <?php if( $selected_type !== 'video' ): ?>style="display: none;"<?php endif; ?>>
			<?php
				echo fw( )->backend->option_type( 'upload' )->render(
					'video',
					[ 
						'type'		=> 'upload',
						'label'		=> false,
						'desc'		=> __( 'Video', 'default' ),
						'files_ext' => [ 'mp4', 'webm', 'ogv' ],
					],
					[
						'value'       => ( $data[ 'value' ][ 'type' ] === 'video' ) ? [ 'attachment_id' => $data[ 'value' ][ 'video' ] ] : '',
						'id_prefix'   => $data[ 'id_prefix' ] . $id . '-',
						'name_prefix' => $data[ 'name_prefix' ] . '[' . $id . ']',
					]
				);

				if( $show_video_options ):
			?>
				<div class="custom-video-options" <?php if( empty( $data[ 'value' ][ 'video' ] ) ): ?>style="display: none;"<?php endif; ?>>	
					<?php

					if( $option[ 'params' ][ 'video' ] && $option[ 'params' ][ 'video' ][ 'attach' ] )
					{
						echo fw( )->backend->option_type( 'multi-picker' )->render(
							'video-attach',
							[
								'type'  => 'multi-picker',
								'label' => false,
								'desc'  => false,
								'picker' => [
									'value' => [
										'type'  => 'switch',
										'label' => __( 'Scroll with Page', 'default' ),
										'left-choice' => [
											'value' => 'no',
											'label' => __( 'No', 'default' ),
										],
										'right-choice' => [
											'value' => 'yes',
											'label' => __( 'Yes', 'default' ),
										],
									]
								],
								'choices' => [
									'yes' => get_parallax_options( 'video', $data )
								]
							],
							[ 
								'value'			=> ( $selected_type === 'video' ? $data[ 'value' ][ 'attach' ] : false ),
								'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
								'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
							]
						);
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php if( !empty( $option[ 'params' ][ 'gradient' ] ) && $option[ 'params' ][ 'gradient' ] ): ?>
		<div class="gradient" <?php if( $selected_type !== 'gradient' ): ?>style="display: none;"<?php endif; ?>>
			<?php
				echo fw( )->backend->option_type( 'addable-option' )->render(
					'gradient',
					[
						'type' => 'addable-option',
						'label' => __( 'Colors', 'default' ),
						'value' => [ ],
						'option' => [ 'type' => 'color-picker' ],
					],
					[
						'value'       => ( $selected_type === 'gradient' ) ? $data[ 'value' ][ 'gradient' ] : [ ],
						'id_prefix'   => $data[ 'id_prefix' ] . $id . '-',
						'name_prefix' => $data[ 'name_prefix' ] . '[' . $id . ']',
					]
				);

				echo fw( )->backend->option_type( 'slider' )->render(
					'gradient-angle',
					[
						'type'		=> 'slider',
						'label'		=> false,
						'desc'		=> __( 'Angle', 'default' ),
						'value'		=> 0,
						'properties' => [ 'min' => 0, 'max' => 360, 'step' => 1 ],
					], 
					[ 
						'value'			=> ( $selected_type === 'gradient' ? $data[ 'value' ][ 'gradient-angle' ] : 0 ),
						'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
						'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
					]
				);

				if( $option[ 'params' ][ 'gradient' ][ 'animate' ] )
				{
					echo fw( )->backend->option_type( 'multi-picker' )->render(
						'gradient-animate',
						[
							'type'		=> 'multi-picker',
							'label'		=> false,
							'desc'		=> __( 'Animate', 'default' ),
							'picker' => [
								'value' => [
									'type' => 'switch',
									'value' => 'off',
									'label' => false,
									'left-choice' => [
										'value' => 'off',
										'label' => __( 'Off', 'default' ),
									],
									'right-choice' => [
										'value' => 'on',
										'label' => __( 'On', 'default' ),
									]
								],
							],
							'choices' => [
								'on' => [
									'gradient-animate-angle' => [
										'type'		=> 'slider',
										'label'		=> false,
										'desc'		=> __( 'Scroll Angle', 'default' ),
										'properties' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
									], 
									'gradient-animate-speed' =>	[
										'type'		=> 'slider',
										'label'		=> false,
										'desc'		=> __( 'Speed', 'default' ),
										'properties' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
									], 
								],
							]
						],
						[ 
							'value'			=> ( $selected_type === 'gradient' ? $data[ 'value' ][ 'gradient-animate' ] : false ), 
							'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
							'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
						]
					);
				}
				
				if( $option[ 'params' ][ 'gradient' ][ 'attach' ] )
				{					
					echo fw( )->backend->option_type( 'multi-picker' )->render(
						'gradient-attach',
						[
							'type'  => 'multi-picker',
							'label' => false,
							'desc'  => false,
							'picker' => [
								'value' => [
									'type'  => 'switch',
									'label' => __( 'Scroll with Page', 'default' ),
									'left-choice' => [
										'value' => 'no',
										'label' => __( 'No', 'default' ),
									],
									'right-choice' => [
										'value' => 'yes',
										'label' => __( 'Yes', 'default' ),
									],
								]
							],
							'choices' => [
								'yes' => get_parallax_options( 'gradient', $data )
							]
						],
						[ 
							'value'			=> ( $selected_type === 'gradient' ? $data[ 'value' ][ 'gradient-attach' ] : false ),
							'id_prefix'		=> $data[ 'id_prefix' ] . $id . '-',
							'name_prefix'	=> $data[ 'name_prefix' ] . '[' . $id . ']', 
						]
					);
				}
			?>
		</div>
	<?php endif; ?>
</div>