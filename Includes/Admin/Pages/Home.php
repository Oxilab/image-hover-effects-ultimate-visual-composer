<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages;

/**
 * Description of Home
 *
 * @author biplo
 */
class Home {


    /**
     * Database Parent Table
     *
     * @since 3.1.0
     */
    public $parent_table;

    /**
     * Database Import Table
     *
     * @since 3.1.0
     */
    public $child_table;

    /**
     * Database Import Table
     *
     * @since 3.1.0
     */
    public $import_table;

    /**
     * Define $wpdb
     *
     * @since 3.1.0
     */
    public $wpdb;

    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\Public_Helper;
    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\CSS_JS_Loader;





    public function manual_import_json() {
        if ( ! empty( $_REQUEST['_wpnonce'] ) ) {
            $nonce = $_REQUEST['_wpnonce'];
        }

        if ( ! empty( $_POST['importdatasubmit'] ) && sanitize_text_field( $_POST['importdatasubmit'] ) == 'Save' ) {
            if ( ! wp_verify_nonce( $nonce, 'oxilab-flipbox-import' ) ) {
                die( 'You do not have sufficient permissions to access this page.' );
            } elseif ( isset( $_FILES['importoxilabflipboxfile'] ) ) {
				if ( ! current_user_can( 'upload_files' ) ) :
					wp_die( esc_html( 'You do not have permission to upload files.' ) );
                    endif;

                    $allowedMimes = [
                        'json' => 'text/plain',
                    ];

                    $fileInfo = wp_check_filetype( basename( $_FILES['importoxilabflipboxfile']['name'] ), $allowedMimes );
                    if ( empty( $fileInfo['ext'] ) ) {
                        wp_die( esc_html( 'You do not have permission to upload files.' ) );
                    }

                    $content = json_decode( file_get_contents( $_FILES['importoxilabflipboxfile']['tmp_name'] ), true );

                    if ( empty( $content ) ) {
                        return new \WP_Error( 'file_error', 'Invalid File' );
                    }
                    $style = $content['style'];

                    if ( ! is_array( $style ) || $style['type'] != 'flip' ) {
                        return new \WP_Error( 'file_error', 'Invalid Content In File' );
                    }

                    $FlipboxApi = new \OXI_FLIP_BOX_PLUGINS\Classes\Admin_Ajax();
                    $new_slug = $FlipboxApi->post_json_import( $content );

                    echo '<script type="text/javascript"> document.location.href = "' . $new_slug . '"; </script>';
                    exit;
            }
        }
    }

    /**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 2.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->parent_table = $this->wpdb->prefix . 'oxi_div_style';
        $this->child_table = $this->wpdb->prefix . 'oxi_div_list';
        $this->import_table = $this->wpdb->prefix . 'oxi_div_import';
        $this->CSSJS_load();
        $this->Render();
    }

    public function database_data() {
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM  $this->parent_table WHERE type = %s ", 'flip' ), ARRAY_A );
    }

    public function CSSJS_load() {
        $this->manual_import_json();
        $this->admin_css_loader();
        $this->admin_home();
        $this->admin_ajax_load();
        apply_filters( 'oxi-flip-box-plugin/admin_menu', true );
    }
    private function create_export_link( $rawdata = '', $shortcode_id = '', $child_id = '' ) {
        return add_query_arg(
            [
                'action' => 'oxi_flip_box_data',
                'functionname' => 'get_shortcode_export',
                'styleid' => $shortcode_id,
                'childid' => $child_id,
                'rawdata' => $rawdata,
                '_wpnonce' => wp_create_nonce( 'oxi-flip-box-editor' ),
            ],
            admin_url( 'admin-ajax.php' )
        );
    }

    /**
     * Number of items per flip box.
     *
     * @since 3.1.0
     *
     * @return array<int,int>
     */
    public function item_counts() {
        $rows   = $this->wpdb->get_results( "SELECT styleid, COUNT(*) AS total FROM {$this->child_table} GROUP BY styleid", ARRAY_A );
        $counts = [];
        foreach ( (array) $rows as $row ) {
            $counts[ (int) $row['styleid'] ] = (int) $row['total'];
        }
        return $counts;
    }

    /**
     * Readable template name, e.g. "style12" becomes "Style 12".
     *
     * @since 3.1.0
     *
     * @param string $style_name Stored style name.
     * @return string
     */
    public function template_label( $style_name ) {
        if ( preg_match( '/^style\s*(\d+)$/i', $style_name, $m ) ) {
            return sprintf( /* translators: %s: template number */ __( 'Style %s', 'oxi-flip-box-plugin' ), $m[1] );
        }
        return $this->name_converter( $style_name );
    }

    public function created_shortcode( $rows = [] ) {
        $counts = $this->item_counts();
        ?>
        <section class="oxi-flip-set-card oxi-flip-sc-card">
            <table class="oxi_addons_table_data oxi-flip-sc-table">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e( 'Name', 'oxi-flip-box-plugin' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Shortcode', 'oxi-flip-box-plugin' ); ?></th>
                        <th scope="col" class="oxi-flip-sc-actions-col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'oxi-flip-box-plugin' ); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $rows as $value ) {
                        $id        = (int) $value['id'];
                        $name      = $this->name_converter( $value['name'] );
                        $template  = $this->template_label( $value['style_name'] );
                        $shortcode = '[oxilab_flip_box id="' . $id . '"]';
                        $php       = "<?php echo do_shortcode('" . $shortcode . "'); ?>";
                        $items     = isset( $counts[ $id ] ) ? $counts[ $id ] : 0;
                        $edit_url  = admin_url( "admin.php?page=oxi-flip-box-ultimate-new&styleid=$id" );
                        ?>
                        <tr data-id="<?php echo esc_attr( $id ); ?>" data-name="<?php echo esc_attr( $name ); ?>">
                            <td class="oxi-flip-sc-name" data-order="<?php echo esc_attr( $id ); ?>">
                                <a class="oxi-flip-sc-title" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $name ); ?></a>
                                <span class="oxi-flip-sc-meta">
                                    <span class="oxi-flip-sc-id">#<?php echo esc_html( $id ); ?></span>
                                    <span class="oxi-flip-sc-template"><?php echo esc_html( $template ); ?></span>
                                    <?php if ( $items > 0 ) : ?>
                                        <span><?php echo esc_html( sprintf( /* translators: %s: number of items */ _n( '%s item', '%s items', $items, 'oxi-flip-box-plugin' ), number_format_i18n( $items ) ) ); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="oxi-flip-sc-code-cell">
                                <div class="oxi-flip-sc-code">
                                    <code><?php echo esc_html( $shortcode ); ?></code>
                                    <button type="button" class="oxi-flip-sc-copy" data-copy="<?php echo esc_attr( $shortcode ); ?>" aria-label="<?php esc_attr_e( 'Copy shortcode', 'oxi-flip-box-plugin' ); ?>">
                                        <span class="dashicons dashicons-admin-page" aria-hidden="true"></span><span class="oxi-flip-sc-copy-text"><?php esc_html_e( 'Copy', 'oxi-flip-box-plugin' ); ?></span>
                                    </button>
                                </div>
                                <button type="button" class="oxi-flip-sc-copy-php" data-copy="<?php echo esc_attr( $php ); ?>">
                                    <span class="oxi-flip-sc-copy-text"><?php esc_html_e( 'Copy PHP code', 'oxi-flip-box-plugin' ); ?></span>
                                </button>
                            </td>
                            <td class="oxi-flip-sc-actions">
                                <div class="oxi-flip-sc-actions-wrap">
                                    <a class="oxi-flip-sc-action is-edit" href="<?php echo esc_url( $edit_url ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: flip box name */ __( 'Edit %s', 'oxi-flip-box-plugin' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span><?php esc_html_e( 'Edit', 'oxi-flip-box-plugin' ); ?>
                                    </a>
                                    <button type="button" class="oxi-flip-sc-action is-clone oxi-flip-sc-clone" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: flip box name */ __( 'Clone %s', 'oxi-flip-box-plugin' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-admin-page" aria-hidden="true"></span><?php esc_html_e( 'Clone', 'oxi-flip-box-plugin' ); ?>
                                    </button>
                                    <a class="oxi-flip-sc-action is-export" href="<?php echo esc_url( $this->create_export_link( 'demo', $id, '' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: flip box name */ __( 'Export %s', 'oxi-flip-box-plugin' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-download" aria-hidden="true"></span><?php esc_html_e( 'Export', 'oxi-flip-box-plugin' ); ?>
                                    </a>
                                    <button type="button" class="oxi-flip-sc-action is-delete oxi-flip-sc-delete" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: flip box name */ __( 'Delete %s', 'oxi-flip-box-plugin' ), $name ) ); ?>">
                                        <span class="dashicons dashicons-trash" aria-hidden="true"></span><?php esc_html_e( 'Delete', 'oxi-flip-box-plugin' ); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </section>
        <?php
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function admin_ajax_load() {
        wp_enqueue_script( 'oxi-flip-box-home', OXI_FLIP_BOX_URL . 'asset/backend/js/home.js', [ 'jquery', 'jquery.dataTables.min' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/home.js' ), true );
        wp_localize_script(
            'oxi-flip-box-home', 'oxi_flip_box_editor', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'oxi-flip-box-editor' ),
            ]
        );
    }

    /**
     * Generate safe path
     * @since v1.0.0
     */
    public function safe_path( $path ) {

        $path = str_replace( [ '//', '\\\\' ], [ '/', '\\' ], $path );
        return str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $path );
    }

    public function Render() {
        $rows = $this->database_data();
        ?>
        <hr class="wp-header-end">
        <div class="oxi-flip-settings oxi-flip-home"
            data-copied="<?php esc_attr_e( 'Copied', 'oxi-flip-box-plugin' ); ?>"
            data-copy-failed="<?php esc_attr_e( 'Press Ctrl+C to copy', 'oxi-flip-box-plugin' ); ?>"
            data-search="<?php esc_attr_e( 'Search flip boxes', 'oxi-flip-box-plugin' ); ?>"
            data-per-page="<?php esc_attr_e( '_MENU_ per page', 'oxi-flip-box-plugin' ); ?>"
            data-info="<?php esc_attr_e( 'Showing _START_ to _END_ of _TOTAL_', 'oxi-flip-box-plugin' ); ?>"
            data-info-empty="<?php esc_attr_e( 'No flip boxes to show', 'oxi-flip-box-plugin' ); ?>"
            data-info-filtered="<?php esc_attr_e( '(filtered from _MAX_)', 'oxi-flip-box-plugin' ); ?>"
            data-zero="<?php esc_attr_e( 'No flip boxes match your search.', 'oxi-flip-box-plugin' ); ?>"
            data-prev="<?php esc_attr_e( 'Previous', 'oxi-flip-box-plugin' ); ?>"
            data-next="<?php esc_attr_e( 'Next', 'oxi-flip-box-plugin' ); ?>"
            data-all="<?php esc_attr_e( 'All', 'oxi-flip-box-plugin' ); ?>">
            <?php
            $this->Admin_header( count( $rows ) );
            if ( empty( $rows ) ) {
                $this->empty_state();
            } else {
                $this->created_shortcode( $rows );
            }
            $this->create_new();
            ?>
        </div>
        <?php
    }

    public function Admin_header( $total = 0 ) {
        apply_filters( 'oxi-flip-box-support-and-comments', true );
        ?>
        <header class="oxi-flip-set-hero">
            <img class="oxi-flip-set-hero-logo" src="<?php echo esc_url( OXI_FLIP_BOX_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
            <div class="oxi-flip-set-hero-text">
                <h1 class="oxi-flip-set-title">
                    <?php esc_html_e( 'Flip boxes', 'oxi-flip-box-plugin' ); ?>
                    <span class="oxi-flip-sc-count"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
                </h1>
                <p class="oxi-flip-set-subtitle"><?php esc_html_e( 'Copy a shortcode into any page or post, or edit, clone, export and delete your flip boxes.', 'oxi-flip-box-plugin' ); ?></p>
            </div>
            <div class="oxi-flip-sc-hero-actions">
                <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-open="oxi-flip-import-dialog">
                    <span class="dashicons dashicons-upload" aria-hidden="true"></span><?php esc_html_e( 'Import', 'oxi-flip-box-plugin' ); ?>
                </button>
                <a class="oxi-flip-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-new' ) ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Create new', 'oxi-flip-box-plugin' ); ?>
                </a>
            </div>
        </header>
        <?php
    }

    /**
     * Shown when there are no flip boxes yet.
     *
     * @since 3.1.0
     */
    public function empty_state() {
        ?>
        <section class="oxi-flip-set-card oxi-flip-sc-empty">
            <span class="oxi-flip-sc-empty-icon dashicons dashicons-images-alt2" aria-hidden="true"></span>
            <h2 class="oxi-flip-set-card-title"><?php esc_html_e( 'No flip boxes yet', 'oxi-flip-box-plugin' ); ?></h2>
            <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'Pick a template, design it, and its shortcode will appear here ready to copy.', 'oxi-flip-box-plugin' ); ?></p>
            <div class="oxi-flip-set-actions">
                <a class="oxi-flip-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-new' ) ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Create your first flip box', 'oxi-flip-box-plugin' ); ?>
                </a>
                <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-open="oxi-flip-import-dialog">
                    <span class="dashicons dashicons-upload" aria-hidden="true"></span><?php esc_html_e( 'Import a JSON file', 'oxi-flip-box-plugin' ); ?>
                </button>
            </div>
        </section>
        <?php
    }

    public function create_new() {
        ?>
        <div class="oxi-flip-set-dialog" id="oxi-flip-import-dialog" hidden>
            <div class="oxi-flip-set-dialog-backdrop" data-oxi-flip-close></div>
            <form class="oxi-flip-set-dialog-box" method="post" enctype="multipart/form-data" role="dialog" aria-modal="true" aria-labelledby="oxi-flip-import-title">
                <span class="oxi-flip-set-dialog-icon is-brand dashicons dashicons-upload" aria-hidden="true"></span>
                <h2 class="oxi-flip-set-dialog-title" id="oxi-flip-import-title"><?php esc_html_e( 'Import a flip box', 'oxi-flip-box-plugin' ); ?></h2>
                <p class="oxi-flip-set-dialog-text"><?php esc_html_e( 'Choose a JSON file exported from Flipbox. It is added as a new flip box, nothing is replaced.', 'oxi-flip-box-plugin' ); ?></p>
                <label class="oxi-flip-sc-drop">
                    <span class="dashicons dashicons-media-code" aria-hidden="true"></span>
                    <span class="oxi-flip-sc-drop-text" data-empty="<?php esc_attr_e( 'Choose a .json file', 'oxi-flip-box-plugin' ); ?>"><?php esc_html_e( 'Choose a .json file', 'oxi-flip-box-plugin' ); ?></span>
                    <input type="file" name="importoxilabflipboxfile" accept=".json,application/json" required>
                </label>
                <?php wp_nonce_field( 'oxilab-flipbox-import' ); ?>
                <div class="oxi-flip-set-dialog-actions">
                    <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-close><?php esc_html_e( 'Cancel', 'oxi-flip-box-plugin' ); ?></button>
                    <button type="submit" class="oxi-flip-set-btn is-primary" name="importdatasubmit" value="Save"><?php esc_html_e( 'Import', 'oxi-flip-box-plugin' ); ?></button>
                </div>
            </form>
        </div>

        <div class="oxi-flip-set-dialog" id="oxi-flip-clone-dialog" hidden>
            <div class="oxi-flip-set-dialog-backdrop" data-oxi-flip-close></div>
            <form class="oxi-flip-set-dialog-box" id="oxi-flip-clone-form" role="dialog" aria-modal="true" aria-labelledby="oxi-flip-clone-title">
                <span class="oxi-flip-set-dialog-icon is-brand dashicons dashicons-admin-page" aria-hidden="true"></span>
                <h2 class="oxi-flip-set-dialog-title" id="oxi-flip-clone-title"><?php esc_html_e( 'Clone flip box', 'oxi-flip-box-plugin' ); ?></h2>
                <p class="oxi-flip-set-dialog-text"><?php esc_html_e( 'Creates a copy with the same design and items, then opens it in the editor.', 'oxi-flip-box-plugin' ); ?></p>
                <label class="oxi-flip-set-dialog-label" for="oxi-flip-clone-name"><?php esc_html_e( 'Name for the copy', 'oxi-flip-box-plugin' ); ?></label>
                <input type="text" class="oxi-flip-set-input" id="oxi-flip-clone-name" required autocomplete="off" maxlength="50" data-suffix="<?php esc_attr_e( 'copy', 'oxi-flip-box-plugin' ); ?>">
                <input type="hidden" id="oxi-flip-clone-id" value="">
                <p class="oxi-flip-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Cloning', 'oxi-flip-box-plugin' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not clone, try again.', 'oxi-flip-box-plugin' ); ?>"></p>
                <div class="oxi-flip-set-dialog-actions">
                    <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-close><?php esc_html_e( 'Cancel', 'oxi-flip-box-plugin' ); ?></button>
                    <button type="submit" class="oxi-flip-set-btn is-primary"><?php esc_html_e( 'Clone', 'oxi-flip-box-plugin' ); ?></button>
                </div>
            </form>
        </div>

        <div class="oxi-flip-set-dialog" id="oxi-flip-delete-sc-dialog" hidden>
            <div class="oxi-flip-set-dialog-backdrop" data-oxi-flip-close></div>
            <div class="oxi-flip-set-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="oxi-flip-delete-sc-title" aria-describedby="oxi-flip-delete-sc-desc">
                <span class="oxi-flip-set-dialog-icon dashicons dashicons-trash" aria-hidden="true"></span>
                <h2 class="oxi-flip-set-dialog-title" id="oxi-flip-delete-sc-title" data-template="<?php /* translators: %s: flip box name */ esc_attr_e( 'Delete %s?', 'oxi-flip-box-plugin' ); ?>"></h2>
                <p class="oxi-flip-set-dialog-text" id="oxi-flip-delete-sc-desc">
                    <?php esc_html_e( 'Any page that still uses this shortcode will show nothing in its place. Its design and items are deleted for good.', 'oxi-flip-box-plugin' ); ?>
                </p>
                <p class="oxi-flip-sc-dialog-code"><code></code></p>
                <p class="oxi-flip-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Deleting', 'oxi-flip-box-plugin' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not delete, try again.', 'oxi-flip-box-plugin' ); ?>"></p>
                <div class="oxi-flip-set-dialog-actions">
                    <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-close><?php esc_html_e( 'Cancel', 'oxi-flip-box-plugin' ); ?></button>
                    <button type="button" class="oxi-flip-set-btn is-danger-solid" id="oxi-flip-delete-sc-submit"><?php esc_html_e( 'Delete flip box', 'oxi-flip-box-plugin' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
}
