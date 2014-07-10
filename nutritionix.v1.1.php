<?php
//
// +---------------------------------------------------------------------------+
// | Nutritionix PHP API Library	     		                                     |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2013 Nutritionix	                                           |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+
//

if ( !function_exists('curl_init') ){
	throw new Exception('CURL is required to run the Nutritionix PHP API.');
}

if ( !function_exists('json_decode') ){
	throw new Exception('JSON Extension is required to run the Nutritionix PHP API.');
}


class Nutritionix
{
	private $app_id;
	private $api_key;
	//for testing this on your local machine, change https to http
	private $api_url = "https://api.nutritionix.com/v1_1/";
	private $api_url_search_post = "https://api.nutritionix.com/v1_1/search";


	/**
	 * Create the Nutritionix API client.
	 *
	 * @param string app_id		Nutritionix application ID
	 * @param string api_key	Nutritionix API key
	 */
	public function __construct ($app_id, $api_key){
		$this->app_id	= $app_id;
		$this->api_key	= $api_key;
	}


	/**
	 * Pass a search term into the API like taco, or cheese fries, and the API will return an array of matching foods.
	 * for more details on what values you can use, please go to https://developer.nutritionix.com/docs/v1_1
	 * demo http://dev2.nutritionix.com/html/nutritionix-api/html/demo-search.html
	 *
	 * @param string $item_name => The phrase or term that you would like to search for on the item name field
	 * @param string $brand_name (optional) => The phrase or terms you would like to search for on the brand name field
	 *                   if you supply this value then queries will be used instead of query
	 * @param int $offset (Optional) => The starting point in the result set for paging
	 * @param int $limit (Optional) => The max number of results to be returned in the page
	 *                   by default, the api will fetch the first 10 results
	 * @param int $min_score (Optional) => the minimum score that you want any returned document to have
	 * @param array fields	(Optional)The fields from an item you would like to return in the results.
	 *                   Supports all item properties in comma delimited format.
	 *                   An empty array parameter will return all item fields
	 * @param bool $allergen_contains_* (Optional) => this will filter the result in case you want to return results with
	 *                   very specific allergens
	 * @param array $sort (Optional) => an array to customize how the result will be sorted
	 * @param array $filters (Optional) => an array to filter the results to allow more customized results
	 * @param bool $returnJson (Optional) => This will handle if the return value is array or json string
	 *                   The dev needs to make sure that the json result is returned with a json header,
	 *                   the api lib just returns the json string value
	 *
	 * @return The results as a PHP array or json string depending on the $returnJson parameter
	 */
	public function search(
			$item_name,
			$brand_name = NULL,
			$offset = 0, $limit = 10,
			$min_score = NULL,
			$fields = array(),
			$allergen_contains_milk = NULL, $allergen_contains_eggs = NULL, $allergen_contains_fish = NULL,
			$allergen_contains_shellfish = NULL, $allergen_contains_tree_nuts = NULL, $allergen_contains_peanuts = NULL,
			$allergen_contains_wheat = NULL, $allergen_contains_soybeans = NULL, $allergen_contains_gluten = NULL,
			$sort = array(),
			$filters = array(),
			$returnJson = false
	){
		$options = array();

		$brand_name = trim($brand_name);
		$brand_name .= '';
		//if brand name is not empty then use QUERIES instead of QUERY
		if ($brand_name != '')
			$options['queries'] = array(
				'item_name' => trim($item_name),
				'brand_name' => trim($brand_name),
			);
		else
			$options['query'] = trim($item_name);

		$offset = (int)$offset;
		$offset = $offset > -1 ? $offset : 0;
		$options['offset'] = $offset;
		$limit = (int)$limit;
		$limit = $limit > 9 ? $limit : 10;
		$options['limit'] = $limit;

		if (is_array($sort) && count($sort) == 2 && $sort['field'] != null && $sort['field'] != '' &&
				$sort['order'] !== NULL && in_array( $sort['order'], array('asc', 'desc') ) ){
			$options['sort']['field'] = $sort['field'];
			$options['sort']['order'] = $sort['order'];
		}

		if (is_array($filters) && count($filters) > 0)
			$options['filters'] = $filters;

		$min_score = (float)$min_score;
		if ($min_score > 0)
			$options['min_score'] = $min_score;


		if ( is_array($fields) ){
			$acceptedFields = array(
				'nf_serving_size_qty',
				'nf_serving_size_unit',
				'nf_water_grams',
				'nf_serving_weight_grams',
				'nf_servings_per_container',

				'nf_calories',
				'nf_calories_from_fat',
				'nf_total_fat',
				'nf_trans_fatty_acid',
				'nf_saturated_fat',
				'nf_polyunsaturated_fat',
				'nf_monounsaturated_fat',
				'nf_cholesterol',
				'nf_sodium',
				'nf_total_carbohydrate',
				'nf_dietary_fiber',
				'nf_sugars',
				'nf_protein',

				'nf_vitamin_a_dv',
				'nf_vitamin_c_dv',
				'nf_calcium_dv',
				'nf_iron_dv',

				'nf_ingredient_statement',
				'nf_refuse_pct',

				'allergen_contains_eggs',
				'allergen_contains_fish',
				'allergen_contains_gluten',
				'allergen_contains_milk',
				'allergen_contains_peanuts',
				'allergen_contains_shellfish',
				'allergen_contains_soybeans',
				'allergen_contains_tree_nuts',
				'allergen_contains_wheat',

				'_id',
				'item_id',
				'brand_id',
				'remote_db_id',

				'item_name',
				'brand_name',
				'item_type',
				'item_description',
				'upc',
				'leg_loc_id',
				'remote_db_key',
				'old_api_id',
				'updated_at',
				'last_sync',
			);
			$newFields = array_merge(array_intersect($acceptedFields, $fields), array());
			if (count($newFields) > 0)
				$options['fields'] = $newFields;
		}

		if ($allergen_contains_milk !== NULL && in_array( $allergen_contains_milk, array(true, false) ) )
			$options['allergen_contains_milk'] = $allergen_contains_milk ? 'true' : 'false';

		if ($allergen_contains_eggs !== NULL && in_array( $allergen_contains_eggs, array(true, false) ) )
			$options['allergen_contains_eggs'] = $allergen_contains_eggs ? 'true' : 'false';

		if ($allergen_contains_fish !== NULL && in_array( $allergen_contains_fish, array(true, false) ) )
			$options['allergen_contains_fish'] = $allergen_contains_fish ? 'true' : 'false';

		if ($allergen_contains_shellfish !== NULL && in_array( $allergen_contains_shellfish, array(true, false) ) )
			$options['allergen_contains_shellfish'] = $allergen_contains_shellfish ? 'true' : 'false';

		if ($allergen_contains_tree_nuts !== NULL && in_array( $allergen_contains_tree_nuts, array(true, false) ) )
			$options['allergen_contains_tree_nuts'] = $allergen_contains_tree_nuts ? 'true' : 'false';

		if ($allergen_contains_peanuts !== NULL && in_array( $allergen_contains_peanuts, array(true, false) ) )
			$options['allergen_contains_peanuts'] = $allergen_contains_peanuts ? 'true' : 'false';

		if ($allergen_contains_wheat !== NULL && in_array( $allergen_contains_wheat, array(true, false) ) )
			$options['allergen_contains_wheat'] = $allergen_contains_wheat ? 'true' : 'false';

		if ($allergen_contains_soybeans !== NULL && in_array( $allergen_contains_soybeans, array(true, false) ) )
			$options['allergen_contains_soybeans'] = $allergen_contains_soybeans ? 'true' : 'false';

		if ($allergen_contains_gluten !== NULL && in_array( $allergen_contains_gluten, array(true, false) ) )
			$options['allergen_contains_gluten'] = $allergen_contains_gluten ? 'true' : 'false';

		return $this -> makeQueryRequest('search', $item_name, $options, $returnJson);
	}


	/**
	 * This operation returns the an item object that contains data on all its nutritional content
	 * for more details on what values you can use, please go to https://developer.nutritionix.com/docs/v1_1
	 * demo http://dev2.nutritionix.com/html/nutritionix-api/html/demo-item.html
	 *
	 * @param string $searchTerm => The id or UPC of the item you want to retrieve
	 * @param string $searchType => so you can choose whether to search the database using item_id or upc
	 * @param bool $returnJson (Optional) => This will handle if the return value is array or json string
	 *                   The dev needs to make sure that the json result is returned with a json header,
	 *                   the api lib just returns the json string value
	 * @param bool $returnRequestUrl (Optional) => to tell the library to return the URL for this query or not (for debugging purposes)
	 *
	 * @return The results as a PHP array or json string depending on the $returnJson parameter
	 *
	 */
	public function getItem($searchTerm, $searchType, $returnJson = false, $returnRequestUrl = false){
		$options = array();
		$options[$searchType] = $searchTerm;
		return $this -> makeQueryRequest('item', null, $options, $returnJson, $returnRequestUrl);
	}


	/**
	 * This operation returns the a brand object
	 *
	 * @param string $id => The id of the brand you want to retrieve
	 * @param bool $returnJson (Optional) => This will handle if the return value is array or json string
	 *                   The dev needs to make sure that the json result is returned with a json header,
	 *                   the api lib just returns the json string value
	 * @param bool $returnRequestUrl (Optional) => to tell the library to return the URL for this query or not (for debugging purposes)
	 *
	 * @return The brand or json string depending on the returnJson value
	 *
	 */
	public function getBrand($id, $returnJson = false, $returnRequestUrl = false){
		return $this -> makeQueryRequest('brand', urlencode($id), array(), $returnJson, $returnRequestUrl);
	}


	/**
	 * The brand search opperation allows for a stadard or autocomplete search.
	 * for more details on what values you can use, please go to https://developer.nutritionix.com/docs/v1_1
	 * demo http://dev2.nutritionix.com/html/nutritionix-api/html/demo-item.html
	 *
	 * @param string $searchTerm => the phrase you want to search by
	 * @param bool $field_auto (Optional) => a boolean opperator that tells us to use an autocomplete style query for fast prefixing
	 * @param bool $field_type (Optional) => Unless defined, will include all brands. If defined: 1=search only restaurant
	 *                   brands (ie mcdonalds), 2=search only food manufacturer brands (ie kashi)
	 * @param float $min_score (Optional) => sometimes you may see too few results, this is because a results score might be
	 *                   lower than min_score. Adjust this to view more results.
	 * @param integer $offset (Optional) => the offset of paging through a result set. limit must be present with offset.
	 * @param integer $limit (Optional) => the limit of the results to be returned. Minimum of 1, max of 50. offset must be
	 *                   present with limit
	 * @param bool $returnJson (Optional) => This will handle if the return value is array or json string
	 *                   The dev needs to make sure that the json result is returned with a json header,
	 *                   the api lib just returns the json string value
	 * @param bool $returnRequestUrl (Optional) => to tell the library to return the URL for this query or not (for debugging purposes)
	 *
	 * @return The results as a PHP array or json string depending on the $returnJson parameter
	 *
	 */
	public function brandSearch($searchTerm, $field_auto, $field_type, $min_score, $offset, $limit, $returnJson = false, $returnRequestUrl = false){
		$options = array();
		if ($searchTerm != '' && $searchTerm != null)
			$options['query'] = $searchTerm;
		if ( $field_auto != '' && $field_auto != null && in_array( $field_auto, array(0, 1) ) )
			$options['auto'] = $field_auto;
		if ( $field_type != '' && $field_type != null && in_array( $field_type, array(1, 2) ) )
			$options['type'] = $field_type;
		if ($min_score != '' && $min_score != null && (float)$min_score > 0)
			$options['min_score'] = $min_score;

		$offset = (int)$offset;
		$limit = (int)$limit;
		if ($offset >= 0 && $limit > 0 && $limit <= 50){
			$options['offset'] = $offset;
			$options['limit'] = $limit;
		}

		return $this -> makeQueryRequest('brand/search', null, $options, $returnJson, $returnRequestUrl);
	}


	/**
	 * Performs a query request with the Nutritionix API Server
	 *
	 * @param string $method => Method of query. Current valid methods are: search, item, brand
	 * @param string $query => Query or search term / phrase
	 * @param array $params => Parameters associated with the query
	 * @param bool $returnJson (Optional) => to return a PHP array or a json string
	 * @param bool $returnRequestUrl (Optional) => to include the request URL for debugging purposes (only works on item and brand methods)
	 *
	 * @return The results as a PHP array or json string depending on the $returnJson parameter
	 */
	private function makeQueryRequest($method, $query, $params = array(), $returnJson = false, $returnRequestUrl = false){
		if ( in_array( $method, array('item', 'brand', 'brand/search') ) )
			$post_params = $this -> get_request_params($params);

		if ($method == 'brand')
			$request_url = $this -> api_url.$method.'/'.$query.'?'.$post_params;
		else if ($method == 'item' || $method == 'brand/search')
			$request_url = $this -> api_url.$method.'?'.$post_params;

		if ($method != 'search'){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Nutritionix API v1.1 PHP Client '.phpversion());
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			$result = json_decode(curl_exec($ch), true);
		}else{
			$data = array(
				'appId' => $this -> app_id,
				'appKey' => $this -> api_key,
			);

			$params['query'] = trim( str_replace(array('/', '"', '<', '~'), array('\/', '\"', '', ''), $params['query']) );
			$data_merged = array_merge($data, $params);
			$data_string = json_encode($data_merged);

			$ch = curl_init($this -> api_url_search_post);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: '.strlen($data_string)
			));

			$result = json_decode( curl_exec($ch) );

			if ( curl_errno($ch) )
				$result['api_error'] = curl_error($ch);
		}

		curl_close($ch);

		if ($method != 'search' && $returnRequestUrl)
			$result['request_url'] = $request_url;

		if ($returnJson)
			$result = json_encode($result);

		return $result;
	}


	/**
	 * Combine the parameter array with access credentials
	 *
	 * @param array params		Parameters associated with the query
	 *
	 * @return array					The request results array
	 */
	private function get_request_params($params){
		$params['appId'] = $this -> app_id;
		$params['appKey'] = $this -> api_key;

		foreach ($params as $key => &$value)
			$request_params[] = $key.'='.urlencode($value);
		return implode('&', $request_params);
	}


	/**
	 * Returned a (formatted, when possible) field of the item
	 *
	 * @param array item		The item
	 * @param field					The field to get
	 *
	 * @return mixed				The item field
	 */
	public function getItemField($item, $field){
		$data = $item[$field];
		if(strpos($field, 'nf_') !== false)
			return number_format($data);
		return $data;
	}
}


class NutritionixException extends Exception
{
	/**
	 * Array mapping error codes to messages
	 */
	public static $error_messages = array(
		'application_not_found' => 'Invalid App ID',
		'brand_not_found' => 'The Brand isn\'t on the Database',
	);


	/**
	 * Format error message
	 */
	public function __toString(){
		if ($this->message)
			return $this->message;
		else
			return 'Unknown Error';
	}
}
?>
