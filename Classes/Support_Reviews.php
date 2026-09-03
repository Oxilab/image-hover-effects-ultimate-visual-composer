<?php

namespace OXI_FLIP_BOX_PLUGINS\Classes;

/**
 * Description of Support_Reviews
 *
 * @author biplo
 */
class Support_Reviews {

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function dismiss_button_scripts() {
        wp_enqueue_script( 'oxilab_flip-admin-notice', OXI_FLIP_BOX_URL . 'asset/backend/js/admin-notice.js', [ 'jquery' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/admin-notice.js' ), true );
        wp_localize_script(
            'oxilab_flip-admin-notice', 'oxilab_flip_notice_dissmiss', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'oxilab_flip_notice_dissmiss' ),
            ]
        );
    }



    /**
     * Admin Notice Ajax  loader
     * @return void
     */
    public function notice_dissmiss() {
        if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'oxilab_flip_notice_dissmiss' ) ) :
            $notice = isset( $_POST['notice'] ) ? sanitize_text_field( $_POST['notice'] ) : '';
            if ( $notice == 'maybe' ) :
                $data = strtotime( 'now' );
                update_option( 'oxilab_flip_box_activation_date', $data );
            else :
                update_option( 'oxilab_flip_box_nobug', $notice );
            endif;
            echo 'Its Complete';
        else :
            return;
        endif;

        die();
    }

    /**
     * Review request notice.
     *
     * Shown once the plugin has been in use for more than 7 days
     * (gated by Admin_helper::admin_notice()).
     *
     * @return void
     */
    public function first_install() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $image  = OXI_FLIP_BOX_URL . 'image/logo.png';
        $review = 'https://wordpress.org/support/plugin/image-hover-effects-ultimate-visual-composer/reviews/?filter=5#new-post';
        ?>
        <div class="notice oxi-flip-review-notice oxilab-flipbox-review-notice">

            <div class="oxi-flip-review-notice__logo">
                <img src="<?php echo esc_url( $image ); ?>"
                    alt="<?php esc_attr_e( 'Flipbox', 'oxi-flip-box-plugin' ); ?>">
            </div>

            <div class="oxi-flip-review-notice__body">

                <div class="oxi-flip-review-notice__stars" aria-hidden="true">
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                </div>

                <h3 class="oxi-flip-review-notice__title">
                    <?php esc_html_e( 'Enjoying Flipbox?', 'oxi-flip-box-plugin' ); ?>
                </h3>

                <p class="oxi-flip-review-notice__text">
                    <?php esc_html_e( 'You have been building flip boxes with us for over a week now, and that is awesome! A quick 5-star review on WordPress.org takes less than a minute, and it genuinely helps us keep improving the plugin.', 'oxi-flip-box-plugin' ); ?>
                </p>

                <div class="oxi-flip-review-notice__actions">
                    <?php // Opens the review form only, the notice deliberately stays put. ?>
                    <a class="oxi-flip-review-btn oxi-flip-review-btn--primary"
                        href="<?php echo esc_url( $review ); ?>"
                        target="_blank"
                        rel="noopener noreferrer">
                        <span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
                        <?php esc_html_e( 'Sure, you deserve it!', 'oxi-flip-box-plugin' ); ?>
                    </a>

                    <button type="button"
                        class="oxi-flip-review-btn oxi-flip-review-btn--ghost oxi-flip-support-reviews"
                        sup-data="success">
                        <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                        <?php esc_html_e( 'I already did', 'oxi-flip-box-plugin' ); ?>
                    </button>

                    <button type="button"
                        class="oxi-flip-review-btn oxi-flip-review-btn--quiet oxi-flip-support-reviews"
                        sup-data="never">
                        <?php esc_html_e( 'Never show this again', 'oxi-flip-box-plugin' ); ?>
                    </button>
                </div>
            </div>

            <button type="button"
                class="oxi-flip-review-notice__close oxi-flip-support-reviews"
                sup-data="maybe"
                aria-label="<?php esc_attr_e( 'Remind me later', 'oxi-flip-box-plugin' ); ?>">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
            </button>
        </div>
        <?php
    }

    /**
     * Revoke this function when the object is created.
     */
    public function __construct() {
        add_action( 'admin_notices', [ $this, 'first_install' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'wp_ajax_oxilab_flip_notice_dissmiss', [ $this, 'notice_dissmiss' ] );
        add_action( 'admin_notices', [ $this, 'dismiss_button_scripts' ] );
    }

    /**
     * Admin Notice CSS file loader
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'oxilab_flip-admin-notice-css', OXI_FLIP_BOX_URL . 'asset/backend/css/notice.css', false, filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/css/notice.css' ) );
        $this->dismiss_button_scripts();
    }
}
