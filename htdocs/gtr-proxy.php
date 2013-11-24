<?php

$org_id = $_GET['id'];

$url = "http://gtr.rcuk.ac.uk/gtr/api/organisations/$org_id/projects?s=10";

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Accept: application/json')); 
$response = curl_exec($ch); 
curl_close($ch);   

#print $response;
#print "\n$url\n";
