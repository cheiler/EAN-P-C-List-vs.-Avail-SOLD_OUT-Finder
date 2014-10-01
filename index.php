<?php
/**
 * EAN P:C List vs. Avail SOLD_OUT Finder
 * @author: Christian Heiler
 * @version: 0.1
 * 
 * 
*/

//HTML Header
require_once("includes/functions.php");
include_once("assets/header.xhtml");

?>

<body>
<?php
include_once("assets/form.xhtml");

echo "For IP Access, enable IP: ".$_SERVER['SERVER_ADDR'];

//Open Logfile.
$filename = getFilename();

$file = fopen("logs/".$filename, "w");
fwrite ($file, "LOGFILE LIST vs. AVAIL \r\nTime: ".date("Y-m-d-H-i-s"));

//TODO: LIST Request

//Create List Request:
    $url="http://api.ean.com/ean-services/rs/hotel/v3/list?";
    $url .= "cid=".getValue('cid'); 
    $url .= "&apiKey=".getValue('apikey'); 
    $url .= "&minorRev=26&type=xml&locale=en_US&currencyCode=EUR";


//getList Results

//TODO: Consecutive AVAIL requests for each Hotel and Room

//TODO: Calculate Results and Display in Table


?>   
</body>
</html>
