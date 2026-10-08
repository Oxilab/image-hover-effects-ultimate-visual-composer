<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes;

/**
 * Assets Handler Class
 *
 * @since 2.10.1
 */
class Assets {

	/**
	 * Assets class constructor
	 *
	 * @since 2.10.1
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scriptss' ] );
		add_filter( 'admin_body_class', [ $this, 'editor_body_class' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'public_enqueue_scripts' ] );
		if ( class_exists( '\\Elementor\\Plugin' ) ) {
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_enqueue_styles' ] );
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'public_enqueue_scripts' ] );
			add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'public_enqueue_scripts' ] );
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'editor_enqueue_styles' ] );
			add_action( 'elementor/preview/enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
		}
	}

	/**
	 * Whether this request is the flip box editor.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	public function is_editor_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['page'] ) && 'oxi-flip-box-ultimate-new' === $_GET['page'] && ! empty( $_GET['styleid'] );
	}

	/**
	 * Mark the editor so editor.css only ever applies there.
	 *
	 * @since 3.1.0
	 *
	 * @param string $classes Admin body classes.
	 * @return string
	 */
	public function editor_body_class( $classes ) {
		if ( $this->is_editor_page() ) {
			$classes .= ' oxi-flip-editor-page';
		}
		return $classes;
	}

	/**
	 * Method admin_enqueue_scriptss.
	 *
	 * @since 2.10.1
	 */
	public function admin_enqueue_scriptss() {
		$current_screen = get_current_screen()->id;
		$current_page   = isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '';

		wp_enqueue_style( 'oxi_flip-global-admin-style', OXI_FLIP_BOX_URL . 'asset/backend/css/global-admin.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );

		if ( 'oxi-flip-box-ultimate-settings' === $current_page ) {
			wp_enqueue_style( 'oxi-flip-settings-css', OXI_FLIP_BOX_URL . 'asset/backend/css/settings.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/settings.css' ) );
		}

		// Flip box editor (Create New with a styleid): restyled by editor.css.
		if ( $this->is_editor_page() ) {
			wp_enqueue_style( 'oxi-flip-editor-css', OXI_FLIP_BOX_URL . 'asset/backend/css/editor.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/editor.css' ) );
		}

		// Template pages: the Create New picker (the same page with a styleid is
		// the editor, so leave that alone) and Import Templates.
		if ( ( 'oxi-flip-box-ultimate-new' === $current_page && empty( $_GET['styleid'] ) ) || 'oxi-flip-box-ultimate-import' === $current_page ) {
			wp_enqueue_style( 'oxi-flip-settings-css', OXI_FLIP_BOX_URL . 'asset/backend/css/settings.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/settings.css' ) );
			wp_enqueue_style( 'oxi-flip-templates-css', OXI_FLIP_BOX_URL . 'asset/backend/css/templates.css', [ 'oxi-flip-settings-css' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/templates.css' ) );
		}

		if ( 'oxi-flip-box-ultimate' === $current_page ) {
			wp_enqueue_style( 'oxi-flip-settings-css', OXI_FLIP_BOX_URL . 'asset/backend/css/settings.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/settings.css' ) );
			wp_enqueue_style( 'oxi-flip-home-css', OXI_FLIP_BOX_URL . 'asset/backend/css/home.css', [ 'oxi-flip-settings-css' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/home.css' ) );
		}

		// Freemius Account page: only our own scoped styles, no Bootstrap or
		// admin.css, so Freemius' forms and dialogs keep working as designed.
		if ( 'oxi-flip-box-ultimate-account' === $current_page ) {
			wp_enqueue_style( 'oxi-flip-settings-css', OXI_FLIP_BOX_URL . 'asset/backend/css/settings.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/settings.css' ) );
			wp_enqueue_style( 'oxi-flip-account-css', OXI_FLIP_BOX_URL . 'asset/backend/css/account.css', [ 'oxi-flip-settings-css' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/account.css' ) );
			wp_enqueue_style( 'oxi-flip-admin-menu-css', OXI_FLIP_BOX_URL . 'asset/backend/css/admin-menu.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/admin-menu.css' ) );
		}

		if ( 'flipbox-getting-started' === $current_page ) {
			//CSS
			wp_enqueue_style( 'flip-box-admin-welcome', OXI_FLIP_BOX_URL . 'asset/backend/css/getting-started.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/getting-started.css' ) );
			//JS
			wp_enqueue_script( 'flip-box-admin-welcome-js', OXI_FLIP_BOX_URL . 'asset/backend/js/getting-started.js', [ 'jquery' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/getting-started.js' ), true );
		}
	}

	/**
	 * Method public_enqueue_scripts.
	 *
	 * @since 2.10.1
	 */
	public function public_enqueue_scripts() {
	}

	public function editor_enqueue_styles() {
		wp_enqueue_style( 'oxi-animation', OXI_FLIP_BOX_URL . 'asset/frontend/css/animation.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
		wp_enqueue_style( 'flip-box-addons-style', OXI_FLIP_BOX_URL . 'asset/frontend/css/style.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
		wp_enqueue_style( 'flipbox-font-awesome', OXI_FLIP_BOX_URL . 'asset/frontend/css/font-awsome.min.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
	}

	public function editor_enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		$patch = "(function(){try{if(window.Backbone && Backbone.Model && Backbone.Model.prototype){var _url=Backbone.Model.prototype.url;Backbone.Model.prototype.url=function(){try{return _url.call(this);}catch(e){return window.ajaxurl||window.location.href;}}}}catch(e){}})();";
		wp_add_inline_script( 'jquery', $patch );
	}
}
