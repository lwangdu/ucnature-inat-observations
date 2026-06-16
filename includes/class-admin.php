<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UCNature_iNat_Observations_Admin {
	const OPTION_NAME = 'ucnature_inat_observations_options';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public static function get_options() {
		$defaults = array(
			'project_id'   => 3234,
			'project_slug' => 'stunt-ranch-santa-monica-mountains-reserve',
			'per_page'     => 100,
			'cache_ttl'    => HOUR_IN_SECONDS,
		);

		$options = get_option( self::OPTION_NAME, array() );

		return wp_parse_args( is_array( $options ) ? $options : array(), $defaults );
	}

	public function add_options_page() {
		add_options_page(
			__( 'iNaturalist Observations', 'ucnature-inat-observations' ),
			__( 'iNaturalist Observations', 'ucnature-inat-observations' ),
			'manage_options',
			'ucnature-inat-observations',
			array( $this, 'render_options_page' )
		);
	}

	public function register_settings() {
		register_setting(
			'ucnature_inat_observations',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => self::get_options(),
			)
		);

		add_settings_section(
			'ucnature_inat_source',
			__( 'iNaturalist Source', 'ucnature-inat-observations' ),
			'__return_false',
			'ucnature-inat-observations'
		);

		add_settings_field(
			'project_slug',
			__( 'Project slug', 'ucnature-inat-observations' ),
			array( $this, 'render_project_slug_field' ),
			'ucnature-inat-observations',
			'ucnature_inat_source'
		);

		add_settings_field(
			'project_id',
			__( 'Project ID fallback', 'ucnature-inat-observations' ),
			array( $this, 'render_project_id_field' ),
			'ucnature-inat-observations',
			'ucnature_inat_source'
		);

		add_settings_field(
			'per_page',
			__( 'Observations per page', 'ucnature-inat-observations' ),
			array( $this, 'render_per_page_field' ),
			'ucnature-inat-observations',
			'ucnature_inat_source'
		);

		add_settings_field(
			'cache_ttl',
			__( 'Cache duration', 'ucnature-inat-observations' ),
			array( $this, 'render_cache_ttl_field' ),
			'ucnature-inat-observations',
			'ucnature_inat_source'
		);
	}

	public function sanitize_options( $options ) {
		$options = is_array( $options ) ? $options : array();

		return array(
			'project_id'   => max( 1, absint( $options['project_id'] ?? 3234 ) ),
			'project_slug' => sanitize_title( $options['project_slug'] ?? 'stunt-ranch-santa-monica-mountains-reserve' ),
			'per_page'     => min( UCNature_iNat_Observations_Cache::MAX_PER_PAGE, max( 1, absint( $options['per_page'] ?? 100 ) ) ),
			'cache_ttl'    => min( DAY_IN_SECONDS, max( 300, absint( $options['cache_ttl'] ?? HOUR_IN_SECONDS ) ) ),
		);
	}

	public function render_project_slug_field() {
		$options = self::get_options();
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[project_slug]" value="<?php echo esc_attr( $options['project_slug'] ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Example: stunt-ranch-santa-monica-mountains-reserve.', 'ucnature-inat-observations' ); ?></p>
		<?php
	}

	public function render_project_id_field() {
		$options = self::get_options();
		?>
		<input type="number" min="1" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[project_id]" value="<?php echo esc_attr( $options['project_id'] ); ?>" class="small-text">
		<p class="description"><?php esc_html_e( 'Used only when no project slug is set, or as a fallback reference.', 'ucnature-inat-observations' ); ?></p>
		<?php
	}

	public function render_per_page_field() {
		$options = self::get_options();
		?>
		<input type="number" min="1" max="<?php echo esc_attr( UCNature_iNat_Observations_Cache::MAX_PER_PAGE ); ?>" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[per_page]" value="<?php echo esc_attr( $options['per_page'] ); ?>" class="small-text">
		<?php
	}

	public function render_cache_ttl_field() {
		$options = self::get_options();
		?>
		<input type="number" min="300" max="<?php echo esc_attr( DAY_IN_SECONDS ); ?>" step="300" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[cache_ttl]" value="<?php echo esc_attr( $options['cache_ttl'] ); ?>" class="small-text">
		<p class="description"><?php esc_html_e( 'Seconds. Keep this at 3600 or higher for normal public pages.', 'ucnature-inat-observations' ); ?></p>
		<?php
	}

	public function render_options_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'iNaturalist Observations', 'ucnature-inat-observations' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ucnature_inat_observations' );
				do_settings_sections( 'ucnature-inat-observations' );
				submit_button();
				?>
			</form>
			<p><?php esc_html_e( 'Add the iNaturalist Observations block to a dedicated page, then set the reserve source in the block sidebar.', 'ucnature-inat-observations' ); ?></p>
			<h2><?php esc_html_e( 'Block settings', 'ucnature-inat-observations' ); ?></h2>
			<p><?php esc_html_e( 'Use Project slug for an iNaturalist project, Place ID for a reserve boundary, or User ID/login for an account feed. Leave Project slug blank and Project ID as 0 when using only a place or account source.', 'ucnature-inat-observations' ); ?></p>
			<p><?php esc_html_e( 'Shortcode support remains available for older pages, but the block is recommended for new reserve pages.', 'ucnature-inat-observations' ); ?></p>
		</div>
		<?php
	}
}
