<?
if (isset($_POST['param']) && trim($_POST['param']) != ''){
	parse_str(trim($_POST['param']), $newData);
	$newData['phrase'] = trim($newData['phrase']);
	$newData['phrase'] .= '';
	if ($newData['phrase'] == '')
		$data = array('result' => 'fail');
	else{
		require_once ('../nutritionix.v1.1.php');
		$nutritionix = new Nutritionix($newData['apiID'], $newData['apiKey']);

		$brand_id = NULL;
		$newData['brand_id'] .= '';
		if ($newData['brand_id'] != '')
			$brand_id = $newData['brand_id'];

		$newData['rangeStart'] = (int)$newData['rangeStart'];
		$newData['rangeStart'] = $newData['rangeStart'] > -1 ? $newData['rangeStart'] : 0;

		$newData['rangeEnd'] = (int)$newData['rangeEnd'];
		$options['rangeEnd'] = $newData['rangeEnd'] > 0 ? $newData['rangeEnd'] : 10;

		/*
		$sort_field = NULL;
		$sort_order = NULL;
		$newData['sort_field'] .= '';
		if ($newData['sort_field'] != ''){
			$sort_field = $newData['sort_field'];
			if ($newData['sort_order'] !== NULL && in_array( $newData['sort_order'], array('asc', 'desc') ) )
				$sort_order = $newData['sort_order'];
		}
		*/

		$min_score = NULL;
		$newData['min_score'] = (int)$newData['min_score'];
		if ($newData['min_score'] > 0)
			$min_score = $newData['min_score'];

		$cal_min = NULL;
		$newData['cal_min'] = (int)$newData['cal_min'];
		if ($newData['cal_min'] > -1)
			$cal_min = $newData['cal_min'];

		$cal_max = NULL;
		$newData['cal_max'] = (int)$newData['cal_max'];
		if ($newData['cal_max'] > 0)
			$cal_max = $newData['cal_max'];

		$fields = NULL;
		$newData['fields'] .= '';
		if ($newData['fields'] != '')
			$fields = $newData['fields'];

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

		$data = $nutritionix -> search($newData['phrase'], $brand_id, $newData['rangeStart'], $newData['rangeEnd'], $min_score,
																		$cal_min, $cal_max, $fields, $allergen_contains_milk, $allergen_contains_eggs,
																		$allergen_contains_fish, $allergen_contains_shellfish, $allergen_contains_tree_nuts,
																		$allergen_contains_peanuts, $allergen_contains_wheat, $allergen_contains_soybeans,
																		$allergen_contains_gluten, (bool)$newData['return_result_as_json'],
																		(bool)$newData['return_request_url']);
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