<?php
/*
Plugin Name: LearnDash Move Users Between Groups
Plugin URI: https://alienmartinez.com/
Description: Este plugin permite mover usuarios entre grupos de LearnDash.
Version: 1.0
Author: Alien Martinez
Author URI: https://alienmartinez.com/
License: GPL2
*/

add_action('admin_menu', 'ld_move_users_between_groups_menu');


function ld_move_users_between_groups_menu() {
    add_submenu_page(
        'learndash-lms',
        'Mover Usuarios Entre Grupos',
        'Mover Usuarios',
        'manage_options',
        'ld-move-users-between-groups',
        'ld_move_users_between_groups_page'
    );
}

function ld_move_users_between_groups_page() {
    // Comprueba si el usuario actual tiene permisos para acceder a esta página
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes suficientes permisos para acceder a esta página.'));
    }

    // Procesa el formulario si se ha enviado
    if (isset($_POST['submit'])) {
        $source_group = intval($_POST['source_group']);
        $destination_group = intval($_POST['destination_group']);

        if ($source_group != $destination_group) {
            $source_group_users = learndash_get_groups_user_ids($source_group);

            foreach ($source_group_users as $user_id) {
                learndash_set_users_group_ids($user_id, array($destination_group));
            }

            echo '<div id="message" class="updated notice is-dismissible"><p>Usuarios movidos con éxito entre grupos.</p></div>';
        } else {
            echo '<div id="message" class="error notice is-dismissible"><p>El grupo de origen y el grupo de destino deben ser diferentes.</p></div>';
        }
    }

    // Obtén todos los grupos de LearnDash
    $args = array(
        'post_type' => 'groups',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $groups = get_posts($args);

// Crea el formulario
echo '<h2>Mover Usuarios Entre Grupos LearnDash</h2>';
echo '<form method="post" action="">';
echo '<table class="form-table">';
echo '<tr>';
echo '<th scope="row"><label for="source_group">Grupo de origen:</label></th>';
echo '<td><select name="source_group" class="regular-text">';
foreach ($groups as $group) {
    echo '<option value="' . $group->ID . '">' . $group->post_title . '</option>';
}
echo '</select></td>';
echo '</tr>';
echo '<tr>';
echo '<th scope="row"><label for="destination_group">Grupo de destino:</label></th>';
echo '<td><select name="destination_group" class="regular-text">';
foreach ($groups as $group) {
    echo '<option value="' . $group->ID . '">' . $group->post_title . '</option>';
}
echo '</select></td>';
echo '</tr>';
echo '</table>';
echo '<p class="submit">';
echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Mover Usuarios">';
echo '</p>';
echo '</form>';
}
