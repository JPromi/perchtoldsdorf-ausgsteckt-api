<?php
//set content type to json
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: * ');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Credentials: true');

//var
$heurigerApi = array();

//date
if(isset($_REQUEST["date"])) {
    $dateURL = date("m/d/Y", strtotime($_REQUEST["date"]));
} else {
    $dateURL = date("m/d/Y");
}

//get data from "api"
$apiS = file_get_contents('https://www.pdorf.at/Cal-neu/abfrage.php?Date='.$dateURL);

//remove everithing after </menu>
$apiS = substr($apiS, 0, strpos($apiS, "</menu>"));

//remove not important code
$apiS = strstr($apiS, '<menu>');
$apiS = substr($apiS, 6);
$apiS = str_replace("<li>", "", $apiS);
$apiS = str_replace("  ", "", $apiS);

//to array
$heurigerArray = explode("</li>", $apiS);
$heurigerArray = array_filter($heurigerArray);
$counter = 0;

foreach ($heurigerArray as $heuriger) {
    $tmp_heuriger = array();

    $tmp_link; #
    $tmp_name; #
    $tmp_address; #
    $tmp_phone; #
    $tmp_playground;
    $tmp_wheelchair;

    $tmp_heuriger = explode(",", $heuriger);

    //get data
    $tmp_link = get_string_between($tmp_heuriger["0"], 'href="', '"');
    $tmp_name = get_string_between($tmp_heuriger["0"], '>', '<');
    $tmp_phone = get_string_between($tmp_heuriger[search_in_array($tmp_heuriger, 'href="tel:')], 'href="tel:', '"');
    $tmp_address = substr($tmp_heuriger["1"], 1);
    $tmp_playground = search_in_array_boolean($tmp_heuriger, 'splplz.gif');
    $tmp_wheelchair = search_in_array_boolean($tmp_heuriger, 'rolli.gif');

    array_push($heurigerApi, array(
        "name" => $tmp_name,
        "link" => $tmp_link,
        "phone" => $tmp_phone,
        "address" => $tmp_address,
        "has_playground" => $tmp_playground,
        "wheelchair_accessible" => $tmp_wheelchair
    ));

}

echo json_encode($heurigerApi);

//functions
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function search_in_array($array, $search) {
    $i = 0;
    foreach ($array as $key) {
        if(str_contains($key, $search)) {
            return $i;
            exit();
        }
        $i++;
    }
    return NULL;
}

function search_in_array_boolean($array, $search) {
    foreach ($array as $key) {
        if(str_contains($key, $search)) {
            return true;
            exit();
        }
    }
    return false;
}
?>