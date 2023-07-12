<?php
//set content type to json
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: * ');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Credentials: true');

//var
$heurigenApi = array();

//get data from "api"
$apiS = file_get_contents('https://www.perchtoldsdorf.com/inhaltsverz.htm');

//remove everithing after </menu>
$apiS = substr($apiS, 0, strpos($apiS, "</body>"));

//remove not important code
$apiS = html_entity_decode($apiS);
$apiS = strstr($apiS, '<body>');
$apiS = substr($apiS, 6);
$apiS = str_replace(array("\n"), '', $apiS);
$apiS = str_replace("\r", ' ', $apiS);
#$apiS = str_replace("  ", "", $apiS);

//array
$heurigerArray = explode("</h5>", $apiS);
//$heurigerArray = array_filter($heurigerArray);

foreach ($heurigerArray as $heurigen) {
    $tmp_id;
    $tmp_name;
    $tmp_address;
    $tmp_nameId;

    $tmp_id = get_string_between($heurigen, 'php/', '.php');
    $tmp_name = get_string_between2($heurigen, 'target="Hauptframe">', '<br>');
    $tmp_address = get_string_between($heurigen, '<font size="1">', '</font>');

    $tmp_nameId = strtolower($tmp_name);
    $tmp_nameId = str_replace(' ', '-', $tmp_nameId);
    $tmp_nameId = str_replace("&", "-a-", $tmp_nameId);
    $tmp_nameId = str_replace('"', "", $tmp_nameId);
    $tmp_nameId = str_replace(".", "", $tmp_nameId);
    $tmp_nameId = str_replace("ö", "oe", $tmp_nameId);
    $tmp_nameId = str_replace("ä", "ae", $tmp_nameId);
    $tmp_nameId = str_replace("ü", "ue", $tmp_nameId);
    $tmp_nameId = str_replace("ß", "ss", $tmp_nameId);
    $tmp_nameId = str_replace("Ö", "oe", $tmp_nameId);
    $tmp_nameId = str_replace("Ä", "ae", $tmp_nameId);
    $tmp_nameId = str_replace("Ü", "ue", $tmp_nameId);

    array_push($heurigenApi, array(
        "id" => $tmp_id,
        "nameId" => $tmp_nameId,
        "name" => $tmp_name,
        "address" => $tmp_address
    ));
}
echo '[';
for ($i=0; $i < count($heurigenApi); $i++) {
    if($i !== 0 && !json_encode($heurigenApi[$i]) == "") {
        echo ',';
    }
    echo json_encode($heurigenApi[$i]);
}
echo ']';

//functions
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function get_string_between2($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    $len = str_replace("-", "", $len);
    return substr($string, $ini, $len);
}
?>