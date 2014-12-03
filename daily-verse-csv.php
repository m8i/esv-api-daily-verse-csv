<?php
error_reporting(0);

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=verse.csv");
header("Pragma: no-cache");
header("Expires: 0");

// Header Row
echo '"verse","reference"'."\n";

$file=do_post_request('http://www.esvapi.org/v2/rest/dailyVerse?key=IP&output-format=crossway-xml-1.0');
$file=preg_replace("/\&emdash\;/i", ' - ', $file);
$file=preg_replace("/\&ldblquot\;/i", '', $file);
$xml=simplexml_load_string($file);

$reference=$xml->passage->reference;
$verse="";
foreach ($xml->passage->content->{'verse-unit'} as $verse_part) {
	$the_verse_part=(string)$verse_part;
	if (strlen($the_verse_part)<3) { $the_verse_part = (string)$verse_part->woc; }
		$verse .= $the_verse_part . " ";
}

// Sometimes HTML tags slip on in there... let's get rid of them
$verse = preg_replace ('/<[^>]*>/', ' ', $verse);

// Trim off the extra spaces and line breaks
$verse = trim(str_replace('"', "'", $verse));

// Echo the verse and reference line of the CSV
echo '"'.$verse.'","'.$reference.' (ESV)"'."\n";



/* CURL HTTP Post function */
function do_post_request( $url){
	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url,
	    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	  return $resp;
}

?>