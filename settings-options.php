<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Breadcrumbs settings options
 */

$options = array(
	'box' => array(
		'title'   => false,
		'type'    => 'box',
		'options' => array(
			'homepage-title'      => array(
				'label' => __( 'Text for Homepage', 'fw' ),
				'desc'  => __( 'The homepage anchor will have this text', 'fw' ),
				'type'  => 'text',
				'value' => __( 'Homepage', 'fw' )
			),
			'blogpage-title'      => array(
				'label' => __( 'Text for Blog Page', 'fw' ),
				'desc'  => __( 'The blog page anchor will have this text. In case homepage will be set as blog page, will be taken the homepage text', 'fw' ),
				'type'  => 'text',
				'value' => __( 'Blog', 'fw' )
			),
			'404-title'           => array(
				'label' => __( 'Text for 404 Page', 'fw' ),
				'desc'  => __( 'The 404 anchor will have this text', 'fw' ),
				'type'  => 'text',
				'value' => '404 Not Found'
			),
		)
	)
);