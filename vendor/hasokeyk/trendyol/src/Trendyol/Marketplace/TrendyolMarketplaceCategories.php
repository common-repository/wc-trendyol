<?php

	namespace Hasokeyk\Trendyol\Marketplace;

	class TrendyolMarketplaceCategories{

		public  $supplierId;
		public  $username;
		public  $password;
		private $trendyol;

		function __construct($trendyol){
			$this->supplierId = $trendyol->supplierId;
			$this->username   = $trendyol->username;
			$this->password   = $trendyol->password;
			$this->trendyol   = $trendyol;
		}

		function request(){
			return $this->trendyol->request;
		}

		public function get_categories(){
			$url    = 'https://api.trendyol.com/sapigw/product-categories';
			$result = $this->request()->get($url);
			return $result;
		}

		public function get_my_categories(){

			$url    = 'https://www.trendyol.com/sr?mid='.$this->supplierId;
			$result = file_get_contents($url);
			preg_match_all('/{"id":"([0-9]+)","text":"([^"]+)","beautifiedName":"[^"]+","count":([^"]+),"filtered":false,"filterField":"(webCategoryIds|leafCategoryIds)","type":"(WebCategory|LeafCategory)","url":"[^"]+"}/is', $result, $matches);

			$supplider_cats = (object)[];
			$total_product  = 0;
			foreach($matches[1] as $id => $match){

				$category_info_json = json_decode(file_get_contents((__DIR__).'/../assets/category_info.json'), true);
				$keys               = $this->trendyol_array_search($category_info_json['Categories'], 'Name', trim($match));
				$supplider_cats->$id = (object)[
					'cat_id'   => trim($keys['Id']),
					'cat_name' => trim($match),
					'count'    => $matches[2][$id]
				];

				$total_product += $matches[2][$id] ?? 0;
			}

			$supplider_cats->total_product_count = $total_product;

			return $supplider_cats;
		}

		public function get_category_info($category_id = null){
			$url    = 'https://api.trendyol.com/sapigw/product-categories/'.$category_id.'/attributes';
			$result = $this->request()->get($url);
			return $result;
		}

		public function get_category_attr($category_id = null, $attr_id = null){
			$get_category_info = $this->get_category_info($category_id);
			foreach($get_category_info->categoryAttributes as $attr){
				if($attr->attribute->id == $attr_id){
					return $attr;
				}
			}
			return null;
		}

		public function get_product_parent_cat_list($barcode = null){
			$product        = $this->product()->get_my_product($barcode);
			$product_cat_id = $product->content[0]->pimCategoryId;
			$all_cat        = $this->get_categories();
			return $this->find_parent_categories($all_cat->categories, $product_cat_id);
		}

		public function search_category_attr_values($category_id = null, $attr_id = null, $search_text = null, $key = 'name'){
			$all_values        = null;
			$get_category_info = $this->get_category_info($category_id);
			foreach($get_category_info->categoryAttributes as $a_id => $attr){
				if($attr->attribute->id == $attr_id){
					$attr_values   = $get_category_info->categoryAttributes[$a_id]->attributeValues;
					$search_result = $this->array_search_cat_in_attr_value($attr_values, $search_text, $key);
					if($search_result != null){
						$all_values = $search_result;
					}
					break;
				}
			}

			return $all_values;
		}

		private function array_search_cat_in_attr_value(array $arr, string $patron, $key = 'name'): array{
			$patron = strstr('%', $patron) ? $patron : $patron.'%';
			return array_filter($arr, static function($value) use ($patron, $key): bool{
				return 1 === preg_match(sprintf('/^%s$/i', preg_replace('/(^%)|(%$)/', '.*', $patron)), $value->{$key});
			});
		}

		public $trendyol_array_search_result = null;

		public function trendyol_array_search($data = [], $key = null, $value = null){
			if(isset($data) and $data != null){

				foreach($data as $category){

					if(gettype($category) == 'array'){
						if(isset($category[$key]) and ($category[$key] == $value or $this->calculateSimilarityScore($category[$key], $value) > 80)){
							$this->trendyol_array_search_result = $category;
						}

						if(isset($category['Nodes']) and $category['Nodes'] != null){
							$this->trendyol_array_search($category['Nodes'], $key, $value);
						}
					}
					else if(gettype($category) == 'object'){
						if(isset($category->{$key}) and ($category->{$key} == $value or $this->calculateSimilarityScore($category->{$key}, $value) > 80)){
							$this->trendyol_array_search_result = $category;
						}

						if(isset($category->subCategories) and $category->subCategories != null){
							$this->trendyol_array_search($category->subCategories, $key, $value);
						}
					}
				}
			}
			else{
				return 2;
			}
			return $this->trendyol_array_search_result;
		}

		private function calculateSimilarityScore($string1, $string2){

			$string1 = str_replace(['\u0026', '&'], ['&', ' ve '], htmlspecialchars_decode($string1));
			$string2 = str_replace(['\u0026', '&'], ['&', ' ve '], htmlspecialchars_decode($string2));

			$levenshteinDistance = levenshtein($string1, $string2);
			$maxLength           = max(strlen($string1), strlen($string2));

			$similarityScore = 100 * (1 - $levenshteinDistance / $maxLength);

			return $similarityScore;
		}


		function find_parent_categories($categories, $category_id){
			foreach($categories as $category){
				if($category->id == $category_id){
					$breadcrumb[] = [
						'id'   => $category->id,
						'name' => $category->name
					];
					$parentId     = $category->parentId;
					while($parentId > 0){
						$parentCategory = $this->find_category_by_id($categories, $parentId);
						if($parentCategory){
							array_unshift($breadcrumb, ['id' => $parentCategory->id, 'name' => $parentCategory->name]);
							$parentId = $parentCategory->parentId;
						}
						else{
							break;
						}
					}
					return $breadcrumb;
				}
				if(!empty($category->subCategories)){
					$breadcrumb = $this->find_parent_categories($category->subCategories, $category_id);
					if(!empty($breadcrumb)){
						array_unshift($breadcrumb, ['id' => $category->id, 'name' => $category->name]);
						return $breadcrumb;
					}
				}
			}
			return [];
		}

		function find_category_by_id($categories, $category_id){
			foreach($categories as $category){
				if($category->id == $category_id){
					return $category;
				}
			}
			return null;
		}

	}