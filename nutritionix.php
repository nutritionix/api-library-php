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

if (!function_exists('curl_init')) {
	throw new Exception('CURL is required to run the Nutritionix PHP API.');
}
if (!function_exists('json_decode')) {
	throw new Exception('JSON Extension is required to run the Nutritionix PHP API.');
}


class Nutritionix
{
	private $app_id;
	private $apikey;
	private $api_url = "http://api.nutritionix.com/v1/";


	/**
	 * Create the Nutritionix API client.
	 *
	 * @param string app_id		Nutritionix application ID
	 * @param string api_key	Nutritionix API key
	 */
	public function __construct ($app_id, $api_key)
	{
		$this->app_id	= $app_id;
		$this->api_key	= $api_key;
	}

	/**
	 * Pass a search term into the API like taco, or cheese fries, and the API will return an array of matching foods.
	 *
	 * @param string term The phrase or terms you would like to search by
	 * @param int rangeStart (Optional)Start of the range of results to view a section of up to 500 items in the "hits" array
	 * @param int rangeEnd (Optional)End of the range of results to view a section of up to 500 items in the "hits" array
	 *  by default, the api will fetch the first 10 results
	 * @param int cal_min (Optional)The minimum number of calories you want to be in an item returned in the results
	 * @param int cal_max (Optional)The maximum number of calories you want to be in an item returned in the results
	 * @param string fields	(Optional)The fields from an item you would like to return in the results.
	 * Supports all item properties in comma delimited format.
	 * A null parameter will return the following item fields only: item_name, brand_name, item_id.
	 * NOTE-- passing "*" as a value will return all item fields.
	 * @param string brandID (Optional)Filter your results by a specific brand by passing in a brand_id
	 * @param bool returnJson (Optional)This will handle if the return value is array or json string
	 * The dev needs to make sure that the json result is returned with a json header,
	 *  the api lib just returns the json string value
	 *
	 * @return The search results array or json string depending on the return Json value
	 */
	public function search($term, $rangeStart = 0, $rangeEnd = 10, $cal_min = NULL, $cal_max = NULL, $fields = NULL, $brandID = NULL, $returnJson = false)
	{
		return $this->makeQueryRequest('search', urlencode($term),
			array(
				  'results' => $rangeStart.':'.$rangeEnd,
				  'cal_min' => $cal_min,
				  'cal_max' => $cal_max,
				  'fields' => $fields,
				  'brand_id' => $brandID,
			), $returnJson);
	}

	/**
	 * This operation returns the an item object that contains data on all its nutritional content
	 *
	 * @param string id The id of the item you want to retrieve
	 * @param bool returnJson (Optional)This will handle if the return value is array or json string
	 * The dev needs to make sure that the json result is returned with a json header,
	 * the api lib just returns the json string value
	 * @return The item array or json string depending on the returnJson value
	 *
	 */
	public function getItem($id, $returnJson = false)
	{
		return $this->makeQueryRequest('item', urlencode($id), array(), $returnJson);
	}


	/**
	 * This operation returns the a brand object that contains data on all its nutritional content
	 *
	 * @param string id The id of the brand you want to retrieve
	 *
	 * @param bool returnJson (Optional)This will handle if the return value is array or json string
	 * The dev needs to make sure that the json result is returned with a json header,
	 * the api lib just returns the json string value
	 *
	 * @return The brand or json string depending on the returnJson value
	 */
	public function getBrand($id, $returnJson = false)
	{
		return $this->makeQueryRequest('brand', urlencode($id), array(), $returnJson);
	}


	/**
	 * Performs a query request with the Nutritionix API Server
	 *
	 * @param string method	Method of query. Current valid methods are: search, item, brand
	 * @param string query Query or search term / phrase
	 * @param array params Parameters associated with the query
	 * @param bool returnJson (Optional)This will handle if the return value is array or json string
	 *															The dev needs to make sure that the json result is returned with a json header,
	 *															the api lib just returns the json string value
	 *
	 * @return 										The request result or json string depending on the returnJson value
	 *
	 * @error
	 *	application_not_found
	 */
	private function makeQueryRequest($method, $query, $params = array(), $returnJson = false)
	{
		$post_params = $this->get_request_params($params);
		$request_url = $this->api_url . $method . '/' . $query . '?' . $post_params;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Nutritionix API v1 PHP Client ' . phpversion());
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		if (!$returnJson){
			$result = json_decode(curl_exec($ch), true);
			if (is_array($result) && (isset($result['error_code']) || isset($result['error_message']))){
				$error_messsage = NutritionixException::$error_messages[$result['error_code']];
				throw new NutritionixException($error_messsage, 1);
			}
		}else
			$result = curl_exec($ch);

		curl_close($ch);
		return $result;
	}


	/**
	 * Combine the parameter array with access credentials
	 *
	 * @param array params		Parameters associated with the query
	 *
	 * @return array					The request results array
	 */
	private function get_request_params($params)
	{
		$params['appId'] = $this->app_id;
		$params['appKey'] = $this->api_key;

		foreach ($params as $key => &$value) {
			$request_params[] = $key.'='.urlencode($value);
		}
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
	public function getItemField($item, $field)
	{
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
		'application_not_found' => 'Invalid App ID'
	);


	/**
	 * Format error message
	 */
	public function __toString()
    {
		if($this->message)
	        return $this->message;
		else
			return 'Unknown Error';
    }
}
?>
