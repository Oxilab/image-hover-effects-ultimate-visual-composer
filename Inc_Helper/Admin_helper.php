<?php

namespace OXI_FLIP_BOX_PLUGINS\Inc_Helper;

trait Admin_helper {




    private function handle_direct_action_error( $message ) {
        _default_wp_die_handler( $message, 'Flipbox Error' );
    }

    public function verify_request_nonce() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $METHOD = $_POST;
        } else {
            $METHOD = $_GET;
        }
        return ! empty( $METHOD['_wpnonce'] ) && wp_verify_nonce( $METHOD['_wpnonce'], 'oxi-flip-box-editor' );
    }

    public function data_process() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $METHOD = $_POST;
        } else {
            $METHOD = $_GET;
        }
        if ( ! $this->verify_request_nonce() ) {
            $this->handle_direct_action_error( 'Access Denied' );
        }
        $functionname = isset( $METHOD['functionname'] ) ? sanitize_text_field( $METHOD['functionname'] ) : '';
        $rawdata = isset( $METHOD['rawdata'] ) ? sanitize_post( $METHOD['rawdata'] ) : '';
        $styleid = isset( $METHOD['styleid'] ) ? (int) $METHOD['styleid'] : '';
        $childid = isset( $METHOD['childid'] ) ? (int) $METHOD['childid'] : '';

        if ( ! empty( $functionname ) && ! empty( $rawdata ) ) :
            new \OXI_FLIP_BOX_PLUGINS\Classes\Admin_Ajax( $functionname, $rawdata, $styleid, $childid );
        endif;

        die();
    }

    public function welcome_remove_menus() {
        remove_submenu_page( 'index.php', 'oxi-flip-box-activation' );
    }

    public function User_Reviews() {
        $this->admin_recommended();
        // Only ever one of these two at a time, so the screen is never stacked
        // with plugin notices.
        if ( ! $this->admin_notice() ) :
            $this->admin_upgrade_notice();
        endif;
    }

	public function admin_notice() {
        if ( ! empty( $this->admin_notice_status() ) ) :
            return false;
        endif;
        if ( strtotime( '-7 day' ) < $this->installation_date() ) :
            return false;
        endif;
        new \OXI_FLIP_BOX_PLUGINS\Classes\Support_Reviews();
        return true;
    }

    /**
     * Pro upgrade notice gate.
     *
     * Free users only, after 10 days of use, until dismissed for good.
     *
     * @since 3.0.3
     */
    public function admin_upgrade_notice() {
        if ( $this->has_pro_access() ) :
            return false;
        endif;
        if ( ! empty( get_option( 'oxilab_flip_box_upgrade_nobug' ) ) ) :
            return false;
        endif;
        if ( strtotime( '-10 days' ) > $this->upgrade_notice_date() ) :
            new \OXI_FLIP_BOX_PLUGINS\Classes\Support_Upgrade();
            return true;
        endif;
        return false;
    }

    /**
     * Whether this install has a valid licence or premium code available.
     *
     * Wrapped defensively: a missing Freemius bootstrap must never be able to
     * fatal the admin.
     *
     * @since 3.0.3
     */
    public function has_pro_access() {
        $has_pro = false;
        try {
            $license = get_option( $this->fixed_data( '6f78696c61625f666c69705f626f785f6c6963656e73655f737461747573' ) );
            if ( $license === $this->fixed_data( '76616c6964' ) ) :
                $has_pro = true;
            elseif ( function_exists( 'wpkin_fb_v' ) && wpkin_fb_v()->can_use_premium_code() ) :
                $has_pro = true;
            endif;
        } catch ( \Throwable $e ) {
            // Treat an unavailable licence layer as "unknown", and stay quiet
            // rather than risk showing an upgrade prompt to a paying customer.
            $has_pro = true;
        }

        /**
         * Filters whether this install counts as having Pro access.
         *
         * Only governs whether the upgrade notice is shown, it unlocks nothing.
         * Useful for QA, where a licenced dev site needs to preview the free
         * user experience.
         *
         * @since 3.0.3
         *
         * @param bool $has_pro
         */
        return (bool) apply_filters( 'oxilab_flip_box_has_pro_access', $has_pro );
    }

    /**
     * Clock for the upgrade notice.
     *
     * Seeded from the real installation date so existing sites are credited for
     * the time they have already used the plugin, then kept separate from it, so
     * snoozing the review notice cannot move this one.
     *
     * @since 3.0.3
     */
    public function upgrade_notice_date() {
        $data = get_option( 'oxilab_flip_box_upgrade_date' );
        if ( empty( $data ) ) :
            $data = $this->installation_date();
            update_option( 'oxilab_flip_box_upgrade_date', $data );
        endif;
        return $data;
    }

    /**
     * Admin Install date Check
     *
     * @since 2.0.0
     */
    public function installation_date() {
        $data = get_option( 'oxilab_flip_box_activation_date' );
        if ( empty( $data ) ) :
            $data = strtotime( 'now' );
            update_option( 'oxilab_flip_box_activation_date', $data );
        endif;
        return $data;
    }

	/**
     * Admin Notice Check
     *
     * @since 2.0.0
     */
    public function admin_notice_status() {
        $data = get_option( 'oxilab_flip_box_nobug' );
        return $data;
    }


	public function admin_recommended() {
        // Recommended plugin feature removed — oxilab.org API no longer in use.
        return;
    }

	/**
     * Admin Notice Check
     *
     * @since 2.0.0
     */
    public function admin_recommended_status() {
        $data = get_option( 'oxilab_flip_box_recommended' );
        return $data;
    }

    /**
     * Plugin Admin Top Menu
     *
     * @since 2.0.0
     */
    public function oxilab_admin_menu( $agr ) {
        $response = [
            'Flip Box' => [
                'name' => 'Flip Box',
                'homepage' => 'oxi-flip-box-ultimate',
            ],
            'Create New' => [
                'name' => 'Create New',
                'homepage' => 'oxi-flip-box-ultimate-new',
            ],
            'Import Templates' => [
                'name' => 'Import Templates',
                'homepage' => 'oxi-flip-box-ultimate-import',
            ],
        ];

        $bgimage = OXI_FLIP_BOX_URL . 'image/flipbox-logo.svg';
        $sub = '';
		?>
        <div class="oxi-addons-wrapper">
            <div class="oxilab-new-admin-menu">
                <div class="oxi-site-logo">
                    <a href="<?php echo esc_url( $this->admin_url_convert( 'oxi-flip-box-ultimate' ) ); ?>" class="header-logo" style=" background-image: url(<?php echo esc_url( $bgimage ); ?>);">
                    </a>
                </div>
                <nav class="oxilab-sa-admin-nav">
                    <ul class="oxilab-sa-admin-menu">
                        <?php
                        $GETPage = sanitize_text_field( $_GET['page'] );

                        foreach ( $response as $key => $value ) {
							?>
                            <li 
                            <?php
							if ( $GETPage == $value['homepage'] ) :
								echo ' class="active" ';
                                endif;
							?>
                                ><a href="<?php echo esc_url( $this->admin_url_convert( $value['homepage'] ) ); ?>"><?php echo esc_html( $this->name_converter( $value['name'] ) ); ?></a></li>
							<?php
                        }
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in Docs.
                        echo \OXI_FLIP_BOX_PLUGINS\Classes\Docs::nav_item();
                        ?>

                    </ul>
                    <ul class="oxilab-sa-admin-menu2">
                        <?php
                        if ( apply_filters( 'oxi-flip-box-plugin/pro_version', false ) == false ) :
							?>
                            <li class="fazil-class"><a target="_blank" href="https://oxilab.dev/flipbox/pricing">Upgrade</a></li>
							<?php
                        endif;
                        ?>

                        <li class="saadmin-doc"><a target="_blank" rel="noopener noreferrer" href="https://demos.oxilab.dev/flipbox/template/">Demos</a></li>
                        <li class="saadmin-doc"><a target="_black" href="https://oxilab.dev/docs/flipbox">Docs</a></li>
                        <li class="saadmin-doc"><a target="_black" href="https://wordpress.org/support/plugin/image-hover-effects-ultimate-visual-composer/">Support</a></li>
                        <li class="saadmin-set"><a href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-settings' ) ); ?>"><span class="dashicons dashicons-admin-generic"></span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
		<?php
    }
    /**
     * Plugin fixed
     *
     * @since 3.1.0
     */
    public function fixed_data( $agr ) {
        return hex2bin( $agr );
    }

    /**
     * Plugin fixed debugging data
     *
     * @since 3.1.0
     */
    public function fixed_debug_data( $str ) {
        return bin2hex( $str );
    }

    public function admin_url_convert( $agr ) {
        return admin_url( strpos( $agr, 'edit' ) !== false ? $agr : 'admin.php?page=' . $agr );
    }

	 /**
     * Plugin check Current Tabs
     *
     * @since 2.0.0
     */
    public function check_current_tabs( $agr ) {
        $vs = get_option( $this->fixed_data( '6f78696c61625f666c69705f626f785f6c6963656e73655f737461747573' ) );
        if ( $vs == $this->fixed_data( '76616c6964' ) || wpkin_fb_v()->can_use_premium_code() ) {
            return true;
        } else {
            return false;
        }
    }
}
