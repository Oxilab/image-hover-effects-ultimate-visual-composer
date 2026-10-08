<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages;

/**
 * Description of Create
 *
 * @author biplo
 */
class Create {

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

    public $IMPORT = [];
    public $TEMPLATE;



    public function template() {
		?>
        <div class="oxi-flip-tpl-list">
            <?php
            if ( count( $this->IMPORT ) == 0 ) :
                $this->IMPORT = [
                    1 => [
						'type' => 'flip',
						'name' => 1,
					],
                    2 => [
						'type' => 'flip',
						'name' => 2,
					],
                    3 => [
						'type' => 'flip',
						'name' => 3,
					],
                    4 => [
						'type' => 'flip',
						'name' => 4,
					],
                    5 => [
						'type' => 'flip',
						'name' => 5,
					],
                ];
                foreach ( $this->IMPORT as $value ) {
                    $this->wpdb->query( $this->wpdb->prepare( "INSERT INTO {$this->import_table} (type, name) VALUES ( %s, %d)", [ $value['type'], $value['name'] ] ) );
                }
            endif;

            foreach ( $this->TEMPLATE as $key => $value ) {
                $id = explode( 'tyle', $key )[1];
                $number = rand();
                if ( array_key_exists( $id, $this->IMPORT ) ) :
                    $C = 'OXI_FLIP_BOX_PLUGINS\Public_Render\\' . $key;
                    /* translators: %s: template number */
                    $title = sprintf( __( 'Style %s', 'oxi-flip-box-plugin' ), $id );
					?>
                    <section class="oxi-flip-set-card oxi-flip-tpl" id="<?php echo esc_attr( $key ); ?>">
                        <div class="oxi-flip-tpl-head">
                            <div class="oxi-flip-tpl-title">
                                <h2><?php echo esc_html( $title ); ?></h2>
                                <span class="oxi-flip-tpl-count"><?php echo esc_html( sprintf( /* translators: %s: number of designs */ _n( '%s design', '%s designs', count( $value ), 'oxi-flip-box-plugin' ), number_format_i18n( count( $value ) ) ) ); ?></span>
                            </div>
                            <form method="post" class="shortcode-addons-template-deactive oxi-flip-tpl-remove">
                                <input type="hidden" name="oxideletestyle" value="<?php echo esc_attr( $id ); ?>">
                                <button type="submit" class="oxi-flip-tpl-remove-btn" title="<?php esc_attr_e( 'You can add it back from Import Templates.', 'oxi-flip-box-plugin' ); ?>">
                                    <span class="dashicons dashicons-hidden" aria-hidden="true"></span><span class="oxi-flip-tpl-remove-text"><?php esc_html_e( 'Remove from list', 'oxi-flip-box-plugin' ); ?></span>
                                </button>
                            </form>
                        </div>
                        <div class="oxi-flip-tpl-grid">
                            <?php
                            if ( class_exists( $C ) ) :
                                foreach ( $value as $k => $v ) {
                                    $REND   = json_decode( $v, true );
                                    $source = 'oxistyle' . $number . 'data-' . $k;
                                    /* translators: 1: template name, 2: design number */
                                    $label  = sprintf( __( '%1$s, design %2$s', 'oxi-flip-box-plugin' ), $title, $k );
                                    ?>
                                    <div class="oxi-flip-tpl-item">
                                        <div class="oxi-flip-tpl-preview">
                                            <div class="oxilab-flip-box-col-3">
                                                <?php new $C( $REND['style'], $REND['child'] ); ?>
                                            </div>
                                            <textarea style="display:none" id="<?php echo esc_attr( $source ); ?>"><?php echo htmlentities( json_encode( $REND ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- htmlentities() escapes it. ?></textarea>
                                        </div>
                                        <div class="oxi-flip-tpl-foot">
                                            <span class="oxi-flip-tpl-design"><?php echo esc_html( sprintf( /* translators: %s: design number */ __( 'Design %s', 'oxi-flip-box-plugin' ), $k ) ); ?></span>
                                            <button type="button" class="oxi-flip-set-btn is-primary is-sm oxi-flip-tpl-use" data-source="<?php echo esc_attr( $source ); ?>" data-label="<?php echo esc_attr( $label ); ?>">
                                                <?php esc_html_e( 'Use this design', 'oxi-flip-box-plugin' ); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            endif;
                            ?>
                        </div>
                    </section>
					<?php
                endif;
            }
            ?>
        </div>
		<?php
    }

    public function Render() {
		?>
        <hr class="wp-header-end">
        <div class="oxi-flip-settings oxi-flip-create"
            data-removing="<?php esc_attr_e( 'Removing', 'oxi-flip-box-plugin' ); ?>"
            data-remove-error="<?php esc_attr_e( 'Could not remove, try again', 'oxi-flip-box-plugin' ); ?>">
            <?php
            $this->Admin_header();
            $this->template();
            $this->create_new();
            ?>
        </div>
		<?php
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

    public function Admin_header() {
        apply_filters( 'oxi-flip-box-support-and-comments', true );
		?>
        <header class="oxi-flip-set-hero">
            <img class="oxi-flip-set-hero-logo" src="<?php echo esc_url( OXI_FLIP_BOX_URL . 'image/logo.png' ); ?>" alt="" width="52" height="52">
            <div class="oxi-flip-set-hero-text">
                <h1 class="oxi-flip-set-title"><?php esc_html_e( 'Create a flip box', 'oxi-flip-box-plugin' ); ?></h1>
                <p class="oxi-flip-set-subtitle"><?php esc_html_e( 'Pick a design you like, give it a name, then customize it in the editor.', 'oxi-flip-box-plugin' ); ?></p>
            </div>
            <a class="oxi-flip-set-btn is-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-import' ) ); ?>">
                <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Browse more templates', 'oxi-flip-box-plugin' ); ?>
            </a>
        </header>
		<?php
    }

    public function create_new() {
		?>
        <section class="oxi-flip-set-card oxi-flip-tpl-more">
            <span class="oxi-flip-set-card-icon dashicons dashicons-layout" aria-hidden="true"></span>
            <div class="oxi-flip-tpl-more-text">
                <h2 class="oxi-flip-set-card-title"><?php esc_html_e( 'Want more designs?', 'oxi-flip-box-plugin' ); ?></h2>
                <p class="oxi-flip-set-card-sub"><?php esc_html_e( 'Add more templates from the template library and they will show up here.', 'oxi-flip-box-plugin' ); ?></p>
            </div>
            <a class="oxi-flip-set-btn is-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=oxi-flip-box-ultimate-import' ) ); ?>"><?php esc_html_e( 'Browse templates', 'oxi-flip-box-plugin' ); ?></a>
        </section>

        <div class="oxi-flip-set-dialog" id="oxi-flip-create-dialog" hidden>
            <div class="oxi-flip-set-dialog-backdrop" data-oxi-flip-close></div>
            <form class="oxi-flip-set-dialog-box" id="oxi-flip-create-form" role="dialog" aria-modal="true" aria-labelledby="oxi-flip-create-title">
                <span class="oxi-flip-set-dialog-icon is-brand dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                <h2 class="oxi-flip-set-dialog-title" id="oxi-flip-create-title"><?php esc_html_e( 'Create flip box', 'oxi-flip-box-plugin' ); ?></h2>
                <p class="oxi-flip-set-dialog-text oxi-flip-create-design"></p>
                <label class="oxi-flip-set-dialog-label" for="oxi-flip-create-name"><?php esc_html_e( 'Name', 'oxi-flip-box-plugin' ); ?></label>
                <input type="text" class="oxi-flip-set-input" id="oxi-flip-create-name" required autocomplete="off" maxlength="50" placeholder="<?php esc_attr_e( 'For example: Team members', 'oxi-flip-box-plugin' ); ?>">
                <input type="hidden" id="oxi-flip-create-source" value="">
                <p class="oxi-flip-set-dialog-status" role="status" aria-live="polite"
                    data-saving="<?php esc_attr_e( 'Creating', 'oxi-flip-box-plugin' ); ?>"
                    data-error="<?php esc_attr_e( 'Could not create it, try again.', 'oxi-flip-box-plugin' ); ?>"></p>
                <div class="oxi-flip-set-dialog-actions">
                    <button type="button" class="oxi-flip-set-btn is-secondary" data-oxi-flip-close><?php esc_html_e( 'Cancel', 'oxi-flip-box-plugin' ); ?></button>
                    <button type="submit" class="oxi-flip-set-btn is-primary"><?php esc_html_e( 'Create and edit', 'oxi-flip-box-plugin' ); ?></button>
                </div>
            </form>
        </div>
		<?php
    }
    public function CSSJS_load() {
        $this->admin_css_loader();
        $this->admin_ajax_load();
        apply_filters( 'oxi-flip-box-plugin/admin_menu', true );
        $i = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM  $this->import_table WHERE type = %s", 'flip' ), ARRAY_A );
        foreach ( $i as $value ) {
            $this->IMPORT[ $value['name'] ] = $value;
        }
        $this->TEMPLATE = include OXI_FLIP_BOX_PATH . 'Page/JSON.php';
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function admin_ajax_load() {
        wp_enqueue_script( 'oxi-flip-create', OXI_FLIP_BOX_URL . 'asset/backend/js/create.js', [ 'jquery' ], filemtime( OXI_FLIP_BOX_PATH . 'asset/backend/js/create.js' ), true );
        wp_localize_script(
            'oxi-flip-create', 'oxi_flip_box_editor', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'oxi-flip-box-editor' ),
            ]
        );
    }
}
