<?php

namespace OXI_FLIP_BOX_PLUGINS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Flipbox block for the block editor (Gutenberg).
 *
 * A dynamic block: it saves only the flip box id and renders through the
 * [oxilab_flip_box] shortcode, so the page shows exactly what the shortcode
 * shows. In the editor, ServerSideRender asks the REST block renderer for a
 * live preview. That request never prints the page footer, where the flip
 * box CSS normally goes, so the preview carries its CSS with it (see
 * render_block()).
 *
 * @since 3.1.0
 */
class Gutenberg {

    /**
     * Block name (also in asset/blocks/flipbox/block.json).
     */
    const BLOCK = 'oxi-flip-box/flipbox';

    /**
     * Editor script handle.
     */
    const SCRIPT = 'oxi-flip-box-block';

    /**
     * Editor style handle.
     */
    const STYLE = 'oxi-flip-box-block-editor';

    public function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
        add_action( 'enqueue_block_editor_assets', [ $this, 'editor_data' ] );
    }

    /**
     * Register the block, its editor script and its editor styles.
     *
     * @since 3.1.0
     */
    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        wp_register_script(
            self::SCRIPT,
            OXI_FLIP_BOX_URL . 'asset/backend/js/gutenberg-block.js',
            [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ],
            OXI_FLIP_BOX_PLUGIN_VERSION,
            true
        );
        wp_set_script_translations( self::SCRIPT, 'oxi-flip-box-plugin' );

        // The flip box structure CSS, loaded into the editor canvas. Each flip
        // box's own design CSS comes inside its preview.
        wp_register_style( self::STYLE . '-base', OXI_FLIP_BOX_URL . 'asset/frontend/css/style.css', [], OXI_FLIP_BOX_PLUGIN_VERSION );
        wp_register_style( self::STYLE, OXI_FLIP_BOX_URL . 'asset/backend/css/gutenberg-block.css', [ self::STYLE . '-base' ], OXI_FLIP_BOX_PLUGIN_VERSION );

        register_block_type(
            OXI_FLIP_BOX_PATH . 'asset/blocks/flipbox',
            [
                'editor_script_handles' => [ self::SCRIPT ],
                'editor_style_handles'  => [ self::STYLE ],
                'render_callback'       => [ $this, 'render_block' ],
            ]
        );
    }

    /**
     * The flip boxes to choose from, and the admin links the block shows.
     *
     * @since 3.1.0
     */
    public function editor_data() {
        global $wpdb;

        $table     = $wpdb->prefix . 'oxi_div_style';
        $rows      = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$table} WHERE type = %s ORDER BY id DESC", 'flip' ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from $wpdb->prefix.
        $flipboxes = [];
        if ( $rows ) {
            foreach ( $rows as $row ) {
                $flipboxes[] = [
                    'value' => (string) $row['id'],
                    'label' => ( isset( $row['name'] ) && '' !== $row['name'] ? $row['name'] : 'Flipbox' ) . ' (#' . $row['id'] . ')',
                ];
            }
        }

        $data = [
            'flipboxes' => $flipboxes,
            'editUrl'   => admin_url( 'admin.php?page=oxi-flip-box-ultimate-new&styleid=' ),
            'createUrl' => admin_url( 'admin.php?page=oxi-flip-box-ultimate-new' ),
        ];
        wp_add_inline_script( self::SCRIPT, 'window.oxiFlipBlockData = ' . wp_json_encode( $data ) . ';', 'before' );
    }

    /**
     * Render the block.
     *
     * @since 3.1.0
     *
     * @param array $attributes Block attributes.
     * @return string
     */
    public function render_block( $attributes ) {
        $id      = isset( $attributes['flipboxId'] ) ? absint( $attributes['flipboxId'] ) : 0;
        $preview = self::is_preview();

        if ( ! $id || ! $this->exists( $id ) ) {
            // Nothing to show on the site. The editor names the problem.
            return $preview && $id ? '<div class="oxi-flip-block-notice">' . esc_html__( 'This flip box no longer exists. Choose another one in the block settings.', 'oxi-flip-box-plugin' ) . '</div>' : '';
        }

        if ( ! $preview ) {
            $shortcode = '[oxilab_flip_box id="' . $id . '"]';
            // In the_content (and widget blocks) blocks render before
            // wptexturize() and do_shortcode(). Leave the shortcode in place
            // so it expands at its usual time, exactly like a shortcode typed
            // into the content: rendered here, wptexturize() would rewrite the
            // flip box's inline script (its "&&" became "&#038;&#038;").
            $filter = current_filter();
            $later  = $filter ? has_filter( $filter, 'do_shortcode' ) : false;
            if ( false !== $later && $later > (int) has_filter( $filter, 'do_blocks' ) ) {
                return '<div ' . get_block_wrapper_attributes() . '>' . $shortcode . '</div>';
            }
            $html = do_shortcode( $shortcode );
            return '' === trim( $html ) ? '' : '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>';
        }

        // Editor preview: render as a page builder does, so the flip box's
        // design CSS (and its click script) comes inline with the markup.
        $queue = wp_styles()->queue;
        \OXI_FLIP_BOX_PLUGINS\Page\Public_Render::$block_preview = true;
        $html = do_shortcode( '[oxilab_flip_box id="' . $id . '"]' );
        \OXI_FLIP_BOX_PLUGINS\Page\Public_Render::$block_preview = false;

        // Stylesheets the flip box asked for while rendering (Font Awesome,
        // Google Fonts) would print in a footer this request never has: link
        // them here. style.css is already in the editor; animation.css is left
        // out on purpose, the preview never runs the scroll-in animations.
        $links = '';
        foreach ( array_diff( wp_styles()->queue, $queue ) as $handle ) {
            if ( in_array( $handle, [ 'oxi-animation', 'flip-box-addons-style' ], true ) || empty( wp_styles()->registered[ $handle ]->src ) ) {
                continue;
            }
            $style = wp_styles()->registered[ $handle ];
            $href  = $style->ver ? add_query_arg( 'ver', $style->ver, $style->src ) : $style->src;
            $links .= '<link rel="stylesheet" href="' . esc_url( $href ) . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- the preview has no footer to print it in.
        }

        return $links . '<div class="oxi-flip-block-preview">' . $html . '</div>';
    }

    /**
     * Whether this is the editor's preview request (REST block renderer).
     *
     * @since 3.1.0
     * @return bool
     */
    public static function is_preview() {
        if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
            return false;
        }
        $route = isset( $GLOBALS['wp']->query_vars['rest_route'] ) ? $GLOBALS['wp']->query_vars['rest_route'] : '';
        return 0 === strpos( $route, '/wp/v2/block-renderer/' . self::BLOCK );
    }

    /**
     * Whether a flip box with this id exists.
     *
     * @since 3.1.0
     *
     * @param int $id Flip box id.
     * @return bool
     */
    protected function exists( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'oxi_div_style';
        return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND type = %s", $id, 'flip' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from $wpdb->prefix.
    }
}
