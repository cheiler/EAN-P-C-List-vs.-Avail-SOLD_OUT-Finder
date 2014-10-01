<?php

/*
 * EAN P:C List vs. Avail FUNCTIONS
 * @author: Christian Heiler
 */


/* function getValue($param)
 * @param: $param: string
 * @returns: GET parameter 'param'
 */
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
    if($temp===""){
        $temp = "log";
    }
    if((strlen($temp)<5) || (strtolower(substr($temp, -4)) = ".txt")){
        $temp += ".txt";
    }
    return $temp;
}

?>
