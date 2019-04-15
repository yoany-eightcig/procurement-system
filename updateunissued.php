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
        throw new Exception($httpCode . ' - ' . $response);
    }
}

function updateUnissued ()
{
	set_time_limit(0);
	ini_set('max_execution_time', 0);

    $fileName = "purchase_order_summary.csv";

    if (file_exists('storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file('storage/app/public/'.$fileName));
    }

    $result = false;

    $missing = [];

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
    	$sku = null;
    	$unissued = null;

    	if (count($line) == 16 && $line[1] != "" && $line[0] != "  " && $line[0] !="Purchase Order #" && $line[1] != "Part Number" && $line[5] !="Unissued") {
    		$sku = $line[1];
    		$unissued = intval(str_replace(',', '', $line[7]));

    	} else if (count($line) == 14 && $line[1] != "" && $line[0] != "  " && $line[0] !="Purchase Order #" && $line[1] != "Part Number") {
    		$sku = $line[1];
    		$unissued = intval(str_replace(',', '', $line[5]));
    	}
    	if ($unissued && $sku) {

			$sql = "SELECT * FROM parts WHERE sku='$sku'";
			$result = mysqli_query($conn, $sql);

			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
				}
		    	$sql = "UPDATE parts SET unissued='$unissued' WHERE sku='$sku'";

		    	if (mysqli_query($conn, $sql)) {
		    	    echo "$sku [$unissued] ";
		    	} else {
		    	    echo "Error updating record: " . $conn->error. " ";
		    	    echo "$sku [$unissued] \r";
		    	}
			}

    	}
    }

	mysqli_close($conn);
}

$locate_base_url = "https://magma.locateinv.com";
$locate_username = "procurement@eightcig.com";
$locate_password = "Vape1234";

// Login
$loginRequest = array(
    'email' => $locate_username,
    'password' => $locate_password,
);

// $loginResponse = locateRequest('POST', '/login', null, $loginRequest);
// $sessionToken = $loginResponse->session_token;

// echo $sessionToken."\n";

updateUnissued();

echo "\n\r";

?>