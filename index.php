<?php

    error_reporting(E_ALL);
    ini_set('display_errors',         1);
    ini_set('display_startup_errors', 1);


    require_once('src/chinaparcelsapi/ChinaParcelsAPI.php');

    $key = "KEY";
    $api = new ChinaParcelsAPI($key);

    //GET ALL CARRIERS
    $response = $api->getAllCarriers();
    var_dump($response);

    //GET CARRIER INFO
    //$carrier_id = 2; //USPS: 2, Canada Post: 3
    //$response = $api->getCarrierInfo($carrier_id);
    //var_dump($response);


    //LOOKUP CARRIERS BY TRACKING NUMBER
    //$tracking_number = "LX652609218US";
    //$response        = $api->lookupParcel($tracking_number);
    //var_dump($response);

    //LOOKUP CARRIERS BY TRACKING NUMBER
    //$tracking_number = "LX652609218US";
    //$response        = $api->trackParcel($tracking_number);
    //var_dump($response);