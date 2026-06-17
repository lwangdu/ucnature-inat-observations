( function ( blocks, element, components, blockEditor, serverSideRender ) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var ServerSideRender = serverSideRender;
	var settings = window.ucnatureINatObservations || {};
	var maxPerPage = settings.maxPerPage || 200;

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
						el( TextControl, {
							label: 'Project slug',
							value: attributes.projectSlug,
							onChange: function ( value ) {
								props.setAttributes( { projectSlug: value } );
							}
						} ),
						el( TextControl, {
							label: 'Project ID fallback',
							type: 'number',
							value: attributes.projectId,
							onChange: function ( value ) {
								props.setAttributes( { projectId: parseInt( value, 10 ) || 0 } );
							}
						} ),
						el( TextControl, {
							label: 'Place ID',
							type: 'number',
							value: attributes.placeId,
							onChange: function ( value ) {
								props.setAttributes( { placeId: parseInt( value, 10 ) || 0 } );
							}
						} ),
						el( TextControl, {
							label: 'User ID or login',
							value: attributes.userId,
							onChange: function ( value ) {
								props.setAttributes( { userId: value } );
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
						} )
					)
				),
				el( ServerSideRender, {
					block: 'ucnature-inat/observations',
					attributes: attributes
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender );
