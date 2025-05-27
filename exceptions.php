<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Archive_Exception extends \Exception {}
class QNAP_Backups_Exception extends \Exception {}
class QNAP_Export_Exception extends \Exception {}
class QNAP_Http_Exception extends \Exception {}
class QNAP_Import_Exception extends \Exception {}
class QNAP_Import_Retry_Exception extends \Exception {}
class QNAP_Not_Accessible_Exception extends \Exception {}
class QNAP_Not_Seekable_Exception extends \Exception {}
class QNAP_Not_Tellable_Exception extends \Exception {}
class QNAP_Not_Readable_Exception extends \Exception {}
class QNAP_Not_Writable_Exception extends \Exception {}
class QNAP_Not_Truncatable_Exception extends \Exception {}
class QNAP_Not_Closable_Exception extends \Exception {}
class QNAP_Not_Found_Exception extends \Exception {}
class QNAP_Not_Directory_Exception extends \Exception {}
class QNAP_Not_Valid_Secret_Key_Exception extends \Exception {}
class QNAP_Quota_Exceeded_Exception extends \Exception {}
class QNAP_Storage_Exception extends \Exception {}
class QNAP_Compatibility_Exception extends \Exception {}
class QNAP_Feedback_Exception extends \Exception {}
class QNAP_Report_Exception extends \Exception {}
class QNAP_Database_Exception extends \Exception {}
