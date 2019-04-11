<?php 
function updateSalesMonth() 
{
	set_time_limit(0);
	ini_set('max_execution_time', 0);

    $fileName = "current_inventory.csv";

    if (file_exists('storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file('storage/app/public/'.$fileName));
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
        if ( (count($line) == 12 && $line[0]!= '' && $line[0]!= 'North' && $line[2]!='Part Name') || (count($line) == 10 && !empty($line[0])) ) {
            $data = [];
            if (count($line) == 12) {
                $data = [
                    'sku' => $line[0],
                    'name' => $line[2],
                    'on_order' => $line[9],
                    'quantity' => $line[10],
                ];
            } if ((count($line) == 10) && $line[1] != 'Available for Sale') {
                $data = [
                    'sku' => $line[0],
                    'name' => $line[1],
                    'on_order' => $line[7],
                    'quantity' => $line[8],
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

                if (mysqli_query($conn, $sql)) {
                    echo $sql."\n";
                    echo $data['sku']." ";
                }                
            }

        }
    }

    echo "updated \n\r";
} 

updateSalesMonth();

?>