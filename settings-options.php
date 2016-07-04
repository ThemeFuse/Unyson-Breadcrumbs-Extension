<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$default_values = apply_filters('fw_ext_breadcrumbs_settings_options_default_values', array(
	'homepage-title' => __( 'Homepage', 'fw' ),
	'blogpage-title' => __( 'Blog', 'fw' ),
	'404-title' => __('404 Not Found', 'fw'),
));

$options = array(
	apply_filters('fw:ext:breadcrumbs:settings-options:before', array()),
	'box' => array(
		'title'   => false,
		'type'    => 'box',
		'options' => array(
			'homepage-title'      => array(
				'label' => __( 'Text for Homepage', 'fw' ),
				'desc'  => __( 'The homepage anchor will have this text', 'fw' ),
				'type'  => 'text',
				'value' => $default_values['homepage-title']
			),
			'blogpage-title'      => array(
				'label' => __( 'Text for Blog Page', 'fw' ),
				'desc'  => __( 'The blog page anchor will have this text. In case homepage will be set as blog page, will be taken the homepage text', 'fw' ),
				'type'  => 'text',
				'value' => $default_values['blogpage-title']
			),
			'404-title'           => array(
				'label' => __( 'Text for 404 Page', 'fw' ),
				'desc'  => __( 'The 404 anchor will have this text', 'fw' ),
				'type'  => 'text',
				'value' => $default_values['404-title']
			),
		)
	),
	apply_filters('fw:ext:breadcrumbs:settings-options:after', array()),
);