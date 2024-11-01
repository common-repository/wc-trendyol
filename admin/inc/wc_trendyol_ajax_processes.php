<?php

	use Hasokeyk\Trendyol\Trendyol;

	class wc_trendyol_ajax_processes{

		function __construct(){

			//LICENSE CONTROL
			add_action('wp_ajax_wc_trendyol_license_control', [$this, 'wc_trendyol_license_control']);
			//LICENSE CONTROL

			//SETTINGS SAVE
			add_action('wp_ajax_wc_trendyol_save_settings', [$this, 'wc_trendyol_save_settings']);
			//SETTINGS SAVE

			//SEARCH BRAND
			add_action('wp_ajax_wc_trendyol_search_brand', [$this, 'wc_trendyol_search_brand']);
			//SEARCH BRAND

			//OTHER PLUGINS
			add_action('wp_ajax_wc_trendyol_install_other_plugins_ajax', [$this, 'wc_trendyol_install_other_plugins_ajax']);
			add_action('wp_ajax_wc_trendyol_active_other_plugins_ajax', [$this, 'wc_trendyol_active_other_plugins_ajax']);
			//OTHER PLUGINS

			//LOAD TABLE
			add_action('wp_ajax_wc_trendyol_product_matching_table', [$this, 'wc_trendyol_product_matching_table']);
			add_action('wp_ajax_wc_trendyol_categories_matching_table', [$this, 'wc_trendyol_categories_matching_table']);
			add_action('wp_ajax_wc_trendyol_bulk_product_processes_table', [$this, 'wc_trendyol_bulk_product_processes_table']);
			add_action('wp_ajax_wc_trendyol_bulk_images_processing_table', [$this, 'wc_trendyol_bulk_images_processing_table']);
			//LOAD TABLE

			//PRODUCT MATCHING SAVE
			add_action('wp_ajax_wc_trendyol_product_matching_save_line', [$this, 'wc_trendyol_product_matching_save_line']);
			add_action('wp_ajax_wc_trendyol_product_matching_save_all', [$this, 'wc_trendyol_product_matching_save_all']);
			//PRODUCT MATCHING SAVE

			//BULK PRODUCT PROCESSES - SAVE WEBSITE LINE
			add_action('wp_ajax_wc_trendyol_bulk_product_processes_save_line', [$this, 'wc_trendyol_bulk_product_processes_save_line']);
			add_action('wp_ajax_wc_trendyol_bulk_product_processes_save_all', [$this, 'wc_trendyol_bulk_product_processes_save_all']);
			add_action('wp_ajax_wc_trendyol_change_this_wc_cat_website_product_price', [$this, 'wc_trendyol_change_this_wc_cat_website_product_price']);
			add_action('wp_ajax_wc_trendyol_change_all_website_product_price', [$this, 'wc_trendyol_change_all_website_product_price']);
			add_action('wp_ajax_wc_trendyol_change_this_wc_cat_trendyol_product_price', [$this, 'wc_trendyol_change_this_wc_cat_trendyol_product_price']);
			add_action('wp_ajax_wc_trendyol_change_all_trendyol_product_price', [$this, 'wc_trendyol_change_all_trendyol_product_price']);
			//BULK PRODUCT PROCESSES - SAVE WEBSITE LINE

			//ADD PLUGUN LIST DEACTIVE BUTTON MODAL
			add_filter('plugin_action_links_wc-trendyol/wc-trendyol.php', [$this, 'wc_trendyol_add_plugin_list_setting_menu']);
			add_action('admin_footer', [$this, 'wc_trendyol_add_plugin_list_deactive_button_modal']);
			//ADD PLUGUN LIST DEACTIVE BUTTON MODAL

			//BULK IMAGE PROCESSES
			add_action('wp_ajax_wc_trendyol_product_batch_images_processing_add_images', [$this, 'wc_trendyol_product_batch_images_processing_add_images']);
			add_action('wp_ajax_wc_trendyol_product_batch_images_processing_del_images', [$this, 'wc_trendyol_product_batch_images_processing_del_images']);
			add_action('wp_ajax_wc_trendyol_product_batch_images_processing_save_trendyol', [$this, 'wc_trendyol_product_batch_images_processing_save_trendyol']);
			//BULK IMAGE PROCESSES

			//TOOLS
			add_action('wp_ajax_wc_trendyol_delete_all_product_sync', [$this, 'wc_trendyol_delete_all_product_sync']);
			add_action('wp_ajax_wc_trendyol_delete_all_actions', [$this, 'wc_trendyol_delete_all_actions']);
			add_action('wp_ajax_wc_trendyol_delete_all_product_trendyol_meta', [$this, 'wc_trendyol_delete_all_product_trendyol_meta']);
			add_action('wp_ajax_wc_trendyol_plugin_reset', [$this, 'wc_trendyol_plugin_reset']);
			//TOOLS
		}

		//LICENSE CONTROL
		public function wc_trendyol_license_control($wc_trendyol_license = null, $wc_trendyol_plugin_slug = null, $output = 'ajax'){
			global $trendyol_metas;

			$wc_trendyol_license     = (!is_null($wc_trendyol_license) and !empty($wc_trendyol_license)) ? $wc_trendyol_license : esc_attr($_POST['wc_trendyol_license']);
			$wc_trendyol_plugin_slug = (!is_null($wc_trendyol_plugin_slug) and !empty($wc_trendyol_plugin_slug)) ? $wc_trendyol_plugin_slug : esc_attr($_POST['wc_trendyol_plugin_slug']);

			if(empty($wc_trendyol_license) or empty($wc_trendyol_plugin_slug)){
				$results = [
					'status'  => 'danger',
					'message' => 'License not valid',
				];
				goto results;
			}

			delete_transient('wc_trendyol_'.$wc_trendyol_plugin_slug.'license_cache');

			$param = json_encode([
				'domain'            => home_url(),
				'license'           => $wc_trendyol_license,
				'plugin_short_code' => $wc_trendyol_plugin_slug,
			]);

			$remote = wp_remote_post(WC_TRENDYOL_API_URL.'/license?query=license_control', [
				'timeout' => 10,
				'headers' => [
					'Accept'  => 'application/json',
					'KEYBAPI' => 'EyslN4zo6iHIYprKtM5yzeTwRxo1nOYtMTX',
				],
				'body'    => [
					'param' => $param,
				],
			]);

			$response = json_decode($remote['body']);
			if($response->status == 'success'){
				$results = [
					'status'  => 'success',
					'message' => $response->message
				];

				$trendyol_metas->update_wc_trendyol_meta_settings('license_'.$wc_trendyol_plugin_slug, $wc_trendyol_license);
			}
			else{
				$results = [
					'status'  => 'error',
					'message' => $response->message
				];
			}

			results:
			if($output === 'ajax'){
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($results);
				wp_die();
			}
			else{
				return $results;
			}

		}
		//LICENSE CONTROL

		//SETTINGS SAVE
		public function wc_trendyol_save_settings(){

			global $trendyol_metas, $trendyol_adapter, $trendyol_admin;

			parse_str($_POST['wc_trendyol_settings'], $post);

			$wc_trendyol_supplier_id         = sanitize_text_field($post['supplier_id'] ?? null);
			$wc_trendyol_username            = sanitize_text_field($post['username'] ?? null);
			$wc_trendyol_password            = sanitize_text_field($post['password'] ?? null);
			$wc_trendyol_token               = sanitize_text_field($post['token'] ?? null);
			$wc_trendyol_main_brand          = sanitize_text_field($post['main_brand'] ?? null);
			$wc_trendyol_shipment_company_id = sanitize_text_field($post['shipment_company_id'] ?? null);
			$test_mode                       = sanitize_text_field($post['test_mode'] ?? false);

			if(empty($wc_trendyol_supplier_id)){
				$results = [
					'status'  => 'danger',
					'message' => 'Satıcı ID sini girmediniz.',
				];
				goto results;
			}

			if(empty($wc_trendyol_username)){
				$results = [
					'status'  => 'danger',
					'message' => 'Trendyol API Key girmediniz.',
				];
				goto results;
			}

			if(empty($wc_trendyol_password)){
				$results = [
					'status'  => 'danger',
					'message' => 'Trendyol API Secret girmediniz.',
				];
				goto results;
			}

			try{

				$trendyol = new Trendyol($wc_trendyol_supplier_id, $wc_trendyol_username, $wc_trendyol_password, $test_mode);

				$products = $trendyol->TrendyolMarketplace()->TrendyolMarketplaceProducts()->get_my_products(null, false);
				if((isset($products->exception) and $products->exception == 'TrendyolAuthorizationException') or (isset($products->status) and $products->status == '404')){

					delete_option('wc_trendyol_supplier');
					delete_option('wc_trendyol_username');
					delete_option('wc_trendyol_password');
					delete_option('wc_trendyol_main_brand');

					$results = [
						'status'  => 'error',
						'message' => __('Girdiğiniz bilgiler ile Trendyol\'a bağlanılamadı. Lütfen tekrar kontrol edin.', 'wc-trendyol'),
					];
				}
				else{
					$trendyol_metas->update_wc_trendyol_meta_settings('supplier', $wc_trendyol_supplier_id);
					$trendyol_metas->update_wc_trendyol_meta_settings('username', $wc_trendyol_username);
					$trendyol_metas->update_wc_trendyol_meta_settings('password', $wc_trendyol_password);
					$trendyol_metas->update_wc_trendyol_meta_settings('token', $wc_trendyol_token);
					$trendyol_metas->update_wc_trendyol_meta_settings('main_brand', $wc_trendyol_main_brand);
					$trendyol_metas->update_wc_trendyol_meta_settings('shipment_company_id', $wc_trendyol_shipment_company_id);

					$wc_trendyol_inputs = $trendyol_admin->wc_trendyol_setting_inputs();
					foreach($wc_trendyol_inputs as $input_slug => $wc_trendyol_input){
						$trendyol_metas->update_wc_trendyol_meta_settings($input_slug, $post[$input_slug] ?? null);
					}

					$results = [
						'status'  => 'success',
						'message' => __('Bilgiler geçerli. Kayıt yapılmıştır.', 'wc-trendyol'),
					];
				}

			}catch(Exception $err){
				$results = [
					'status'  => 'danger',
					'message' => __('Trendyola Bağlanamıyor. Bilgileri kontrol ediniz.', 'wc-trendyol'),
					'error'   => $err->getMessage()
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();

		}
		//SETTINGS SAVE

		//SEARCH BRAND
		public function wc_trendyol_search_brand(){
			global $trendyol_adapter;

			$query = esc_attr($_POST['q']);

			$trendyol_brands = $trendyol_adapter->search_brand($query);

			$return = null;
			if($trendyol_brands != null){
				foreach($trendyol_brands as $brand){
					$title    = (mb_strlen($brand->name) > 50) ? mb_substr($brand->name, 0, 49).'...' : $brand->name;
					$return[] = [
						$brand->id.':'.$title,
						$title.' ('.$brand->id.')'
					];
				}
			}

			echo json_encode($return);
			wp_die();
		}
		//SEARCH BRAND

		//OTHER PLUGINS
		public function wc_trendyol_install_other_plugins_ajax(){
			global $trendyol_admin, $trendyol_metas;

			$plugin_slug          = esc_attr($_POST['plugin_slug']);
			$nonce                = esc_attr($_POST['nonce']);
			$plugin_license_check = $trendyol_admin->plugin_license_check($plugin_slug);

			if(!wp_verify_nonce($nonce, 'trendyol_ajax_nonce')){
				$result = [
					'status'  => 'danger',
					'message' => __('Güvenlik Doğrulaması Başarısız', 'wc-trendyol'),
				];
			}

			if($plugin_license_check->status == 'success'){

				require_once ABSPATH.'/wp-admin/includes/file.php';
				WP_Filesystem();

				$wc_trendyol_plugin_license = $trendyol_metas->get_wc_trendyol_meta_settings('license_'.$plugin_slug);
				$param                      = json_encode([
					'domain'            => home_url(),
					'license'           => $wc_trendyol_plugin_license,
					'plugin_short_code' => $plugin_slug,
				]);
				$response                   = wp_remote_post(WC_TRENDYOL_API_URL.'/update?query=download_new_version', [
					'timeout' => 10,
					'headers' => [
						'Accept'  => 'application/json',
						'KEYBAPI' => 'EyslN4zo6iHIYprKtM5yzeTwRxo1nOYtMTX',
					],
					'body'    => [
						'param' => $param,
					],
				]);
				if(is_wp_error($response)){
					$result = [
						'status'  => 'danger',
						'message' => __('Eklenti indirilirken bir hata oluştu: ', 'wc-trendyol'),
					];
				}
				else{
					$zip_path = $response['body'];
					$zip_file = WP_CONTENT_DIR.'/plugins/'.$plugin_slug.'.zip';
					$file     = file_put_contents($zip_file, $zip_path);
					if($file === false){
						$result = [
							'status'  => 'danger',
							'message' => __('Eklenti dosyası kaydedilirken bir hata oluştu.', 'wc-trendyol'),
						];
					}
					else{
						$unzip_result = unzip_file($zip_file, WP_PLUGIN_DIR);
						if(is_wp_error($unzip_result)){
							$result = [
								'status'  => 'danger',
								'message' => __('Eklenti dosyası kurulurken bir hata oluştu.', 'wc-trendyol'),
							];
						}
						else{
							$result = [
								'status'  => 'success',
								'message' => __('Eklenti Kuruldu.', 'wc-trendyol'),
							];
						}
					}
				}

			}

			result:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($result ?? ['status' => 'danger', 'messagel' => 'Bilinmeyen hata']);
			wp_die();
		}

		public function wc_trendyol_active_other_plugins_ajax(){

			$plugin_slug = esc_attr($_POST['plugin_slug']);
			$nonce       = esc_attr($_POST['nonce']);

			if(!wp_verify_nonce($nonce, 'trendyol_ajax_nonce')){
				$result = [
					'status'  => 'danger',
					'message' => __('Güvenlik Doğrulaması Başarısız', 'wc-trendyol'),
				];
			}

			if(!function_exists('activate_plugin')){
				require_once ABSPATH.'wp-admin/includes/plugin.php';
			}

			$plugin_path_file = $plugin_slug.'/'.$plugin_slug.'.php';
			$active_plugin    = activate_plugin($plugin_path_file);
			if(is_wp_error($active_plugin)){
				$result = [
					'status'  => 'danger',
					'message' => __('Eklenti aktifleştirilken bir sorun oldu', 'wc-trendyol').' : '.is_wp_error($active_plugin),
				];
			}
			else{
				$result = [
					'status'  => 'success',
					'message' => __('Eklenti Aktifleştirildi', 'wc-trendyol'),
				];
			}

			result:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($result ?? ['status' => 'danger', 'messagel' => 'Bilinmeyen hata']);
			wp_die();
		}
		//OTHER PLUGINS

		//LOAD TABLE
		public function wc_trendyol_product_matching_table(){
			global $trendyol_admin, $trendyol_metas;

			$wc_cat_id = esc_attr($_POST['wc_cat_id'] ?? null);
			$paged     = esc_attr($_POST['paged'] ?? null);

			parse_str($_POST['params'], $params);

			$html                = '';
			$page_line_count     = 20;
			$products            = $trendyol_admin->wc_trendyol_wc_all_products(($wc_cat_id ?? 0), false, ($paged - 1), $page_line_count);
			$total_product_count = $trendyol_admin->wc_trendyol_wc_all_product_count(($wc_cat_id ?? 0));
			if($products != null){

				$html_body = '';

				foreach($products as $r_id => $raw_product){

					$product_id = $raw_product->id;

					$product       = wc_get_product($product_id);
					$product_type  = $product->get_type();
					$product_attrs = $product->get_attributes();

					if($product_type == 'variation'){
						//ALT ÜRÜN
						$main_product = wc_get_product($product->get_parent_id());
						$has_child    = false;
						$parent_id    = $product->get_parent_id();
					}
					else if($product_type == 'variable'){
						//ANA ÜRÜN ÜRÜN
						$main_product = false;
						$has_child    = true;
						$parent_id    = 0;
					}
					else{
						//BASİT ÜRÜN
						$main_product = false;
						$has_child    = false;
						$parent_id    = 0;
					}

					if($main_product instanceof WC_Product){
						$main_product_sku = $main_product->get_sku();
					}
					else{
						$main_product_sku = '';
					}

					$product_name = $product->get_name();
					$product_sku  = $product->get_sku();

					if(mb_strtolower($main_product_sku, 'utf8') == mb_strtolower($product_sku, 'utf8')){
						$sku_error = true;
					}
					else{
						$sku_error = false;
					}

					$wc_trendyol_trendyol_barcode = $trendyol_metas->get_meta_trendyol_barcode($product_id);

					//ALT VERSİYONLARIN ÖZELLİKLERİ
					$attr_text = '';
					if($product_type == 'variation'){
						foreach($product_attrs as $key => $val){
							$attr_text .= $key.' : '.$val." | ";
						}
					}
					//ALT VERSİYONLARIN ÖZELLİKLERİ

					$html_body .= '
                    <tr class="wc_trendyol_product_matching_table_tr '.($has_child ? 'sticky_main_product' : '').'" style="background:'.($product_type == 'variation' ? '#ddd;' : '#fff').'" data-product_type="'.($product_type).'" data-parent_product_id="'.($parent_id).'" data-product_id="'.($product_id).'">
                        <td>
                            <a href="post.php?post='.($product_type == 'variation' ? $parent_id : $product_id).'&action=edit" target="_blank">'.$product_name.'</a>
                            <br>
                            '.mb_strtoupper(rtrim($attr_text, '| '), 'utf8').'
                        </td>
                        <td>
                            <input type="text" name="'.$product_id.'[main_sku]" class="wc_product_main_sku_input '.($sku_error ? 'wc_trendyol_sku_error' : '').'" value="'.($product_type == 'variation' ? $main_product_sku : $product_sku).'" style="width: 100%" readonly>
                        </td>
                        <td>
                             <input type="text" name="'.$product_id.'[sku]" class="wc_trendyol_product_sku_input '.($sku_error ? 'wc_trendyol_sku_error' : '').'" value="'.($product_type == 'variation' ? $product_sku : $main_product_sku).'" style="width: 100%" '.($product_type == 'variation' ? '' : 'readonly').'>
                        </td>
                        <td class="'.($sku_error ? 'wc_trendyol_sku_error' : '').'" '.($sku_error ? 'data-tooltip="true"' : '').' data-tooltip_text="'.(!$has_child ? 'Ana ürün SKU\'su ile Alt ürün SKU\'su aynı olmaz. Alt ürün SKU\'sunu değiştirin' : 'Ana ürüne lütfen SKU ekleyin. Trendyol aktarımında bu özellik önem arz etmektedir.').'">
                             <input type="text" name="'.$product_id.'[trendyol_barcode]" class="wc_trendyol_barcode" '.($sku_error ? 'placeholder' : 'value').'="'.(!empty($wc_trendyol_trendyol_barcode) ? $wc_trendyol_trendyol_barcode : $product_sku).'" style="width : 100%" '.($product_type == 'variable' ? 'readonly' : '').'>
                        </td>
                        <td style="text-align: center; z-index:'.(count((array)$products) - $r_id).'"">
                             <button class="wc_trendyol_btn icon wc_trendyol_save_line_btn" data-tooltip="true" data-tooltip_text="Siteye Kaydet">
                                <i class="fa-solid fa-floppy-disk"></i>
                            </button>
                        </td>
                    </tr>
                    ';

				}

				$html .= '
                <table class="wc_trendyol_table">
                    <thead>
                        <tr>
                            <th>Ürün Adı</th>
                            <th>Ana Ürün Sku</th>
                            <th>Alt Ürün Sku</th>
                            <th>
                            <button class="wc_trendyol_btn icon wc_trendyol_barcode_sync_btn" data-tooltip="true" data-tooltip_text="Boş trendyol barkodlarını alt ürünle eşleştir"><i class="fa-solid fa-rotate"></i></button>
                            Trendyol Barkod
                            </th>
                            <th style="text-align: center">İşlemler</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Ürün Adı</th>
                            <th>Ana Ürün Sku</th>
                            <th>Alt Ürün Sku</th>
                            <th>
                            <button class="wc_trendyol_btn icon wc_trendyol_barcode_sync_btn" data-tooltip="true" data-tooltip_text="Boş trendyol barkodlarını alt ürünle eşleştir"><i class="fa-solid fa-rotate"></i></button>
                            Trendyol Barkod
                            </th>
                            <th style="text-align: center">İşlemler</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        '.$html_body.'
                    </tbody>
                </table>
                ';

			}
			else{
				$html = '<div class="wc_trendyol_alert">ÜRÜN BULUNAMADI</div>';
			}

			$results = [
				'status'     => 'success',
				'data'       => $html,
				'pagination' => $trendyol_admin->get_pagination_html(ceil($total_product_count / $page_line_count), $paged),
			];

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_bulk_product_processes_table(){
			global $trendyol_admin, $trendyol_metas;

			$wc_cat_id = esc_attr($_POST['wc_cat_id'] ?? null);
			$paged     = esc_attr($_POST['paged'] ?? null);

			parse_str($_POST['params'], $params);

			$html                = '';
			$page_line_count     = 20;
			$products            = $trendyol_admin->wc_trendyol_wc_all_products(($wc_cat_id ?? 0), false, ($paged - 1), $page_line_count);
			$total_product_count = $trendyol_admin->wc_trendyol_wc_all_product_count(($wc_cat_id ?? 0));
			if($products != null){

				$html_body = '';

				foreach($products as $r_id => $raw_product){

					$product_id    = $raw_product->id;
					$product       = wc_get_product($product_id);
					$product_type  = $product->get_type();
					$product_attrs = $product->get_attributes();

					if($product_type == 'variable'){
						$parent_product_id = $product->get_id();
					}
					else{
						$parent_product_id = $product->get_parent_id();
					}

					$product_sku            = $product->get_sku();
					$product_name           = $product->get_name();
					$product_stock          = $product->get_stock_quantity();
					$product_sale_price     = $product->get_regular_price();
					$product_discount_price = $product->get_sale_price();
					$product_discount_price = !empty($product_discount_price) ? $product_discount_price : 0;

					$get_trendyol_product_title = $trendyol_metas->get_meta_trendyol_title($product_id);
					$get_trendyol_product_title = !empty($get_trendyol_product_title) ? $get_trendyol_product_title : $product_name;

					$get_trendyol_stock = $trendyol_metas->get_meta_trendyol_stock_quantity($product_id);
					$get_trendyol_stock = !empty($get_trendyol_stock) ? $get_trendyol_stock : $product_stock;

					$get_trendyol_sale_price = $trendyol_metas->get_meta_trendyol_sale_price($product_id);
					$get_trendyol_sale_price = !empty($get_trendyol_sale_price) ? $get_trendyol_sale_price : $product_sale_price;

					$get_trendyol_discount_price = $trendyol_metas->get_meta_trendyol_discount_price($product_id);
					$get_trendyol_discount_price = !empty($get_trendyol_discount_price) ? $get_trendyol_discount_price : $product_discount_price;

					//ALT VERSİYONLARIN ÖZELLİKLERİ
					$attr_text = '';
					if($product_type == 'variation'){
						foreach($product_attrs as $key => $val){
							$attr_text .= $key.' : '.$val." | ";
						}
					}
					//ALT VERSİYONLARIN ÖZELLİKLERİ

					$html_body .= '<tr style="background:'.($product_type == 'variable' ? '#fff' : '#eee').';">';
					$html_body .= '<td><a href="/wp-admin/post.php?post='.($product_type == 'variable' ? $parent_product_id : $product_id).'&action=edit" target="_blank">'.($product_sku).'</a><br>'.mb_strtoupper(rtrim($attr_text, '| '), 'utf8').'</td>';
					$html_body .= '<td><input type="text" name="'.$product_id.'[trendyol_product_title]" class="trendyol_product_title" value="'.($get_trendyol_product_title).'" '.($product_type == 'variable' ? 'disabled' : '').' required></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[website_stock_qty]" class="website_stock_qty just_int" value="'.($product_stock).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[trendyol_stock_qty]" class="trendyol_stock_qty just_int" value="'.($get_trendyol_stock).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[website_sale_price]" class="website_sale_price just_float" value="'.($product_sale_price).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[website_discount_price]" class="website_discount_price just_float" value="'.($product_discount_price).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[trendyol_sale_price]" class="trendyol_sale_price just_float" value="'.($get_trendyol_sale_price).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td><input type="text" step="1" min="0" name="'.$product_id.'[trendyol_discount_price]" class="trendyol_discount_price just_float" value="'.($get_trendyol_discount_price).'" '.($product_type == 'variable' ? 'disabled' : '').' ></td>';
					$html_body .= '<td style="text-align: center;">';
					if($product_type != 'variable'){
						$html_body .= '<button class="wc_trendyol_btn wc_trendyol_bulk_product_processes_save_btn" data-wc_product_id="'.($product_id).'" data-tooltip="true" data-tooltip_text="Değişiklikleri Kayıt Et"><i class="fa-solid fa-floppy-disk"></i></button>';
					}
					else{
						$html_body .= 'Ana ürüne işlem yapılamaz';
					}
					$html_body .= '</td>';
					$html_body .= '</tr>';

				}

				$get_currency = get_woocommerce_currency_symbol();

				$html .= '
                <table class="wc_trendyol_table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Ürün Adı</th>
                            <th class="small">Website Stok</th>
                            <th class="small">Trendyol Stok</th>
                            <th class="small">Site Satış F. ('.($get_currency).')</th>
                            <th class="small">Site İndirimli F. ('.($get_currency).')</th>
                            <th class="small">Trendyol Satış F. ('.($get_currency).')</th>
                            <th class="small">Trendyol İndirimli F. ('.($get_currency).')</th>
                            <th style="text-align: center; width: 100px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$html_body.'
                    </tbody>
                </table>
                ';

			}
			else{
				$html = '<div class="wc_trendyol_alert">ÜRÜN BULUNAMADI</div>';
			}

			$results = [
				'status'     => 'success',
				'data'       => $html,
				'pagination' => $trendyol_admin->get_pagination_html(ceil($total_product_count / $page_line_count), $paged),
			];

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_bulk_images_processing_table(){
			global $trendyol_admin, $trendyol_metas;

			$wc_cat_id = esc_attr($_POST['wc_cat_id']);
			$paged     = esc_attr($_POST['paged']);

			parse_str($_POST['params'], $params);

			$html                = '';
			$page_line_count     = 20;
			$products            = $trendyol_admin->wc_trendyol_wc_all_products(($wc_cat_id ?? 0), false, ($paged - 1), $page_line_count);
			$total_product_count = $trendyol_admin->wc_trendyol_wc_all_product_count(($wc_cat_id ?? 0));
			if($products != null){

				$html_body = '';

				foreach($products as $raw_product){

					$product_id = $raw_product->id;

					$product       = wc_get_product($product_id);
					$product_type  = $product->get_type();
					$product_name  = $product->get_name();
					$product_sku   = $product->get_sku();
					$product_attrs = $product->get_attributes();

					$get_trendyol_product_title = $trendyol_metas->get_meta_trendyol_title($product_id);
					$get_trendyol_product_title = !empty($get_trendyol_product_title) ? $get_trendyol_product_title : $product_name;

					if($product_type == 'variation'){
						$parent_id = $product->get_parent_id();
					}
					else{
						$product_id = $product->get_id();
					}

					//ALT VERSİYONLARIN ÖZELLİKLERİ
					$attr_text = '';
					if($product_type == 'variation'){
						foreach($product_attrs as $key => $val){
							$attr_text .= $key.' : '.$val." | ";
						}
					}
					//ALT VERSİYONLARIN ÖZELLİKLERİ

					//GÖRSELLER
					$image_html = '';
					if(isset($main_product) and $main_product){

						$get_product_main_image_id = $main_product->get_image_id();
						$url                       = wp_get_attachment_image_src($get_product_main_image_id);
						if(is_array($url)){
							if($product_type != 'variable'){
								$image_html .= '<div class="wc_trendyol_product_image_content"><img src="'.(current($url)).'" width="100" loading="lazy"/><div class="wc_trendyol_images_del_btn" data-tooltip="true" data-tooltip_text="Görseli Sil" data-product_id="'.($product_id).'"><i class="fa-solid fa-xmark"></i></div></div>';
							}
						}

						$get_product_images = $main_product->get_gallery_image_ids();
						foreach($get_product_images as $image_id){
							$url = wp_get_attachment_image_src($image_id);
							if(is_array($url)){
								if($product_type != 'variable'){
									$image_html .= '<div class="wc_trendyol_product_image_content"><img src="'.(current($url)).'" width="100" loading="lazy"/><div class="wc_trendyol_images_del_btn" data-tooltip="true" data-tooltip_text="Görseli Sil" data-product_id="'.($product_id).'" data-image_id="'.($image_id).'" ><i class="fa-solid fa-xmark"></i></div></div>';
								}
							}
						}
					}

					$get_product_image_id = $product->get_image_id();
					$url                  = wp_get_attachment_image_src($get_product_image_id);
					if(is_array($url)){
						$image_html .= '<div class="wc_trendyol_product_image_content"><img src="'.(current($url)).'" width="100" loading="lazy"/><div class="wc_trendyol_images_del_btn" data-tooltip="true" data-tooltip_text="Görseli Sil" data-product_id="'.($product_id).'" data-image_id="'.($get_product_image_id).'" ><i class="fa-solid fa-xmark"></i></div></div>';
					}

					$get_product_images = $product->get_gallery_image_ids();
					foreach($get_product_images as $image_id){
						$url = wp_get_attachment_image_src($image_id);
						if(is_array($url)){
							if($product_type != 'variable'){
								$image_html .= '<div class="wc_trendyol_product_image_content"><img src="'.(current($url)).'" width="100" loading="lazy"/><div class="wc_trendyol_images_del_btn" data-tooltip="true" data-tooltip_text="Görseli Sil" data-product_id="'.($product_id).'" data-image_id="'.($image_id).'" ><i class="fa-solid fa-xmark"></i></div></div>';
							}
						}
					}
					//GÖRSELLER

					$html_body .= '<tr style="background:'.($product_type == 'variable' ? '#fff' : '#eee').';">';
					$html_body .= '<td><a href="/wp-admin/post.php?post='.($product_type == 'variation' ? $parent_id : $product_id).'&action=edit" target="_blank">'.($get_trendyol_product_title).'</a><br>'.mb_strtoupper(rtrim($attr_text, '| '), 'utf8').'</td>';
					$html_body .= '<td>'.$product_sku.'</td>';
					$html_body .= '<td class="wc_trendyol_images_list">'.$image_html.'</td>';
					$html_body .= '<td style="text-align: center"><button class="wc_trendyol_btn wc_trendyol_add_images_product_btn" data-product_id="'.$product_id.'" '.($product_type != 'variable' ? '' : 'disabled').'>Görsel Ekle</button></td>';
					$html_body .= '<td style="text-align: center"><button class="wc_trendyol_btn icon wc_trendyol_send_images_trendyol_line_btn" data-product_id="'.$product_id.'" data-tooltip="true" data-tooltip_text="Trendyola Gönder" '.($product_type != 'variable' ? '' : 'disabled').'><i class="fa-solid fa-floppy-disk"></i></button></td>';
					$html_body .= '</tr>';

				}

				$html .= '
                <table class="wc_trendyol_table">
                    <thead>
                        <tr>
                            <th>Trendyol Ürün Adı</th>
                            <th>Ürün Sku</th>
                            <th width="60%">Ürün Görselleri</th>
                            <th>Görsel Ekle</th>
                            <th style="text-align: center">Trendyola Gönder</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$html_body.'
                    </tbody>
                </table>
                ';

			}
			else{
				$html = '<div class="wc_trendyol_alert">ÜRÜN BULUNAMADI</div>';
			}

			$results = [
				'status'     => 'success',
				'data'       => $html,
				'pagination' => $trendyol_admin->get_pagination_html(ceil($total_product_count / $page_line_count), $paged),
			];

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_categories_matching_table(){
			global $trendyol_admin, $trendyol_metas, $trendyol_adapter;

			$paged = esc_attr($_POST['paged']);

			parse_str($_POST['params'], $params);

			$html                   = '';
			$page_line_count        = 20;
			$categories             = $trendyol_admin->wc_trendyol_wc_all_categories(0, ($paged - 1), $page_line_count);
			$total_categories_count = $trendyol_admin->wc_trendyol_wc_all_categories_count();
			$trendyol_categories    = $trendyol_adapter->get_all_categories();

			if($categories != null){
				$html_body = '';
				foreach($categories as $category){

					$wc_cat_id = $category->term_id;

					$get_wc_trendyol_category_id = get_term_meta($wc_cat_id, 'wc_trendyol_category_id', true);
					$convert_option              = $trendyol_admin->trendyol_categories_array_to_select_option($trendyol_categories->categories ?? [], 0, ($get_wc_trendyol_category_id ?? 0));

					$html_body .= '<tr>';
					$html_body .= '<td>'.($category->term_name ?? 'xx').'</td>';
					$html_body .= '<td>';
					$html_body .= '<div class="wc_trendyol_form_group_inline" data-wc_cat_id="'.$wc_cat_id.'" data-wc_parent_id="'.($category->parent).'">';
					$html_body .= '<select name="'.$wc_cat_id.'[trendyol_cat_id]" id="wc_trendyol_wc_cat_'.$wc_cat_id.'" class="wc_trendyol_normal_search form-required" aria-required="true" style="width: 100%">';
					$html_body .= '<option value="0">Trendyol kategorisi seçiniz</option>';
					$html_body .= $convert_option;
					$html_body .= '</select>';
					if(isset($category->sub) and $category->sub === true){
						$html_body .= '<button class="wc_trendyol_btn apply_sub_cat_main_trendyol_cat_id" data-wc_cat_id="'.($wc_cat_id ?? 0).'"><i class="fa-solid fa-arrow-down-wide-short"></i> Alt Kategorilere Uygula</button>';
					}
					$html_body .= '</div>';
					$html_body .= '</td>';
					$html_body .= '</tr>';

				}

				$html .= '
                <table class="wc_trendyol_table">
                    <thead>
                        <tr>
                            <th>Woocommerce Kategori</th>
                            <th>Trendyol Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$html_body.'
                    </tbody>
                </table>
                ';

			}
			else{
				$html = '<div class="wc_trendyol_alert">ÜRÜN BULUNAMADI</div>';
			}

			$results = [
				'status'     => 'success',
				'data'       => $html,
				'pagination' => $trendyol_admin->get_pagination_html(ceil($total_categories_count / $page_line_count), $paged),
			];

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();
		}
		//LOAD TABLE

		//PRODUCT MATCHING SAVE LINE
		public function wc_trendyol_product_matching_save_line(){
			global $trendyol_wc_adapter, $trendyol_admin, $trendyol_metas;

			try{

				$wc_product_id            = esc_attr($_POST['wc_trendyol_product_id']);
				$wc_trendyol_barcode      = esc_attr($_POST['wc_trendyol_barcode']) ?? null;
				$wc_trendyol_main_barcode = esc_attr($_POST['wc_product_main_sku_input']) ?? null;
				$wc_trendyol_sku          = esc_attr($_POST['wc_trendyol_product_sku_input']) ?? null;

				$results = $this->wc_trendyol_product_matching_save_one_product($wc_product_id, $wc_trendyol_main_barcode, $wc_trendyol_sku, $wc_trendyol_barcode);

				if($results){
					$results = [
						'status'  => 'success',
						'message' => __('Kayıt Edildi', 'wc_trendyol'),
					];
				}
				else{
					$results = [
						'status'  => 'danger',
						'message' => __('Kayıt edilemedi', 'wc_trendyol')
					];
				}


			}catch(Exception $err){
				$results = [
					'status'  => 'danger',
					'message' => $err->getMessage()
				];
			}


			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_product_matching_save_all(){
			global $trendyol_wc_adapter, $trendyol_admin, $trendyol_metas;

			parse_str($_POST['form_data'], $post);

			foreach($post as $wc_product_id => $data){

				$wc_trendyol_barcode      = $data['trendyol_barcode'] ?? null;
				$wc_trendyol_main_barcode = $data['main_sku'] ?? null;
				$wc_trendyol_sku          = $data['sku'] ?? null;

				$results = $this->wc_trendyol_product_matching_save_one_product($wc_product_id, $wc_trendyol_main_barcode, $wc_trendyol_sku, $wc_trendyol_barcode);

				if(isset($results['status']) and $results['status'] != 'success'){
					goto results;
				}

			}

			$results = [
				'status' => 'success',
			];

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		private function wc_trendyol_product_matching_save_one_product($wc_product_id, $main_sku_input, $sub_product_sku, $wc_trendyol_barcode){
			global $trendyol_metas;

			try{

				$product      = wc_get_product($wc_product_id);
				$product_type = $product->get_type();

				$trendyol_metas->update_meta_trendyol_barcode($wc_product_id, $wc_trendyol_barcode);
				$trendyol_metas->update_meta_trendyol_main_barcode($wc_product_id, $main_sku_input);

				if($product_type == 'variation'){
					$product->set_sku($sub_product_sku);
				}
				else if($product_type == 'variable'){
					$product->set_sku($main_sku_input);
				}
				else if($product_type == 'simple'){
					$product->set_sku($main_sku_input);
				}

				$product->save();

				$results = [
					'status' => 'success',
				];

			}catch(Exception $err){
				$results = [
					'status'  => 'danger',
					'message' => $err->getMessage()
				];
			}

			return $results ?? false;
		}
		//PRODUCT MATCHING SAVE LINE

		//BULK PRODUCT PROCESSES - SAVE WEBSITE LINE
		public function wc_trendyol_bulk_product_processes_save_line(){

			$wc_cat_id               = esc_attr($_POST['wc_cat_id']);
			$wc_product_id           = esc_attr($_POST['wc_product_id']);
			$trendyol_product_title  = esc_attr($_POST['trendyol_product_title']);
			$website_stock_qty       = esc_attr($_POST['website_stock_qty']);
			$trendyol_stock_qty      = esc_attr($_POST['trendyol_stock_qty']);
			$website_sale_price      = esc_attr($_POST['website_sale_price']);
			$website_discount_price  = esc_attr($_POST['website_discount_price']);
			$trendyol_sale_price     = esc_attr($_POST['trendyol_sale_price']);
			$trendyol_discount_price = esc_attr($_POST['trendyol_discount_price']);

			$results = $this->wc_trendyol_bulk_product_processes_save_one_product($trendyol_product_title, $wc_product_id, $website_stock_qty, $trendyol_stock_qty, $website_sale_price, $website_discount_price, $trendyol_sale_price, $trendyol_discount_price);

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_bulk_product_processes_save_all(){

			parse_str($_POST['form_data'], $posts);

			$all_success = null;
			$all_dangers = null;
			foreach($posts as $wc_product_id => $data){

				$trendyol_product_title  = esc_attr($data['trendyol_product_title']);
				$website_stock_qty       = esc_attr($data['website_stock_qty']);
				$trendyol_stock_qty      = esc_attr($data['trendyol_stock_qty']);
				$website_sale_price      = esc_attr($data['website_sale_price']);
				$website_discount_price  = esc_attr($data['website_discount_price'] ?? '');
				$trendyol_sale_price     = esc_attr($data['trendyol_sale_price']);
				$trendyol_discount_price = esc_attr($data['trendyol_discount_price'] ?? '');

				$save = $this->wc_trendyol_bulk_product_processes_save_one_product($trendyol_product_title, $wc_product_id, $website_stock_qty, $trendyol_stock_qty, $website_sale_price, $website_discount_price, $trendyol_sale_price, $trendyol_discount_price);
				if(isset($save['status']) and $save['status'] == 'success'){
					$all_success[] = $save;
				}
				else{
					$all_dangers[] = $save;
				}

			}

			$results = [
				'status'      => 'success',
				'all_success' => $all_success,
				'all_dangers' => $all_dangers,
				'message'     => 'Kayıt Edildi',
			];

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		private function wc_trendyol_bulk_product_processes_save_one_product($trendyol_product_title, $wc_product_id, $website_stock_qty, $trendyol_stock_qty, $website_sale_price, $website_discount_price, $trendyol_sale_price, $trendyol_discount_price){
			global $trendyol_adapter, $trendyol_metas;
			try{

				$product = wc_get_product($wc_product_id);

				$website_sale_price     = str_replace([','], ['.'], $website_sale_price);
				$website_discount_price = str_replace([','], ['.'], $website_discount_price);

				$trendyol_sale_price     = str_replace([','], ['.'], $trendyol_sale_price);
				$trendyol_discount_price = str_replace([','], ['.'], $trendyol_discount_price);

				//TRENDYOL SAVE
				$trendyol_metas->update_meta_trendyol_product_title($wc_product_id, $trendyol_product_title);
				$trendyol_metas->update_meta_trendyol_stock_quantity($wc_product_id, $trendyol_stock_qty);
				$trendyol_metas->update_meta_trendyol_sale_price($wc_product_id, $trendyol_sale_price);
				$trendyol_metas->update_meta_trendyol_discount_price($wc_product_id, $trendyol_discount_price);

				if(empty($trendyol_discount_price)){
					$discount_price = $trendyol_sale_price;
					$sale_price     = $trendyol_sale_price;
				}
				else if($trendyol_discount_price > $trendyol_sale_price){
					$discount_price = 0;
					$sale_price     = $trendyol_sale_price;
					$trendyol_metas->update_meta_trendyol_discount_price($wc_product_id, $discount_price);
				}
				else if($trendyol_discount_price <= $trendyol_sale_price){
					$sale_price     = $trendyol_discount_price;
					$discount_price = $trendyol_sale_price;
				}
				else{
					$sale_price     = $trendyol_discount_price;
					$discount_price = $trendyol_sale_price;
				}

				$wc_trendyol_barcode = $trendyol_metas->get_meta_trendyol_barcode($wc_product_id);
				$trendyol_adapter->update_product_price_and_stock($wc_trendyol_barcode, $trendyol_stock_qty, $discount_price, $sale_price);
				$trendyol_adapter->update_product_title($wc_trendyol_barcode, $trendyol_product_title);
				//TRENDYOL SAVE

				if($website_discount_price > 0 and $website_sale_price > $website_discount_price){
					//					$product->set_price($website_sale_price);
					$product->set_regular_price($website_sale_price);
					$product->set_sale_price($website_discount_price);
				}
				else{
					//					$product->set_price($website_sale_price);
					$product->set_regular_price($website_sale_price);
					$product->set_sale_price($website_sale_price);
				}

				$product->set_manage_stock(true);
				$product->set_stock_quantity($website_stock_qty);

				$product->save();

				$results = [
					'status'  => 'success',
					'message' => 'Kayıt Edildi',
				];

			}catch(Exception $err){
				$results = [
					'status'  => 'danger',
					'message' => $err->getMessage()
				];
			}

			return $results ?? false;
		}

		public function wc_trendyol_change_this_wc_cat_website_product_price(){
			global $trendyol_admin;

			$wc_cat_id = esc_attr($_POST['wc_cat_id']);

			if(!empty($wc_cat_id)){

				$value_input = esc_attr($_POST['wc_trendyol_change_website_price_value_input']);
				$action      = esc_attr($_POST['wc_trendyol_change_website_price_action_input']);
				$rate        = esc_attr($_POST['wc_trendyol_change_website_price_rate_input']);

				if(strlen($value_input) > 0 and $value_input > 0){

					$get_this_cat_products = $trendyol_admin->wc_trendyol_wc_all_products($wc_cat_id);
					foreach($get_this_cat_products as $get_this_cat_product){

						$wc_product = wc_get_product($get_this_cat_product->id);

						$old_regular_price = (float)$wc_product->get_regular_price(); //İNDİRİMSİZ FİYAT
						$old_sale_price    = (float)$wc_product->get_sale_price(); //İNDİRİMLİ FİYAT

						if(!empty($old_regular_price)){

							$new_regular_price = 0;
							$new_sale_price    = 0;
							if($action === '-'){
								$value = $value_input * -1;
							}
							else{
								$value = $value_input;
							}

							if($rate === 'sabit'){
								$new_regular_price = $old_regular_price + $value;
								$new_sale_price    = $old_sale_price + $value;
							}
							else if($rate === 'yuzde'){
								$new_regular_price = $old_regular_price + (($old_regular_price / 100) * $value);
								$new_sale_price    = $old_sale_price + (($old_sale_price / 100) * $value);
							}

							$new_regular_price = number_format($new_regular_price, 2, '.', '');
							$new_sale_price    = number_format($new_sale_price, 2, '.', '');

							$wc_product->set_regular_price($new_regular_price);

							if(!empty($old_sale_price)){
								$wc_product->set_sale_price($new_sale_price);
							}

							$wc_product->save();

						}

					}

					$results = [
						'status'  => 'success',
						'message' => 'Bu kategorideki tüm ürünlerin web site fiyatları değişti. Sayfayı yenileyin ve kontrol edin'
					];

				}
				else{
					$results = [
						'status'  => 'danger',
						'message' => 'Değer 0 dan küçük. Lütfen büyük bir değer girin.'
					];
				}

			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Kategori seçmemişsiniz. Lütfen önce kategori seçin'
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_change_all_website_product_price(){
			global $trendyol_admin;

			$value_input = esc_attr($_POST['wc_trendyol_change_website_price_value_input']);
			$action      = esc_attr($_POST['wc_trendyol_change_website_price_action_input']);
			$rate        = esc_attr($_POST['wc_trendyol_change_website_price_rate_input']);

			if(strlen($value_input) > 0 and $value_input > 0){

				$get_this_cat_products = $trendyol_admin->wc_trendyol_wc_all_products(null, false, 0, 10000);
				foreach($get_this_cat_products as $get_this_cat_product){

					$wc_product = wc_get_product($get_this_cat_product->id);

					$old_regular_price = (float)$wc_product->get_regular_price(); //İNDİRİMSİZ FİYAT
					$old_sale_price    = (float)$wc_product->get_sale_price(); //İNDİRİMLİ FİYAT

					if(!empty($old_regular_price)){

						$new_regular_price = 0;
						$new_sale_price    = 0;
						if($action === '-'){
							$value = $value_input * -1;
						}
						else{
							$value = $value_input;
						}

						if($rate === 'sabit'){
							$new_regular_price = $old_regular_price + $value;
							$new_sale_price    = $old_sale_price + $value;
						}
						else if($rate === 'yuzde'){
							$new_regular_price = $old_regular_price + (($old_regular_price / 100) * $value);
							$new_sale_price    = $old_sale_price + (($old_sale_price / 100) * $value);
						}

						$new_regular_price = number_format($new_regular_price, 2, '.', '');
						$new_sale_price    = number_format($new_sale_price, 2, '.', '');

						$wc_product->set_regular_price($new_regular_price);

						if(!empty($old_sale_price)){
							$wc_product->set_sale_price($new_sale_price);
						}

						$wc_product->save();

					}

				}

				$results = [
					'status'  => 'success',
					'message' => 'Bu kategorideki tüm ürünlerin web site fiyatları değişti. Sayfayı yenileyin ve kontrol edin'
				];

			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Değer 0 dan küçük. Lütfen büyük bir değer girin.'
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_change_this_wc_cat_trendyol_product_price(){
			global $trendyol_admin, $trendyol_metas;

			$wc_cat_id = esc_attr($_POST['wc_cat_id']);

			if(!empty($wc_cat_id)){

				$value_input = esc_attr($_POST['wc_trendyol_change_trendyol_price_value_input']);
				$action      = esc_attr($_POST['wc_trendyol_change_trendyol_price_action_input']);
				$rate        = esc_attr($_POST['wc_trendyol_change_trendyol_price_rate_input']);

				if(strlen($value_input) > 0 and $value_input > 0){

					$get_this_cat_products = $trendyol_admin->wc_trendyol_wc_all_products($wc_cat_id, false, 0, 10000);
					foreach($get_this_cat_products as $get_this_cat_product){

						$old_regular_price = $trendyol_metas->get_meta_trendyol_sale_price($get_this_cat_product->id); //İNDİRİMSİZ FİYAT
						$old_sale_price    = $trendyol_metas->get_meta_trendyol_discount_price($get_this_cat_product->id); //İNDİRİMLİ FİYAT

						$get_wc_product = wc_get_product($get_this_cat_product->id);

						if(is_null($old_regular_price)){
							$old_regular_price = $get_wc_product->get_regular_price(); //İNDİRİMSİZ FİYAT
						}

						if(is_null($old_sale_price)){
							$old_sale_price = $get_wc_product->get_sale_price(); //İNDİRİML FİYAT
						}

						if(!empty($old_regular_price)){

							$new_regular_price = 0;
							$new_sale_price    = 0;
							if($action === '-'){
								$value = $value_input * -1;
							}
							else{
								$value = $value_input;
							}

							if($rate === 'sabit'){
								$new_regular_price = $old_regular_price + $value;
								$new_sale_price    = $old_sale_price + $value;
							}
							else if($rate === 'yuzde'){
								$new_regular_price = $old_regular_price + (($old_regular_price / 100) * $value);
								$new_sale_price    = $old_sale_price + (($old_sale_price / 100) * $value);
							}

							$new_regular_price = number_format($new_regular_price, 2, '.', '');
							$new_sale_price    = number_format($new_sale_price, 2, '.', '');

							$trendyol_metas->update_meta_trendyol_sale_price($get_this_cat_product->id, $new_regular_price);

							if(!empty($old_sale_price)){
								$trendyol_metas->update_meta_trendyol_discount_price($get_this_cat_product->id, $new_sale_price);
							}

						}


					}

					$results = [
						'status'  => 'success',
						'message' => 'Bu kategorideki tüm trendyol fiyatları güncellendi ama trendyol paneline hemen yansımaz. Lütfen kontrol ettikten sonra tümünü kaydet butonuna basın ve trendyol panelinde yansımasını sağlayın'
					];

				}
				else{
					$results = [
						'status'  => 'danger',
						'message' => 'Değer 0 dan küçük. Lütfen büyük bir değer girin.'
					];
				}

			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Kategori seçmemişsiniz. Lütfen önce kategori seçin'
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}

		public function wc_trendyol_change_all_trendyol_product_price(){
			global $trendyol_admin, $trendyol_metas;

			$value_input = esc_attr($_POST['wc_trendyol_change_trendyol_price_value_input']);
			$action      = esc_attr($_POST['wc_trendyol_change_trendyol_price_action_input']);
			$rate        = esc_attr($_POST['wc_trendyol_change_trendyol_price_rate_input']);

			if(strlen($value_input) > 0 and $value_input > 0){

				$get_this_cat_products = $trendyol_admin->wc_trendyol_wc_all_products();
				foreach($get_this_cat_products as $get_this_cat_product){

					$old_regular_price = $trendyol_metas->get_meta_trendyol_sale_price($get_this_cat_product->id); //İNDİRİMSİZ FİYAT
					$old_sale_price    = $trendyol_metas->get_meta_trendyol_discount_price($get_this_cat_product->id); //İNDİRİMLİ FİYAT

					$get_wc_product = wc_get_product($get_this_cat_product->id);

					if(is_null($old_regular_price)){
						$old_regular_price = $get_wc_product->get_regular_price(); //İNDİRİMSİZ FİYAT
					}

					if(is_null($old_sale_price)){
						$old_sale_price = $get_wc_product->get_sale_price(); //İNDİRİML FİYAT
					}

					if(!empty($old_regular_price)){

						$new_regular_price = 0;
						$new_sale_price    = 0;
						if($action === '-'){
							$value = $value_input * -1;
						}
						else{
							$value = $value_input;
						}

						if($rate === 'sabit'){
							$new_regular_price = $old_regular_price + $value;
							$new_sale_price    = $old_sale_price + $value;
						}
						else if($rate === 'yuzde'){
							$new_regular_price = $old_regular_price + (($old_regular_price / 100) * $value);
							$new_sale_price    = $old_sale_price + (($old_sale_price / 100) * $value);
						}

						$new_regular_price = number_format($new_regular_price, 2, '.', '');
						$new_sale_price    = number_format($new_sale_price, 2, '.', '');

						$trendyol_metas->update_meta_trendyol_sale_price($get_this_cat_product->id, $new_regular_price);

						if(!empty($old_sale_price)){
							$trendyol_metas->update_meta_trendyol_discount_price($get_this_cat_product->id, $new_sale_price);
						}

					}

				}

				$results = [
					'status'  => 'success',
					'message' => 'Sitedeki tüm trendyol fiyatları güncellendi ama trendyol paneline hemen yansımaz. Lütfen kontrol ettikten sonra tümünü kaydet butonuna basın ve trendyol panelinde yansımasını sağlayın'
				];

			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Değer 0 dan küçük. Lütfen büyük bir değer girin.'
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? []);
			wp_die();
		}
		//BULK PRODUCT PROCESSES - SAVE WEBSITE LINE

		//ADD PLUGUN LIST DEACTIVE BUTTON MODAL
		private function get_uninstall_reasons(){

			$reasons = [
				[
					'id'     => 'setup-difficult',
					'text'   => __('Kullanımı çok zor. Döküman eksikliği var', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'textarea',
							'placeholder' => __('Eklentimizi kullanırken karşılaştığınız zorlukları açıklar mısınız?', 'wc-trendyol'),
						],
					],
				],
				[
					'id'     => 'not-have-that-feature',
					'text'   => __('Eklenti çok güzel ama benim ihtiyacımı karşılamıyor', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'textarea',
							'placeholder' => __('Neye ihtiyacınız olduğunu açıklar mısınız?', 'wc-trendyol'),
						],
					],
				],
				[
					'id'     => 'affecting-performance',
					'text'   => __('Eklenti web sitesi hızını etkiliyor', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'textarea',
							'placeholder' => __('Hangi işlemi yaparken web siteniz yavaşlıyor?', 'wc-trendyol'),
						],
					],
				],
				[
					'id'     => 'found-better-plugin',
					'text'   => __('Daha iyi bir eklenti buldum', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'text',
							'placeholder' => __('Bu eklentinin adını paylaşır mısınız?', 'wc-trendyol'),
						],
					],
				],
				[
					'id'     => 'trednyol-connection-issues',
					'text'   => __('Trendyo\'a bağlanırken sorun yaşıyorum', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'textarea',
							'placeholder' => __('Sorunu açıklayabilir misiniz?', 'wc-trendyol'),
						],
					],
				],
				[
					'id'   => 'temporary-deactivation',
					'text' => __('Geçici olarak devre dışı bırakıyorum', 'wc-trendyol'),
				],
				[
					'id'     => 'other',
					'text'   => __('Diğer', 'wc-trendyol'),
					'fields' => [
						[
							'type'        => 'textarea',
							'placeholder' => __('Lütfen açıklama yazın', 'wc-trendyol'),
						],
					],
				],
			];

			return $reasons;
		}

		public function wc_trendyol_add_plugin_list_deactive_button_modal(){
			global $pagenow, $trendyol_admin;
			if('plugins.php' !== $pagenow){
				return;
			}
			$reasons = $this->get_uninstall_reasons();
			?>
            <div class="wc_trendyol" id="wc_trendyol">
                <div class="wc_trendyol-wrap">
                    <div class="wc_trendyol-header">
                        <h3><?=esc_html__('Geri Bildirim Yollayın', 'wc-trendyol');?></h3>
                        <button type="button" class="wc_trendyol-close">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.572899 0.00327209C0.459691 0.00320032 0.349006 0.036716 0.254854 0.0995771C0.160701 0.162438 0.0873146 0.251818 0.0439819 0.356405C0.000649228 0.460992 -0.0106814 0.576084 0.0114242 0.687113C0.0335299 0.798142 0.0880779 0.900118 0.168164 0.980132L4.18928 5L0.168164 9.01987C0.0604905 9.12754 0 9.27358 0 9.42585C0 9.57812 0.0604905 9.72416 0.168164 9.83184C0.275838 9.93951 0.421875 10 0.574148 10C0.726422 10 0.872459 9.93951 0.980133 9.83184L5.00125 5.81197L9.02237 9.83184C9.13023 9.93836 9.2755 9.99844 9.4271 9.99923C9.5023 9.99958 9.57681 9.98497 9.6463 9.95623C9.71579 9.92749 9.77886 9.8852 9.83184 9.83184C9.93924 9.72402 9.99955 9.57804 9.99955 9.42585C9.99955 9.27367 9.93924 9.12768 9.83184 9.01987L5.81072 5L9.83184 0.980132C9.88515 0.926818 9.92744 0.863524 9.9563 0.793865C9.98515 0.724206 10 0.649547 10 0.574148C10 0.49875 9.98515 0.42409 9.9563 0.354431C9.92744 0.284772 9.88515 0.221479 9.83184 0.168164C9.77852 0.114849 9.71523 0.072558 9.64557 0.0437044C9.57591 0.0148507 9.50125 0 9.42585 0C9.35045 0 9.27579 0.0148507 9.20614 0.0437044C9.13648 0.072558 9.07318 0.114849 9.01987 0.168164L4.99813 4.19053L0.976385 0.170662C0.868901 0.0635642 0.723383 0.00338113 0.57165 0.00327209H0.572899Z"
                                      fill="#ffffff"/>
                            </svg>
                        </button>
                    </div>
                    <div class="wc_trendyol-body">
                        <h4 class="cky-feedback-caption"><?=esc_html__('Eklentimizi daha iyi hale getirebilmek için sizin deneyiminize ihtiyacımız var. Lütfen bizimle sorununuzu paylaşın', 'wc-trendyol');?></h4>
                        <ul class="cky-feedback-reasons-list">
							<?php
								foreach($reasons as $reason) :
									?>
                                    <li>
                                        <div class="cky-feedback-form-group">
                                            <label class="cky-feedback-label">
                                                <input type="radio" name="selected-reason" value="<?=esc_attr($reason['id']);?>" class="cky-feedback-input-radio"><?=esc_html($reason['text']);?></label>
											<?php
												$fields = (isset($reason['fields']) && is_array($reason['fields'])) ? $reason['fields'] : [];
												if(empty($fields)){
													continue;
												}
											?>
                                            <div class="cky-feedback-form-fields">
												<?php

													foreach($fields as $field) :
														$field_type = isset($field['type']) ? $field['type'] : 'text';
														$field_placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
														$field_key = isset($reason['id']) ? $reason['id'] : '';
														$field_name = $field_key.'-'.$field_type;
														if('textarea' === $field_type) :
															?>
                                                            <textarea rows="3" cols="45" class="cky-feedback-input-field" name="<?=esc_attr($field_name);?>" placeholder="<?=esc_attr($field_placeholder);?>"></textarea>
														<?php
														else :
															?>
                                                            <input class="cky-feedback-input-field" type="text" name="<?=esc_attr($field_name);?>" placeholder="<?=esc_attr($field_placeholder);?>">
														<?php
														endif;
													endforeach;
												?>
                                            </div>
                                        </div>
                                    </li>

								<?php
								endforeach; ?>
                        </ul>
                        <div class="wc_trendyol_form_group">
                            <label for="email" class="wc_trendyol_form_label">Size özel indirim için email adresinizi yazın</label>
                            <input type="text" id="email" name="email" class="wc_trendyol_form_input wc_trendyol_admin_email" value="<?=get_option('admin_email')?>" autocomplete="email">
                        </div>
                        <div class="wc_trendyol_form_group">
                            <label for="phone" class="wc_trendyol_form_label">Size özel indirim için telefo numaranızı yazın</label>
                            <input type="text" id="phone" name="phone" class="wc_trendyol_form_input wc_trendyol_admin_phone" value="<?=get_option('phone_number')?>" autocomplete="phone">
                        </div>
                    </div>

                    <div class="wc_trendyol-footer">
                        <button class="button-primary wc_trendyol-submit">
							<?=esc_html__('Geribildirimi Gönder ve Eklentiyi Pasif Yap', 'wc-trendyol');?>
                        </button>
                        <a class="cky-goto-support" href="https://hayatikodla.net/" target="_blank">
                            <span class="dashicons dashicons-external"></span>
							<?=esc_html__('Destek Alın', 'wc-trendyol');?>
                        </a>
                        <button class="button-secondary wc_trendyol-skip">
							<?=esc_html__('Sadece Eklentiyi Pasif Yap', 'wc-trendyol');?>
                        </button>
                    </div>
                </div>
            </div>

            <style type="text/css">
                .wc_trendyol {
                    position: fixed;
                    z-index: 99999;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    background: rgba(0, 0, 0, 0.5);
                    display: none;
                }
                .wc_trendyol.modal-active {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .wc_trendyol-wrap {
                    width: 600px;
                    position: relative;
                    background: #fff;
                }
                .wc_trendyol-header {
                    background: var(--button-background);
                    padding: 12px 20px;
                }
                .wc_trendyol-header h3 {
                    display: inline-block;
                    color: #fff;
                    line-height: 150%;
                    margin: 0;
                }
                .wc_trendyol-body {
                    font-size: 14px;
                    line-height: 2.4em;
                    padding: 5px 30px 20px 30px;
                    box-sizing: border-box;
                }
                .wc_trendyol-body h3 {
                    font-size: 15px;
                }
                .wc_trendyol-body .input-text,
                .wc_trendyol-body {
                    width: 100%;
                }
                .wc_trendyol-body .cky-feedback-input {
                    margin-top: 5px;
                    margin-left: 20px;
                }
                .wc_trendyol-footer {
                    padding: 0px 20px 15px 20px;
                    display: flex;
                }
                .cky-button-left {
                    float: left;
                }
                .cky-button-right {
                    float: right;
                }
                .cky-sub-reasons {
                    display: none;
                    padding-left: 20px;
                    padding-top: 10px;
                    padding-bottom: 4px;
                }
                .cky-uninstall-feedback-privacy-policy {
                    text-align: left;
                    font-size: 12px;
                    line-height: 14px;
                    margin-top: 20px;
                    font-style: italic;
                }
                .cky-uninstall-feedback-privacy-policy a {
                    font-size: 11px;
                    color: var(--button-background);
                    text-decoration-color: #99c3d7;
                }
                .cky-goto-support {
                    color: var(--button-background);;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    margin-left: 15px;
                }
                .wc_trendyol-footer .wc_trendyol-submit {
                    background-color: var(--button-background);
                    border-color: var(--button-background);
                    color: #FFFFFF;
                }
                .wc_trendyol-footer .wc_trendyol-submit:hover {
                    background-color: var(--button-background);
                    border-color: var(--button-background);
                    color: #FFFFFF;
                }
                .wc_trendyol-footer .wc_trendyol-skip {
                    font-size: 12px;
                    color: #a4afb7;
                    background: none;
                    border: none;
                    margin-left: auto;
                }
                .wc_trendyol-close {
                    background: transparent;
                    border: none;
                    color: #fff;
                    float: right;
                    font-size: 18px;
                    font-weight: lighter;
                    cursor: pointer;
                }
                .cky-feedback-caption {
                    font-weight: bold;
                    font-size: 15px;
                    color: #27283C;
                    line-height: 1.5;
                }
                input[type="radio"].cky-feedback-input-radio {
                    margin: 0 10px 0 0;
                    box-shadow: none;
                }
                .cky-feedback-reasons-list li {
                    line-height: 1.9;
                }
                .cky-feedback-label {
                    font-size: 13px;
                }
                .wc_trendyol .cky-feedback-input-field {
                    width: 98%;
                    display: flex;
                    padding: 5px;
                    -webkit-box-shadow: none;
                    box-shadow: none;
                    font-size: 13px;
                }
                .wc_trendyol input[type="text"].cky-feedback-input-field:focus {
                    -webkit-box-shadow: none;
                    box-shadow: none;
                }
                .cky-feedback-form-fields {
                    margin: 10px 0 0 25px;
                    display: none;
                }
            </style>

            <script type="text/javascript">
                jQuery(function($){

                    const modal        = $('#wc_trendyol');
                    let deactivateLink = '';
                    $(document).on('click', '.wc_trendyol-deactivate-link', function(e){
                        //
                        //var param = {
                        //    domain      : location.host,
                        //    plugin_name : '<?php //echo $this->plugin_name; ?>//',
                        //    action      : "deactive_plugin",
                        //    reason_title: "Eklentiyi Pasif Yapmaya Ã‡alıştı",
                        //    reason_value: "",
                        //}
                        //
                        //$.ajax({
                        //    url     : "<?php //echo WC_TRENDYOL_API_URL ?>///feedback?query=new_feedback",
                        //    type    : 'POST',
                        //    data    : {
                        //        param: JSON.stringify(param)
                        //    },
                        //    complete: function (){
                        //        // window.location.href = deactivateLink;
                        //    }
                        //});

                        modal.addClass('modal-active');
                        deactivateLink = $(this).attr('href');
                        modal.find('a.dont-bother-me').attr('href', deactivateLink).css('float', 'right');

                        return false;
                    });

                    modal.on('click', '.wc_trendyol-skip', function(e){
                        modal.removeClass('modal-active');
                        window.location.href = deactivateLink;
                        return false;
                    });

                    modal.on('click', '.wc_trendyol-close', function(e){
                        modal.removeClass('modal-active');
                        return false;
                    });
                    modal.on('click', 'input[type="radio"]', function(){
                        $('.cky-feedback-form-fields').hide();
                        const $parent = $(this).closest('.cky-feedback-form-group');
                        if(!$parent){
                            return;
                        }
                        const $fields = $parent.find('.cky-feedback-form-fields');
                        if(!$fields){
                            return;
                        }
                        const $input = $fields.find('.cky-feedback-input-field');
                        $input && $fields.show(), $input.focus();
                    });
                    modal.on('click', '.wc_trendyol-submit', function(e){
                        e.preventDefault();
                        const button = $(this);
                        if(button.hasClass('disabled')){
                            return;
                        }
                        const $radio  = $('input[type="radio"]:checked', modal);
                        const $parent = $radio && $radio.closest('.cky-feedback-form-group');
                        if(!$parent){
                            window.location.href = deactivateLink;
                            return;
                        }
                        const $input = $parent.find('.cky-feedback-input-field');

                        var param = {
                            domain      : location.host,
                            plugin_name : '<?=$trendyol_admin->plugin_name ?? 'wc-trendyol';?>',
                            email       : $('.wc_trendyol_admin_email').val(),
                            phone       : $('.wc_trendyol_admin_phone').val(),
                            action      : "deactive_plugin",
                            reason_id   : (0 === $radio.length) ? 'none' : $radio.val(),
                            reason_title: (0 === $radio.length) ? 'none' : $radio.closest('label').text(),
                            reason_value: (0 !== $input.length) ? $input.val().trim() : ''
                        }

                        $.ajax({
                            url       : "<?php echo WC_TRENDYOL_API_URL ?>/feedback?query=new_feedback",
                            type      : 'POST',
                            data      : {
                                param: JSON.stringify(param)
                            },
                            beforeSend: function(xhr){
                                button.addClass('disabled');
                                button.text('Lütfen Bekleyin...');
                                xhr.setRequestHeader('X-WP-Nonce', '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>');
                            },
                            complete  : function(){
                                window.location.href = deactivateLink;
                            }
                        });
                    });
                });
            </script>
			<?php
		}
		//ADD PLUGUN LIST DEACTIVE BUTTON MODAL

		//ADD PLUGIN LIST SETTINGS MENU
		function wc_trendyol_add_plugin_list_setting_menu($links){
			$links['deactivate'] = str_replace('<a', '<a class="wc_trendyol-deactivate-link"', $links['deactivate']);
			$links[]             = '<a href="https://hayatikodla.net/urun/woocommerce-trendyol-entegrasyonu-pro/" target="_blank">'.__('Destek').'</a>';
			$links[]             = '<a href="'.admin_url('admin.php?page=trendyol_settings').'">'.__('Trendyol Ayarları').'</a>';

			return $links;
		}
		//ADD PLUGIN LIST SETTINGS MENU

		//BULK IMAGE PROCESSES
		public function wc_trendyol_product_batch_images_processing_save_trendyol(){
			global $trendyol_metas, $trendyol_adapter, $trendyol_admin;

			$wc_product_id = esc_attr($_POST['wc_product_id']);

			$wc_product = wc_get_product($wc_product_id);

			$all_images_url = null;

			$get_product_main_image_id = $wc_product->get_image_id();
			$url                       = wp_get_attachment_image_src($get_product_main_image_id);
			if(is_array($url)){
				$all_images_url[] = ['url' => current($url)];
			}

			$all_images = $wc_product->get_gallery_image_ids();
			if($all_images != null){

				$trendyol_barcode = $trendyol_metas->get_meta_trendyol_barcode($wc_product_id);

				foreach($all_images as $image_id){
					$url              = wp_get_attachment_image_src($image_id);
					$all_images_url[] = ['url' => current($url)];
				}

				$update_images = $trendyol_adapter->update_product_images($trendyol_barcode, $all_images_url);

				if(isset($update_images->batchRequestId) and !empty($update_images->batchRequestId)){
					$results = [
						'status'  => 'success',
						'message' => 'Görseller yüklendi. Lütfen trendyol panelinden kontrol edin.',
					];
				}
				else{
					$results = [
						'status'  => 'danger',
						'message' => 'Görsel yüklenemedi. Loglara bakın'
					];
					$trendyol_admin->wc_trendyol_error_log('Görsel yüklenemedi : '.$trendyol_barcode.' - '.json_encode($update_images));
				}

			}


			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results ?? null);
			wp_die();

		}

		public function wc_trendyol_product_batch_images_processing_del_images(){

			$wc_product_id = esc_attr($_POST['wc_product_id']);
			$image_id      = esc_attr($_POST['image_id']);

			$wc_product = wc_get_product($wc_product_id);

			$all_images = $wc_product->get_gallery_image_ids();
			$all_images = array_diff($all_images, [$image_id]);

			$wc_product->set_gallery_image_ids($all_images);
			$save = $wc_product->save();
			if($save){
				$results = [
					'status' => 'success',
				];
			}
			else{
				$results = [
					'status' => 'danger',
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();

		}

		public function wc_trendyol_product_batch_images_processing_add_images(){

			$wc_product_id = esc_attr($_POST['wc_product_id']);
			$image_id      = esc_attr($_POST['image_id']);

			$wc_product = wc_get_product($wc_product_id);

			$all_images = $wc_product->get_gallery_image_ids();
			array_push($all_images, $image_id);
			$wc_product->set_gallery_image_ids($all_images);
			$save = $wc_product->save();
			if($save){
				$results = [
					'status' => 'success',
				];
			}
			else{
				$results = [
					'status' => 'danger',
				];
			}

			results:
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();

		}
		//BULK IMAGE PROCESSES

		//TOOLS
		public function wc_trendyol_delete_all_product_sync(){

			global $wpdb;

			$del_all_product_sync = $wpdb->query("DELETE FROM ".$wpdb->prefix."actionscheduler_actions WHERE hook LIKE 'wc_trendyol%'");
			if($del_all_product_sync){
				$results = [
					'status'  => 'success',
					'message' => 'Tüm aktarmalar silindi',
				];
			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Aktarmalar silinemedi',
				];
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();
		}

		public function wc_trendyol_delete_all_actions(){

			global $wpdb;

			$del_all_actions = $wpdb->query("TRUNCATE ".$wpdb->prefix."actionscheduler_actions");
			$del_all_actions = $wpdb->query("TRUNCATE ".$wpdb->prefix."actionscheduler_logs");
			if($del_all_actions){
				$results = [
					'status'  => 'success',
					'message' => 'Temizlendi',
				];
			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Temizlenemdi',
				];
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();
		}

		public function wc_trendyol_delete_all_product_trendyol_meta(){

			global $wpdb;

			$del_all_actions = $wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key LIKE 'wc_trendyol%'");
			if($del_all_actions){
				$results = [
					'status'  => 'success',
					'message' => 'Temizlendi',
				];
			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Temizlenemedi : '.$wpdb->last_error,
				];
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();
		}

		public function wc_trendyol_plugin_reset(){

			global $wpdb;

			$del_all_actions = $wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key LIKE 'wc_trendyol%'");
			$del_all_actions = $wpdb->query("DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'wc_trendyol%'");
			$del_all_actions = $wpdb->query("DELETE FROM ".$wpdb->prefix."termmeta WHERE meta_key LIKE 'wc_trendyol%'");
			if($del_all_actions){
				$results = [
					'status'  => 'success',
					'message' => 'Temizlendi',
				];
			}
			else{
				$results = [
					'status'  => 'danger',
					'message' => 'Temizlenemedi : '.$wpdb->error,
				];
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($results);
			wp_die();
		}
		//TOOLS

	}