<div class="wrap rtrs-settings">
    <?php
    settings_errors();
    self::show_messages();
    ?>

    <h2 class="nav-tab-wrapper">
        <?php
        $edit_url = admin_url('admin.php?page=rtrs-settings');
        foreach ( $this->tabs as $slug => $title ) {
            $class = "nav-tab nav-".$slug;
            if ( $this->active_tab == $slug ) {
                $class .= ' nav-tab-active';
            }
            echo '<a href="'.esc_url( $edit_url ).'&tab=' . esc_attr( $slug ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $title ) . '</a>';
        }
        ?>
    </h2> 

    <?php
        if (!empty($this->subtabs)) {
            echo '<ul class="subsubsub">';
            $array_keys = array_keys($this->subtabs);
            foreach ($this->subtabs as $id => $label) { 
                echo '<li><a href="' . admin_url('admin.php?page=rtrs-settings') . '&tab=' . $this->active_tab . '&section=' . sanitize_title($id) . '" class="' . ($this->current_section == $id ? 'current' : '') . '">' . $label . '</a> ' . (end($array_keys) == $id ? '' : '|') . ' </li>';
            }
            echo '</ul><br class="clear" />';
        }
    ?>

    <form method="post" action="">
        <?php
        do_action( 'rtrs_admin_settings_groups', $this->active_tab, $this->current_section );
        wp_nonce_field( 'rtrs-settings' ); 
        if ( $this->active_tab != 'support' ) {
            submit_button();
        } 
        ?>
    </form>  
</div>