<?php 
while ( $all_posts->have_posts() ) :

    $all_posts->the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class('wpcap-post rowsection'); ?>>
            <div class="width50_01">
                <?php $this->render_thumbnail($settings); ?>
            </div>
            <div class="width50_02">
                <?php $this->render_title($settings); ?>
                <?php $this->render_meta($settings); ?>
                <?php $this->render_categories($settings); ?> 
                <?php $this->render_excerpt($settings); ?>
                <div class="News_Title_text">
                    <p><span class="News_Title">PRODUCTION DATE:</span> <span>March 10, 2018</span></p>
                    <p><span class="News_Title">Title:</span><span>Late Night</span></p>
                    <p><span class="News_Title">DIRECTORS:</span><span>Impressive Lady</span></p>
                    <p><span class="News_Title">LENSES:</span><span>Primo X Series, C-Series Anamorphic</span></p>
                </div>
                <?php $this->render_readmore($settings); ?>
            </div>
        </div>
        <?php

endwhile; 

wp_reset_postdata();