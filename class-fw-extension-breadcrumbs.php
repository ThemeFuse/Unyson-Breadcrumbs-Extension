<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Breadcrumbs extends FW_Extension {

	/**
	 * @internal
	 */
	public function _init() {
	}

	/**
	 * Creates the breadcrumbs HTML
	 *
	 * @param string $separator , separator symbol that will be set between elements
	 *
	 * @return string
	 */
	public function render( $separator = ">" ) {
		$data = array(
			'labels' => array()
		);

		$settings['labels']['homepage-title'] = fw_get_db_ext_settings_option( $this->get_name(), 'homepage-title' );
		$settings['labels']['blogpage-title'] = fw_get_db_ext_settings_option( $this->get_name(), 'blogpage-title' );
		$settings['labels']['404-title']      = fw_get_db_ext_settings_option( $this->get_name(), '404-title' );

		$breadcrumbs = new Breadcrumbs_Builder( $settings );

		$data['items']     = $breadcrumbs->get_breadcrumbs();
		$data['separator'] = $separator;

		return $this->render_view( 'breadcrumbs', $data );
	}

	/**
	 * Returns an hardcoded id for the breadcrumbs option
	 * @return string
	 */
	public function get_option_id() {
		return $this->get_name() . '-option';
	}
}