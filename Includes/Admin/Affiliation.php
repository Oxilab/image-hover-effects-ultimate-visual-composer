<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Brands the Freemius Affiliation page.
 *
 * Same approach as the Account page: Freemius' "/forms/affiliation.php"
 * filter wraps its template with the plugin header, a hero and a
 * "How it works" card, and asset/backend/css/affiliation.css restyles the
 * template inside that wrapper. The SDK is not edited, so its application
 * form and its submit keep working as shipped.
 *
 * @since 3.1.2
 */
class Affiliation {

    public function __construct() {
        if ( ! function_exists( 'wpkin_fb_v' ) ) {
            return;
        }
        wpkin_fb_v()->add_filter( '/forms/affiliation.php', [ $this, 'wrap' ] );
        add_filter( 'admin_body_class', [ $this, 'body_class' ] );
    }

    /**
     * Mark the page: full width header (admin-menu.css) and no notices (account.css).
     *
     * @param string $classes Admin body classes.
     * @return string
     */
    public function body_class( $classes ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only reads which admin page is open.
        if ( isset( $_GET['page'] ) && 'oxi-flip-box-ultimate-affiliation' === $_GET['page'] ) {
            $classes .= ' oxi-flip-menu-page'; // oxi-flip-no-notices comes from Account::body_class().
        }
        return $classes;
    }

    /**
     * Wrap the Freemius affiliation template.
     *
     * @param string $html Rendered Freemius template.
     * @return string
     */
    public function wrap( $html ) {
        $fs        = wpkin_fb_v();
        $terms     = $fs->get_affiliate_terms();
        $affiliate = $fs->get_affiliate();

        // Freemius formats it, for example "30%" or "$20".
        $commission = is_object( $terms ) ? $terms->get_formatted_commission() : '';
        $is_active  = is_object( $affiliate ) && $affiliate->is_active();

        ob_start();
        // The plugin's header menu, same as on the other Flipbox pages.
        apply_filters( 'oxi-flip-box-plugin/admin_menu', true ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- the plugin's existing header menu hook, used by every Flipbox page.
        ?>
        <div class="oxi-flip-settings oxi-flip-account oxi-flip-affiliation">
            <header class="oxi-flip-set-hero">
                <img class="oxi-flip-set-hero-logo" src="<?php echo esc_url( OXI_FLIP_BOX_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
                <div class="oxi-flip-set-hero-text">
                    <h1 class="oxi-flip-set-title"><?php esc_html_e( 'Affiliate Program', 'oxi-flip-box-plugin' ); ?></h1>
                    <p class="oxi-flip-set-subtitle">
                        <?php
                        if ( '' !== $commission ) {
                            /* translators: %s: commission, for example 30% */
                            echo esc_html( sprintf( __( 'Recommend Flipbox and earn %s commission on every sale you refer.', 'oxi-flip-box-plugin' ), $commission ) );
                        } else {
                            esc_html_e( 'Recommend Flipbox and earn a commission on every sale you refer.', 'oxi-flip-box-plugin' );
                        }
                        ?>
                    </p>
                </div>
                <?php if ( '' !== $commission ) : ?>
                    <div class="oxi-flip-aff-rate">
                        <span class="oxi-flip-aff-rate-value"><?php echo esc_html( $commission ); ?></span>
                        <span class="oxi-flip-aff-rate-label"><?php esc_html_e( 'commission', 'oxi-flip-box-plugin' ); ?></span>
                    </div>
                <?php endif; ?>
            </header>
            <hr class="wp-header-end">
            <div class="oxi-flip-aff-layout">
                <div class="oxi-flip-aff-main">
        <?php
        $before = ob_get_clean();

        ob_start();
        ?>
                </div>
                <aside class="oxi-flip-aff-aside">
                    <div class="oxi-flip-aff-card">
                        <h2 class="oxi-flip-aff-card-title"><?php esc_html_e( 'How it works', 'oxi-flip-box-plugin' ); ?></h2>
                        <ol class="oxi-flip-aff-steps">
                            <li>
                                <strong><?php esc_html_e( 'Apply', 'oxi-flip-box-plugin' ); ?></strong>
                                <span><?php esc_html_e( 'Fill in the short application form and accept the program terms.', 'oxi-flip-box-plugin' ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Get approved', 'oxi-flip-box-plugin' ); ?></strong>
                                <span><?php esc_html_e( 'We review every application and reply within 14 days.', 'oxi-flip-box-plugin' ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Share and earn', 'oxi-flip-box-plugin' ); ?></strong>
                                <span><?php esc_html_e( 'Get your referral link from the affiliate area. Commissions are paid monthly in USD via PayPal.', 'oxi-flip-box-plugin' ); ?></span>
                            </li>
                        </ol>
                        <?php if ( $is_active ) : ?>
                            <a class="oxi-flip-set-btn is-primary" href="https://users.freemius.com/login" target="_blank" rel="noopener">
                                <span class="dashicons dashicons-external" aria-hidden="true"></span><?php esc_html_e( 'Open affiliate area', 'oxi-flip-box-plugin' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
        <?php
        return $before . $html . ob_get_clean();
    }
}
