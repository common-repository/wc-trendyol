<?php
	global $trendyol_admin, $trendyol_metas, $trendyol_adapter;

	$supplier_id         = $trendyol_metas->get_wc_trendyol_meta_settings('supplier');
	$username            = $trendyol_metas->get_wc_trendyol_meta_settings('username');
	$password            = $trendyol_metas->get_wc_trendyol_meta_settings('password');
	$token               = $trendyol_metas->get_wc_trendyol_meta_settings('token');
	$main_brand          = $trendyol_metas->get_wc_trendyol_meta_settings('main_brand');
	$shipment_company_id = $trendyol_metas->get_wc_trendyol_meta_settings('shipment_company_id');
	$debug_log           = $trendyol_metas->get_wc_trendyol_meta_settings('debug_log');
?>
<div class="wc_trendyol_col_5">
    <form action="" method="post" class="wc_trendyol_settings_frm">

        <div class="wc_trendyol_form_group">
            <a href="https://partner.trendyol.com/account/info?tab=integrationInformation" target="_blank" class="wc_trendyol_link"><?=__('Trendyol Entegrasyon Sayfasını Aç', 'wc-trendyol');?></a>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="supplier_id" class="wc_trendyol_form_label"><?=__('Satıcı ID (Cari ID)', 'wc-trendyol');?></label>
            <input type="text" name="supplier_id" id="supplier_id" class="supplier_id wc_trendyol_form_input" value="<?=$supplier_id;?>" autocapitalize="off" autocomplete="one-time-code" required>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="username" class="wc_trendyol_form_label"><?=__('API key', 'wc-trendyol');?></label>
            <input type="text" name="username" id="username" class="username wc_trendyol_form_input" value="<?=$username;?>" autocapitalize="off" autocomplete="one-time-code" required>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="trpassword" class="wc_trendyol_form_label"><?=__('API secret', 'wc-trendyol');?></label>
            <input type="password" name="password" id="trpassword" class="password wc_trendyol_form_input" value="<?=$password;?>" autocapitalize="off" autocomplete="one-time-code" required>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="token" class="wc_trendyol_form_label"><?=__('Token', 'wc-trendyol');?></label>
            <input type="password" name="token" id="token" class="token wc_trendyol_form_input" value="<?=$token;?>" autocapitalize="off" autocomplete="one-time-code" required>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="main_brand" class="wc_trendyol_form_label"><?=__('Ana marka', 'wc-trendyol');?></label>
            <select name="main_brand" class="wc_trendyol_brand_search wc_trendyol_form_select">
                <option value=""><?=__('Lütfen markanızı seçin', 'wc-trendyol');?></option>
				<?php
					if(!empty($main_brand)){
						$brand_explode = explode(':', $main_brand);
						?>
                        <option value="<?=$brand_explode[0].':'.$brand_explode[1]?>" selected><?=$brand_explode[1] ?? 'Marka hatası'?></option>
						<?php
					}
				?>
            </select>
        </div>

        <div class="wc_trendyol_form_group">
            <label for="shipment_company_id" class="wc_trendyol_form_label"><?=__('Anlaşmalı kargo firmanız', 'wc-trendyol');?></label>
            <select name="shipment_company_id" class="wc_trendyol_form_select">
                <option value=""><?=__('Kargo Firmanızı Seçin', 'wc-trendyol')?></option>
				<?php
					$shipment_companies = $trendyol_adapter->get_shipment_companies();
					foreach($shipment_companies as $shipment_company){
						?>
                        <option value="<?=$shipment_company['ID']?>" <?=$shipment_company['ID'] == $shipment_company_id ? 'selected' : ''?>><?=$shipment_company['company_name'] ?? 'XX'?></option>
						<?php
					}
				?>
            </select>
        </div>

		<?php
			$settings_inputs = $trendyol_admin->wc_trendyol_setting_inputs();
			foreach($settings_inputs as $input_slug => $input){

				$tooltip_text = $input['tooltip_text'] ?? null;
				$label        = $input['label'] ?? 'XX';
				$value        = $input['value'] ?? 'false';
				$disabled     = $input['disabled'] ?? false;
				$meta_value   = $trendyol_metas->get_wc_trendyol_meta_settings($input_slug);

				?>
                <div class="wc_trendyol_form_group_inline">
                    <input name="<?=$input_slug?>" type="checkbox" class="wc_trendyol_checkbox" id="<?=$input_slug?>" value="<?=$value?>" <?=!empty($meta_value) ? 'checked' : ''?> <?=$disabled ? 'disabled' : ''?>/>
                    <label for="<?=$input_slug?>" class="wc_trendyol_form_label">
						<?=$label?>
						<?php
							if($tooltip_text != null){
								?>
                                <span style="position: relative" <?=!empty($tooltip_text) ? 'data-tooltip="true"' : ''?> data-tooltip_text="<?=$tooltip_text?>"><i class="fa-solid fa-circle-question"></i></span>
								<?php
							}
						?>
                    </label>
                </div>
				<?php
			}
		?>

        <div class="wc_trendyol_form_group">
            <button class="wc_trendyol_btn wc_trendyol_settings_save_btn"><?=__('Kaydet', 'wc-trendyol');?></button>
        </div>

    </form>
</div>

<div class="wc_trendyol_faq_list wc_trendyol_col_6" style="position:sticky; top:130px;">

    <div class="wc_trendyol_content_collapse">
        <div class="wc_trendyol_collapse_title">
			<?=__('Ayarlar Kurulum Videosu', 'wc-trendyol');?>
        </div>
        <div class="wc_trendyol_collapse_content">
            <iframe width="100%" height="400" src="https://www.youtube.com/embed/alAq4xD4QoE?si=cgew4-mxi_xsb25w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
    </div>

</div>