<?php   $wpn = new Nakiedy_Rezerwacje();?>
<div class="wrap">
    <h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url('../images/nakiedy-logo.png', __FILE__);?>" alt=""></span>
        <?php echo __( 'Pomoc', 'wp-analytify'); ?>
    </h2>
<?php
    if (isset($update_message)) echo $update_message;

    if ( isset ( $_GET['tab'] ) ) $wpn->pa_help_tabs($_GET['tab']);
    else
        $wpn->pa_help_tabs( 'step1' );

    if ( isset ( $_GET['tab'] ) )
        $tab = $_GET['tab'];
    else
        $tab = 'step1';
    // Authentication Tab section
  if( $tab == 'step1' ) {
?>
    <table width="1004" class="form-table">
        <tbody>
        <tr>
            <td colspan="2" class="help">
                <p style="font-size: 16px;padding-bottom: 10px;"><span style="color:#E46500;">Krok 1</span> - autoryzacja kluczem API<p>
                <ol>
                    <li>Zaloguj się do panelu Nakiedy: <a href="http://www.nakiedy.com/zaloguj.html" target="_blank">Zaloguj się</a> <br>lub<br> jeśli nie masz konta <a href="http://www.nakiedy.com/zarejestruj.html" target="_blank">Zarejestruj się</a></li>
                    <li><p>Przejdź do zakładki <a href="http://app.nakiedy.com/settings/services" target="_blank">moja firma</a></p><img src="<?php echo plugins_url('../images/moja_firma.jpg', __FILE__);?>" /></li>
                    <li><p>Wybierz <a href="http://app.nakiedy.com/settings/general" target="_blank">Ustawienia>System</a> z lewego menu</p><img src="<?php echo plugins_url('../images/ustawienia.gif', __FILE__);?>" /></li>
                    <li>Znajdź pole: <span style="color:#0073aa">klucz API</span></li>
                    <li>Przekopiuj kod i wklej w pole powyżej: Wstaw <span style="color:#0073aa">klucz API</span></li>
                    <li>Zapisz</li>
                </ol>
            </td>
        </tr>
        </tbody>
    </table>
      Przejdź do <a href="admin.php?page=wpn-help&tab=step2">Kroku 2 - Wyświetlanie</a>
        <?php }
else{?>
    <table width="1004" class="form-table">
        <tbody>
        <tr>
            <td colspan="2" class="help">
                <p style="font-size: 16px;padding-bottom: 10px;"><span style="color:#E46500;">Krok 2</span> - wyświetlanie systemu rezerwacji<p>
                <ol>
                    <li><p>Przejdź do zakładki Strony</p> <img src="<?php echo plugins_url('../images/strony.gif', __FILE__);?>" /></li>
                    <li>Dodaj nową Stronę lub edytuj istniejącą w Wordpress</li>
                    <li><p>W prawym panelu pojawił się box "Rezerwacje". Jeśli chcesz aby na danej stronie pojawiły się rezerwację, zmień na Tak i zapisz podstronę</p> <img src="<?php echo plugins_url('../images/rezerwacje_box.gif', __FILE__);?>" /></li>
                    <li><p>W edytorze strony pojawi się grafika, pokazująca w którym jej miejscu, wtyczka będzie widoczna</p> <img src="<?php echo plugins_url('../images/nakiedy-spacer.jpg', __FILE__);?>" width="200" /></li>
                    <li>Przejdź do poglądu strony i sprawdź czy szerokość i wysokość jest właściwa. Jeśli nie jest, przejdź do Rezerwacje>Ustawienia - zakładka Wygląd i dostosuj jej rozmiar</li>
                </ol>
            </td>
        </tr>
        </tbody>
    </table>
<?php }?>
</div>