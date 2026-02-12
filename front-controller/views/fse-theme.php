<!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>
    
        <?php block_template_part('header'); ?>

        <main class="wp-block-group is-layout-constrained">
            <div class="wp-block-post-content alignwide">
                <?php echo $template;?>
            </div>
        </main>

        <?php     
            block_template_part('footer'); 
            wp_footer();
        ?>
    </body>
</html>