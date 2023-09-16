<?php
class ProductCategoryDB{
	static function get_item($cond,$item_per_page){
		return DB::fetch_all('
			SELECT
				product.id,product.status,product.name_'.Portal::language().' as name,product.description_'.Portal::language().' as description,
				product.small_thumb_url,
				product.name_id,
				category.name_id AS category_name_id
			FROM
				product
				INNER JOIN product_category ON product_category.product_id = product.id
				INNER JOIN category ON product_category.category_id = category.id AND category.type="PRODUCT"
			WHERE
				'.$cond.'
			ORDER BY
				product.position DESC,product.time DESC
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	function get_total_item($cond)
	{
		return DB::fetch('
				SELECT
					count(*) as acount
				FROM
					product
					INNER JOIN product_category ON product_category.product_id = product.id
				INNER JOIN category ON product_category.category_id = category.id AND category.type="PRODUCT"
				WHERE
					'.$cond.'
			');
	}
}
?>