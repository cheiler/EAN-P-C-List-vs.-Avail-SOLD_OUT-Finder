<?php
/**
 * EAN P:C List vs. Avail SOLD_OUT Finder
 * @author: Christian Heiler
 * @version: 0.6
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
    if (getValue("destination") !=""){
        $xml .= "<destinationId>".getValue("destination")."</destinationId>";
    } elseif(getValue("hotelids") !=""){
        
        $xml .= "<hotelIdList>".getValue("hotelids")."</hotelIdList>";
    }
    
    
    $xml .= "<arrivalDate>".getValue("arrival")."</arrivalDate>";
    $xml .= "<departureDate>".getValue("departure")."</departureDate>";
    $xml .= "<RoomGroup>";
    $xml .= "<Room><numberOfAdults>2</numberOfAdults></Room></RoomGroup>";
    $xml .= "<numberOfResults>".getValue("results")."</numberOfResults>";
    $xml .= "<maxRatePlanCount>".getValue("max")."</maxRatePlanCount>";
    //$xml .= "<includeDetails>true</includeDetails>";
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
    $xml .= "<arrivalDate>".getValue("arrival")."</arrivalDate>";
    $xml .= "<departureDate>".getValue("departure")."</departureDate>";
    //$xml .= "<arrivalDate>10/06/2014</arrivalDate>";
    //$xml .= "<departureDate>10/07/2014</departureDate>";
    $xml .= "<RoomGroup>";
    $xml .= "<Room><numberOfAdults>2</numberOfAdults></Room></RoomGroup>";
    $xml .= "</HotelRoomAvailabilityRequest>";
    

//Calls per Hotel ID in list response element:
$i=0;
foreach($listResult as $entry){
    fwrite($file, "Avail Request for Hotel ID: ".$entry['hotelId']."\r\n");
    $xml2 = str_replace("[+hotelid+]", $entry['hotelId'], $xml);
    fwrite($file, ($url)."\r\n");
    fwrite($file, ($xml2)."\r\n");
    
    $availResponse = apiWrapper($url, $xml2);
    
    fwrite($file, "AVAIL response for Hotel ID: ".$entry['hotelId']."\r\n");
    fwrite($file, print_r($availResponse, true)."\r\n");
    $hotelId = $availResponse->hotelId;
    $availResult[$i]['hotelId']=$hotelId;
    
    $j=0;
    $rateArray = $availResponse->HotelRoomResponse;
    
    //screenlog($rateArray);
    
    foreach($rateArray as $rate){
        //screenlog($rate);
        $availResult[$i]['rooms'][$j]['rateCode'] = $rate->rateCode;
        $availResult[$i]['rooms'][$j]['roomTypeCode'] = $rate->roomTypeCode;
        $availResult[$i]['rooms'][$j]['price'] = $rate->RateInfos->RateInfo->ChargeableRateInfo['total'];
        $j++;
    }
    $i++;
    
}
    fwrite($file, print_r($availResult, true)."\r\n");
    //screenlog($availResult);
    
    
//Calculate Results and Display in Table

$space = "";
//screenlog($listResult);
    echo "<table border='1'>";
    echo "<tr><td>Hotel ID</td><td>roomTypeCode</td><td>rateKey</td><td>Price</td><td>||</td><td>Hotel ID</td><td>roomTypeCode</td><td>rateKey</td><td>Price</td><td>PriceChange</td></tr>";
foreach($listResult as $list){
    
    foreach($list['rooms'] as $room){
       echo "<tr>";
       echo "<td>".$list['hotelId']."</td>";
       echo "<td>".$room['roomTypeCode']."</td>"; 
       echo "<td>".$room['rateCode']."</td>";
       echo "<td>".$room['price']."</td>";
       echo "<td>||</td>";
       echo "<td>".findHotelId($list['hotelId'], $availResult)."</td>";
       echo "<td>".findRoomTypeCode($room['roomTypeCode'], $list['hotelId'], $availResult)."</td>";
       echo "<td>".findRateCode($room['rateCode'], $list['hotelId'], $room['roomTypeCode'], $availResult)."</td>"; 
       $availPrice=findPrice($room['rateCode'], $list['hotelId'], $room['roomTypeCode'],$room['rateCode'], $availResult);
       echo "<td>".$availPrice."</td>";
       echo "<td>".getDifference($room['price'], $availPrice)."</td>";
       
       
       echo "</tr>\r\n"; 
    }
    
}
echo "</table>";
    
    
    
    
    
    
?>   
</body>
</html>
