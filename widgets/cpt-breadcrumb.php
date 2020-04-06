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

class Elementor_Breadcrumb_Widget extends Widget_Base {
    public function get_name() {
        return 'elementor-breadcrumbs';
    }

    public function get_title() {
        return __('Breadcrumbs', 'post-grid-elementor-addon');
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs';
    }
    
    protected function render($instance = []) {
        global $post;
        $schema_on = '';
        $taxlist=  "";
        $schema_link = '';
        $schema_prop_url = '';
        $schema_prop_title = '';
        $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $delimiter = '<i class="fa fa-chevron-right" aria-hidden="true"></i>'; // delimiter between crumbs
        $home = __('<span>Home</span>', ''); // text for the 'Home' link
        $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
        $before = '<li><a class="current">'; // tag before the current crumb
        $after = '</a></li>'; // tag after the current crumb
        $schema_breadcrumb_on = get_theme_mod('schema_breadcrumb_on');
        if ($schema_breadcrumb_on == 'enable') {
            $schema_link = ' itemscope itemtype="http://data-vocabulary.org/Breadcrumb"';
            $schema_prop_url = ' itemprop="url"';
            $schema_prop_title = ' itemprop="title"';
        }
        echo '<div class="page-title-highliht">';
        $homeLink = home_url();
        if (is_home() || is_front_page()) {
            if ($showOnHome == 1) {
                echo '<ul class="breadcrumb">';
                //echo __('You are here: ', 'international-sportsman');
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $homeLink . '">' . $home . '</a></li>';
                echo '</ul>';
            }
        } else {
            echo '<ul class="breadcrumb">';
            if (!is_single()) {
                //echo __('You are here: ', 'international-sportsman');
            }
            echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $homeLink . '">' . $home . '</a>' . $delimiter . '</li> ';
            if (is_category()) {
                $thisCat = get_category(get_query_var('cat'), false);
                if ($thisCat->parent != 0) {
                    $category_link = get_category_link($thisCat->parent);
                    echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $category_link . '">' . get_cat_name($thisCat->parent) . '</a>' . $delimiter . '</li> ';
                }
                $category_id = get_cat_ID(single_cat_title('', false));
                $category_link = get_category_link($category_id);
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $category_link . '">' . single_cat_title('', false) . '</a></li>';
            } elseif (is_search()) {
                echo __('Search results for', 'international-sportsman') . ' "' . get_search_query() . '"';
            } elseif (is_day()) {
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . '</li> ';
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $delimiter . '</li> ';
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')) . '">' . get_the_time('d') . '</a></li>';
            } elseif (is_month()) {
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . ' ';
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a></li>';
            } elseif (is_year()) {
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_year_link(get_the_time('Y')) . '">'  . get_the_time('Y') . '</a>';
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $homeLink . '/' . $slug['slug'] . '">'  . $post_type->labels->singular_name . '</a></li>';
                    // get post type by post
                    $post_type = $post->post_type;
                    // get post type taxonomies
                    $taxonomies = get_object_taxonomies($post_type, 'objects');
                    
                    if ($taxonomies) {
                        foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
                            // get the terms related to post
                            $terms = get_the_terms($post->ID, $taxonomy_slug);
                            //echo "<pre>"; print_r($terms);
                            if (!empty($terms)) {
                                foreach ($terms as $term) {
                                    $taxlist .= '<li> ' . $delimiter . ' ' . '<a' . $schema_prop_url . ' href="' . get_term_link($term->slug, $taxonomy_slug) . '">' .  ucfirst($term->name) . '</a></li>';
                                }
                            }
                        }
                        if ($taxlist) {
                            echo $taxlist;
                        }
                    }
                    //echo ' ' . $delimiter . ' ' . __('You are reading &raquo;', 'international-sportsman');
                } else {
                    $category = get_the_category();
                    if ($category) {
                        foreach ($category as $cat) {
                            echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_category_link($cat->term_id) . '">' . $cat->name . '</a>' . $delimiter . '</li> ';
                        }
                    }
                    //echo __('You are reading &raquo;', 'international-sportsman');
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                if( !is_null($post_type) ):
                echo $before . $post_type->labels->singular_name . $after;
                endif;
            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                $cat = $cat[0];
                if ($cat) {
                    echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                }
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink($parent) . '">' .  $parent->post_title . '</a></li>';
                if ($showCurrent == 1)
                    echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
            }
            elseif (is_page() && !$post->post_parent) {
                if (class_exists('buddypress')) {
                    global $bp;
                    if (bp_is_groups_component()) {
                        echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . home_url() . '/' . bp_get_root_slug('groups') . '">' . bp_get_root_slug('groups') . '</a></li>';
                        if (!bp_is_directory()) {
                            echo $delimiter . '<span' . $schema_link . '><a' . $schema_prop_url . ' href="' . home_url() . '/' . bp_get_root_slug('groups') . '/' . bp_current_item() . '">' . bp_current_item() .  '</a></li>';
                            if (bp_current_action()) {
                                echo $delimiter . '<span' . $schema_link . '><a' . $schema_prop_url . ' href="' . home_url() . '/' . bp_get_root_slug('groups') . '/' . bp_current_item() . '/' . bp_current_action() . '">' .  bp_current_action() . '</a></li>';
                            }
                        }
                    } else
                    if (bp_is_members_directory()) {
                        echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . home_url() . '/' . bp_get_root_slug('members') . '">' .  bp_get_root_slug('members') .  '</a></li>';
                    } else
                    if (bp_is_user()) {
                        echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . home_url() . '/' . bp_get_root_slug('members') . '">' .  bp_get_root_slug('members') . '</a></li>';
                        echo $delimiter . '<span' . $schema_link . '><a' . $schema_prop_url . ' href="' . bp_core_get_user_domain($bp->displayed_user->id) . '">' .  bp_get_displayed_user_username() . '</span>' . '</a></li>';
                        if (bp_current_action()) {
                            echo $delimiter . '<span' . $schema_link . '><a' . $schema_prop_url . ' href="' . bp_core_get_user_domain($bp->displayed_user->id) . bp_current_component() . '">' .  bp_current_component() . '</a></li>';
                        }
                    } else {
                        if (bp_is_directory()) {
                            echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink() . '">' .  bp_current_component() .  '</a></li>';
                        } else {
                            echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink() . '">' .  the_title_attribute('echo=0') .  '</a></li>';
                        }
                    }
                } else {
                    echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink() . '">' .  the_title_attribute('echo=0') .  '</a></li>';
                }
            } elseif (is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink($page->ID) . '">' .  get_the_title($page->ID) .  '</a></li>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo $breadcrumbs[$i];
                    if ($i != count($breadcrumbs) - 1)
                        echo ' ' . $delimiter . ' ';
                }
                echo $delimiter . '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_permalink() . '">' .  the_title_attribute('echo=0') .  '</a></li>';
            }
            elseif (is_tag()) {
                $tag_id = get_term_by('name', single_cat_title('', false), 'post_tag');
                if ($tag_id) {
                    $tag_link = get_tag_link($tag_id->term_id);
                }
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . $tag_link . '">' .  single_cat_title('', false) . '</a></li>';
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo '<li' . $schema_link . '><a' . $schema_prop_url . ' href="' . get_author_posts_url($userdata->ID) . '">' .  $userdata->display_name .  '</a></li>';
            } elseif (is_404()) {
                echo ' ' . $delimiter . ' ' . __('Error 404', 'international-sportsman');
            }
            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                    echo ' (';
                echo '<li> ' . $delimiter . ' <a href="#" >' . __('Page', 'international-sportsman') . ' ' . get_query_var('paged').'</a></li>';
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                    echo ')';
            }
            echo '</ul>';
            
            echo '</div>';
        }
    }
}