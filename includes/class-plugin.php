<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UCNature_iNat_Observations_Plugin {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		new UCNature_iNat_Observations_Admin();
		new UCNature_iNat_Observations_REST();
		new UCNature_iNat_Observations_Renderer();
		new UCNature_iNat_Observations_Cache();
	}
}