# Shortcode Creator - Documentation

- [Shortcode Creator - Documentation](#shortcode-creator---documentation)
  - [🎯 Objectifs pédagogiques](#-objectifs-pédagogiques)
  - [Architecture du plugin](#architecture-du-plugin)
    - [Rôle de chaque fichier](#rôle-de-chaque-fichier)
  - [Utilisation](#utilisation)
    - [Syntaxe du shortcode](#syntaxe-du-shortcode)
    - [Paramètres](#paramètres)
    - [Exemples](#exemples)
    - [Comment trouver l'ID d'un article ?](#comment-trouver-lid-dun-article-)
  - [Explication du code](#explication-du-code)
    - [Fichier principal : `shortcode-creator.php`](#fichier-principal--shortcode-creatorphp)
      - [Analyse ligne par ligne](#analyse-ligne-par-ligne)
    - [Classe ShortcodeCreator](#classe-shortcodecreator)
      - [Le constructeur `__construct()`](#le-constructeur-__construct)
      - [Méthode `enqueueStyles()`](#méthode-enqueuestyles)
      - [Méthode `render()`](#méthode-render)
      - [Méthode `prepareData()`](#méthode-preparedata)
      - [Méthode `loadTemplate()`](#méthode-loadtemplate)
    - [Template blog-card](#template-blog-card)
  - [Concepts clés](#concepts-clés)
    - [1. Le pattern MVC (simplifié)](#1-le-pattern-mvc-simplifié)
    - [2. Séparation des responsabilités](#2-séparation-des-responsabilités)
    - [3. Namespaces en PHP](#3-namespaces-en-php)
    - [4. Output Buffering (ob\_start)](#4-output-buffering-ob_start)
    - [5. Sécurité WordPress](#5-sécurité-wordpress)
  - [🎯 Pour aller plus loin](#-pour-aller-plus-loin)
    - [Ajout d'un nouveau paramètre](#ajout-dun-nouveau-paramètre)
    - [Créer un nouveau shortcode](#créer-un-nouveau-shortcode)
    - [Améliorations possibles](#améliorations-possibles)
      - [1. Ajouter une image à la une](#1-ajouter-une-image-à-la-une)
      - [2. Afficher les catégories](#2-afficher-les-catégories)


---

## 🎯 Objectifs pédagogiques

**Shortcode Creator** est un plugin WordPress pédagogique qui permet de créer des cartes d'articles (blog cards) via un shortcode. Comprendre les concepts fondamentaux du développement WordPress :

- Création de shortcodes
- Architecture orientée objet (POO)
- Séparation de la logique et de la présentation
- Utilisation des namespaces PHP
- Gestion des templates

## Architecture du plugin

```
shortcode-creator/
├── shortcode-creator.php          # Fichier principal du plugin
├── class/
│   └── ShortcodeCreator.php       # Classe principale
├── templates/
│   └── blog-card.php              # Template HTML
└── assets/
    └── css/
        └── blog-card.css          # Styles CSS
```

### Rôle de chaque fichier

| Fichier | Rôle |
|---------|------|
| `shortcode-creator.php` | Point d'entrée du plugin, charge la classe et initialise le shortcode |
| `class/ShortcodeCreator.php` | Contient toute la logique métier (récupération des données, enregistrement du shortcode) |
| `templates/blog-card.php` | Template HTML pour l'affichage visuel de la carte |
| `assets/css/blog-card.css` | Styles CSS pour la mise en forme |


---

## Utilisation

### Syntaxe du shortcode

```
[blog_card post_id="123"]
```

### Paramètres

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `post_id` | int | ✅ Oui | L'ID de l'article WordPress à afficher |

### Exemples

```
[blog_card post_id="42"]
[blog_card post_id="156"]
```

### Comment trouver l'ID d'un article ?

1. Allez dans **Articles > Tous les articles**
2. Survolez un article, l'ID apparaît dans l'URL : `post=123`
3. Utilisez cet ID dans le shortcode

---

## Explication du code

### Fichier principal : `shortcode-creator.php`

```php
<?php
/**
 * Plugin Name: Shortcode Creator
 * Description: Création de shortcode (en dur)
 * Version: 1.0.0
 * Author: David
 */
namespace ShortcodeCreator;

require_once 'class/ShortcodeCreator.php';

add_action('init', function () {
    new ShortcodeCreator();
});
```

#### Analyse ligne par ligne

**En-tête du plugin :**
```php
/**
 * Plugin Name: Shortcode Creator
 * Description: Création de shortcode (en dur)
 * Version: 1.0.0
 * Author: David
 */
```
- **Rôle** : Déclaration des métadonnées du plugin
- **Importance** : WordPress lit ces informations pour afficher le plugin dans l'administration
- **Obligatoire** : Au minimum `Plugin Name` doit être présent

**Namespace :**
```php
namespace ShortcodeCreator;
```
- **Rôle** : Évite les conflits de noms avec d'autres plugins ou le core WordPress
- **Bonne pratique** : Utilisez toujours un namespace unique pour vos plugins

**Chargement de la classe :**
```php
require_once 'class/ShortcodeCreator.php';
```
- **Rôle** : Inclut le fichier contenant la classe principale
- **`require_once`** : S'assure que le fichier n'est chargé qu'une seule fois

**Initialisation du plugin :**
```php
add_action('init', function () {
    new ShortcodeCreator();
});
```
- **`add_action('init', ...)`** : Hook WordPress qui s'exécute après l'initialisation de WordPress
- **`new ShortcodeCreator()`** : Crée une instance de la classe (appelle le constructeur)
- **Moment d'exécution** : Se déclenche après que tous les plugins et le thème soient chargés

---

### Classe ShortcodeCreator

```php
<?php
namespace ShortcodeCreator;

class ShortcodeCreator
{
    public function __construct()
    {
        add_shortcode('blog_card', array($this, 'render'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
    }
    
    // ... méthodes
}
```

#### Le constructeur `__construct()`

**Rôle du constructeur :**
- Méthode spéciale appelée automatiquement lors de `new ShortcodeCreator()`
- Initialise les hooks et enregistre le shortcode
- S'exécute une seule fois lors de la création de l'objet

**Enregistrement du shortcode :**
```php
add_shortcode('blog_card', array($this, 'render'));
```
- **`add_shortcode()`** : Fonction WordPress pour créer un shortcode
- **Premier paramètre** : `'blog_card'` → le nom du shortcode à utiliser dans l'éditeur
- **Deuxième paramètre** : `array($this, 'render')` → la méthode de la classe à appeler
- **Syntaxe** : `array($this, 'nomMethode')` indique "appelle la méthode de cette classe"

**Enregistrement des styles CSS :**
```php
add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
```
- **`wp_enqueue_scripts`** : Hook qui se déclenche quand WordPress charge les scripts/styles du front-end
- **Appelle** : La méthode `enqueueStyles()` pour charger le CSS

---

#### Méthode `enqueueStyles()`

```php
public function enqueueStyles()
{
    wp_enqueue_style(
        'blog-card-styles',
        plugin_dir_url(dirname(__FILE__)) . 'assets/css/blog-card.css',
        array(),
        '1.0.0'
    );
}
```

**Rôle :** Charge le fichier CSS du plugin dans WordPress

**Paramètres de `wp_enqueue_style()` :**

| Paramètre | Valeur | Explication |
|-----------|--------|-------------|
| Handle | `'blog-card-styles'` | Identifiant unique pour ce fichier CSS |
| URL | `plugin_dir_url(...)` | Chemin complet vers le fichier CSS |
| Dépendances | `array()` | Tableau vide = aucune dépendance |
| Version | `'1.0.0'` | Numéro de version (pour gérer le cache) |

**Fonction `plugin_dir_url()` :**
```php
plugin_dir_url(dirname(__FILE__))
```
- **`__FILE__`** : Chemin complet du fichier actuel
- **`dirname(__FILE__)`** : Remonte d'un niveau (du dossier `class/` vers la racine du plugin)
- **`plugin_dir_url()`** : Convertit le chemin système en URL web
- **Résultat** : `https://monsite.com/wp-content/plugins/shortcode-creator/`

---

#### Méthode `render()`

```php
public function render($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'post_id' => null
    ), $atts);
    
    if (!$atts['post_id']) {
        return 'Erreur : Blog Card: post_id requis';
    }
    
    $post = get_post(absint($atts['post_id']));
    
    if (!$post) {
       return '<div>Erreur : Post non trouvé</div>';
    }
    
    $data = $this->prepareData($post, $atts);
    
    return $this->loadTemplate('blog-card', $data);
}
```

**Rôle :** Méthode principale appelée lorsque WordPress rencontre `[blog_card]` dans le contenu

**Paramètres de la méthode :**

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$atts` | array | Attributs du shortcode (ex: `post_id="123"`) |
| `$content` | string\|null | Contenu entre les balises (pour `[blog_card]contenu[/blog_card]`) |

**Étape 1 : Normalisation des attributs**
```php
$atts = shortcode_atts(array(
    'post_id' => null
), $atts);
```
- **`shortcode_atts()`** : Fonction WordPress qui fusionne les attributs par défaut avec ceux fournis
- **Premier paramètre** : Valeurs par défaut
- **Deuxième paramètre** : Attributs fournis par l'utilisateur
- **Résultat** : Si l'utilisateur oublie `post_id`, la valeur sera `null`

**Étape 2 : Validation du post_id**
```php
if (!$atts['post_id']) {
    return 'Erreur : Blog Card: post_id requis';
}
```
- **Vérifie** : Si `post_id` est absent ou vide
- **Return** : Les shortcodes doivent **retourner** le contenu, pas l'afficher avec `echo`

**Étape 3 : Récupération de l'article**
```php
$post = get_post(absint($atts['post_id']));
```
- **`absint()`** : Convertit en entier positif absolu (sécurité)
- **`get_post()`** : Fonction WordPress qui récupère un article par son ID
- **Retour** : Un objet `WP_Post` ou `null` si l'article n'existe pas

**Étape 4 : Vérification de l'existence**
```php
if (!$post) {
   return '<div>Erreur : Post non trouvé</div>';
}
```

**Étape 5 : Préparation des données**
```php
$data = $this->prepareData($post, $atts);
```
- **Appelle** : La méthode privée `prepareData()` pour structurer les données

**Étape 6 : Chargement du template**
```php
return $this->loadTemplate('blog-card', $data);
```
- **Appelle** : La méthode privée `loadTemplate()` qui génère le HTML

---

#### Méthode `prepareData()`

```php
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
```

**Rôle :** Prépare et structure les données de l'article pour le template

**Modificateur `private` :**
- Cette méthode ne peut être appelée que depuis l'intérieur de la classe
- Bonne pratique : séparer la logique métier (private) de l'interface publique (public)

**Configuration du post global :**
```php
setup_postdata($post);
```
- **Rôle** : Configure les fonctions WordPress (`get_the_title()`, etc.) pour utiliser ce post spécifique
- **Nécessaire** : Car nous ne sommes pas dans la boucle WordPress traditionnelle

**Construction du tableau de données :**
```php
$data = array(
    'post_id' => $post->ID,
    'titre' => get_the_title($post),
    'lien' => get_permalink($post),
    'excerpt' => get_the_excerpt($post)
);
```

| Clé | Fonction WordPress | Description |
|-----|-------------------|-------------|
| `post_id` | `$post->ID` | ID de l'article |
| `titre` | `get_the_title()` | Titre de l'article |
| `lien` | `get_permalink()` | URL de l'article |
| `excerpt` | `get_the_excerpt()` | Extrait de l'article (résumé court) |

**Réinitialisation :**
```php
wp_reset_postdata();
```
- **Rôle** : Restaure les données du post global après utilisation
- **Importance** : Évite les conflits avec d'autres fonctionnalités WordPress

---

#### Méthode `loadTemplate()`

```php
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
```

**Rôle :** Charge et exécute un fichier template PHP, puis retourne son contenu

**Étape 1 : Construction du chemin**
```php
$templatePath = plugin_dir_path(dirname(__FILE__)) . 'templates/' . $templateName . '.php';
```
- **`plugin_dir_path()`** : Retourne le chemin absolu du dossier du plugin
- **`dirname(__FILE__)`** : Remonte au dossier parent (racine du plugin)
- **Résultat** : `/var/www/html/wp-content/plugins/shortcode-creator/templates/blog-card.php`

**Étape 2 : Vérification de l'existence**
```php
if (!file_exists($templatePath)) {
    return '<!-- Template non trouvé : ' . esc_html($templateName) . ' -->';
}
```
- **`file_exists()`** : Vérifie si le fichier existe sur le serveur
- **`esc_html()`** : Échappe le nom du template (sécurité XSS)
- **Commentaire HTML** : Visible dans le code source mais pas à l'écran

**Étape 3 : Extraction des données**
```php
extract($data);
```
- **Rôle** : Transforme les clés du tableau en variables
- **Exemple** : `['titre' => 'Mon article']` devient `$titre = 'Mon article'`
- **Résultat** : Les variables sont directement accessibles dans le template

**Étape 4 : Buffering de sortie**
```php
ob_start();
include $templatePath;
return ob_get_clean();
```

**Explication du buffering :**

| Fonction | Rôle |
|----------|------|
| `ob_start()` | Démarre la capture de sortie (buffer) |
| `include` | Exécute le template (qui génère du HTML) |
| `ob_get_clean()` | Récupère le contenu du buffer et le vide |

**Pourquoi utiliser le buffering ?**
- Sans buffering : le template afficherait directement le HTML à l'écran
- Avec buffering : on capture le HTML dans une variable pour le retourner
- **Important** : Les shortcodes doivent **retourner** le contenu, pas l'afficher

**Schéma du flux :**
```
ob_start() → include template → HTML généré → ob_get_clean() → return HTML
```

---

### Template blog-card

```php
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
```

**Rôle :** Définit la structure HTML de la carte d'article

**Documentation en en-tête :**
```php
/**
 * Variables disponibles :
 * @var int $post_id
 * @var string $titre
 * ...
 */
```
- **Bonne pratique** : Documenter les variables disponibles pour les développeurs
- **Aide l'IDE** : Certains éditeurs peuvent fournir l'autocomplétion

**Attribut data :**
```php
data-post-id="<?php echo $post_id; ?>"
```
- **Attribut HTML5** : `data-*` permet de stocker des données personnalisées
- **Utilité** : JavaScript peut récupérer cet ID facilement

**Fonctions de sécurité :**

| Fonction | Utilisation | Rôle |
|----------|-------------|------|
| `esc_url()` | URLs | Échappe et valide les URLs |
| `esc_html()` | Texte | Échappe les caractères HTML (`<`, `>`, `&`) |
| `esc_attr()` | Attributs HTML | Échappe pour les attributs HTML |

**Pourquoi échapper les données ?**
- **Sécurité XSS** : Empêche l'injection de code malveillant
- **Exemple** : Si le titre contient `<script>alert('hack')</script>`, `esc_html()` le convertira en texte inoffensif

---

## Concepts clés

### 1. Le pattern MVC (simplifié)

Ce plugin suit une architecture MVC simplifiée :

| Composant | Fichier | Rôle |
|-----------|---------|------|
| **Model** | `prepareData()` | Récupère et structure les données |
| **View** | `templates/blog-card.php` | Affiche les données (HTML) |
| **Controller** | `ShortcodeCreator` | Orchestre Model et View |

### 2. Séparation des responsabilités

```
┌─────────────────────────────────────┐
│   ShortcodeCreator (Logique PHP)    │
│  - Récupération des données         │
│  - Validation                       │
│  - Traitement                       │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   Template (Présentation HTML)      │
│  - Structure HTML                   │
│  - Affichage des données            │
│  - Classes CSS                      │
└─────────────────────────────────────┘
```

**Avantages :**
- ✅ Code plus lisible et maintenable
- ✅ Designer peut modifier le HTML sans toucher au PHP
- ✅ Développeur peut modifier la logique sans casser l'affichage
- ✅ Réutilisation facile des templates

### 3. Namespaces en PHP

```php
namespace ShortcodeCreator;
```

**Sans namespace :**
```php
class ShortcodeCreator { } // Risque de conflit si un autre plugin utilise ce nom
```

**Avec namespace :**
```php
namespace ShortcodeCreator;
class ShortcodeCreator { } // Nom complet : ShortcodeCreator\ShortcodeCreator
```

**Évite les conflits :**
```php
namespace PluginA;
class Helper { }

namespace PluginB;
class Helper { } // Pas de conflit, car PluginB\Helper ≠ PluginA\Helper
```

### 4. Output Buffering (ob_start)

**Problème sans buffering :**
```php
function render() {
    echo '<div>Hello</div>'; // S'affiche immédiatement
    return ''; // Trop tard !
}
```

**Solution avec buffering :**
```php
function render() {
    ob_start();
    echo '<div>Hello</div>'; // Capturé dans le buffer
    return ob_get_clean(); // Retourne le contenu
}
```

**Analogie :**
- **Sans buffering** : Parler directement dans un micro
- **Avec buffering** : Enregistrer dans un magnétophone, puis écouter l'enregistrement

### 5. Sécurité WordPress

**Toujours échapper les sorties :**

| Contexte | Fonction à utiliser | Exemple |
|----------|-------------------|---------|
| Texte HTML | `esc_html()` | `<p><?php echo esc_html($titre); ?></p>` |
| Attribut HTML | `esc_attr()` | `<div class="<?php echo esc_attr($class); ?>">` |
| URL | `esc_url()` | `<a href="<?php echo esc_url($lien); ?>">` |
| JavaScript | `esc_js()` | `var name = '<?php echo esc_js($nom); ?>';` |

**Valider les entrées :**
```php
absint($atts['post_id']); // Force un entier positif
sanitize_text_field($atts['titre']); // Nettoie une chaîne de caractères
```

---

## 🎯 Pour aller plus loin

### Ajout d'un nouveau paramètre

**1. Modifier les attributs par défaut :**
```php
$atts = shortcode_atts(array(
    'post_id' => null,
    'show_image' => 'true' // Nouveau paramètre
), $atts);
```

**2. Ajouter au tableau de données :**
```php
$data = array(
    // ... données existantes
    'show_image' => $atts['show_image'] === 'true',
    'image_url' => get_the_post_thumbnail_url($post, 'medium')
);
```

**3. Utiliser dans le template :**
```php
<?php if ($show_image && $image_url): ?>
    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($titre); ?>">
<?php endif; ?>
```

**4. Utilisation :**
```
[blog_card post_id="123" show_image="true"]
```

### Créer un nouveau shortcode

**1. Ajouter dans le constructeur :**
```php
add_shortcode('autre_shortcode', array($this, 'renderAutreShortcode'));
```

**2. Créer la méthode de rendu :**
```php
public function renderAutreShortcode($atts, $content = null) {
    // Votre logique ici
    return $this->loadTemplate('autre-template', $data);
}
```

**3. Créer le template :**
```
templates/autre-template.php
```

---

### Améliorations possibles

#### 1. Ajouter une image à la une
```php
// Dans prepareData()
'image_url' => get_the_post_thumbnail_url($post, 'medium_large'),
'image_alt' => get_post_meta(get_post_thumbnail_id($post), '_wp_attachment_image_alt', true)
```

#### 2. Afficher les catégories
```php
// Dans prepareData()
'categories' => wp_get_post_categories($post->ID, array('fields' => 'names'))
```

---