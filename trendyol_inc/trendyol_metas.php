<?php

	class trendyol_metas{

		private static string $pro_prefix  = 'wc_trendyol_pro';
		private static string $free_prefix = 'wc_trendyol';
		public                $wc;

		function __construct(){

			if(!class_exists('WC_Product_Factory')){
				require (ABSPATH).'/wp-content/plugins/woocommerce/includes/class-wc-product-factory.php';
			}

			$this->wc = new WC_Product_Factory();
		}

		//SETTINGS
		public function get_wc_trendyol_meta_settings($meta_name, $default = null){
			$meta_value = get_option(self::$pro_prefix.'_'.$meta_name);
			if($meta_value === false){
				$meta_value = get_option(self::$free_prefix.'_'.$meta_name, $default);
			}
			return $meta_value;
		}

		public function update_wc_trendyol_meta_settings($meta_name, $new_value = null){
			if(!empty($meta_name)){
				update_option(self::$pro_prefix.'_'.$meta_name, $new_value);
				update_option(self::$free_prefix.'_'.$meta_name, $new_value);
				return true;
			}
			else if(strlen($meta_name) == 0){
				delete_option(self::$pro_prefix.'_'.$meta_name);
				delete_option(self::$free_prefix.'_'.$meta_name);
				return false;
			}

			return false;
		}

		//PRODUCT
		public function get_meta_trendyol_title($wc_product_id){
			$product      = $this->wc->get_product($wc_product_id);
			$product_name = get_post_meta($wc_product_id, 'wc_trendyol_title', true);
			$product_name = !empty($product_name) ? $product_name : $product->get_name();

			return $product_name;
		}

		public function get_meta_trendyol_description($wc_product_id){

			$product      = $this->wc->get_product($wc_product_id);
			$product_type = $product->get_type() ?? null;

			if($product_type == 'variation'){
				//ALT ÜRÜN
				$main_product = $this->wc->get_product($product->get_parent_id());
			}
			else if($product_type == 'variable'){
				//ANA ÜRÜN ÜRÜN
				$main_product = false;
			}
			else{
				//BASİT ÜRÜN
				$main_product = false;
			}

			$product_desc = $product->get_description();
			if(strlen($product_desc) === 0){
				$product_desc = $main_product ? $main_product->get_description() : '';
			}

			return $product_desc;
		}

		public function get_meta_trendyol_brand($wc_product_id){
			$main_brand                = get_option('wc_trendyol_main_brand', null) ?? '';
			$wc_trendyol_product_brand = get_post_meta($wc_product_id, 'wc_trendyol_brand', true);

			if(!empty($wc_trendyol_product_brand)){
				$product_brand = explode(':', $wc_trendyol_product_brand);
			}
			else if(!empty($main_brand)){
				$product_brand = explode(':', $main_brand);
			}

			return $product_brand;
		}

		public function get_meta_trendyol_images($wc_product_id){
			$product = $this->wc->get_product($wc_product_id);

			$images = null;

			if($product->get_type() == 'variation'){
				$parent_product_id = $product->get_parent_id();
				$parent_product    = $this->wc->get_product($parent_product_id);

				$parent_main_image_id     = $parent_product->get_image_id();
				$parent_gallery_image_ids = $parent_product->get_gallery_image_ids();
				if(is_numeric($parent_main_image_id)){
					$images[] = ['url' => wp_get_attachment_url($parent_main_image_id)];
				}
				foreach($parent_gallery_image_ids as $image){
					$images[] = ['url' => wp_get_attachment_url($image)];
				}
			}

			$main_image_id     = $product->get_image_id();
			$gallery_image_ids = $product->get_gallery_image_ids();
			if(is_numeric($main_image_id)){
				$images[] = ['url' => wp_get_attachment_url($main_image_id)];
			}
			foreach($gallery_image_ids as $image){
				$images[] = ['url' => wp_get_attachment_url($image)];
			}

			return $images;
		}

		public function get_meta_trendyol_category_id($wc_product_id){
			return get_post_meta($wc_product_id, 'wc_trendyol_category_id', true);
		}

		public function get_meta_trendyol_stock_quantity($wc_product_id){
			return get_post_meta($wc_product_id, 'wc_trendyol_stock_quantity', true);
		}

		public function get_meta_trendyol_sale_price($wc_product_id){
			$trendyol_sale_price = get_post_meta($wc_product_id, 'wc_trendyol_sale_price', true);
			if(strlen($trendyol_sale_price) > 0){
				return $trendyol_sale_price;
			}
			else{
				$wc_product = $this->wc->get_product($wc_product_id);
				return $wc_product->get_sale_price();
			}
		}

		public function get_meta_trendyol_discount_price($wc_product_id){
			$trendyol_discount_price = get_post_meta($wc_product_id, 'wc_trendyol_discount_price', true);
			if(strlen($trendyol_discount_price) > 0){
				return $trendyol_discount_price;
			}
			else{
				$wc_product = $this->wc->get_product($wc_product_id);
				return $wc_product->get_regular_price();
			}
		}

		public function get_meta_trendyol_main_barcode($wc_product_id){
			$main_barcode = get_post_meta($wc_product_id, 'wc_trendyol_main_barcode', true);
			if(strlen($main_barcode) > 0){
				return $main_barcode;
			}
			else{
				$wc_product      = $this->wc->get_product($wc_product_id);
				$wc_product_type = $wc_product->get_type();
				if($wc_product_type == 'variation'){
					$wc_parent_product_id = $wc_product->get_parent_id();
					$main_barcode         = $this->wc->get_product($wc_parent_product_id)->get_sku();
				}
				else{
					$main_barcode = $wc_product->get_sku();
				}
				return $main_barcode;
			}
		}

		public function get_meta_trendyol_barcode($wc_product_id){
			$trendyol_barcode = get_post_meta($wc_product_id, 'wc_trendyol_barcode', true);
			if(strlen($trendyol_barcode) > 0){
				return $trendyol_barcode;
			}
			else{
				$wc_product = $this->wc->get_product($wc_product_id);
				return $wc_product->get_sku();
			}
		}

		public function get_meta_trendyol_barcode_status($wc_product_id){
			return get_post_meta($wc_product_id, 'wc_trendyol_barcode_status', true);
		}

		public function get_meta_trendyol_attr($wc_product_id){
			return json_decode(get_post_meta($wc_product_id, 'wc_trendyol_trenyol_cat_attr', true));
		}

		public function get_meta_trendyol_show_customer_questions($wc_product_id){
			return json_decode(get_post_meta($wc_product_id, 'wc_trendyol_show_customer_questions', true));
		}

		public function get_meta_trendyol_show_customer_comments($wc_product_id){
			return json_decode(get_post_meta($wc_product_id, 'wc_trendyol_show_customer_comments', true));
		}

		public function get_meta_trendyol_custom_meta($wc_product_id, $meta_name = null){
			return get_post_meta($wc_product_id, 'wc_trendyol_'.$meta_name, true);
		}

		public function update_meta_trendyol_product_title($wc_product_id, $title){
			if(!empty($title)){
				return update_post_meta($wc_product_id, 'wc_trendyol_title', $title);
			}
			else if(empty($title) and strlen($title) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_title');
			}

			return null;
		}

		public function update_meta_trendyol_product_brand($wc_product_id, $brand){
			if(!empty($brand)){
				return update_post_meta($wc_product_id, 'wc_trendyol_brand', $brand);
			}
			else if(empty($brand) and strlen($brand) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_brand');
			}

			return null;
		}

		public function update_meta_trendyol_category_id($wc_product_id, $trendyol_category_id = null){
			if(!empty($trendyol_category_id)){
				return update_post_meta($wc_product_id, 'wc_trendyol_category_id', $trendyol_category_id, null);
			}
			else if(strlen($trendyol_category_id) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_category_id');
			}

			return null;
		}

		public function update_meta_trendyol_stock_quantity($wc_product_id, $stock_quantity = null){
			if(strlen($stock_quantity) > 0){
				return update_post_meta($wc_product_id, 'wc_trendyol_stock_quantity', $stock_quantity);
			}
			else if(empty($stock_quantity) and strlen($stock_quantity) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_stock_quantity');
			}

			return 0;
		}

		public function update_meta_trendyol_sale_price($wc_product_id, $price = null){
			if(strlen($price) > 0){
				return update_post_meta($wc_product_id, 'wc_trendyol_sale_price', $price);
			}
			else if(empty($price) and strlen($price) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_sale_price');
			}

			return 0;
		}

		public function update_meta_trendyol_discount_price($wc_product_id, $price = null){
			if(strlen($price) > 0){
				return update_post_meta($wc_product_id, 'wc_trendyol_discount_price', $price);
			}
			else if(empty($price) and strlen($price) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_discount_price');
			}

			return 0;
		}

		public function update_meta_trendyol_barcode($wc_product_id, $barcode = null){
			if(!empty($barcode)){
				return update_post_meta($wc_product_id, 'wc_trendyol_barcode', $barcode);
			}
			else if(strlen($barcode) == 0){
				delete_post_meta($wc_product_id, 'wc_trendyol_barcode');

				return delete_post_meta($wc_product_id, 'wc_trendyol_barcode_status');
			}

			return null;
		}

		public function update_meta_trendyol_barcode_status($wc_product_id, $status = false){
			if($status){
				return update_post_meta($wc_product_id, 'wc_trendyol_barcode_status', $status);
			}
			else{
				return delete_post_meta($wc_product_id, 'wc_trendyol_barcode_status');
			}
		}

		public function update_meta_trendyol_main_barcode($wc_product_id, $main_barcode = null){
			if(!empty($main_barcode)){
				return update_post_meta($wc_product_id, 'wc_trendyol_main_barcode', $main_barcode);
			}
			else if(empty($main_barcode) and strlen($main_barcode) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_main_barcode');
			}

			return null;
		}

		public function update_meta_trendyol_attr($wc_product_id, $attr_array = null){
			if(!empty($attr_array)){
				$attr = json_encode($attr_array, JSON_UNESCAPED_UNICODE);
				return update_post_meta($wc_product_id, 'wc_trendyol_trenyol_cat_attr', $attr);
			}
			else if(empty($attr_array)){
				return delete_post_meta($wc_product_id, 'wc_trendyol_trenyol_cat_attr');
			}

			return false;
		}

		public function update_meta_trendyol_show_customer_questions($wc_product_id, $show = null){
			if(!empty($show)){
				return update_post_meta($wc_product_id, 'wc_trendyol_show_customer_questions', $show);
			}
			else if(empty($show) and strlen($show) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_show_customer_questions');
			}

			return false;
		}

		public function update_meta_trendyol_show_customer_comments($wc_product_id, $show = null){
			if(!empty($show)){
				return update_post_meta($wc_product_id, 'wc_trendyol_show_customer_comments', $show);
			}
			else if(empty($show) and strlen($show) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_show_customer_comments');
			}

			return false;
		}

		public function update_meta_trendyol_custom_data($wc_product_id, $meta_name = null, $value = null){
			if(strlen($value) > 0){
				return update_post_meta($wc_product_id, 'wc_trendyol_'.$meta_name, $value);
			}
			else if(empty($value) and strlen($value) == 0){
				return delete_post_meta($wc_product_id, 'wc_trendyol_'.$meta_name);
			}

			return false;
		}

	}

	global $trendyol_metas;

	$trendyol_metas = new trendyol_metas();