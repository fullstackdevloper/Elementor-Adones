<?php

namespace ElementorPostGrid\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */

class Elementor_Archive_Categories_Widget extends Widget_Base {

    public function get_name() {
        return 'elementor-archive-categories';
    }

    public function get_title() {
        return __('Archive Categories', 'cpt-elementor-addon');
    }

    public function get_icon() {
        return 'eicon-wordpress';
    }

    public function get_categories() {
        return ['wpcap-items'];
    } 

    protected function _register_controls() {

        $this->wpcap_content_layout_options();
        $this->wpcap_content_query_options();
    }
    
    /**
     * Content Layout Options.
     */
    private function wpcap_content_layout_options() {

        
    }

    /**
     * Content Query Options.
     */
    private function wpcap_content_query_options() {

        
    }
    
    protected function get_subcategories($parent, $hide_empty) {
        $cat_args = array(
            'parent' => $parent,
            'hide_empty' => $hide_empty,
        );
        $terms = get_terms('product_categories', $cat_args);
        
        return $terms;
    }

    protected function render($instance = []) {
        // Get settings.
        $hide_empty = false;
        $subcategories = $this->get_subcategories(null, $hide_empty);
        echo '<div class="cpt_accordion">';
        if (!empty($subcategories)) {
            //echo "<pre>"; print_r($subcategories); die;
            echo "<ul class='cpt_accordion_inner woo_lvl_1'>";
            foreach ($subcategories as $key => $subcategory) {
                $isactive = $key == 0 ? 'woo_active' : '';
                echo "<li class='{$isactive} cpt_accordion_item woo_subcategory_parent woo_subcategory $subcategory->slug'>"
                . "<a class='prnt_link' href='" . get_category_link($subcategory->term_id) . "'>{$subcategory->name}</a>";

                $subcats = $this->get_subcategories($subcategory->term_id, $hide_empty);
                if (!empty($subcats)) {
                    echo "<span class='acordion_sign woo_plus cpt_plus'>+</span><span class='acordion_sign woo_minus cpt_minus'>-</span>";
                    echo "<ul class='cpt_accordion_inner_2 woo_nxt_lvl ul_labl_2 woo_subcategory woo_lvl_1'>";
                    foreach ($subcats as $key => $subcat) {
                        $subcats3 = $this->get_subcategories($subcat->term_id, $hide_empty);
                        $class = !empty($subcats3) ? 'has_child_elements' : '';
                        echo "<li class='cpt_accordion_item {$class} woo_subcategory $subcat->slug'>";
                            echo "<a href='" . get_category_link($subcat->term_id) . "'>{$subcat->name}</a>";
                        //check lbl 3
                        
                        if (!empty($subcats3)) {
                            echo "<span class='acordion_sign woo_plus cpt_plus'>+</span><span class='acordion_sign woo_minus cpt_minus'>-</span>";
                            echo "<ul class='cpt_accordion_inner_3 woo_nxt_lvl ul_labl_3 woo_subcategory woo_lvl_1'>";
                            foreach ($subcats3 as $key => $subcat3) {
                                echo "<li class='woo_subcategory $subcat3->slug'><a href='" . get_category_link($subcat3->term_id) . "'>{$subcat3->name}</a></li>";
                            }
                            echo "</ul>";
                        }
                        echo '</li>';
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
            echo "</ul>";
        }
        echo '</div>';
    }

}
    