<?php

/**
 * Dump and Exit
 */
function dump_exit($var) {
    if(!function_exists('xdebug_disable'))  echo "<pre>";
    var_dump($var);
    if(!function_exists('xdebug_disable'))  echo "</pre>";
    exit;
}

/**
 * Echo and Exit
 */
function echo_exit($var) {
    echo (string)$var;
    exit;
}

/** Object to Array **/
function object_to_array($obj) {
    $arr = array();
    if($obj) {
        foreach($obj as $key => $val) {
            if(is_object($val) || is_array($val)) {
                $arr[$key] = object_to_array($val);
            } else {
                $arr[$key] = $val;
            }
        }
    }
    return $arr;
}

//  Clear All Output
function clearTheOutput() {
    if(ob_get_level() > 0) {
        for($i=0; $i<ob_get_length(); $i++)
            @ob_clean();
    }
}

//  Redirect
function doTheRedirect($url) {
    clearTheOutput();
    @header('Location: ' . $url);
    exit;
}


//  Create Global Variable to Store Database Connection
global $db_pdo, $db_conn;

//  PDO Database Connection
$db_pdo = new PDO("mysql:dbname=" . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);

//  Create NotORM Connection
$db_conn = new NotORM($db_pdo);

//  Get PDO Connection
function getPDOConn() {
    global $db_pdo;
    return $db_pdo;
}

//  Get Database Connection
function getDBConn() {
    global $db_conn;
    return $db_conn;
}


//  Create Global Variable for Event Emitter
global $event_emitter;

//  Create Event Emitter Instance
$event_emitter = new Evenement\EventEmitter();

//  Get Event Emitter
function event() {
    global $event_emitter;
    return $event_emitter;
}


//  Get Gateway Object
function getGatewayObject($gateway, $append = 'Gateway', $prepend = '') {
    $gatewayClass = $prepend . $gateway . $append;
    if(!class_exists($gatewayClass))
        throw new Exception("Payment Gateway '{$gateway}' does not exist.");
    $instance = new $gatewayClass();
    if(!$instance instanceof PaymentGateway)
        throw new Exception("Payment Gateway '{$gateway}' must extend class 'PaymentGateway'.");
    return $instance;
}

//  Check Card Type
function getCardType($number) {

    //  Patterns
    $cards = array(
        "visa" => "(4\d{12}(?:\d{3})?)",
        "amex" => "(3[47]\d{13})",
        "jcb" => "((3[0-9]{4}|2131|1800)[0-9]{11}?)",
        "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
        "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
        "mastercard" => "(5[1-5]\d{14})",
        "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
        "diners" => "(3(?:0[0-5]|[68][0-9])[0-9]{11}?)",
        "discover" => "(6(?:011|5[0-9]{2})[0-9]{12}?)",
    );

    //  Match
    $matches = array();
    $pattern = "#^(?:".implode("|", $cards).")$#";
    $result = preg_match($pattern, str_replace(" ", "", $number), $matches);

    //  Keys
    $keys = array_keys($cards);

    //  Return
    return ($result > 0) ? $keys[sizeof($matches)-2] : null;
}

//  Match Card Type
function cardTypeIs($number, $type) {
    return (getCardType($number) == $type);
}

//  Get Card Type Label
function cardTypeLabel($type) {
    $labels = array(
        'visa' => 'Visa',
        'amex' => 'American Express',
        'jcb' => 'JCB',
        'maestro' => 'Maestro',
        'solo' => 'Solo',
        'mastercard' => 'Mastercard',
        'switch' => 'Switch',
        'diners' => 'Diners Club',
        'discover' => 'Discover'
    );
    return (isset($labels[$type]) ? $labels[$type] : $type);
}

//  Validate Credit Card Number
function isValidCardNumber($number) {

    // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
    $number = preg_replace('/\D/', '', $number);

    // Set the string length and parity
    $number_length = strlen($number);
    $parity = $number_length % 2;

    // Loop through each digit and do the maths
    $total = 0;
    for ($i = 0; $i < $number_length; $i++) {
        $digit = $number[$i];

        // Multiply alternate digits by two
        if ($i % 2 == $parity) {
            $digit*=2;

            // If the sum is two digits, add them together (in effect)
            if ($digit > 9) {
                $digit-=9;
            }
        }

        // Total up the digits
        $total+=$digit;
    }

    // If the total mod 10 equals 0, the number is valid
    return ($total % 10 == 0) ? TRUE : FALSE;
}