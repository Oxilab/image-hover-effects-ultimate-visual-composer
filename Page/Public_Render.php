<?php

namespace OXI_FLIP_BOX_PLUGINS\Page;

/**
 * Description of Public
 *
 * @author biplo
 */
class Public_Render {


    /**
     * Current Elements id
     *
     * @since 2.0.0
     */
    public $oxiid;

    /**
     * Current Elements Style Data
     *
     * @since 2.0.0
     */
    public $style = [];

    /**
     * Current Elements Style Full
     *
     * @since 2.0.0
     */
    public $dbdata = [];

    /**
     * Current Elements multiple list data
     *
     * @since 2.0.0
     */
    public $child = [];

    /**
     * Current Elements Global CSS Data
     *
     * @since 2.0.0
     */
    public $CSSDATA = [];

    /**
     * Current Elements Global CSS Data
     *
     * @since 2.0.0
     */
    public $inline_css;
    public $inline_js;

    /**
     * Current Elements Global JS Handle
     *
     * @since 2.0.0
     */
    public $JSHANDLE = 'flipbox-addons-jquery';

    /**
     * Current Elements Global DATA WRAPPER
     *
     * @since 2.0.0
     */
    public $WRAPPER;

    /**
     * Current Elements Admin Control
     *
     * @since 2.0.0
     */
    public $admin;

    /**
     * True while the Flipbox block renders its block editor preview
     * (Modules/Gutenberg.php), which works like a page builder preview.
     *
     * @since 3.1.0
     */
    public static $block_preview = false;



    /**
     * old empty old render
     *
     * @since 2.0.0
     */
    public function default_render() {
        echo '';
    }

    /**
     * Parse the raw CSS string from the database into a typed $styledata array.
     *
     * Replaces the old pattern used in every style file:
     *   $styledata = array_map( 'esc_attr', explode( '|', $this->dbdata['css'] ) );
     *
     * What this does differently:
     *   - Still runs esc_attr() on every value (XSS safety unchanged).
     *   - Auto-casts numeric strings to float so PHP 8 arithmetic works correctly.
     *     e.g. "10" → 10.0, "20.5" → 20.5
     *   - Non-numeric values (colors, font names, CSS classes) stay as strings.
     *     e.g. "rgba(0,0,0,0.5)" → "rgba(0,0,0,0.5)"  (is_numeric = false)
     *   - Empty strings stay as "" (not converted to 0).
     *
     * Compatible with PHP 5.6+.
     *
     * @param  string $css_string Raw CSS string from $this->dbdata['css'].
     * @return array
     */
    protected function parse_styledata( $css_string ) {
        return array_map(
            function ( $v ) {
                $v = esc_attr( $v );
                return is_numeric( $v ) ? (float) $v : $v;
            },
            explode( '|', $css_string )
        );
    }

    public function font_familly( $data = '' ) {

        $check = get_option( 'oxi_addons_google_font' );

        $custom = [
            'Arial' => '',
            'Helvetica+Neue' => '',
            'Courier+New' => '',
            'Times+New+Roman' => '',
            'Comic+Sans+MS' => '',
            'Verdana' => '',
            'Impact' => '',
            'cursive' => '',
            'inherit' => '',
        ];
        if ( $check != 'no' && ! array_key_exists( $data, $custom ) ) :
            wp_enqueue_style( '' . $data . '', 'https://fonts.googleapis.com/css?family=' . $data . '' );
        endif;
        $data = str_replace( '+', ' ', $data );
        $data = explode( ':', $data );
        return '"' . esc_attr( $data[0] ) . '"';
    }

    public function admin_name_validation( $data ) {
        $data = str_replace( '_', ' ', $data );
        $data = str_replace( '-', ' ', $data );
        $data = str_replace( '+', ' ', $data );
        return ucwords( $data );
    }

    public function name_converter( $data ) {
        $data = str_replace( '_', ' ', $data );
        $data = str_replace( '-', ' ', $data );
        $data = str_replace( '+', ' ', $data );
        return ucwords( $data );
    }

    public function text_render( $data ) {
        echo wp_kses_post( do_shortcode( str_replace( 'spTac', '&nbsp;', str_replace( 'spBac', '<br>', html_entity_decode( $data ) ) ), $ignore_html = false ) );
    }

    public function font_awesome_render( $data ) {
        $fadata = get_option( 'oxi_addons_font_awesome' );
        if ( $fadata != 'no' ) :
            wp_enqueue_style( 'font-awsome.min', OXI_FLIP_BOX_URL . 'asset/frontend/css/font-awsome.min.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        endif;
		?>
        <i class="<?php echo esc_attr( $data ); ?> oxi-icons"></i>
        <?php
    }
    /**
     * load css and js hooks
     *
     * @since 2.0.0
     */
    public function hooks() {
        $this->public_loader();
        $inlinecss = $this->inline_css;

        // Page builders (Elementor editor, Divi Visual Builder / its preview iframe)
        // render a module, then extract ONLY that module's outerHTML and inject it
        // into the editor canvas. Anything enqueued separately — the per-instance
        // CSS/JS added via wp_add_inline_style()/wp_add_inline_script() — prints in
        // a different node and is left behind, so the module appears unstyled in the
        // builder. In those contexts we instead emit the CSS/JS INLINE, inside the
        // rendered output, so it travels with the extracted module.
        $is_builder_ctx = $this->is_builder_context();

        if ( $this->inline_js != '' ) :
            $jquery = '(function ($) {' . $this->inline_js . '})(jQuery);';
            wp_add_inline_script( $this->JSHANDLE, $jquery );
            if ( $is_builder_ctx ) {
                echo '<script>' . $jquery . '</script>';
            }
        endif;

        if ( $this->inline_css != '' ) :
            $css = wp_kses_decode_entities( stripslashes( $inlinecss ) );
            if ( $is_builder_ctx ) {
                echo '<style>' . $css . '</style>';
            } else {
                wp_add_inline_style( 'flip-box-addons-style', $css );
            }
        endif;
    }

    /**
     * Whether we are rendering inside a page-builder editor / preview, where the
     * builder extracts a module's outerHTML and re-injects it into its own canvas
     * (so separately-enqueued inline CSS/JS would be lost).
     *
     * @since 3.0.2
     * @return bool
     */
    protected function is_builder_context() {
        // Block editor preview of the Flipbox block (REST block renderer).
        if ( self::$block_preview ) {
            return true;
        }
        // Elementor editor.
        if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance ) && isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return true;
        }
        // Divi 5 Visual Builder preview iframe (`?preview=true&et_vb_preview_id=N`).
        if ( isset( $_GET['et_vb_preview_id'] ) ) {
            return true;
        }
        // Divi 5 module-data REST render.
        if ( function_exists( 'et_builder_is_rest_api_request' ) && false !== et_builder_is_rest_api_request( '/module-data/shortcode-module' ) ) {
            return true;
        }
        // Divi Visual Builder / classic preview.
        if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
            return true;
        }
        if ( function_exists( 'is_et_pb_preview' ) && is_et_pb_preview() ) {
            return true;
        }
        if ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) {
            return true;
        }
        return false;
    }



    /**
     * front end loader css and js
     *
     * @since 2.0.0
     */
    public function public_loader() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'oxi-animation', OXI_FLIP_BOX_URL . 'asset/frontend/css/animation.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_style( 'flip-box-addons-style', OXI_FLIP_BOX_URL . 'asset/frontend/css/style.css', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_script( 'waypoints.min', OXI_FLIP_BOX_URL . 'asset/frontend/js/waypoints.min.js', false, OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_enqueue_script( 'flipbox-addons-jquery', OXI_FLIP_BOX_URL . 'asset/frontend/js/jquery.js', false, OXI_FLIP_BOX_PLUGIN_VERSION );
    }

    /**
     * load current element render since 2.0.0
     *
     * @since 2.0.0
     */
    public function render() {
        $click = 'click' === self::flip_trigger( isset( $this->dbdata['css'] ) ? $this->dbdata['css'] : '' );
        // Page builders inject the markup without running its scripts, so the
        // load guard (see boot_script()) is left out there.
        $booting = ! $this->is_builder_context();
        echo '<div class="oxi-addons-container ' . esc_attr( $this->WRAPPER ) . '  oxi-addons-flipbox-template-' . esc_attr( $this->dbdata['style_name'] ) . ( $click ? ' oxi-flip-trigger-click' : '' ) . ( $booting ? ' oxi-flip-booting' : '' ) . '">';
        $this->default_render( $this->style, $this->child, $this->admin );
        echo '</div>';
        if ( $booting ) {
            $this->boot_script();
        }
        if ( $click ) {
            $this->flip_trigger_script();
        }
    }

    /**
     * Load guard (3.1.0).
     *
     * The flip box CSS prints at the end of the page, after this markup, so the
     * browser first shows the boxes unstyled. When the CSS arrives, the
     * "transition: all" in style.css made every property animate into place
     * (sizes, colors and the flip itself), which looked like flashing and
     * shaking for a moment after a reload. The container carries
     * oxi-flip-booting, which turns transitions off (style.css), until the CSS
     * has been applied for one painted frame. Then this script removes it, so
     * hover and click flips animate exactly as before.
     *
     * The class never stays: it is also removed after about 5 seconds, and on
     * the first mouse over a box once the page has fully loaded (covers boxes
     * added to the page later without running scripts). Printed once per page.
     *
     * @since 3.1.0
     */
    protected function boot_script() {
        static $printed = false;
        if ( $printed ) {
            return;
        }
        $printed = true;
        $script = <<<'JS'
(function () {
    if (window.oxiFlipBoot) { return; }
    window.oxiFlipBoot = true;
    var released = false;
    function release() {
        var boxes = document.querySelectorAll('.oxi-flip-booting');
        for (var i = 0; i < boxes.length; i++) { boxes[i].classList.remove('oxi-flip-booting'); }
    }
    function styled() {
        var box = document.querySelector('.oxi-flip-booting');
        return !box || window.getComputedStyle(box).display === 'flex';
    }
    var frames = 0;
    function wait() {
        if (released) { return; }
        if ((document.readyState !== 'loading' && styled()) || frames > 300) {
            released = true;
            requestAnimationFrame(function () { requestAnimationFrame(release); });
            return;
        }
        frames++;
        requestAnimationFrame(wait);
    }
    requestAnimationFrame(wait);
    setTimeout(function () { released = true; release(); }, 5000);
    document.addEventListener('mouseover', function (e) {
        if (document.readyState !== 'complete' || !e.target || !e.target.closest) { return; }
        var box = e.target.closest('.oxi-flip-booting');
        if (box) { box.classList.remove('oxi-flip-booting'); }
    }, true);
})();
JS;
        // The attributes keep caching and "delay JavaScript" plugins from
        // holding this tiny script back.
        echo '<script data-no-optimize="1" data-no-defer="1" data-cfasync="false" nowprocket>' . $script . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static script.
    }

    /**
     * How a flip box flips: "hover" or "click".
     *
     * Saved as its own " flip-trigger |click|" pair at the end of the style
     * string and read by name, so the numbered values every design reads never
     * move. Anything else, including every flip box saved before 3.1.0, is
     * "hover": those keep working exactly as before.
     *
     * @since 3.1.0
     * @param  string $css Raw style string from the database.
     * @return string
     */
    public static function flip_trigger( $css ) {
        return ( is_string( $css ) && false !== strpos( $css, ' flip-trigger |click|' ) ) ? 'click' : 'hover';
    }

    /**
     * Load the On Click script, only for flip boxes that use it.
     *
     * @since 3.1.0
     */
    protected function flip_trigger_script() {
        wp_enqueue_script( 'oxi-flip-trigger', OXI_FLIP_BOX_URL . 'asset/frontend/js/flip-trigger.js', [], OXI_FLIP_BOX_PLUGIN_VERSION, true );
        // Page builders keep only the module's own markup, so it travels
        // inline there (the script guards against running twice).
        if ( $this->is_builder_context() ) {
            $script = file_get_contents( OXI_FLIP_BOX_PATH . 'asset/frontend/js/flip-trigger.js' );
            if ( $script ) {
                echo '<script>' . $script . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the plugin's own file.
            }
        }
    }

    public function admin_edit_panel( $id ) {

        if ( $this->admin == 'admin' ) :
			?>
            <div class="oxilab-admin-absulote">
                <div class="oxilab-style-absulate-edit">
                    <form method="post">
                        <input type="hidden" name="item-id" value="<?php echo esc_attr( $id ); ?>">
                        <button class="btn btn-primary" type="submit" value="edit" name="edit" title="Edit">Edit</button>
                        <?php echo wp_nonce_field( 'oxiflipeditdata' ); ?>
                    </form>
                </div>
                <div class="oxilab-style-absulate-clone">
                    <form method="post">
                        <input type="hidden" name="item-id" value="<?php echo esc_attr( $id ); ?>">
                        <button class="btn btn-light" type="submit" value="clone" name="clone" title="<?php esc_attr_e( 'Clone', 'oxi-flip-box-plugin' ); ?>"><?php esc_html_e( 'Clone', 'oxi-flip-box-plugin' ); ?></button>
                        <?php wp_nonce_field( 'oxiflipclonedata' ); ?>
                    </form>
                </div>
                <div class="oxilab-style-absulate-delete">
                    <form method="post" class="oxilab-style-absulate-delete-confirmation">
                        <input type="hidden" name="item-id" value="<?php echo esc_attr( $id ); ?>">
                        <button class="btn btn-danger" type="submit" value="delete" name="delete" title="Delete">Delete</button>
                        <?php echo wp_nonce_field( 'oxiflipdeletedata' ); ?>
                    </form>
                </div>
            </div>
			<?php
        endif;
    }

    /**
     * load constructor
     *
     * @since 2.0.0
     */
    public function __construct( array $dbdata = [], array $child = [], $admin = 'user' ) {
        if ( count( $dbdata ) > 0 ) :
            $this->dbdata = $dbdata;
            $this->child = $child;
            $this->admin = $admin;
            $this->loader();
        endif;
    }

    /**
     * Current element loader
     *
     * @since 2.0.0
     */
    public function loader() {
        $this->oxiid = $this->dbdata['id'];
        foreach ( $this->child as $key => $value ) {
            $this->child[ $key ]['files'] = $value['files'] . '{#}|{#}{#}|{#}{#}|{#}{#}|{#}{#}|{#}{#}|{#}{#}|{#}{#}|{#}{#}|{#}';
        }
        $this->render();
        $this->hooks();
    }
}
