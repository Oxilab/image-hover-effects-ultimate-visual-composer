<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Brands the Freemius Account page.
 *
 * Freemius renders this page from its own template, so nothing in the SDK
 * is edited. Its "templates/account.php" filter (meant for wrapping the
 * template) adds the plugin header and a scoping wrapper, and
 * asset/backend/css/account.css restyles the page inside that wrapper.
 *
 * @since 3.1.0
 */
class Account {

    public function __construct() {
        if ( ! function_exists( 'wpkin_fb_v' ) ) {
            return;
        }
        $fs = wpkin_fb_v();
        // The page only has one tab, so the tab bar adds nothing.
        $fs->add_filter( 'hide_account_tabs', '__return_true' );
        $fs->add_filter( 'templates/account.php', [ $this, 'wrap' ] );
        add_filter( 'admin_body_class', [ $this, 'body_class' ] );
    }

    /**
     * Mark the Account page so admin-menu.css can make the header full width.
     *
     * @param string $classes Admin body classes.
     * @return string
     */
    public function body_class( $classes ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['page'] ) && 'oxi-flip-box-ultimate-account' === $_GET['page'] ) {
            $classes .= ' oxi-flip-menu-page';
        }
        return $classes;
    }

    /**
     * Wrap the Freemius account template.
     *
     * @param string $html Rendered Freemius template.
     * @return string
     */
    public function wrap( $html ) {
        ob_start();
        // The plugin's header menu, same as on the other Flipbox pages.
        apply_filters( 'oxi-flip-box-plugin/admin_menu', true );

        // account.css shows this label after the version number, as a CSS
        // string, so it needs CSS string escaping before HTML escaping.
        $is_premium = apply_filters( 'oxi-flip-box-plugin/pro_version', false ) != false;
        $label      = '"' . addcslashes( __( 'Premium version', 'oxi-flip-box-plugin' ), '"\\' ) . '"';
        ?>
        <div class="oxi-flip-settings oxi-flip-account<?php echo $is_premium ? ' is-premium' : ''; ?>" style="<?php echo esc_attr( '--flip-premium-label: ' . $label ); ?>">
            <header class="oxi-flip-set-hero">
                <img class="oxi-flip-set-hero-logo" src="<?php echo esc_url( OXI_FLIP_BOX_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
                <div class="oxi-flip-set-hero-text">
                    <h1 class="oxi-flip-set-title"><?php esc_html_e( 'Account', 'oxi-flip-box-plugin' ); ?></h1>
                    <p class="oxi-flip-set-subtitle"><?php esc_html_e( 'Your Flipbox license, billing details and invoices.', 'oxi-flip-box-plugin' ); ?></p>
                </div>
                <a class="oxi-flip-set-btn is-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-settings' ) ); ?>">
                    <span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e( 'Settings', 'oxi-flip-box-plugin' ); ?>
                </a>
            </header>
            <hr class="wp-header-end">
        <?php
        return ob_get_clean() . $html . '</div>';
    }
}
