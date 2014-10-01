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

?>
