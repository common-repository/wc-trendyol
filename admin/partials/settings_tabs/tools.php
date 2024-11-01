<?php
	global $trendyol_wc_adapter, $trendyol_adapter, $trendyol_admin, $trendyol_product_sync_admin;

	wp_enqueue_script($trendyol_admin->plugin_name.'-tools', WC_TRENDYOL_DIR_URL.'/admin/assets/js/tools.js', [], $trendyol_admin->version);
?>
<div class="wc_trendyol_tools">

    <div class="wc_trendyol_form_group" style="margin-top: 15px">
        <label for="" class="wc_trendyol_form_label">Trendyol ürün aktarmalarını durdurur</label>
        <button class="wc_trendyol_btn wc_trendyol_delete_all_product_sync_btn"><?=__("Tüm Aktarmaları Durdur", 'wc-trendyol')?></button>
    </div>

    <div class="wc_trendyol_form_group" style="margin-top: 15px">
        <label for="" class="wc_trendyol_form_label">Zamanlanmış eylemlerlerdeki tüm görevleri siler. (arada temizlemek iyi olur)</label>
        <button class="wc_trendyol_btn wc_trendyol_clear_actions_btn"><?=__("Zamanlanmış Eylemleri Temizle", 'wc-trendyol')?></button>
    </div>

    <div class="wc_trendyol_form_group" style="margin-top: 15px">
        <label for="" class="wc_trendyol_form_label">Sadece ürün bilgilerini sıfırlar</label>
        <button class="wc_trendyol_btn wc_trendyol_delete_all_product_trendyol_meta_btn"><?=__("Trendyol Ürün Bilgilerini Sıfırla", 'wc-trendyol')?></button>
    </div>

    <div class="wc_trendyol_form_group" style="margin-top: 50px">
        <label for="" class="wc_trendyol_form_label">Tüm trendyol bilgilerini siler. Her şeye en baştan başlarsınız</label>
        <button class="wc_trendyol_btn wc_trendyol_plugin_reset_btn" style="background: #ba0000" data-tooltip="true" data-tooltip_text="Sadece eminseniz kullanın"><?=__("Fabrika Ayarlarına Dön", 'wc-trendyol')?></button>
        <img src="<?=WC_TRENDYOL_DIR_URL?>admin/assets/img/emoji-no.gif" style="width: 30px;margin-right: 10px;float: left;margin-top: 2px;" data-tooltip="true" data-tooltip_text="Sadece eminseniz kullanın">
    </div>

</div>