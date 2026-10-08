<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages;

/**
 * Description of Settings
 *
 * @author biplo
 */
class Settings {


    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\CSS_JS_Loader;

    public $roles;
    public $saved_role;
    public $oxi_fixed_header;
    public $fontawesome;
    public $getfontawesome = [];

    /**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 2.0.0
     */
    public function __construct() {
        $this->admin();
        $this->Render();
    }

    public function admin() {
        global $wp_roles;
        $this->roles = $wp_roles->get_names();
        $this->saved_role = get_option( 'oxi_addons_user_permission' );
        $this->admin_ajax_load();
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function admin_ajax_load() {
        $this->admin_css_loader();
        wp_enqueue_script( 'oxi-flip-settings', OXI_FLIP_BOX_URL . 'asset/backend/js/settings.js', [ 'jquery' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/settings.js' ), true );
        wp_localize_script(
            'oxi-flip-settings', 'oxi_flip_box_settings', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'oxi-flip-box-editor' ),
            ]
        );
    }

    /**
     * Render an on/off switch row.
     *
     * By default an option is "on" unless it holds its off value, which is
     * how the frontend reads it, so an option that was never saved shows as on.
     *
     * @since 3.1.0
     *
     * @param string $name        Option name, also the AJAX function name.
     * @param string $label       Row label.
     * @param string $description Row help text.
     * @param string $on          Value stored when switched on.
     * @param string $off         Value stored when switched off.
     * @param bool   $default_on  Whether an option that was never saved shows as on.
     */
    public function switch_row( $name, $label, $description, $on = '', $off = 'no', $default_on = true ) {
        $checked = $default_on ? get_option( $name ) !== $off : get_option( $name ) === $on;
        ?>
        <div class="oxi-flip-set-row">
            <div class="oxi-flip-set-row-text">
                <label class="oxi-flip-set-label" for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
                <p class="oxi-flip-set-desc" id="<?php echo esc_attr( $name ); ?>-desc"><?php echo esc_html( $description ); ?></p>
            </div>
            <div class="oxi-flip-set-row-control">
                <span class="oxi-flip-set-status" data-status-for="<?php echo esc_attr( $name ); ?>" aria-live="polite"></span>
                <span class="oxi-flip-set-switch">
                    <input type="checkbox" role="switch" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" data-on="<?php echo esc_attr( $on ); ?>" data-off="<?php echo esc_attr( $off ); ?>" aria-describedby="<?php echo esc_attr( $name ); ?>-desc" <?php checked( $checked ); ?>>
                    <span class="oxi-flip-set-switch-track" aria-hidden="true"><span class="oxi-flip-set-switch-thumb"></span></span>
                </span>
            </div>
        </div>
        <?php
    }

    /**
     * Render a card header.
     *
     * @since 3.1.0
     *
     * @param string $icon        Dashicons class suffix.
     * @param string $title       Card title.
     * @param string $description Card subtitle.
     */
    public function card_head( $icon, $title, $description ) {
        ?>
        <div class="oxi-flip-set-card-head">
            <span class="oxi-flip-set-card-icon dashicons dashicons-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
            <div>
                <h2 class="oxi-flip-set-card-title"><?php echo esc_html( $title ); ?></h2>
                <p class="oxi-flip-set-card-sub"><?php echo esc_html( $description ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Read the licence state from Freemius and the legacy licence option.
     *
     * Wrapped defensively: a Freemius error must never break the settings page.
     *
     * @since 3.1.0
     *
     * @return array
     */
    public function license_state() {
        $state = [
            'status'      => 'free',
            'plan'        => '',
            'expires'     => '',
            'key'         => '',
            'can_manage'  => false,
            'affix'       => '',
            'account_url' => '',
        ];
        try {
            if ( function_exists( 'wpkin_fb_v' ) ) {
                $fs      = wpkin_fb_v();
                $license = $fs->_get_license();

                $state['can_manage'] = $fs->is_user_admin();
                $state['affix']      = $fs->get_unique_affix();
                $state['plan']       = $fs->get_plan_title();
                if ( $fs->is_registered() ) {
                    $state['account_url'] = $fs->get_account_url();
                }

                if ( is_object( $license ) ) {
                    $state['key']     = $license->get_html_escaped_masked_secret_key();
                    $state['expires'] = $license->is_lifetime() ? '' : date_i18n( get_option( 'date_format' ), strtotime( $license->expiration ) );
                }

                if ( $fs->is_trial() ) {
                    $state['status'] = 'trial';
                } elseif ( $fs->can_use_premium_code() ) {
                    $state['status'] = 'active';
                } elseif ( is_object( $license ) && $license->is_expired() ) {
                    $state['status'] = 'expired';
                }
            }
        } catch ( \Throwable $e ) {
            $state['can_manage'] = false;
        }

        if ( 'free' === $state['status'] && 'valid' === get_option( 'oxilab_flip_box_license_status' ) ) {
            $state['status'] = 'legacy';
        }
        return $state;
    }

    /**
     * Render the licence card.
     *
     * The Activate/Change buttons open Freemius' own licence dialog, the same
     * one behind "Activate License" on the Plugins screen. Freemius only
     * prints that dialog on the Plugins screen, so it is added to this page's
     * footer here; its AJAX handler is registered on every admin request.
     *
     * @since 3.1.0
     */
    public function license_card() {
        $state   = $this->license_state();
        $status  = $state['status'];
        $pricing = 'https://oxilab.dev/flipbox/pricing';

        $show_dialog = $state['can_manage'] && '' !== $state['affix'] && 'legacy' !== $status;
        if ( $show_dialog ) {
            add_action( 'admin_footer', [ wpkin_fb_v(), '_add_license_activation_dialog_box' ] );
        }
        $trigger = 'activate-license-trigger ' . $state['affix'];

        $pills = [
            'free'    => __( 'Free plan', 'oxi-flip-box-plugin' ),
            'trial'   => __( 'Trial', 'oxi-flip-box-plugin' ),
            'active'  => __( 'Active', 'oxi-flip-box-plugin' ),
            'expired' => __( 'Expired', 'oxi-flip-box-plugin' ),
            'legacy'  => __( 'Active', 'oxi-flip-box-plugin' ),
        ];
        ?>
        <section class="oxi-flip-set-card oxi-flip-set-license is-<?php echo esc_attr( $status ); ?>" id="license">
            <div class="oxi-flip-set-card-head">
                <span class="oxi-flip-set-card-icon dashicons dashicons-admin-network" aria-hidden="true"></span>
                <div>
                    <h2 class="oxi-flip-set-card-title"><?php esc_html_e( 'License', 'oxi-flip-box-plugin' ); ?></h2>
                    <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'Your Flipbox Pro license for this site.', 'oxi-flip-box-plugin' ); ?></p>
                </div>
                <span class="oxi-flip-set-pill is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $pills[ $status ] ); ?></span>
            </div>

            <div class="oxi-flip-set-license-body">
                <?php if ( in_array( $status, [ 'active', 'trial', 'expired' ], true ) && ( '' !== $state['plan'] || '' !== $state['key'] ) ) : ?>
                    <dl class="oxi-flip-set-license-facts">
                        <?php if ( '' !== $state['plan'] && 'expired' !== $status ) : ?>
                            <div>
                                <dt><?php esc_html_e( 'Plan', 'oxi-flip-box-plugin' ); ?></dt>
                                <dd><?php echo esc_html( $state['plan'] ); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( '' !== $state['key'] ) : ?>
                            <div>
                                <dt><?php echo esc_html( 'expired' === $status ? __( 'Expired on', 'oxi-flip-box-plugin' ) : __( 'Expires', 'oxi-flip-box-plugin' ) ); ?></dt>
                                <dd><?php echo esc_html( '' !== $state['expires'] ? $state['expires'] : __( 'Never (lifetime)', 'oxi-flip-box-plugin' ) ); ?></dd>
                            </div>
                            <div>
                                <dt><?php esc_html_e( 'License key', 'oxi-flip-box-plugin' ); ?></dt>
                                <dd class="oxi-flip-set-license-key"><?php echo wp_kses( $state['key'], [] ); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                <?php endif; ?>

                <p class="oxi-flip-set-license-text">
                    <?php
                    if ( 'active' === $status ) :
                        esc_html_e( 'Pro is active on this site. Thank you for supporting Flipbox.', 'oxi-flip-box-plugin' );
                    elseif ( 'trial' === $status ) :
                        esc_html_e( 'Your Pro trial is running. Activate a license key to keep Pro when the trial ends.', 'oxi-flip-box-plugin' );
                    elseif ( 'expired' === $status ) :
                        esc_html_e( 'Your license has expired. Renew it to get Pro back on this site.', 'oxi-flip-box-plugin' );
                    elseif ( 'legacy' === $status ) :
                        esc_html_e( 'Pro is active on this site with a license key from an earlier version.', 'oxi-flip-box-plugin' );
                    elseif ( ! $state['can_manage'] ) :
                        esc_html_e( 'Pro is not active on this site yet.', 'oxi-flip-box-plugin' );
                    else :
                        esc_html_e( 'Bought Pro? Paste the license key from your purchase email to activate it here.', 'oxi-flip-box-plugin' );
                    endif;
                    ?>
                </p>

                <?php ob_start(); ?>
                    <?php if ( $show_dialog ) : ?>
                        <?php if ( 'active' === $status ) : ?>
                            <a href="#" class="oxi-flip-set-btn is-secondary <?php echo esc_attr( $trigger ); ?>"><?php esc_html_e( 'Change license', 'oxi-flip-box-plugin' ); ?></a>
                        <?php else : ?>
                            <a href="#" class="oxi-flip-set-btn is-primary <?php echo esc_attr( $trigger ); ?>">
                                <span class="dashicons dashicons-admin-network" aria-hidden="true"></span><?php esc_html_e( 'Activate license', 'oxi-flip-box-plugin' ); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( 'expired' === $status ) : ?>
                        <a class="oxi-flip-set-btn is-secondary" target="_blank" rel="noopener" href="<?php echo esc_url( $pricing ); ?>"><?php esc_html_e( 'Renew license', 'oxi-flip-box-plugin' ); ?></a>
                    <?php elseif ( $state['can_manage'] && ( 'free' === $status || 'trial' === $status ) ) : ?>
                        <a class="oxi-flip-set-btn is-secondary" target="_blank" rel="noopener" href="<?php echo esc_url( $pricing ); ?>"><?php esc_html_e( 'Get Pro', 'oxi-flip-box-plugin' ); ?></a>
                    <?php endif; ?>

                    <?php if ( $state['can_manage'] && '' !== $state['account_url'] && 'free' !== $status ) : ?>
                        <a class="oxi-flip-set-link" href="<?php echo esc_url( $state['account_url'] ); ?>"><?php esc_html_e( 'Manage account', 'oxi-flip-box-plugin' ); ?></a>
                    <?php endif; ?>

                    <?php if ( ! $state['can_manage'] && 'legacy' !== $status && 'active' !== $status ) : ?>
                        <span class="oxi-flip-set-license-note"><?php esc_html_e( 'Ask a site administrator to activate the license.', 'oxi-flip-box-plugin' ); ?></span>
                    <?php endif; ?>
                <?php
                $actions = trim( ob_get_clean() );
                if ( '' !== $actions ) :
                    ?>
                    <div class="oxi-flip-set-actions"><?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built above, every value escaped there. ?></div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }

    /**
     * Render the Danger zone card and its confirmation dialog.
     *
     * Administrators only: these actions remove data for the whole site.
     *
     * @since 3.1.0
     */
    public function danger_zone() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $counts = \OXI_FLIP_BOX_PLUGINS\Classes\Data_Cleaner::counts();
        /* translators: %s: number of flip boxes */
        $boxes = sprintf( _n( '%s flip box', '%s flip boxes', $counts['boxes'], 'oxi-flip-box-plugin' ), number_format_i18n( $counts['boxes'] ) );
        /* translators: %s: number of flip box items */
        $items = sprintf( _n( '%s item', '%s items', $counts['items'], 'oxi-flip-box-plugin' ), number_format_i18n( $counts['items'] ) );
        ?>
        <section class="oxi-flip-set-card oxi-flip-set-danger" aria-labelledby="oxi-flip-danger-title">
            <div class="oxi-flip-set-card-head">
                <span class="oxi-flip-set-card-icon dashicons dashicons-warning" aria-hidden="true"></span>
                <div>
                    <h2 class="oxi-flip-set-card-title" id="oxi-flip-danger-title"><?php esc_html_e( 'Danger zone', 'oxi-flip-box-plugin' ); ?></h2>
                    <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'These actions permanently remove your Flipbox data.', 'oxi-flip-box-plugin' ); ?></p>
                </div>
            </div>
            <?php
            $this->switch_row(
                \OXI_FLIP_BOX_PLUGINS\Classes\Data_Cleaner::UNINSTALL_OPTION,
                __( 'Delete data when the plugin is deleted', 'oxi-flip-box-plugin' ),
                __( 'When you delete Flipbox from the Plugins screen, also remove its flip boxes, items and settings. Deactivating never removes data, so you can safely deactivate while troubleshooting.', 'oxi-flip-box-plugin' ),
                'yes',
                'no',
                false
            );
            ?>
            <div class="oxi-flip-set-row">
                <div class="oxi-flip-set-row-text">
                    <span class="oxi-flip-set-label"><?php esc_html_e( 'Delete all data now', 'oxi-flip-box-plugin' ); ?></span>
                    <p class="oxi-flip-set-desc">
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: 1: number of flip boxes, 2: number of items */
                                __( 'Remove %1$s, %2$s and every setting on this page. Your license stays active.', 'oxi-flip-box-plugin' ),
                                $boxes,
                                $items
                            )
                        );
                        ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate' ) ); ?>"><?php esc_html_e( 'Export what you want to keep first.', 'oxi-flip-box-plugin' ); ?></a>
                    </p>
                </div>
                <div class="oxi-flip-set-row-control">
                    <button type="button" class="oxi-flip-set-btn is-danger" data-oxi-flip-open="oxi-flip-delete-dialog">
                        <span class="dashicons dashicons-trash" aria-hidden="true"></span><?php esc_html_e( 'Delete all data', 'oxi-flip-box-plugin' ); ?>
                    </button>
                </div>
            </div>
        </section>

        <div class="oxi-flip-set-dialog" id="oxi-flip-delete-dialog" hidden>
            <div class="oxi-flip-set-dialog-backdrop" data-oxi-flip-close></div>
            <div class="oxi-flip-set-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="oxi-flip-delete-title" aria-describedby="oxi-flip-delete-desc">
                <span class="oxi-flip-set-dialog-icon dashicons dashicons-warning" aria-hidden="true"></span>
                <h2 class="oxi-flip-set-dialog-title" id="oxi-flip-delete-title"><?php esc_html_e( 'Delete all Flipbox data?', 'oxi-flip-box-plugin' ); ?></h2>
                <div id="oxi-flip-delete-desc">
                    <p class="oxi-flip-set-dialog-text"><?php esc_html_e( 'This permanently deletes:', 'oxi-flip-box-plugin' ); ?></p>
                    <ul class="oxi-flip-set-dialog-list">
                        <li><?php echo esc_html( $boxes ); ?></li>
                        <li><?php echo esc_html( $items ); ?></li>
                        <li><?php esc_html_e( 'Every setting on this page', 'oxi-flip-box-plugin' ); ?></li>
                    </ul>
                    <p class="oxi-flip-set-dialog-text"><?php esc_html_e( 'Pages that use a Flipbox shortcode will show nothing in its place. This cannot be undone.', 'oxi-flip-box-plugin' ); ?></p>
                </div>
                <label class="oxi-flip-set-dialog-label" for="oxi-flip-delete-confirm">
                    <?php
                    printf(
                        /* translators: %s: the word the user must type */
                        esc_html__( 'Type %s to confirm', 'oxi-flip-box-plugin' ),
                        '<code>DELETE</code>'
                    );
                    ?>
                </label>
                <input type="text" class="oxi-flip-set-input" id="oxi-flip-delete-confirm" autocomplete="off" spellcheck="false" autocapitalize="characters">
                <p class="oxi-flip-set-dialog-status" role="status" aria-live="polite"
                    data-deleting="<?php esc_attr_e( 'Deleting all data', 'oxi-flip-box-plugin' ); ?>"
                    data-done="<?php esc_attr_e( 'All data deleted. Reloading the page.', 'oxi-flip-box-plugin' ); ?>"
                    data-error="<?php esc_attr_e( 'Nothing was deleted. Reload the page and try again.', 'oxi-flip-box-plugin' ); ?>"></p>
                <div class="oxi-flip-set-dialog-actions">
                    <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-close><?php esc_html_e( 'Cancel', 'oxi-flip-box-plugin' ); ?></button>
                    <button type="button" class="oxi-flip-set-btn is-danger-solid" id="oxi-flip-delete-submit" disabled><?php esc_html_e( 'Delete everything', 'oxi-flip-box-plugin' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    public function Render() {
        $is_premium = apply_filters( 'oxi-flip-box-plugin/pro_version', false ) != false;
		?>
        <div class="wrap">
            <?php
            apply_filters( 'oxi-flip-box-plugin/admin_menu', true );
            ?>
            <hr class="wp-header-end">

            <div class="oxi-flip-settings"
                data-saving="<?php esc_attr_e( 'Saving', 'oxi-flip-box-plugin' ); ?>"
                data-saved="<?php esc_attr_e( 'Saved', 'oxi-flip-box-plugin' ); ?>"
                data-error="<?php esc_attr_e( 'Not saved, try again', 'oxi-flip-box-plugin' ); ?>">

                <header class="oxi-flip-set-hero">
                    <img class="oxi-flip-set-hero-logo" src="<?php echo esc_url( OXI_FLIP_BOX_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
                    <div class="oxi-flip-set-hero-text">
                        <h1 class="oxi-flip-set-title"><?php esc_html_e( 'Settings', 'oxi-flip-box-plugin' ); ?></h1>
                        <p class="oxi-flip-set-subtitle"><?php esc_html_e( 'Control who manages Flipbox and which assets load with your flip boxes.', 'oxi-flip-box-plugin' ); ?></p>
                    </div>
                    <span class="oxi-flip-set-autosave">
                        <span class="oxi-flip-set-autosave-dot" aria-hidden="true"></span>
                        <?php esc_html_e( 'Changes save automatically', 'oxi-flip-box-plugin' ); ?>
                    </span>
                </header>

                <div class="oxi-flip-set-layout">
                    <div class="oxi-flip-set-main">

                        <?php $this->license_card(); ?>

                        <section class="oxi-flip-set-card">
                            <?php $this->card_head( 'groups', __( 'Access', 'oxi-flip-box-plugin' ), __( 'Decide who can create and edit flip boxes.', 'oxi-flip-box-plugin' ) ); ?>
                            <div class="oxi-flip-set-row">
                                <div class="oxi-flip-set-row-text">
                                    <label class="oxi-flip-set-label" for="oxi_addons_user_permission"><?php esc_html_e( 'Who can edit', 'oxi-flip-box-plugin' ); ?></label>
                                    <p class="oxi-flip-set-desc">
                                        <?php esc_html_e( 'Select the role who can manage this plugin.', 'oxi-flip-box-plugin' ); ?>
                                        <a target="_blank" rel="noopener" href="https://wordpress.org/documentation/article/roles-and-capabilities/"><?php esc_html_e( 'About roles', 'oxi-flip-box-plugin' ); ?></a>
                                    </p>
                                </div>
                                <div class="oxi-flip-set-row-control">
                                    <span class="oxi-flip-set-status" data-status-for="oxi_addons_user_permission" aria-live="polite"></span>
                                    <select class="oxi-flip-set-select" name="oxi_addons_user_permission" id="oxi_addons_user_permission">
                                        <?php foreach ( $this->roles as $key => $role ) : ?>
                                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->saved_role, $key ); ?>><?php echo esc_html( translate_user_role( $role ) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section class="oxi-flip-set-card">
                            <?php $this->card_head( 'performance', __( 'Assets and performance', 'oxi-flip-box-plugin' ), __( 'Turn off anything your theme already loads to keep pages lighter.', 'oxi-flip-box-plugin' ) ); ?>
                            <?php
                            $this->switch_row(
                                'oxi_addons_font_awesome',
                                __( 'Font Awesome', 'oxi-flip-box-plugin' ),
                                __( 'Load Font Awesome CSS when a flip box shows icons. If your theme already loads it, turn this off for faster loading.', 'oxi-flip-box-plugin' )
                            );
                            $this->switch_row(
                                'oxi_addons_google_font',
                                __( 'Google Fonts', 'oxi-flip-box-plugin' ),
                                __( 'Load the fonts used in your flip boxes from Google. If you already load those fonts locally, turn this off for faster loading.', 'oxi-flip-box-plugin' )
                            );
                            ?>
                        </section>

                        <section class="oxi-flip-set-card">
                            <?php $this->card_head( 'admin-tools', __( 'Advanced', 'oxi-flip-box-plugin' ), __( 'Fine tune the admin area.', 'oxi-flip-box-plugin' ) ); ?>
                            <?php
                            $this->switch_row(
                                'oxi_flipbox_support_massage',
                                __( 'Support message', 'oxi-flip-box-plugin' ),
                                __( 'Display the support message in the Flipbox admin area.', 'oxi-flip-box-plugin' )
                            );
                            ?>
                        </section>

                        <?php $this->danger_zone(); ?>
                    </div>

                    <aside class="oxi-flip-set-aside">
                        <div class="oxi-flip-set-card oxi-flip-set-help">
                            <h2 class="oxi-flip-set-card-title"><?php esc_html_e( 'Need a hand?', 'oxi-flip-box-plugin' ); ?></h2>
                            <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'Guides, live examples and answers from our team.', 'oxi-flip-box-plugin' ); ?></p>
                            <ul class="oxi-flip-set-links">
                                <li>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=flipbox-getting-started' ) ); ?>">
                                        <span class="dashicons dashicons-flag" aria-hidden="true"></span><?php esc_html_e( 'Getting started', 'oxi-flip-box-plugin' ); ?>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://oxilab.dev/docs/flipbox/">
                                        <span class="dashicons dashicons-book-alt" aria-hidden="true"></span><?php esc_html_e( 'Documentation', 'oxi-flip-box-plugin' ); ?><span class="oxi-flip-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://demos.oxilab.dev/flipbox/template/">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php esc_html_e( 'Live demos', 'oxi-flip-box-plugin' ); ?><span class="oxi-flip-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/image-hover-effects-ultimate-visual-composer/">
                                        <span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e( 'Support forum', 'oxi-flip-box-plugin' ); ?><span class="oxi-flip-set-ext dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </li>
                            </ul>
                            <div class="oxi-flip-set-meta">
                                <span><?php echo esc_html( sprintf( /* translators: %s: plugin version */ __( 'Version %s', 'oxi-flip-box-plugin' ), OXI_FLIP_BOX_PLUGIN_VERSION ) ); ?></span>
                                <?php if ( $is_premium ) : ?>
                                    <span class="oxi-flip-set-plan is-pro"><?php esc_html_e( 'Premium version', 'oxi-flip-box-plugin' ); ?></span>
                                <?php else : ?>
                                    <span class="oxi-flip-set-plan"><?php esc_html_e( 'Free', 'oxi-flip-box-plugin' ); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
		<?php
    }
}
