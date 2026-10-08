<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages;

/**
 * Description of Import
 *
 * @author biplo
 */
class Import {

    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\Public_Helper;
    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\CSS_JS_Loader;

    public $IMPORT = [];
    public $wpdb;
    public $parent_table;
    public $child_table;
    public $import_table;
    public $TEMPLATE;

	/**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 2.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->parent_table = $wpdb->prefix . 'oxi_div_style';
        $this->child_table = $wpdb->prefix . 'oxi_div_list';
        $this->import_table = $wpdb->prefix . 'oxi_div_import';
        $this->CSSJS_load();
        $this->Render();
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function admin_ajax_load() {
        wp_enqueue_script( 'oxi-flip-import', OXI_FLIP_BOX_URL . 'asset/backend/js/import.js', [ 'jquery' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/import.js' ), true );
        wp_localize_script(
            'oxi-flip-import', 'oxi_flip_box_editor', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'oxi-flip-box-editor' ),
            ]
        );
    }
    /**
     * Templates not added to the Create New list yet, keyed by template key.
     *
     * @since 3.1.0
     *
     * @return array
     */
    public function available() {
        $list = [];
        foreach ( $this->TEMPLATE as $k => $value ) {
            $id = (int) explode( 'tyle', $k )[1];
            if ( ! array_key_exists( $id, $this->IMPORT ) ) {
                $list[ $k ] = $value;
            }
        }
        return $list;
    }

    public function template( $available = [] ) {
        $checking = apply_filters( 'oxi-flip-box-plugin/pro_version', true );
		?>
        <div class="oxi-flip-tpl-list">
            <?php
            foreach ( $available as $k => $value ) {
                $id = (int) explode( 'tyle', $k )[1];
                $C = 'OXI_FLIP_BOX_PLUGINS\Public_Render\\' . $k;
                /* translators: %s: template number */
                $title = sprintf( __( 'Style %s', 'oxi-flip-box-plugin' ), $id );
                $pro_only = $id > 10 && false == $checking;
				?>
                <section class="oxi-flip-set-card oxi-flip-tpl" id="<?php echo esc_attr( $k ); ?>">
                    <div class="oxi-flip-tpl-head">
                        <div class="oxi-flip-tpl-title">
                            <h2><?php echo esc_html( $title ); ?></h2>
                            <span class="oxi-flip-tpl-count"><?php echo esc_html( sprintf( /* translators: %s: number of designs */ _n( '%s design', '%s designs', count( $value ), 'oxi-flip-box-plugin' ), number_format_i18n( count( $value ) ) ) ); ?></span>
                        </div>
                        <?php if ( $pro_only ) : ?>
                            <span class="oxi-flip-tpl-pro"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php esc_html_e( 'Pro only', 'oxi-flip-box-plugin' ); ?></span>
                        <?php else : ?>
                            <form method="post" class="shortcode-addons-template-import oxi-flip-tpl-add">
                                <input type="hidden" name="oxiimportstyle" value="<?php echo esc_attr( $id ); ?>">
                                <button type="submit" class="oxi-flip-set-btn is-primary is-sm">
                                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><span class="oxi-flip-tpl-add-text"><?php esc_html_e( 'Add to Create New', 'oxi-flip-box-plugin' ); ?></span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="oxi-flip-tpl-grid">
                        <?php
                        if ( class_exists( $C ) ) :
                            foreach ( $value as $key => $v ) {
                                $REND = json_decode( $v, true );
                                ?>
                                <div class="oxi-flip-tpl-item">
                                    <div class="oxi-flip-tpl-preview">
                                        <div class="oxilab-flip-box-col-3">
                                            <?php new $C( $REND['style'], $REND['child'] ); ?>
                                        </div>
                                    </div>
                                    <div class="oxi-flip-tpl-foot">
                                        <span class="oxi-flip-tpl-design"><?php echo esc_html( sprintf( /* translators: %s: design number */ __( 'Design %s', 'oxi-flip-box-plugin' ), $key ) ); ?></span>
                                    </div>
                                </div>
                                <?php
                            }
                        endif;
                        ?>
                    </div>
                </section>
				<?php
            }
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
                    <?php esc_html_e( 'Template library', 'oxi-flip-box-plugin' ); ?>
                    <span class="oxi-flip-tpl-total"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
                </h1>
                <p class="oxi-flip-set-subtitle"><?php esc_html_e( 'Add a template to your Create New list, then use any of its designs for a new flip box.', 'oxi-flip-box-plugin' ); ?></p>
            </div>
            <a class="oxi-flip-set-btn is-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-new' ) ); ?>">
                <span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span><?php esc_html_e( 'Back to Create New', 'oxi-flip-box-plugin' ); ?>
            </a>
        </header>
		<?php
    }

    /**
     * Shown when every template is already in the Create New list.
     *
     * @since 3.1.0
     */
    public function empty_state() {
        ?>
        <section class="oxi-flip-set-card oxi-flip-tpl-empty">
            <span class="oxi-flip-set-card-icon dashicons dashicons-yes-alt" aria-hidden="true"></span>
            <h2 class="oxi-flip-set-card-title"><?php esc_html_e( 'Every template is already added', 'oxi-flip-box-plugin' ); ?></h2>
            <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'All templates are in your Create New list, ready to use.', 'oxi-flip-box-plugin' ); ?></p>
            <div class="oxi-flip-set-actions">
                <a class="oxi-flip-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-new' ) ); ?>"><?php esc_html_e( 'Go to Create New', 'oxi-flip-box-plugin' ); ?></a>
            </div>
        </section>
        <?php
    }

    public function Render() {
        $available = $this->available();
		?>
        <hr class="wp-header-end">
        <div class="oxi-flip-settings oxi-flip-import"
            data-adding="<?php esc_attr_e( 'Adding', 'oxi-flip-box-plugin' ); ?>"
            data-add-error="<?php esc_attr_e( 'Could not add, try again', 'oxi-flip-box-plugin' ); ?>">
            <?php
            $this->Admin_header( count( $available ) );
            if ( empty( $available ) ) {
                $this->empty_state();
            } else {
                $this->template( $available );
            }
            ?>
        </div>
		<?php
    }

    public function CSSJS_load() {
        $this->admin_css_loader();
        $this->admin_ajax_load();
        apply_filters( 'oxi-flip-box-plugin/admin_menu', true );
        $import = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM  $this->import_table WHERE type = %s ", 'flip' ), ARRAY_A );
        foreach ( $import as $value ) {
            $this->IMPORT[ $value['name'] ] = $value['name'];
        }
        $this->TEMPLATE = include OXI_FLIP_BOX_PATH . 'Page/JSON.php';
    }
}
