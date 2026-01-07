<?php
/**
 * Plugin Name: Plugin DWWM 1
 * Description: 1° Plugin WordPress
 * Version: 1.0
 * Author: David Trannoy
 */

if (!defined('ABSPATH')) { 
    exit; // petite sécurité Wordpress
}

// On défini notre fonction pour 
function dwwm_1_menu() {
    add_menu_page(
        'Plugin 1', // Meta title de la page du plugin
        'Plugin minimaliste n°1', // Libellé dans le menu
        'manage_options', // Pour la gestion des droits des utilisateurs
        'dwwm-plugin-1', // Slug du menu, doit être unique
        'dwwm_1_plugin_page' // Fonction utilisée pour générer la page
    );
}

// La page de votre plugin
function dwwm_1_plugin_page() {
    echo '<h1>Mon premier plugin WordPress</h1>';
    echo '<p>Ici on affiche ce qu\'on veut avec des echo en PHP :\'( </p>';
}

// Hook pour ajouter un menu admin
add_action('admin_menu', 'dwwm_1_menu');

