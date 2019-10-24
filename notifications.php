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

function saveReport($id, $filename, $sessionToken, $response) 
{
    $result = file_put_contents(dirname(__FILE__)."/storage/app/public/".$filename, $response);

    $response = locateRequest('POST', "/notification/delete", $sessionToken, array(
        "notification_ids" => [$id],
    ));
    echo var_dump($response). "\n";
}

function getReportDate ($basePath, $line, $col)
{
    $csv = array_map('str_getcsv', file($basePath));
    $date = $csv[$line][$col];
    return $date;
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


function updateUnissued ()
{
    set_time_limit(0);
    ini_set('max_execution_time', 0);

    $fileName = "purchase_order_summary.csv";

    if (file_exists(dirname(__FILE__).'/storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file(dirname(__FILE__).'/storage/app/public/'.$fileName));
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

function getNotifications($sessionToken)
{
	$result = true;
	$response = locateRequest('GET', "/notification", $sessionToken, array());

	echo "Count: ".count($response->data)."\n";

	foreach ($response->data as $notification) {

		echo "Notification ID: {$notification->id}\n";
		preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $notification->message, $url);
		echo ("Link: ".$url['href'][0])."\n";
		$url = $url['href'][0];

		$parts = parse_url($url, PHP_URL_PATH);
		$filename = basename($parts);
		echo "File: $filename \n";

        //save notification file.
        $response = file_get_contents($url);
        $basePath = dirname(__FILE__).'/storage/app/public/base.csv';
        $result = file_put_contents($basePath, $response);

        $response = file_get_contents($basePath);

		if (strpos($filename, "InventoryByPart") !== false) {
			$filename = "current_inventory.csv";
            saveReport($notification->id, $filename, $sessionToken, $response);
            updateCurrentInventory();
		}
        else if (strpos($filename, "OrderDashboard") !== false) 
        {
	        $date = "today";

            $reportDate = getReportDate($basePath, 6, 6);
            $currentDate = "Issued Date Range: ".date('m/j/Y')." - ".date('m/j/Y');
            $date = ($reportDate == $currentDate) ? "today" : "previous";

    		$filename = "sales_order_".$date.".csv";
            saveReport($notification->id, $filename, $sessionToken, $response);
		}
	}

	return $result;
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
echo "Getting Notification List \n";
getNotifications($sessionToken);
echo "Done  \n";
