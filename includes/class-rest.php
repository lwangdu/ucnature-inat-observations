<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UCNature_iNat_Observations_REST {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			'ucnature-inat/v1',
			'/observations',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_observations' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'project_id'   => array(
						'type'              => 'integer',
						'default'           => 0,
						'sanitize_callback' => 'absint',
						'minimum'           => 0,
					),
					'project_slug' => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_title',
					),
					'place_id'     => array(
						'type'              => 'integer',
						'default'           => 0,
						'sanitize_callback' => 'absint',
						'minimum'           => 0,
					),
					'user_id'      => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'per_page'     => array(
						'type'              => 'integer',
						'default'           => 100,
						'sanitize_callback' => 'absint',
						'minimum'           => 1,
						'maximum'           => UCNature_iNat_Observations_Cache::MAX_PER_PAGE,
					),
					'page'         => array(
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'minimum'           => 1,
					),
					'group'        => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => array( 'UCNature_iNat_Observations_Cache', 'sanitize_group' ),
					),
				),
			)
		);
	}

	public function get_observations( WP_REST_Request $request ) {
		$data = UCNature_iNat_Observations_Cache::get_observations(
			array(
				'project_id'   => $request->get_param( 'project_id' ),
				'project_slug' => $request->get_param( 'project_slug' ),
				'place_id'     => $request->get_param( 'place_id' ),
				'user_id'      => $request->get_param( 'user_id' ),
				'per_page'     => $request->get_param( 'per_page' ),
				'page'         => $request->get_param( 'page' ),
				'group'        => $request->get_param( 'group' ),
			)
		);

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		return rest_ensure_response( $data );
	}
}
