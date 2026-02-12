<?php
/**
 * Plugin Name: Front Controller
 * Description: Module WordPress permettant de créer un controller front pour afficher des informations
 * Version: 1.0
 * Author: David
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale du plugin
 */
class FrontControllerPlugin
{
    public function __construct()
    {
        add_action('init', array($this, 'addRewriteRules'));
        add_filter('query_vars', array($this, 'addQueryVars'));
        add_action('template_redirect', array($this, 'handleCustomRoute'));        
    }

    /**
     * Ajouter les règles de réécriture d'URL
     */
    public function addRewriteRules()
    {
        add_rewrite_rule(
            '^page-front-controller$',
            'index.php?my_route=method_helloworld',
            'top'
        );
        
        add_rewrite_rule(
             '^page-formulaire$',
             'index.php?my_route=formulaire',
             'top'
        );
    }
    /**
     * Ajouter les variables de requête personnalisées
     */
    public function addQueryVars($vars)
    {
        $vars[] = 'my_route';
        return $vars;
    }
    /**
     * Gérer les routes personnalisées
     */
    public function handleCustomRoute()
    {
        $my_route = get_query_var('my_route', false);
        
        if ($my_route === 'method_helloworld') {
            $this->renderPageHelloWorld();
            exit;
        } elseif ($my_route === 'formulaire') {
            $this->renderPageFormulaire();
            exit;
        }
    }

    private function renderPageHelloWorld()
    {
        $data = array(
            'title' => 'Titre de ma page ou valeur récupérée en base de données',
            'content' => 'Le contenu, ou également des informations qui viennent de la base de données !',
        );
                
        // Les variables sont extraites et disponibles dans la vue
        extract($data);

        ob_start();
        include plugin_dir_path(__FILE__) . 'views/hello-world-template.php';
        $template = ob_get_clean();

        
        if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
            include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
        } else {
            include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
        }
    }

    private function renderPageFormulaire()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $element = sanitize_text_field($_POST['element'] ?? '');
            // Traiter les données : sauveagrde en base de données, via la table options ou ses propres tables.
            $message = 'Formulaire soumis avec succès !';
            $data = array(
                'message' => $message
            );
        } else {
            $data = array(
                'input_value' => 'Valeur par défaut'
            );
        }
                
        // Les variables sont extraites et disponibles dans la vue
        extract($data);

        ob_start();
        include plugin_dir_path(__FILE__) . 'views/formulaire.php';
        $template = ob_get_clean();

        
        if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
            include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
        } else {
            include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
        }
    }

}

// Démarrer le plugin
$frontControllerPlugin = new FrontControllerPlugin();

// Hook d'activation : régénérer les règles de réécriture
register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

// Hook de désactivation : nettoyer les règles
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});