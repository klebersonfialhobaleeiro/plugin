<?php
/*
Plugin Name: Tamanho
Description: receber o tamanho da camisa selecionada pelo user
Version: 1.0
Author: Kléberson Fialho
*/

function tamanho_painel() {
    add_menu_page(
        'tamanho do pedido', // Título da página
        'Config tamanho pedido', // Título do menu
        'manage_options', // Permissão necessária
        'configuracoes-tamanho-pedido', // Slug da página
        'painel_conteudo_tamanho', // Função para exibir o conteúdo
        'dashicons-admin-generic', // Ícone do menu
        21 // Posição no menu
    );
    add_submenu_page(
        'configuracoes-tamanho-pedido', // Slug da página pai
        'Registrar tamanhos', // Título da página
        'Registrar tamanhos', // Título do menu
        'manage_options', // Permissões necessárias
        'registrar_tamanhos_personalizados', // Slug da página
        'painel_conteudo_opcoes_tamanho_personalizado' // Função que irá exibir o conteúdo da página
    );

}

function painel_conteudo_tamanho() {
    ?>
    <form action="" method="post">
        <p>
            <label for="tamanho">Tamanho</label>
            <input type="text" id="tamanho" name="tamanho" required>
        </p>
        <input type="submit" name="submit" value="Salvar">
    </form>
    
    <?php

    if(isset($_POST['submit'])) {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = 'tamanhos_pedido';

        $sql = "CREATE TABLE $table_name (
        id int AUTO_INCREMENT NOT NULL,
        tamanho varchar(255) NOT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;";


        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $tamanho = sanitize_text_field($_POST['tamanho']);

        $data = array(
            'tamanho' => $tamanho,
        );

        $wpdb->insert($table_name, $data);
    }
    
}

function painel_conteudo_opcoes_tamanho_personalizado() {
    ?> 

    <form action="" method="post">
        <p>
            <label for="tamanho">medida</label>
            <input type="text" id="medida" name="medida" required>
        </p>
        <input type="submit" name="submit" value="Salvar">
    </form>
    
    <?php

    if(isset($_POST['submit'])) {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = 'tamanhos_medidas';

        $sql = "CREATE TABLE $table_name (
        medida varchar(255) NOT NULL,
        PRIMARY KEY (medida)
        ) $charset_collate;";


        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $medida = sanitize_text_field($_POST['medida']);

        $data = array(
            'medida' => $medida,
        );

        $wpdb->insert($table_name, $data);
    }
}


function selecionar_tamanho() {
    ?> 
    <h1>Selecionar Tamanho</h1>
    <form action="." method="post">
    <select id="tamanho" name="tamanho">
        <?php
        global $wpdb;
        $table_name = 'tamanhos_pedido';
        $rows = $wpdb->get_results("SELECT * FROM $table_name");
        foreach($rows as $row) {
            ?>
            <option value=<?php echo $row->tamanho; ?>><?php echo $row->tamanho; ?></option>
            <?php
        }
        ?>
        <option value="PERSONALIZADO" id="PERSONALIZADO">PERSONALIZADO</option>
    </select>
    
    <fieldset id="tamanho_personalizado" style="display:none">
        <?php
        global $wpdb;
        $table_name = 'tamanhos_medidas';
        $rows = $wpdb->get_results("SELECT * FROM $table_name");
        foreach($rows as $row) {
            ?>
            <p>
                <label for=<?php echo $row->medida; ?>> <?php echo $row->medida; ?> </label>
                <input type="number" name=<?php echo $row->medida; ?> >
            </p>
            <?php
        }
        ?>
    </fieldset>
    <input type="submit" value="submit" name="submit">
    <script>
        const select = document.querySelector("#tamanho");
        const personalizado = document.querySelector("#PERSONALIZADO");
        const input = document.querySelector("#tamanho_personalizado");

        select.addEventListener("change", function() {
            if (select.value === personalizado.value) {
                input.style.display = "block";
            } else {
                input.style.display = "none";
            }
        });
    </script>

    </form>

    <?php
    
    if (isset($_POST['submit'])) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'camisa';

        $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        tamanho varchar(50),
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        $tamanho = sanitize_text_field($_POST['tamanho']);
        if ( $tamanho == "PERSONALIZADO" ) {
            $medidas = $wpdb->get_results("SELECT * FROM tamanhos_medidas");
            $tamanho = "";
            foreach($rows as $row) {
                $tamanho .= $row->medida . " = " . $_POST[$row->medida] . "; ";
            }

        }

        $data = array(
            'tamanho' => $tamanho,
        );

        $wpdb->insert($table_name, $data);
    }
}


add_action('admin_menu', 'tamanho_painel');
add_shortcode('selecionar_tamanho', 'selecionar_tamanho');