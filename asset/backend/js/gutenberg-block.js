/**
 * Flipbox block (block editor).
 *
 * Choose a flip box in the block settings or in the block itself; the
 * editor then shows a live preview rendered by the server (the same output
 * as the [oxilab_flip_box] shortcode). The block saves nothing but the id:
 * the page is rendered by Modules/Gutenberg.php.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.components || ! wp.blockEditor || ! wp.serverSideRender ) {
		return;
	}

	var el                = wp.element.createElement;
	var Fragment          = wp.element.Fragment;
	var __                = wp.i18n.__;
	var useBlockProps     = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody         = wp.components.PanelBody;
	var SelectControl     = wp.components.SelectControl;
	var Placeholder       = wp.components.Placeholder;
	var Button            = wp.components.Button;
	var Spinner           = wp.components.Spinner;
	var ServerSideRender  = wp.serverSideRender;
	var data              = window.oxiFlipBlockData || {};
	var flipboxes         = data.flipboxes || [];
	var TEXT_DOMAIN       = 'oxi-flip-box-plugin';

	// The plugin's mark (Classes/Docs.php, "flipbox").
	var points = [
		'11.6 17.05 11.6 20.19 9.41 18.9 7.79 17.94 4.89 16.23 4.89 13.11 4.91 13.1 7.55 14.66 7.82 14.82 10.45 16.37 11.6 17.05',
		'19.18 13.1 19.18 13.12 16.57 14.66 15.01 13.74 14.96 13.71 17.59 12.15 17.8 12.28 19.18 13.1',
		'19.18 13.12 19.18 16.26 14.72 18.9 12.5 20.21 12.5 17.07 13.68 16.37 16.31 14.82 16.57 14.66 19.18 13.12',
		'9.17 13.71 9.12 13.73 9.12 13.73 7.55 14.66 4.91 13.1 5.17 12.94 6.52 12.14 9.17 13.71',
		'11.6 12.03 11.6 15.14 9.43 13.86 9.43 13.86 9.17 13.71 6.52 12.14 4.9 11.18 4.89 11.18 4.89 8.11 4.93 8.09 7.53 9.63 11.6 12.03',
		'19.18 8.07 19.18 8.1 16.6 9.62 12.5 7.2 12.5 4.12 14.68 5.4 19.18 8.07',
		'11.6 4.15 11.6 7.23 7.53 9.63 4.93 8.09 9.47 5.41 9.47 5.41 11.6 4.15',
		'19.18 8.1 19.18 11.21 17.59 12.15 14.96 13.71 14.69 13.86 12.5 15.16 12.5 12.05 16.6 9.62 19.18 8.1'
	];
	var blockIcon = el( 'svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24' },
		el( 'rect', { width: '24', height: '24', rx: '5', fill: '#17bcb5' } ),
		el( 'g', { fill: '#ffffff', transform: 'translate(12 12) scale(1.12) translate(-12.03 -12.18)' },
			points.map( function ( p, i ) {
				return el( 'polygon', { key: i, points: p } );
			} )
		)
	);

	function flipboxSelect( value, onChange ) {
		return el( SelectControl, {
			label: __( 'Select a flip box', TEXT_DOMAIN ),
			value: value,
			options: [ { value: '', label: __( 'Choose a flip box', TEXT_DOMAIN ) } ].concat( flipboxes ),
			onChange: onChange,
			__nextHasNoMarginBottom: true
		} );
	}

	wp.blocks.registerBlockType( 'oxi-flip-box/flipbox', {

		icon: blockIcon,

		edit: function ( props ) {
			var id         = props.attributes.flipboxId || '';
			var blockProps = useBlockProps();
			var choose     = function ( value ) {
				props.setAttributes( { flipboxId: value } );
			};

			var settings = el( InspectorControls, null,
				el( PanelBody, { title: __( 'Flipbox', TEXT_DOMAIN ), initialOpen: true },
					flipboxes.length
						? flipboxSelect( id, choose )
						: el( 'p', null, __( 'You have no flip boxes yet.', TEXT_DOMAIN ) ),
					el( 'div', { className: 'oxi-flip-block-links' },
						id ? el( Button, { variant: 'secondary', href: data.editUrl + id, target: '_blank' }, __( 'Edit this flip box', TEXT_DOMAIN ) ) : null,
						el( Button, { variant: 'link', href: data.createUrl, target: '_blank' }, __( 'Create a new flip box', TEXT_DOMAIN ) )
					)
				)
			);

			var body;
			if ( ! id ) {
				body = el( Placeholder, {
					icon: blockIcon,
					label: __( 'Flipbox', TEXT_DOMAIN ),
					instructions: flipboxes.length
						? __( 'Choose the flip box to show.', TEXT_DOMAIN )
						: __( 'You have no flip boxes yet. Create one, then come back to this block.', TEXT_DOMAIN )
				},
					flipboxes.length
						? flipboxSelect( id, choose )
						: el( Button, { variant: 'primary', href: data.createUrl, target: '_blank' }, __( 'Create a flip box', TEXT_DOMAIN ) )
				);
			} else {
				// Links and buttons inside the flip box would leave the editor.
				body = el( 'div', {
					onClick: function ( e ) {
						if ( e.target.closest && e.target.closest( 'a' ) ) {
							e.preventDefault();
						}
					}
				},
					el( ServerSideRender, {
						block: 'oxi-flip-box/flipbox',
						attributes: props.attributes,
						LoadingResponsePlaceholder: function () {
							return el( 'div', { className: 'oxi-flip-block-loading' }, el( Spinner ) );
						}
					} )
				);
			}

			return el( Fragment, null, settings, el( 'div', blockProps, body ) );
		},

		save: function () {
			return null;
		}
	} );
}( window.wp ) );
