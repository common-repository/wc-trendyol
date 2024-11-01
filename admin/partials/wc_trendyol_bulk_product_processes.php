<?php
	global $trendyol_wc_adapter, $trendyol_adapter, $trendyol_admin;

	//    wp_enqueue_style($trendyol_admin->plugin_name.'-product_matching-css', WC_TRENDYOL_DIR_URL.'/admin/assets/css/product_matching.css', [], $trendyol_admin->version, 'all');
	wp_enqueue_script($trendyol_admin->plugin_name.'-bulk_product_processes-js', WC_TRENDYOL_DIR_URL.'/admin/assets/js/bulk_product_processes.js', [], $trendyol_admin->version);

	$wc_cat_id = (int)(($_GET['wc_cat_id']) ?? 0);

	$product_categories = get_terms([
		'taxonomy'   => "product_cat",
		'hide_empty' => false,
	]);

?>
<div class="wc_trendyol_card wc_trendyol_bulk_product_processing_page">
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
					<?=__('Toplu Ürün İşlemleri', 'wc-trendyol');?>
                    <button class="wc_trendyol_btn table_refresh_btn">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                </div>
                <div class="wc_trendyol_card_toolbar item_right">
                    <button class="wc_trendyol_btn wc_trendyol_modal_open_btn" data-modal_class=".wc_trendyol_change_website_price_modal">Web Site Fiyatlarını Değiştir</button>
                    <button class="wc_trendyol_btn wc_trendyol_modal_open_btn" data-modal_class=".wc_trendyol_change_trendyol_price_modal">Trendyol Fiyatlarını Değiştir</button>
                    <button class="wc_trendyol_btn wc_trendyol_bulk_product_processes_save_all_btn"><?=__('Tümünü Kaydet', 'wc-trendyol')?></button>
                </div>
            </div>
            <div class="wc_trendyol_card_body wc_trendyol_autoload_table" data-load_table="wc_trendyol_bulk_product_processes_table">
                <div class="wc_trendyol_please_wait">
					<?=__('LÜTFEN ÖNCE KATEGORİ SEÇİN', 'wc-trendyol');?>
                </div>
            </div>
            <div class="wc_trendyol_card_footer"></div>

            <div class="wc_trendyol_modal wc_trendyol_change_website_price_modal">
                <div class="wc_trendyol_modal_content">
                    <div class="wc_trendyol_modal_header">
                        <div class="wc_trendyol_modal_header_title">
                            Web Sitesindeki Ürün Fiyatlarını Değiştir (Beta)
                        </div>
                    </div>
                    <div class="wc_trendyol_modal_body">
                        <form action="" method="post" class="wc_trendyol_change_website_price_modal_frm">

                            <div class="wc_trendyol_alert" style="width: 100%; font-size:22px; text-align: center">
                                BU ÖZELLİK BETA SÜRÜMDE OLDUĞU İÇİN LÜTFEN ÖNCE YEDEK ALINIZ!!!
                            </div>

                            <div class="wc_trendyol_form_group_inline">

                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">Değer</label>
                                    <input type="text" name="deger" class="wc_trendyol_form_input just_float wc_trendyol_change_website_price_value_input" value="0">
                                </div>
                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">Oran</label>
                                    <select name="oran" class="wc_trendyol_form_select wc_trendyol_change_website_price_rate_input">
                                        <option value="sabit">Sabit (±)</option>
                                        <option value="yuzde">Yüzde (%)</option>
                                    </select>
                                </div>
                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">İşlem</label>
                                    <select name="islem" class="wc_trendyol_form_select wc_trendyol_change_website_price_action_input">
                                        <option value="+">Arttır (+)</option>
                                        <option value="-">Azalt (-)</option>
                                    </select>
                                </div>

                            </div>

                            <div class="wc_trendyol_preview">
                                <h3 style="text-align: center; border-bottom: 1px solid #eee; padding-bottom: 10px;">Örnek Hesaplama</h3>
                                <div class="wc_trendyol_form_group_inline">

                                    <div class="wc_trendyol_form_group">
                                        <label class="wc_trendyol_form_label">Eski Ürün Fiyatı (Örnek Fiyat)</label>
                                        <input type="text" class="wc_trendyol_form_input just_float wc_trendyol_website_calc_preview_old_price" value="980.99">
                                    </div>

                                    <div class="wc_trendyol_form_group">
                                        <label class="wc_trendyol_form_label">Yeni Ürün Fiyatı</label>
                                        <input type="text" class="wc_trendyol_form_input wc_trendyol_website_calc_preview_new_price" readonly>
                                    </div>

                                </div>

                            </div>

                        </form>
                    </div>
                    <div class="wc_trendyol_modal_footer">
                        <button class="wc_trendyol_btn wc_trendyol_modal_close_btn">Kapat</button>
                        <button class="wc_trendyol_btn wc_trendyol_change_this_wc_cat_website_product_price_modal_btn" style="float:right;">SADECE BU KATEGORİDEKİ ÜRÜNLERE UYGULA</button>
                        <button class="wc_trendyol_btn wc_trendyol_change_all_website_product_price_modal_btn" style="float:right;">SİTEDEKİ TÜM ÜRÜNLERE UYGULA</button>
                    </div>
                </div>
            </div>

            <div class="wc_trendyol_modal wc_trendyol_change_trendyol_price_modal">
                <div class="wc_trendyol_modal_content">
                    <div class="wc_trendyol_modal_header">
                        <div class="wc_trendyol_modal_header_title">
                            Trendyoldaki Ürün Fiyatlarını Değiştir (Beta)
                        </div>
                    </div>
                    <div class="wc_trendyol_modal_body">
                        <form action="" method="post" class="wc_trendyol_change_trendyol_price_modal_frm">

                            <div class="wc_trendyol_alert" style="width: 100%; font-size:22px; text-align: center">
                                BU ÖZELLİK BETA SÜRÜMDE OLDUĞU İÇİN LÜTFEN ÖNCE YEDEK ALINIZ!!!
                            </div>

                            <div class="wc_trendyol_form_group_inline">

                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">Değer</label>
                                    <input type="text" name="deger" class="wc_trendyol_form_input just_float wc_trendyol_change_trendyol_price_value_input" value="0">
                                </div>
                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">Oran</label>
                                    <select name="oran" class="wc_trendyol_form_select wc_trendyol_change_trendyol_price_rate_input">
                                        <option value="sabit">Sabit (±)</option>
                                        <option value="yuzde">Yüzde (%)</option>
                                    </select>
                                </div>
                                <div class="wc_trendyol_form_group">
                                    <label for="" class="wc_trendyol_form_label">İşlem</label>
                                    <select name="islem" class="wc_trendyol_form_select wc_trendyol_change_trendyol_price_action_input">
                                        <option value="+">Arttır (+)</option>
                                        <option value="-">Azalt (-)</option>
                                    </select>
                                </div>

                            </div>

                            <div class="wc_trendyol_preview">
                                <h3 style="text-align: center; border-bottom: 1px solid #eee; padding-bottom: 10px;">Örnek Hesaplama</h3>
                                <div class="wc_trendyol_form_group_inline">

                                    <div class="wc_trendyol_form_group">
                                        <label class="wc_trendyol_form_label">Eski Ürün Fiyatı (Örnek Fiyat)</label>
                                        <input type="text" class="wc_trendyol_form_input just_float wc_trendyol_trendyol_calc_preview_old_price" value="980.99">
                                    </div>

                                    <div class="wc_trendyol_form_group">
                                        <label class="wc_trendyol_form_label">Yeni Ürün Fiyatı</label>
                                        <input type="text" class="wc_trendyol_form_input wc_trendyol_trendyol_calc_preview_new_price" readonly>
                                    </div>

                                </div>

                            </div>

                        </form>
                    </div>
                    <div class="wc_trendyol_modal_footer">
                        <button class="wc_trendyol_btn wc_trendyol_modal_close_btn">Kapat</button>
                        <button class="wc_trendyol_btn wc_trendyol_change_this_wc_cat_trendyol_product_price_modal_btn" style="float:right;">SADECE BU KATEGORİDEKİ ÜRÜNLERE UYGULA</button>
                        <button class="wc_trendyol_btn wc_trendyol_change_all_trendyol_product_price_modal_btn" style="float:right;">SİTEDEKİ TÜM ÜRÜNLERE UYGULA</button>
                    </div>
                </div>
            </div>

			<?php
		}
		else{
			?>
            <div class="wc_trendyol_alert"><?=__('Sitenize ait kategori bulunamadı. Lütfen önce kategori ekleyin.', 'wc-trendyol');?></div>
			<?php
		}
	?>
</div>