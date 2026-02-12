# 📚 Plugin WordPress Front Controller - Guide Pédagogique

## 🎯 Objectif du module

Ce plugin WordPress vous permet de créer des **pages personnalisées** avec vos propres routes (URLs) et contrôleurs, tout en conservant l'apparence de votre thème WordPress (header et footer).

**Exemple concret :** Au lieu de créer une page WordPress classique via l'admin, vous pouvez créer une page `/page-formulaire` directement en PHP avec votre propre logique métier.

---

## 📂 Structure du projet

```
front-controller/
├── front-controller.php          # Fichier principal du plugin
└── views/                         # Dossier contenant les vues
    ├── classic-theme.php          # Layout pour thèmes classiques
    ├── fse-theme.php              # Layout pour thèmes FSE (Full Site Editing)
    ├── hello-world-template.php   # Vue de la page Hello World
    └── formulaire.php             # Vue de la page formulaire
```

---

## 🔧 Installation

1. **Placer le plugin dans WordPress**
   ```
   wp-content/plugins/front-controller/
   ```

2. **Activer le plugin**
   - Aller dans `Extensions > Extensions installées`
   - Activer "Front Controller"

3. **Régénérer les permaliens** (IMPORTANT)
   - Aller dans `Réglages > Permaliens`
   - Cliquer sur "Enregistrer les modifications"
   - Cette étape est nécessaire pour que WordPress reconnaisse vos nouvelles routes

4. **Tester les pages**
   - Visiter : `https://votresite.com/page-front-controller`
   - Visiter : `https://votresite.com/page-formulaire`

---

## 📖 Comprendre le fonctionnement

### 1️⃣ Le fichier principal : `front-controller.php`

#### A. Sécurité de base

```php
if (!defined('ABSPATH')) {
    exit;
}
```

**Explication :** Cette vérification empêche l'accès direct au fichier PHP. Si quelqu'un essaie d'accéder à `votresite.com/wp-content/plugins/front-controller/front-controller.php` directement dans le navigateur, le script s'arrête immédiatement.

**Pourquoi ?** Pour éviter les failles de sécurité où un attaquant pourrait exécuter votre code hors du contexte WordPress.

---

#### B. La classe principale

```php
class FrontControllerPlugin
{
    public function __construct()
    {
        add_action('init', array($this, 'addRewriteRules'));
        add_filter('query_vars', array($this, 'addQueryVars'));
        add_action('template_redirect', array($this, 'handleCustomRoute'));        
    }
}
```

**Explication :** Le constructeur enregistre 3 hooks WordPress qui seront exécutés à différents moments :

1. **`init`** : Moment où WordPress initialise ses fonctionnalités → On en profite pour ajouter nos routes personnalisées
2. **`query_vars`** : Moment où WordPress analyse l'URL → On ajoute nos variables personnalisées
3. **`template_redirect`** : Juste avant que WordPress charge un template → On intercepte pour afficher notre page

**💡 Concept clé :** WordPress fonctionne avec un système de **hooks** (crochets). C'est comme dire à WordPress : "Quand tu fais X, appelle ma fonction Y".

---

#### C. Ajouter des routes personnalisées

```php
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
```

**Explication ligne par ligne :**

- **Premier paramètre** : Expression régulière qui définit l'URL
  - `^page-front-controller$` = L'URL doit être exactement "page-front-controller"
  - `^` = début de l'URL
  - `$` = fin de l'URL
  
- **Deuxième paramètre** : Ce que WordPress doit faire en interne
  - `index.php?my_route=method_helloworld` = Transformer l'URL en paramètre de requête
  
- **Troisième paramètre** : Priorité
  - `'top'` = Cette règle est vérifiée en premier

**🔍 Exemple concret :**

Quand un visiteur tape `https://votresite.com/page-front-controller` :

1. WordPress voit cette URL
2. Il vérifie les règles de réécriture
3. Il trouve notre règle et transforme ça en `index.php?my_route=method_helloworld`
4. WordPress charge `index.php` avec le paramètre `my_route=method_helloworld`

---

#### D. Déclarer les variables personnalisées

```php
public function addQueryVars($vars)
{
    $vars[] = 'my_route';
    return $vars;
}
```

**Explication :** Par défaut, WordPress ne reconnaît que certains paramètres d'URL (comme `?p=123` ou `?page_id=45`). Si on veut utiliser `?my_route=formulaire`, il faut le déclarer ici.

**Sans cette étape :** `get_query_var('my_route')` retournerait toujours vide, même si l'URL contient `?my_route=formulaire`.

**💡 Analogie :** C'est comme déclarer une variable en JavaScript avant de l'utiliser. WordPress a besoin de savoir que `my_route` est une variable valide.

---

#### E. Le routeur : rediriger vers le bon contrôleur

```php
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
```

**Explication :** C'est le **routeur** du plugin. Il regarde quelle route a été demandée et appelle le bon contrôleur.

**Étapes :**

1. `get_query_var('my_route', false)` → Récupère la valeur de `my_route` (ou `false` si elle n'existe pas)
2. Compare la valeur avec les routes connues
3. Appelle le contrôleur correspondant
4. `exit` → Arrête l'exécution pour que WordPress ne continue pas à chercher un template

**🔍 Flux complet :**

```
URL: /page-formulaire
  ↓
Rewrite Rule: my_route=formulaire
  ↓
handleCustomRoute() détecte my_route=formulaire
  ↓
Appelle renderPageFormulaire()
  ↓
exit (WordPress s'arrête ici)
```

---

### 2️⃣ Les contrôleurs

#### A. Contrôleur Hello World

```php
private function renderPageHelloWorld()
{
    // 1. Préparer les données
    $data = array(
        'title' => 'Titre de ma page ou valeur récupérée en base de données',
        'content' => 'Le contenu, ou également des informations qui viennent de la base de données !',
    );
    
    // 2. Rendre les variables disponibles
    extract($data);

    // 3. Capturer le contenu de la vue
    ob_start();
    include plugin_dir_path(__FILE__) . 'views/hello-world-template.php';
    $template = ob_get_clean();

    // 4. Choisir le bon layout selon le type de thème
    if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
        include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
    } else {
        include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
    }
}
```

**📝 Explication détaillée :**

**Étape 1 : Préparer les données**

```php
$data = array(
    'title' => 'Titre de ma page',
    'content' => 'Le contenu',
);
```

C'est ici que vous préparez toutes les données nécessaires pour afficher votre page. En pratique, vous feriez :

```php
// Exemple avec une requête en base de données
global $wpdb;
$product = $wpdb->get_row("SELECT * FROM products WHERE id = 1");

$data = array(
    'title' => $product->name,
    'content' => $product->description,
    'price' => $product->price
);
```

**Étape 2 : Extract - Rendre les variables disponibles**

```php
extract($data);
```

**Avant extract :**
- On a `$data['title']` et `$data['content']`

**Après extract :**
- On a directement `$title` et `$content`

**💡 C'est magique mais attention :** `extract()` crée des variables. C'est pratique mais peut être dangereux si vous ne contrôlez pas les clés du tableau.

**Étape 3 : Output Buffering - Capturer le HTML**

```php
ob_start();
include plugin_dir_path(__FILE__) . 'views/hello-world-template.php';
$template = ob_get_clean();
```

**Explication :**

1. `ob_start()` → Dit à PHP : "À partir de maintenant, ne pas afficher le HTML, stocke-le en mémoire"
2. `include ...` → Charge la vue qui génère du HTML
3. `ob_get_clean()` → Récupère tout le HTML stocké et le met dans `$template`, puis vide le buffer

**Pourquoi faire ça ?**

Parce qu'on veut d'abord générer le contenu de la page, puis l'injecter dans un layout (avec header/footer). Si on faisait juste `include`, le HTML s'afficherait immédiatement.

**Analogie :** C'est comme préparer un plat dans un bol avant de le servir dans une assiette décorée.

**Étape 4 : Choisir le bon layout**

```php
if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
    include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
} else {
    include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
}
```

**Explication :** WordPress a deux types de thèmes :

1. **Thèmes classiques** : Utilisent `get_header()` et `get_footer()`
   - Exemples : Twenty Twenty-One, Astra, GeneratePress

2. **Thèmes FSE** (Full Site Editing) : Utilisent des blocs et `block_template_part()`
   - Exemples : Twenty Twenty-Five, Twenty Twenty-Four

On détecte automatiquement le type de thème et on charge le bon layout.

---

#### B. Contrôleur Formulaire

```php
private function renderPageFormulaire()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $element = sanitize_text_field($_POST['element'] ?? '');
        // Traiter les données : sauvegarde en base de données
        $message = 'Formulaire soumis avec succès !';
        $data = array(
            'message' => $message
        );
    } else {
        $data = array(
            'input_value' => 'Valeur par défaut'
        );
    }
    
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
```

**📝 Explication du flux POST/GET :**

**Cas 1 : Première visite (GET)**

```
Utilisateur tape /page-formulaire
  ↓
$_SERVER['REQUEST_METHOD'] = 'GET'
  ↓
On prépare $data avec 'input_value' par défaut
  ↓
On affiche le formulaire vide
```

**Cas 2 : Soumission du formulaire (POST)**

```
Utilisateur remplit et soumet le formulaire
  ↓
$_SERVER['REQUEST_METHOD'] = 'POST'
  ↓
On récupère $_POST['element']
  ↓
On nettoie avec sanitize_text_field()
  ↓
On traite les données (sauvegarde, etc.)
  ↓
On prépare $data avec un message de succès
  ↓
On réaffiche le formulaire avec le message
```

**🔒 Sécurité importante :**

```php
$element = sanitize_text_field($_POST['element'] ?? '');
```

- `$_POST['element'] ?? ''` → Si la clé n'existe pas, utiliser une chaîne vide (évite les erreurs)
- `sanitize_text_field()` → Nettoie la valeur pour éviter les injections XSS

**💡 Ne JAMAIS faire :**

```php
// ❌ DANGEREUX
$element = $_POST['element'];
echo $element;  // Si un utilisateur envoie du code JavaScript, il s'exécutera !
```

**✅ Toujours faire :**

```php
// ✅ SÉCURISÉ
$element = sanitize_text_field($_POST['element'] ?? '');
echo esc_html($element);  // Le code JavaScript sera affiché comme du texte
```

---

### 3️⃣ Les vues (Views)

#### A. Vue Hello World : `hello-world-template.php`

```php
<div>
    <h1><?php echo $title;?></h1>
    <p><?php echo $content;?></p>
</div>
```

**Explication :** C'est la vue la plus simple. Elle affiche juste le titre et le contenu.

**Variables disponibles :** Grâce à `extract($data)` dans le contrôleur, on a accès à :
- `$title`
- `$content`

**⚠️ Bonne pratique :** En production, il faudrait échapper les variables :

```php
<div>
    <h1><?php echo esc_html($title); ?></h1>
    <p><?php echo esc_html($content); ?></p>
</div>
```

---

#### B. Vue Formulaire : `formulaire.php`

```php
<?php if (isset($message)) {?>
    <div class="alert alert-success">
        <?php echo $message;?>
    </div>
<?php } ?>

<form action="" method="post">
    <div>
        Element : <input type="text" name="element" value="<?php echo $input_value ?? ''; ?>">
    </div>
    <div>
        <button type="submit">Valider</button>
    </div>
</form>
```

**📝 Explication détaillée :**

**1. Affichage conditionnel du message**

```php
<?php if (isset($message)) {?>
    <div class="alert alert-success">
        <?php echo $message;?>
    </div>
<?php } ?>
```

- `isset($message)` → Vérifie si la variable `$message` existe
- Si oui, affiche le message de succès
- Si non (première visite), n'affiche rien

**2. Le formulaire**

```php
<form action="" method="post">
```

- `action=""` → Le formulaire se soumet sur la même URL
- `method="post"` → Utilise la méthode POST (les données ne sont pas visibles dans l'URL)

**3. Pré-remplir le champ**

```php
<input type="text" name="element" value="<?php echo $input_value ?? ''; ?>">
```

- `$input_value ?? ''` → Opérateur de coalescence nulle
- Si `$input_value` existe, l'utiliser
- Sinon, utiliser une chaîne vide

**💡 Pourquoi pré-remplir ?** Pour que l'utilisateur voie ce qu'il a saisi après la soumission.

---

#### C. Layout pour thème classique : `classic-theme.php`

```php
<?php get_header(); ?>
<main>
    <?php echo $template;?>
</main>
<?php get_footer(); ?>
```

**Explication :**

- `get_header()` → Fonction WordPress qui charge le fichier `header.php` de votre thème
  - Affiche le logo, le menu, etc.
  
- `echo $template` → Injecte le contenu qu'on a capturé avec `ob_get_clean()`
  
- `get_footer()` → Fonction WordPress qui charge le fichier `footer.php` de votre thème
  - Affiche le footer, les widgets, etc.

**💡 Résultat final :**

```html
<!-- Header du thème WordPress -->
<header>...</header>

<!-- Notre contenu -->
<main>
    <div>
        <h1>Titre de ma page</h1>
        <p>Le contenu</p>
    </div>
</main>

<!-- Footer du thème WordPress -->
<footer>...</footer>
```

---

#### D. Layout pour thème FSE : `fse-theme.php`

```php
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
```

**📝 Explication ligne par ligne :**

**1. Structure HTML de base**

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
```

- `language_attributes()` → Ajoute l'attribut `lang="fr-FR"` (selon la langue de WordPress)

**2. Head**

```php
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
```

- `bloginfo('charset')` → Affiche le charset (généralement "UTF-8")
- `wp_head()` → **TRÈS IMPORTANT** - Charge tous les CSS et JS de WordPress et des plugins
  - Sans ça, pas de styles, pas de scripts !

**3. Body**

```php
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
```

- `body_class()` → Ajoute des classes CSS automatiques (ex: `page-id-5`, `logged-in`, etc.)
- `wp_body_open()` → Hook pour que les plugins puissent injecter du code au début du body

**4. Header FSE**

```php
<?php block_template_part('header'); ?>
```

- Charge le template "header" du thème FSE
- Équivalent de `get_header()` pour les thèmes à blocs

**5. Contenu avec classes FSE**

```php
<main class="wp-block-group is-layout-constrained">
    <div class="wp-block-post-content alignwide">
        <?php echo $template;?>
    </div>
</main>
```

**Explication des classes CSS :**

- `wp-block-group` → Groupe de blocs WordPress
- `is-layout-constrained` → Layout avec contrainte de largeur
- `alignwide` → Largeur large (environ 1200-1400px)

**💡 Options de largeur :**

- Sans classe → Largeur normale (~800px)
- `alignwide` → Largeur large (~1200px)
- `alignfull` → Pleine largeur (100%)

**6. Footer et scripts**

```php
<?php     
    block_template_part('footer'); 
    wp_footer();
?>
```

- `block_template_part('footer')` → Charge le footer du thème FSE
- `wp_footer()` → **TRÈS IMPORTANT** - Charge les scripts JavaScript en fin de page
  - Sans ça, pas de jQuery, pas de scripts de plugins !

---

### 4️⃣ Les hooks d'activation/désactivation

```php
register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
```

**Explication :**

**À l'activation du plugin :**
- WordPress régénère toutes les règles de réécriture d'URL
- Cela permet d'enregistrer nos routes personnalisées

**À la désactivation :**
- WordPress régénère les règles en supprimant les nôtres
- Cela nettoie proprement le système

**💡 Pourquoi c'est important ?**

Sans `flush_rewrite_rules()`, vos routes personnalisées ne fonctionneraient pas. WordPress ne les connaîtrait tout simplement pas.

**⚠️ Important :** Ne JAMAIS appeler `flush_rewrite_rules()` dans le hook `init` ou à chaque chargement de page. C'est très gourmand en ressources. Uniquement lors de l'activation/désactivation.

---

## 🔄 Flux complet d'une requête

Voici ce qui se passe quand un utilisateur visite `/page-formulaire` :

```
1. Navigateur → https://monsite.com/page-formulaire

2. WordPress reçoit la requête

3. WordPress vérifie les règles de réécriture
   ↓
   Trouve : '^page-formulaire$' → 'index.php?my_route=formulaire'

4. WordPress transforme en paramètre interne
   ↓
   my_route = formulaire

5. Hook 'template_redirect' se déclenche
   ↓
   handleCustomRoute() est appelé

6. handleCustomRoute() détecte my_route=formulaire
   ↓
   Appelle renderPageFormulaire()

7. renderPageFormulaire() :
   - Vérifie si c'est un POST ou un GET
   - Prépare les données
   - Capture la vue formulaire.php dans $template
   - Détecte le type de thème
   - Charge fse-theme.php ou classic-theme.php

8. Le layout injecte $template et affiche :
   - Header du thème
   - Notre formulaire
   - Footer du thème

9. HTML final envoyé au navigateur
```

---

## 🎓 Exercices pratiques

### Exercice 1 : Ajouter une nouvelle route

**Objectif :** Créer une page `/ma-page-perso` qui affiche "Bonjour [votre prénom]"

**Étapes :**

1. Ajouter une nouvelle règle de réécriture dans `addRewriteRules()`
2. Créer une méthode `renderMaPagePerso()` dans la classe
3. Ajouter la condition dans `handleCustomRoute()`
4. Créer une vue `views/ma-page-perso.php`
5. Désactiver puis réactiver le plugin (pour régénérer les règles)

<details>
<summary>Voir la solution</summary>

```php
// Dans addRewriteRules()
add_rewrite_rule(
    '^ma-page-perso$',
    'index.php?my_route=ma_page_perso',
    'top'
);

// Dans handleCustomRoute()
elseif ($my_route === 'ma_page_perso') {
    $this->renderMaPagePerso();
    exit;
}

// Nouvelle méthode
private function renderMaPagePerso()
{
    $data = array(
        'prenom' => 'David'
    );
    
    extract($data);

    ob_start();
    include plugin_dir_path(__FILE__) . 'views/ma-page-perso.php';
    $template = ob_get_clean();

    if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
        include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
    } else {
        include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
    }
}
```

```php
<!-- views/ma-page-perso.php -->
<div>
    <h1>Bonjour <?php echo esc_html($prenom); ?> !</h1>
    <p>Bienvenue sur ma page personnalisée.</p>
</div>
```

</details>

---

### Exercice 2 : Route avec paramètre

**Objectif :** Créer une page `/produit/[nom-produit]` qui affiche le nom du produit

**Indice :** Utilisez les parenthèses capturantes dans l'expression régulière et `$matches[1]`

<details>
<summary>Voir la solution</summary>

```php
// Dans addRewriteRules()
add_rewrite_rule(
    '^produit/([^/]+)/?$',
    'index.php?my_route=produit&product_slug=$matches[1]',
    'top'
);

// Dans addQueryVars()
public function addQueryVars($vars)
{
    $vars[] = 'my_route';
    $vars[] = 'product_slug';
    return $vars;
}

// Dans handleCustomRoute()
elseif ($my_route === 'produit') {
    $this->renderProduit();
    exit;
}

// Nouvelle méthode
private function renderProduit()
{
    $slug = get_query_var('product_slug', 'inconnu');
    
    $data = array(
        'product_name' => ucfirst(str_replace('-', ' ', $slug))
    );
    
    extract($data);

    ob_start();
    include plugin_dir_path(__FILE__) . 'views/produit.php';
    $template = ob_get_clean();

    if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {
        include plugin_dir_path(__FILE__) . 'views/fse-theme.php';
    } else {
        include plugin_dir_path(__FILE__) . 'views/classic-theme.php';
    }
}
```

```php
<!-- views/produit.php -->
<div>
    <h1>Produit : <?php echo esc_html($product_name); ?></h1>
    <p>Informations sur le produit...</p>
</div>
```

Exemple : `/produit/mon-super-produit` affichera "Produit : Mon super produit"

</details>

---

### Exercice 3 : Formulaire avec sauvegarde en base

**Objectif :** Sauvegarder les données du formulaire dans la table `wp_options`

<details>
<summary>Voir la solution</summary>

```php
private function renderPageFormulaire()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $element = sanitize_text_field($_POST['element'] ?? '');
        
        // Sauvegarder dans wp_options
        update_option('mon_element_sauvegarde', $element);
        
        $message = 'Formulaire sauvegardé avec succès !';
        $data = array(
            'message' => $message,
            'input_value' => $element
        );
    } else {
        // Récupérer la valeur sauvegardée
        $saved_value = get_option('mon_element_sauvegarde', 'Valeur par défaut');
        
        $data = array(
            'input_value' => $saved_value
        );
    }
    
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
```

</details>

---

## 🔍 Concepts WordPress à retenir

### 1. Les Hooks (Actions et Filtres)

**Actions** (`add_action`) :
- Permettent d'exécuter du code à un moment précis
- Exemples : `init`, `template_redirect`, `wp_head`

**Filtres** (`add_filter`) :
- Permettent de modifier une valeur avant qu'elle soit utilisée
- Exemples : `query_vars`, `the_content`, `the_title`

### 2. Rewrite Rules

Les règles de réécriture transforment des URLs "jolies" en paramètres de requête que WordPress comprend.

**Exemple :**
```
/produit/chaussures → index.php?my_route=produit&slug=chaussures
```

### 3. Query Vars

Variables que vous pouvez récupérer avec `get_query_var()`. Il faut les déclarer dans le filtre `query_vars`.

### 4. Template Hierarchy

WordPress cherche les templates dans un ordre précis :
1. Template spécifique (ex: `page-formulaire.php`)
2. Template générique (ex: `page.php`)
3. Template de fallback (ex: `index.php`)

### 5. Output Buffering

Technique pour capturer du HTML au lieu de l'afficher immédiatement :

```php
ob_start();        // Démarrer la capture
echo "Hello";      // N'affiche rien, stocke en mémoire
$html = ob_get_clean(); // Récupère "Hello" et vide le buffer
```

### 6. Sécurité

**Toujours nettoyer les données entrantes :**
- `sanitize_text_field()` → Pour du texte simple
- `sanitize_textarea_field()` → Pour du texte multiligne
- `sanitize_email()` → Pour les emails
- `absint()` → Pour les entiers positifs

**Toujours échapper les données sortantes :**
- `esc_html()` → Pour afficher du texte
- `esc_attr()` → Pour les attributs HTML
- `esc_url()` → Pour les URLs

---

## 🚀 Aller plus loin

### Améliorations possibles

1. **Ajouter la validation CSRF avec nonce**
   ```php
   // Dans la vue
   <?php wp_nonce_field('mon_formulaire_action', 'mon_nonce'); ?>
   
   // Dans le contrôleur
   if (!wp_verify_nonce($_POST['mon_nonce'], 'mon_formulaire_action')) {
       wp_die('Erreur de sécurité');
   }
   ```

2. **Créer une table personnalisée**
   ```php
   global $wpdb;
   $table_name = $wpdb->prefix . 'mes_donnees';
   
   $wpdb->insert($table_name, array(
       'element' => $element,
       'date' => current_time('mysql')
   ));
   ```

3. **Ajouter de la pagination**
4. **Intégrer une API externe**
5. **Créer un système d'authentification personnalisé**

---

## 📚 Ressources utiles

- [Documentation WordPress sur les Rewrite Rules](https://developer.wordpress.org/reference/functions/add_rewrite_rule/)
- [Documentation WordPress sur les Query Vars](https://developer.wordpress.org/reference/functions/get_query_var/)
- [Guide de sécurité WordPress](https://developer.wordpress.org/apis/security/)
- [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)

---

## ❓ FAQ

**Q : Pourquoi mes routes ne fonctionnent pas après modification ?**

R : Il faut régénérer les règles de réécriture. Désactivez puis réactivez le plugin, ou allez dans Réglages > Permaliens > Enregistrer.

**Q : Peut-on avoir plusieurs paramètres dans une route ?**

R : Oui ! Exemple :
```php
add_rewrite_rule(
    '^produit/([^/]+)/([^/]+)/?$',
    'index.php?my_route=produit&category=$matches[1]&slug=$matches[2]',
    'top'
);
```

**Q : Comment débugger mes règles de réécriture ?**

R : Installez le plugin "Rewrite Rules Inspector" qui vous montre toutes les règles actives.

**Q : Faut-il utiliser `extract()` ?**

R : C'est pratique mais pas obligatoire. Vous pouvez aussi passer le tableau `$data` directement à la vue et utiliser `$data['title']`.

---

## 🎯 Conclusion

Ce plugin vous donne les bases pour créer des applications web personnalisées dans WordPress tout en bénéficiant de l'écosystème WordPress (thèmes, plugins, utilisateurs, etc.).

**Architecture MVC appliquée :**
- **Modèle** : Les données (dans les contrôleurs ou via la base de données)
- **Vue** : Les fichiers dans `/views/`
- **Contrôleur** : Les méthodes `renderPage...()`

Vous pouvez maintenant créer des applications complexes tout en gardant la simplicité de WordPress !

---

**Bon développement ! 🚀**
