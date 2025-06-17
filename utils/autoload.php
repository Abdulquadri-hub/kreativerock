<?php

$rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";

date_default_timezone_set('Africa/Lagos');

// Include utility files
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'utils/sanitize.php';
require_once $rootFolder . 'utils/validator.php';
require_once $rootFolder . 'utils/common.php';

// vendor files
require_once $rootFolder . 'vendor/src/Facebook/autoload.php';


// Include model files
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/config.php';
require_once $rootFolder . 'model/Administration.php';
require_once $rootFolder . 'model/FrameWork.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/ResponseHandler.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'model/ExternalSmsApi.php';
require_once $rootFolder . 'model/GushupApi.php';
require_once $rootFolder . 'model/Template.php';
require_once $rootFolder . 'model/Contact.php';
require_once $rootFolder . 'model/Campaign.php';
require_once $rootFolder . 'model/Conversation.php';
require_once $rootFolder . 'model/Segment.php';
require_once $rootFolder . 'model/SmsPackage.php';
require_once $rootFolder . 'model/SmsPurchase.php';
require_once $rootFolder . 'model/SmsTransaction.php';
require_once $rootFolder . 'model/WhatsAppPackage.php';
require_once $rootFolder . 'model/WhatsAppPurchase.php';
require_once $rootFolder . 'model/WhatsAppTransaction.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/TwoWayWhatsapp.php';
require_once $rootFolder . 'model/ApiKeyManager.php';
require_once $rootFolder . 'model/SmsIntegration.php';
require_once $rootFolder . 'model/Logger.php';
require_once $rootFolder . 'model/Facebook.php';
require_once $rootFolder . 'model/RolesAndPermissions.php';
require_once $rootFolder . 'model/smslog.php';
require_once $rootFolder . 'model/Email_Template.php';
