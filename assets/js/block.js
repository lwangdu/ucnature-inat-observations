( function ( blocks, element, components, blockEditor, serverSideRender ) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var Placeholder = components.Placeholder;
	var SelectControl = components.SelectControl;
	var Spinner = components.Spinner;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var ServerSideRender = serverSideRender;
	var settings = window.ucnatureINatObservations || {};
	var maxPerPage = settings.maxPerPage || 200;
	var presets = settings.presets || [];
	var openLinksInNewTab = settings.openLinksInNewTab !== false;

	function findPreset( value ) {
		return presets.filter( function ( preset ) {
			return preset.value === value;
		} )[ 0 ];
	}

	blocks.registerBlockType( 'ucnature-inat/observations', {
		title: 'iNaturalist Observations',
		icon: 'visibility',
		category: 'widgets',
		attributes: {
			projectId: {
				type: 'number',
				default: 3234
			},
			projectSlug: {
				type: 'string',
				default: 'stunt-ranch-santa-monica-mountains-reserve'
			},
			placeId: {
				type: 'number',
				default: 0
			},
			userId: {
				type: 'string',
				default: ''
			},
			perPage: {
				type: 'number',
				default: 100
			},
			openLinksInNewTab: {
				type: 'boolean',
				default: openLinksInNewTab
			},
			sourcePreset: {
				type: 'string',
				default: 'stunt-ranch'
			}
		},
		edit: function ( props ) {
			var attributes = props.attributes;

			return el(
				'div',
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'iNaturalist Source' },
						el( SelectControl, {
							label: 'Reserve preset',
							value: attributes.sourcePreset || '',
							options: presets.map( function ( preset ) {
								return {
									label: preset.label,
									value: preset.value
								};
							} ),
							onChange: function ( value ) {
								if ( '' === value ) {
									props.setAttributes( { sourcePreset: '' } );
									return;
								}

								var preset = findPreset( value );

								if ( ! preset ) {
									props.setAttributes( { sourcePreset: '' } );
									return;
								}

								props.setAttributes( {
									sourcePreset: value,
									projectSlug: preset.projectSlug,
									projectId: parseInt( preset.projectId, 10 ) || 0,
									placeId: parseInt( preset.placeId, 10 ) || 0,
									userId: preset.userId || ''
								} );
							}
						} ),
						el( TextControl, {
							label: 'Project slug',
							value: attributes.projectSlug,
							onChange: function ( value ) {
								props.setAttributes( { projectSlug: value, sourcePreset: '' } );
							}
						} ),
						el( TextControl, {
							label: 'Project ID fallback',
							type: 'number',
							value: attributes.projectId,
							onChange: function ( value ) {
								props.setAttributes( { projectId: parseInt( value, 10 ) || 0, sourcePreset: '' } );
							}
						} ),
						el( TextControl, {
							label: 'Place ID',
							type: 'number',
							value: attributes.placeId,
							onChange: function ( value ) {
								props.setAttributes( { placeId: parseInt( value, 10 ) || 0, sourcePreset: '' } );
							}
						} ),
						el( TextControl, {
							label: 'User ID or login',
							value: attributes.userId,
							onChange: function ( value ) {
								props.setAttributes( { userId: value, sourcePreset: '' } );
							}
						} ),
						el( TextControl, {
							label: 'Observations per page',
							type: 'number',
							value: attributes.perPage,
							onChange: function ( value ) {
								var count = parseInt( value, 10 ) || 100;
								props.setAttributes( { perPage: Math.min( Math.max( count, 1 ), maxPerPage ) } );
							}
						} ),
						el( ToggleControl, {
							label: 'Open iNaturalist links in a new tab',
							checked: attributes.openLinksInNewTab,
							onChange: function ( value ) {
								props.setAttributes( { openLinksInNewTab: value } );
							}
						} )
					)
				),
				el( ServerSideRender, {
					block: 'ucnature-inat/observations',
					attributes: attributes,
					LoadingResponsePlaceholder: function () {
						return el(
							Placeholder,
							{ label: 'iNaturalist Observations' },
							el( Spinner ),
							el( 'span', {}, 'Loading observations...' )
						);
					},
					ErrorResponsePlaceholder: function () {
						return el(
							Placeholder,
							{ label: 'iNaturalist Observations' },
							el( 'span', {}, 'Unable to preview observations. Check the source settings and try again.' )
						);
					}
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender );
