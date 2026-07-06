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
	 * Method admin_enqueue_scriptss.
	 *
	 * @since 2.10.1
	 */
	public function admin_enqueue_scriptss() {
		$current_screen = get_current_screen()->id;
		$current_page   = isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '';

		wp_enqueue_style( 'oxi_flip-global-admin-style', OXI_FLIP_BOX_URL . 'asset/backend/css/global-admin.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );

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
