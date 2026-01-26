<?php

// Récupérer toutes les marques créées avec Woocommerce
$brands= get_terms([
    'taxonomy' => 'product_brand',
    'hide_empty' => false, // Inclut les marques sans produits
    'orderby' => 'name',
    'order' => 'ASC'
]);

// Récupérer les catégories de Woocommerce
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => false, // false = afficher même les catégories vides
    'orderby' => 'name',
    'order' => 'ASC'
]);

// Récupérer TOUS les produits de Woocommerce
// Code pour l'exemple, pas viable sur un site de prod
$products = wc_get_products(array(
    'limit' => -1, // -1 = tous les produits
    'status' => 'publish', // Seulement les produits publiés
));
