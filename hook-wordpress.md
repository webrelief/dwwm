# Hooks WordPress - Front Office

## Hooks d'initialisation (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `muplugins_loaded` | Après le chargement des plugins "must-use" |
| `plugins_loaded` | Après le chargement de tous les plugins actifs |
| `sanitize_comment_cookies` | Après la configuration des cookies de commentaires |
| `setup_theme` | Avant que le thème soit chargé |
| `after_setup_theme` | Après que le thème a été chargé et initialisé |
| `init` | Après l'initialisation de WordPress, avant tout header HTTP |
| `widgets_init` | Quand les widgets peuvent être enregistrés |
| `wp_loaded` | Après que tous les fichiers WordPress soient chargés |

## Hooks de requête (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `parse_request` | Après que WordPress a parsé la requête |
| `send_headers` | Juste avant l'envoi des headers HTTP |
| `pre_get_posts` | Avant qu'une requête de base de données soit exécutée |
| `posts_selection` | Après la sélection des posts mais avant leur récupération |
| `wp` | Après que WordPress ait déterminé quel contenu afficher |
| `template_redirect` | Avant que WordPress détermine quel template charger |

## Hooks de template (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `template_include` | Filtre le chemin du template à inclure |
| `get_header` | Avant le chargement du fichier header.php |
| `wp_head` | Dans la section `<head>` du HTML |
| `wp_enqueue_scripts` | Pour enregistrer les scripts et styles du front-end |
| `wp_body_open` | Juste après la balise `<body>` (depuis WP 5.2) |
| `get_sidebar` | Avant le chargement du fichier sidebar.php |
| `get_search_form` | Avant le chargement du formulaire de recherche |
| `get_footer` | Avant le chargement du fichier footer.php |
| `wp_footer` | Juste avant la balise `</body>` |
| `wp_print_footer_scripts` | Pour afficher les scripts dans le footer |

## Hooks de contenu (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `the_post` | Après que les données du post soient configurées |
| `the_content` | Filtre le contenu d'un article avant affichage |
| `the_title` | Filtre le titre d'un article avant affichage |
| `the_excerpt` | Filtre l'extrait d'un article avant affichage |
| `the_permalink` | Filtre l'URL d'un article |
| `get_the_excerpt` | Filtre l'extrait lors de sa récupération |
| `post_thumbnail_html` | Filtre le HTML de l'image à la une |
| `wp_get_attachment_image_attributes` | Filtre les attributs des images |

## Hooks de navigation (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `wp_nav_menu` | Filtre le HTML d'un menu |
| `wp_nav_menu_items` | Filtre les items d'un menu |
| `nav_menu_css_class` | Filtre les classes CSS d'un item de menu |

## Hooks de commentaires (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `comment_post` | Après qu'un commentaire ait été inséré |
| `wp_insert_comment` | Après qu'un commentaire ait été ajouté |
| `preprocess_comment` | Avant qu'un commentaire soit traité |
| `comment_form_before` | Avant le formulaire de commentaire |
| `comment_form` | Début du formulaire de commentaire |
| `comment_form_after` | Après le formulaire de commentaire |
| `comment_text` | Filtre le texte d'un commentaire |

## Hooks de widgets (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `dynamic_sidebar_before` | Avant l'affichage d'une sidebar |
| `dynamic_sidebar_after` | Après l'affichage d'une sidebar |
| `widget_title` | Filtre le titre d'un widget |
| `widget_text` | Filtre le contenu d'un widget texte |

## Hooks de recherche (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `pre_get_posts` | Avant la requête de recherche (peut modifier la recherche) |
| `posts_search` | Filtre la clause WHERE de la recherche SQL |
| `the_search_query` | Filtre le terme de recherche affiché |

## Hooks AJAX (Front Office)

| Hook | Quand il est appelé |
|------|---------------------|
| `wp_ajax_nopriv_{action}` | Pour les requêtes AJAX des utilisateurs non connectés |
| `wp_ajax_{action}` | Pour les requêtes AJAX des utilisateurs connectés |

## Ordre chronologique d'une requête Front Office

1. `muplugins_loaded` - Plugins must-use chargés
2. `plugins_loaded` - Tous les plugins chargés
3. `setup_theme` - Avant le chargement du thème
4. `after_setup_theme` - Thème chargé
5. `init` - WordPress initialisé
6. `widgets_init` - Widgets prêts à être enregistrés
7. `wp_loaded` - Tout WordPress est chargé
8. `parse_request` - Requête parsée
9. `send_headers` - Headers HTTP envoyés
10. `pre_get_posts` - Avant requête DB
11. `posts_selection` - Posts sélectionnés
12. `wp` - Contenu déterminé
13. `template_redirect` - Avant choix du template
14. `template_include` - Template choisi
15. `get_header` - Avant header.php
16. `wp_head` - Dans le `<head>`
17. `wp_enqueue_scripts` - Enregistrement scripts/styles
18. `wp_body_open` - Après `<body>`
19. `the_post` - Configuration des données du post
20. `the_content` / `the_title` - Affichage du contenu
21. `get_sidebar` - Avant sidebar.php
22. `get_footer` - Avant footer.php
23. `wp_footer` - Avant `</body>`
24. `shutdown` - Fin du traitement

---

## Hooks Back Office (Administration)

Pour information, voici les principaux hooks d'administration :

| Hook | Quand il est appelé |
|------|---------------------|
| `admin_init` | Initialisation de l'interface d'administration |
| `admin_menu` | Création du menu d'administration |
| `admin_enqueue_scripts` | Enregistrement scripts/styles admin |
| `admin_head` | Dans le `<head>` des pages admin |
| `admin_notices` | Notifications dans l'admin |
| `admin_footer` | Dans le footer des pages admin |
| `save_post` | Après sauvegarde d'un article |
| `publish_post` | Après publication d'un article |
| `pre_get_posts` | Avant requête DB (aussi disponible en admin) |
| `load-{page_hook}` | Avant le chargement d'une page admin spécifique |
| `admin_post_{action}` | Pour traiter les soumissions de formulaires admin |

## Ordre chronologique d'une requête Back Office

1. `muplugins_loaded` - Plugins must-use chargés
2. `plugins_loaded` - Tous les plugins chargés
3. `setup_theme` - Avant le chargement du thème
4. `after_setup_theme` - Thème chargé
5. `init` - WordPress initialisé
6. `widgets_init` - Widgets prêts à être enregistrés
7. `wp_loaded` - Tout WordPress est chargé
8. `admin_menu` - Construction du menu d'administration
9. `admin_init` - Initialisation de l'admin (enregistrement settings, etc.)
10. `current_screen` - L'écran admin actuel est déterminé
11. `load-{page_hook}` - Avant le chargement d'une page admin spécifique
12. `admin_enqueue_scripts` - Enregistrement des scripts/styles admin
13. `pre_get_posts` - Avant les requêtes de posts (listes d'articles, etc.)
14. `admin_head` - Dans le `<head>` de la page admin
15. `admin_notices` / `all_admin_notices` - Zone pour afficher les notifications
16. **Contenu de la page admin s'affiche**
17. `admin_footer` - Dans le footer de la page admin
18. `admin_print_footer_scripts` - Scripts dans le footer admin
19. `shutdown` - Fin du traitement

### Hooks spécifiques aux actions admin

Lors de la sauvegarde/modification de contenu :

1. `save_post_{post_type}` - Avant sauvegarde (post type spécifique)
2. `save_post` - Avant sauvegarde (tous les types)
3. `wp_insert_post` - Insertion/mise à jour dans la DB
4. `edit_post` - Après modification d'un post
5. `publish_post` - Si le post est publié
6. `transition_post_status` - Changement de statut du post
