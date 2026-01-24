<?php
// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

$page_title = $is_edit ? 'Modifier un élément' : 'Ajouter un élément';
$button_text = $is_edit ? 'Mettre à jour' : 'Ajouter';
$name_value = $is_edit ? esc_attr($element->name) : '';
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'empty'): ?>
            <div class="notice notice-error is-dismissible">
                <p>Le nom ne peut pas être vide.</p>
            </div>
        <?php elseif ($_GET['error'] === 'toolong'): ?>
            <div class="notice notice-error is-dismissible">
                <p>Le nom ne peut pas dépasser 50 caractères.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('crud_demo_save', 'crud_demo_nonce'); ?>
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_element" value="<?php echo esc_attr($element->id_element); ?>">
        <?php endif; ?>
        
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="name">Nom <span class="description">(requis)</span></label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="<?php echo $name_value; ?>" 
                            class="regular-text" 
                            maxlength="50"
                            required
                        >
                        <p class="description">Maximum 50 caractères.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <p class="submit">
            <input 
                type="submit" 
                name="crud_demo_submit" 
                id="submit" 
                class="button button-primary" 
                value="<?php echo $button_text; ?>"
            >
            <a href="<?php echo admin_url('admin.php?page=crud-demo-listing'); ?>" class="button">Annuler</a>
        </p>
    </form>
</div>