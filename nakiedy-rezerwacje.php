<?php
/*
  Plugin Name: Nakiedy - System rezerwacji
  Plugin URI: http://www.nakiedy.com
  Description: Darmowa wtyczka do rezerwacji wizyt oprarta na systemie rezerwacji online Nakiedy. Aby z niej korzystać wejdź na www.nakiedy.com i <a href="http://http://www.nakiedy.com/zarejestruj.html" target="_blank">zarejestruj konto</a>. Instalacja: 1) Kliknij odnośnik "Włącz" po lewej od tego opisu, 2) <a href="http://http://www.nakiedy.com/zarejestruj.html" target="_blank">Zarejestruj się, by zdobyć klucz API do Nakiedy</a>, oraz 3) Przejdź na stronę ustawień nowej pozycji w menu "Rezerwacje" i wprowadź swój klucz API. Teraz możesz na dowolnej podstronie umieścić system rezerwacji.
  Version: 1.0
  Author: System rezerwacji Nakiedy
  Author URI: http://www.nakiedy.com
  License: GPLv2+
  Text Domain: nakiedy-rezerwacje
*/
ini_set( 'include_path', dirname(__FILE__) . '/lib/' );
define( 'ROOT_PATH', dirname(__FILE__) );

class Nakiedy_Rezerwacje{

    // Constructor
    function __construct() {

        add_action( 'admin_menu', array( $this, 'wpn_add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wpn_styles') );

        //add meta box on page
        add_action( 'add_meta_boxes', array( $this,'wpn_add_meta_box') );
        add_action( 'save_post', array( $this,'wpn_save_meta_box_data') );

        add_filter( 'the_content', array( $this,'wpn_content_filter'), 0 );

        add_filter( 'content_edit_pre', array( $this,'wpn_change_wysiwyg') );

        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
    }

    /*
      * Actions perform at loading of admin menu
      */
    function wpn_add_menu() {

        add_menu_page( 'Nakiedy Rezerwacje', 'Rezerwacje', 'manage_options', 'wpn-dashboard', array(
                          __CLASS__,
                         'wpn_page_file_path'
                        ), plugins_url('images/nakiedy-logo.png', __FILE__),'2.2.9');

        add_submenu_page( 'wpn-dashboard', 'Nakiedy Rezerwacje' . ' Kalendarz', '<b style="color:#E46500;">Kalendarz</b>', 'manage_options', 'wpn-dashboard', array(
                              __CLASS__,
                             'wpn_page_file_path'
                            ));

        add_submenu_page( 'wpn-dashboard', 'Nakiedy Rezerwacje' . ' Ustawienia', 'Ustawienia', 'manage_options', 'wpn-settings', array(
                              __CLASS__,
                             'wpn_page_file_path'
                            ));
        add_submenu_page( 'wpn-dashboard', 'Nakiedy Rezerwacje' . ' Pomoc', 'Pomoc', 'manage_options', 'wpn-help', array(
                        __CLASS__,
                        'wpn_page_file_path'
                    ));
    }

    /*
     * Actions perform on loading of menu pages
     */
    static function wpn_page_file_path() {
      
        $screen = get_current_screen();

        if ( strpos( $screen->base, 'wpn-settings' ) !== false ) {
            include( dirname(__FILE__) . '/includes/wpn-settings.php' );
        }
        elseif ( strpos( $screen->base, 'wpn-help' ) !== false ) {
            include( dirname(__FILE__) . '/includes/wpn-help.php' );
        }
        else {
            include( dirname(__FILE__) . '/includes/wpn-dashboard.php' );
        }

    }

    public function pa_settings_tabs( $current = 'authentication' ) {
            $tabs=array( 'settings'       =>  'Wygląd',
                'authentication' =>  'Autoryzacja'
            );
            if (!get_option('nakiedy_url'))
                      unset($tabs['settings']);

            echo '<div class="left-area">';

            echo '<div id="icon-themes" class="icon32"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';

            foreach( $tabs as $tab => $name ) {

                $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=wpn-settings&tab=$tab'>$name</a>";
            }

            echo '</h2>';
    }

    public function pa_help_tabs( $current = 'step1' ) {
        $tabs=array( 'step1'       =>  'Krok 1 - Autoryzacja',
            'step2' =>  'Krok 2 - Wyświetlanie'
        );

        echo '<div class="left-area">';

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';

        foreach( $tabs as $tab => $name ) {

            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=wpn-help&tab=$tab'>$name</a>";
        }

        echo '</h2>';
    }


    public function wpn_save_data( $nakiedy_token ) {

        if ( !class_exists( 'RestClient' ) ) {
            require_once dirname(__FILE__) . '/lib/restclient.php';
        }

        $api = new RestClient(array(
            'base_url' => "http://api.nakiedy.com/v1/api",
            'format' => "json",
            'headers' => array('apiKey' => $nakiedy_token),
        ));
        $result = $api->get("wordpress",array('secret'=>$nakiedy_token));
        if($result->info->http_code == 200){
            $res=$result->decode_response();
            $nakiedy_url=$res->url;
            update_option( 'nakiedy_token', $nakiedy_token );
            update_option( 'nakiedy_url', $nakiedy_url);
            update_option( 'nakiedy_width', 800);
            update_option( 'nakiedy_height', 600);
            return true;
        }
        return $result->info->http_code;
    }

    /**
     * Adds the meta box to the page admin screen
     */
    function wpn_add_meta_box()
    {
        add_meta_box(
            'wpn-meta-box', // id, used as the html id att
            '<img src="'.plugins_url('images/nakiedy-logo.png', __FILE__).'" style="vertical-align: middle;"/> '.__('Rezerwacje'), // meta box title, like "Page Attributes"
            array( $this,'wpn_meta_box_cb'), // callback function, spits out the content
            'page', // post type or page. We'll add this to pages only
            'side', // context (where on the screen
            'core' // priority, where should this go in the context?
        );
    }
    /**
     * Callback function for our meta box.  Echos out the content
     */
    function wpn_meta_box_cb($post)
    {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'wpn_meta_box', 'wpn_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $value = get_post_meta( $post->ID, 'wpn_meta_value_key', true );

        echo '<label for="myplugin_new_field">';
        _e( 'Strona rezerwacji', 'myplugin_textdomain' );
        echo '</label> ';
        echo '<select name="wpn_page_show">
          <option value="0" '.(empty($value)?'selected':'').'>'.__('Nie').'</option>
          <option value="1" '.(!empty($value)?'selected':'').'>'.__('Tak').'</option>
        </select>';
    }

    function wpn_save_meta_box_data( $post_id ) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['wpn_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['wpn_meta_box_nonce'], 'wpn_meta_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST['wpn_page_show'] ) ) {
            return;
        }

        // Sanitize user input.
        $my_data = sanitize_text_field($_POST['wpn_page_show']);

        if ($my_data==0){
            $content_post = get_post($post_id);
            $content = $content_post->post_content;
            $dom=new DOMDocument();
            @$dom->loadHtml($content);
            $image=$dom->getElementById('nakiedy-tag');
            if ($image){
                $image->parentNode->removeChild($image);
                $content=$dom->saveHTML();
                wp_update_post(array('ID'=>$post_id,'post_content'=>$content));
            }
        }

        // Update the meta field in the database.
        update_post_meta( $post_id, 'wpn_meta_value_key', $my_data );
    }

    function wpn_content_filter( $content ) {

        if ( is_singular('page') ){

            global $post;
            $value = get_post_meta( $post->ID, 'wpn_meta_value_key', true );

            if (!empty($value)){
                $dom=new DOMDocument();
                @$dom->loadHtml($content);
                $image=$dom->getElementById('nakiedy-tag');
                if (get_option('nakiedy_url') && isset($image)){
                    $link= $dom->createElement('iframe');
                    $link->setAttribute('src','http://'.get_option('nakiedy_url').'/em/?h='.((int)get_option('nakiedy_height')-270));
                    $link->setAttribute('id','nakiedyWidget');
                    $link->setAttribute('style','width: '.get_option('nakiedy_width').'px; height: '.get_option('nakiedy_height').'px; border:0;');
                    $image->parentNode->replaceChild($link, $image);
                    $content=$dom->saveHTML();
                }
                elseif (isset($image)){
                    $src=plugins_url('images/nakiedy-spacer-configure.jpg', __FILE__);
                    $image->setAttribute( 'src' , $src );
                    $content=$dom->saveHTML();
                }
            }
        }
        // Returns the content.
        return $content;
    }
    function wpn_change_wysiwyg($content) {
        global $post;
        if ( $post->post_type=='page'){
            $value = get_post_meta( $post->ID, 'wpn_meta_value_key', true );
            $img_file=plugins_url('images/nakiedy-spacer.jpg', __FILE__);
            $img='<img src="'.$img_file.'" alt="System rezerwacji Nakiedy" id="nakiedy-tag"/>';
            if (!empty($value)){
                if (!strpos($content,'id="nakiedy-tag"')){
                $content=$content.$img;
                }
            }else{
                if (strpos($content,'id="nakiedy-tag"')){
                    $dom=new DOMDocument();
                    @$dom->loadHtml($content);
                    $image=$dom->getElementById('nakiedy-tag');
                    $image->parentNode->removeChild($image);
                    $content=$dom->saveHTML();
                }
            }
        }
        return $content;

    }
    /**
     * Styling: loading stylesheets for the plugin.
     */
    public function wpn_styles( $page ) {

        wp_enqueue_style( 'wpn-style', plugins_url('css/wpn-style.css', __FILE__));
    }

    /*
     * Actions perform on activation of plugin
     */
    function wpa_install() {
      


    }

    /*
     * Actions perform on de-activation of plugin
     */
    function wpa_uninstall() {

        delete_option( 'nakiedy_token');
        delete_option( 'nakiedy_url');
        delete_option( 'nakiedy_width');
        delete_option( 'nakiedy_height');
        delete_post_meta_by_key( 'wpn_meta_value_key' );

        $allposts = get_posts( 'numberposts=-1&post_type=page&post_status=any' );

        if (!empty($allposts) && is_array($allposts))
            foreach ($allposts as $post){
                if (strpos($post->post_content,'id="nakiedy-tag"')){
                    $dom=new DOMDocument();
                    @$dom->loadHtml($post->post_content);
                    $image=$dom->getElementById('nakiedy-tag');
                    $image->parentNode->removeChild($image);
                    $post->post_content=$dom->saveHTML();
                    wp_update_post($post);
                }

            }
    }

}

new Nakiedy_Rezerwacje();
?>