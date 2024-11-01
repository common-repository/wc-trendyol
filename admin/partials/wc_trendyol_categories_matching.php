<?php
	global $trendyol_wc_adapter, $trendyol_adapter, $trendyol_admin;

	//    wp_enqueue_style($trendyol_admin->plugin_name.'-product_matching-css', WC_TRENDYOL_DIR_URL.'/admin/assets/css/product_matching.css', [], $trendyol_admin->version, 'all');
	wp_enqueue_script($trendyol_admin->plugin_name.'-categories_matching-js', WC_TRENDYOL_DIR_URL.'/admin/assets/js/categories_matching.js', [], $trendyol_admin->version);
?>
<div class="wc_trendyol_card wc_trendyol_categories_matching_page">
    <form action="" method="post" class="wc_trendyol_categories_matching_frm">
        <div class="wc_trendyol_card_header">
            <div class="wc_trendyol_card_toolbar">
                <input type="hidden" name="wc_cat_id" class="wc_cat_id" value="1">
            </div>
            <div class="wc_trendyol_card_title">
				<?=__('Toplu Kategori Eşitleme', 'wc-trendyol');?>
            </div>
            <div class="wc_trendyol_card_toolbar item_right">
                <button class="wc_trendyol_btn wc_trendyol_categories_matching_btn">Tümünü Kaydet</button>
            </div>
        </div>
        <div class="wc_trendyol_card_body wc_trendyol_autoload_table" data-load_table="wc_trendyol_categories_matching_table">
            <div class="wc_trendyol_please_wait">
				<?=__('LÜTFEN ÖNCE KATEGORİ SEÇİN', 'wc-trendyol');?>
            </div>
        </div>
        <div class="wc_trendyol_card_footer"></div>
    </form>
</div>