<?php
  $wpn = new Nakiedy_Rezerwacje();

    if (! function_exists( 'curl_init' ) ) {
        esc_html_e('Wtyczka wymaga biblioteki CURL PHP');
        return false;
    }

    if (! function_exists( 'json_decode' ) ) {
        esc_html_e('Wtyczka wymaga biblioteki JSON');
        return false;
    }

  // Save access code
  if ( isset( $_POST["save_code"]) and isset($_POST["nakiedy_token"]) ) {

    $res=$wpn->wpn_save_data(  sanitize_text_field($_POST["nakiedy_token"]) );
    if($res===true){
        wp_safe_redirect('admin.php?page=wpn-dashboard');
        exit();
    }
      else{
          $update_message = '<div id="setting-error-settings_updated" class="updated settings-error below-h2"><p><strong>Błędny klucz API lub brak możliwości połączenia ('.$res.'). W razie problemów prosimy o kontakt (kontakt@nakiedy.com)</strong></p></div>';
      }
  }
    // Clear Authorization and other data
  if (isset($_POST[ "clear" ])) {

        delete_option( 'nakiedy_token' );
        delete_option( 'nakiedy_url' );
        $update_message = '<div id="setting-error-settings_updated" class="updated settings-error below-h2">
                            <p><strong>Wstaw nowy klucz API.</strong></p></div>';
  }

    if (isset($_POST[ "save_size" ])) {

        update_option( 'nakiedy_width', (int)$_POST[ "nakiedy_width" ]);
        update_option( 'nakiedy_height', (int)$_POST[ "nakiedy_height" ]);
        $update_message = '<div id="setting-error-settings_updated" class="updated settings-error below-h2">
                                <p><strong>Zmiany zapisane.</strong></p></div>';
    }


if (isset($_GET['noheader']))
    require_once(ABSPATH . 'wp-admin/admin-header.php');

?>
<div class="wrap">

  <h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url('../images/nakiedy-logo.png', __FILE__);?>" alt=""></span>
    <?php echo __( 'Ustawienia', 'wp-analytify'); ?>
  </h2>

  
  <?php
  if (isset($update_message)) echo $update_message;
  
  if ( isset ( $_GET['tab'] ) ) $wpn->pa_settings_tabs($_GET['tab']);
  elseif (get_option('nakiedy_url'))
      $wpn->pa_settings_tabs( 'settings' );
  else
      $wpn->pa_settings_tabs( 'authentication' );

  if ( isset ( $_GET['tab'] ) ) 
    $tab = $_GET['tab'];
  elseif (get_option('nakiedy_url'))
    $tab = 'settings';
  else
    $tab = 'authentication';
  // Authentication Tab section
  if( $tab == 'authentication' ) {
  ?>

  <form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&noheader=true" method="post" name="settings_form" id="settings_form">
    <table width="1004" class="form-table">
      <tbody>
      <?php if( get_option( 'nakiedy_token' ) ) { ?>
        <tr>
          <p>Chcesz wpisać nowy klucz API? Kliknij Reset i wpisz nowy klucz.<p>
          
        </tr>
        <tr>
          <th><?php esc_html_e( 'Wyczyść Klucz API', 'wp-analytify' ); ?></th>
          <td><input type="submit" class="button-primary" value="Reset" name="clear" /></td>
        </tr>
      <?php 
      }
      else { ?>
        <tr>
              <th>Wstaw <span style="color:#0073aa">klucz API</span>: </th>
              <td>
                <input type="text" name="nakiedy_token" value="" style="width:350px;"/>
              </td>
        </tr>
        <tr>
          <th></th>
          <td>
            <p class="submit">
              <input type="submit" class="button-primary" value = "<?php echo __('Zapisz');?>" name = "save_code" />
            </p>

          </td>
        </tr>
      <?php } ?>
      <tr>
          <td colspan="2" class="help">
          <p style="font-size: 15px;">Nie wiesz skąd wziąć <span style="color:#0073aa">klucz API</span>? Przejdź do <a href="admin.php?page=wpn-help">Pomocy</a><p>
          </td>
      </tr>
      <tr>
          <th colspan="2">
              <span style="font-size: 15px; line-height: 2.3;">Nie masz jeszcze konta <span style="color:#E46500;">Nakiedy</span>?? </span><a href="http://www.nakiedy.com/zarejestruj.html" target="_blank" class="button media-button button-large">Wypóbuj za darmo</a>
          </th>
      </tr>
      </tbody>
    </table>
  </form>
  <?php
  } // endif
  else{
      $nakiedy_width=get_option('nakiedy_width');
      $nakiedy_height=get_option('nakiedy_height');
      ?>
    <?php if( get_option('nakiedy_token') ): ?>
  <form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&noheader=true" method="post" name="settings_form" id="settings_form">
      <table width="1004" class="form-table">
          <tbody>

          <tr>
              <p>Ustaw wielkość okna rezerwacji na stronie<p>
          </tr>
          <tr>
              <th><?php esc_html_e('Szerokość:')?> </th>
              <td>
                  <input type="text" name="nakiedy_width" value="<?php echo empty($nakiedy_width)?'800':get_option('nakiedy_width');?>" style="width:80px;"/> px
              </td>
          </tr>
          <tr>
              <th><?php esc_html_e('Wysokość:')?> </th>
              <td>
                  <input type="text" name="nakiedy_height" value="<?php echo empty($nakiedy_height)?'600':get_option('nakiedy_height');?>" style="width:80px;"/> px
              </td>
          </tr>
          <tr>
              <th></th>
              <td>
                  <p class="submit">
                      <input type="submit" class="button-primary" value = "<?php echo __('Zapisz');?>" name = "save_size" />
                  </p>

              </td>
          </tr>
          </tbody>
    </table>
  </form>
  <h3>Podgląd</h3>
  <iframe src="http://<?php echo get_option('nakiedy_url')?>/em?h=<?php echo $nakiedy_height-270;?>" id="nakiedyWidget" style="width: <?php echo $nakiedy_width;?>px; height: <?php echo $nakiedy_height;?>px; border:0;"></iframe>
    <?php endif;?>
<?php    }?>
</div>
</div>