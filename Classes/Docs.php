<?php

namespace OXI_FLIP_BOX_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * "How to use?" documentation menu.
 *
 * One source of links, rendered into the plugin's admin menu bar
 * (Inc_Helper/Admin_helper.php). Styles: asset/backend/css/howto-menu.css.
 *
 * @since 3.0.3
 */
class Docs {

    /**
     * Documentation entries, in menu order. The last one is the
     * "browse everything" link shown at the bottom of the menu.
     *
     * @return array<int, array{label: string, desc: string, icon: string, url: string}>
     */
    public static function links() {
        return [
            [
                'label' => __( 'Create your first flip box', 'oxi-flip-box-plugin' ),
                'desc'  => __( 'Pick a design and publish it in 5 minutes', 'oxi-flip-box-plugin' ),
                'icon'  => 'flipbox',
                'url'   => 'https://oxilab.dev/docs/flipbox/getting-started/quick-start-create-your-first-flip-box-in-5-minutes/',
            ],
            [
                'label' => __( 'Using the shortcode in posts & pages', 'oxi-flip-box-plugin' ),
                'desc'  => __( 'Paste the shortcode anywhere in your content', 'oxi-flip-box-plugin' ),
                'icon'  => 'shortcode',
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/using-the-flipbox-shortcode-in-posts-pages/',
            ],
            [
                'label' => __( 'Using it with Elementor', 'oxi-flip-box-plugin' ),
                'desc'  => __( 'Add the Flipbox widget to any Elementor layout', 'oxi-flip-box-plugin' ),
                'icon'  => 'elementor',
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-with-elementor/',
            ],
            [
                'label' => __( 'Using it with WPBakery', 'oxi-flip-box-plugin' ),
                'desc'  => __( 'Add flip boxes to your WPBakery layouts', 'oxi-flip-box-plugin' ),
                'icon'  => 'wpbakery',
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-with-wpbakery-visual-composer/',
            ],
            [
                'label' => __( 'Using it as a WordPress widget', 'oxi-flip-box-plugin' ),
                'desc'  => __( 'Show a flip box in a sidebar or footer', 'oxi-flip-box-plugin' ),
                'icon'  => 'wordpress',
                'url'   => 'https://oxilab.dev/docs/flipbox/page-builder-integration/how-to-use-flipbox-as-a-wordpress-widget/',
            ],
            [
                'label' => __( 'Browse all documentation', 'oxi-flip-box-plugin' ),
                'desc'  => '',
                'icon'  => 'book',
                'url'   => 'https://oxilab.dev/docs/flipbox/',
            ],
        ];
    }

    /**
     * Logo for a guide: the tool's own mark where there is one.
     *
     * Elementor: the glyph from Elementor's own icon font (eicon-elementor,
     * eicons.svg) on its brand circle. WPBakery: its own logo image. WordPress:
     * the WordPress logo from Dashicons. Flipbox: the plugin's own mark.
     *
     * @param  string $key Icon key from links().
     * @return string
     */
    public static function icon( $key ) {
        switch ( $key ) {
            case 'flipbox':
                return '<svg class="oxi-howto-logo oxi-howto-logo-flipbox" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
                    . '<rect width="24" height="24" rx="5" fill="#17bcb5"/>'
                    . '<g fill="#ffffff" transform="translate(12 12) scale(1.12) translate(-12.03 -12.18)">'
                    . '<polygon points="11.6 17.05 11.6 20.19 9.41 18.9 7.79 17.94 4.89 16.23 4.89 13.11 4.91 13.1 7.55 14.66 7.82 14.82 10.45 16.37 11.6 17.05"/>'
                    . '<polygon points="19.18 13.1 19.18 13.12 16.57 14.66 15.01 13.74 14.96 13.71 17.59 12.15 17.8 12.28 19.18 13.1"/>'
                    . '<polygon points="19.18 13.12 19.18 16.26 14.72 18.9 12.5 20.21 12.5 17.07 13.68 16.37 16.31 14.82 16.57 14.66 19.18 13.12"/>'
                    . '<polygon points="9.17 13.71 9.12 13.73 9.12 13.73 7.55 14.66 4.91 13.1 5.17 12.94 6.52 12.14 9.17 13.71"/>'
                    . '<polygon points="11.6 12.03 11.6 15.14 9.43 13.86 9.43 13.86 9.17 13.71 6.52 12.14 4.9 11.18 4.89 11.18 4.89 8.11 4.93 8.09 7.53 9.63 11.6 12.03"/>'
                    . '<polygon points="19.18 8.07 19.18 8.1 16.6 9.62 12.5 7.2 12.5 4.12 14.68 5.4 19.18 8.07"/>'
                    . '<polygon points="11.6 4.15 11.6 7.23 7.53 9.63 4.93 8.09 9.47 5.41 9.47 5.41 11.6 4.15"/>'
                    . '<polygon points="19.18 8.1 19.18 11.21 17.59 12.15 14.96 13.71 14.69 13.86 12.5 15.16 12.5 12.05 16.6 9.62 19.18 8.1"/>'
                    . '</g></svg>';
            case 'elementor':
                return '<svg class="oxi-howto-logo" viewBox="0 0 32 32" aria-hidden="true" focusable="false">'
                    . '<circle cx="16" cy="16" r="16" fill="#92003b"/>'
                    . '<path fill="#ffffff" transform="translate(9.5 9.5) scale(0.013) matrix(1 0 0 -1 0 850)" d="M201-150h-201v1000h201v-1000z m803 0h-602v200h602v-200z m0 400h-602v200h602v-200z m0 400h-602v200h602v-200z"/>'
                    . '</svg>';
            case 'wpbakery':
                return '<img class="oxi-howto-logo" src="' . esc_url( OXI_FLIP_BOX_URL . 'image/brand/wpbakery.png' ) . '" alt="" width="24" height="24">';
            case 'wordpress':
                return '<span class="oxi-howto-logo oxi-howto-logo-wordpress dashicons dashicons-wordpress" aria-hidden="true"></span>';
            default:
                return '<span class="oxi-howto-logo oxi-howto-logo-dashicon dashicons dashicons-' . esc_attr( $key ) . '" aria-hidden="true"></span>';
        }
    }

    /**
     * Menu item for the plugin's admin menu bar (a list item in
     * ul.oxilab-sa-admin-menu). Opens on hover and on keyboard focus.
     *
     * @return string
     */
    public static function nav_item() {
        $links = self::links();
        $all   = array_pop( $links );

        $out = '<li class="saadmin-doc oxi-howto-item">'
            . '<a href="' . esc_url( $links[0]['url'] ) . '" target="_blank" rel="noopener noreferrer" class="oxi-howto-toggle" aria-haspopup="true">'
            // An SVG, not a Dashicon: Dashicons draw low in their box, so the
            // "?" sat below the label.
            . '<svg class="oxi-howto-q" viewBox="0 0 20 20" aria-hidden="true" focusable="false">'
            . '<circle cx="10" cy="10" r="9" fill="currentColor"/>'
            . '<path d="M7.7 7.7a2.35 2.35 0 1 1 3.3 2.15c-.62.29-1 .83-1 1.5v.35" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round"/>'
            . '<circle cx="10" cy="14.4" r="1.05" fill="#ffffff"/>'
            . '</svg>'
            . esc_html__( 'How to use?', 'oxi-flip-box-plugin' )
            . '</a>'
            . '<div class="oxi-howto-menu">'
            . '<div class="oxi-howto-head">'
            . '<span class="oxi-howto-head-title">' . esc_html__( 'Guides', 'oxi-flip-box-plugin' ) . '</span>'
            . '<span class="oxi-howto-head-text">' . esc_html__( 'Step by step help for every way to use Flipbox', 'oxi-flip-box-plugin' ) . '</span>'
            . '</div>'
            . '<ul class="oxi-howto-list">';

        foreach ( $links as $link ) {
            $out .= '<li><a class="oxi-howto-link" href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener noreferrer">'
                . '<span class="oxi-howto-icon" aria-hidden="true">' . self::icon( $link['icon'] ) . '</span>'
                . '<span class="oxi-howto-text">'
                . '<span class="oxi-howto-label">' . esc_html( $link['label'] ) . '</span>'
                . '<span class="oxi-howto-desc">' . esc_html( $link['desc'] ) . '</span>'
                . '</span>'
                . '</a></li>';
        }

        return $out . '</ul>'
            . '<a class="oxi-howto-all" href="' . esc_url( $all['url'] ) . '" target="_blank" rel="noopener noreferrer">'
            . '<span class="dashicons dashicons-' . esc_attr( $all['icon'] ) . '" aria-hidden="true"></span>'
            . '<span class="oxi-howto-all-label">' . esc_html( $all['label'] ) . '</span>'
            . '<span class="dashicons dashicons-external" aria-hidden="true"></span>'
            . '</a>'
            . '</div></li>';
    }
}
