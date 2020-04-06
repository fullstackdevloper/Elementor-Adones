<?php

namespace CPT_Addons_Elementor\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size as Group_Control_Image_Size;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Utils as Utils;

trait Helper
{
    /**
     * For All Settings Key Need To Display
     *
     */
    public $post_args = array(
        // content ticker
        'cpt_ticker_type',
        'cpt_ticker_custom_contents',

        // post grid
        'cpt_post_grid_columns',

        // common
        'meta_position',
        'cpt_show_meta',
        'image_size',
        'cpt_show_image',
        'cpt_show_title',
        'cpt_show_excerpt',
        'cpt_excerpt_length',
        'cpt_show_read_more',
        'cpt_read_more_text',
        'show_load_more',
        'show_load_more_text',
        'cpt_post_grid_bg_hover_icon',

        // query_args
        'post_type',
        'post__in',
        'posts_per_page',
        'post_style',
        'tax_query',
        'post__not_in',
        'cpt_post_authors',
        'eaeposts_authors',
        'offset',
        'orderby',
        'order',
        'cpt_post_grid_hover_animation',
    );

    /**
     * Query Controls
     *
     */
    protected function cpt_query_controls()
    {
        if ('eael-content-ticker' === $this->get_name()) {
            $this->start_controls_section(
                'cpt_section_content_ticker_filters',
                [
                    'label' => __('Dynamic Content Settings', 'essential-addons-elementor'),
                    'condition' => [
                        'cpt_ticker_type' => 'dynamic',
                    ],
                ]
            );
        }

        if ('eael-content-timeline' === $this->get_name()) {
            $this->start_controls_section(
                'cpt_section_timeline__filters',
                [
                    'label' => __('Dynamic Content Settings', 'essential-addons-elementor'),
                    'condition' => [
                        'cpt_content_timeline_choose' => 'dynamic',
                    ],
                ]
            );
        }

        if ('eael-content-timeline' !== $this->get_name() && 'eael-content-ticker' !== $this->get_name()) {
            $this->start_controls_section(
                'cpt_section_post__filters',
                [
                    'label' => __('Query', 'essential-addons-elementor'),
                ]
            );
        }

        $this->add_group_control(
            'eaeposts',
            [
                'name' => 'eaeposts',
            ]
        );

        $this->add_control(
            'post__not_in',
            [
                'label' => __('Exclude', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->cpt_get_all_types_post(),
                'label_block' => true,
                'post_type' => '',
                'multiple' => true,
                'condition' => [
                    'eaeposts_post_type!' => 'by_id',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '4',
            ]
        );

        $this->add_control(
            'offset',
            [
                'label' => __('Offset', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '0',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->cpt_get_post_orderby_options(),
                'default' => 'date',

            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],
                'default' => 'desc',

            ]
        );

        $this->end_controls_section();
    }

    /**
     * Layout Controls For Post Block
     *
     */
    protected function cpt_layout_controls()
    {
        $this->start_controls_section(
            'cpt_section_post_timeline_layout',
            [
                'label' => __('Layout Settings', 'essential-addons-elementor'),
            ]
        );

        if ('eael-post-grid' === $this->get_name()) {
            $this->add_control(
                'cpt_post_grid_columns',
                [
                    'label' => esc_html__('Number of Columns', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'eael-col-4',
                    'options' => [
                        'eael-col-1' => esc_html__('Single Column', 'essential-addons-elementor'),
                        'eael-col-2' => esc_html__('Two Columns', 'essential-addons-elementor'),
                        'eael-col-3' => esc_html__('Three Columns', 'essential-addons-elementor'),
                        'eael-col-4' => esc_html__('Four Columns', 'essential-addons-elementor'),
                        'eael-col-5' => esc_html__('Five Columns', 'essential-addons-elementor'),
                        'eael-col-6' => esc_html__('Six Columns', 'essential-addons-elementor'),
                    ],
                ]
            );
        }

        if ('eael-post-block' === $this->get_name()) {
            $this->add_control(
                'grid_style',
                [
                    'label' => esc_html__('Post Block Style Preset', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'post-block-style-default',
                    'options' => [
                        'post-block-style-default' => esc_html__('Default', 'essential-addons-elementor'),
                        'post-block-style-overlay' => esc_html__('Overlay', 'essential-addons-elementor'),
                    ],
                ]
            );
        }

        if ('eael-post-carousel' !== $this->get_name()) {

            /**
             * Show Read More
             * @uses ContentTimeLine Elements - EAE
             */
            if ('eael-content-timeline' === $this->get_name()) {

                $this->add_control(
                    'cpt_show_read_more',
                    [
                        'label' => __('Show Read More', 'essential-addons-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            '1' => [
                                'title' => __('Yes', 'essential-addons-elementor'),
                                'icon' => 'fa fa-check',
                            ],
                            '0' => [
                                'title' => __('No', 'essential-addons-elementor'),
                                'icon' => 'fa fa-ban',
                            ],
                        ],
                        'default' => '1',
                        'condition' => [
                            'cpt_content_timeline_choose' => 'dynamic',
                        ],
                    ]
                );

                $this->add_control(
                    'cpt_read_more_text',
                    [
                        'label' => esc_html__('Label Text', 'essential-addons-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => false,
                        'default' => esc_html__('Read More', 'essential-addons-elementor'),
                        'condition' => [
                            'cpt_content_timeline_choose' => 'dynamic',
                            'cpt_show_read_more' => '1',
                        ],
                    ]
                );

            } else {

                $this->add_control(
                    'show_load_more',
                    [
                        'label' => __('Show Load More', 'essential-addons-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            '1' => [
                                'title' => __('Yes', 'essential-addons-elementor'),
                                'icon' => 'fa fa-check',
                            ],
                            '0' => [
                                'title' => __('No', 'essential-addons-elementor'),
                                'icon' => 'fa fa-ban',
                            ],
                        ],
                        'default' => '0',
                    ]
                );

                $this->add_control(
                    'show_load_more_text',
                    [
                        'label' => esc_html__('Label Text', 'essential-addons-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => false,
                        'default' => esc_html__('Load More', 'essential-addons-elementor'),
                        'condition' => [
                            'show_load_more' => '1',
                        ],
                    ]
                );
            }

        }

        if ('eael-content-timeline' !== $this->get_name()) {
            $this->add_control(
                'cpt_show_image',
                [
                    'label' => __('Show Image', 'essential-addons-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        '1' => [
                            'title' => __('Yes', 'essential-addons-elementor'),
                            'icon' => 'fa fa-check',
                        ],
                        '0' => [
                            'title' => __('No', 'essential-addons-elementor'),
                            'icon' => 'fa fa-ban',
                        ],
                    ],
                    'default' => '1',
                ]
            );
            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'image',
                    'exclude' => ['custom'],
                    'default' => 'medium',
                    'condition' => [
                        'cpt_show_image' => '1',
                    ],
                ]
            );

        }

        if ('eael-content-timeline' === $this->get_name()) {

            $this->add_control(
                'cpt_show_image_or_icon',
                [
                    'label' => __('Show Circle Image / Icon', 'essential-addons-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'img' => [
                            'title' => __('Image', 'essential-addons-elementor'),
                            'icon' => 'fa fa-picture-o',
                        ],
                        'icon' => [
                            'title' => __('Icon', 'essential-addons-elementor'),
                            'icon' => 'fa fa-info',
                        ],
                        'bullet' => [
                            'title' => __('Bullet', 'essential-addons-elementor'),
                            'icon' => 'fa fa-circle',
                        ],
                    ],
                    'default' => 'icon',
                    'condition' => [
                        'cpt_content_timeline_choose' => 'dynamic',
                    ],
                ]
            );

            $this->add_control(
                'cpt_icon_image',
                [
                    'label' => esc_html__('Icon Image', 'essential-addons-elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'condition' => [
                        'cpt_show_image_or_icon' => 'img',
                    ],
                ]
            );
            $this->add_control(
                'cpt_icon_image_size',
                [
                    'label' => esc_html__('Icon Image Size', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 24,
                    ],
                    'range' => [
                        'px' => [
                            'max' => 60,
                        ],
                    ],
                    'condition' => [
                        'cpt_show_image_or_icon' => 'img',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eael-content-timeline-img img' => 'width: {{SIZE}}px;',
                    ],
                ]
            );

            $this->add_control(
                'cpt_content_timeline_circle_icon',
                [
                    'label' => esc_html__('Icon', 'essential-addons-elementor'),
                    'type' => Controls_Manager::ICON,
                    'default' => 'fa fa-pencil',
                    'condition' => [
                        'cpt_content_timeline_choose' => 'dynamic',
                        'cpt_show_image_or_icon' => 'icon',
                    ],
                ]
            );

        }

        $this->add_control(
            'cpt_show_title',
            [
                'label' => __('Show Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', 'essential-addons-elementor'),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', 'essential-addons-elementor'),
                        'icon' => 'fa fa-ban',
                    ],
                ],
                'default' => '1',
            ]
        );

        $this->add_control(
            'cpt_show_excerpt',
            [
                'label' => __('Show excerpt', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', 'essential-addons-elementor'),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', 'essential-addons-elementor'),
                        'icon' => 'fa fa-ban',
                    ],
                ],
                'default' => '1',
            ]
        );

        $this->add_control(
            'cpt_excerpt_length',
            [
                'label' => __('Excerpt Words', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '10',
                'condition' => [
                    'cpt_show_excerpt' => '1',
                ],
                'description' => '<span class="pro-feature"> Pro Feature. Get <a href="https://essential-addons.com/elementor/buy.php" target="_blank">Pro version</a> </span>',
            ]
        );

        if ('eael-post-grid' === $this->get_name() || 'eael-post-block' === $this->get_name() || 'eael-post-carousel' === $this->get_name()) {

            $this->add_control(
                'cpt_show_meta',
                [
                    'label' => __('Show Meta', 'essential-addons-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        '1' => [
                            'title' => __('Yes', 'essential-addons-elementor'),
                            'icon' => 'fa fa-check',
                        ],
                        '0' => [
                            'title' => __('No', 'essential-addons-elementor'),
                            'icon' => 'fa fa-ban',
                        ],
                    ],
                    'default' => '1',
                ]
            );

            $this->add_control(
                'meta_position',
                [
                    'label' => esc_html__('Meta Position', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'meta-entry-footer',
                    'options' => [
                        'meta-entry-header' => esc_html__('Entry Header', 'essential-addons-elementor'),
                        'meta-entry-footer' => esc_html__('Entry Footer', 'essential-addons-elementor'),
                    ],
                    'condition' => [
                        'cpt_show_meta' => '1',
                    ],
                ]
            );

        }

        $this->end_controls_section();
    }

    /**
     * Load More Button Style
     *
     */
    protected function cpt_load_more_button_style()
    {
        $this->start_controls_section(
            'cpt_section_load_more_btn',
            [
                'label' => __('Load More Button Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_load_more' => '1',
                ],
            ]
        );

        $this->add_responsive_control(
            'cpt_post_grid_load_more_btn_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cpt_post_grid_load_more_btn_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cpt_post_grid_load_more_btn_typography',
                'selector' => '{{WRAPPER}} .eael-load-more-button',
            ]
        );

        $this->start_controls_tabs('cpt_post_grid_load_more_btn_tabs');

        // Normal State Tab
        $this->start_controls_tab('cpt_post_grid_load_more_btn_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $this->add_control(
            'cpt_post_grid_load_more_btn_normal_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'cpt_cta_btn_normal_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#29d8d8',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'cpt_post_grid_load_more_btn_normal_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-load-more-button',
            ]
        );

        $this->add_control(
            'cpt_post_grid_load_more_btn_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'cpt_post_grid_load_more_btn_shadow',
                'selector' => '{{WRAPPER}} .eael-load-more-button',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab('cpt_post_grid_load_more_btn_hover', ['label' => esc_html__('Hover', 'essential-addons-elementor')]);

        $this->add_control(
            'cpt_post_grid_load_more_btn_hover_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'cpt_post_grid_load_more_btn_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#27bdbd',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'cpt_post_grid_load_more_btn_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]

        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'cpt_post_grid_load_more_btn_hover_shadow',
                'selector' => '{{WRAPPER}} .eael-load-more-button:hover',
                'separator' => 'before',
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'cpt_post_grid_loadmore_button_alignment',
            [
                'label' => __('Button Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button-wrap' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Go Premium
     *
     */
    protected function cpt_go_premium()
    {
        $this->start_controls_section(
            'cpt_section_pro',
            [
                'label' => __('Go Premium for More Features', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'cpt_control_get_pro',
            [
                'label' => __('Unlock more possibilities', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('', 'essential-addons-elementor'),
                        'icon' => 'fa fa-unlock-alt',
                    ],
                ],
                'default' => '1',
                'description' => '<span class="pro-feature"> Get the  <a href="http://essential-addons.com/elementor/#pricing" target="_blank">Pro version</a> for more stunning elements and customization options.</span>',
            ]
        );

        $this->end_controls_section();
    }
    
    public function get_post_taxonomy($control_id, $settings) {
        
        $post_type = $settings[$control_id . '_post_type'];
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        foreach ($taxonomies as $object) {
            if($object->labels->singular_name == 'Category') {
                return $object->name;
            }
        }
    }
    public function cpt_get_query_args($control_id, $settings)
    {
        $defaults = [
            $control_id . '_post_type' => 'post',
            $control_id . '_posts_ids' => [],
            'orderby' => 'date',
            'order' => 'desc',
            'posts_per_page' => 3,
            'offset' => 0,
        ];

        $settings = wp_parse_args($settings, $defaults);

        $post_type = $settings[$control_id . '_post_type'];

        $query_args = [
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish', // Hide drafts/private posts for admins
        ];

        if ('by_id' === $post_type) {
            $query_args['post_type'] = 'any';
            $query_args['post__in'] = $settings[$control_id . '_posts_ids'];

            if (empty($query_args['post__in'])) {
                // If no selection - return an empty query
                $query_args['post__in'] = [0];
            }
        } else {
            $query_args['post_type'] = $post_type;
            $query_args['posts_per_page'] = $settings['posts_per_page'];
            $query_args['tax_query'] = [];

            $query_args['offset'] = $settings['offset'];

            $taxonomies = get_object_taxonomies($post_type, 'objects');

            foreach ($taxonomies as $object) {
                $setting_key = $control_id . '_' . $object->name . '_ids';

                if (!empty($settings[$setting_key])) {
                    $query_args['tax_query'][] = [
                        'taxonomy' => $object->name,
                        'field' => 'term_id',
                        'terms' => $settings[$setting_key],
                    ];
                }
            }
        }

        if (!empty($settings[$control_id . '_authors'])) {
            $query_args['author__in'] = $settings[$control_id . '_authors'];
        }

        $post__not_in = [];
        if (!empty($settings['post__not_in'])) {
            $post__not_in = array_merge($post__not_in, $settings['post__not_in']);
            $query_args['post__not_in'] = $post__not_in;
        }

        if (isset($query_args['tax_query']) && count($query_args['tax_query']) > 1) {
            $query_args['tax_query']['relation'] = 'OR';
        }

        return $query_args;
    }
    public function render_title($settings) {

        $show_title = $settings['show_title'];

        if ('yes' !== $show_title) {
            return;
        }

        $title_tag = $settings['title_tag'];
        ?>
            <<?php echo $title_tag; ?> class="title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </<?php echo $title_tag; ?>>
        <?php
    }
    protected function render_thumbnail($settings) {

        $show_image = $settings['show_image'];

        if ('yes' !== $show_image) {
            return;
        }

        $post_thumbnail_size = $settings['post_thumbnail_size'];

        if (has_post_thumbnail()) :
        ?>
        <div class="post-grid-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail($post_thumbnail_size); ?>
            </a>
        </div>
        <?php
        endif;
    }
    
    protected function render_meta($settings) {

        $meta_data = $settings['meta_data'];

        if (empty($meta_data)) {
            return;
        }
        ?>
        <div class="post-grid-meta">
        <?php if (in_array('author', $meta_data)) { ?>

                <span class="post-author"><?php the_author(); ?></span>

            <?php
        }

        if (in_array('date', $meta_data)) {
            ?>

                <span class="post-author"><?php echo apply_filters('the_date', get_the_date(), get_option('date_format'), '', ''); ?></span>

            <?php
        }

        if (in_array('categories', $meta_data)) {

            $categories_list = get_the_category_list(esc_html__(', ', 'post-grid-elementor-addon'));

            if ($categories_list) {
                printf('<span class="post-categories">%s</span>', $categories_list); // WPCS: XSS OK.
            }
        }

        if (in_array('comments', $meta_data)) {
            ?>

                <span class="post-comments"><?php comments_number(); ?></span>

            <?php
        }
        ?>
        </div>
        <?php
    }
    
    protected function render_categories($settings) {
        $display_cats = $settings['display_categories'];
        if ('yes' !== $display_cats) {
            return;
        }
        $posttype = str_replace('cpt_', '', $settings['cpt_posts_post_type']);
        $taxonomy = "{$posttype}_categories";
        $cats = get_terms($taxonomy);
        if ( is_wp_error( $cats ) ) {
            return ;
        }
        foreach ( $cats as $cat ):
            echo '<span class="cpt_cat">'.$cat->name.'</span>';
        endforeach;
    }
    protected function render_excerpt($settings) {

        $show_excerpt = $settings['show_excerpt'];

        if ('yes' !== $show_excerpt) {
            return;
        }

        add_filter('excerpt_more', [$this, 'wpcap_filter_excerpt_more'], 20);
        add_filter('excerpt_length', [$this, 'wpcap_filter_excerpt_length'], 9999);
        ?>
        <div class="post-grid-excerpt">
        <?php the_excerpt(); ?>
        </div>
        <?php
        remove_filter('excerpt_length', [$this, 'wpcap_filter_excerpt_length'], 9999);
        remove_filter('excerpt_more', [$this, 'wpcap_filter_excerpt_more'], 20);
    }

    protected function render_readmore($settings) {


        $show_read_more = $settings['show_read_more'];
        $read_more_text = $settings['read_more_text'];

        if ('yes' !== $show_read_more) {
            return;
        }
        ?>
        <a class="read-more-btn" href="<?php the_permalink(); ?>"><?php echo esc_html($read_more_text); ?></a>
        <?php
    }
    /**
     * Get All POst Types
     * @return array
     */
    public function cpt_get_post_types()
    {
        $cpt_cpts = get_post_types(array('public' => true, 'show_in_nav_menus' => true), 'object');
        $cpt_exclude_cpts = array('elementor_library', 'attachment');

        foreach ($cpt_exclude_cpts as $exclude_cpt) {
            unset($cpt_cpts[$exclude_cpt]);
        }
        $post_types = array_merge($cpt_cpts);
        foreach ($post_types as $type) {
            $types[$type->name] = $type->label;
        }

        return $types;
    }

    /**
     * Get all types of post.
     * @return array
     */
    public function cpt_get_all_types_post()
    {
        $posts_args = array(
            'post_type' => 'any',
            'post_style' => 'all_types',
            'post_status' => 'publish',
            'posts_per_page' => '-1',
        );
        $posts = $this->cpt_load_more_ajax($posts_args);

        $post_list = [];

        foreach ($posts as $post) {
            $post_list[$post->ID] = $post->post_title;
        }

        return $post_list;
    }

    /**
     * Post Settings Parameter
     * @param  array $settings
     * @return array
     */
    public function cpt_get_post_settings($settings)
    {
        foreach ($settings as $key => $value) {
            if (in_array($key, $this->post_args)) {
                $post_args[$key] = $value;
            }
        }

        $post_args['post_style'] = isset($post_args['post_style']) ? $post_args['post_style'] : 'grid';
        $post_args['post_status'] = 'publish';

        return $post_args;
    }

    /**
     * Getting Excerpts By Post Id
     * @param  int $post_id
     * @param  int $excerpt_length
     * @return string
     */
    public function cpt_get_excerpt_by_id($post_id, $excerpt_length)
    {
        $the_post = get_post($post_id); //Gets post ID

        $the_excerpt = null;
        if ($the_post) {
            $the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
        }

        $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
        $words = explode(' ', $the_excerpt, $excerpt_length + 1);

        if (count($words) > $excerpt_length):
            array_pop($words);
            array_push($words, '…');
            $the_excerpt = implode(' ', $words);
        endif;

        return $the_excerpt;
    }

    /**
     * Get Post Thumbnail Size
     *
     * @return array
     */
    public function cpt_get_thumbnail_sizes()
    {
        $sizes = get_intermediate_image_sizes();
        foreach ($sizes as $s) {
            $ret[$s] = $s;
        }

        return $ret;
    }

    /**
     * POst Orderby Options
     *
     * @return array
     */
    public function cpt_get_post_orderby_options()
    {
        $orderby = array(
            'ID' => 'Post ID',
            'author' => 'Post Author',
            'title' => 'Title',
            'date' => 'Date',
            'modified' => 'Last Modified Date',
            'parent' => 'Parent Id',
            'rand' => 'Random',
            'comment_count' => 'Comment Count',
            'menu_order' => 'Menu Order',
        );

        return $orderby;
    }

    /**
     * Get Post Categories
     *
     * @return array
     */
    public function cpt_post_type_categories()
    {
        $terms = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => true,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }

        return $options;
    }

    /**
     * WooCommerce Product Query
     *
     * @return array
     */
    public function cpt_woocommerce_product_categories()
    {
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->slug] = $term->name;
            }
            return $options;
        }
    }

    /**
     * WooCommerce Get Product By Id
     *
     * @return array
     */
    public function cpt_woocommerce_product_get_product_by_id()
    {
        $postlist = get_posts(array(
            'post_type' => 'product',
            'showposts' => 9999,
        ));
        $options = array();

        if (!empty($postlist) && !is_wp_error($postlist)) {
            foreach ($postlist as $post) {
                $options[$post->ID] = $post->post_title;
            }
            return $options;

        }
    }

    /**
     * WooCommerce Get Product Category By Id
     *
     * @return array
     */
    public function cpt_woocommerce_product_categories_by_id()
    {
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
            return $options;
        }

    }

    /**
     * Get Contact Form 7 [ if exists ]
     */
    public function cpt_select_contact_form()
    {
        $options = array();

        if (function_exists('wpcf7')) {
            $wpcf7_form_list = get_posts(array(
                'post_type' => 'wpcf7_contact_form',
                'showposts' => 999,
            ));
            $options[0] = esc_html__('Select a Contact Form', 'essential-addons-elementor');
            if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
                foreach ($wpcf7_form_list as $post) {
                    $options[$post->ID] = $post->post_title;
                }
            } else {
                $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
            }
        }
        return $options;
    }

    /**
     * Get Gravity Form [ if exists ]
     *
     * @return array
     */
    public function cpt_select_gravity_form()
    {
        $options = array();

        if (class_exists('GFCommon')) {
            $gravity_forms = \RGFormsModel::get_forms(null, 'title');

            if (!empty($gravity_forms) && !is_wp_error($gravity_forms)) {

                $options[0] = esc_html__('Select Gravity Form', 'essential-addons-elementor');
                foreach ($gravity_forms as $form) {
                    $options[$form->id] = $form->title;
                }

            } else {
                $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
            }
        }

        return $options;
    }

    /**
     * Get WeForms Form List
     *
     * @return array
     */
    public function cpt_select_weform()
    {
        $wpuf_form_list = get_posts(array(
            'post_type' => 'wpuf_contact_form',
            'showposts' => 999,
        ));

        $options = array();

        if (!empty($wpuf_form_list) && !is_wp_error($wpuf_form_list)) {
            $options[0] = esc_html__('Select weForm', 'essential-addons-elementor');
            foreach ($wpuf_form_list as $post) {
                $options[$post->ID] = $post->post_title;
            }
        } else {
            $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
        }

        return $options;
    }

    /**
     * Get Ninja Form List
     *
     * @return array
     */
    public function cpt_select_ninja_form()
    {
        $options = array();

        if (class_exists('Ninja_Forms')) {
            $contact_forms = Ninja_Forms()->form()->get_forms();

            if (!empty($contact_forms) && !is_wp_error($contact_forms)) {

                $options[0] = esc_html__('Select Ninja Form', 'essential-addons-elementor');

                foreach ($contact_forms as $form) {
                    $options[$form->get_id()] = $form->get_setting('title');
                }
            }
        } else {
            $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
        }

        return $options;
    }

    /**
     * Get Caldera Form List
     *
     * @return array
     */
    public function cpt_select_caldera_form()
    {
        $options = array();

        if (class_exists('Caldera_Forms')) {
            $contact_forms = \Caldera_Forms_Forms::get_forms(true, true);

            if (!empty($contact_forms) && !is_wp_error($contact_forms)) {
                $options[0] = esc_html__('Select Caldera Form', 'essential-addons-elementor');
                foreach ($contact_forms as $form) {
                    $options[$form['ID']] = $form['name'];
                }
            }
        } else {
            $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
        }

        return $options;
    }

    /**
     * Get WPForms List
     *
     * @return array
     */
    public function cpt_select_wpforms_forms()
    {
        $options = array();

        if (class_exists('\WPForms\WPForms')) {
            $args = array(
                'post_type' => 'wpforms',
                'posts_per_page' => -1,
            );

            $contact_forms = get_posts($args);

            if (!empty($contact_forms) && !is_wp_error($contact_forms)) {
                $options[0] = esc_html__('Select a WPForm', 'essential-addons-elementor');
                foreach ($contact_forms as $post) {
                    $options[$post->ID] = $post->post_title;
                }
            }
        } else {
            $options[0] = esc_html__('Create a Form First', 'essential-addons-elementor');
        }

        return $options;
    }

    /**
     * Get all elementor page templates
     *
     * @return array
     */
    public function cpt_get_page_templates()
    {
        $page_templates = get_posts(array(
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
        ));

        $options = array();

        if (!empty($page_templates) && !is_wp_error($page_templates)) {
            foreach ($page_templates as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        return $options;
    }

    /**
     * Get all Authors
     *
     * @return array
     */
    public function cpt_get_authors()
    {
        $options = array();
        $users = get_users();

        if ($users) {
            foreach ($users as $user) {
                $options[$user->ID] = $user->display_name;
            }
        }

        return $options;
    }

    /**
     * Get all Tags
     *
     * @return array
     */
    public function cpt_get_tags()
    {
        $options = array();
        $tags = get_tags();

        foreach ($tags as $tag) {
            $options[$tag->term_id] = $tag->name;
        }

        return $options;
    }

    /**
     * Get all Posts
     *
     * @return array
     */
    public function cpt_get_posts()
    {
        $post_list = get_posts(array(
            'post_type' => 'post',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        ));

        $posts = array();

        if (!empty($post_list) && !is_wp_error($post_list)) {
            foreach ($post_list as $post) {
                $posts[$post->ID] = $post->post_title;
            }
        }

        return $posts;
    }

    /**
     * Get all Pages
     *
     * @return array
     */
    public function cpt_get_pages()
    {
        $page_list = get_posts(array(
            'post_type' => 'page',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        ));

        $pages = array();

        if (!empty($page_list) && !is_wp_error($page_list)) {
            foreach ($page_list as $page) {
                $pages[$page->ID] = $page->post_title;
            }
        }

        return $pages;
    }

    /**
     * This function is responsible for get the post data.
     * It will return HTML markup with AJAX call and with normal call.
     *
     * @return string of an html markup with AJAX call.
     * @return array of content and found posts count without AJAX call.
     */
    public function cpt_load_more_ajax()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'load_more') {
            $post_args = $this->cpt_get_post_settings($_POST);
            $post_args = array_merge($this->cpt_get_query_args('eaeposts', $_POST), $post_args);

            if (isset($_POST['tax_query']) && count($_POST['tax_query']) > 1) {
                $post_args['tax_query']['relation'] = 'OR';
            }
        } else {
            $args = func_get_args();
            $post_args = $args[0];
        }

        $posts = new \WP_Query($post_args);

        /**
         * For returning all types of post as an array
         * @return array;
         */
        if (isset($post_args['post_style']) && $post_args['post_style'] == 'all_types') {
            return $posts->posts;
        }

        $return = array();
        $return['count'] = $posts->found_posts;

        ob_start();

        while ($posts->have_posts()): $posts->the_post();
            include cpt_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/content/' . @$post_args['post_style'] . '.php';
        endwhile;

        $return['content'] = ob_get_clean();

        wp_reset_postdata();
        wp_reset_query();

        if (isset($_POST['action']) && $_POST['action'] == 'load_more') {
            wp_send_json($return['content']);
        } else {
            return $return;
        }
    }

}
