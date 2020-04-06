<?php
namespace ElementorPostGrid;

/**
 * Class Elementor_Post_Carousel_Main
 *
 * Main Plugin class
 * @since 1.2.0
 */
class Elementor_Post_Carousel_Main {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * widget_style
	 *
	 * Load main style files.
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function widget_styles() {
		wp_register_style( 'post-carousel-elementor-addon-main', plugins_url( '/assets/css/main.css', __FILE__ ), null, '1.0.'.rand(1,99) );
		wp_enqueue_style( 'post-carousel-elementor-addon-main' );
	}
        
        /**
	 * widget_scripts
	 *
	 * Load main script files.
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function widget_scripts() {
		wp_register_script( 'post-carousel-elementor-script', plugins_url( '/assets/js/epc_script.js', __FILE__ ), null, '1.2.2'.time() );
		wp_enqueue_script( 'post-carousel-elementor-script' );
	}
        
        
	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function include_widgets_files() {
		require_once( __DIR__ . '/widgets/post-carousel.php' );
                require_once( __DIR__ . '/widgets/archive-categories.php' );
                require_once( __DIR__ . '/widgets/post-grid.php' );
                require_once( __DIR__ . '/widgets/post-listing.php' );
                require_once( __DIR__ . '/widgets/cpt-breadcrumb.php' );
                
                
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Post_Carousel_Widget() );
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Archive_Categories_Widget() );
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Post_Grid_Widget() );
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Post_List_Widget() );
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Elementor_Breadcrumb_Widget() );
                
                
	}

	public function register_widget_category( $elements_manager ) {

		$elements_manager->add_category(
			'wpcap-items',
			[
				'title' => __( 'WPCap Elements', 'post-carousel-elementor-addon' ),
				'icon' => 'fa fa-plug',
			]
		);

	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {

		// Register widget style
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
                
                add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
                
		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
                
                add_action('elementor/controls/controls_registered', array($this, 'controls_registered'));
               
	}
        
        /**
        * Add new group control
        *
        * @since v1.0.0
        */
        public function controls_registered($controls_manager)
        {
            require_once( __DIR__ . '/classes/Group_Control_Cpt_Posts.php' );
            
            $controls_manager->add_group_control('cpt_posts', new \Cpt_Addons_Elementor\Classes\Group_Control_CPT_Posts);
        }
}

// Instantiate Plugin Class
Elementor_Post_Carousel_Main::instance();
