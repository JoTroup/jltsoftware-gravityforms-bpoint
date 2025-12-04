<?php

/*
  Plugin Name: GravityForms Bpoint Addon
  Plugin URI: http://www.gravityforms.com
  Description: Bpoint payment extension for Gravity Forms
  Version: 1.0.1
  Author: Josiah Troup
  Author URI: 
  Date: 4 Dec 2025
 */


 add_filter('gform_pre_render', function($form) {
    if (isset($_POST['entry_id'])) {
        $entry = GFAPI::get_entry($_POST['entry_id']);
        foreach ($form['fields'] as &$field) {
            if (isset($entry[$field->id])) {
                $field->defaultValue = $entry[$field->id];
            }
        }
    }
    return $form;
});

if (class_exists("GFForms")) {
    GFForms::include_payment_addon_framework();

    class GFBpoint extends GFPaymentAddOn {

        protected $_version = "1.0.1";
        protected $_min_gravityforms_version = "1.8.12";
        protected $_slug = 'bpoint';
        protected $_path = 'gravityforms-bpoint/gravityforms-bpoint.php';
        protected $_full_path = __FILE__;
        protected $_title = 'GravityForms Bpoint Addon';
        protected $_short_title = 'Bpoint';
        protected $_supports_callbacks = true;
        protected $_requires_credit_card = true;

        public function init() {
            parent::init();
            error_log("GFBpoint plugin initialized.");
            //add_filter("gform_confirmation", array($this, "payBpoint"), 1000, 4);        
        }

        public function init_frontend() {
            parent::init_frontend();
            error_log("GFBpoint frontend initialized.");
            if (isset($_GET['bpoint_return']) && $_GET['bpoint_return'] == 1) {
                error_log("Bpoint return detected in frontend.");
                add_filter('the_content', array($this, 'result_page'), 20);
            }
        }

        /**
         * set fields bpoint config
         * @return type
         */
        public function feed_settings_fields() {
            $default_settings = parent::feed_settings_fields();

            $fields = array(
                array(
                    'name' => 'bpoint_username',
                    'label' => 'Bpoint API Username',
                    'type' => 'text',
                    'class' => 'medium',
                    'required' => true,
                    'tooltip' => 'Bpoint API Username'
                ),
                array(
                    'name' => 'bpoint_password',
                    'label' => 'BPoint API Password',
                    'type' => 'text',
                    'class' => 'medium',
                    'required' => true,
                    'tooltip' => 'BPoint API Password'
                ),
                array(
                    'name' => 'bpoint_merchant_id',
                    'label' => 'BPoint Merchant Id',
                    'type' => 'text',
                    'class' => 'medium',
                    'required' => true,
                    'tooltip' => 'BPoint Merchant Id'
                ),
                array(
                    'name' => 'bpoint_merchant_currency',
                    'label' => 'Currency Code',
                    'type' => 'text',
                    'class' => 'medium',
                    'required' => true,
                    'default_value' => 'AUD',
                    'tooltip' => 'Currency Code assign to this Merchant Account'
                ),
                array(
                    'name' => 'bpoint_testmode',
                    'label' => 'Test Mode',
                    'type' => 'radio',
                    'choices' => array(
                        array('id' => 'gf_bpoint_mode_test_true', 'label' => 'True', 'value' => 'true'),
                        array('id' => 'gf_bpoint_mode_test_false', 'label' => 'False', 'value' => 'false'),
                    ),
                    'horizontal' => true,
                    'default_value' => 'true',
                    'tooltip' => 'Select False to receive live payments. Select True for testing purposes'
                ),
                array(
                    'name' => 'bpoint_action',
                    'label' => 'Action',
                    'type' => 'radio',
                    'choices' => array(
                        array('id' => 'gf_bpoint_action_payment', 'label' => 'Payment', 'value' => 'payment'),
                        array('id' => 'gf_bpoint_action_preauth', 'label' => 'Preauth', 'value' => 'preauth'),
                    ),
                    'horizontal' => true,
                    'default_value' => 'payment',
                    'tooltip' => 'Select Action'
                ),
                array(
                    'name' => 'bpoint_storecard',
                    'label' => 'Store Card',
                    'type' => 'radio',
                    'choices' => array(
                        array('id' => 'gf_bpoint_storecard_true', 'label' => 'True', 'value' => 'true'),
                        array('id' => 'gf_bpoint_storecard_false', 'label' => 'False', 'value' => 'false'),
                        array('id' => 'gf_bpoint_storecard_dvtoken', 'label' => 'Store Card Only - No Payment', 'value' => 'dvtoken'),
                    ),
                    'horizontal' => true,
                    'default_value' => 'true',
                    'tooltip' => 'Flag to indicate whether the cardholder agrees to save their card details'
                ),
            );

            $default_settings = parent::add_field_after('feedName', $fields, $default_settings);
            return $default_settings;
        }

        public function payBpoint($feed, $form, $entry) {
            error_log("Processing Bpoint payment for entry ID: " . $entry['id']);
            include_once('lib/BPOINT_API.php' );
            global $wp;
            if ($feed['bpoint_testmode'] == 'true') {
                $gateway_url = 'https://www.bpoint.com.au/webapi/v2/';
            } else {
                $gateway_url = 'https://www.bpoint.com.au/webapi/v2/';
            }

            $bpoint_username = $feed['bpoint_username'];
            $bpoint_password = $feed['bpoint_password'];
            $bpoint_merchantid = $feed['bpoint_merchant_id'];
            $amount = GFCommon::get_order_total($form, $entry);
            $amount = number_format($amount * 100, 2, '.', '');

            // checking $_POST Value
            error_log('POST data: ' . print_r($_POST, true));

            $cc = $this->get_cc_fields($form['id']);
            error_log('get_cc_fields returned: ' . print_r($cc, true));


            error_log('Processing credit card details from POST data.');

            error_log('Card Number Field ID: ' . str_replace(".", "_", $cc["Card Number"]));
            $cardNumber = $_POST["input_" . str_replace(".", "_", $cc["Card Number"])];

            error_log('Security Code Field ID: ' . str_replace(".", "_", $cc["Security Code"]));
            $cVN = $_POST["input_" . str_replace(".", "_", $cc["Security Code"])];

            error_log('Expiration Month Field ID: ' . str_replace(array(".", "_month"), array("_", ""), $cc["Expiration Month"]));
            $card_expiration_date = $_POST["input_" . str_replace(array(".", "_month"), array("_", ""), $cc["Expiration Month"])];
            if ($card_expiration_date[0] < 10) {
                $month_card_expiration = '0' . $card_expiration_date[0];
            } else {
                $month_card_expiration = $card_expiration_date[0];
            }
            $expiryDate = $month_card_expiration . substr($card_expiration_date[1], -2);
            $cardHolderName = $_POST["input_" . str_replace(".", "_", $cc["Cardholder Name"])];

            if (isset($entry['id'])) {
                $crn1 = $entry['id'];
            } else {
                $crn1 = 'No provided';
            }
            $bpoint = new BPOINT_API($bpoint_username, $bpoint_password, $bpoint_merchantid, $gateway_url);
            error_log("BPOINT_API initialized with gateway URL: " . $gateway_url);
            if ($feed['bpoint_storecard'] != 'dvtoken') {
                $bpoint->setAction($feed['bpoint_action']);
                $bpoint->setAmount($amount);
                $bpoint->setCurrency($feed['bpoint_merchant_currency']);
                $bpoint->setMerchantReference("");
                $bpoint->setCrn1($crn1);
                $bpoint->setCrn2("");
                $bpoint->setCrn3("");
                $bpoint->setBillerCode(null);
                $bpoint->setSubType("single");
                $bpoint->setType("internet");
                $bpoint->setTestMode($feed['bpoint_testmode']);
                $bpoint->setStoreCard($feed['bpoint_storecard']);
                $bpoint->setcardDetails($cardNumber, $cVN, $expiryDate, $cardHolderName);
                $response = $bpoint->processTransaction();
                error_log("Transaction response: " . print_r($response, true));
            } else {
                $email_customer = $_POST["input_" . $feed['billingInformation_email']];
                $bpoint->setCrn1($crn1);
                $bpoint->setCrn2("");
                $bpoint->setCrn3("");
                $bpoint->setEmailAddress($email_customer);
                $bpoint->setcardDetails($cardNumber, $cVN, $expiryDate, $cardHolderName);
                $response = $bpoint->processDVToken();
                error_log("DVToken response: " . print_r($response, true));
            }
            return $response;
        }




        public function authorize( $feed, $submission_data, $form, $entry ) {
            
            error_log("Authorize (preauth) process started with feed: " . print_r($feed, true) . " for entry ID: " . $entry['id']);
            include_once('lib/BPOINT_API.php' );
            global $wp;

            $meta = isset($feed['meta']) ? $feed['meta'] : array();

            error_log("Feed meta: " . print_r($meta, true));

            if (isset($meta['bpoint_testmode']) && $meta['bpoint_testmode'] == 'true') {
                $gateway_url = 'https://www.bpoint.com.au/webapi/v2/';
            } else {
                $gateway_url = 'https://www.bpoint.com.au/webapi/v2/';
            }

            $bpoint_username   = isset($meta['bpoint_username'])   ? $meta['bpoint_username']   : '';
            $bpoint_password   = isset($meta['bpoint_password'])   ? $meta['bpoint_password']   : '';
            $bpoint_merchantid = isset($meta['bpoint_merchant_id'])? $meta['bpoint_merchant_id']: '';

            if (!$bpoint_username || !$bpoint_password || !$bpoint_merchantid) {
                error_log('BPOINT credentials missing in feed settings for form ID: ' . $form['id']);
                error_log(print_r($feed, true));
            }
            
            // Prepare card details from $submission_data
            $cardNumber      = rgar( $submission_data, 'card_number' );
            $cVN             = rgar( $submission_data, 'cvn' );
            $expiryDate      = rgar( $submission_data, 'expiry' );
            $cardHolderName  = rgar( $submission_data, 'cardholder' );
        
            // Prepare BPOINT API
            $bpoint = new BPOINT_API($bpoint_username, $bpoint_password, $bpoint_merchantid, $gateway_url);
            $bpoint->setType("preauth");
            $bpoint->setTestMode($feed['bpoint_testmode']);
            $bpoint->setStoreCard($feed['bpoint_storecard']);
            $bpoint->setcardDetails($cardNumber, $cVN, $expiryDate, $cardHolderName);
            // Set other required fields (amount, CRN, etc.) from $submission_data or $entry as needed
        
            $response = $bpoint->processTransaction();
            error_log("Authorize (preauth) response: " . print_r($response, true));
        
            // Handle response
            if (isset($response->APIResponse->ResponseCode) && $response->APIResponse->ResponseCode == 0) {
                error_log("Authorize (preauth) successful for entry ID: " . $entry['id']);
                $auth_id = $response->TxnResp->ReceiptNumber;
                RGFormsModel::update_lead_property($entry['id'], 'transaction_id', $auth_id);
                RGFormsModel::update_lead_property($entry['id'], 'payment_status', 'Preauthorized');
                return array(
                    'is_authorized' => true,
                    'transaction_id' => $auth_id,
                    'amount' => $response->TxnResp->Amount / 100,
                    'authorization' => $response
                );
            } else {
                error_log("Authorize (preauth) failed for entry ID: " . $entry['id'] . ". Reason: " . (isset($response->TxnResp->ResponseText) ? $response->TxnResp->ResponseText : 'Unknown error'));
                RGFormsModel::update_lead_property($entry['id'], 'payment_status', 'Preauth Failed');
                return array(
                    'is_authorized' => false,
                    'error_message' => isset($response->TxnResp->ResponseText) ? $response->TxnResp->ResponseText : 'Unknown error',
                    'authorization' => $response
                );
            }
        }



        public function confirmation($confirmation, $form, $entry, $ajax) {
            error_log("Confirmation process started for entry ID: " . $entry['id']);
            $ajax = true;
            $feed = $this->get_feed_setting($form["id"]);

            // Updating lead's payment_status to Processing
            RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Processing');
            RGFormsModel::update_lead_property($entry["id"], "payment_date", date('Y-m-d'));

            if (empty($feed)) {
                error_log("No Bpoint feed found for form ID: " . $form['id']);
                return $confirmation;
            }

            // Process payment
            $response = $this->payBpoint($feed, $form, $entry);

            if (isset($response->APIResponse->ResponseCode) && $response->APIResponse->ResponseCode == 0) {
                if ($feed['bpoint_storecard'] == 'dvtoken') {
                    $trans_id = $response->DVTokenResp->DVToken;
                    RGFormsModel::update_lead_property($entry["id"], "transaction_id", $trans_id);
                    RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Saved Credit Card');
                } else {
                    if ($response->TxnResp->ResponseCode == "0") {
                        $amount = $response->TxnResp->Amount / 100;
                        $trans_id = $response->TxnResp->ReceiptNumber;
                        RGFormsModel::update_lead_property($entry["id"], "payment_amount", $amount);
                        RGFormsModel::update_lead_property($entry["id"], "transaction_id", $trans_id);
                        RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Paid');
                        error_log("Payment successful for entry ID: " . $entry['id']);
                    } else {
                        RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Failed');
                        error_log("Payment failed for entry ID: " . $entry['id'] . ". Reason: " . $response->TxnResp->ResponseText);
                        return '<strong style="color:red;">BPOINT payment declined. Reason: ' . esc_html($response->TxnResp->ResponseText) . '</strong><br/><br/>' .
                            '<input type="hidden" name="entry_id" value="' . esc_attr($entry['id']) . '">' .
                            GFFormDisplay::get_form($form['id'], true, true, false, $entry);
                    }
                }
            } else {
                RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Failed');
                error_log("Payment failed for entry ID: " . $entry['id'] . ". No valid response received.");
                return '<strong style="color:red;">BPOINT payment declined. Reason: ' . esc_html($response->TxnResp->ResponseText) . '</strong><br/><br/>' .
                    '<input type="hidden" name="entry_id" value="' . esc_attr($entry['id']) . '">' .
                    GFFormDisplay::get_form($form['id'], true, true, false, $entry);
            }

            // Return confirmation message or redirect
            if (!empty($form['confirmations'])) {
                foreach ($form['confirmations'] as $confirmation_setting) {
                    if ($confirmation_setting['isDefault']) {
                        return $confirmation_setting['message'];
                    }
                }
            }

            return $confirmation;
        }

        public function get_cc_fields($form_id) {
            error_log("Fetching credit card fields for form ID: " . $form_id);
            $result = array();
            $destForm = RGFormsModel::get_form_meta($form_id, true);
            foreach ($destForm['fields'] as $row) {
                if ($row->type == 'creditcard') {
                    $cc = $row->inputs;
                    foreach ($cc as $cc_row) {
                        $result[$cc_row['label']] = $cc_row['id'];
                    }
                }
            }
            return $result;
        }

        public function get_email_fields($form_id) {
            error_log("Fetching email fields for form ID: " . $form_id);
            $result = array();
            $destForm = RGFormsModel::get_form_meta($form_id, true);
            foreach ($destForm['fields'] as $row) {
                if ($row->type == 'email') {
                    $result[$row['label']] = $row['id'];
                    break;
                }
            }
            return $result;
        }

        public function get_feed_setting($form_id) {
            error_log("Fetching feed settings for form ID: " . $form_id);
            $feeds = $this->get_feeds($form_id);
            $setting = array();
            for ($i = 0; $i < count($feeds); $i++) {
                if ($feeds[$i]['addon_slug'] == $this->_slug && $feeds[$i]['is_active'] == 1)
                    $setting = $feeds[$i]['meta'];
            }
            return $setting;
        }

        public function result_page() {
            error_log("Result page accessed.");
            if (isset($_GET['bpoint_return']) && $_GET['bpoint_return'] == 1) {
                $message = '<br/><br/><strong>Your transaction info:</strong><br/>';
                $message .= 'BPOINT Payment of $' . $_GET['payment_amount'] / 100 . ' was successful. <br>';
                $message .= 'Transaction Id: ' . $_GET['transaction_id'] . '<br/>';
                $message .= get_the_content();
                return $message;
            }
        }

    }

    $GFBpoint = new GFBpoint();
}

add_action('gform_payment_details', 'fgc_payment_details', 9, 2);

function fgc_payment_details($form, $entry) {
    error_log("Displaying payment details for entry ID: " . $entry['id']);
    $payment_gateway = gform_get_meta($entry['id'], 'payment_gateway');
    if ($payment_gateway == 'bpoint') {
        echo 'CRN1:' . $entry['id'];
    }
}

add_filter( 'gform_disable_notification', 'fgc_disable_notification', 10, 4 );
function fgc_disable_notification( $is_disabled, $notification, $form, $entry ) {
    global $GFBpoint;
    if(isset($GFBpoint)) {
        $feed = $GFBpoint->get_feed_setting($form['id']);
        if(isset($feed['bpoint_username'])) {
            error_log("Notification disabled for form ID: " . $form['id']);
            $is_disabled = true;
        }
    }
    return $is_disabled;
}
