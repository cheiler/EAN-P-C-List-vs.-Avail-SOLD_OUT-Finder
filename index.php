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

//TODO: LIST Request (partial complete)

//Create List Request URL:
    $url="http://api.ean.com/ean-services/rs/hotel/v3/list?";
    $url .= "cid=".getValue('cid'); 
    $url .= "&apiKey=".getValue('apikey');
    $url .= "&sig=".generateSig;
    $url .= "&minorRev=26&type=xml";
    $url .= "&locale=".getValue('locale'); 
    $url .= "&currencyCode=".getValue('currency');

//Create LIST Request XML:
    
    $xml = "<HotelListRequest>";
    $xml .= "<destinationId>".getValue("destination")."</destinationId>";
    $xml .= "<arrivalDate>09/01/2015</arrivalDate>";
    $xml .= "<departureDate>09/03/2015</departureDate>";
    $xml .= "<RoomGroup>";
    $xml .= "<Room><numberOfAdults>2</numberOfAdults></Room></RoomGroup>";
    $xml .= "<numberOfResults>1</numberOfResults>";
    $xml .= "</HotelListRequest>";

    
    
//getList Results

//TODO: Consecutive AVAIL requests for each Hotel and Room

//TODO: Calculate Results and Display in Table


?>   
</body>
</html>
