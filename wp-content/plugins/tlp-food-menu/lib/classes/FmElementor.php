<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists('FmElementor') ):

	class FmElementor {
		function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'init' ) );
			}
		}

		function init() {
			require_once( TLPFoodMenu()->getIncPath() . '/vendor/FmElementorWidget.php' );

			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new FmElementorWidget() );
		}
	}

endif;