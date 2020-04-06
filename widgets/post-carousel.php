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

class Elementor_Post_Carousel_Widget extends Widget_Base {
    use \CPT_Addons_Elementor\Traits\Helper;
    
    public function get_name() {
        return 'elementor-carousel-posts';
    }

    public function get_title() {
        return __('Post Carousel', 'post-carousel-elementor-addon');
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return ['wpcap-items'];
    }
    
    /**
    * Retrieve the list of scripts the image carousel widget depended on.
    *
    * Used to set scripts dependencies required to run the widget.
    *
    * @since 1.3.0
    * @access public
    *
    * @return array Widget scripts dependencies.
    */
    public function get_script_depends() {
            return [ 'jquery-slick' ];
    }
        
    private function wpcap_get_all_post_categories($control, $post_type) {
        //$settings = $control->get_settings();
        $options = array();

        $taxonomy = str_replace("cpt_", "" ,"cpt_credit_categories");
        //$taxonomy = 'credit_categories';
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
                        if (isset($term->term_id) && isset($term->name)) {
                            $options[$term->term_id] = $term->name;
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

        $this->wpcap_style_navigation_options();
        $this->wpcap_slide_options();
    }

    /**
     * Content Layout Options.
     */
    private function wpcap_content_layout_options() {

        $this->start_controls_section(
            'section_layout', [
                'label' => esc_html__('Layout', 'post-carousel-elementor-addon'),
            ]
        );

        $this->add_control(
            'show_image', [
                'label' => __('Image', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-carousel-elementor-addon'),
                'label_off' => __('Hide', 'post-carousel-elementor-addon'),
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
                'label' => __('Title', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-carousel-elementor-addon'),
                'label_off' => __('Hide', 'post-carousel-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_tag', [
                'label' => __('Title HTML Tag', 'post-carousel-elementor-addon'),
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
            'show_excerpt', [
                'label' => __('Excerpt', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-carousel-elementor-addon'),
                'label_off' => __('Hide', 'post-carousel-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'excerpt_length', [
                'label' => __('Excerpt Length', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'meta_data',
            [
                    'label' => __( 'Meta Data', 'post-carousel-elementor-addon' ),
                    'label_block' => true,
                    'type' => Controls_Manager::SELECT2,
                    'default' => [ 'date', 'comments' ],
                    'multiple' => true,
                    'options' => [
                            'author' => __( 'Author', 'post-carousel-elementor-addon' ),
                            'date' => __( 'Date', 'post-carousel-elementor-addon' ),
                            'categories' => __( 'Categories', 'post-carousel-elementor-addon' ),
                            'comments' => __( 'Comments', 'post-carousel-elementor-addon' ),
                    ],
                    'separator' => 'before',
            ]
        );
        $this->add_control(
            'show_read_more', [
                'label' => __('Read More', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'post-carousel-elementor-addon'),
                'label_off' => __('Hide', 'post-carousel-elementor-addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'read_more_text', [
                'label' => __('Read More Text', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More »', 'post-carousel-elementor-addon'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );
        $slides_to_show = range( 1, 10 );
        $slides_to_show = array_combine( $slides_to_show, $slides_to_show );

        $this->add_responsive_control(
                'slides_to_show',
                [
                        'label' => __( 'Slides to Show', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                                '' => __( 'Default', 'elementor' ),
                        ] + $slides_to_show,
                        'frontend_available' => true,
                ]
        );

        $this->add_responsive_control(
                'slides_to_scroll',
                [
                        'label' => __( 'Slides to Scroll', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'description' => __( 'Set how many slides are scrolled per swipe.', 'elementor' ),
                        'options' => [
                                '' => __( 'Default', 'elementor' ),
                        ] + $slides_to_show,
                        'condition' => [
                                'slides_to_show!' => '1',
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'image_stretch',
                [
                        'label' => __( 'Image Stretch', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'no',
                        'options' => [
                                'no' => __( 'No', 'elementor' ),
                                'yes' => __( 'Yes', 'elementor' ),
                        ],
                ]
        );

        $this->add_control(
                'navigation',
                [
                        'label' => __( 'Navigation', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'both',
                        'options' => [
                                'both' => __( 'Arrows and Dots', 'elementor' ),
                                'arrows' => __( 'Arrows', 'elementor' ),
                                'dots' => __( 'Dots', 'elementor' ),
                                'none' => __( 'None', 'elementor' ),
                        ],
                        'frontend_available' => true,
                ]
        );    
        $this->end_controls_section();
        
        $this->start_controls_section(
                'section_additional_options',
                [
                        'label' => __( 'Additional Options', 'elementor' ),
                ]
        );

        $this->add_control(
                'pause_on_hover',
                [
                        'label' => __( 'Pause on Hover', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'yes',
                        'options' => [
                                'yes' => __( 'Yes', 'elementor' ),
                                'no' => __( 'No', 'elementor' ),
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'autoplay',
                [
                        'label' => __( 'Autoplay', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'yes',
                        'options' => [
                                'yes' => __( 'Yes', 'elementor' ),
                                'no' => __( 'No', 'elementor' ),
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'autoplay_speed',
                [
                        'label' => __( 'Autoplay Speed', 'elementor' ),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 5000,
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'infinite',
                [
                        'label' => __( 'Infinite Loop', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'yes',
                        'options' => [
                                'yes' => __( 'Yes', 'elementor' ),
                                'no' => __( 'No', 'elementor' ),
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'effect',
                [
                        'label' => __( 'Effect', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'slide',
                        'options' => [
                                'slide' => __( 'Slide', 'elementor' ),
                                'fade' => __( 'Fade', 'elementor' ),
                        ],
                        'condition' => [
                                'slides_to_show' => '1',
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'speed',
                [
                        'label' => __( 'Animation Speed', 'elementor' ),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 500,
                        'frontend_available' => true,
                ]
        );

        $this->add_control(
                'direction',
                [
                        'label' => __( 'Direction', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'ltr',
                        'options' => [
                                'ltr' => __( 'Left', 'elementor' ),
                                'rtl' => __( 'Right', 'elementor' ),
                        ],
                        'frontend_available' => true,
                ]
        );

        $this->end_controls_section();
    }
    
    private function _get_texonomies() {
        $taxonomies = get_taxonomies([], 'object');
        $options = [];
        foreach ($taxonomies as $key => $tax) {
            $options[$tax->name] = $tax->label;
        }
        
        return $options;
    }
    /**
     * Content Query Options.
     */
    private function wpcap_content_query_options() {

        $this->start_controls_section(
            'section_query', [
                'label' => __('Query', 'post-carousel-elementor-addon'),
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
            'posts_per_page', [
                'label' => __('Maximum Posts', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
            ]
        );
        $this->add_control(
            'advanced', [
                'label' => __('Advanced', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'orderby', [
                'label' => __('Order By', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SELECT,
                'default' => 'post_date',
                'options' => [
                    'post_date' => __('Date', 'post-carousel-elementor-addon'),
                    'post_title' => __('Title', 'post-carousel-elementor-addon'),
                    'rand' => __('Random', 'post-carousel-elementor-addon'),
                ],
            ]
        );

        $this->add_control(
            'order', [
                'label' => __('Order', 'post-carousel-elementor-addon'),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc' => __('ASC', 'post-carousel-elementor-addon'),
                    'desc' => __('DESC', 'post-carousel-elementor-addon'),
                ],
            ]
        );

        $this->end_controls_section();
    }
    
    private function wpcap_slide_options() {
        $this->start_controls_section(
            'slide_style',
            [
                'label' => __( 'Slide', 'elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
                'slide_spacing',
                [
                        'label' => __( 'Spacing', 'elementor' ),
                        'type' => Controls_Manager::NUMBER,
                        'separator' => 'before',
                        
                ]
        );
        
        $this->end_controls_section();
    }
    /**
     * Style Layout Options.
     */
    private function wpcap_style_navigation_options() {
        $this->start_controls_section(
                'section_style_navigation',
                [
                        'label' => __( 'Navigation', 'elementor' ),
                        'tab' => Controls_Manager::TAB_STYLE,
                        'condition' => [
                                'navigation' => [ 'arrows', 'dots', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'heading_style_arrows',
                [
                        'label' => __( 'Arrows', 'elementor' ),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                        'condition' => [
                                'navigation' => [ 'arrows', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'arrows_position',
                [
                        'label' => __( 'Position', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'inside',
                        'options' => [
                                'inside' => __( 'Inside', 'elementor' ),
                                'outside' => __( 'Outside', 'elementor' ),
                        ],
                        'condition' => [
                                'navigation' => [ 'arrows', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'arrows_size',
                [
                        'label' => __( 'Size', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 20,
                                        'max' => 60,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                                'navigation' => [ 'arrows', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'arrows_color',
                [
                        'label' => __( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before' => 'color: {{VALUE}};',
                        ],
                        'condition' => [
                                'navigation' => [ 'arrows', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'heading_style_dots',
                [
                        'label' => __( 'Dots', 'elementor' ),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                        'condition' => [
                                'navigation' => [ 'dots', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'dots_position',
                [
                        'label' => __( 'Position', 'elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'outside',
                        'options' => [
                                'outside' => __( 'Outside', 'elementor' ),
                                'inside' => __( 'Inside', 'elementor' ),
                        ],
                        'condition' => [
                                'navigation' => [ 'dots', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'dots_size',
                [
                        'label' => __( 'Size', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                'px' => [
                                        'min' => 5,
                                        'max' => 10,
                                ],
                        ],
                        'selectors' => [
                                '{{WRAPPER}} .elementor-image-carousel-wrapper .elementor-image-carousel .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                                'navigation' => [ 'dots', 'both' ],
                        ],
                ]
        );

        $this->add_control(
                'dots_color',
                [
                        'label' => __( 'Color', 'elementor' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                                '{{WRAPPER}} .elementor-image-carousel-wrapper .elementor-image-carousel .slick-dots li button:before' => 'color: {{VALUE}};',
                        ],
                        'condition' => [
                                'navigation' => [ 'dots', 'both' ],
                        ],
                ]
        );

        $this->end_controls_section();
        
    }

    protected function render($instance = []) {
        // Get settings.
        
        $settings = $this->get_active_settings();
        
        $carousel_settings = [
            //'arrows' => ('yes' === $settings['arrows']),
            'direction' => $settings['direction'],
            'infinite' => ('yes' === $settings['infinite']),
            'autoplay' => ('yes' === $settings['autoplay']),
            'autoplay_speed' => absint($settings['autoplay_speed']),
            'animation_speed' => absint($settings['speed']),
            'pause_on_hover' => ('yes' === $settings['pause_on_hover']),
        ];
        $responsive_settings = [
            'slides_to_show' => $settings['slides_to_show'],
            'slides_to_scroll' => $settings['slides_to_scroll'], 
            'tablet_display_columns' => $settings['slides_to_show_tablet'],
            'tablet_scroll_columns' => $settings['slides_to_scroll_tablet'],
            'mobile_display_columns' => $settings['slides_to_show_mobile'],
            'mobile_scroll_columns' => $settings['slides_to_scroll_mobile'],

        ];
        $carousel_settings = array_merge($carousel_settings, $responsive_settings);
        $this->add_render_attribute( 'carousel', 'data-settings', wp_json_encode( $carousel_settings ), true );
        $settings = $this->get_settings();
        $space = (!empty($settings['slide_spacing']) ? $settings['slide_spacing'].'px' : '6px' );
        
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
        
        $all_posts = get_posts($query_args);
        $slides = [];
        foreach($all_posts as $index => $post) {
            $image_html = '<img class="slick-slide-image" src="' . esc_attr( get_the_post_thumbnail_url($post->ID) ) . '" alt="" />';
            $posturl = get_post_permalink($post->ID);
            $link = ['url' => $posturl, 'is_external' => true, 'nofollow' => false];
            if ( $link ) {
                    $link_key = 'link_' . $index;

                    $this->add_render_attribute( $link_key, [
                            'href' => $link['url'],
                            'data-elementor-open-lightbox' => false,
                            'data-elementor-lightbox-slideshow' => $this->get_id(),
                            'data-elementor-lightbox-index' => $index,
                    ] );

                    if ( ! empty( $link['is_external'] ) ) {
                            $this->add_render_attribute( $link_key, 'target', '_blank' );
                    }

                    if ( ! empty( $link['nofollow'] ) ) {
                            $this->add_render_attribute( $link_key, 'rel', 'nofollow' );
                    }

                    $image_html = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
            }
            $slide_html = '<div class="slick-slide"><figure style="padding-left:'.$space.';" class="slick-slide-inner">' . $image_html;
            $slide_html.= $this->render_title($post->ID);
            $slide_html.= $this->render_meta($post->ID);
            $slide_html.='<div class="post-carousel-text-wrap">';
            $slide_html.=$this->render_excerpt($post->ID);
            $slide_html.=$this->render_readmore($post->ID);
            $slide_html.='</div>';
            $slide_html .= '</figure></div>';
            
            $slides[] = $slide_html;
        }
        //$this->add_render_attribute( 'carousel', 'class', 'ecp_posts' );
        $this->add_render_attribute( 'carousel', 'class', 'elementor-image-carousel panavision_post_carousel' );

        if ( 'none' !== $settings['navigation'] ) {
            if ( 'dots' !== $settings['navigation'] ) {
                    $this->add_render_attribute( 'carousel', 'class', 'slick-arrows-' . $settings['arrows_position'] );
            }

            if ( 'arrows' !== $settings['navigation'] ) {
                    $this->add_render_attribute( 'carousel', 'class', 'slick-dots-' . $settings['dots_position'] );
            }
        }

        if ( 'yes' === $settings['image_stretch'] ) {
                $this->add_render_attribute( 'carousel', 'class', 'slick-image-stretch' );
        }
        
        ?>
        
        <div class="elementor-image-carousel-wrapper elementor-slick-slider" dir="<?php echo $settings['direction']; ?>">
            <div <?php echo $this->get_render_attribute_string( 'carousel' ); ?>>
                    <?php echo implode( '', $slides ); ?>
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

    protected function render_title($postid) {

        $settings = $this->get_settings();

        $show_title = $settings['show_title'];

        if ('yes' !== $show_title) {
            return;
        }

        $title_tag = $settings['title_tag'];
        $title = get_the_title($postid);
        $link = get_the_permalink($postid);
        return "<{$title_tag} class='title'><a href='{$link}'>{$title}</a></{$title_tag}>";
    }

    protected function render_meta($postid) {

        $settings = $this->get_settings();

        $meta_data = $settings['meta_data'];

        if (empty($meta_data)) {
            return;
        }
        $html = '<div class="post-carousel-meta">';
        if (in_array('author', $meta_data)) {
            $html.= '<span class="post-author">'.get_the_author_meta( 'display_name' , $postid ).'</span>';
        }

        if (in_array('date', $meta_data)) {
            $html.= '<span class="post-author">'.apply_filters('the_date', get_the_date('',$postid), get_option('date_format'), '', '').'</span>';
        }

        if (in_array('categories', $meta_data)) {

            $categories_list = get_the_category_list(esc_html__(', ', 'post-carousel-elementor-addon'), 'single', $postid);

            if ($categories_list) {
                $html.= '<span class="post-categories">'.$categories_list.'</span>'; // WPCS: XSS OK.
            }
        }

        if (in_array('comments', $meta_data)) {
            $html.= '<span class="post-comments">'.get_comments_number($postid).'</span>';
        }
        $html.= '</div>';
            
        return $html;    
    }

    protected function render_excerpt($postid) {

        $settings = $this->get_settings();

        $show_excerpt = $settings['show_excerpt'];

        if ('yes' !== $show_excerpt) {
            return;
        }

        add_filter('excerpt_more', [$this, 'wpcap_filter_excerpt_more'], 20);
        add_filter('excerpt_length', [$this, 'wpcap_filter_excerpt_length'], 9999);
        $excerpt = '<div class="post-carousel-excerpt">'.get_the_excerpt($postid).'</div>';
        remove_filter('excerpt_length', [$this, 'wpcap_filter_excerpt_length'], 9999);
        remove_filter('excerpt_more', [$this, 'wpcap_filter_excerpt_more'], 20);
        
        return $excerpt;
    }

    protected function render_readmore($postid) {

        $settings = $this->get_settings();

        $show_read_more = $settings['show_read_more'];
        $read_more_text = $settings['read_more_text'];

        if ('yes' !== $show_read_more) {
            return;
        }
        return '<a class="read-more-btn" href="'.get_the_permalink($postid).'">'.esc_html($read_more_text).'</a>';
        }

    }
    