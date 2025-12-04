<?php
	namespace BPOINT {
		abstract class Request {
			protected $url;
			protected $mode;
			protected $method;
			private $username;
			private $password;
			private $merchantNumber;
			private $baseUrl;
			private $urlSuffix;
			private $userAgent;
			protected $authHeader;
			private $timeout;
			
			public abstract function submit();
			
			public function __construct() {
				$this->mode = NULL;
				$this->username = NULL;
				$this->password = NULL;
				$this->merchantNumber = NULL;
				$this->userAgent = "BPOINT:1034:1|PHP";
				$this->timeout = 100000;
			}
			
			public function setCredentials($credentials) {
				$this->setMode($credentials->getMode());
				$this->setUsername($credentials->getUsername());
				$this->setPassword($credentials->getPassword());
				$this->setMerchantNumber($credentials->getMerchantNumber());
			}
			
			public function setMode($mode) {
				$this->mode = $mode;
				$this->baseUrl = URLDirectory::getBaseURL($this->mode);
			}
			
			public function setUsername($username) {
				$this->username = $username;
				$this->setAuthHeader();
			}
			
			public function setTimeout($timeout) {
				$this->timeout = $timeout;
			}
			
			public function setPassword($password) {
				$this->password = $password;
				$this->setAuthHeader();
			}
			
			public function setMerchantNumber($merchantNumber) {
				$this->merchantNumber = $merchantNumber;
				$this->setAuthHeader();
			}
			
			protected function setURL($suffix) {
				$this->urlSuffix = $suffix;
				if (NULL == $this->baseUrl) {
					return;
				} else {
					$this->url = $this->baseUrl . $this->urlSuffix;
				}
			}
			
			protected function setMethod($method) {
				$this->method = $method;
			}
			
			protected function getMethod() {
				return $this->method;
			}
			
			protected function setAuthHeader() {
				if ($this->username === NULL || $this->password === NULL || $this->merchantNumber === NULL) {
					return;
				} else {
					$this->authHeader = base64_encode($this->username . "|" . $this->merchantNumber . ":" . $this->password);
				}
				
				if ($this->userAgent != NULL) {
					RequestSender::setUserAgent($this->userAgent);
				}
				
				RequestSender::setTimeout($this->timeout);
				
				
			}
			
			protected function prepare() {
				$this->url = $this->baseUrl . $this->urlSuffix;
				$this->setAuthHeader();
			}
		}
		
		class Credentials {
			private $username;
			private $password;
			private $merchantNumber;
			private $mode;
			
			public function __construct($username, $password, $merchantNumber, $mode = Mode::Live) {
				$this->username = $username;
				$this->password = $password;
				$this->merchantNumber = $merchantNumber;
				$this->mode = $mode;
			}
			
			public function getUsername() {
				return $this->username;
			}
			public function setUsername($username) {
				$this->username = $username;
				return $this;
			}
			public function getPassword() {
				return $this->password;
			}
			public function setPassword($password) {
				$this->password = $password;
				return $this;
			}
			public function getMerchantNumber() {
				return $this->merchantNumber;
			}
			public function setMerchantNumber($merchantNumber) {
				$this->merchantNumber = $merchantNumber;
				return $this;
			}
			public function getMode() {
				return $this->mode;
			}
			public function setMode($mode) {
				$this->mode = $mode;
				return $this;
			}
		}
		
        class HppTxnFlowParameters {
            private $tokeniseTxnCheckBoxDefaultValue;
            public function __construct() {
				
			}
			
			public function getPayload() {
				$payload = array();
				$payload["TokeniseTxnCheckBoxDefaultValue"] = $this->tokeniseTxnCheckBoxDefaultValue;
						
				return $payload;
			}
			
			public function getTokeniseTxnCheckBoxDefaultValue() {
				return $this->tokeniseTxnCheckBoxDefaultValue;
			}
			
			public function setTokeniseTxnCheckBoxDefaultValue($tokeniseTxnCheckBoxDefaultValue) {
				$this->tokeniseTxnCheckBoxDefaultValue = $tokeniseTxnCheckBoxDefaultValue;
				return $this;
			}
        }
        
		class HppParameters {
			private $hideCrn1 = false;
			private $hideCrn2 = false;
			private $hideCrn3 = false;
			private $isEddr = false;
			private $crnLabel1 = NULL;
			private $crnLabel2 = NULL;
			private $crnLabel3 = NULL;
			private $billerCode = NULL;
			private $showCustomerDetailsForm = false;
			
			public function __construct() {
				
			}
			
			public function getPayload() {
				$payload = array();
				$payload["HideCrn1"] = $this->hideCrn1;
				$payload["HideCrn2"] = $this->hideCrn2;
				$payload["HideCrn3"] = $this->hideCrn3;
				$payload["IsEddr"] = $this->isEddr;
				$payload["CrnLabel1"] = $this->crnLabel1;
				$payload["CrnLabel2"] = $this->crnLabel2;
				$payload["CrnLabel3"] = $this->crnLabel3;
				$payload["BillerCode"] = $this->billerCode;
				$payload["ShowCustomerDetailsForm"] = $this->showCustomerDetailsForm;
						
				return $payload;
			}
			
			public function getHideCrn1() {
				return $this->hideCrn1;
			}
			
			public function setHideCrn1($hideCrn1) {
				$this->hideCrn1 = $hideCrn1;
				return $this;
			}
			
			public function getHideCrn2() {
				return $this->hideCrn2;
			}
			
			public function setHideCrn2($hideCrn2) {
				$this->hideCrn2 = $hideCrn2;
				return $this;
			}
			
			public function getHideCrn3() {
				return $this->hideCrn3;
			}
			
			public function setHideCrn3($hideCrn3) {
				$this->hideCrn3 = $hideCrn3;
				return $this;
			}
			
			public function getIsEddr() {
				return $this->isEddr;
			}
			
			public function setIsEddr($isEddr) {
				$this->isEddr = $isEddr;
				return $this;
			}
			
			public function getCrnLabel1() {
				return $this->crnLabel1;
			}
			public function setCrnLabel1($crnLabel1) {
				$this->crnLabel1 = $crnLabel1;
				return $this;
			}
			public function getCrnLabel2() {
				return $this->crnLabel2;
			}
			public function setCrnLabel2($crnLabel2) {
				$this->crnLabel2 = $crnLabel2;
				return $this;
			}
			public function getCrnLabel3() {
				return $this->crnLabel3;
			}
			public function setCrnLabel3($crnLabel3) {
				$this->crnLabel3 = $crnLabel3;
				return $this;
			}
			public function getBillerCode() {
				return $this->billerCode;
			}
			public function setBillerCode($billerCode) {
				$this->billerCode = $billerCode;
				return $this;
			}
			public function getShowCustomerDetailsForm() {
				return $this->showCustomerDetailsForm;
			}
			public function setShowCustomerDetailsForm($showCustomerDetailsForm) {
				$this->showCustomerDetailsForm = $showCustomerDetailsForm;
				return $this;
			}
		}
		
		class SystemStatus extends Request {
			public function __construct() {
				parent::__construct();
				$this->setMethod("GET");
			}
			
			public function submit() {
				$this->setAuthHeader();
				$this->setURL('/status/');
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				
				return APIResponse::fromFullResponse($response);
			}
			
		}
		
		class Transaction extends Request {
			private $action;
			private $amount;
			private $currency;
			private $customer;
			private $merchantReference;
			private $order;
			private $originalTxnNumber;
			private $crn1;
			private $crn2;
			private $crn3;
			private $billerCode;
			private $storeCard = FALSE;
			private $testMode = FALSE;
			private $subType;
			private $type;
			private $emailAddress = NULL;
			private $cardDetail;
			private $tokenisationMode = TokenisationMode::Default_Mode;
			private $fraudScreeningRequest;
			
			public function __construct() {
				parent::__construct();
				$this->setMethod("POST");
				
				/** Set defaults for currently ignored fields */
				
				$this->curency = NULL;
				$this->order = NULL;
				$this->customer = NULL;
				$this->originalTxnNumber = NULL;
				$this->fraudScreeningRequest = NULL;
			}
			
			
			
			public function setAction($action) {
				$this->action = $action;
			}
			
			public function setAmount($amount) {
				$this->amount = $amount;
			}
			
			public function setCurrency($currency) {
				$this->currency = $currency;
			}
			
			public function setCustomer($customer) {
				$this->customer = $customer;
			}
			
			public function setMerchantReference($note) {
				$this->merchantReference = $note;
			}
			
			public function setOrder($order) {
				$this->order = $order;
			}
			
			public function setOriginalTxnNumber($txnNo) {
				$this->originalTxnNumber = $txnNo;
			}
			
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
			}
			
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
			}
			
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
			}
			
			public function setBillerCode($billerCode) {
				$this->billerCode = $billerCode;
			}
			
			public function setStoreCard($store) {
				$this->storeCard = $store;
			}
			
			public function setSubType($subType) {
				$this->subType = $subType;
			}
			
			public function setType($type) {
				$this->type = $type;
			}
			
			public function setCardDetails($cardDetail) {
				$this->cardDetail = $cardDetail;
			}
			
			public function setTestMode($testMode) {
				$this->testMode = $testMode;
			}
			
			public function setTokenisationMode($mode) {
				$this->tokenisationMode = $mode;
			}
			
			public function setFraudScreeningRequest($fraudScreeningRequest) {
				$this->fraudScreeningRequest = $fraudScreeningRequest;
			}

            public function getEmailAddress() {
				return $this->emailAddress;
			}
            
			public function setEmailAddress($emailAddress) {
				$this->emailAddress = $emailAddress;
				return $this;
			}
            
			private function validate() {
				// FIXME - Currently does nothing
				
				return TRUE;
			}
			
			public function submit() {
				$payload = array();
				
				$payload["Action"] = $this->action;
				$payload["Amount"] = $this->amount;
				$payload["Currency"] = $this->currency;
				if ($this->customer !== NULL) {
					$payload["Customer"] = $this->customer->getPayload();
				} else {
					$payload["Customer"] = null;
				}
				$payload["MerchantReference"] = $this->merchantReference;
				if ($this->order !== NULL) {
					$payload["Order"] = $this->order->getPayload();
				} else {
					$payload["Order"]= null;
				}
				$payload["OriginalTxnNumber"] = $this->originalTxnNumber;
				$payload["Crn1"] = $this->crn1;
				$payload["Crn2"] = $this->crn2;
				$payload["Crn3"] = $this->crn3;
				$payload["BillerCode"] = $this->billerCode;
				$payload["StoreCard"] = !! $this->storeCard;
				$payload["SubType"] = $this->subType;
				$payload["Type"] = $this->type;
				$payload["CardDetails"] = $this->cardDetail->getArrayRepresentation();
				$payload["TestMode"] = $this->testMode;
				$payload["EmailAddress"] = $this->emailAddress;
				$payload["TokenisationMode"] = $this->tokenisationMode;
				if ($this->fraudScreeningRequest !== NULL) {
					$payload["FraudScreeningRequest"] = $this->fraudScreeningRequest->getPayload();
				} else {
					$payload["FraudScreeningRequest"] = null;
				}
				$this->setAuthHeader();
				$this->setURL('/txns/');
				$wrappedPayload = array("TxnReq" => $payload);
				
				$response = RequestSender::send($this->url, $this->authHeader, $wrappedPayload, $this->method);
				
				return new TransactionResponse($response);
			}
		}

		class TransactionRetrieval extends Request {
			private $txnNumber = NULL;
			
			public function __construct($txnNumber = NULL) {
				parent::__construct();
				$this->setMethod("GET");
				
				$this->setTxnNumber($txnNumber);
			}
			
			public function setTxnNumber($txnNumber) {
				$this->txnNumber = $txnNumber;
			}
			
			public function submit() {
				$this->setAuthHeader();
				$this->setURL("/txns/" . $this->txnNumber);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				
				return new TransactionResponse($response);
			}
		}
		
		class DeleteToken extends Request {
			private $dvtoken;
			
			public function __construct($dvtoken = NULL) {
				parent::__construct();
				$this->setMethod("DELETE");
				
				$this->dvtoken = $dvtoken;
			}
			
			public function settoken($dvtoken) {
				$this->dvtoken = $dvtoken;
			}
			
			public function submit() {
				$this->setURL("/dvtokens/" . $this->dvtoken);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				
				return APIResponse::fromFullResponse($response);
			}
		}
		
		class AddDVTokenAuthKey extends Request {
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
			private $emailAddress = NULL;
			private $redirectionUrl = NULL;
			private $webHookUrl = NULL;
			private $hppParameters = NULL;
			
			public function __construct() {
				parent::__construct();
				$this->setMethod("POST");
			}
			
			public function setHppParameters($params) {
				$this->hppParameters = $params;
			}
			
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
				return $this;
			}
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
				return $this;
			}
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
				return $this;
			}
			public function setEmailAddress($emailAddress) {
				$this->emailAddress = $emailAddress;
				return $this;
			}
			public function setRedirectionUrl($redirectionUrl) {
				$this->redirectionUrl = $redirectionUrl;
				return $this;
			}
			public function setWebHookUrl($webHookUrl) {
				$this->webHookUrl = $webHookUrl;
				return $this;
			}
			
			protected function buildPayload() {
				$payload = array();
				$outerPayload = array();
				
				$payload["Crn1"] = $this->crn1;
				$payload["Crn2"] = $this->crn2;
				$payload["Crn3"] = $this->crn3;
				$payload["EmailAddress"] = $this->emailAddress;
				
				
				$outerPayload["FixedAddDVTokenData"] = $payload;
				$outerPayload["RedirectionUrl"] = $this->redirectionUrl;
				$outerPayload["WebHookUrl"] = $this->webHookUrl;
				
				if ($this->hppParameters !== NULL) {
					$outerPayload["HppParameters"] = $this->hppParameters->getPayload();
				} else {
					$outerPayload["HppParameters"] = NULL;
				}
				
				return $outerPayload;
			}
	
			public function submit() {
				$this->setURL("/dvtokens/adddvtokenauthkey");
				$payload = $this->buildPayload();
				
				$response = RequestSender::send($this->url, $this->authHeader, $payload, $this->method);
				
				return new AuthKeyResponse($response);
			}			
		}
		
		class UpdateDVTokenAuthKey extends AddDVTokenAuthKey {
			private $dvtoken;
			
			public function __construct() {
				parent::__construct();
				$this->setMethod("POST");
			}
			
			public function setToken($dvtoken) {
				$this->dvtoken = $dvtoken;
			}
			
			public function submit() {
				$this->setURL("/dvtokens/updatedvtokenauthkey");
				$tempPayload = $this->buildPayload();
				
				// Rename FixedAddDVTokenData to FixedUpdateDVTokenData

				$payload = array("FixedUpdateDVTokenData" => $tempPayload["FixedAddDVTokenData"],
						"RedirectionUrl" => $tempPayload["RedirectionUrl"],
						"WebHookUrl" => $tempPayload["WebHookUrl"],
						"HppParameters" => $tempPayload["HppParameters"]); 
				
				$payload["FixedUpdateDVTokenData"]["DVToken"] = $this->dvtoken;
				
				$response = RequestSender::send($this->url, $this->authHeader, $payload, $this->method);
				
				return new AuthKeyResponse($response);
			}
		}

		class AddDVToken extends Request {
			private $cardDetails = NULL;
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
			private $emailAddress = NULL;
			private $bankAccountDetails = NULL;
			
			public function __construct() {
				parent::__construct();
				$this->setMethod("POST");
			}
			
			public function setCardDetails($cardDetails) {
				$this->cardDetails = $cardDetails;
			}
			
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
			}
			
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
			}
			
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
			}
			
			public function setEmailAddress($emailAddress) {
				$this->emailAddress = $emailAddress;
			}
			
			protected function createPayload() {
				$payload = array();
				$payload["BankAccountDetails"] = $this->bankAccountDetails;
				$payload["Crn1"] = $this->crn1;
				$payload["Crn2"] = $this->crn2;
				$payload["Crn3"] = $this->crn3;
                $payload["EmailAddress"] = $this->emailAddress;
				if (NULL != $this->cardDetails) {
					$payload["CardDetails"] = $this->cardDetails->getArrayRepresentation();
				}
				
				$wrappedPayload = array("DVTokenReq" => $payload);
				
				return $wrappedPayload;
			}

			
			public function submit() {
				$payload = $this->createPayload();
				$this->setURL("/dvtokens/");
				
				$response = RequestSender::send($this->url, $this->authHeader, $payload, $this->method);
				
				return new TokenResponse($response);
			}
		}
		
		class UpdateDVToken extends AddDVToken {
			private $dvtoken;
			
			public function __construct($dvtoken = NULL) {
				parent::__construct();
				$this->setMethod("PUT");
				$this->dvtoken = $dvtoken;	
			}
			
			public function setToken($dvtoken) {
				$this->dvtoken = $dvtoken;
			}
			
			public function getToken() {
				return $this->dvtoken;
			}
			
			public function submit() {
				$payload = $this->createPayload();
				$this->setURL("/dvtokens/" . $this->dvtoken);
				
				$response = RequestSender::send($this->url, $this->authHeader, $payload, $this->method);
				
				return new TokenResponse($response);
			}

		}
		
		class TokeniseTransaction extends Request {
			private $txnNumber;
			
			public function __construct($txnNumber = NULL) {
				parent::__construct();
				$this->setMethod("POST");
				$this->txnNumber = $txnNumber;
			}
			
			public function setTxnNumber($txnNumber) {
				$this->txnNumber = $txnNumber;
			}
			
			public function submit() {
				$this->setURL("/dvtokens/txn/" . $this->txnNumber);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				
				return new TokenResponse($response);
			}
		}
		
		class RetrieveToken extends Request {
			private $dvtoken;

			public function __construct($dvtoken = NULL) {
				parent::__construct();
				$this->setMethod("GET");
				$this->dvtoken = $dvtoken;
			}
			
			public function setToken($dvtoken) {
				$this->dvtoken = $dvtoken;
			}
			
			public function submit() {
				$this->setURL("/dvtokens/" . $this->dvtoken);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				
				return new TokenResponse($response);
			}

		}
		
		class TokenSearch extends Request {
			private $cardType = NULL;
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
			private $expiredCardsOnly = FALSE;
			private $expiryDate = NULL;
			private $fromDate = NULL;
			private $toDate = NULL;
			private $source = NULL;
			private $token = NULL;
			private $userCreated = NULL;
			private $userUpdated = NULL;
			private $maskedCardNumber = NULL;
			
			public function __construct() {
				parent::__construct();
				
				$this->setMethod("POST");
			}
			
			public function submit() {
				$this->setURL("/dvtokens/search");
				$payload = array();
				
				$payload["CardType"] = $this->cardType;
				$payload["Crn1"] = $this->crn1;
				$payload["Crn2"] = $this->crn2;
				$payload["Crn3"] = $this->crn3;
				$payload["ExpiredCardsOnly"] = !! $this->expiredCardsOnly;
				$payload["ExpiryDate"] = $this->expiryDate;
				$payload["FromDate"] = $this->fromDate;
				$payload["ToDate"] = $this->toDate;
				$payload["Source"] = $this->source;
				$payload["DVToken"] = $this->token;
				$payload["UserCreated"] = $this->userCreated;
				$payload["UserUpdated"] = $this->userUpdated;
				$payload["MaskedCardNumber"] = $this->maskedCardNumber;
				
				$wrappedPayload = array("SearchInput" => $payload);
				
				$response = RequestSender::send($this->url, $this->authHeader, $wrappedPayload, $this->method);
				
				return new TokenSearchResponse($response);
			}
			
			public function getMaskedCardNumber() {
				return $this->maskedCardNumber;
			}
			
			public function setMaskedCardNumber($maskedCC) {
				$this->maskedCardNumber = $maskedCC;
			}
			
			public function getCardType() {
				return $this->cardType;
			}
			public function setCardType($cardType) {
				$this->cardType = $cardType;
				return $this;
			}
			public function getCrn1() {
				return $this->crn1;
			}
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
				return $this;
			}
			public function getCrn2() {
				return $this->crn2;
			}
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
				return $this;
			}
			public function getCrn3() {
				return $this->crn3;
			}
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
				return $this;
			}
			public function getExpiredCardsOnly() {
				return $this->expiredCardsOnly;
			}
			public function setExpiredCardsOnly($expiredCardsOnly) {
				$this->expiredCardsOnly = $expiredCardsOnly;
				return $this;
			}
			public function getExpiryDate() {
				return $this->expiryDate;
			}
			public function setExpiryDate($expiryDate) {
				$this->expiryDate = $expiryDate;
				return $this;
			}
			public function getFromDate() {
				return $this->fromDate;
			}
			public function setFromDate($fromDate) {
				$this->fromDate = $fromDate;
				return $this;
			}
			public function getToDate() {
				return $this->toDate;
			}
			public function setToDate($toDate) {
				$this->toDate = $toDate;
				return $this;
			}
			public function getSource() {
				return $this->source;
			}
			public function setSource($source) {
				$this->source = $source;
				return $this;
			}
			public function getToken() {
				return $this->token;
			}
			public function setToken($token) {
				$this->token = $token;
				return $this;
			}
			public function getUserCreated() {
				return $this->userCreated;
			}
			public function setUserCreated($userCreated) {
				$this->userCreated = $userCreated;
				return $this;
			}
			public function getUserUpdated() {
				return $this->userUpdated;
			}
			public function setUserUpdated($userUpdated) {
				$this->userUpdates = $userUpdated;
				return $this;
			}
	
			
			
		}
		
		class TokenResultKeyRetrieval extends Request {
			private $resultKey;
			
			public function __construct($resultKey = NULL) {
				parent::__construct();
				$this->setMethod("GET");
				
				$this->setResultKey($resultKey);
			}
			
			public function setResultKey($resultKey) {
				$this->resultKey = $resultKey;
			}
			
			public function submit() {
				$this->setURL("/dvtokens/withauthkey/" . $this->resultKey);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				return new TokenResponse($response);
			}
		}
		
		class ResultKeyRetrieval extends Request {
			private $resultKey;
			
			public function __construct($resultKey = NULL) {
				parent::__construct();
				$this->setMethod("GET");
				
				$this->setResultKey($resultKey);
			}
			
			public function setResultKey($resultKey) {
				$this->resultKey = $resultKey;
			}
			
			public function submit() {
				$this->setAuthHeader();
				$this->setURL("/txns/withauthkey/" . $this->resultKey);
				
				$response = RequestSender::send($this->url, $this->authHeader, NULL, $this->method);
				return new TransactionResponse($response);
			}
		}
		
		class AuthKeyTransaction extends Request {
			private $action = NULL;
			private $amount = 0;
			private $billerCode = NULL;
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
            private $customer;
			private $order;
			private $currency = NULL;
			private $merchantReference = NULL;
			private $redirectionUrl = NULL;
			private $webHookUrl = NULL;
            private $type = NULL;
            private $subType = NULL;
			private $testMode = FALSE;
			private $emailAddress = NULL;
			private $tokenisationMode = TokenisationMode::Default_Mode;
            private $hppParameters;
            private $fraudScreeningDeviceFingerPrint = NULL;
            private $AmexExpressCheckout = FALSE;
			
			public function __construct() {
				parent::__construct();
				$this->setMethod("POST");
			}
			
			public function submit() {
				$this->setAuthHeader();
				$this->setURL("/txns/processtxnauthkey");
				
				$payload = array();
				$wrappedPayload = array();
				
				$payload["Action"] = $this->action;
				$payload["Amount"] = $this->amount;
				$payload["Currency"] = $this->currency;
				
				if ($this->customer !== NULL) {
					$payload["Customer"] = $this->customer->getPayload();
				} else {
					$payload["Customer"] = null;
				}
				$payload["MerchantReference"] = $this->merchantReference;
				if ($this->customer !== NULL) {
					$payload["Order"] = $this->order->getPayload();
				} else {
					$payload["Order"]= null;
				}
				
				if ($this->hppParameters !== NULL) {
					$payload["HppParameters"] = $this->hppParameters->getPayload();
				} else {
					$payload["HppParameters"]= null;
				}
				$payload["Crn1"] = $this->crn1;
				$payload["Crn2"] = $this->crn2;
				$payload["Crn3"] = $this->crn3;
                $payload["Type"] = $this->type;
                $payload["SubType"] = $this->subType;
				$payload["BillerCode"] = $this->billerCode;
				$payload["TestMode"] = $this->testMode;
				$payload["TokenisationMode"] = $this->tokenisationMode;
				$payload["EmailAddress"] = $this->emailAddress;
				$payload["FraudScreeningDeviceFingerPrint"] = $this->fraudScreeningDeviceFingerPrint;
                $payload["AmexExpressCheckout"] = $this->AmexExpressCheckout;
                
				$wrappedPayload["ProcessTxnData"] = $payload;
				$wrappedPayload["RedirectionUrl"] = $this->redirectionUrl;
				$wrappedPayload["WebHookUrl"] = $this->webHookUrl;
				
				$response = RequestSender::send($this->url, $this->authHeader, $wrappedPayload, $this->method);
				
				return new AuthKeyTransactionResponse($response);
			}
			
			public function setTokenisationMode($mode) {
				$this->tokenisationMode = $mode;
			}
			
			public function setEmailAddress($email) {
				$this->emailAddress = $email;
			}
			
			public function setAction($action) {
				$this->action = $action;
			}
			
			public function setAmount($amount) {
				$this->amount = $amount;
			}
			
			public function setCurrency($currency) {
				$this->currency = $currency;
			}
			
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
			}
				
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
			}
				
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
			}
            
			public function setType($type) {
				$this->type = $type;
			}	
            
            public function setSubType($subType) {
				$this->subType = $subType;
			}	
            
			public function setBillerCode($billerCode) {
				$this->billerCode = $billerCode;
			}
			
			public function setCustomer($customer) {
				$this->customer = $customer;
			}
			
			public function setMerchantReference($note) {
				$this->merchantReference = $note;
			}
			
			public function setOrder($order) {
				$this->order = $order;
			}
            
            public function setFraudScreeningDeviceFingerPrint($fraudScreeningDeviceFingerPrint) {
				$this->fraudScreeningDeviceFingerPrint = $fraudScreeningDeviceFingerPrint;
			}
            
			public function setRedirectionURL($redirectionUrl) {
				$this->redirectionUrl = $redirectionUrl;
			}
			
			public function setWebHookURL($webHookUrl) {
				$this->webHookUrl = $webHookUrl;
			}
			
			public function setTestMode($testMode) {
				$this->testMode = $testMode;
			}
			
			public function setHppParameters($hppParameters) {
				$this->hppParameters = $hppParameters;
			}
			 public function setAmexExpressCheckout($AmexExpressCheckout){
                $this->AmexExpressCheckout = $AmexExpressCheckout;
            }
            
		}
		
		class TransactionSearch extends Request {
			private $action = NULL;
			private $amount = 0;
			private $authoriseId = NULL;
			private $bankResponseCode = NULL;
			private $cardType = NULL;
			private $currency = NULL;
			private $merchantReference = NULL;
			private $rrn = NULL;
			private $receiptNumber = NULL;
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
			private $responseCode = NULL;
			private $billerCode = NULL;
			private $settlementDate = NULL;
			private $source = NULL;
			private $txnNumber = NULL;
			private $expiryDate = NULL;
			private $emailAddress = NULL;
			private $maskedCardNumber = NULL;
			private $fromDate = NULL;
			private $toDate = NULL;
			
			public function submit() {
				$this->setURL("/txns/search");
				$this->setMethod("POST");
				$this->setAuthHeader();
				
				$request = array();
				
				$request["Action"] = $this->action;
				$request["Amount"] = $this->amount;
				$request["AuthoriseId"] = $this->authoriseId;
				$request["BankResponseCode"] = $this->bankResponseCode;
				$request["BillerCode"] = $this->billerCode;
				$request["CardType"] = $this->cardType;
				$request["Crn1"] = $this->crn1;
				$request["Crn2"] = $this->crn2;
				$request["Crn3"] = $this->crn3;
				$request["Curency"] = $this->currency;
				$request["ExpiryDate"] = $this->expiryDate;
				$request["FromDate"] = $this->fromDate;
				$request["MaskedCardNumber"] = $this->maskedCardNumber;
				$request["MerchantReference"] = $this->merchantReference;
				$request["RRN"] = $this->rrn;
				$request["EmailAddress"] = $this->emailAddress;
				$request["ReceiptNumber"] = $this->receiptNumber;
				$request["ResponseCode"] = $this->responseCode;
				$request["SettlementDate"] = $this->settlementDate;
				$request["Source"] = $this->source;
				$request["ToDate"] = $this->toDate;
				$request["FromDate"] = $this->fromDate;
				
				$wrappedRequest = array("SearchInput" => $request);
				
				$response = RequestSender::send($this->url, $this->authHeader, $wrappedRequest, $this->method);
				
				return new TransactionSearchResponse($response);
			}
			
			public function setAction($action) {
				$this->action = $action;
			}
			
			public function setAmount($amount) {
				$this->amount = $amount;
			}
			
			public function setEmailAddress($email) {
				$this->emailAddress = $email;
			}
			
			public function setAuthoriseId($authoriseId) {
				$this->authoriseId = $authoriseId;
			}
			
			public function setBankResponseCode($bankResponseCode) {
				$this->bankResponseCode = $bankResponseCode;
			}
			
			public function setCardType($cardType) {
				$this->cardType = $cardType;
			}
			
			public function setCurrency($currency) {
				$this->currency = $currency;
			}
			
			public function setMerchantReference($merchantReference) {
				$this->merchantReference = $merchantReference;
			}
			
			public function setRRN($rrn) {
				$this->rrn = $rrn;
			}
			
			public function setReceiptNumber($receiptNumber) {
				$this->receiptNumber = $receiptNumber;
			}
			
			public function setCrn1($crn1) {
				$this->crn1 = $crn1;
			}
			
			public function setCrn2($crn2) {
				$this->crn2 = $crn2;
			}
			
			public function setCrn3($crn3) {
				$this->crn3 = $crn3;
			}
			
			public function setResponseCode($responseCode) {
				$this->responseCode = $responseCode;
			}
			
			public function setBillerCode($billerCode) {
				$this->billerCode = $billerCode;
			}
			
			public function setSettlmentDate($settlementDate) {
				$this->settlementDate = $settlementDate;
			}
			
			public function setSource($source) {
				$this->source = $source;
			}
			
			public function setTxnNumber($txnNo) {
				$this->txnNumber = $txnNo;
			}
			
			public function setExpiryDate($expiryDate) {
				$this->expiryDate = $expiryDate;
			}
			
			public function setMaskedCardNumber($maskedCc) {
				$this->maskedCardNumber = $maskedCc;
			}
			
			public function setFromDate($fromDate) {
				//$newDate = new \DateTime($fromDate);
				//$this->fromDate = $newDate->format('c');
				
				$this->fromDate = $fromDate;
			}
			
			public function setToDate($toDate) {
				//$newDate = new \DateTime($toDate);
				//$this->toDate = $newDate->format('c');
				
				$this->toDate = $toDate;
			}
			
			
		}
		
		Class Order{
			private $billingAddress;
			private $shippingAddress;
			private $shippingMethod;
			private $orderRecipients = array();
			private $orderItems = array();
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["BillingAddress"] = $this->billingAddress->getPayload();
				$payload["ShippingAddress"] = $this->shippingAddress->getPayload();
				$payload["ShippingMethod"] = $this->shippingMethod;
                
                $itemsPayload = array();
				
				for($i = 0; $i<count($this->orderItems); $i++)
				{
					array_push($itemsPayload,$this->orderItems[$i]->getPayload());
				}
				
				$recipientsPayload = array();
				
				for($i = 0; $i<count($this->orderRecipients); $i++)
				{
					array_push($recipientsPayload,$this->orderRecipients[$i]->getPayload());
				}
				
				$payload["OrderRecipients"] = $recipientsPayload;
				$payload["OrderItems"] = $itemsPayload;
		
				return $payload;
			}
		
			public function getBillingAddress(){
				return $this->billingAddress;
			}
			public function setBillingAddress($billingAddress){
				$this->billingAddress = $billingAddress;
				return $this;
			}
				
			public function getShippingAddress(){
				return $this->shippingAddress;
			}
			public function setShippingAddress($shippingAddress){
				$this->shippingAddress = $shippingAddress;
				return $this;
			}
				
			public function getShippingMethod(){
				return $this->shippingMethod;
			}
			public function setShippingMethod($shippingMethod){
				$this->shippingMethod = $shippingMethod;
				return $this;
			}
				
			public function getOrderRecipients(){
				return $this->orderRecipients;
			}
			public function setOrderRecipients($orderRecipients){
				$this->orderRecipients = $orderRecipients;
				return $this;
			}
				
			public function getOrderItems(){
				return $this->orderItems;
			}
			public function setOrderItems($orderItems){
				$this->orderItems = $orderItems;
				return $this;
			}
		}
		
		Class OrderAddress{
			private $address;
			private $contactDetails;
			private $personalDetails;
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["Address"] = $this->address->getPayload();
				$payload["ContactDetails"] = $this->contactDetails->getPayload();
				$payload["PersonalDetails"] = $this->personalDetails->getPayload();
		
				return $payload;
			}
				
			public function getAddress(){
				return $this->address;
			}
			public function setAddress($address){
				$this->address = $address;
				return $this;
			}
				
			public function getContactDetails(){
				return $this->contactDetails;
			}
			public function setContactDetails($contactDetails){
				$this->contactDetails = $contactDetails;
				return $this;
			}
				
			public function getPersonalDetails(){
				return $this->personalDetails;
			}
			public function setPersonalDetails($personalDetails){
				$this->personalDetails = $personalDetails;
				return $this;
			}
		}
		
		Class OrderItem{
			private $comments;
			private $description;
			private $giftMessage;
			private $partNumber;
			private $productCode;
			private $quantity;
			private $sku;
			private $shippingMethod;
			private $shippingNumber;
			private $unitPrice;
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["Comments"] = $this->comments;
				$payload["Description"] = $this->description;
				$payload["GiftMessage"] = $this->giftMessage;
				$payload["PartNumber"] = $this->partNumber;
				$payload["ProductCode"] = $this->productCode;
				$payload["Quantity"] = $this->quantity;
				$payload["Sku"] = $this->sku;
				$payload["ShippingMethod"] = $this->shippingMethod;
				$payload["ShippingNumber"] = $this->shippingNumber;
				$payload["UnitPrice"] = $this->unitPrice;
		
				return $payload;
			}
				
			public function getComments(){
				return $this->comments;
			}
			public function setComments($comments){
				$this->comments = $comments;
				return $this;
			}
				
			public function getDescription(){
				return $this->description;
			}
			public function setDescription($description){
				$this->description = $description;
				return $this;
			}
				
			public function getGiftMessage(){
				return $this->giftMessage;
			}
			public function setGiftMessage($giftMessage){
				$this->giftMessage = $giftMessage;
				return $this;
			}
				
			public function getPartNumber(){
				return $this->partNumber;
			}
			public function setPartNumber($partNumber){
				$this->partNumber = $partNumber;
				return $this;
			}
				
			public function getProductCode(){
				return $this->productCode;
			}
			public function setProductCode($productCode){
				$this->productCode = $productCode;
				return $this;
			}
				
			public function getQuantity(){
				return $this->quantity;
			}
			public function setQuantity($quantity){
				$this->quantity = $quantity;
				return $this;
			}
				
			public function getSku(){
				return $this->sku;
			}
			public function setSku($sku){
				$this->sku = $sku;
				return $this;
			}
				
			public function getShippingMethod(){
				return $this->shippingMethod;
			}
			public function setShippingMethod($shippingMethod){
				$this->shippingMethod = $shippingMethod;
				return $this;
			}
				
			public function getShippingNumber(){
				return $this->shippingNumber;
			}
			public function setShippingNumber($shippingNumber){
				$this->shippingNumber = $shippingNumber;
				return $this;
			}
				
			public function getUnitPrice(){
				return $this->unitPrice;
			}
			public function setUnitPrice($unitPrice){
				$this->unitPrice = $unitPrice;
				return $this;
			}
		}
		
		Class OrderRecipient{
			private $address;
			private $contactDetails;
			private $personalDetails;
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["Address"] = $this->address->getPayload();
				$payload["ContactDetails"] = $this->contactDetails->getPayload();
				$payload["PersonalDetails"] = $this->personalDetails->getPayload();
		
				return $payload;
			}
				
			public function getAddress(){
				return $this->address;
			}
			public function setAddress($address){
				$this->address = $address;
				return $this;
			}
				
			public function getContactDetails(){
				return $this->contactDetails;
			}
			public function setContactDetails($contactDetails){
				$this->contactDetails = $contactDetails;
				return $this;
			}
				
			public function getPersonalDetails(){
				return $this->personalDetails;
			}
			public function setPersonalDetails($personalDetails){
				$this->personalDetails = $personalDetails;
				return $this;
			}
		}
		
		Class FraudScreeningRequest{
			private $performFraudScreening;
			private $deviceFingerprint;
			private $customerIPAddress;
			private $txnSourceWebsiteURL;
			private $customFields = array();
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["PerformFraudScreening"] = $this->performFraudScreening;
				$payload["DeviceFingerprint"] = $this->deviceFingerprint;
				$payload["CustomerIPAddress"] = $this->customerIPAddress;
				$payload["TxnSourceWebsiteURL"] = $this->txnSourceWebsiteURL;
				$payload["CustomFields"] = $this->customFields;
		
				return $payload;
			}
				
			public function getPerformFraudScreening(){
				return $this->performFraudScreening;
			}
			public function setPerformFraudScreening($performFraudScreening){
				$this->performFraudScreening = $performFraudScreening;
				return $this;
			}
				
			public function getDeviceFingerprint(){
				return $this->deviceFingerprint;
			}
			public function setDeviceFingerprint($deviceFingerprint){
				$this->deviceFingerprint = $deviceFingerprint;
				return $this;
			}
				
			public function getCustomerIPAddress(){
				return $this->customerIPAddress;
			}
			public function setCustomerIPAddress($customerIPAddress){
				$this->customerIPAddress = $customerIPAddress;
				return $this;
			}
				
			public function getTxnSourceWebsiteURL(){
				return $this->txnSourceWebsiteURL;
			}
			public function setTxnSourceWebsiteURL($txnSourceWebsiteURL){
				$this->txnSourceWebsiteURL = $txnSourceWebsiteURL;
				return $this;
			}
				
			public function getCustomFields(){
				return $this->customFields;
			}
			public function setCustomFields($customFields){
				$this->customFields = $customFields;
				return $this;
			}
		}
		
		Class CustomField{
			private $customFieldValue;
				
			public function __construct() {
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["CustomFieldValue"] = $this->customFieldValue;
		
				return $payload;
			}
				
			public function getCustomFieldValue(){
				return $this->customFieldValue;
			}
			public function setCustomFieldValue($customFieldValue){
				$this->customFieldValue = $customFieldValue;
				return $this;
			}
		}
		
		class Customer {
			private $address;
			private $contactDetails;
			private $customerNumber;
			private $personalDetails;
			private $isExistingCustomer = FALSE;
			private $daysOnFile = 1;
				
			public function __construct(){
		
			}
				
			public function getPayload() {
				$payload = array();
		
				$payload["Address"] = $this->address->getPayload();
				$payload["ContactDetails"] = $this->contactDetails->getPayload();
				$payload["CustomerNumber"] = $this->customerNumber;
				$payload["PersonalDetails"] = $this->personalDetails->getPayload();
				$payload["ExistingCustomer"] = $this->isExistingCustomer;
				$payload["DaysOnFile"] = $this->daysOnFile;
		
				return $payload;
			}
				
				
			public function getAddress(){
				return $this->address;
			}
			public function setAddress($address){
				$this->address = $address;
				return $this;
			}
				
			public function getContactDetails(){
				return $this->contactDetails;
			}
			public function setContactDetails($contactDetails){
				$this->contactDetails = $contactDetails;
				return $this;
			}
				
			public function getCustomerNumber(){
				return $this->customerNumber;
			}
			public function setCustomerNumber($customerNumber){
				$this->customerNumber = $customerNumber;
				return $this;
			}
				
			public function getPersonalDetails(){
				return $this->personalDetails;
			}
			public function setPersonalDetails($personalDetails){
				$this->personalDetails = $personalDetails;
				return $this;
			}
				
			public function isExistingCustomer(){
				return $this->isExistingCustomer;
			}
			public function setExistingCustomer($isExistingCustomer){
				$this->isExistingCustomer = $isExistingCustomer;
				return $this;
			}
				
			public function getDaysOnFile(){
				return $this->daysOnFile;
			}
			public function setDaysOnFile($daysOnFile){
				$this->daysOnFile = $daysOnFile;
				return $this;
			}
		}
		
		class Address {
			private $addressLine1;
			private $addressLine2;
			private $addressLine3;
			private $city;
			private $countryCode;
			private $postCode;
			private $state;
		
		
			public function __construct() {
			}
		
			public function getPayload() {
				$payload = array();
		
				$payload["AddressLine1"] = $this->addressLine1;
				$payload["AddressLine2"] = $this->addressLine2;
				$payload["AddressLine3"] = $this->addressLine3;
				$payload["City"] = $this->city;
				$payload["CountryCode"] = $this->countryCode;
				$payload["PostCode"] = $this->postCode;
				$payload["State"] = $this->state;
		
				return $payload;
			}
				
			public function getAddressLine1(){
				return $this->addressLine1;
			}
			public function setAddressLine1($addressLine1){
				$this->addressLine1 = $addressLine1;
				return $this;
			}
				
			public function getAddressLine2(){
				return $this->addressLine2;
			}
			public function setAddressLine2($addressLine2){
				$this->addressLine2 = $addressLine2;
				return $this;
			}
				
			public function getAddressLine3(){
				return $this->addressLine3;
			}
			public function setAddressLine3($addressLine3){
				$this->addressLine3 = $addressLine3;
				return $this;
			}
			public function getCity(){
				return $this->city;
			}
			public function setCity($city){
				$this->city = $city;
				return $this;
			}
				
			public function getCountryCode(){
				return $this->countryCode;
			}
			public function setCountryCode($countryCode){
				$this->countryCode = $countryCode;
				return $this;
			}
				
			public function getPostCode(){
				return $this->postCode;
			}
			public function setPostCode($postCode){
				$this->postCode = $postCode;
				return $this;
			}
				
			public function getState(){
				return $this->state;
			}
			public function setState($state){
				$this->state = $state;
				return $this;
			}
				
		}
		
		class ContactDetails{
			private $emailAddress;
			private $faxNumber;
			private $homePhoneNumber;
			private $mobilePhoneNumber;
			private $workPhoneNumber;
				
			public function __construct() {
			}
		
			public function getPayload(){
				$payload = array();
		
				$payload["EmailAddress"] = $this->emailAddress;
				$payload["FaxNumber"] = $this->faxNumber;
				$payload["HomePhoneNumber"] = $this->homePhoneNumber;
				$payload["MobilePhoneNumber"] = $this->mobilePhoneNumber;
				$payload["WorkPhoneNumber"] = $this->workPhoneNumber;
		
				return $payload;
			}
				
			public function getEmailAddress(){
				return $this->emailAddress;
			}
			public function setEmailAddress($emailAddress){
				$this->emailAddress = $emailAddress;
				return $this;
			}
				
			public function getFaxNumber(){
				return $this->faxNumber;
			}
			public function setFaxNumber($faxNumber){
				$this->faxNumber = $faxNumber;
				return $this;
			}
				
			public function getHomePhoneNumber(){
				return $this->homePhoneNumber;
			}
			public function setHomePhoneNumber($homePhoneNumber){
				$this->homePhoneNumber = $homePhoneNumber;
				return $this;
			}
				
			public function getMobilePhoneNumber(){
				return $this->mobilePhoneNumber;
			}
			public function setMobilePhoneNumber($mobilePhoneNumber){
				$this->mobilePhoneNumber = $mobilePhoneNumber;
				return $this;
			}
				
			public function getWorkPhoneNumber(){
				return $this->workPhoneNumber;
			}
			public function setWorkPhoneNumber($workPhoneNumber){
				$this->workPhoneNumber = $workPhoneNumber;
				return $this;
			}
		}
		
		class PersonalDetails{
			private $dateOfBirth;
			private $firstName;
			private $lastName;
			private $middleName;
			private $salutation;
				
			public function __construct() {
				
			}
		
			public function getPayload() {
				$payload = array();
		
				$payload["DateOfBirth"] = $this->dateOfBirth;
				$payload["FirstName"] = $this->firstName;
				$payload["LastName"] = $this->lastName;
				$payload["MiddleName"] = $this->middleName;
				$payload["Salutation"] = $this->salutation;
		
				return $payload;
			}
				
			public function getDateOfBirth(){
				return $this->dateOfBirth;
			}
			public function setDateOfBirth($dateOfBirth){
				$this->dateOfBirth = $dateOfBirth;
				return $this;
			}
				
			public function getFirstName(){
				return $this->firstName;
			}
			public function setFirstName($firstName){
				$this->firstName = $firstName;
				return $this;
			}
				
			public function getLastName(){
				return $this->lastName;
			}
			public function setLastName($lastName){
				$this->lastName = $lastName;
				return $this;
			}
				
			public function getMiddleName(){
				return $this->middleName;
			}
			public function setMiddleName($middleName){
				$this->middleName = $middleName;
				return $this;
			}
				
			public function getSalutation(){
				return $this->salutation;
			}
			public function setSalutation($salutation){
				$this->salutation = $salutation;
				return $this;
			}
		}
		
	}
