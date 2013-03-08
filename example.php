<?php
//
// +---------------------------------------------------------------------------+
// | Nutritionix PHP API Library	     		                               |
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

require_once 'config.inc.php';

$nutritionix = new Nutritionix(NUTRITIONIX_APP_ID, NUTRITIONIX_APP_KEY);

try {
	
	print "<h1>Nutritionix PHP API v1 Examples</h1>";
	print "<h2>Search Example</h2>";
	print "Searching for: <b>tacos</b>" . "<br><br>";
	
	$search_result = $nutritionix->search('tacos', 0, 5, 0, NULL, '*');
	
	print "Total hits: " . $search_result['total_hits'] . "<br>";
	print "Max Score: " . $search_result['max_score'] . "<br>";
	print "Hits Returned: " . sizeof($search_result['hits']). "<br><br>";
	
	foreach($search_result['hits'] as $each_result)
	{
		print"=======Item Details======<br>";
		
		print "Index: " . $each_result['_index'];
		print"<br>";
		print "Type: " . $each_result['_type'];
		print"<br>";
		print "ID: " . $each_result['_id'];
		print"<br>";
		print "Score: " . $each_result['_score'];
		print"<br>";
		
		$item = $each_result['_source'];
		print "Item Name: " . $nutritionix->getItemField($item, 'item_name') . "<br>";
		print "Brand: " . $nutritionix->getItemField($item, 'brand_name') . "<br>";
		print "Item Description: " . $nutritionix->getItemField($item, 'item_description') . "<br>";
		print "Calories: " . $nutritionix->getItemField($item, 'nf_calories') . "<br>";
		print "Calories From fat: " . $nutritionix->getItemField($item, 'nf_calories_from_fat') . "<br>";
		print "Total fat: " . $nutritionix->getItemField($item, 'nf_total_fat') . "<br>";
	}
	
	
	
	print"<br><br><br>";
	
	print "<h2>Item Example</h2>";
	print "Getting data for item ID: <b>eajmz6GbcLXtMluFNhEr</b>" . "<br><br>";
	
	$item = $nutritionix->getItem('eajmz6GbcLXtMluFNhEr');
	
	print "Item Name: " . $nutritionix->getItemField($item, 'item_name') . "<br>";
	print "Brand: " . $nutritionix->getItemField($item, 'brand_name') . "<br>";
	print "Item Description: " . $nutritionix->getItemField($item, 'item_description') . "<br>";
	print "Calories: " . $nutritionix->getItemField($item, 'nf_calories') . "<br>";
	print "Calories From fat: " . $nutritionix->getItemField($item, 'nf_calories_from_fat') . "<br>";
	print "Total fat: " . $nutritionix->getItemField($item, 'nf_total_fat') . "<br>";
	
	
	
	print"<br><br><br>";
	
	print "<h2>Brand Example</h2>";
	print "Getting data for brand ID: <b>VSQksuzwib4H1h9</b>" . "<br><br>";
	
	$item = $nutritionix->getBrand('VSQksuzwib4H1h9');
	
	print "Brand Name: " . $nutritionix->getItemField($item, 'name') . "<br>";
	print "Website: " . $nutritionix->getItemField($item, 'website') . "<br>";
	
} catch (Exception $e) {
	die('Nutritionix API Error: '.$e);	
}
?>