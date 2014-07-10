<?
if (isset($_POST['param']) && trim($_POST['param']) != ''){
	parse_str(trim($_POST['param']), $newData);

	require_once ('../nutritionix.v1.1.php');
	$nutritionix = new Nutritionix($newData['apiID'], $newData['apiKey']);

	$newData['brand_name'] = trim($newData['brand_name']);

	$field_auto = NULL;
	if ( in_array( (int)$newData['field_auto'], array(1, 0) ) )
		$field_auto = (bool)$newData['field_auto'];

	$field_type = NULL;
	if ( in_array( (int)$newData['field_type'], array(1, 2) ) )
		$field_type = (int)$newData['field_type'];

	$min_score = NULL;
	$newData['min_score'] = (float)$newData['min_score'];
	if ($newData['min_score'] > 0)
		$min_score = $newData['min_score'];

	$newData['offset'] = (int)$newData['offset'];
	$newData['offset'] = $newData['offset'] > -1 ? $newData['offset'] : 0;

	$newData['limit'] = (int)$newData['limit'];
	$options['limit'] = $newData['limit'] > 9 ? $newData['limit'] : 10;

	$data = $nutritionix -> brandSearch(
															$newData['brand_name'],
															$field_auto, $field_type, $min_score,
															$newData['offset'], $newData['limit'],
															(bool)$newData['return_result_as_json'],
															(bool)$newData['return_request_url']
													);
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