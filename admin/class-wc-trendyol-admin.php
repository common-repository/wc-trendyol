<?php
	global $trendyol_admin;

	class Wc_Trendyol_Admin{

		private $plugin_name;
		private $version;
		public  $license = null;

		public function __construct($plugin_name = 'wc-trendyol', $version = '3.0.0'){
			global $trendyol_admin, $trendyol_metas;

			$trendyol_admin    = $this;
			$this->plugin_name = $plugin_name;
			$this->version     = $version;

			new wc_trendyol_ajax_processes();

			//ADD MENU
			add_action('admin_menu', [$this, 'wc_trendyol_add_menu']);
			add_action('admin_bar_menu', [$this, 'wc_trendyol_add_admin_bar_menu'], 50);
			//ADD MENU

			//CATEGORIES MATHING SAVE
			add_action('wp_ajax_wc_trendyol_categories_matching_save_all', [$this, 'wc_trendyol_categories_matching_save_all']);
			//CATEGORIES MATHING SAVE
		}

		//SETTINGS MENU
		public function wc_trendyol_setting_menu(){

			$menus = [
				'general_settings'  => [
					'link'      => 'general_settings',
					'icon'      => '<i class="fa-solid fa-gear"></i>',
					'title'     => __('Ayarlar', 'wc-trendyol'),
					'help_text' => __('Bu sayfada lisans ve temel bilgilerinizi girebilirsiniz.', 'wc-trendyol'),
					'type'      => 'orj'
				],
				'other_plugins'     => [
					'link'      => 'other_plugins',
					'icon'      => '<i class="fa-solid fa-puzzle-piece"></i>',
					'title'     => __('Diğer Eklentiler', 'wc-trendyol'),
					'help_text' => __('İşinize yarayacak diğer eklentilerimiz', 'wc-trendyol'),
					'type'      => 'orj'
				],
				'cronjobs_settings' => [
					'link'      => 'cronjobs_settings',
					'icon'      => '<i class="fa-solid fa-clock"></i>',
					'title'     => __('Cron İşlemleri', 'wc-trendyol'),
					'help_text' => __('Otomatik yapılacak işlemler için tetikleyiciler', 'wc-trendyol'),
					'type'      => 'orj'
				],
				'tools'             => [
					'link'      => 'tools',
					'icon'      => '<i class="fa-solid fa-screwdriver-wrench"></i>',
					'title'     => __('Araçlar', 'wc-trendyol'),
					'help_text' => __('Araçlar ile trendyol eklentisinizi daha iyi yöntebilirsiniz', 'wc-trendyol'),
					'type'      => 'orj'
				],
			];

			$menus = apply_filters('wc_trendyol_setting_menu', $menus);

			return $menus;

		}
		//SETTINGS MENU

		//SETTINGS INPUTS
		public function wc_trendyol_setting_inputs(){

			$inputs = [
				'debug_log' => [
					'input_type'   => 'text',
					'label'        => __("DEBUG MODE", 'wc-trendyol'),
					'tooltip_text' => __("DİKKAT : Yazılımcı değilseniz açmanız tavsiye edilmez.", 'wc-trendyol'),
				],
				'test_mode' => [
					'input_type'   => 'text',
					'label'        => __("TEST MODE", 'wc-trendyol'),
					'tooltip_text' => __("DİKKAT : Yazılımcı değilseniz açmanız tavsiye edilmez.", 'wc-trendyol'),
					'value'        => true
				],
			];

			$inputs = apply_filters('wc_trendyol_setting_inputs', $inputs);

			return $inputs;

		}
		//SETTINGS INPUTS

		//SETTINGS CRONJOBS
		public function wc_trendyol_setting_cron_jobs_list(){
			$inputs = null;

			$inputs = apply_filters('wc_trendyol_setting_cron_jobs_list', $inputs);

			return $inputs;

		}
		//SETTINGS CRONJOBS

		//ADD MENU
		public function wc_trendyol_add_menu(){
			add_menu_page(__('Trendyol', 'wc-trendyol'), __('Trendyol', 'wc-trendyol'), 'manage_options', 'trendyol_settings', [$this, 'wc_trendyol_settings_page'], 'dashicons-store');
			add_submenu_page('trendyol_settings', __('Ayarlar', 'wc-trendyol'), __('Ayarlar', 'wc-trendyol'), 'manage_options', 'trendyol_settings', [$this, 'wc_trendyol_settings_page']);
			add_submenu_page('trendyol_settings', __('Ürün Eşleştir', 'wc-trendyol'), __('Ürün Eşleştir', 'wc-trendyol'), 'manage_options', 'product_matching', [$this, 'wc_trendyol_product_matching',]);
			add_submenu_page('trendyol_settings', __('Kategori Eşleştir', 'wc-trendyol'), __('Kategori Eşleştir', 'wc-trendyol'), 'manage_options', 'categories_matching', [$this, 'wc_trendyol_categories_matching',]);
			add_submenu_page('trendyol_settings', __('Toplu Ürün İşlemleri', 'wc-trendyol'), __('Toplu Ürün İşlemleri', 'wc-trendyol'), 'manage_options', 'bulk_product_processes', [$this, 'wc_trendyol_bulk_product_processes',]);
			add_submenu_page('trendyol_settings', __('Toplu Görsel İşlemleri', 'wc-trendyol'), __('Toplu Görsel İşlemleri', 'wc-trendyol'), 'manage_options', 'batch_images_processing', [$this, 'wc_trendyol_bulk_images_processing',]);
		}

		public function wc_trendyol_add_admin_bar_menu($admin_bar){
			$market_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512" style="fill:#fff"><path d="M547.6 103.8L490.3 13.1C485.2 5 476.1 0 466.4 0H109.6C99.9 0 90.8 5 85.7 13.1L28.3 103.8c-29.6 46.8-3.4 111.9 51.9 119.4c4 .5 8.1 .8 12.1 .8c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.2 0 49.3-11.4 65.2-29c16 17.6 39.1 29 65.2 29c4.1 0 8.1-.3 12.1-.8c55.5-7.4 81.8-72.5 52.1-119.4zM499.7 254.9l-.1 0c-5.3 .7-10.7 1.1-16.2 1.1c-12.4 0-24.3-1.9-35.4-5.3V384H128V250.6c-11.2 3.5-23.2 5.4-35.6 5.4c-5.5 0-11-.4-16.3-1.1l-.1 0c-4.1-.6-8.1-1.3-12-2.3V384v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V384 252.6c-4 1-8 1.8-12.3 2.3z"/></svg>';
			$admin_bar->add_menu([
				'id'    => 'trendyol_admin_bar_menu',
				'title' => $market_icon.' '.__('Trendyol', 'wc-trendyol'),
				'href'  => admin_url('admin.php?page=trendyol_settings'),
			]);

			$admin_bar->add_menu([
				'parent' => 'trendyol_admin_bar_menu',
				'id'     => 'trendyol_settings',
				'title'  => __('Trendyol Ayarları', 'wc-trendyol'),
				'href'   => admin_url('admin.php?page=trendyol_settings'),
			]);

			$admin_bar->add_menu([
				'parent' => 'trendyol_admin_bar_menu',
				'id'     => 'trendyol_product_matching',
				'title'  => __('Ürün Eşleştirme', 'wc-trendyol'),
				'href'   => admin_url('admin.php?page=product_matching'),
			]);

			$admin_bar->add_menu([
				'parent' => 'trendyol_admin_bar_menu',
				'id'     => 'trendyol_categories_matching',
				'title'  => __('Kategori Eşleştirme', 'wc-trendyol'),
				'href'   => admin_url('admin.php?page=categories_matching'),
			]);

			$admin_bar->add_menu([
				'parent' => 'trendyol_admin_bar_menu',
				'id'     => 'bulk_product_processes',
				'title'  => __('Toplu Ürün İşlemleri', 'wc-trendyol'),
				'href'   => admin_url('admin.php?page=bulk_product_processes'),
			]);

			$admin_bar->add_menu([
				'parent' => 'trendyol_admin_bar_menu',
				'id'     => 'trendyol_batch_images_processing',
				'title'  => __('Toplu Görsel İşlemleri', 'wc-trendyol'),
				'href'   => admin_url('admin.php?page=batch_images_processing'),
			]);
		}

		public function wc_trendyol_settings_page(){
			require (WC_TRENDYOL_DIR_PATH)."/admin/partials/wc_trendyol_settings_page.php";
		}

		public function wc_trendyol_product_matching(){
			require (WC_TRENDYOL_DIR_PATH)."/admin/partials/wc_trendyol_product_matching.php";
		}

		public function wc_trendyol_categories_matching(){
			require (WC_TRENDYOL_DIR_PATH)."/admin/partials/wc_trendyol_categories_matching.php";
		}

		public function wc_trendyol_bulk_product_processes(){
			require (WC_TRENDYOL_DIR_PATH)."/admin/partials/wc_trendyol_bulk_product_processes.php";
		}

		public function wc_trendyol_bulk_images_processing(){
			require (WC_TRENDYOL_DIR_PATH)."/admin/partials/wc_trendyol_bulk_images_processing.php";
		}
		//ADD MENU

		//CATEGORIES MATCHING SAVE LINE
		public function wc_trendyol_categories_matching_save_all(){
			global $trendyol_admin;

			try{
				parse_str($_POST['form_data'], $post);

				if(isset($post)){
					foreach($post as $term_id => $trendyol_cat_info){
						if(!empty($trendyol_cat_info['trendyol_cat_id'])){
							update_term_meta($term_id, 'wc_trendyol_category_id', $trendyol_cat_info['trendyol_cat_id']);
						}
						else{
							delete_term_meta($term_id, 'wc_trendyol_category_id');
						}
					}

					$results = [
						'status'  => 'success',
						'message' => 'Kategori seçenekleri kayıt edildi.',
					];
				}
				else{
					$results = [
						'status'  => 'danger',
						'message' => 'Lütfen tüm kategorileri eşleştirin',
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
		//CATEGORIES MATCHING SAVE LINE

		//UTILS
		public function wc_trendyol_error_log($str = null, $write_type = 'a'){
			$debug_mode = (new trendyol_metas())->get_wc_trendyol_meta_settings('debug_log');
			if($debug_mode){

				$debug_file_path = ABSPATH.'wc_trendyol_pro.log';

				if(file_exists($debug_file_path) and filesize($debug_file_path) > 10000){
					unlink($debug_file_path);
				}

				$run_log = debug_backtrace();

				$file     = $run_log[0]['file'] ?? 'XX';
				$filename = basename($file);
				$fileline = $run_log[0]['line'] ?? 'XX';

				$log = '['.date('d-m-Y-H-i-s').']:'.($str ?? 'XX')."\n";
				//                $log          = '['.date('d-m-Y-H-i-s').' '.$filename.':'.$fileline.']:'.($str ?? 'XX')."\n";
				$open         = fopen($debug_file_path, $write_type);
				$content_utf8 = iconv('UTF-8', 'ISO-8859-9', $log);
				$content_utf8 = str_replace(['ý', 'þ'], ['ı', 'ş'], $content_utf8);
				fwrite($open, $content_utf8);
				fclose($open);
			}
		}

		public function sort_terms_hierarchicaly(array $cats, $parentId = 0){
			$into = [];
			foreach($cats as $i => $cat){
				if($cat->parent == $parentId){
					$cat->children       = $this->sort_terms_hierarchicaly($cats, $cat->term_id);
					$into[$cat->term_id] = $cat;
				}
			}
			return $into;
		}

		public function generate_select_box_to_array($array, $selected_term_id = null, $indent = 0){
			foreach($array as $key => $value){
				$prefix = str_repeat("-", $indent * 2);

				if(is_array($value->children) && !empty($value->children)){
					echo '<option value="'.$value->term_id.'" '.($selected_term_id == $value->term_id ? 'selected' : '').'>'.$prefix.$value->name.'</option>';
					self::generate_select_box_to_array($value->children, $selected_term_id, $indent + 1);
				}

				else{
					echo '<option value="'.$value->term_id.'" '.($selected_term_id == $value->term_id ? 'selected' : '').'>'.$prefix.$value->name.' ('.$value->count.')</option>';
				}
			}
		}

		public function wc_trendyol_wc_all_products($cat = false, $just_trendyol_barcode = false, $page = 0, $line_count = 5, $all_product_list = false, $stock_status = null, $post_status = null){
			global $wpdb;

			$all_products = null;

			//SAYFALAMA
			$page_sql = $all_product_list ? '' : "LIMIT ".$line_count." OFFSET ".($page * $line_count);
			//SAYFALAMA

			//KATEGORİ FİLTRELEME
			if(is_numeric($cat)){
				$sql_cat = "
                INNER JOIN ".$wpdb->prefix."term_relationships AS tr ON p.ID = tr.object_id
                INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id  AND tt.term_taxonomy_id = ".$cat."
                ";
			}
			//KATEGORİ FİLTRELEME

			$sql               = "
				SELECT 
                    p.ID AS id
				FROM 
				    ".$wpdb->prefix."posts AS p 
				    ".($sql_cat ?? '')." 
			    WHERE 
			        post_type IN('product')
			        ".((isset($post_status)) ? "AND p.post_status IN('".$post_status."')" : "")."
		        ".$page_sql;
			$get_main_products = $wpdb->get_results($sql);
			if(!is_null($get_main_products)){
				$row = 0;
				foreach($get_main_products as $m_id => $main_product){

					$all_products[$row] = (object)[
						'id' => $main_product->id ?? null,
						//						'post_title'  => $main_product->post_title??null,
						//						'post_parent' => $main_product->post_parent??null,
						//						'post_type'   => $main_product->post_type??null,
					];

					$sub_sql          = "
					SELECT 
					    p.ID AS id,
					    (SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_stock' AND post_id = p.id LIMIT 1) AS STOCK
					FROM 
					    ".$wpdb->prefix."posts AS p
				    WHERE 
				        post_parent = '".$main_product->id."' AND 
				        post_type IN('product_variation')
				        ".((isset($post_status)) ? "AND p.post_status IN('".$post_status."')" : "")."
			        ".((isset($stock_status) and $stock_status == 'in_stock') ? "HAVING STOCK > 0" : ((isset($stock_status) and $stock_status == 'not_in_stock') ? "HAVING STOCK = 0" : ""));
					$get_sub_products = $wpdb->get_results($sub_sql);
					if(!is_null($get_sub_products)){
						foreach($get_sub_products as $sub_product){
							$row++;
							$all_products[$row] = (object)[
								'id' => $sub_product->id ?? null,
								//								'post_title'  => $sub_product->post_title??null,
								//								'post_parent' => $sub_product->post_parent??null,
								//								'post_type'   => $sub_product->post_type??null,
							];
						}
					}
					$row++;
				}
			}

			return (object)$all_products;

		}

		public function wc_trendyol_wc_all_products_old($cat = false, $just_trendyol_barcode = false, $page = 0, $line_count = 5, $all_product_list = false, $stock_status = null, $post_status = null){
			global $wpdb;

			if($just_trendyol_barcode === true){
				$sql_text = "
                AND p.ID IN (
                    SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'wc_trendyol_barcode' AND meta_value IS NOT NULL
                )
                ";
			}

			if(is_numeric($cat)){
				$sql_cat = "
                INNER JOIN ".$wpdb->prefix."term_relationships AS tr ON p.ID = tr.object_id
                INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id  AND tt.term_taxonomy_id = ".$cat."
                ";
			}

			$sub_sql      = "
                    WITH RECURSIVE ProductHierarchy AS (
                        (SELECT
                            p.ID AS id,
                            p.post_title,
                            p.post_parent,
                            ".(isset($sql_cat) and (is_numeric($sql_cat)) ? "tt.term_taxonomy_id AS category_id," : "")."
                            p.post_type,
                            ( SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_sku' AND post_id = p.ID LIMIT 1) AS SKU,
                            ( SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_stock' AND post_id = p.ID LIMIT 1) AS STOCK
                        FROM
                            ".$wpdb->prefix."posts AS p
                            ".($sql_cat ?? '')."
                        WHERE
                            p.post_type IN ( 'product', 'product_variation' )
                            AND p.post_parent = 0
                            ".((isset($post_status)) ? "AND p.post_status IN('".$post_status."')" : "")."
                            ".($sql_text ?? '')."
                            ".(($all_product_list === true) ? '' : "LIMIT ".$line_count." OFFSET ".($page * $line_count))."
                            )
                    
                        UNION ALL
                    
                        SELECT
                            p.ID AS id,
                            p.post_title,
                            p.post_parent,
                            ".((isset($sql_cat) and is_numeric($sql_cat)) ? "ph.category_id," : "")."
                            p.post_type,
                            ( SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_sku' AND post_id = p.ID LIMIT 1) AS SKU,
                            ( SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_stock' AND post_id = p.ID LIMIT 1) AS STOCK
                        FROM
                            ".$wpdb->prefix."posts AS p
                            INNER JOIN ProductHierarchy AS ph ON p.post_parent = ph.ID
                        WHERE
                            p.post_type = 'product_variation'
                            ".($sql_text ?? '')."
                    )
                    SELECT
                        id,
                            post_title,
                            post_parent,
                            post_type,
                            SKU,
                            STOCK
                    FROM ProductHierarchy
                    ".((isset($stock_status) and $stock_status == 'in_stock') ? "HAVING STOCK > 0" : ((isset($stock_status) and $stock_status == 'not_in_stock') ? "HAVING STOCK = 0" : ""))."
                    ORDER BY COALESCE(NULLIF(post_parent, 0), id), post_parent, id;
                    ";
			$all_products = null;
			$sub_products = $wpdb->get_results($sub_sql);
			if($sub_products != null){
				foreach($sub_products as $sub_product){
					$all_products[] = $sub_product;
				}
			}

			return $all_products;

		}

		public function wc_trendyol_wc_all_product_count($cat = 0){
			global $wpdb;

			//FİLRELENMEMİŞ KAÇ ADET ÜRÜN VAR
			$sql                = "
            SELECT COUNT(*) AS product_count
            FROM ".$wpdb->prefix."terms AS t
            JOIN ".$wpdb->prefix."term_taxonomy AS tt ON t.term_id = tt.term_id
            JOIN ".$wpdb->prefix."term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN ".$wpdb->prefix."posts AS p ON tr.object_id = p.ID
            WHERE tt.taxonomy = 'product_cat' AND tt.term_id = ".$cat." AND p.post_type = 'product' AND p.post_status = 'publish';
            ";
			$all_products_count = $wpdb->get_row($sql);
			return $all_products_count->product_count ?? 0;

		}

		public function get_pagination_html($total_pages = 0, $active_page = 1, $page_limit = 15){

			$html = '';
			if($total_pages > 0){

				$page = esc_attr($_GET['page'] ?? null);

				$html .= '<div class="wc_trendyol_pagination">';
				$html .= '<ul>';

				if(($active_page - 4) > 1){
					//İLK SAYFA
					$html .= '<li>';
					$html .= '<a href="?page='.($page).'?paged=1" data-paged="1">';
					$html .= '<<';
					$html .= '</a>';
					$html .= '</li>';
					//İLK SAYFA

					//ÖNCEKİ SAYFA
					$html .= '<li>';
					$html .= '<a href="?page='.($page).'&paged='.($active_page - 1).'" data-paged="'.($active_page - 1).'">';
					$html .= '<';
					$html .= '</a>';
					$html .= '</li>';
					//ÖNCEKİ SAYFA
				}

				// En fazla 5 sayfa sayısı
				$start_page = max(1, $active_page - floor($page_limit / 2));
				$end_page   = min($start_page + $page_limit - 1, $total_pages);

				for($i = $start_page; $i <= $end_page; $i++){
					if($i == $active_page){
						$html .= '<li class="active">';
						$html .= '<a href="?page='.($page).'&paged='.($i).'" data-paged="'.($i).'">';
						$html .= $i;
						$html .= '</a>';
						$html .= '</li>';
					}
					else{
						$html .= '<li>';
						$html .= '<a href="?page='.($page).'&paged='.($i).'" data-paged="'.($i).'">';
						$html .= $i;
						$html .= '</a>';
						$html .= '</li>';
					}
				}
				if($active_page < $total_pages){

					//SONRAKİ SAYFA
					$html .= '<li>';
					$html .= '<a href="?page='.($page).'&paged='.($active_page + 1).'" data-paged="'.($active_page + 1).'">';
					$html .= '>';
					$html .= '</a>';
					$html .= '</li>';
					//SONRAKİ SAYFA


					//SON SAYFA
					$html .= '<li>';
					$html .= '<a href="?page='.($page).'&paged='.($total_pages).'" data-paged="'.($total_pages).'">';
					$html .= '>>';
					$html .= '</a>';
					$html .= '</li>';
					//SON SAYFA
				}

				$html .= '</ul>';
				$html .= '</div>';

			}
			return $html;
		}

		public function trendyol_categories_array_to_select_option($categories = null, $depth = 0, $selected = 0){

			$html = '';
			if($categories != null){
				foreach($categories as $id => $category){
					if(isset($category->subCategories) and $category->subCategories != null){
						$html .= '<option value="'.$category->id.'" '.($selected == $category->id ? 'selected' : '').' disabled>'.str_repeat('-', ($depth)).$category->name.'</option>'."\n";
						$html .= $this->trendyol_categories_array_to_select_option($category->subCategories, ($depth + 1), $selected);
					}
					else{
						$html .= '<option value="'.$category->id.'" '.($selected == $category->id ? 'selected' : '').' data-parent_cat_id="'.($category->parentId ?? 'XX').'">'.str_repeat('-', ($depth)).$category->name.'</option>'."\n";
					}
				}
			}

			return $html;
		}

		public function calc_license_time($expired_time){
			$now        = time();
			$diff_time  = $expired_time - $now;
			$day_count  = floor($diff_time / (60 * 60 * 24));
			$hour_count = floor(($diff_time % (60 * 60 * 24)) / (60 * 60));

			if($day_count > 0){
				return $day_count.' '.__('Gün kaldı', 'wc-trendyol-pro');
			}
			else{
				return $hour_count.' '.__('Saat kaldı', 'wc-trendyol-pro');
			}
		}

		public function plugin_license_check($plugin_slug = null, $clear_control = false){
			global $trendyol_metas;

			try{

				if($clear_control){
					delete_transient('wc_trendyol_'.$plugin_slug.'_license_cache');
				}

				$license = get_transient('wc_trendyol_'.$plugin_slug.'_license_cache');
				if($license){
					$results = $license;
				}
				else{

					$wc_trendyol_pro_license = $trendyol_metas->get_wc_trendyol_meta_settings('license_'.$plugin_slug);
					$param                   = json_encode([
						'domain'            => home_url(),
						'license'           => $wc_trendyol_pro_license ?? 'no_license',
						'plugin_short_code' => $plugin_slug,
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

					if(isset($remote['body']) and !empty($remote['body'])){

						$response = json_decode($remote['body'] ?? []);
						if($response->status == 'success'){
							$results = (object)[
								'status' => 'success',
								'data'   => $response,
							];
							$trendyol_metas->update_wc_trendyol_meta_settings('license_'.$plugin_slug, $wc_trendyol_pro_license);
							set_transient('wc_trendyol_'.$plugin_slug.'_license_cache', $results, (60 * 10));
						}
						else{
							$results = $response;
						}
					}
					else{
						$results = (object)[
							'status'  => 'error',
							'message' => __("Hayatikodla.net'e bağlanamadı.", 'wc-trendyol'),
						];
					}

				}

			}catch(Exception $err){
				$results = (object)[
					'status'  => 'error',
					'message' => __("Bağlantı sorunu.", 'wc-trendyol-pro'),
				];
			}

			return $results;
		}

		public function wc_trendyol_wc_all_categories($wc_parent = 0, $page = 0, $line_count = 20, &$all_categories = [], $depth = 0){
			global $wpdb;

			// SAYFALAMA (sadece ana kategoriler için)
			if($wc_parent == 0){
				$page_sql = "LIMIT ".$line_count." OFFSET ".($page * $line_count);
			}
			else{
				$page_sql = ''; // Alt kategoriler için sayfalama yok
			}

			// SQL sorgusu: Ana ve alt kategorileri al
			$sql = "
            SELECT T.term_id, T.name, TT.parent, (SELECT COUNT(term_id) FROM ".$wpdb->prefix."term_taxonomy WHERE parent = TT.term_id) AS SUB
            FROM ".$wpdb->prefix."term_taxonomy AS TT, ".$wpdb->prefix."terms AS T 
            WHERE T.term_id = TT.term_id 
            AND TT.taxonomy = 'product_cat' 
            AND TT.parent = ".$wc_parent." 
            ORDER BY TT.term_id ASC ".$page_sql;

			$get_categories = $wpdb->get_results($sql);

			if(!is_null($get_categories)){
				foreach($get_categories as $main_wc_cat){
					// Kategorinin başına derinliğe göre "-" ekle
					$all_categories[] = (object)[
						'term_id'   => $main_wc_cat->term_id ?? null,
						'term_name' => str_repeat('-', $depth).' '.($main_wc_cat->name ?? null),
						'parent'    => $main_wc_cat->parent,
						'sub'       => ($main_wc_cat->SUB > 0),
					];

					// Alt kategoriler varsa tekrar çağır, derinliği bir artır
					$this->wc_trendyol_wc_all_categories($main_wc_cat->term_id, 0, 0, $all_categories, $depth + 1);

				}
			}

			return $all_categories;
		}


		public function wc_trendyol_wc_all_categories_count(){
			global $wpdb;
			$sql                 = "SELECT T.term_id,T.name FROM ".$wpdb->prefix."term_taxonomy AS TT, ".$wpdb->prefix."terms AS T WHERE T.term_id = TT.term_id AND TT.taxonomy = 'product_cat' AND TT.parent = 0 ORDER BY TT.term_id ASC";
			$get_main_categories = $wpdb->get_results($sql);
			return count((array)$get_main_categories);
		}
		//UTILS

		//ADMIN
		public function enqueue_styles(){
			wp_enqueue_style($this->plugin_name.'-select2', WC_TRENDYOL_DIR_URL.'admin/assets/vendor/select2/css/select2.min.css', [], $this->version, 'all');
			wp_enqueue_style($this->plugin_name.'-fontawesome', WC_TRENDYOL_DIR_URL.'admin/assets/vendor/fontawesome/css/all.min.css', [], $this->version, 'all');
			wp_enqueue_style($this->plugin_name, WC_TRENDYOL_DIR_URL.'admin/assets/css/root.css', [], $this->version, 'all');
		}

		public function enqueue_scripts(){
			wp_enqueue_script($this->plugin_name, WC_TRENDYOL_DIR_URL.'admin/assets/js/wc_trendyol_general.js', ['jquery'], $this->version, false);
			wp_enqueue_script($this->plugin_name.'-select2', WC_TRENDYOL_DIR_URL.'admin/assets/vendor/select2/js/select2.min.js', $this->version, true);
			wp_enqueue_script($this->plugin_name.'-sweetalert2', WC_TRENDYOL_DIR_URL.'admin/assets/vendor/sweetalert2/js/sweetalert2.js', $this->version, true);
		}

	}
