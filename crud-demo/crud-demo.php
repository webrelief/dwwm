<?php
/**
 * Plugin Name: CRUD Demo
 * Description: Module WordPress permettant de créer une table, et de créer un système d'ajout, modification et suppression dans cette table
 * Version: 1.0
 * Author: David
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'models/ElementModel.php';

/**
 * Classe principale du plugin
 */
class CrudDemoPlugin
{    
    private $elementModel;

    public function __construct()
    {        
        $this->elementModel = new ElementModel();

        // Hooks WordPress
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'handleActions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }
    
    public function enqueueScripts($hook)
    {
        // Charger uniquement sur nos pages
        if (strpos($hook, 'crud-demo') === false) {
            return;
        }
        
        wp_enqueue_script(
            'crud-demo-admin',
            plugin_dir_url(__FILE__) . 'assets/js/admin-crud.js',
            ['jquery'],
            '1.0',
            true
        );
    }

    // Ajouter le menu dans l'admin
    public function addAdminMenu()
    {
        add_menu_page(
            'CRUD Démo',
            'CRUD Démo',
            'manage_options',
            'crud-demo-listing',
            [$this, 'renderAdminListingPage'],
            'dashicons-admin-settings',
            30
        );
        // Sous-page : Ajouter un élément
        add_submenu_page(
            'crud-demo-listing',           // Parent slug
            'Ajouter un élément',          // Titre de la page
            'Ajouter',                     // Titre du menu
            'manage_options',              // Capacité requise
            'crud-demo-form',               // Slug de la page
            [$this, 'renderAdminFormPage']  // Fonction de rendu
        );
    }
    
    public function renderAdminFormPage()
    {
        $element = null;
        $is_edit = false;
        
        // Si un ID est présent, on est en mode édition
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $element = $this->elementModel->getById($id);
            
            if (!$element) {
                wp_die('Élément non trouvé');
            }
            
            $is_edit = true;
        }
        
        include plugin_dir_path(__FILE__) . 'views/admin-form-page.php';
    }

    public function renderAdminListingPage()
    {        
        $elements = $this->elementModel->getAll();
        include 'views/admin-listing-page.php';
    }


    /**
     * Gérer les actions (suppression, ajout, modification)
     */
    public function handleActions()
    {
        // Gestion de la suppression
        if (isset($_GET['page']) && $_GET['page'] === 'crud-demo-listing' 
            && isset($_GET['action']) && $_GET['action'] === 'delete' 
            && isset($_GET['id'])) {
            
            $id = intval($_GET['id']);
            
            // Vérifier le nonce pour la sécurité
            if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_element_' . $id)) {
                wp_die('Action non autorisée');
            }
            
            $this->elementModel->delete($id);
            
            // Redirection après suppression
            wp_redirect(admin_url('admin.php?page=crud-demo-listing&deleted=1'));
            exit;
        }
        
        // Gestion de l'ajout/modification
        if (isset($_POST['crud_demo_submit'])) {
            // Vérifier le nonce
            if (!isset($_POST['crud_demo_nonce']) 
                || !wp_verify_nonce($_POST['crud_demo_nonce'], 'crud_demo_save')) {
                wp_die('Action non autorisée');
            }
            
            // Vérifier les capacités
            if (!current_user_can('manage_options')) {
                wp_die('Vous n\'avez pas les permissions nécessaires');
            }
            
            // Récupérer et nettoyer les données
            $name = sanitize_text_field($_POST['name']);
            
            // Validation
            if (empty($name)) {
                wp_redirect(admin_url('admin.php?page=crud-demo-form&error=empty'));
                exit;
            }
            
            if (strlen($name) > 50) {
                wp_redirect(admin_url('admin.php?page=crud-demo-form&error=toolong'));
                exit;
            }
            
            // Modification ou ajout
            if (isset($_POST['id_element']) && !empty($_POST['id_element'])) {
                // Modification
                $id = intval($_POST['id_element']);
                $this->elementModel->update($id, $name);
                wp_redirect(admin_url('admin.php?page=crud-demo-listing&updated=1'));
            } else {
                // Ajout
                $this->elementModel->create($name);
                wp_redirect(admin_url('admin.php?page=crud-demo-listing&added=1'));
            }
            
            exit;
        }
    }

    public function createTable()
    {
        $this->elementModel->createTable();
    }
    
    /**
     * Supprimer la table lors de la désactivation du plugin
     */
    public function dropTable()
    {
        $this->elementModel->dropTable();
    }

}

// Démarrer le plugin
$crudDemoPlugin = new CrudDemoPlugin();
register_activation_hook(__FILE__, [$crudDemoPlugin, 'createTable']);
register_deactivation_hook(__FILE__, [$crudDemoPlugin, 'dropTable']);