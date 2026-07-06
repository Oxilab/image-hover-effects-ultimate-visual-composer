<?php

namespace OXI_FLIP_BOX_PLUGINS\Modules;



class Elementor {

    /**
     * CSS class used as the widget's panel icon.
     */
    const ICON_CLASS = 'oxi-eicon-flipbox';

    public function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register' ] );
        // Paint the custom widget icon (plugin SVG) in the Elementor editor panel.
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_icon_style' ] );
    }

    /**
     * Output the CSS that renders the plugin's SVG as the Flipbox widget icon
     * in Elementor's widget panel. get_icon() returns ICON_CLASS, applied to an
     * <i> element; we paint that element with the SVG as a background image.
     */
    public function editor_icon_style() {
        if ( ! defined( 'OXI_FLIP_BOX_URL' ) ) {
            return;
        }
        $svg = OXI_FLIP_BOX_URL . 'image/admin-icon.svg';
        $css = 'i.' . self::ICON_CLASS . '{display:inline-block;width:28px;height:28px;'
            . "background:url('" . esc_url( $svg ) . "') center/contain no-repeat;}"
            . 'i.' . self::ICON_CLASS . ':before{content:"";}';
        wp_register_style( 'oxi-flipbox-elementor-icon', false );
        wp_enqueue_style( 'oxi-flipbox-elementor-icon' );
        wp_add_inline_style( 'oxi-flipbox-elementor-icon', $css );
    }

    public function register( $widgets_manager ) {
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
            return;
        }
        if ( ! class_exists( __NAMESPACE__ . '\Flipbox_Elementor_Widget' ) ) {
            oxilab_define_flipbox_elementor_widget();
        }
                        $widgets_manager->register( new Flipbox_Elementor_Widget() );
    }
}
function oxilab_define_flipbox_elementor_widget() {
    if ( class_exists( '\Elementor\Widget_Base' ) && ! class_exists( __NAMESPACE__ . '\Flipbox_Elementor_Widget' ) ) {
        class Flipbox_Elementor_Widget extends \Elementor\Widget_Base {
            public function get_name() { return 'wpkin_flipbox'; }
            public function get_title() { return 'Flipbox'; }
            public function get_icon() { return 'oxi-eicon-flipbox'; }
            public function get_categories() { return [ 'general' ]; }
            public function get_style_depends() { return [ 'oxi-animation', 'flip-box-addons-style' ]; }
            public function get_script_depends() { return [ 'jquery', 'waypoints.min', 'flipbox-addons-jquery' ]; }
            protected function register_controls() {
                global $wpdb;
                $options = [];
                $table = $wpdb->prefix . 'oxi_div_style';
                $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$table} WHERE type = %s ORDER BY id DESC", 'flip' ), ARRAY_A );
                if ( $rows ) {
                    foreach ( $rows as $row ) {
                        $label = ( isset( $row['name'] ) && $row['name'] !== '' ? $row['name'] : 'Flipbox' ) . ' (#' . $row['id'] . ')';
                        $options[ (string) $row['id'] ] = $label;
                    }
                }
                $default = '';
                if ( ! empty( $options ) ) {
                    $keys = array_keys( $options );
                    $default = reset( $keys );
                }
                $this->start_controls_section( 'section_flipbox', [ 'label' => 'Flipbox' ] );
                $this->add_control( 'id', [
                    'label' => 'Shortcode',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $options,
                    'default' => $default,
                ] );
                $this->end_controls_section();
            }
            protected function render() {
                $settings = $this->get_settings_for_display();
                $id = isset( $settings['id'] ) ? $settings['id'] : '';
                echo do_shortcode( '[oxilab_flip_box id="' . esc_attr( $id ) . '"]' );
            }
        }
    }
}