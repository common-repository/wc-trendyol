<?php

	use Hasokeyk\Trendyol\Trendyol;

	class trendyol_adapter{

		private $trendyol;

		public function __construct(){

			$supplier_id = get_option('wc_trendyol_supplier', null);
			$username    = get_option('wc_trendyol_username', null);
			$password    = get_option('wc_trendyol_password', null);
			$test_mode   = get_option('wc_trendyol_test_mode', false);

			$trendyol                      = new Trendyol($supplier_id, $username, $password, $test_mode);
			$trendyol->request->cache_time = 5;

			$this->trendyol = $trendyol->TrendyolMarketplace();

		}

		public function get_brands(){
			$trendyol_brands = $this->trendyol->TrendyolMarketplaceBrands();
			$brands          = $trendyol_brands->get_brands();

			return $brands;
		}

		public function get_shipment_companies(){
			$trendyol_shipments = $this->trendyol->TrendyolMarketplaceShipment();
			$shipments          = $trendyol_shipments->get_shipment_companies();

			return $shipments;
		}

		public function search_brand($brand_name = ''){
			$trendyol_brands = $this->trendyol->TrendyolMarketplaceBrands();
			$brands          = $trendyol_brands->search_brand($brand_name);

			return $brands;
		}

		public function get_my_all_products($filters = null, $cache = true){
			$trendyol_products = $this->trendyol->TrendyolMarketplaceProducts();
			$products          = $trendyol_products->get_my_products($filters);

			return $products;
		}

		public function get_my_product($barcode = ''){
			$trendyol_products = $this->trendyol->TrendyolMarketplaceProducts();
			return $trendyol_products->get_my_product($barcode);
		}

		public function get_my_categories(){
			$trendyol_my_categories = $this->trendyol->TrendyolMarketplaceCategories();
			return $trendyol_my_categories->get_my_categories();
		}

		public function update_product_price_and_stock($barcode = '', $quantity = null, $list_price = null, $sale_price = null){
			$trendyol_products = $this->trendyol->TrendyolMarketplaceProducts();
			$update_price      = $trendyol_products->update_product_price_and_stock($barcode, $quantity, $sale_price, $list_price);

			return $update_price;
		}

		public function get_product_comments($sku = null){
			$trendyol_question = $this->trendyol->TrendyolMarketplaceProducts();
			$product_question  = $trendyol_question->get_product_comment($sku);

			return $product_question;
		}

		public function add_product_control($main_barcode = null, $barcode = null, $title = null, $description = null, $images = null, $vat_rate = null, $quantity = null, $list_price = null, $sale_price = null, $brand_id = null, $category_id = null, $product_attributes = null, $cargo_company_id = null){

			global $trendyol_wc_adapter;
			$product_control = true;
			$message         = 'Sorun Yok';

			if(is_null($main_barcode)){
				$message         = 'Ana Sku veya Barkod Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(strlen($main_barcode) == 0){
				$message         = 'Ana Sku veya Barkod Zorunludur';
				$product_control = false;
				goto result;
			}

			if(is_null($barcode)){
				$message         = 'Sku veya Barkod Zorunludur';
				$product_control = false;
				goto result;
			}

			if(is_null($title)){
				$message         = 'Ürün Başlığı Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(strlen($title) > 500){
				$message         = 'Ürün Başlığı 100 Karakterden Büyük Olamaz';
				$product_control = false;
				goto result;
			}
			else if(strlen($title) < 10){
				$message         = 'Ürün Başlığı 10 Karakterden Az Olamaz';
				$product_control = false;
				goto result;
			}

			if(is_null($description)){
				$message         = 'Ürün Açıklaması Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(is_string($description) and strlen($description) === 0){
				$message         = 'Ürün Açıklaması Zorunludur';
				$product_control = false;
				goto result;
			}

			if(is_null($images)){
				$message         = 'Ürün Görselleri Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(!is_array($images)){
				$message         = 'Ürün Görselleri Yanlış Gönderildi Zorunludur';
				$product_control = false;
				goto result;
			}

			if(is_null($vat_rate)){
				$message         = 'Ürün Vergi Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(!in_array($vat_rate, [0, 1, 8, 10, 18, 20, 25])){
				$message         = 'Ürün Vergi Değeri Yanlış Gönderildi. Gönderilen Değer : '.($vat_rate ?? 'Boş');
				$product_control = false;
				goto result;
			}

			if(is_null($quantity)){
				$message         = 'Ürün Stok Bilgisi Zorunludur';
				$product_control = false;
				goto result;
			}
			else if($quantity < 1){
				$message         = 'Ürün Stok Değeri 1den Az Olamaz';
				$product_control = false;
				goto result;
			}

			if(is_null($sale_price) and strlen($sale_price) > 0){
				$message         = 'Ürün satış fiyatı zorunludur';
				$product_control = false;
				goto result;
			}
			else if($sale_price < 1){
				$message         = 'Ürün satış fiyatı 1tl den az olamaz';
				$product_control = false;
				goto result;
			}
			else if($list_price > $sale_price){
				$message         = 'İndirimli fiyat, satış fiyatından fazla olamaz';
				$product_control = false;
				goto result;
			}

			if(is_null($brand_id)){
				$message         = 'Ürün Markası Zorunludur';
				$product_control = false;
				goto result;
			}

			if(is_null($cargo_company_id)){
				$message         = 'Ürün Kargo Firması Zorunludur';
				$product_control = false;
				goto result;
			}
			else if(!in_array($cargo_company_id, [42, 38, 36, 34, 39, 35, 30, 12, 13, 14, 10, 19, 9, 17, 6, 20, 4, 7])){
				$message         = 'Ürün Kargo Firması Değeri Yanlıştır. Girilen Değer : '.($cargo_company_id);
				$product_control = false;
				goto result;
			}

			if(is_null($category_id) or empty($category_id) or strlen($category_id) == 0){
				$message         = 'Ürün Kategorisi Zorunludur';
				$product_control = false;
				goto result;
			}
			else{
				$get_cat_info = $this->get_category_info($category_id);

				if(!isset($get_cat_info->name)){
					$message         = 'Ürün Kategorisi Değeri Yanlıştır';
					$product_control = false;
					goto result;
				}
				else if(isset($get_cat_info->categoryAttributes[0])){
					foreach($get_cat_info->categoryAttributes as $attr){

						if($attr->required and !isset($product_attributes->{$attr->attribute->id})){
							//EĞER ATTR DEĞERİ YOKSA VARSAYILAN OLARAK KAYIT EDİLMİŞ Mİ ONA BAKIYORUZ
							$get_trendyol_attr_default = $trendyol_wc_adapter->get_term_meta_trendyol_attr_default($attr->attribute->id);
							if(is_null($get_trendyol_attr_default)){
								$message         = 'Ürün Zorunlu Nitelik "'.($attr->attribute->name).'" Değeri Yok';
								$product_control = false;
								goto result;
							}
							//EĞER ATTR DEĞERİ YOKSA VARSAYILAN OLARAK KAYIT EDİLMİŞ Mİ ONA BAKIYORUZ

						}
					}
				}
			}

			result:
			return [
				'status'  => $product_control ? 'success' : 'fail',
				'message' => $message,
			];
		}

		public function update_product($barcode = null, $title = null, $description = null, $image = null, $vat_rate = 18, $quantity = 1, $list_price = null, $sale_price = null, $brand_id = null, $category_id = null, $product_attributes = null){
			$trendyol_product = $this->trendyol->TrendyolMarketplaceProducts();

			$new_product_attributes = [];
			$get_cat_info           = $this->get_category_info($category_id);
			foreach($get_cat_info->categoryAttributes as $attr){
				if(isset($product_attributes->{$attr->attribute->id})){
					if($attr->allowCustom){
						$new_product_attributes[] = [
							'attributeId'          => $attr->attribute->id,
							'customAttributeValue' => ($product_attributes->{$attr->attribute->id}),
						];
					}
					else{
						if(!empty($product_attributes->{$attr->attribute->id})){
							$new_product_attributes[] = [
								'attributeId'      => $attr->attribute->id,
								'attributeValueId' => ($product_attributes->{$attr->attribute->id}),
							];
						}
					}
				}
			}

			$update_product = $trendyol_product->update_product_info($barcode, [
				'title'             => $title,
				'barcode'           => $barcode,
				'productMainId'     => $barcode,
				'brandId'           => $brand_id,
				'categoryId'        => $category_id,
				'quantity'          => $quantity,
				'stockCode'         => $barcode,
				'dimensionalWeight' => 1,
				'description'       => $description,
				'currencyType'      => 'TRY',
				'listPrice'         => $list_price,
				'salePrice'         => $sale_price,
				'images'            => $image,
				'vatRate'           => $vat_rate,
				'attributes'        => $new_product_attributes,
			]);

			$this->update_product_price_and_stock($barcode, $quantity, $list_price, $sale_price);

			return $update_product;
		}

		public function add_product($main_code = null, $barcode = null, $title = null, $description = null, $images = null, $vat_rate = 20, $quantity = 1, $discount_price = null, $sale_price = null, $brand_id = null, $category_id = null, $product_attributes = null, $cargo_company_id = null){
			global $trendyol_wc_adapter;
			$trendyol_product = $this->trendyol->TrendyolMarketplaceProducts();

			$new_product_attributes = [];
			$get_cat_info           = $this->get_category_info($category_id);
			foreach($get_cat_info->categoryAttributes as $attr){
				if(isset($product_attributes->{$attr->attribute->id})){
					if($attr->allowCustom){
						$new_product_attributes[] = [
							'attributeId'          => $attr->attribute->id,
							'customAttributeValue' => ($product_attributes->{$attr->attribute->id}),
						];
					}
					else{

						if(!empty($product_attributes->{$attr->attribute->id})){
							$new_product_attributes[] = [
								'attributeId'      => $attr->attribute->id,
								'attributeValueId' => ($product_attributes->{$attr->attribute->id}),
							];
						}
						else{
							$get_trendyol_attr_default = $trendyol_wc_adapter->get_term_meta_trendyol_attr_default($attr->attribute->id);
							if(!is_null($get_trendyol_attr_default)){
								$new_product_attributes[] = [
									'attributeId'      => $attr->attribute->id,
									'attributeValueId' => $get_trendyol_attr_default,
								];
							}
						}
					}
				}
			}


			if($discount_price <= $sale_price and $discount_price > 0 and !empty($discount_price)){
				$sale_price = $discount_price;
				$list_price = $sale_price;
			}
			else{
				$list_price = $sale_price;
			}

			return $trendyol_product->create_product([
				'barcode'        => $barcode,
				'title'          => $title,
				'productMainId'  => strlen($main_code) > 0 ? $main_code : $barcode,
				'brandId'        => $brand_id,
				'categoryId'     => $category_id,
				'quantity'       => $quantity,
				'stockCode'      => $barcode,
				'description'    => $description,
				'currencyType'   => 'TRY',
				'listPrice'      => str_replace(',', '.', $list_price), //PSF - Satış fiyatı
				'salePrice'      => str_replace(',', '.', $sale_price), //TSF - İndirimli satış fiyatı
				'cargoCompanyId' => $cargo_company_id,
				'images'         => $images,
				'vatRate'        => $vat_rate,
				'attributes'     => $new_product_attributes,
			]);
		}

		public function add_multi_product($products = null){
			global $trendyol_wc_adapter, $trendyol_admin, $trendyol_adapter;

			$trendyol_product = $this->trendyol->TrendyolMarketplaceProducts();

			$suitable_products = null;
			foreach($products as $p_id => $product){

				$barcode          = $product['barcode'] ?? null;
				$title            = $product['title'] ?? null;
				$main_barcode     = $product['productMainId'] ?? null;
				$brand_id         = $product['brandId'] ?? null;
				$trendyol_cat_id  = $product['categoryId'] ?? null;
				$stock_qty        = $product['quantity'] ?? null;
				$desc             = $product['description'] ?? null;
				$images           = $product['images'] ?? null;
				$vat              = $product['vatRate'] ?? null;
				$list_price       = $product['listPrice'] ?? null;
				$sale_price       = $product['salePrice'] ?? null;
				$attrs            = $product['attributes'] ?? null;
				$cargo_company_id = $product['cargoCompanyId'] ?? null;

				$product_control = $this->add_product_control($main_barcode, $barcode, $title, $desc, $images, $vat, $stock_qty, $list_price, $sale_price, $brand_id, $trendyol_cat_id, $attrs, $cargo_company_id);
				$trendyol_admin->wc_trendyol_error_log('Kontrol : '.$barcode.' - '.json_encode($product_control));
				if($product_control['status'] == 'success'){

					$new_product_attributes = [];
					$get_cat_info           = $trendyol_adapter->get_category_info($trendyol_cat_id);
					foreach($get_cat_info->categoryAttributes as $attr){
						if(isset($attrs->{$attr->attribute->id})){
							if($attr->allowCustom){
								$new_product_attributes[] = [
									'attributeId'          => $attr->attribute->id,
									'customAttributeValue' => ($attrs->{$attr->attribute->id}),
								];
							}
							else{

								if(!empty($attrs->{$attr->attribute->id})){
									$new_product_attributes[] = [
										'attributeId'      => $attr->attribute->id,
										'attributeValueId' => ($attrs->{$attr->attribute->id}),
									];
								}
								else{
									$get_trendyol_attr_default = $trendyol_wc_adapter->get_term_meta_trendyol_attr_default($attr->attribute->id);
									if(!is_null($get_trendyol_attr_default)){
										$new_product_attributes[] = [
											'attributeId'      => $attr->attribute->id,
											'attributeValueId' => $get_trendyol_attr_default,
										];
									}
								}
							}
						}
					}

					$suitable_products[$p_id]               = $product;
					$suitable_products[$p_id]['attributes'] = $new_product_attributes;
				}
			}

			$trendyol_admin->wc_trendyol_error_log('Dizi : '.json_encode($suitable_products));

			if(is_null($suitable_products)){
				$trendyol_admin->wc_trendyol_error_log('Çoklu gönderilecek ürün bulunamadı');
				return false;
			}

			return $trendyol_product->create_multi_product($suitable_products);
		}

		public function get_all_categories(){
			$trendyol_categories = $this->trendyol->TrendyolMarketplaceCategories();
			$categories          = $trendyol_categories->get_categories();
			return $categories;
		}

		public function get_category_info($category_id = 0){
			$trendyol_categories = $this->trendyol->TrendyolMarketplaceCategories();
			$categories          = $trendyol_categories->get_category_info($category_id);

			return $categories;
		}

		public function get_product_parent_cat_list($category_id = 0){
			$trendyol_categories = $this->trendyol->TrendyolMarketplaceCategories();
			$categories          = $trendyol_categories->get_product_parent_cat_list($category_id);

			return $categories;
		}


		public function search_category_attr_values($cat_id = 0, $attr_id = 0, $search_text = '', $key = 'name'){
			$trenyol_categories = $this->trendyol->TrendyolMarketplaceCategories();
			$categories         = $trenyol_categories->search_category_attr_values($cat_id, $attr_id, $search_text, $key);
			return $categories;
		}

		public function get_category_attr_info($category_id = 0){
			$trendyol_categories = $this->trendyol->TrendyolMarketplaceCategories();
			$categories          = $trendyol_categories->get_category_info($category_id);

			return $categories;
		}

		public function get_product_questions($sku = null){
			$trendyol_question = $this->trendyol->TrendyolMarketplaceCustomerQuestions();
			$product_question  = $trendyol_question->get_product_question_web($sku);

			return $product_question;
		}

		public function wc_metakey_exists($meta_key, $meta_value){
			global $wpdb;

			return $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '".$meta_key."' AND meta_value = '".$meta_value."'");
		}

		public function get_batch_status($batch_code = null){
			$trendyol_product = $this->trendyol->TrendyolMarketplaceProducts();

			return $trendyol_product->get_batch_request_result($batch_code);
		}

		public function get_suppliers_address(){
			$trendyol_product = $this->trendyol->TrendyolMarketplaceAddresses();

			return $trendyol_product->get_my_addresses();
		}

		public function create_trendyol_product($product_ids = []){

			if($product_ids != null){

				foreach($product_ids as $product_id){

					$get_product = wc_get_product($product_id);

				}

			}

			return false;
		}

		public function get_trendyol_to_website_suitable_product(){
			$my_products = [];

			$args            = [
				'post_type'      => 'product',
				'posts_per_page' => -1,
			];
			$get_my_products = new WP_Query($args);

			if(isset($get_my_products)){

				foreach($get_my_products->posts as $wc_product){

					$wc_product_id = $wc_product->ID;
					$get_product   = wc_get_product($wc_product_id);

					$product_title                = get_post_meta($wc_product_id, 'wc_trendyol_product_trendyol_title', true);
					$product_title                = !empty($product_title) ? $product_title : $wc_product->post_title;
					$product_desc                 = $wc_product->post_content;
					$product_image                = $get_product->get_image('woocommerce_gallery_thumbnail');
					$product_barcode              = get_post_meta($wc_product_id, 'wc_trendyol_product_trendyol_barcode', true);
					$product_list_price           = get_post_meta($wc_product_id, 'wc_trendyol_product_trendyol_list_price', true);
					$product_sale_price           = get_post_meta($wc_product_id, 'wc_trendyol_product_trendyol_sales_price', true);
					$product_vat                  = $get_product->get_tax_status();
					$product_stock_quantity       = get_post_meta($wc_product_id, 'wc_trendyol_product_stock_quantity', true);
					$wc_trendyol_trenyol_cat_attr = json_decode(get_post_meta($wc_product_id, 'wc_trendyol_trenyol_cat_attr', true), true);
					$cargo_company_id             = 4;

					$main_brand                = get_option('wc_trendyol_main_brand', null) ?? '';
					$wc_trendyol_product_brand = get_post_meta($wc_product_id, 'wc_trendyol_product_brand', true);
					if(!empty($wc_trendyol_product_brand)){
						$product_brand = explode(':', $wc_trendyol_product_brand);
					}
					else if(!empty($main_brand)){
						$product_brand = explode(':', $main_brand);
					}

					$trendyol_product_cat_id = get_post_meta($wc_product_id, 'wc_trendyol_product_trendyol_cat_id', true);
					$trendyol_product_cat_id = !empty($trendyol_product_cat_id) ? $trendyol_product_cat_id : ($wc_trendyol_product->pimCategoryId ?? get_term_meta(current($get_product->get_category_ids()), 'trendyol_wc_category_id', true));

					$images            = null;
					$main_image_id     = $get_product->get_image_id();
					$gallery_image_ids = $get_product->get_gallery_image_ids();
					if(is_numeric($main_image_id)){
						$images[] = ['url' => wp_get_attachment_url($main_image_id)];
					}
					foreach($gallery_image_ids as $image){
						$images[] = ['url' => wp_get_attachment_url($image)];
					}

					$product_attributes = null;
					if(isset($wc_trendyol_trenyol_cat_attr)){
						foreach($wc_trendyol_trenyol_cat_attr as $attr_id => $att_val){
							$product_attributes[$attr_id] = $att_val;
						}
					}

					$product_control = $this->add_product_control($product_barcode, $product_title, $product_desc, $images, $product_vat, $product_stock_quantity, $product_list_price, $product_sale_price, $product_brand[0], $trendyol_product_cat_id, $product_attributes, $cargo_company_id);
					if($product_control['status'] == 'success'){
						$my_products[$wc_product_id] = [
							'barcode'           => $product_barcode,
							'title'             => $product_title,
							'productMainId'     => $product_barcode,
							'brandId'           => $product_brand[0],
							'categoryId'        => $trendyol_product_cat_id,
							'quantity'          => $product_stock_quantity,
							'stockCode'         => $product_barcode,
							'dimensionalWeight' => 1,
							'description'       => $product_desc,
							'currencyType'      => 'TRY',
							'listPrice'         => $product_list_price,
							'salePrice'         => $product_sale_price,
							'vatRate'           => $product_sale_price,
							'cargoCompanyId'    => $product_sale_price,
							"deliveryOption"    => [
								"deliveryDuration" => 1,
								"fastDeliveryType" => "SAME_DAY_SHIPPING",
							],
							"images"            => $images,
							"attributes"        => $product_attributes,
						];
					}
				}

				return $my_products;
			}

			return false;
		}

		public function update_product_title($barcode = null, $title = null){
			return $this->trendyol->TrendyolMarketplaceProducts()->update_product_title($barcode, $title);
		}

		public function update_product_images($barcode, $images){
			$trendyol_products = $this->trendyol->TrendyolMarketplaceProducts();
			$update_price      = $trendyol_products->update_product_images($barcode, $images);

			return $update_price;
		}

		public function get_orders($filters = null){
			return $this->trendyol->TrendyolMarketplaceOrders()->get_my_orders($filters);
		}

	}

	global $trendyol_adapter;

	$trendyol_adapter = new trendyol_adapter();