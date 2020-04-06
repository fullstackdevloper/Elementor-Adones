<div class="ecp_posts">
    <?php 
    while ( $all_posts->have_posts() ) :

        $all_posts->the_post(); ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class('wpcap-post'); ?>>

                <div class="post-carousel-inner">

                    <?php $this->render_title($settings); ?>

                    <?php $this->render_meta($settings); ?>

                    <?php $this->render_thumbnail($settings); ?>

                    <div class="post-carousel-text-wrap">
                            <?php $this->render_excerpt($settings); ?>
                            <?php $this->render_readmore($settings); ?>
                    </div>

                </div><!-- .blog-inner -->

            </div>
            <?php
    endwhile; 
    wp_reset_postdata();
?>
</div>