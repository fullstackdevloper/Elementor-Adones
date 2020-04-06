<?php 
while ( $all_posts->have_posts() ) :

    $all_posts->the_post(); ?>
    
        <article id="post-<?php the_ID(); ?>" <?php post_class('wpcap-post'); ?>>
         
            <div class="modern_grid_theme">
                
                <?php $this->render_thumbnail($settings); ?>

                <div class="post-carousel-text-wrap">
                    <?php $this->render_meta($settings); ?>
                    <?php $this->render_title($settings); ?>
                    <?php $this->render_excerpt($settings); ?>
                    <?php $this->render_readmore($settings); ?>
                </div>

            </div><!-- .blog-inner -->
           
        </article>

        <?php

endwhile; 

wp_reset_postdata();