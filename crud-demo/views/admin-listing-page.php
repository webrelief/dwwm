<?php
// Sécurité
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">CRUD Démo - Liste des éléments</h1>
    
    <a href="<?php echo admin_url('admin.php?page=crud-demo-form'); ?>" class="page-title-action">Ajouter</a>
    
    <hr class="wp-header-end">
    
    <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
        <div class="notice notice-success is-dismissible">
            <p>Élément ajouté avec succès.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="notice notice-success is-dismissible">
            <p>Élément supprimé avec succès.</p>
        </div>
    <?php endif; ?>
    
    <?php if (empty($elements)): ?>
        <p>Aucun élément trouvé.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary" style="width: 80px;">ID</th>
                    <th scope="col" class="manage-column">Nom</th>
                    <th scope="col" class="manage-column" style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($elements as $element): ?>
                    <tr>
                        <td class="column-primary" data-colname="ID">
                            <strong><?php echo esc_html($element->id_element); ?></strong>
                        </td>
                        <td data-colname="Nom">
                            <?php echo esc_html($element->name); ?>
                        </td>
                        <td data-colname="Actions">
                            <a href="<?php echo admin_url('admin.php?page=crud-demo-form&id=' . $element->id_element); ?>" 
                               class="button button-small">
                                Modifier
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=crud-demo-listing&action=delete&id=' . $element->id_element), 'delete_element_' . $element->id_element); ?>" 
                               class="button button-small button-link-delete">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>