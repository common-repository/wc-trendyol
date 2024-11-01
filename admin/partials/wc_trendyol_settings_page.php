<?php
	global $trendyol_admin, $trendyol_metas, $trendyol_adapter, $trendyol_wc_adapter, $trendyol_product_sync_admin;

	wp_enqueue_style($trendyol_admin->plugin_name.'-settings-css', WC_TRENDYOL_DIR_URL.'/admin/assets/css/settings.css', [], $trendyol_admin->version, 'all');
	wp_enqueue_script($trendyol_admin->plugin_name.'-settings-js', WC_TRENDYOL_DIR_URL.'/admin/assets/js/settings.js', [], $trendyol_admin->version);

	$menus = $trendyol_admin->wc_trendyol_setting_menu();

	$license_control = $trendyol_admin->license
?>

<div class="wc_trendyol_settings_container">

    <div class="wc_trendyol_settings_sidebar">

        <div class="wc_trendyol_settings_logo">
            <img src="//hayatikodla.net/assets/hayatikodla-logo.png" alt="Hayatı Kodla" width="150">
        </div>
        <div class="wc_trendyol_settings_plugin_name">
            Woocommerce <br>
            <div class="wc_trendyol_badget">Trendyol Entegrasyonu</div>
        </div>
        <div class="wc_trendyol_version">
            V<?=WC_TRENDYOL_VERSION?>
        </div>

        <ul class="wc_trendyol_settings_menus">
			<?php
				foreach($menus as $id => $menu_name){

					$link = strstr($menu_name['link'], 'http') ? $menu_name['link'] : '#'.$menu_name['link'];

					?>
                    <li data-menu_name="<?=$menu_name['link']?>">
                        <a href="<?=$link?>"><?=$menu_name['icon'] ?? ''?> <?=$menu_name['title'] ?? ''?></a>
                    </li>
					<?php
				}
			?>
        </ul>

    </div>
    <div class="wc_trendyol_settings_content">

		<?php
			foreach($menus as $menu_name => $menu){
				?>
                <div class="wc_trendyol_tab_content" data-menu_name="<?=$menu_name?>">

                    <div class="wc_trendyol_content_header">
                        <div class="wc_trendyol_content_title">
							<?php
								echo ($menu['icon'] ?? '').' '.($menu['title'] ?? '');
							?>
                        </div>
                        <div class="wc_trendyol_content_title_help">
							<?php
								echo $menu['help_text'] ?? '';
							?>
                        </div>
                    </div>

                    <div class="wc_trendyol_content_container">
						<?php
							if(isset($menu['type']) and $menu['type'] == 'orj'){
								$path = WC_TRENDYOL_DIR_PATH.'/admin/partials/settings_tabs/'.$menu_name.'.php';
							}
							else{
								$path = $menu['path'] ?? 'XX';
							}

							if(file_exists($path)){
								require $path;
							}
							else{
								_e('Sayfa Bulunamadı');
							}
						?>
                    </div>

                </div>
				<?php
			}
		?>

    </div>

</div>