<div class="wc_trendyol_other_plugins">

	<?php
		global $trendyol_metas, $trendyol_admin;

		$response = wp_remote_get(WC_TRENDYOL_API_URL.'/other_plugins.php');
		if(is_array($response) && !is_wp_error($response)){
			$headers                = $response['headers']; // array of http header lines
			$get_other_plugins      = $response['body']; // use the content
			$get_other_plugins_json = json_decode($get_other_plugins);
			if(strlen($get_other_plugins) > 0 and json_last_error() === JSON_ERROR_NONE){

				if(!function_exists('is_plugin_active')){
					require_once ABSPATH.'wp-admin/includes/plugin.php';
				}

				foreach($get_other_plugins_json as $plugin_slug => $plugin){

					$plugin_path_file = $plugin_slug.'/'.$plugin_slug.'.php';
					$plugin_path      = ABSPATH.'wp-content/plugins/'.$plugin_path_file;
					$is_plugin_active = is_plugin_active($plugin_path_file);
					$is_plugin_exists = file_exists($plugin_path);

					$plugin_name          = $plugin->plugin_name;
					$plugin_cover         = $plugin->plugin_cover;
					$plugin_pro_link      = $plugin->plugin_pro_link;
					$plugin_license_check = $plugin->is_installed ? $trendyol_admin->plugin_license_check($plugin_slug, true) : false;
					$plugin_info          = $is_plugin_exists ? get_plugin_data($plugin_path) : null;
					$plugin_api_version   = $plugin->version;

					?>
                    <div class="wc_trendyol_other_plugin <?=$is_plugin_active ? 'installed' : ''?>">
                        <div class="wc_trendyol_other_plugin_cover">
                            <div class="wc_trendyol_other_plugin_badges">
								<?php
									if(isset($plugin->badges)){
										foreach($plugin->badges as $badge_class => $badge_text){
											?>
                                            <div class="wc_trendyol_other_plugin_badge <?=$badge_class?>"><?=$badge_text?></div>
											<?php
										}
									}
								?>
                            </div>
                            <img src="<?=$plugin_cover?>" alt="<?=$plugin_name?>">
                        </div>
                        <div class="wc_trendyol_other_plugin_name"><?=$plugin_name?> <br>(V<?=$plugin_info['Version'] ?? $plugin_api_version?>)</div>
                        <div class="wc_trendyol_other_plugin_install">
							<?php
								if(isset($plugin->is_installed) and $plugin->is_installed){
									if($plugin_license_check->status == 'success'){

										if(isset($plugin_info['Version']) and version_compare($plugin_info['Version'], $plugin_api_version, '<')){
											?>
                                            <button class="btn wc_trendyol_btn wc_trendyol_install_other_plugin_btn" data-nonce="<?=wp_create_nonce('trendyol_ajax_nonce')?>" data-plugin_slug="<?=$plugin_slug?>"><?=__('GÜNCELLE (V'.($plugin_api_version).')', 'wc-trendyol');?></button>
											<?php
										}
										else if(!$is_plugin_exists){
											?>
                                            <button class="btn wc_trendyol_btn wc_trendyol_install_other_plugin_btn" data-nonce="<?=wp_create_nonce('trendyol_ajax_nonce')?>" data-plugin_slug="<?=$plugin_slug?>"><?=__('YÜKLE', 'wc-trendyol');?></button>
											<?php
										}
										else if($is_plugin_exists and !$is_plugin_active){
											?>
                                            <button class="btn wc_trendyol_btn wc_trendyol_active_other_plugin_btn" data-nonce="<?=wp_create_nonce('trendyol_ajax_nonce')?>" data-plugin_slug="<?=$plugin_slug?>"><?=__('AKTİFLEŞTİR', 'wc-trendyol');?></button>
											<?php
										}
										else{
											if(isset($plugin_license_check->data->status) and $plugin_license_check->data->status == 'success'){
												$wc_trendyol_plugin_license_time = $trendyol_admin->calc_license_time(strtotime($plugin_license_check->data->expired_time ?? ''));
												?>
                                                <input type="text" class="wc_trendyol_form_input" readonly value="<?=$wc_trendyol_plugin_license_time?>">
                                                <a href="<?=$plugin_pro_link?>" target="_blank" class="btn wc_trendyol_link" style="background: #00A510" referrerpolicy="no-referrer-when-downgrade"><?=__('Süreyi Uzat', 'wc-trendyol');?></a>
												<?php
											}
											else{
												?>
                                                <input type="text" class="wc_trendyol_form_input wc_trendyol_other_plugin_license" style="grid-column: 1 / 4;" placeholder="<?=$plugin_name?> Lisans">
                                                <button class="btn wc_trendyol_link wc_trendyol_other_plugin_active_license_btn" data-plugin_slug="<?=$plugin_slug?>"><?=__('Aktif Et', 'wc-trendyol');?></button>
												<?php
											}
										}

									}
									else if(isset($plugin_license_check->has_it_expired)){
										?>
                                        <a href="<?=$plugin_pro_link?>" target="_blank" class="btn wc_trendyol_link" style="background: #6caf04" referrerpolicy="no-referrer-when-downgrade"><?=__('Lisans Bitmiş. Süreyi Uzat', 'wc-trendyol');?></a>
										<?php
									}
									else{
										?>
                                        <input type="text" class="wc_trendyol_form_input wc_trendyol_other_plugin_license" style="grid-column: 1 / 3;" placeholder="<?=$plugin_name?> Lisans">
                                        <button class="btn wc_trendyol_link wc_trendyol_other_plugin_active_license_btn" data-plugin_slug="<?=$plugin_slug?>"><?=__('Aktif Et', 'wc-trendyol');?></button>
                                        <a href="<?=$plugin_pro_link?>" target="_blank" class="btn wc_trendyol_link" style="background: #00A510" referrerpolicy="no-referrer-when-downgrade"><?=__('Satın Al', 'wc-trendyol');?></a>
										<?php
									}
								}
								else{
									?>
                                    <a href="<?=$plugin_pro_link?>" target="_blank" class="btn wc_trendyol_link" referrerpolicy="no-referrer-when-downgrade"><?=__('ÇOK YAKINDA', 'wc-trendyol');?></a>
									<?php
								}
							?>
                        </div>
                    </div>
					<?php
				}
			}
		}
		else{
			?>
            <div class="wc_trendyol_alert">Diğer Eklentiler çekilemedi</div>
			<?php
		}
	?>

</div>