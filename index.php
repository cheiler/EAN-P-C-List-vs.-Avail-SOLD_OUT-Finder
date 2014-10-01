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
fwrite ($file, "LOGFILE LIST vs. AVAIL \r\nTime: ".date("Y-m-d-H-i-s")."\r\n");

//TODO: LIST Request (partial complete)

//Create List Request URL:
    $url="http://api.ean.com/ean-services/rs/hotel/v3/list?";
    $url .= "cid=".getValue('cid'); 
    $url .= "&apiKey=".getValue('apikey');
    $url .= "&sig=".generateSig(getValue('apikey'), getValue('shared'));
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
    $xml .= "<numberOfResults>10</numberOfResults>";
    $xml .= "<maxRatePlanCount>2</maxRatePlanCount>";
    $xml .= "<includeDetails>true</includeDetails>";
    $xml .= "</HotelListRequest>";


    
//getList Results
    
    $listResponse = apiWrapper($url, $xml);
    
    fwrite($file, "LIST request:\r\n");
    fwrite($file, ($url)."\r\n");
    fwrite($file, ($xml)."\r\n");
    
    fwrite($file, "LIST response:\r\n");
    fwrite($file, print_r($listResponse, true)."\r\n");
    
//parse List results into Array
    
    $hotelElement = $listResponse->HotelList;
    //get hotel List Array
    $hotelList = $hotelElement->HotelSummary;
    
    $listResult = array();
    $i=0;
    foreach($hotelList as $hotel){
        $hotelId = $hotel->hotelId;
        $listResult[$i]['hotelId']=$hotelId;
        
        $roomList = $hotel->RoomRateDetailsList->RoomRateDetails;
        $j=0;
        foreach($roomList as $room){
            $listResult[$i]['rooms'][$j]['rateCode'] = $room->rateCode;
            $listResult[$i]['rooms'][$j]['roomTypeCode'] = $room->roomTypeCode;
            $listResult[$i]['rooms'][$j]['price'] = $room->RateInfos->RateInfo->ChargeableRateInfo['total'];
            $j++;
        }
        $i++;
    }
    
    fwrite($file, print_r($listResult, true)."\r\n");
    
    
//TODO: Consecutive AVAIL requests for each Hotel and Room

//Create AVAIL Request URL:
    $url="http://api.ean.com/ean-services/rs/hotel/v3/avail?";
    $url .= "cid=".getValue('cid'); 
    $url .= "&apiKey=".getValue('apikey');
    $url .= "&sig=".generateSig(getValue('apikey'), getValue('shared'));
    $url .= "&minorRev=26&type=xml";
    $url .= "&locale=".getValue('locale'); 
    $url .= "&currencyCode=".getValue('currency');    
    
    $xml = "<HotelRoomAvailabilityRequest>";
    $xml .="<hotelId>[+hotelid+]</hotelId>";
    $xml .= "<arrivalDate>09/01/2015</arrivalDate>";
    $xml .= "<departureDate>09/03/2015</departureDate>";
    $xml .= "<RoomGroup>";
    $xml .= "<Room><numberOfAdults>2</numberOfAdults></Room></RoomGroup>";
    $xml .= "<numberOfResults>10</numberOfResults>";
    $xml .= "<maxRatePlanCount>2</maxRatePlanCount>";
    $xml .= "</HotelRoomAvailabilityRequest>";
    

//Calls per Hotel ID in list response element:

foreach($listResult as $entry){
    fwrite($file, "Avail Request for Hotel ID: ".$entry['hotelId']."\r\n");
    $xml = str_replace("[+hotelid+]", $entry['hotelId'], $xml);
    fwrite($file, ($url)."\r\n");
    fwrite($file, ($xml)."\r\n");
    
    $availResponse = apiWrapper($url, $xml);
    
    fwrite($file, "AVAIL response for Hotel ID: ".$entry['hotelId']."\r\n");
    fwrite($file, print_r($availResponse, true)."\r\n");
    
}
    
    
    
    
//TODO: Calculate Results and Display in Table


?>   
</body>
</html>
