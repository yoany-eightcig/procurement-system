<?php 
function updateSalesMonth() 
{
	set_time_limit(0);
	ini_set('max_execution_time', 0);

    $fileName = "finished_data.csv";

    if (file_exists('storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file('storage/app/public/'.$fileName));
    }

    $missing = [];
    echo "updateSalesMonth \n\r";

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
        if (!empty($line[0])) {
        	$sku = $line[0];
            $name = $line[1];

			$sql = "SELECT * FROM parts WHERE sku='$sku'";
			$result = mysqli_query($conn, $sql);
            if (count($line) >= 17) {
                $ave = str_replace(',', '', $line[3]);
                $max = str_replace(',', '', $line[4]);
                $jan = str_replace(',', '', $line[5]);
                $feb = str_replace(',', '', $line[6]);
                $mar = str_replace(',', '', $line[7]);
                $apr = str_replace(',', '', $line[8]);
                $may = str_replace(',', '', $line[9]);
                $jun = str_replace(',', '', $line[10]);
                $jul = str_replace(',', '', $line[11]);
                $aug = str_replace(',', '', $line[12]);
                $sept = str_replace(',', '', $line[13]);
                $oct = str_replace(',', '', $line[14]);
                $nov = str_replace(',', '', $line[15]);
                $dec = str_replace(',', '', $line[16]);

                if (mysqli_num_rows($result) > 0) {

                    while($row = mysqli_fetch_assoc($result)) {
                        //
                    }
                    $sql = "UPDATE `parts` SET `ave` = '$ave', `max` = '$max', `jan` = '$jan', `feb` = '$feb', `mar` = '$mar', `apr` = '$apr', `may` = '$may', `jun` = '$jun', `jul` = '$jul', `aug` = '$aug', `sept` = '$sept', `oct` = '$oct', `nov` = '$nov', `dec` = '$dec' WHERE `sku` LIKE '$sku'";

                    if (mysqli_query($conn, $sql)) {
                        // echo "$sku ";
                    }

                } else {
                    $sql = "INSERT INTO `parts` (`id`, `sku`, `name`, `quantity`, `ave`, `max`, `jan`, `feb`, `mar`, `apr`, `may`, `jun`, `jul`, `aug`, `sept`, `oct`, `nov`, `dec`, `unissued`, `on_order`, `created_at`, `updated_at`) VALUES (NULL, '$sku', '$name', '1', $ave, $max, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sept, $oct, $nov, $dec, '0', '0', CURRENT_TIME(), CURRENT_TIME());";

                    $missing[] = $line[0];
                    if (mysqli_query($conn, $sql)) {
                        echo "$sku ";
                    }

                }                
            } else {
                echo "$sku ";                
            }

        }
    }

    echo "updated \n\r";
} 

updateSalesMonth();

?>