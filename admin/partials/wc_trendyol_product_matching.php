<?php
	global $trendyol_wc_adapter, $trendyol_adapter, $trendyol_admin;

	//    wp_enqueue_style($trendyol_admin->plugin_name.'-product_matching-css', WC_TRENDYOL_DIR_URL.'/admin/assets/css/product_matching.css', [], $trendyol_admin->version, 'all');
	wp_enqueue_script($trendyol_admin->plugin_name.'-product_matching-js', WC_TRENDYOL_DIR_URL.'/admin/assets/js/product_matching.js', [], $trendyol_admin->version);

	$wc_cat_id = (int)(($_GET['wc_cat_id']) ?? 0);

	$product_categories = get_terms([
		'taxonomy'   => "product_cat",
		'hide_empty' => false,
	]);

?>
<div class="wc_trendyol_card wc_trendyol_product_matching_page">
	<?php
		if($product_categories != null){
			?>
			<div class="wc_trendyol_card_header">
				<div class="wc_trendyol_card_toolbar item_left">
					<div class="wc_trendyol_form_group">
						<label for="" class="wc_trendyol_form_label">Kategori</label>
						<?php
							$product_categories_result = $trendyol_admin->sort_terms_hierarchicaly($product_categories);
						?>
						<select class="wc_cat_id wc_trendyol_form_select refresh_table">
							<option value="0"><?=__('Ürün Kategorisi Seçiniz', 'wc-trendyol');?></option>
							<?php
								$trendyol_admin->generate_select_box_to_array($product_categories_result, $wc_cat_id);
							?>
						</select>
					</div>
				</div>
				<div class="wc_trendyol_card_title">
					<?=__('Toplu Ürün Eşitleme', 'wc-trendyol');?>
					<button class="wc_trendyol_btn table_refresh_btn">
						<i class="fa-solid fa-arrows-rotate"></i>
					</button>
				</div>
				<div class="wc_trendyol_card_toolbar item_right">
					<button class="wc_trendyol_btn wc_trendyol_auto_sku_generator_btn"><?=__('Otomatik SKU Oluştur','wc-trendyol')?></button>
					<button class="wc_trendyol_btn wc_trendyol_save_all_btn"><?=__('Tümünü Kaydet','wc-trendyol')?></button>
				</div>
			</div>
			<div class="wc_trendyol_card_body wc_trendyol_autoload_table" data-page_load="true" data-load_table="wc_trendyol_product_matching_table">
				<div class="wc_trendyol_please_wait">
					<?=__('LÜTFEN ÖNCE KATEGORİ SEÇİN', 'wc-trendyol');?>
				</div>
			</div>
			<div class="wc_trendyol_card_footer"></div>
			<?php
		}
		else{
			?>
			<div class="wc_trendyol_alert"><?=__('Sitenize ait kategori bulunamadı. Lütfen önce kategori ekleyin.', 'wc-trendyol');?></div>
			<?php
		}
	?>
</div>