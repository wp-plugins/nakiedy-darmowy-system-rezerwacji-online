<?php
$wpn_class = new Nakiedy_Rezerwacje();
?>
<div class="wrap" style="position: fixed;
  width: 85%;
  top: 40px;
  bottom: 80px;">
  <h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url('../images/nakiedy-logo.png', __FILE__);?>" alt=""></span>
    <?php echo __( 'Kalendarz rezerwacji', 'wp-analytify' ); ?>
  </h2>
    <?php if (get_option( 'nakiedy_token' )):?>
    <p>Kliknij przycisk poniżej i przejdź do kalendarza rezerwacji:</p>
    <a href="http://app.nakiedy.com/iframe/<?php echo get_option( 'nakiedy_token' )?>" target="_blank" class="button-primary">Zarządzaj rezerwacjami</a>
    <?php else:?>
        <p>Przejdź do <a href="admin.php?page=wpn-settings">ustawień</a> i wpisz <span style="color:#0073aa">klucz API</span></p>
    <?php endif;?>
</div>