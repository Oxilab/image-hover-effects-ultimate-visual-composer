<?php

namespace OXI_FLIP_BOX_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * "How to use?" documentation menu.
 *
 * One source of links, rendered into the plugin's admin menu bar
 * (Inc_Helper/Admin_helper.php).
 *
 * @since 3.0.3
 */
class Docs {

    /**
     * Documentation entries, in menu order.
     *
     * @return array<int, array{label: string, url: string}>
     */
    public static function links() {
        return [
            [
                'label' => __( 'Create your first flip box', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/getting-started/quick-start-create-your-first-flip-box-in-5-minutes/',
            ],
            [
                'label' => __( 'Using the shortcode in posts & pages', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/using-the-flipbox-shortcode-in-posts-pages/',
            ],
            [
                'label' => __( 'Using it with Elementor', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-with-elementor/',
            ],
            [
                'label' => __( 'Using it with WPBakery', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-with-wpbakery-visual-composer/',
            ],
            [
                'label' => __( 'Using it as a WordPress widget', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-as-a-wordpress-widget/',
            ],
            [
                'label' => __( 'Browse all documentation', 'oxi-flip-box-plugin' ),
                'url'   => 'https://oxilab.dev/docs/flipbox/',
            ],
        ];
    }

    /**
     * Menu item for the plugin's admin menu bar (a list item in
     * ul.oxilab-sa-admin-menu).
     *
     * @return string
     */
    public static function nav_item() {
        $out = '<li class="saadmin-doc oxi-howto-item">'
            . '<a href="' . esc_url( self::links()[0]['url'] ) . '" target="_blank" rel="noopener noreferrer" class="oxi-howto-toggle">'
            . '<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>'
            . esc_html__( 'How to use?', 'oxi-flip-box-plugin' )
            . '</a><ul class="oxi-howto-menu">';

        foreach ( self::links() as $link ) {
            $out .= '<li><a href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener noreferrer">'
                . esc_html( $link['label'] ) . '</a></li>';
        }

        return $out . '</ul></li>';
    }
}
