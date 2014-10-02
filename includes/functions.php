<?php

/*
 * EAN P:C List vs. Avail FUNCTIONS
 * @author: Christian Heiler
 */


/* function getValue($param)
 * @param: $param: string
 * @returns: GET parameter 'param'
 */
date_default_timezone_set("GMT");

function getValue($param){
    $back = "";
    if(isset($_GET[$param])){
        $back=$_GET[$param];
    }  
    return $back;
}


function show($param){
    $value = getValue($param);
    echo $value;
}

function getFilename(){
    $temp = getValue('log');
    if($temp==""){
        $temp = "log";
    }
    if((strlen($temp)<5) || (strtolower(substr($temp, -4)) != ".txt")){
        $temp = $temp.".txt";
    }
    return $temp;
}



function apiWrapper($url, $xml, $method="GET"){
    
    $header[] = "Accept: application/xml";
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    //curl_setopt($ch,CURLOPT_POST,5);
    //curl_setopt($ch,CURLOPT_POSTFIELDS,$XML);
    curl_setopt( $ch, CURLOPT_URL, $url."&xml=".$xml );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $response = curl_exec($ch);
    
    $response = simplexml_load_string($response);   
    
    return $response;
}


function generateSig($apiKey, $secret){
    $timestamp = gmdate('U'); // 1200603038  (Thu, 17 Jan 2008 20:50:38 +0000)
    //echo "<br>Timestamp: ".$timestamp;
    //echo "<br>API Key: ".$apiKey;
    //echo "<br>Secret: ".$secret;
    $sig = md5($apiKey . $secret . $timestamp);
    return $sig;
}


function screenlog($variable){
    echo "\r\n<br /><pre>";
    echo print_r($variable, true);
    echo "\r\n<br /></pre>\r\n<br />";
    
}


function findHotelId($id, $list){
    $back = "SOLD_OUT";
    foreach($list as $item){
        if(intval($item['hotelId']) == intval($id)){
            $back = $item['hotelId'];
        }
    }
    return $back;
}


?>
