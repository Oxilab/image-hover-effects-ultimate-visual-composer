<?php

namespace OXI_FLIP_BOX_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Pro upgrade notice.
 *
 * Shown to free users who have had the plugin installed for more than 10 days.
 *
 * Scope is deliberately narrow, per the WordPress.org plugin guidelines
 * (guideline 11, "Plugins should not hijack the admin dashboard"): upgrade
 * prompts must be limited in scope and used sparingly, contextually or only on
 * the plugin's own screens. So this notice renders ONLY on this plugin's admin
 * pages, never site-wide, and is dismissible both temporarily and permanently.
 * The pricing link carries no referral or tracking parameters (guideline 7).
 *
 * @since 3.0.3
 */
class Support_Upgrade {

    /**
     * Admin pages this notice is allowed to appear on.
     *
     * @var string[]
     */
    private $allowed_pages = [
        'oxi-flip-box-ultimate',
        'oxi-flip-box-ultimate-new',
        'oxi-flip-box-ultimate-import',
        'oxi-flip-box-ultimate-settings',
    ];

    public function __construct() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        add_action( 'wp_ajax_oxilab_flip_upgrade_dissmiss', [ $this, 'ajax_action' ] );
        add_action( 'admin_notices', [ $this, 'render' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
    }

    /**
     * Whether the current request is one of this plugin's own admin screens.
     *
     * @return bool
     */
    private function is_plugin_screen() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

        return in_array( $page, $this->allowed_pages, true );
    }

    /**
     * Notice markup.
     *
     * @return void
     */
    public function render() {
        if ( ! $this->is_plugin_screen() ) {
            return;
        }

        $image   = OXI_FLIP_BOX_URL . 'image/logo.png';
        $pricing = 'https://oxilab.dev/flipbox/pricing';

        $features = [
            __( '70+ premium styles', 'oxi-flip-box-plugin' ),
            __( 'Advanced flip animations', 'oxi-flip-box-plugin' ),
            __( 'Full design customization', 'oxi-flip-box-plugin' ),
            __( 'Priority support', 'oxi-flip-box-plugin' ),
        ];
        ?>
        <div class="notice oxi-flip-upgrade-notice oxilab-flipbox-upgrade-notice">

            <div class="oxi-flip-upgrade-notice__logo">
                <img src="<?php echo esc_url( $image ); ?>"
                    alt="<?php esc_attr_e( 'Flipbox', 'oxi-flip-box-plugin' ); ?>">
            </div>

            <div class="oxi-flip-upgrade-notice__body">

                <span class="oxi-flip-upgrade-notice__badge">
                    <?php esc_html_e( 'Pro', 'oxi-flip-box-plugin' ); ?>
                </span>

                <h3 class="oxi-flip-upgrade-notice__title">
                    <?php esc_html_e( 'Ready for the full style library?', 'oxi-flip-box-plugin' ); ?>
                </h3>

                <p class="oxi-flip-upgrade-notice__text">
                    <?php esc_html_e( 'You have been building with the free version for a while. Pro adds the complete style library, advanced animations, deeper design control, and direct support from the team that builds the plugin.', 'oxi-flip-box-plugin' ); ?>
                </p>

                <ul class="oxi-flip-upgrade-notice__features">
                    <?php foreach ( $features as $feature ) : ?>
                        <li>
                            <span class="dashicons dashicons-yes" aria-hidden="true"></span>
                            <?php echo esc_html( $feature ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="oxi-flip-upgrade-notice__actions">
                    <?php // Plain pricing link, no referral or tracking parameters. ?>
                    <a class="oxi-flip-upgrade-btn oxi-flip-upgrade-btn--primary"
                        href="<?php echo esc_url( $pricing ); ?>"
                        target="_blank"
                        rel="noopener noreferrer">
                        <?php esc_html_e( 'See Pro plans', 'oxi-flip-box-plugin' ); ?>
                    </a>

                    <button type="button"
                        class="oxi-flip-upgrade-btn oxi-flip-upgrade-btn--ghost oxi-flip-upgrade-dismiss"
                        sup-data="maybe">
                        <?php esc_html_e( 'Maybe later', 'oxi-flip-box-plugin' ); ?>
                    </button>

                    <button type="button"
                        class="oxi-flip-upgrade-btn oxi-flip-upgrade-btn--quiet oxi-flip-upgrade-dismiss"
                        sup-data="never">
                        <?php esc_html_e( 'Do not show this again', 'oxi-flip-box-plugin' ); ?>
                    </button>
                </div>
            </div>

            <button type="button"
                class="oxi-flip-upgrade-notice__close oxi-flip-upgrade-dismiss"
                sup-data="maybe"
                aria-label="<?php esc_attr_e( 'Remind me later', 'oxi-flip-box-plugin' ); ?>">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
            </button>
        </div>
        <?php
    }

    /**
     * Assets. Shares admin-notice.js and notice.css with the review notice.
     *
     * @return void
     */
    public function admin_enqueue_scripts() {
        if ( ! $this->is_plugin_screen() ) {
            return;
        }

        wp_enqueue_style(
            'oxilab_flip-admin-notice-css',
            OXI_FLIP_BOX_URL . 'asset/backend/css/notice.css',
            false,
            filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/notice.css' )
        );
        wp_enqueue_script(
            'oxilab_flip-admin-notice',
            OXI_FLIP_BOX_URL . 'asset/backend/js/admin-notice.js',
            [ 'jquery' ],
            filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/admin-notice.js' ),
            true
        );
        wp_localize_script(
            'oxilab_flip-admin-notice',
            'oxilab_flip_notice_dissmiss',
            [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'oxilab_flip_notice_dissmiss' ),
            ]
        );
    }

    /**
     * Dismissal handler.
     *
     * 'maybe' pushes the 10 day clock forward, anything else silences the
     * notice for good.
     *
     * @return void
     */
    public function ajax_action() {
        $wpnonce = isset( $_POST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $wpnonce, 'oxilab_flip_notice_dissmiss' ) ) {
            wp_send_json_error( __( 'Invalid request', 'oxi-flip-box-plugin' ), 422 );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Insufficient permissions', 'oxi-flip-box-plugin' ), 403 );
        }

        $notice = isset( $_POST['notice'] ) ? sanitize_text_field( wp_unslash( $_POST['notice'] ) ) : '';

        if ( 'maybe' === $notice ) {
            update_option( 'oxilab_flip_box_upgrade_date', strtotime( 'now' ) );
        } else {
            update_option( 'oxilab_flip_box_upgrade_nobug', 'never' );
        }

        wp_send_json_success( 'Done' );
    }
}
