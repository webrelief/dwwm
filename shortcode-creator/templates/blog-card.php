<?php
/**
 * Template pour le shortcode blog_card
 * 
 * Variables disponibles :
 * @var int $post_id
 * @var string $titre
 * @var string $lien
 * @var string $excerpt
 */
?>

<article class="blog-card" data-post-id="<?php echo $post_id; ?>">    
    <div class="blog-card__content">                
        <h3 class="blog-card__title">
            <a href="<?php echo esc_url($lien); ?>" class="blog-card__title-link">
                <?php echo esc_html($titre); ?>
            </a>
        </h3>
        
        <p class="blog-card__excerpt">
            <?php echo $excerpt; ?>
        </p>        
    </div>
</article>
