<?php

namespace ElementorPostGrid\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */

class Elementor_Post_List_Widget extends Widget_Base {
    
    use \CPT_Addons_Elementor\Traits\Helper;
    
    public function get_name() {
        return 'elementor-list-posts';
    }

    public function get_title() {
        return __('Post List', 'post-list-elementor-addon');
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return ['wpcap-items'];
    }

    private function wpcap_get_all_post_categories($post_type) {

        $options = array();

        $taxonomy = 'category';

        if (!empty($taxonomy)) {
            // Get categories for post type.
            $terms = get_terms(
                    array(
                        'taxonomy' => $taxonomy,
                        'hide_empty' => false,
                    )
            );
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (isset($term)) {
                        if (isset($term->slug) && isset($term->name)) {
                            $options[$term->slug] = $term->name;
                        }
                    }
                }
            }
        }

        return $options;
    }

    protected function _register_controls() {

        $this->wpcap_content_layout_options();
        $this->wpcap_content_query_options();

        $this->wpcap_style_layout_options();
        $this->wpcap_style_box_options();
        $this->wpcap_style_image_options();

        $this->wpcap_style_title_options();
        $this->wpcap_style_meta_options();
        $this->wpcap_style_content_options();
        $this->wpcap_style_readmore_options();
    }
    
    protected function _get_post_types() {
        $types = [];
        $post_types = get_post_types(array('public' => true, 'show_in_nav_menus' => true), 'object');
        foreach ($post_types as $type) {
            $types[$type->name] = $type->label;
        }
        return $types;
    }
    
    /**
     * Content Layout Options.
     */
    private function wpcap_content_layout_options() {

        $this->start_controls_section(
            'section_layout', [
                'label' => esc_html__('Layout', 'post-list-elementor-addon'),
            ]
        );

        $this->add_control(
            'list_style', [
                'label' => __('List Style', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    'classic' => esc_html__('Classic', 'post-list-elementor-addon'),
                    'modern' => esc_html__('Modern', 'post-list-elementor-addon'),
                    //'3' => esc_html__('Listing', 'post-list-elementor-addon'),
                    //'4' => esc_html__('Layout 4', 'post-list-elementor-addon'),
                    //'5' => esc_html__('Layout 5', 'post-list-elementor-addon'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'columns', [
                'label' => __('Columns', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'prefix_class' => 'elementor-list%s-',
                'frontend_available' => true,
                'selectors' => [
                    '.elementor-msie {{WRAPPER}} .elementor-portfolio-item' => 'width: calc( 100% / {{SIZE}} )',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page', [
                'label' => __('Posts Per Page', 'post-list-elementor-addon'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

        $this->add_control(
            'show_image', [
                'label' => __('Image', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-list-elementor-addon'),
                'label_off' => __('Hide', 'post-list-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'post_thumbnail',
            'exclude' => ['custom'],
            'default' => 'full',
            'prefix_class' => 'post-thumbnail-size-',
            'condition' => [
                'show_image' => 'yes',
            ],
                ]
        );

        $this->add_control(
            'show_title', [
                'label' => __('Title', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-list-elementor-addon'),
                'label_off' => __('Hide', 'post-list-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_tag', [
                'label' => __('Title HTML Tag', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_data', [
                'label' => __('Meta Data', 'post-list-elementor-addon'),
                'label_block' => true,
                'type' => Controls_Manager::SELECT2,
                'default' => ['date', 'comments'],
                'multiple' => true,
                'options' => [
                    'author' => __('Author', 'post-list-elementor-addon'),
                    'date' => __('Date', 'post-list-elementor-addon'),
                    'categories' => __('Categories', 'post-list-elementor-addon'),
                    'comments' => __('Comments', 'post-list-elementor-addon'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'meta_separator', [
                'label' => __('Separator Between', 'post-list-elementor-addon'),
                'type' => Controls_Manager::TEXT,
                'default' => '/',
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-meta span + span:before' => 'content: "{{VALUE}}"',
                ],
                'condition' => [
                    'meta_data!' => [],
                ],
            ]
        );

        $this->add_control(
            'show_excerpt', [
                'label' => __('Excerpt', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-list-elementor-addon'),
                'label_off' => __('Hide', 'post-list-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'excerpt_length', [
                'label' => __('Excerpt Length', 'post-list-elementor-addon'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_read_more', [
                'label' => __('Read More', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-list-elementor-addon'),
                'label_off' => __('Hide', 'post-list-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'read_more_text', [
                'label' => __('Read More Text', 'post-list-elementor-addon'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More »', 'post-list-elementor-addon'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'display_categories', [
                'label' => __('Categories', 'elementor-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'elementor-pro'),
                'label_off' => __('Hide', 'elementor-pro'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        
        $this->add_control(
                'content_align', [
                    'label' => __('Alignment', 'post-list-elementor-addon'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', 'post-list-elementor-addon'),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', 'post-list-elementor-addon'),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', 'post-list-elementor-addon'),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => 'left',
                    'selectors' => [
                        '{{WRAPPER}} .post-list-inner' => 'text-align: {{VALUE}};',
                    ],
                    'separator' => 'before',
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Content Query Options.
     */
    private function wpcap_content_query_options() {

        $this->start_controls_section(
                'section_query', [
            'label' => __('Query', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->add_group_control(
            'cpt_posts',
            [
                'name' => 'cpt_posts',
            ]
        );

        $this->add_control(
                'advanced', [
            'label' => __('Advanced', 'post-list-elementor-addon'),
            'type' => Controls_Manager::HEADING,
                ]
        );

        $this->add_control(
                'orderby', [
            'label' => __('Order By', 'post-list-elementor-addon'),
            'type' => Controls_Manager::SELECT,
            'default' => 'post_date',
            'options' => [
                'post_date' => __('Date', 'post-list-elementor-addon'),
                'post_title' => __('Title', 'post-list-elementor-addon'),
                'rand' => __('Random', 'post-list-elementor-addon'),
            ],
                ]
        );

        $this->add_control(
                'order', [
            'label' => __('Order', 'post-list-elementor-addon'),
            'type' => Controls_Manager::SELECT,
            'default' => 'desc',
            'options' => [
                'asc' => __('ASC', 'post-list-elementor-addon'),
                'desc' => __('DESC', 'post-list-elementor-addon'),
            ],
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Style Layout Options.
     */
    private function wpcap_style_layout_options() {

        // Layout.
        $this->start_controls_section(
                'section_layout_style', [
            'label' => __('Layout', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        // Columns margin.
        $this->add_control(
            'list_style_columns_margin', [
                'label' => __('Columns margin', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container' => 'list-column-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        // Row margin.
        $this->add_control(
            'list_style_rows_margin', [
                'label' => __('Rows margin', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 30,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container' => 'list-row-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Style Box Options.
     */
    private function wpcap_style_box_options() {

        // Box.
        $this->start_controls_section(
            'section_box', [
                'label' => __('Box', 'post-list-elementor-addon'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Image border radius.
        $this->add_control(
            'list_box_border_width', [
                'label' => __('Border Widget', 'post-list-elementor-addon'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        // Border Radius.
        $this->add_control(
            'list_style_border_radius', [
                'label' => __('Border Radius', 'post-list-elementor-addon'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        // Box internal padding.
        $this->add_responsive_control(
            'list_items_style_padding', [
                'label' => __('Padding', 'post-list-elementor-addon'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->start_controls_tabs('list_button_style');

        // Normal tab.
        $this->start_controls_tab(
            'list_button_style_normal', [
                'label' => __('Normal', 'post-list-elementor-addon'),
            ]
        );

        // Normal background color.
        $this->add_control(
            'list_button_style_normal_bg_color', [
                'type' => Controls_Manager::COLOR,
                'label' => __('Background Color', 'post-list-elementor-addon'),
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'separator' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Normal border color.
        $this->add_control(
            'list_button_style_normal_border_color', [
                'type' => Controls_Manager::COLOR,
                'label' => __('Border Color', 'post-list-elementor-addon'),
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'separator' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        // Normal box shadow.
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'list_button_style_normal_box_shadow',
                'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post',
            ]
        );

        $this->end_controls_tab();

        // Hover tab.
        $this->start_controls_tab(
            'list_button_style_hover', [
                'label' => __('Hover', 'post-list-elementor-addon'),
            ]
        );

        // Hover background color.
        $this->add_control(
            'list_button_style_hover_bg_color', [
                'type' => Controls_Manager::COLOR,
                'label' => __('Background Color', 'post-list-elementor-addon'),
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'separator' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Hover border color.
        $this->add_control(
            'list_button_style_hover_border_color', [
                'type' => Controls_Manager::COLOR,
                'label' => __('Border Color', 'post-list-elementor-addon'),
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'separator' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpcap-list-container .wpcap-post:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        // Hover box shadow.
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'list_button_style_hover_box_shadow',
            'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post:hover',
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Style Image Options.
     */
    private function wpcap_style_image_options() {

        // Box.
        $this->start_controls_section(
                'section_image', [
            'label' => __('Image', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        // Image border radius.
        $this->add_control(
                'list_image_border_radius', [
            'label' => __('Border Radius', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .post-list-inner .post-list-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'list_style_image_margin', [
            'label' => __('Margin', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .post-list-inner .post-list-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Style > Title.
     */
    private function wpcap_style_title_options() {
        // Tab.
        $this->start_controls_section(
            'section_list_title_style', [
                'label' => __('Title', 'post-list-elementor-addon'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Title typography.
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
                    'name' => 'list_title_style_typography',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post .title, {{WRAPPER}} .wpcap-list-container .wpcap-post .title > a',
                ]
        );

        // Title color.
        $this->add_control(
                'list_title_style_color', [
                    'type' => Controls_Manager::COLOR,
                    'label' => __('Color', 'post-list-elementor-addon'),
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_1,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wpcap-list-container .wpcap-post .title, {{WRAPPER}} .wpcap-list-container .wpcap-post .title > a' => 'color: {{VALUE}};',
                    ],
                ]
        );

        // Title margin.
        $this->add_responsive_control(
                'list_title_style_margin', [
            'label' => __('Margin', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .title, {{WRAPPER}} .wpcap-list-container .wpcap-post .title > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Style > Meta.
     */
    private function wpcap_style_meta_options() {
        // Tab.
        $this->start_controls_section(
                'section_list_meta_style', [
            'label' => __('Meta', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        // Meta typography.
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'list_meta_style_typography',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-meta span',
                ]
        );

        // Meta color.
        $this->add_control(
                'list_meta_style_color', [
            'type' => Controls_Manager::COLOR,
            'label' => __('Color', 'post-list-elementor-addon'),
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-meta span' => 'color: {{VALUE}};',
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-meta span a' => 'color: {{VALUE}};',
            ],
                ]
        );

        // Meta margin.
        $this->add_responsive_control(
                'list_meta_style_margin', [
            'label' => __('Margin', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Style > Content.
     */
    private function wpcap_style_content_options() {
        // Tab.
        $this->start_controls_section(
                'section_list_content_style', [
            'label' => __('Content', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        // Content typography.
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'list_content_style_typography',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-excerpt p',
                ]
        );

        // Content color.
        $this->add_control(
                'list_content_style_color', [
            'type' => Controls_Manager::COLOR,
            'label' => __('Color', 'post-list-elementor-addon'),
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-excerpt p' => 'color: {{VALUE}};',
            ],
                ]
        );

        // Content margin
        $this->add_responsive_control(
                'list_content_style_margin', [
            'label' => __('Margin', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post .post-list-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
    }

    /**
     * Style > Readmore.
     */
    private function wpcap_style_readmore_options() {
        // Tab.
        $this->start_controls_section(
                'section_list_readmore_style', [
            'label' => __('Read More', 'post-list-elementor-addon'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        // Readmore typography.
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'list_readmore_style_typography',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            'selector' => '{{WRAPPER}} .wpcap-list-container .wpcap-post a.read-more-btn',
                ]
        );

        // Readmore color.
        $this->add_control(
                'list_readmore_style_color', [
            'type' => Controls_Manager::COLOR,
            'label' => __('Color', 'post-list-elementor-addon'),
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post a.read-more-btn' => 'color: {{VALUE}};',
            ],
                ]
        );

        // Readmore margin
        $this->add_responsive_control(
                'list_readmore_style_margin', [
            'label' => __('Margin', 'post-list-elementor-addon'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .wpcap-list-container .wpcap-post a.read-more-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
    }

    protected function render($instance = []) {

        // Get settings.
        $settings = $this->get_active_settings();
        $posts_per_page = (!empty($settings['posts_per_page']) ? $settings['posts_per_page'] : 3 );
        //$cats = is_array($settings['post_categories']) ? implode(',', $settings['post_categories']) : $settings['post_categories'];

        $post_args = $this->cpt_get_post_settings( $settings );
        $query_args = $this->cpt_get_query_args( 'cpt_posts', $this->get_settings() );
        $query_args = array_merge( $query_args, $post_args );
        // Order by.
        if (!empty($settings['orderby'])) {
            $query_args['orderby'] = $settings['orderby'];
        }

        // Order .
        if (!empty($settings['order'])) {
            $query_args['order'] = $settings['order'];
        }
        ?>
        <div class="wpcap-list">
            <?php
            $columns_desktop = (!empty($settings['columns']) ? 'wpcap-list-desktop-' . $settings['columns'] : 'wpcap-list-desktop-3' );

            $columns_tablet = (!empty($settings['columns_tablet']) ? ' wpcap-list-tablet-' . $settings['columns_tablet'] : ' wpcap-list-tablet-2' );

            $post_type = !empty($settings['post_type']) ? $settings['post_type'] : 'post';

            $columns_mobile = (!empty($settings['columns_mobile']) ? ' wpcap-list-mobile-' . $settings['columns_mobile'] : ' wpcap-list-mobile-1' );

            $list_style = $settings['list_style'];

            $list_class = '';
            ?>
            <div class="display_filter_data wpcap-list-container elementor-list">

            <?php
            $all_posts = new \WP_Query($query_args);

            if ($all_posts->have_posts()) :

                if ('classic' == $list_style) {

                    include( __DIR__ . '/layouts/classic-list.php' );
                } elseif ('modern' == $list_style) {

                    include( __DIR__ . '/layouts/modern-list.php' );
                } else {

                    include( __DIR__ . '/layouts/classic-list.php' );
                }

            endif;
            ?>
            </div>			      						               
        </div>
    <?php
    }

    public function wpcap_filter_excerpt_length($length) {

        $settings = $this->get_settings();

        $excerpt_length = (!empty($settings['excerpt_length']) ) ? absint($settings['excerpt_length']) : 25;

        return absint($excerpt_length);
    }

    public function wpcap_filter_excerpt_more($more) {
        return '&hellip;';
    }
    
}
    