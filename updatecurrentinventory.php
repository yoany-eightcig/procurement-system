<?php 

function locateRequest($curlRequestType, $endpoint, $sessionToken = null, $postData = null) {
    global $locate_base_url;
    // Create CURL Request
    $curlRequest = curl_init();

    // Set CURL Options
    curl_setopt($curlRequest, CURLOPT_CUSTOMREQUEST, $curlRequestType);
    curl_setopt($curlRequest, CURLOPT_URL, $locate_base_url . $endpoint);
    curl_setopt($curlRequest, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);

    // Check for POST Data
    if($postData !== null) {
        curl_setopt($curlRequest, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    // BASIC Auth
    if($sessionToken !== null) {
        curl_setopt($curlRequest, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curlRequest, CURLOPT_USERPWD, $sessionToken);
    }
    // Execute CURL Request
    $response = curl_exec($curlRequest);    
    $httpCode = curl_getinfo($curlRequest, CURLINFO_HTTP_CODE);

    // Check HTTP Status Code
    if($httpCode == 200 || $httpCode == 201) {
        $json_object = json_decode($response);
        if ($json_object) {
            return $json_object;
        } else {
            return $response;               
        }
    }
    else {
        // throw new Exception($httpCode . ' - ' . $response);
        echo ($httpCode . ' - ' . $response);
    }
}


function updateCurrentInventory() 
{
	set_time_limit(0);
	ini_set('max_execution_time', 0);

    $fileName = "current_inventory.csv";

    if (file_exists(dirname(__FILE__).'/storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file(dirname(__FILE__).'/storage/app/public/'.$fileName));
    }

    $missing = [];
    echo "updating: Current Inventory \n\r";

	$servername = "localhost";
	$username = "root";
	$password = "Magma2019";
	$dbname = "procurement_system";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

    foreach ($csv as $line) {
        if ( (count($line) == 4 && $line[0]!= '' && $line[0]!= 'North' && $line[0]!= 'Main' && $line[0]!= 'Part Number') ||  (count($line) == 6 && $line[0]!= ''  && $line[0]!= 'Main' && $line[0]!= 'North' && $line[0]!= 'Part Number')) {

            $data = [];
            if (count($line) == 4) {
                $data = [
                    'sku' => $line[0],
                    'on_order' => $line[1],
                    'quantity' => $line[2],
                ];
            } 
            if ((count($line) == 6) && $line[2] != 'Available for Sale') {
                $data = [
                    'sku' => $line[0],
                    'on_order' => $line[2],
                    'quantity' => $line[3],
                ];
            }

            if (count($data)) {
                $data['quantity'] = str_replace(',', '', $data['quantity']);
                if (empty($data['quantity'])) {
                    $data['quantity'] = 0;
                }

                $data['on_order'] = str_replace(',', '', $data['on_order']);
                if (empty($data['on_order'])) {
                    $data['on_order'] = 0;
                }

                $sql = "UPDATE `parts` SET `on_order` = '{$data['on_order']}', `quantity` = '{$data['quantity']}' WHERE `sku` LIKE '{$data['sku']}'";

                echo $sql."\n";
                if (mysqli_query($conn, $sql)) {
                    echo $sql."\n";
                    echo $data['sku']." ";
                } 
            }

        }
    }

    echo "updated \n\r";
} 

function downloadCurrentInventoryReport($sessionToken) 
{
    set_time_limit(0);
    ini_set('max_execution_time', 0);

    /*
    $report_name = "Inventory By Part";
    $report = $this->locateRequest('GET', '/report?name='.urlencode($report_name), $this->sessionToken);
    $report_id = $report->data[0]->id;
    */

    $report_id = 645;

    $response = locateRequest('GET', "/report/".$report_id."/run", $sessionToken, array(
        //'includeoutofstockparts' => 'LEFT',
        'includeoutofstockparts' => '',
        'orderby' => 1,
        'availableforsale' => 1,
        'bomqtydeconstructable' => 0,
        'bomquantitybuildable' => 0,
        'costingvalue' => 0,
        'formatassinglepage' => 0,
        'lastcostatsite' => 0,
        'lmbundlequantitybuildable' => 0,
        'lmbundlequantitydeconstructable' => 0,
        'partdescription' => 0,
        'partname' => 0,
        'partnumber' => 1,
        'purchasevalue' => 0,
        'qtyallocated' => 0,
        'qtycommitted' => 0,
        'qtyininventory' => 0,
        'qtyonorder' => 1,
        'uom' => 0,
        'format'=> 'csv',
    ));

    $result = file_put_contents(dirname(__FILE__).'/storage/app/public/current_inventory.csv', $response);

    if ($result) {
        return true;
    } else {
        return $response;
    }
}

$locate_base_url = "https://magma.locateinv.com";
$locate_username = "procurement@eightcig.com";
$locate_password = "Vape1234";

// Login
$loginRequest = array(
    'email' => $locate_username,
    'password' => $locate_password,
);

$loginResponse = locateRequest('POST', '/login', null, $loginRequest);
$sessionToken = $loginResponse->session_token;

echo "Session Token: ".$sessionToken."\n";
echo "Downloading: Inventory By Part Report\n";
$result = downloadCurrentInventoryReport($sessionToken)."\n";

if ($result) {
    updateCurrentInventory();
} else {
    echo $result;
}

echo "Done";

?>