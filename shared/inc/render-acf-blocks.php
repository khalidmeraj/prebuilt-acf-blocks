<?php
/**
 * Shared setup for the prebuilt ACF blocks.
 *
 * 1. Copy this file to your-theme/inc/render-acf-blocks.php
 * 2. Load it from functions.php:
 *      require_once get_theme_file_path( '/inc/render-acf-blocks.php' );
 * 3. Paste each block's acf_register_block_type() call (blocks/<name>/register.php)
 *    inside gutenbergtheme_register_blocks() below.
 *
 * Already have your own render file? Don't copy this one (the functions would be
 * declared twice). Only paste the block's acf_register_block_type() call into your
 * own acf/init function. See the main README.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Block icon (used by every block) and block category
 * ------------------------------------------------------------------------- */
if ( ! defined( 'THEME_BLOCK_ICON' ) ) {
	define( 'THEME_BLOCK_ICON', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4.5 6v12M9.8 6 5 12.2M6.9 9.8 10 18M13 6v12M13 6h3.2a3 3 0 0 1 0 6H13M13 12h3.8a3 3 0 0 1 0 6H13"/></svg>' );
}

add_filter( 'block_categories_all', 'gutenbergtheme_register_block_category', 10, 2 );

function gutenbergtheme_register_block_category( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug'  => 'themeblock',
				'title' => __( 'Khalid Blocks', 'gutenbergtheme' ),
				'icon'  => THEME_BLOCK_ICON,
			),
		),
		$categories
	);
}

/* ---------------------------------------------------------------------------
 * Register the blocks
 * ------------------------------------------------------------------------- */
add_action( 'acf/init', 'gutenbergtheme_register_blocks' );

function gutenbergtheme_register_blocks() {

	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	// Paste the acf_register_block_type( array( ... ) ); call of each block here.

}

/* ---------------------------------------------------------------------------
 * Assets: css/block_{slug}.css and js/blocks_js/block_{slug}.js
 * ------------------------------------------------------------------------- */
function gutenbergtheme_acf_block_enqueue_assets( $block ) {
	$slug = str_replace( 'acf/', '', $block['name'] );

	$css = get_theme_file_path( "/css/block_{$slug}.css" );
	if ( file_exists( $css ) ) {
		wp_enqueue_style( 'block-' . $slug, get_theme_file_uri( "/css/block_{$slug}.css" ), array(), filemtime( $css ) );
	}

	$js = get_theme_file_path( "/js/blocks_js/block_{$slug}.js" );
	if ( file_exists( $js ) ) {
		// A block that needs jQuery (or another script) can add it with this filter.
		$deps = apply_filters( 'gutenbergtheme_block_script_deps', array(), $slug );
		wp_enqueue_script( 'block-' . $slug, get_theme_file_uri( "/js/blocks_js/block_{$slug}.js" ), $deps, filemtime( $js ), true );
	}
}

/* ---------------------------------------------------------------------------
 * Render: template-parts/blocks/section_{slug}.php
 * ------------------------------------------------------------------------- */
function gutenbergtheme_acf_block_render_callback( $block, $content = '', $is_preview = false ) {
	$slug = str_replace( 'acf/', '', $block['name'] );

	// Inserter hover preview: show the static image instead of the real block.
	if ( ! empty( $block['data']['is_inserter_preview'] ) ) {
		$preview_path = get_theme_file_path( "/assets/block-previews/{$slug}.jpg" );
		if ( file_exists( $preview_path ) ) {
			echo '<img src="' . esc_url( get_theme_file_uri( "/assets/block-previews/{$slug}.jpg" ) ) . '" style="width:100%;height:auto;display:block;" alt="" />';
			return;
		}
	}

	$template = get_theme_file_path( "/template-parts/blocks/section_{$slug}.php" );
	if ( file_exists( $template ) ) {
		include $template;
	}
}

/* ---------------------------------------------------------------------------
 * Editor: load every block stylesheet in the block editor
 * ------------------------------------------------------------------------- */
add_action( 'enqueue_block_assets', 'gutenbergtheme_enqueue_all_block_styles_in_editor' );

function gutenbergtheme_enqueue_all_block_styles_in_editor() {
	if ( ! is_admin() ) {
		return; // The frontend is handled by gutenbergtheme_acf_block_enqueue_assets().
	}

	$css_files = glob( get_theme_file_path( '/css/block_*.css' ) );
	if ( ! $css_files ) {
		return;
	}

	foreach ( $css_files as $file ) {
		wp_enqueue_style(
			'editor-' . basename( $file, '.css' ),
			get_theme_file_uri( '/css/' . basename( $file ) ),
			array(),
			filemtime( $file )
		);
	}
}

/* ---------------------------------------------------------------------------
 * Choices for ACF select fields
 * NOTE: this changes every ACF select field whose name ends with "fontsize"
 * or "gradient", on the whole site. Make the condition more specific if it
 * clashes with other fields.
 * ------------------------------------------------------------------------- */
add_filter( 'acf/load_field/type=select', 'gutenbergtheme_dynamic_fontsize_choices' );

function gutenbergtheme_dynamic_fontsize_choices( $field ) {

	if ( isset( $field['name'] ) && str_ends_with( $field['name'], 'fontsize' ) ) {

		$field['choices'] = array(
			'default'   => 'Default',
			'large-65'  => 'Large - 65px',
			'large-50'  => 'Large - 50px',
			'large-45'  => 'Large - 45px',
			'medium-40' => 'Medium - 40px',
			'medium-35' => 'Medium - 35px',
			'medium-30' => 'Medium - 30px',
			'small-25'  => 'Small - 25px',
			'small-24'  => 'Small - 24px',
			'small-21'  => 'Small - 21px',
			'small-20'  => 'Small - 20px',
			'small-18'  => 'Small - 18px',
			'small-16'  => 'Small - 16px',
		);

	} elseif ( isset( $field['name'] ) && str_ends_with( $field['name'], 'gradient' ) ) {

		$field['choices'] = array(
			'skygray-gradient'  => 'Skygray Gradient',
			'white-transparent' => 'White Transparent',
		);

	}

	return $field;
}
