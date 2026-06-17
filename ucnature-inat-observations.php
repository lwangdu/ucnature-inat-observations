<?php
/**
 * Plugin Name: UC Nature iNaturalist Observations
 * Description: Displays iNaturalist project observations in WordPress using cached API requests and a block editor interface.
 * Version: 0.1.1
 * Author: Lobsang Wangdu
 * License: GPL-2.0-or-later
 * Text Domain: ucnature-inat-observations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UCNATURE_INAT_VERSION', '0.1.1' );
define( 'UCNATURE_INAT_PATH', plugin_dir_path( __FILE__ ) );
define( 'UCNATURE_INAT_URL', plugin_dir_url( __FILE__ ) );
define( 'UCNATURE_INAT_PAGE_OPTION', 'ucnature_inat_observations_page_id' );

require_once UCNATURE_INAT_PATH . 'includes/class-ucnature-inat-observations-plugin.php';
require_once UCNATURE_INAT_PATH . 'includes/class-ucnature-inat-observations-admin.php';
require_once UCNATURE_INAT_PATH . 'includes/class-ucnature-inat-observations-rest.php';
require_once UCNATURE_INAT_PATH . 'includes/class-ucnature-inat-observations-renderer.php';
require_once UCNATURE_INAT_PATH . 'includes/class-ucnature-inat-observations-cache.php';

add_action(
	'plugins_loaded',
	function () {
		UCNature_INat_Observations_Plugin::instance();
	}
);

register_activation_hook( __FILE__, 'ucnature_inat_observations_activate' );

function ucnature_inat_observations_activate() {
	$page_id = absint( get_option( UCNATURE_INAT_PAGE_OPTION ) );

	if ( $page_id && 'page' === get_post_type( $page_id ) ) {
		return;
	}

	$existing_page = get_page_by_path( 'inaturalist-observations' );
	if ( $existing_page instanceof WP_Post ) {
		update_option( UCNATURE_INAT_PAGE_OPTION, $existing_page->ID );
		return;
	}

	$content  = '<!-- wp:paragraph --><p>UC Nature sites support remarkable biodiversity, and community science platforms like iNaturalist help document those living communities over time. This page highlights recent observations recorded for this reserve.</p><!-- /wp:paragraph -->';
	$content .= "\n\n" . '<!-- wp:ucnature-inat/observations {"projectSlug":"stunt-ranch-santa-monica-mountains-reserve","projectId":3234,"perPage":100} /-->';

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'iNaturalist Observations', 'ucnature-inat-observations' ),
			'post_name'    => 'inaturalist-observations',
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		),
		true
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_option( UCNATURE_INAT_PAGE_OPTION, absint( $page_id ) );
	}
}
