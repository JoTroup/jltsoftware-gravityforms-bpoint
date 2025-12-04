<?php
	namespace BPOINT {
		include_once("BPOINT.php");
		abstract class Response {
			private $apiResponse;
			
			public function getAPIResponse() {
				return $this->apiResponse;
			}
			
			public function __construct($apiResponse) {
				$this->apiResponse = $apiResponse;
			}
		}
		
		class APIResponse {
			private $responseCode;
			private $responseText;
			
			public function __construct($responseCode, $responseText) {
				$this->responseCode = $responseCode;
				$this->responseText = $responseText;	
			}
			
			public function getResponseCode() {
				return $this->responseCode;
			}
			
			public function getResponseText() {
				return $this->responseText;
			}
			
			public function isSuccessful() {
				return !! ($this->responseCode == 0);
			}
			
			public static function fromFullResponse($response) {
				$responseCode = $response->APIResponse->ResponseCode;
				$responseText = $response->APIResponse->ResponseText;
				$apiResponse = new APIResponse($responseCode, $responseText);
				
				return $apiResponse;
			}
		}
		
		class CVNResult {
			private $cvnResultCode;
			
			public function __construct($cvnResult) {
				if (NULL == $cvnResult) {
					$this->cvnResultCode = "Unsupported";
				} else {
					$this->cvnResultCode = $cvnResult->CVNResultCode;
				}
			}
			
			public function getCVNResultCode() {
				return $this->cvnResultCode;
			}
		}
		
		class TokenResponse extends Response {
			private $cardDetails = NULL;
			private $bankAccountDetails = NULL;
			private $cardType = NULL;
			private $crn1 = NULL;
			private $crn2 = NULL;
			private $crn3 = NULL;
			private $emailAddress = NULL;
			private $token = NULL;
			
			public function __construct($responseArray) {
				$apiPayload = $responseArray->APIResponse;
				$apiResponse = new APIResponse($apiPayload->ResponseCode, $apiPayload->ResponseText);
				
				parent::__construct($apiResponse);
				
				if ($apiResponse->getResponseCode() == "0") {
					$elements = $responseArray->DVTokenResp;
					$this->cardDetails = new CardDetails($elements->CardDetails);
					$this->cardType = $elements->CardType;
					$this->emailAddress = $elements->EmailAddress;
					$this->crn1 = $elements->Crn1;
					$this->crn2 = $elements->Crn2;
					$this->crn3 = $elements->Crn3;
					$this->token = $elements->DVToken;
				}
			}
			
			public function getCardDetails() {
				return $this->cardDetails;
			}
			public function getBankAccountDetails() {
				return $this->bankAccountDetails;
			}
			public function getCardType() {
				return $this->cardType;
			}
			public function getCrn1() {
				return $this->crn1;
			}
			public function getCrn2() {
				return $this->crn2;
			}
			public function getCrn3() {
				return $this->crn3;
			}
			public function getEmailAddress() {
				return $this->emailAddress;
			}
			public function getToken() {
				return $this->token;
			}
		}
			
		class TokenSearchResponse extends Response {
			private $dvtokens;
			private $tokenIndex;
				
			public function __construct($responseArray) {
				$apiPayload = $responseArray->APIResponse;
				$apiResponse = new APIResponse($apiPayload->ResponseCode, $apiPayload->ResponseText);
			
				parent::__construct($apiResponse);
			
				$this->dvtokens = array();
			
				if ($apiResponse->getResponseCode() == "0") {
					$tokenList = $responseArray->DVTokenRespList;
			
					foreach ($tokenList as $token) {
						$tokenPayload = (object) array();
						$tokenPayload->APIResponse = $apiPayload;
						$tokenPayload->DVTokenResp = $token;
			
						$this->dvtokens[] = new TokenResponse($tokenPayload);
					}
				}
			}
			
			public function getResultCount() {
				return count($this->dvtokens);
			}
				
			public function getTokens() {
				return $this->dvtokens;
			}
				
			public function nextToken() {
				$returnValue = NULL;
				if (count($this->dvtokens) > $this->tokenIndex) {
					$returnValue = $this->dvtokens[$this->tokenIndex];
					$this->tokenIndex += 1;
				}
			
				return $returnValue;
			}
				
			public function reset() {
				$this->tokenIndex = 0;
			}
		}
		

		
		class TransactionResponse extends Response {
			private $action;
			private $amount;
			private $amountSurcharge;
			private $threeDSResponse;
			private $authoriseId;
			private $bankAccountDetails;
			private $bankResponseCode;
			private $cvnResult;
			private $cardDetails;
			private $cardType;
			private $currency;
			private $isThreeDS;
			private $isCVNPresent;
			private $merchantNumber;
			private $originalTxnNumber;
			private $processedDateTime;
			private $rrn;
			private $receiptNumber;
			private $crn1;
			private $crn2;
			private $crn3;
			private $responseCode;
			private $responseText;
			private $billerCode;
			private $settlementDate;
			private $source;
			private $storeCard;
			private $subType;
			private $txnNumber;
			private $type;
			private $isTestTxn;
			private $emailAddress;
			private $dvtoken;
			private $fraudScreeningResponse;
			
			public function __construct($responseArray) {
				$apiPayload = $responseArray->APIResponse;
				$apiResponse = new APIResponse($apiPayload->ResponseCode, $apiPayload->ResponseText);
				
				parent::__construct($apiResponse);
				if ($apiResponse->getResponseCode() == "0") {
					
					$elements = $responseArray->TxnResp;

					$this->action = $elements->Action;
					$this->amount = $elements->Amount;
					$this->amountSurcharge = $elements->AmountSurcharge;
					$this->threeDSResponse = $elements->ThreeDSResponse;
					$this->authoriseId = $elements->AuthoriseId;
					$this->bankAccountDetails = $elements->BankAccountDetails;
					$this->bankResponseCode = $elements->BankResponseCode;
					$this->cvnResult = new CVNResult($elements->CVNResult);
					
					$ccDetails = new CardDetails();
					$cardPayload = $elements->CardDetails;
					
					$ccDetails->setCardHolderName($cardPayload->CardHolderName);
					$ccDetails->setExpiryDate($cardPayload->ExpiryDate);
					$ccDetails->setMaskedCardNumber($cardPayload->MaskedCardNumber);
					
					$this->cardDetails = $ccDetails;
					
					$this->cardType = $elements->CardType;
					$this->currency = $elements->Currency;
					$this->isThreeDS = $elements->IsThreeDS;
					$this->isCVNPresent = $elements->IsCVNPresent;
					$this->merchantNumber = $elements->MerchantNumber;
					$this->originalTxnNumber = $elements->OriginalTxnNumber;
					$this->processedDateTime = $elements->ProcessedDateTime;
					$this->rrn = $elements->RRN;
					$this->ReceiptNumber = $elements->ReceiptNumber;
					$this->crn1 = $elements->Crn1;
					$this->crn2 = $elements->Crn2;
					$this->crn3 = $elements->Crn3;
					$this->responseCode = $elements->ResponseCode;
					$this->responseText = $elements->ResponseText;
					$this->billerCode = $elements->BillerCode;
					$this->settlementDate = $elements->SettlementDate;
					$this->source = $elements->Source;
					$this->subType = $elements->SubType;
                    $this->storeCard = $elements->StoreCard;
					$this->txnNumber = $elements->TxnNumber;
					$this->type = $elements->Type;
					$this->isTestTxn = $elements->IsTestTxn;
					
					$fsDetails = new FraudScreeningResponse( $elements->FraudScreeningResponse);
					
					$this->fraudScreeningResponse = $fsDetails;
					
					if (isset($elements->DVToken)) {
						$this->dvtoken = $elements->DVToken;
					}
					
					if (isset($elements->EmailAddress)) {
						$this->emailAddress = $elements->EmailAddress;
					}
				}
				
			}
			
			public function isApproved() {
				$resp = $this->responseCode;
				$retVal = NULL;
				
				if ($resp == "0" || $resp == "00" || $resp == "08" || $resp == "16") {
					$retVal = TRUE;	
				} else {
					$retVal = FALSE;
				}
				
				return $retVal;
			}
			
			public function getAction() {
				return $this->action;
			}
			
			public function getAmount() {
				return $this->amount;
			}
			
			public function getAmountSurcharge() {
				return $this->amountSurcharge;
			}
			
			public function getThreeDSResponse() {
				return $this->threeDSResponse;
			}
			
			public function getAuthoriseId() {
				return $this->authoriseId;
			}
			
			public function getBankAccountDetails() {
				return $this->bankAccountDetails;
			}
			
			public function getCVNResult() {
				return $this->cvnResult;
			}
			
			public function getCardDetails() {
				return $this->cardDetails;
			} 
			
			public function getCardType() {
				return $this->cardType;
			}
			
			public function getCurrency() {
				return $this->currency;
			}

			public function getIs3DS() {
				return $this->isThreeDS;
			}
			
			public function getIsCVNPresent() {
				return $this->isCVNPresent;
			}
			
			public function getMerchantNumber() {
				return $this->merchantNumber;
			}
			
			public function getOriginalTxnNumber() {
				return $this->originalTxnNumber;
			}
			
			public function getProcessedDateTime() {
				return $this->processedDateTime;
			}
			
			public function getRRN() {
				return $this->rrn;
			}
			
			public function getReceiptNumber() {
				return $this->receiptNumber;
			}
			
			public function getCrn1() {
				return $this->crn1;
			}
			
			public function getCrn2() {
				return $this->crn2;
			}
			
			public function getCrn3() {
				return $this->crn3;
			}
		
			public function getResponseCode() {
				return $this->responseCode;
			}
			
			public function getResponseText() {
				return $this->responseText;
			}
			
			public function getBillerCode() {
				return $this->billerCode;
			}
				
			public function getSettlementDate() {
				return $this->settlementDate;
			}
			
			public function getSource() {
				return $this->source;
			}
			
			public function getStoreCard() {
				return $this->storeCard;
			}
		
			public function getSubType() {
				return $this->subType;
			}
			
			public function getTxnNumber() {
				return $this->txnNumber;
			}
			
			public function getType() {
				return $this->type;
			}
			
			public function getFraudScreeningResponse() {
				return $this->fraudScreeningResponse;
			}
		}
		
		class AuthKeyTransactionResponse extends AuthKeyResponse {
			public function __construct($responseArray) {
				parent::__construct($responseArray);
			}

		}
	
		class AuthKeyResponse extends Response {
			private $authKey = NULL;
			
			public function __construct($responseArray) {
				$apiPayload = $responseArray->APIResponse;
				$apiResponse = new APIResponse($apiPayload->ResponseCode, $apiPayload->ResponseText);
				
				parent::__construct($apiResponse);
				
				if ($apiResponse->getResponseCode() == "0") {
					$this->authKey = $responseArray->AuthKey;
				}
			}
			
			public function getAuthKey() {
				return $this->authKey;
			}
		}
		
		class TransactionSearchResponse extends Response {
			private $transactions;
			private $transactionIndex;
			
			public function __construct($responseArray) {
				$apiPayload = $responseArray->APIResponse;
				$apiResponse = new APIResponse($apiPayload->ResponseCode, $apiPayload->ResponseText);
				
				parent::__construct($apiResponse);
				
				$this->transactions = array();
				
				if ($apiResponse->getResponseCode() == "0") {
					$transactionList = $responseArray->TxnRespList;
				
					foreach ($transactionList as $transaction) {
						$transactionPayload = (object) array();
						$transactionPayload->APIResponse = $apiPayload;
						$transactionPayload->TxnResp = $transaction;
						
						$this->transactions[] = new TransactionResponse($transactionPayload);
					}
				}
			}
			
			public function getResultCount() {
				return count($this->transactions);
			}
			
			public function getTransactions() {
				return $this->transactions;
			}
			
			public function nextTransaction() {
				$returnValue = NULL;
				if (count($this->transactions) > $this->transactionIndex) {
					$returnValue = $this->transactions[$this->transactionIndex];
					$this->transactionIndex += 1;
				}
				
				return $returnValue;
			}
			
			public function reset() {
				$this->transactionIndex = 0;
			}
		}
		
		class FraudScreeningResponse{
				
			private $txnRejected;
			private $responseCode;
			private $responseMessage;
			private $reDResponse;
		
			public function __construct($responseArray) {
                if (isset($responseArray->TxnRejected)) {
					$this->txnRejected = $responseArray->TxnRejected;
				}
				if (isset($responseArray->ResponseCode)) {
					$this->responseCode = $responseArray->ResponseCode;
				}
				if (isset($responseArray->ResponseMessage)) {
					$this->responseMessage = $responseArray->ResponseMessage;
				}
				if (isset($responseArray->ReDResponse)) {
					$this->reDResponse = $responseArray->ReDResponse;
				}
			}
				
			public function getTxnRejected(){
				return $this->txnRejected;
			}
			public function setTxnRejected($txnRejected){
				$this->txnRejected = $txnRejected;
				return $this;
			}
				
			public function getResponseCode(){
				return $this->responseCode;
			}
			public function setResponseCode($responseCode){
				$this->responseCode = $responseCode;
				return $this;
			}
				
			public function getResponseMessage(){
				return $this->responseMessage;
			}
			public function setResponseMessage($responseMessage){
				$this->responseMessage = $responseMessage;
				return $this;
			}
				
			public function getReDResponse(){
				return $this->reDResponse;
			}
			public function setReDResponse($reDResponse){
				$this->reDResponse = $reDResponse;
				return $this;
			}
		}
		
		class ReDResponse{
			private $rEQ_ID;
			private $oRD_ID;
			private $sTAT_CD;
			private $fRAUD_STAT_CD;
			private $fRAUD_RSP_CD;
			private $fRAUD_REC_ID;
			private $fRAUD_NEURAL;
			private $fRAUD_RCF;
				
			public function __construct($responseArray) {
                if (isset($responseArray->REQ_ID)) {
					$this->rEQ_ID = $responseArray->REQ_ID;
				}
				if (isset($responseArray->ORD_ID)) {
					$this->oRD_ID = $responseArray->ORD_ID;
				}
				if (isset($responseArray->STAT_CD)) {
					$this->sTAT_CD = $responseArray->STAT_CD;
				}
				if (isset($responseArray->FRAUD_STAT_CD)) {
					$this->fRAUD_STAT_CD = $responseArray->FRAUD_STAT_CD;
				}
				if (isset($responseArray->FRAUD_RSP_CD)) {
					$this->fRAUD_RSP_CD = $responseArray->FRAUD_RSP_CD;
				}
				if (isset($responseArray->FRAUD_REC_ID)) {
					$this->fRAUD_REC_ID = $responseArray->FRAUD_REC_ID;
				}
				if (isset($responseArray->FRAUD_NEURAL)) {
					$this->fRAUD_NEURAL = $responseArray->FRAUD_NEURAL;
				}
				if (isset($responseArray->FRAUD_RCF)) {
					$this->fRAUD_RCF = $responseArray->FRAUD_RCF;
				}
			}
			public function getREQ_ID(){
				return $this->rEQ_ID;
			}
			public function setREQ_ID($rEQ_ID){
				$this->rEQ_ID = $rEQ_ID;
				return $this;
			}
				
			public function getORD_ID(){
				return $this->oRD_ID;
			}
			public function setORD_ID($oRD_ID){
				$this->oRD_ID = $oRD_ID;
				return $this;
			}
				
			public function getSTAT_CD(){
				return $this->sTAT_CD;
			}
			public function setSTAT_CD($sTAT_CD){
				$this->sTAT_CD = $sTAT_CD;
				return $this;
			}
				
			public function getFRAUD_STAT_CD(){
				return $this->fRAUD_STAT_CD;
			}
			public function setFRAUD_STAT_CD($fRAUD_STAT_CD){
				$this->fRAUD_STAT_CD = $fRAUD_STAT_CD;
				return $this;
			}
				
			public function getFRAUD_RSP_CD(){
				return $this->fRAUD_RSP_CD;
			}
			public function setFRAUD_RSP_CD($fRAUD_RSP_CD){
				$this->fRAUD_RSP_CD = $fRAUD_RSP_CD;
				return $this;
			}
				
			public function getFRAUD_REC_ID(){
				return $this->fRAUD_REC_ID;
			}
			public function setFRAUD_REC_ID($fRAUD_REC_ID){
				$this->fRAUD_REC_ID = $fRAUD_REC_ID;
				return $this;
			}
				
			public function getFRAUD_NEURAL(){
				return $this->fRAUD_NEURAL;
			}
			public function setFRAUD_NEURAL($fRAUD_NEURAL){
				$this->fRAUD_NEURAL = $fRAUD_NEURAL;
				return $this;
			}
				
			public function getFRAUD_RCF(){
				return $this->fRAUD_RCF;
			}
			public function setFRAUD_RCF($fRAUD_RCF){
				$this->fRAUD_RCF = $fRAUD_RCF;
				return $this;
			}
		}
		
		
	}
