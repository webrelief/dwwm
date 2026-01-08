<?php

namespace ShortcodeCreator;

class ShortcodeCreator
{
    public function __construct()
    {
        add_shortcode('blog_card', array($this, 'render'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
    }

    public function enqueueStyles()
    {
        wp_enqueue_style(
            'blog-card-styles',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/blog-card.css',
            array(),
            '1.0.0'
        );
    }

    public function render($atts, $content = null)
    {
        
        $atts = shortcode_atts(array(
            'post_id' => null
        ), $atts);

        // Vérifier que le post_id existe
        if (!$atts['post_id']) {
            return 'Erreur : Blog Card: post_id requis';
        }

        $post = get_post(absint($atts['post_id']));
        
        if (!$post) {
           return '<div>Erreur :  Post non trouvé</div>';
        }

        // Préparer les données
        $data = $this->prepareData($post, $atts);
        // Charger le template
        return $this->loadTemplate('blog-card', $data);
    }

    private function prepareData($post, $atts)
    {
        setup_postdata($post);

        $data = array(
            'post_id' => $post->ID,
            'titre' => get_the_title($post),
            'lien' => get_permalink($post),
            'excerpt' => get_the_excerpt($post)
        );

        wp_reset_postdata();

        return $data;
    }

    private function loadTemplate($templateName, $data)
    {
        $templatePath = plugin_dir_path(dirname(__FILE__)) . 'templates/' . $templateName . '.php';

        if (!file_exists($templatePath)) {
            return '<!-- Template non trouvé : ' . esc_html($templateName) . ' -->';
        }

        extract($data);

        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
