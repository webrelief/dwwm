# Récupération des données WooCommerce

Ce code permet de récupérer les marques, catégories et produits WooCommerce dans des variables PHP.

## 📋 Prérequis

- WordPress installé
- WooCommerce activé

## 🔧 Utilisation

### Récupérer toutes les marques

```php
$brands = get_terms([
    'taxonomy' => 'product_brand',
    'hide_empty' => false, // Inclut les marques sans produits
    'orderby' => 'name',
    'order' => 'ASC'
]);
```

**Accéder aux données d'une marque :**

```php
foreach ($brands as $brand) {
    echo $brand->term_id;      // ID de la marque
    echo $brand->name;         // Nom de la marque
    echo $brand->slug;         // Slug URL
    echo $brand->description;  // Description
    echo $brand->count;        // Nombre de produits
}
```

### Récupérer toutes les catégories

```php
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => false, // false = afficher même les catégories vides
    'orderby' => 'name',
    'order' => 'ASC'
]);
```

**Accéder aux données d'une catégorie :**

```php
foreach ($categories as $category) {
    echo $category->term_id;      // ID de la catégorie
    echo $category->name;         // Nom
    echo $category->slug;         // Slug
    echo $category->parent;       // ID de la catégorie parente (0 si aucune)
    echo $category->count;        // Nombre de produits
}
```

### Récupérer tous les produits

```php
$products = wc_get_products(array(
    'limit' => -1, // -1 = tous les produits
    'status' => 'publish', // Seulement les produits publiés
));
```

**Accéder aux données d'un produit :**

```php
foreach ($products as $product) {
    echo $product->get_id();              // ID du produit
    echo $product->get_name();            // Nom
    echo $product->get_slug();            // Slug
    echo $product->get_price();           // Prix
    echo $product->get_regular_price();   // Prix régulier
    echo $product->get_sale_price();      // Prix en promotion
    echo $product->get_sku();             // SKU/Référence
    echo $product->get_stock_quantity();  // Quantité en stock
    echo $product->get_description();     // Description longue
    echo $product->get_short_description(); // Description courte
    echo $product->get_image_id();        // ID de l'image principale
    echo $product->get_permalink();       // URL du produit
}
```

## ⚠️ Avertissements

### Performance

> **⚠️ ATTENTION :** Le code `'limit' => -1` récupère **TOUS** les produits en une seule fois.
> 
> **Ceci n'est PAS viable en production** si vous avez :
> - Plus de 100 produits
> - Un site à fort trafic
> - Des ressources serveur limitées

### Risques

- **Épuisement de la mémoire PHP** : Trop de produits peuvent dépasser la limite `memory_limit`
- **Timeout du serveur** : Le script peut prendre trop de temps à s'exécuter
- **Ralentissement du site** : Impact sur les performances globales

## ✅ Solutions pour la production

### Option 1 : Pagination

```php
// Récupérer les produits par lots de 50
$page = 1;
$per_page = 50;

$products = wc_get_products(array(
    'limit' => $per_page,
    'page' => $page,
    'status' => 'publish',
));
```

### Option 2 : Requête spécifique

```php
// Récupérer uniquement les produits d'une catégorie
$products = wc_get_products(array(
    'limit' => 20,
    'category' => array('vetements'), // Slug de la catégorie
    'status' => 'publish',
));
```

### Option 3 : Filtres avancés

```php
// Récupérer avec des filtres
$products = wc_get_products(array(
    'limit' => 50,
    'status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'stock_status' => 'instock', // Seulement en stock
));
```

### Afficher dans un template

```php
$products = wc_get_products(['limit' => 12]);

foreach ($products as $product) {
    echo '<div class="product">';
    echo '<h3>' . esc_html($product->get_name()) . '</h3>';
    echo '<p>' . wc_price($product->get_price()) . '</p>';
    echo '<a href="' . esc_url($product->get_permalink()) . '">Voir le produit</a>';
    echo '</div>';
}
```

## 🔗 Ressources

- [Documentation WooCommerce - wc_get_products()](https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query)
- [Documentation WordPress - get_terms()](https://developer.wordpress.org/reference/functions/get_terms/)
- [WooCommerce Product Class](https://woocommerce.github.io/code-reference/classes/WC-Product.html)

## 📝 Notes

- Utilisez toujours `esc_html()`, `esc_url()`, etc. pour sécuriser l'affichage

## 🤝 Contribution

Ce code est fourni à titre d'exemple éducatif. Pour une utilisation en production, adaptez-le selon vos besoins et contraintes.
