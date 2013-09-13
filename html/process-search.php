<?
if (isset($_POST['param']) && trim($_POST['param']) != ''){
	parse_str(trim($_POST['param']), $newData);
	$newData['item_name'] = trim($newData['item_name']);
	$newData['item_name'] .= '';
	if ($newData['item_name'] == '')
		$data = array('result' => 'fail');
	else{
		require_once ('../nutritionix.v1.1.php');
		$nutritionix = new Nutritionix($newData['apiID'], $newData['apiKey']);

		$newData['brand_name'] = trim($newData['brand_name']);

		$newData['offset'] = (int)$newData['offset'];
		$newData['offset'] = $newData['offset'] > -1 ? $newData['offset'] : 0;

		$newData['limit'] = (int)$newData['limit'];
		$options['limit'] = $newData['limit'] > 9 ? $newData['limit'] : 10;

		$sort = array();
		$newData['sort_field'] .= '';
		if ($newData['sort_field'] != '' && $newData['sort_order'] !== NULL && in_array( $newData['sort_order'], array('asc', 'desc') )){
			$sort['field'] = $newData['sort_field'];
			$sort['order'] = $newData['sort_order'];
		}

		$newData['filters'] .= '';
		$newData['filters'] = trim($newData['filters']);
		if ($newData['filters'] != ''){
			//check if the value is a valid json array
			$checkJSON = json_decode($newData['filters'], true);
			$filters = $checkJSON == null || $checkJSON == false ? array() : $checkJSON;
		}else
			$filters = array();

		$min_score = NULL;
		$newData['min_score'] = (float)$newData['min_score'];
		if ($newData['min_score'] > 0)
			$min_score = $newData['min_score'];

		$newData['fields'] .= '';
		$newData['fields'] = trim($newData['fields']);
		if ($newData['fields'] != ''){
			//check if the value is a valid json array
			$checkJSON = json_decode($newData['fields'], true);
			$fields = $checkJSON == null || $checkJSON == false ? array() : $checkJSON;
		}else
			$fields = array();

		$allergen_contains_milk = NULL;
		if ( in_array((int)$newData['allergen_contains_milk'], array(1, 0) ) )
			$allergen_contains_milk = (bool)$newData['allergen_contains_milk'];

		$allergen_contains_eggs = NULL;
		if ( in_array((int)$newData['allergen_contains_eggs'], array(1, 0) ) )
			$allergen_contains_eggs = (bool)$newData['allergen_contains_eggs'];

		$allergen_contains_fish = NULL;
		if ( in_array((int)$newData['allergen_contains_fish'], array(1, 0) ) )
			$allergen_contains_fish = (bool)$newData['allergen_contains_fish'];

		$allergen_contains_shellfish = NULL;
		if ( in_array((int)$newData['allergen_contains_shellfish'], array(1, 0) ) )
			$allergen_contains_shellfish = (bool)$newData['allergen_contains_shellfish'];

		$allergen_contains_tree_nuts = NULL;
		if ( in_array((int)$newData['allergen_contains_tree_nuts'], array(1, 0) ) )
			$allergen_contains_tree_nuts = (bool)$newData['allergen_contains_tree_nuts'];

		$allergen_contains_peanuts = NULL;
		if ( in_array((int)$newData['allergen_contains_peanuts'], array(1, 0) ) )
			$allergen_contains_peanuts = (bool)$newData['allergen_contains_peanuts'];

		$allergen_contains_wheat = NULL;
		if ( in_array((int)$newData['allergen_contains_wheat'], array(1, 0) ) )
			$allergen_contains_wheat = (bool)$newData['allergen_contains_wheat'];

		$allergen_contains_soybeans = NULL;
		if ( in_array((int)$newData['allergen_contains_soybeans'], array(1, 0) ) )
			$allergen_contains_soybeans = (bool)$newData['allergen_contains_soybeans'];

		$allergen_contains_gluten = NULL;
		if ( in_array((int)$newData['allergen_contains_gluten'], array(1, 0) ) )
			$allergen_contains_gluten = (bool)$newData['allergen_contains_gluten'];

		$data = $nutritionix -> search(
																$newData['item_name'],
																$newData['brand_name'],
																$newData['offset'], $newData['limit'],
																$min_score,
																$fields,
																$allergen_contains_milk, $allergen_contains_eggs, $allergen_contains_fish,
																$allergen_contains_shellfish, $allergen_contains_tree_nuts, $allergen_contains_peanuts,
																$allergen_contains_wheat, $allergen_contains_soybeans, $allergen_contains_gluten,
																$sort,
																$filters,
																(bool)$newData['return_result_as_json']
														);
	}
}else
	$data = array('result' => 'fail');

if ( (bool)$newData['return_result_as_json'] ){
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Type: application/json');
	echo json_encode($data);
}else
	print_r($data);
exit;
?>