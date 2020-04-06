<?php

/**
 * Plugin Name: CPT Elementor Addons
 * Description: Elementor page builder addon. post carousel , categories post grid
 * Plugin URI: https://example.com/
 * Version: 1.0.0
 * Author: Automatic
 * Author URI: https://facebook.com/
 * Text Domain: post-carousel-elementor-addon
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

require( __DIR__ . '/classes/Helper.php' );

/**
 * Main Post Grid Elementor Addon Class
 *
 * The init class that runs the Post Grid plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 *
 * You should only modify the constants to match your plugin's needs.
 *
 * Any custom code should go inside Plugin Class in the plugin.php file.
 * @since 1.2.0
 */

final class CptElementorAddons {

    use \CPT_Addons_Elementor\Traits\Helper;

    /**
     * Plugin Version
     *
     * @since 1.2.0
     * @var string The plugin version.
     */
    const VERSION = '1.0.3';

    /**
     * Minimum Elementor Version
     *
     * @since 1.2.0
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '5.4.0';

    /**
     * Constructor
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct() {

        // Load translation
        add_action('init', array($this, 'i18n'));

        // Init Plugin
        add_action('plugins_loaded', array($this, 'init'));
        //add_action( 'wp_footer', [$this, 'extend_carousel_file']);

        /* ajax calls */
        add_action('wp_ajax_cpt_filter', [$this, 'filter_cpt_posts']);
        add_action('wp_ajax_nopriv_cpt_filter', [$this, 'filter_cpt_posts']);
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     * Fired by `init` action hook.
     *
     * @since 1.2.0
     * @access public
     */
    public function i18n() {
        load_plugin_textdomain('post-carousel-elementor-addon');
    }

    /**
     * Initialize the plugin
     *
     * Validates that Elementor is already loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed include the plugin class.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.2.0
     * @access public
     */
    public function init() {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once( 'plugin.php' );
    }

    public function filter_cpt_posts() {
        $metaquery = [];
        if (isset($_POST['query_arg']) && $_POST['query_arg']) {
            $arg = json_decode(stripslashes($_POST['query_arg']), true);
            $settings = json_decode(stripslashes($_POST['settings']), true);
            $metaquery = $this->_get_metaquery_filter($_POST);
            $allarg = array_merge($arg, $metaquery);
            $all_posts = new \WP_Query($allarg);
            $grid_style = $settings['grid_style'];
            if ($all_posts->have_posts()) :
                if (5 == $grid_style) {
                    include( __DIR__ . '/widgets/layouts/layout-5.php' );
                } elseif (4 == $grid_style) {
                    include( __DIR__ . '/widgets/layouts/layout-4.php' );
                } elseif (3 == $grid_style) {
                    include( __DIR__ . '/widgets/layouts/layout-3.php' );
                } elseif (2 == $grid_style) {
                    include( __DIR__ . '/widgets/layouts/layout-2.php' );
                } else {
                    include( __DIR__ . '/widgets/layouts/layout-1.php' );
                }
            else:
                echo "<span class='cpt_no_data'>No posts to display. Please try a different search.</span>";
            endif;
            die;
        }
    }

    protected function _get_metaquery_filter($data) {
        $filters = [];
        if (isset($data['categories']) && !empty($data['categories'])) {
            $cats = $data['categories'];
            $taxquery = array(
                array(
                    'taxonomy' => $data['taxonomy'],
                    'field' => 'id',
                    'terms' => $cats,
                    'operator' => 'IN'
                )
            );

            $filters['tax_query'] = $taxquery;
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $companies = $data['company'];
            $metas2 = array('relation' => 'OR');
            foreach ($companies as $company) {
                $metas2[] = array(
                    'key' => 'cpt_company',
                    'value' => sprintf(':"%s";', $company),
                    'compare' => 'LIKE'
                );
            }
            $filters['meta_query'][] = $metas2;
        }
        if (isset($data['regions']) && !empty($data['regions'])) {
            $regions = $data['regions'];
            $religion_filters = array('relation' => 'OR');
            foreach ($regions as $region) {
                $religion_filters[] = array(
                    'key' => '_region',
                    'value' => $region,
                    'compare' => '=',
                    'type' => 'string'
                );
            }
            $filters['meta_query'][] = $religion_filters;
        }

        if (isset($data['region']) && !isset($religion_filters)) {
            $region = $data['region'];
        }
        return $filters;
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor */
                esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'post-carousel-elementor-addon'), '<strong>' . esc_html__('Post Grid Elementor Addon', 'post-carousel-elementor-addon') . '</strong>', '<strong>' . esc_html__('Elementor', 'post-carousel-elementor-addon') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'post-carousel-elementor-addon'), '<strong>' . esc_html__('Post Grid Elementor Addon', 'post-carousel-elementor-addon') . '</strong>', '<strong>' . esc_html__('Elementor', 'post-carousel-elementor-addon') . '</strong>', self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
                /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'post-carousel-elementor-addon'), '<strong>' . esc_html__('Post Grid Elementor Addon', 'post-carousel-elementor-addon') . '</strong>', '<strong>' . esc_html__('PHP', 'post-carousel-elementor-addon') . '</strong>', self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

}

// Instantiate Elementor_Post_Carousel.
new CptElementorAddons();
