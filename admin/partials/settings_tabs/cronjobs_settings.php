<?php
	global $trendyol_metas, $trendyol_admin, $trendyol_adapter;

	$admin_ajax_url = admin_url('admin-ajax.php');
?>
<div class="wc_trendyol_col_5">
	<?php
		$cron_jobs_list = $trendyol_admin->wc_trendyol_setting_cron_jobs_list();
		if($cron_jobs_list != null){
			foreach($cron_jobs_list as $cron_slug => $cron_data){
				$cron_data['plugin_name'] = $trendyol_admin->plugin_name;
				?>
                <div class="wc_trendyol_form_group wc_trendyol_pro_col_12">
                    <label for="<?=$cron_slug?>" class="wc_trendyol_form_label"><?=$cron_data['job_title'] ?? 'XX'?></label>
					<?php

						if(isset($cron_data['disabled']) and !empty($cron_data['disabled'])){
							?>
                            <div class="wc_trendyol_alert"><?=$cron_data['help_text'] ?? 'XX'?></div>
							<?php
						}
						else{
							?>
                            <help><?=$cron_data['help_text'] ?? 'XX'?></help>
							<?php
							unset($cron_data['help_text']);
							unset($cron_data['job_title']);
							$sync_orders_cron_url = add_query_arg($cron_data, $admin_ajax_url);
							?>
                            <input type="text" name="<?=$cron_slug?>" id="<?=$cron_slug?>" class="wc_trendyol_form_input <?=$cron_slug?>" value="<?=$sync_orders_cron_url;?>" readonly>
							<?php
						}

					?>
                </div>
				<?php
			}
		}
		else{
			?>
            <div class="wc_trendyol_alert"><?=__('Henüz aktif cron işlemini bulunmamakta. ', 'wc-trendyol')?></div>
			<?php
		}
	?>
</div>

<div class="wc_trendyol_faq_list wc_trendyol_col_6" style="position:sticky; top:10px;">

    <div class="wc_trendyol_content_collapse">
        <div class="wc_trendyol_collapse_title">
			<?=__('Tetikliyiciler Nasıl Kurulur?', 'wc-trendyol');?>
        </div>
        <div class="wc_trendyol_collapse_content">
            <iframe width="100%" height="400" src="https://www.youtube.com/embed/9gWcw0dZG68?si=y-uqp-gpeuBpOVN3" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
    </div>

</div>