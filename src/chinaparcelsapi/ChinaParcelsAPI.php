<?php

class Result
{
    public int      $error;
    public array    $sourceData;
    function __construct(array $data)
    {
        var_dump($data);

        $this->sourceData   = $data;
        $this->error        = @$data['error'];
    }
}

class CarrierTrackingData
{
    public int $time;
    public string $info;
    public string | null $location;
    public string $date;

    public function __construct(array $data)
    {
        $this->time         = @$data['time'];
        $this->info         = @$data['info'];
        $this->location     = @$data['location'];
        $this->date         = @$data['date'];
    }
}

class ParcelOtherData
{
    public string | null $postalProduct;
    public array | null $referenceTrackingNumbers;
    public string | null $description;
    public string | null $recipient;
    public string | null $sender;
    public string | null $scheduledDelivery;
    public string | null $weight;
    public string | null $dimensions;

    public function __construct(array | null $data)
    {
        $this->postalProduct                = @$data['postal_product'];
        $this->referenceTrackingNumbers     = @$data['reference_tracking_numbers'];
        $this->description                  = @$data['description'];
        $this->recipient                    = @$data['recipient'];
        $this->sender                       = @$data['sender'];
        $this->scheduledDelivery            = @$data['scheduled_delivery'];
        $this->weight                       = @$data['weight'];
        $this->dimensions                   = @$data['dimensions'];
    }
}

class CarrierBasic
{
    public string | null $id;
    public string | null  $type;
    public string | null $countryCode;
    public string | null  $support;
    public string | null  $name;

    public function __construct(array | null $data)
    {
        ///var_dump($data);

        $this->id           = @$data['carrier_id'];
        $this->countryCode  = @$data['carrier_country_code'];
        $this->support      = @$data['carrier_support'];
        $this->type         = @$data['carrier_type'];
        $this->name         = @$data['carrier_name'];
    }
}
class Carrier extends CarrierBasic
{
    public string | null $website;
    public string | null $email;
    public string | null $phone;

    public string | null $iconBackgroundHex;

    public string | null $countryName;
    public string | null $icon;
    public int | null $iconWidth;
    public int | null $iconHeight;
    public string | null $thumbnail;
    public int | null $thumbnailWidth;
    public int | null $thumbnailHeight;

    public string | null $languageCode;

    public function __construct(array | null $data)
    {
        parent::__construct($data);

        $this->website              = @$data['carrier_website'];
        $this->email                = @$data['carrier_email'];
        $this->phone                = @$data['carrier_phone'];

        $this->iconBackgroundHex    = @$data['carrier_icon_background_hex'];

        $this->countryName          = @$data['carrier_country_name'];
        $this->icon                 = @$data['carrier_icon'];
        $this->iconWidth            = @$data['carrier_icon_width'];
        $this->iconHeight           = @$data['carrier_icon_height'];
        $this->thumbnail            = @$data['carrier_thumbnail'];
        $this->thumbnailWidth       = @$data['carrier_thumbnail_width'];
        $this->thumbnailHeight      = @$data['carrier_thumbnail_height'];

        $this->languageCode         = @$data['carrier_language_code'];
    }
}

class ParcelTrackingResult
{
    public array | null $carrierTrackingData;
    public ParcelOtherData | null $parcelOtherData;
    public string | null $parcelOriginCountryCode;
    public string | null $parcelDestinationCountryCode;
    public int | null $status;
    public Carrier | null $carrier;
    public int | null $estimateDeliveryDays;
    public string | null $estimateDeliveryDate;

    public function __construct(array | null $data)
    {

        if (@$data['carrier_tracking_data'])
        {
            $this->carrierTrackingData = array_map(
                fn($item) => new CarrierTrackingData($item),
                @$data['carrier_tracking_data']
            );
        }

        $this->parcelOtherData              = @$data['parcel_other_data'] ? new ParcelOtherData(@$data['parcel_other_data']) : null;
        $this->parcelOriginCountryCode      = @$data['parcel_origin_country_code'];
        $this->parcelDestinationCountryCode = @$data['parcel_destination_country_code'];
        $this->status                       = @$data['status'];
        $this->carrier                      = @$data['carrier'] ? new Carrier(@$data['carrier']) : null;
        $this->estimateDeliveryDays         = @$data['estimate_delivery_days'];
        $this->estimateDeliveryDate         = @$data['estimate_delivery_date'];
    }
}

class CarrierInfoResponse extends Result
{
    public Carrier | null $result;

    public function __construct(array | null $data)
    {
        parent::__construct($data);
        $this->result = @$data['result'] ? new Carrier(@$data['result']) : null;
    }
}

class AllCarriersResponse extends Result
{
    public array | null $result;

    public function __construct(array | null $data)
    {
        parent::__construct($data);
        $this->result = @$data['result'] ? array_map(fn($item) => new CarrierBasic($item), @$data['result']) : null;
    }
}
class ParcelsTrackingResponse extends Result
{
    public ParcelTrackingResult | null $result;

    public function __construct(array | null $data)
    {
        parent::__construct($data);
        $this->result = @$data['result'] ? new ParcelTrackingResult(@$data['result']) : null;
    }
}

class ParcelsLookupResponse extends Result
{
    public array | null $result;
    function __construct(array | null $data)
    {
        parent::__construct($data);
        $this->result = @$data['result'] ? @$data['result'] : null;
    }
}



class ChinaParcelsAPI
{

    public static $VERSION = "v1";
    private $_developer_security_key;

    private static $USER_API_SECRET_ACCESS_KEY_MASK    = "{USER_API_SECRET_ACCESS_KEY}";
    private static  $CARRIER_ID_MASK                    = "{CARRIER_ID}";
    private static  $TRACKING_NUMBER_MASK               = "{TRACKING_NUMBER}";

    private $_CARRIERS_GET_ALL      = "https://developers.chinaparcels.com/api/v1/user/{USER_API_SECRET_ACCESS_KEY}/carriers";
    private $_CARRIERS_GET_ONE      = "https://developers.chinaparcels.com/api/v1/user/{USER_API_SECRET_ACCESS_KEY}/carriers/{CARRIER_ID}";
    private $_PARCEL_LOOKUP         = "https://developers.chinaparcels.com/api/v1/user/{USER_API_SECRET_ACCESS_KEY}/parcels/{TRACKING_NUMBER}/lookup";
    private $_PARCEL_TRACK          = "https://developers.chinaparcels.com/api/v1/user/{USER_API_SECRET_ACCESS_KEY}/parcels/{TRACKING_NUMBER}/track/{CARRIER_ID}";
    private $_TIME_OUT = 180;

    function __construct($_developer_security_key)
    {
        $this->_developer_security_key = $_developer_security_key;
    }

    private function fixMask($_url, $_mask, $_value)
    {
        return @str_replace($_mask, $_value, $_url);
    }

    private function getRequest($_url)
    {
        $ch = curl_init($_url);
        //var_dump($_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    $this->_TIME_OUT);
        curl_setopt($ch, CURLOPT_TIMEOUT,           $this->_TIME_OUT);

        curl_exec($ch);
        @curl_close($ch);

        $result = @curl_exec($ch);
        //var_dump($result);
        //exit;
        return @json_decode($result, true);
    }

    public function getAllCarriers():AllCarriersResponse
    {
        $url    = $this->fixMask($this->_CARRIERS_GET_ALL, self::$USER_API_SECRET_ACCESS_KEY_MASK, $this->_developer_security_key);
        return new AllCarriersResponse($this->getRequest($url));
    }

    public function getCarrierInfo(string $_carrier_id):CarrierInfoResponse
    {
        $url    = $this->fixMask($this->_CARRIERS_GET_ONE, self::$USER_API_SECRET_ACCESS_KEY_MASK, $this->_developer_security_key);
        $url    = $this->fixMask($url, self::$CARRIER_ID_MASK, $_carrier_id);

        return new CarrierInfoResponse($this->getRequest($url));
    }

    public function lookupParcel(string $_tracking_number):ParcelsLookupResponse
    {
        $url    = $this->fixMask($this->_PARCEL_LOOKUP, self::$USER_API_SECRET_ACCESS_KEY_MASK, $this->_developer_security_key);
        $url    = $this->fixMask($url, self::$TRACKING_NUMBER_MASK, $_tracking_number);

        return new ParcelsLookupResponse($this->getRequest($url));
    }
    public function trackParcel(string $_tracking_number, string $_carrier_id = null):ParcelsTrackingResponse
    {
        $url    = $this->fixMask($this->_PARCEL_TRACK, self::$USER_API_SECRET_ACCESS_KEY_MASK, $this->_developer_security_key);
        $url    = $this->fixMask($url, self::$TRACKING_NUMBER_MASK, $_tracking_number);
        $url    = $this->fixMask($url, self::$CARRIER_ID_MASK, (($_carrier_id) ? : ""));

        return new ParcelsTrackingResponse($this->getRequest($url));
    }

}