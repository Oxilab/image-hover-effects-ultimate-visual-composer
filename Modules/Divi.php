<?php

namespace OXI_FLIP_BOX_PLUGINS\Modules;

/**
 * Divi page builder addon for Flipbox.
 *
 * Registers a native Divi module that lets the user pick a saved Flipbox
 * style and outputs it through the [oxilab_flip_box] shortcode. Mirrors the
 * Elementor / Visual Composer addons under the same Modules folder.
 *
 * @since 3.0.2
 */
class Divi {

    /**
     * Module / shortcode slug used by Divi for this module.
     */
    const SLUG = 'oxilab_flip_box_divi';

    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\Public_Helper;

    public function __construct() {
        // Divi exposes its module base class once the builder framework is ready.
        // This makes the module appear inside the Divi/Visual Builder.
        add_action( 'et_builder_ready', [ $this, 'register' ] );

        // Front-end safety net. Divi 5 lazy-registers third-party module
        // shortcodes only for slugs stored in its `all_third_party_shortcode_slugs`
        // option, which it refreshes solely on theme/plugin activation. A module
        // added by code is therefore unknown to the front-end lazy loader, so the
        // saved `[oxilab_flip_box_divi]` tag is printed as raw text. We register
        // our own handler for the slug whenever Divi has not claimed it. Runs on
        // `wp` — after Divi's shortcode manager (init) and before `the_content`.
        if ( ! is_admin() ) {
            add_action( 'wp', [ $this, 'maybe_register_frontend_shortcode' ] );
        }

        // Inside the Divi Visual Builder the module is rendered via AJAX, so the
        // assets that Public_Render enqueues on demand never reach the builder
        // iframe and the flip box (absolutely positioned) collapses to nothing.
        // Pre-load the front-end flip box assets in the VB iframe so the preview
        // renders correctly.
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_builder_assets' ] );

        // Per-instance design CSS endpoint. In the Divi builder a selected Text
        // module is swapped to a TinyMCE editor that STRIPS the inline <style>
        // block carrying the flip box's visual styling, and Divi's preview
        // extractor only preserves <head> <link type="text/css"> (never <style>).
        // So we expose each flip box's design CSS as a real stylesheet URL and
        // enqueue it into <head>, where it survives selection. See
        // maybe_enqueue_builder_assets().
        add_action( 'wp_ajax_oxiflip_design_css', [ $this, 'output_design_css' ] );
        add_action( 'wp_ajax_nopriv_oxiflip_design_css', [ $this, 'output_design_css' ] );

        // When a module is selected/edited, Divi re-renders it through the classic
        // preview, then extracts the page's <head> stylesheets — but it ONLY keeps
        // <link> tags whose `type` is exactly "text/css". WordPress 5.3+ omits that
        // attribute, so the flip box CSS gets filtered out and the re-rendered
        // module loses its styles. Force `type="text/css"` onto our stylesheet tags
        // (in builder/preview contexts) so Divi's extractor keeps them.
        add_filter( 'style_loader_tag', [ $this, 'force_style_type' ], 10, 4 );
    }

    /**
     * Add an explicit `type="text/css"` to the flip box stylesheets so Divi's
     * Visual Builder preview extractor (which filters by `type === 'text/css'`)
     * keeps them when a module is selected/edited.
     *
     * @param string $tag    The full <link> tag.
     * @param string $handle Stylesheet handle.
     * @param string $href   Stylesheet URL.
     * @param string $media  Media attribute.
     * @return string
     */
    public function force_style_type( $tag, $handle, $href, $media ) {
        if ( ! self::is_builder() ) {
            return $tag;
        }
        $is_flipbox_asset = ( is_string( $href ) && strpos( $href, 'image-hover-effects-ultimate-visual-composer' ) !== false )
            || ( is_string( $href ) && strpos( $href, 'fonts.googleapis.com' ) !== false )
            || ( is_string( $href ) && strpos( $href, 'oxiflip_design_css' ) !== false )
            || 0 === strpos( (string) $handle, 'oxiflip-design-' )
            || in_array( $handle, [ 'oxi-animation', 'flip-box-addons-style', 'flipbox-font-awesome', 'font-awsome.min' ], true );
        if ( $is_flipbox_asset && false === strpos( $tag, ' type=' ) ) {
            $tag = str_replace( '<link ', '<link type="text/css" ', $tag );
        }
        return $tag;
    }

    /**
     * Enqueue the flip box front-end assets when the Divi Visual Builder is open.
     */
    public function maybe_enqueue_builder_assets() {
        $in_vb         = function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled();
        $in_d5_preview = isset( $_GET['et_vb_preview_id'] );
        // Classic preview iframe used when a module is selected/edited
        // (`?et_pb_preview=true&et_pb_preview_nonce=...`). The builder extracts the
        // page's <head> stylesheets from this response, so the flip box base CSS
        // must be enqueued early (in the head) here — otherwise it only prints late
        // in the footer and the re-rendered module loses its structure styles.
        $in_pb_preview = isset( $_GET['et_pb_preview_nonce'] )
            || ( function_exists( 'is_et_pb_preview' ) && is_et_pb_preview() );
        if ( ! $in_vb && ! $in_d5_preview && ! $in_pb_preview ) {
            return;
        }
        wp_enqueue_style( 'oxi-animation', OXI_FLIP_BOX_URL . 'asset/frontend/css/animation.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_style( 'flip-box-addons-style', OXI_FLIP_BOX_URL . 'asset/frontend/css/style.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_style( 'flipbox-font-awesome', OXI_FLIP_BOX_URL . 'asset/frontend/css/font-awsome.min.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'waypoints.min', OXI_FLIP_BOX_URL . 'asset/frontend/js/waypoints.min.js', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_script( 'flipbox-addons-jquery', OXI_FLIP_BOX_URL . 'asset/frontend/js/jquery.js', false, OXI_FLIP_BOX_PLUGIN_VERSION );

        // Each flip box's VISUAL styling (colors, backgrounds, dimensions) is
        // per-instance CSS that Public_Render normally prints inline. Inside the
        // builder that inline <style> is stripped when a Text module is selected
        // (TinyMCE) and Divi's preview extractor only keeps <head> stylesheets.
        // So enqueue every used flip box's design CSS as a real <head> stylesheet
        // link (served by output_design_css()), which survives selection.
        foreach ( $this->detect_flipbox_ids() as $fid ) {
            wp_enqueue_style(
                'oxiflip-design-' . $fid,
                add_query_arg(
                    [ 'action' => 'oxiflip_design_css', 'id' => $fid ],
                    admin_url( 'admin-ajax.php' )
                ),
                [ 'flip-box-addons-style' ],
                OXI_FLIP_BOX_PLUGIN_VERSION
            );
        }
    }

    /**
     * Detect the flip box style IDs in play for the current builder request so
     * their per-instance design CSS can be pre-loaded into <head>.
     *
     * @return int[] Unique style IDs.
     */
    protected function detect_flipbox_ids() {
        $content = '';
        // Classic preview (module selected/edited) posts the shortcode directly.
        if ( isset( $_POST['shortcode'] ) ) {
            $content = wp_unslash( $_POST['shortcode'] ); // phpcs:ignore WordPress.Security
        } else {
            $post = get_post();
            if ( $post instanceof \WP_Post ) {
                $content = $post->post_content;
            }
        }
        if ( '' === $content ) {
            return [];
        }
        // Match [oxilab_flip_box id="N"] and [oxilab_flip_box_divi id="N"].
        if ( ! preg_match_all( '/\[oxilab_flip_box(?:_divi)?[^\]]*\bid=["\']?(\d+)/', $content, $m ) ) {
            return [];
        }
        return array_values( array_unique( array_map( 'absint', $m[1] ) ) );
    }

    /**
     * Output a single flip box's per-instance design CSS as a stylesheet.
     *
     * Endpoint: admin-ajax.php?action=oxiflip_design_css&id=N
     */
    public function output_design_css() {
        $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security
        header( 'Content-Type: text/css; charset=utf-8' );
        if ( $id ) {
            echo $this->get_flipbox_design_css( $id ); // phpcs:ignore WordPress.Security.EscapeOutput -- CSS output.
        }
        exit;
    }

    /**
     * Build the per-instance design CSS for a flip box style ID without echoing
     * its HTML. Mirrors Public_Helper::shortcode_render but captures the renderer's
     * accumulated $inline_css.
     *
     * @param int $styleid
     * @return string
     */
    public function get_flipbox_design_css( $styleid ) {
        if ( null === $this->wpdb ) {
            $this->Public_loader();
        }
        $style = $this->wpdb->get_row( $this->wpdb->prepare( 'SELECT * FROM ' . $this->parent_table . ' WHERE id = %d', $styleid ), ARRAY_A );
        if ( empty( $style ) ) {
            return '';
        }
        $style_name = ucfirst( $style['style_name'] );
        $child      = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM $this->child_table WHERE styleid = %d ORDER by id ASC", $styleid ), ARRAY_A );
        $class      = 'OXI_FLIP_BOX_PLUGINS\\Public_Render\\' . $style_name;
        if ( ! class_exists( $class ) ) {
            return '';
        }
        // Instantiating runs the render (which accumulates $inline_css). Discard
        // the echoed HTML; we only want the CSS.
        ob_start();
        $renderer = new $class( $style, $child, 'user' );
        ob_end_clean();
        $css = isset( $renderer->inline_css ) ? $renderer->inline_css : '';
        return wp_kses_decode_entities( stripslashes( (string) $css ) );
    }

    /**
     * Register a front-end handler for the module slug when Divi has not
     * registered a real one (either no callback at all, or just its lazy
     * `__return_empty_string` placeholder).
     */
    public function maybe_register_frontend_shortcode() {
        global $shortcode_tags;

        $current  = isset( $shortcode_tags[ self::SLUG ] ) ? $shortcode_tags[ self::SLUG ] : null;
        $has_real = ( null !== $current && '__return_empty_string' !== $current );

        // Inside the Divi builder / D5 preview iframe the canvas locates each module
        // in the rendered page by its order-indexed wrapper class (e.g.
        // `oxilab_flip_box_divi_0`) — `querySelector('.{slug}_{order}')`. Only Divi's
        // own ET_Builder_Module render emits that wrapper; our raw fallback does not,
        // so the canvas can never bind the output and the module loads forever.
        // Therefore in builder context register Divi's wrapped handler (by
        // instantiating the module) instead of the raw fallback.
        if ( self::is_builder() ) {
            if ( ! $has_real && class_exists( '\ET_Builder_Module' ) ) {
                $this->register();
                $current  = isset( $shortcode_tags[ self::SLUG ] ) ? $shortcode_tags[ self::SLUG ] : null;
                $has_real = ( null !== $current && '__return_empty_string' !== $current );
            }
            if ( $has_real ) {
                return;
            }
        }

        if ( null === $current || '__return_empty_string' === $current ) {
            add_shortcode( self::SLUG, [ $this, 'frontend_shortcode' ] );
        }
    }

    /**
     * Render the module's saved shortcode on the front end.
     *
     * Divi saves the module as `[oxilab_flip_box_divi id="1" ...]`; we only need
     * the `id` and delegate to the plugin's own renderer. The base Public_Render
     * class enqueues every required asset when the shortcode runs.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function frontend_shortcode( $atts ) {
        $atts = shortcode_atts( [ 'id' => '' ], (array) $atts, self::SLUG );
        $id   = trim( (string) $atts['id'] );
        if ( '' === $id ) {
            // In the Visual Builder an empty render leaves the canvas preview
            // spinning forever, so output a visible placeholder there instead.
            return self::is_builder() ? self::placeholder() : '';
        }
        ob_start();
        $this->shortcode_render( $id, 'user' );
        return ob_get_clean();
    }

    /**
     * Whether we are rendering inside the Divi builder / its preview request.
     *
     * @return bool
     */
    public static function is_builder() {
        // Divi 5 renders the canvas preview by loading the page in a hidden iframe
        // with `?preview=true&et_vb_preview_id=N`; this param only appears there.
        if ( isset( $_GET['et_vb_preview_id'] ) ) {
            return true;
        }
        // Divi 5 also renders module previews through the REST endpoint
        // `/module-data/shortcode-module`; in that request none of the classic
        // builder flags below are set, so detect it explicitly.
        if ( function_exists( 'et_builder_is_rest_api_request' )
            && false !== et_builder_is_rest_api_request( '/module-data/shortcode-module' ) ) {
            return true;
        }
        return ( function_exists( 'is_et_pb_preview' ) && is_et_pb_preview() )
            || ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() )
            || ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() );
    }

    /**
     * Placeholder shown in the builder when no Flip box has been selected yet.
     *
     * @return string
     */
    public static function placeholder() {
        return '<div style="padding:40px 20px;text-align:center;border:1px dashed #bbb;color:#666;font-size:15px;background:#fafafa;">'
            . esc_html__( 'Flipbox: please open the settings and select a Flip box to display.', 'oxi-flip-box-plugin' )
            . '</div>';
    }

    /**
     * Define and instantiate the Divi module.
     *
     * Divi auto-registers any ET_Builder_Module subclass the moment it is
     * instantiated, so we only need to declare the class and create one.
     */
    public function register() {
        static $instantiated = false;
        if ( ! class_exists( '\ET_Builder_Module' ) ) {
            return;
        }
        if ( ! class_exists( __NAMESPACE__ . '\Flipbox_Divi_Module' ) ) {
            oxilab_define_flipbox_divi_module();
        }
        if ( ! $instantiated && class_exists( __NAMESPACE__ . '\Flipbox_Divi_Module' ) ) {
            new Flipbox_Divi_Module();
            $instantiated = true;
        }
    }
}

/**
 * Defines the Flipbox Divi module class.
 *
 * Kept inside a function (like the Elementor addon) so the class body is only
 * parsed once Divi's \ET_Builder_Module base class is guaranteed to exist.
 */
function oxilab_define_flipbox_divi_module() {

    if ( ! class_exists( '\ET_Builder_Module' ) || class_exists( __NAMESPACE__ . '\Flipbox_Divi_Module' ) ) {
        return;
    }

    class Flipbox_Divi_Module extends \ET_Builder_Module {

        public $slug       = 'oxilab_flip_box_divi';
        public $vb_support  = 'on';

        /**
         * Module meta.
         */
        public function init() {
            $this->name = esc_html__( 'Flipbox', 'oxi-flip-box-plugin' );

            // Custom SVG icon shipped with the plugin (shown in the Divi module
            // list). Divi inlines the raw file contents into the builder's React
            // data, so the SVG must be clean — no XML prolog and no <style> block,
            // both of which break the builder app (infinite loading). `divi-icon.svg`
            // is the sanitized version (fills inlined as attributes).
            if ( defined( 'OXI_FLIP_BOX_PATH' ) ) {
                $this->icon_path = OXI_FLIP_BOX_PATH . 'image/divi-icon.svg';
            }

            $this->settings_modal_toggles = [
                'general' => [
                    'toggles' => [
                        'main_content' => esc_html__( 'Flipbox', 'oxi-flip-box-plugin' ),
                    ],
                ],
            ];
        }

        /**
         * Fetch saved Flipbox styles for the dropdown.
         *
         * @return array<string,string> id => label
         */
        protected function flipbox_options() {
            global $wpdb;
            $options = [];
            $table   = $wpdb->prefix . 'oxi_div_style';
            $rows    = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$table} WHERE type = %s ORDER BY id DESC", 'flip' ), ARRAY_A );
            if ( $rows ) {
                foreach ( $rows as $row ) {
                    $label                        = ( isset( $row['name'] ) && $row['name'] !== '' ? $row['name'] : 'Flipbox' ) . ' (#' . $row['id'] . ')';
                    $options[ (string) $row['id'] ] = $label;
                }
            }
            return $options;
        }

        /**
         * Module fields (controls).
         */
        public function get_fields() {
            $options = $this->flipbox_options();
            // Default to the first saved Flipbox so a freshly-added module renders
            // a real preview immediately instead of an empty/placeholder state.
            $default = '';
            if ( ! empty( $options ) ) {
                $keys    = array_keys( $options );
                $default = (string) reset( $keys );
            }
            return [
                'id' => [
                    'label'           => esc_html__( 'Select Flipbox', 'oxi-flip-box-plugin' ),
                    'type'            => 'select',
                    'option_category' => 'basic_option',
                    'options'         => $options,
                    'default'         => $default,
                    'description'     => esc_html__( 'Choose a saved Flipbox style to display.', 'oxi-flip-box-plugin' ),
                    'toggle_slug'     => 'main_content',
                ],
            ];
        }

        /**
         * Frontend / Visual Builder output.
         *
         * The base Public_Render class enqueues every required asset
         * (animation, style, waypoints, jQuery) when the shortcode renders.
         */
        public function render( $attrs, $content, $render_slug ) {
            $id = isset( $this->props['id'] ) ? $this->props['id'] : '';
            if ( '' === trim( (string) $id ) ) {
                return Divi::is_builder() ? Divi::placeholder() : '';
            }
            return do_shortcode( '[oxilab_flip_box id="' . esc_attr( $id ) . '"]' );
        }
    }
}
