<?php

/**
 * BPOINT API
 *
 * @category    BPOINT
 * @package     BPOINT_API
 * @copyright   Copyright (c) 2015 Premier Technologies Pty Ltd. (http://www.premier.com.au)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class BPOINT_API {

    /**
     * public and public variables
     *
     * @var string stores data for the class
     */
    public $_path;
    public $_headers;
    public $_proxyFlag;
    public $_proxyHost;
    public $_proxyPort;
    public $_action;
    public $_amount;
    public $_currency;
    public $_merchantReference;
    public $_crn1;
    public $_crn2;
    public $_crn3;
    public $_billerCode;
    public $_redirectionUrl;
    public $_webHookUrl;
    public $_storeCard;
    public $_subType;
    public $_type;
    public $_originalTxnNumber;
    public $_cardDetails;
    public $_testMode;
    public $_userAgent;
    public $_EmailAddress;

    /**
     * Sets $_path, $_headers, $_proxy upon class instantiation
     * 
     * @param $username, $password, $apiUrl, $token required for the class
     * @return void
     */
    public function __construct($username, $password, $merchantNumber, $apiUrl, $proxyHost = "", $proxyPort = "") {
        $this->_path = $apiUrl;
        $encodedToken = base64_encode($username . "|" . $merchantNumber . ":" . $password);
        $authHeaderString = 'Authorization: ' . $encodedToken;
        $this->_headers = array($authHeaderString, 'Content-Type: application/json; charset=utf-8');
        if (($proxyHost == "") || ($proxyPort == "")) {
            $this->_proxyFlag = false;
        } else {
            $this->_proxyFlag = true;
            $this->_proxyHost = $proxyHost;
            $this->_proxyPort = $proxyPort;
        }
        //initiate values
        $this->_storeCard = false;
    }

    public function setAction($action) {
        $this->_action = $action;
        return $this;
    }

    public function setAmount($amount) {
        $this->_amount = $amount;
        return $this;
    }

    public function setMerchantReference($merchantReference) {
        $this->_merchantReference = $merchantReference;
        return $this;
    }

    public function setCurrency($currency) {
        $this->_currency = $currency;
        return $this;
    }

    public function setCrn1($crn1) {
        $this->_crn1 = $crn1;
        return $this;
    }

    public function setCrn2($crn2) {
        $this->_crn2 = $crn2;
        return $this;
    }

    public function setCrn3($crn3) {
        $this->_crn3 = $crn3;
        return $this;
    }

    public function setBillerCode($billerCode) {
        $this->_billerCode = $billerCode;
        return $this;
    }

    public function setRedirectionUrl($redirectionUrl) {
        $this->_redirectionUrl = $redirectionUrl;
        return $this;
    }

    public function setWebHookUrl($webHookUrl) {
        $this->_webHookUrl = $webHookUrl;
        return $this;
    }

    public function setStoreCard($storeCard) {
        $this->_storeCard = $storeCard;
        return $this;
    }

    public function setSubType($subType) {
        $this->_subType = $subType;
        return $this;
    }

    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    public function setEmailAddress($EmailAddress) {
        $this->_EmailAddress = $EmailAddress;
        return $this;
    }

    public function setcardDetails($cardNumber, $cVN, $expiryDate = "", $cardHolderName = "") {
        $cardDetails = new stdClass();
        $cardDetails->CardNumber = $cardNumber;
        $cardDetails->CVN = $cVN;
        if ($expiryDate) {
            $cardDetails->ExpiryDate = $expiryDate;
        }
        if ($cardHolderName) {
            $cardDetails->CardHolderName = $cardHolderName;
        }
        $this->_cardDetails = $cardDetails;
        return $this;
    }
    
    public function setTestMode($testMode) {
        $this->_testMode = $testMode;
        return $this;
    }

    public function setOriginalTxnNumber($originalTxnNumber) {
        $this->_originalTxnNumber = $originalTxnNumber;
        return $this;
    }
    
    public function setUserAgent($userAgent) {
        $this->_userAgent = $userAgent;
        return $this;
    }
    
    public function createAuthkey() {
        $fields = array(
            "ProcessTxnData" => array(
                "Action" => $this->_action,
                "Amount" => $this->_amount,
                "Currency" => $this->_currency,
                "MerchantReference" => $this->_merchantReference,
                "Crn1" => $this->_crn1,
                "Crn2" => $this->_crn2,
                "Crn3" => $this->_crn3,
                "BillerCode" => $this->_billerCode,
                "TestMode" => $this->_testMode
            ),
            "RedirectionUrl" => $this->_redirectionUrl,
            "WebHookUrl" => $this->_webHookUrl
        );
        $result = $this->post("/txns/processtxnauthkey", $fields);
        return $result;
    }

    public function processTransaction() {
        $fields = array(
            "TxnReq" => array(
                "Action" => $this->_action,
                "Amount" => $this->_amount,
                "Currency" => $this->_currency,
                "MerchantReference" => $this->_merchantReference,
                "OriginalTxnNumber" => $this->_originalTxnNumber,
                "Crn1" => $this->_crn1,
                "Crn2" => $this->_crn2,
                "Crn3" => $this->_crn3,
                "BillerCode" => $this->_billerCode,
                "CardDetails" => $this->_cardDetails,
                "StoreCard" => $this->_storeCard,
                "SubType" => $this->_subType,
                "Type" => $this->_type,
                "TestMode" => $this->_testMode
            )
        );
        $result = $this->post("/txns/", $fields);
        return $result;
    }
    
    public function processDVToken() {
        $fields = array(
            "DVTokenReq" => array(
                "CardDetails" => $this->_cardDetails,
                "Crn1" => $this->_crn1,
                "Crn2" => $this->_crn2,
                "Crn3" => $this->_crn3,
                "EmailAddress" => $this->_EmailAddress
            )
        );
        $result = $this->post("/dvtokens/", $fields);
        return $result;
    }

    public function getTransactionResult($resultKey) {
        $request = "/txns/withauthkey/" . $resultKey;
        $result = $this->get($request);
        return $result;
    }

    public static function http_parse_headers($header) {
    }

    public function error($body, $url, $json, $type) {
        global $error;
        if (isset($json)) {
            $results = json_decode($body, true);
            $results = $results[0];
            $results['type'] = $type;
            $results['url'] = $url;
            $results['payload'] = $json;
            $error = $results;
        } else {
            $results = json_decode($body, true);
            $results = $results[0];
            $results['type'] = $type;
            $results['url'] = $url;
            $error = $results;
        }
    }

    /**
     * Performs a get request to the instantiated class
     * 
     * Accepts the resource to perform the request on
     * 
     * @param $resource string $resource a string to perform get on
     * @return results or var_dump error
     */
    protected function get($APIrequest) {
        $url = $this->_path . $APIrequest;
        $curl = curl_init();
        if ($this->_proxyFlag == true) {
            curl_setopt_array($curl, array(
                CURLOPT_PROXY => $this->_proxyHost,
                CURLOPT_PROXYPORT => $this->_proxyPort,
            ));
        }
        if ($this->_userAgent) {
            curl_setopt_array($curl, array(
                CURLOPT_USERAGENT => $this->_userAgent
            ));
        }
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_POST => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => $this->_headers
        ));
        $response = curl_exec($curl);
        //Incase server not support to verify the CA cert
        if (!$response) {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_POST => false,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/bpoint-cacert.pem",
                CURLOPT_HTTPHEADER => $this->_headers
            ));
            $response = curl_exec($curl);
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        self::http_parse_headers($headers);
        curl_close($curl);
        if ($http_status == 200) {
            $results = json_decode($response);
            return $results;
        } else {
            $this->error($body, $url, null, 'GET');
        }
    }

    /**
     * Performs a post request to the instantiated class
     * 
     * Accepts the resource to perform the request on, and fields to be sent
     * 
     * @param string $APIrequest a string to perform get on
     * @param array $fields an array to be sent in the request
     * @return results or var_dump error
     */
    protected function post($APIrequest, $fields) {
        global $error;
        $url = $this->_path . $APIrequest;
        $json = json_encode($fields);
        $curl = curl_init();
        if ($this->_proxyFlag == true) {
            curl_setopt_array($curl, array(
                CURLOPT_PROXY => $this->_proxyHost,
                CURLOPT_PROXYPORT => $this->_proxyPort,
            ));
        }
        if ($this->_userAgent) {
            curl_setopt_array($curl, array(
                CURLOPT_USERAGENT => $this->_userAgent
            ));
        }
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => $this->_headers
        ));
        $response = curl_exec($curl);
        //Incase server not support to verify the CA cert
        if (!$response) {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/bpoint-cacert.pem",
                CURLOPT_HTTPHEADER => $this->_headers
            ));
            $response = curl_exec($curl);
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        self::http_parse_headers($headers);
        curl_close($curl);
        if ($http_status == 200) {
            $results = json_decode($response);
            return $results;
        } else {
            $this->error($body, $url, $json, 'POST');
        }
    }

    /**
     * Performs a put request to the instantiated class
     * 
     * Accepts the resource to perform the request on, and fields to be sent
     * 
     * @param string $resource a string to perform get on
     * @param array $fields an array to be sent in the request
     * @return results or var_dump error
     */
    protected function put($resource, $fields) {
        $url = $this->_path . '/' . $resource;
        $json = json_encode($fields);
        $curl = curl_init();
        if ($this->_proxyFlag == true) {
            curl_setopt_array($curl, array(
                CURLOPT_PROXY => $this->_proxyHost,
                CURLOPT_PROXYPORT => $this->_proxyPort,
            ));
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $this->_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ));
        $response = curl_exec($curl);
        //Incase server not support to verify the CA cert
        if (!$response) {
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->_headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => 1,
                CURLOPT_HEADER => 1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/bpoint-cacert.pem"
            ));
            $response = curl_exec($curl);
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        self::http_parse_headers($headers);
        curl_close($curl);
        if ($http_status == 200) {
            $results = json_decode($body, true);
            return $results;
        } else {
            $this->error($body, $url, $json, 'PUT');
        }
    }

    /**
     * Performs a delete request to the instantiated class
     * 
     * Accepts the resource to perform the request on
     * 
     * @param string $resource a string to perform get on
     * @return proper response or var_dump error
     */
    protected function delete($resource) {

        $url = $this->_path . '/' . $resource;

        $curl = curl_init();
        if ($this->_proxyFlag == true) {
            curl_setopt_array($curl, array(
                CURLOPT_PROXY => $this->_proxyHost,
                CURLOPT_PROXYPORT => $this->_proxyPort,
            ));
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $this->_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ));
        $response = curl_exec($curl);
        //Incase server not support to verify the CA cert
        if (!$response) {
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->_headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => 1,
                CURLOPT_HEADER => 1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/bpoint-cacert.pem"
            ));
            $response = curl_exec($curl);
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        self::http_parse_headers($headers);
        curl_close($curl);
        if ($http_status == 204) {
            return $http_status . ' DELETED';
        } else {
            $this->error($body, $url, null, 'DELETE');
        }
    }

}
