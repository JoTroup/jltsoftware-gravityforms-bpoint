<?php
	namespace BPOINT {
		class CardDetails {
			private $cardHolderName;
			private $cardNumber;
			private $cvn;
			private $expiryDate;
			private $maskedCardNumber;
			
			public function setCardHolderName($name) {
				$this->cardHolderName = $name;
			}
			
			public function setCardNumber($cc) {
				$this->cardNumber = $cc;
			}
			
			public function setCVN($cvn) {
				$this->cvn = $cvn;
			}
			
			public function setExpiryDate($expiryDate) {
				$this->expiryDate = $expiryDate;
			}
			
			public function getExpiryDate() {
				return $this->expiryDate;
			}
			
			public function getMaskedCardNumber() {
				return $this->maskedCardNumber;
			}
			
			public function setMaskedCardNumber($cc) {
				$this->maskedCardNumber = $cc;
			}
			
			public function getArrayRepresentation() {
				$detail = array();
				
				$detail["CardHolderName"] = $this->cardHolderName;
				$detail["CardNumber"] = $this->cardNumber;
				$detail["Cvn"] = $this->cvn;
				$detail["ExpiryDate"] = $this->expiryDate;
				
				return $detail;
			}
			
			public function __construct($rep = NULL, $expiryDate = NULL, $cvn = NULL, $cardHolderName = NULL) {
				if ($rep != NULL && $expiryDate == NULL) {
					$this->cardHolderName = $rep->CardHolderName;
					$this->expiryDate = $rep->ExpiryDate;
					$this->maskedCardNumber = $rep->MaskedCardNumber;
				} else if ($rep != NULL && $expiryDate != NULL) {
					$this->cardNumber = $rep;
					$this->expiryDate = $expiryDate;
					$this->cvn = $cvn;
					$this->cardHolderName = $cardHolderName;
				}
			}
		}
		
		class BankAccountDetails {
			private $bankAccountDetails;
			private $accountName;
			private $accountNumber;
			private $bsbNumber;
			private $truncatedAccountNumber;
			
			public function getBankAccountDetails() {
				return $this->bankAccountDetails;
			}
			
			public function setBankAccountDetails($bankAccountDetails) {
				$this->bankAccountDetails = $bankAccountDetails;
				return $this;
			}
			
			public function getAccountName() {
				return $this->accountName;
			}
			
			public function setAccountName($accountName) {
				$this->accountName = $accountName;
				return $this;
			}
			
			public function getAccountNumber() {
				return $this->accountNumber;
			}
			
			public function setAccountNumber($accountNumber) {
				$this->accountNumber = $accountNumber;
				return $this;
			}
			
			public function getBsbNumber() {
				return $this->bsbNumber;
			}
			
			public function setBsbNumber($bsbNumber) {
				$this->bsbNumber = $bsbNumber;
				return $this;
			}
			
			public function getTruncatedAccountNumber() {
				return $this->truncatedAccountNumber;
			}
			
			public function setTruncatedAccountNumber($truncatedAccountNumber) {
				$this->truncatedAccountNumber = $truncatedAccountNumber;
				return $this;
			}
		}
	}
?>
