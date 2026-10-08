<?php

namespace OXI_FLIP_BOX_PLUGINS\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Removes Flipbox data from the database.
 *
 * Two entry points:
 * - delete_now(): the Danger zone button on the Settings page. The plugin
 *   stays active, so its rows are deleted, the default templates are seeded
 *   again by Flipbox's own installer, and the licence is kept.
 * - uninstall(): runs from Freemius' after_uninstall action when the plugin
 *   is deleted, only on sites where "Delete data when the plugin is deleted"
 *   is switched on.
 *
 * Flipbox stores its rows in {prefix}oxi_div_style, {prefix}oxi_div_list and
 * {prefix}oxi_div_import. Those tables carry a "type" column, so only
 * Flipbox's own rows are removed (style type "flip", their list rows, import
 * types "flip" and "oxi-addons-flip") and the tables themselves are never
 * dropped. oxi_addons_font_awesome and oxi_addons_google_font are kept while
 * another Oxilab plugin is installed, because that plugin reads them too.
 *
 * Freemius keeps its own data and handles it on uninstall.
 *
 * @since 3.1.0
 */
class Data_Cleaner {

    /**
     * Option that turns on cleanup when the plugin is deleted.
     */
    const UNINSTALL_OPTION = 'oxi_flipbox_delete_data_on_uninstall';

    /**
     * Import table types Flipbox writes and reads.
     */
    const IMPORT_TYPES = [ 'flip', 'oxi-addons-flip' ];

    /**
     * Settings page options.
     */
    const SETTINGS = [
        'oxi_addons_user_permission',
        'oxi_addons_pre_loader',
        'oxi_flipbox_support_massage',
    ];

    /**
     * Internal options (install state, notice dismissals, widget instances).
     */
    const OPTIONS = [
        'oxilab_flip_box_version',
        'oxilab_flip_box_activation_date',
        'oxilab_flip_box_nobug',
        'oxilab_flip_box_upgrade_date',
        'oxilab_flip_box_upgrade_nobug',
        'oxilab_flip_box_recommended',
        'widget_oxi_flip_box_widget',
    ];

    /**
     * Options another Oxilab plugin reads as well.
     */
    const SHARED_OPTIONS = [ 'oxi_addons_font_awesome', 'oxi_addons_google_font' ];

    /**
     * Legacy (pre Freemius) licence options.
     */
    const LICENSE_OPTIONS = [ 'oxilab_flip_box_license_status', 'oxilab_flip_box_license_key' ];

    const TRANSIENTS = [ 'oxi_flip_box_activation_redirect' ];

    /**
     * Count what delete_now() would remove, for the confirmation dialog.
     *
     * @return array{boxes:int,items:int}
     */
    public static function counts() {
        global $wpdb;
        $style = $wpdb->prefix . 'oxi_div_style';
        $list  = $wpdb->prefix . 'oxi_div_list';
        if ( ! self::table_exists( $style ) ) {
            return [ 'boxes' => 0, 'items' => 0 ];
        }
        // phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $boxes = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . esc_sql( $style ) . ' WHERE type = %s', 'flip' ) );
        $items = self::table_exists( $list ) ? (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . esc_sql( $list ) . ' WHERE styleid IN ( SELECT id FROM ' . esc_sql( $style ) . ' WHERE type = %s )', 'flip' ) ) : 0;
        // phpcs:enable
        return [ 'boxes' => $boxes, 'items' => $items ];
    }

    /**
     * Delete every flip box, its items and every setting, keep the plugin usable.
     *
     * Rows are deleted rather than the tables truncated, so new flip boxes
     * never reuse an old ID and suddenly appear inside an old post.
     */
    public static function delete_now() {
        self::delete_rows();

        foreach ( self::SETTINGS as $option ) {
            delete_option( $option );
        }
        self::delete_shared_options();
        foreach ( self::TRANSIENTS as $transient ) {
            delete_transient( $transient );
        }

        // Back to a fresh install: Flipbox's installer seeds the default templates again.
        \OXI_FLIP_BOX_PLUGINS\Includes\Installation::get_instance()->Flip_Datatase();
    }

    /**
     * Freemius after_uninstall callback.
     */
    public static function uninstall() {
        if ( is_multisite() ) {
            foreach ( get_sites( [ 'fields' => 'ids', 'number' => 0 ] ) as $site_id ) {
                switch_to_blog( $site_id );
                self::uninstall_site();
                restore_current_blog();
            }
            return;
        }
        self::uninstall_site();
    }

    /**
     * Remove everything on the current site, if the site opted in.
     */
    public static function uninstall_site() {
        if ( 'yes' !== get_option( self::UNINSTALL_OPTION ) ) {
            return;
        }
        self::delete_rows();

        foreach ( array_merge( self::SETTINGS, self::OPTIONS, self::LICENSE_OPTIONS ) as $option ) {
            delete_option( $option );
        }
        self::delete_shared_options();
        foreach ( self::TRANSIENTS as $transient ) {
            delete_transient( $transient );
        }
        delete_option( self::UNINSTALL_OPTION );
    }

    /**
     * Delete Flipbox's rows from its three tables.
     */
    public static function delete_rows() {
        global $wpdb;
        $style  = $wpdb->prefix . 'oxi_div_style';
        $list   = $wpdb->prefix . 'oxi_div_list';
        $import = $wpdb->prefix . 'oxi_div_import';

        // phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        if ( self::table_exists( $style ) ) {
            if ( self::table_exists( $list ) ) {
                $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . esc_sql( $list ) . ' WHERE styleid IN ( SELECT id FROM ' . esc_sql( $style ) . ' WHERE type = %s )', 'flip' ) );
                $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . esc_sql( $list ) . ' WHERE type = %s', 'flip' ) );
            }
            $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . esc_sql( $style ) . ' WHERE type = %s', 'flip' ) );
        }
        if ( self::table_exists( $import ) ) {
            $placeholders = implode( ', ', array_fill( 0, count( self::IMPORT_TYPES ), '%s' ) );
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
            $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . esc_sql( $import ) . " WHERE type IN ( $placeholders )", self::IMPORT_TYPES ) );
        }
        // phpcs:enable
    }

    /**
     * Whether a table exists.
     *
     * @param string $table Full table name.
     * @return bool
     */
    public static function table_exists( $table ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) === $table; // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    }

    /**
     * Delete options another Oxilab plugin also reads, unless one is installed.
     */
    public static function delete_shared_options() {
        if ( self::other_oxilab_plugin_installed() ) {
            return;
        }
        foreach ( self::SHARED_OPTIONS as $option ) {
            delete_option( $option );
        }
    }

    /**
     * Whether any other installed plugin is made by Oxilab.
     *
     * @return bool
     */
    public static function other_oxilab_plugin_installed() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $self = defined( 'OXI_FLIP_BOX_BASENAME' ) ? OXI_FLIP_BOX_BASENAME : 'image-hover-effects-ultimate-visual-composer/index.php';
        foreach ( get_plugins() as $file => $data ) {
            if ( $file === $self ) {
                continue;
            }
            if ( false !== stripos( $data['Author'] . ' ' . $data['AuthorURI'], 'oxilab' ) ) {
                return true;
            }
        }
        return false;
    }
}
