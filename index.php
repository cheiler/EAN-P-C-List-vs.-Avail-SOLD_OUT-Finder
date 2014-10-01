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

//getList Results

//TODO: Consecutive AVAIL requests for each Hotel and Room

//TODO: Calculate Results and Display in Table


?>   
</body>
</html>
