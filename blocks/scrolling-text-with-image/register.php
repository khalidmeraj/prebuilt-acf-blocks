<?php
/**
 * Registration for the "Scrolling Text with Image" block.
 *
 * Do NOT include this file. Copy only the acf_register_block_type() call below
 * into your own acf/init function (for example inside gutenbergtheme_register_blocks()).
 *
 * Check these four values against your own setup (see the main README):
 *   category, icon, render_callback, enqueue_assets
 */

acf_register_block_type( array(
	'name'            => 'scrolling-text-with-image',
	'title'           => __( 'Scrolling Text with Image', 'gutenbergtheme' ),
	'description'     => __( 'Sticky scroll section: text sections on the right, the matching image on the left.', 'gutenbergtheme' ),
	'category'        => 'themeblock',
	'icon'            => THEME_BLOCK_ICON,
	'mode'            => 'auto',
	'keywords'        => array( 'scroll', 'sticky', 'image', 'text', 'content', 'kb' ),

	// Hover preview image in the inserter. Needs the is_inserter_preview check in the
	// render callback. If your callback doesn't have it, delete this whole 'example' array.
	'example'         => array(
		'attributes' => array(
			'mode' => 'preview',
			'data' => array(
				'is_inserter_preview' => true,
			),
		),
	),

	'render_callback' => 'gutenbergtheme_acf_block_render_callback',
	'enqueue_assets'  => 'gutenbergtheme_acf_block_enqueue_assets',

	'supports'        => array(
		'align'           => array( 'wide', 'full' ),
		'align_text'      => true,
		'anchor'          => true,
		'customClassName' => true,
		'typography'      => array(
			'fontSize'   => true,
			'fontFamily' => true,
		),
		'color'           => array(
			'background' => true,
			'text'       => true,
		),
	),
) );
