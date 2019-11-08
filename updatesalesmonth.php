<?php 
function updateSalesMonth() 
{
	set_time_limit(0);
	ini_set('max_execution_time', 0);

    $fileName = "finished_data.csv";

    if (file_exists(dirname(__FILE__).'/storage/app/public/'.$fileName)) {
        $csv = array_map('str_getcsv', file(dirname(__FILE__).'/storage/app/public/'.$fileName));
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

			$sql = "SELECT * FROM `parts` WHERE `sku` LIKE '$sku'";
			$result = mysqli_query($conn, $sql);
            if (count($line) == 18) {
                $ave = str_replace(',', '', $line[2]);
                $ave = $ave == "" ? 0 : $ave; 
                $max = str_replace(',', '', $line[3]);
                $max = $max == "" ? 0 : $max;
                $jan = str_replace(',', '', $line[4]);
                $jan = $jan == "" ? 0 : $jan;
                $feb = str_replace(',', '', $line[5]);
                $feb = $feb == "" ? 0 : $feb;
                $mar = str_replace(',', '', $line[6]);
                $mar = $mar == "" ? 0 : $mar;
                $apr = str_replace(',', '', $line[7]);
                $apr = $apr == "" ? 0 : $apr;
                $may = str_replace(',', '', $line[8]);
                $may = $may == "" ? 0 : $may;
                $jun = str_replace(',', '', $line[9]);
                $jun = $jun == "" ? 0 : $jun;
                $jul = str_replace(',', '', $line[10]);
                $jul = $jul == "" ? 0 : $jul;
                $aug = str_replace(',', '', $line[11]);
                $aug = $aug == "" ? 0 : $aug;
                $sept = str_replace(',', '', $line[12]);
                $sept = $sept == "" ? 0 : $sept;
                $oct = str_replace(',', '', $line[13]);
                $oct = $oct == "" ? 0 : $oct;
                $nov = str_replace(',', '', $line[14]);
                $nov = $nov == "" ? 0 : $nov;
                $dec = str_replace(',', '', $line[15]);
                $dec = $dec == "" ? 0 : $dec;
                $vendor = $line[16];
                $case_lot = str_replace(',', '', $line[17]);
                $case_lot = $case_lot == "" ? 0 : $case_lot;

                echo $sku." ";

                if (mysqli_num_rows($result) > 0) {

                    $sql = "UPDATE `parts` SET `ave` = '$ave', `max` = '$max', `jan` = '$jan', `feb` = '$feb', `mar` = '$mar', `apr` = '$apr', `may` = '$may', `jun` = '$jun', `jul` = '$jul', `aug` = '$aug', `sept` = '$sept', `oct` = '$oct', `nov` = '$nov', `dec` = '$dec', `vendor` = \"$vendor\", `case_lot` = '$case_lot', `suggest_qty` = (`ave`-`quantity`-`unissued`-`on_order`) WHERE `sku` LIKE '$sku'";

                    if (mysqli_query($conn, $sql)) {
                        echo "UPDATE ";
                    }

                } else {
                    $sql = "INSERT INTO `parts` (`id`, `sku`, `name`, `quantity`, `ave`, `max`, `jan`, `feb`, `mar`, `apr`, `may`, `jun`, `jul`, `aug`, `sept`, `oct`, `nov`, `dec`, `unissued`, `on_order`, `created_at`, `updated_at`, `vendor`, `case_lot`, `suggest_qty`) VALUES (NULL, \"$sku\", \"$name\", '0', $ave, $max, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sept, $oct, $nov, $dec, '0', '0', CURRENT_TIME(), CURRENT_TIME(), \"$vendor\", $case_lot, (`ave`-`quantity`-`unissued`-`on_order`));";

                    if (mysqli_query($conn, $sql)) {
                        echo "NEW ";
                    } else {
                        echo "ERROR \n";
                        echo $sql. "\n";
                        exit;
                    }
                }
               
                echo "[".count($line)."] \n";
            } else {
                echo "ERROR: [".count($line)."] $sku \n";
            }

        }
    }

    $sql = "UPDATE `parts` SET `ave`=(`jan`+`feb`+`mar`+`apr`+`may`+`jun`+`jul`+`aug`+`sept`+`oct`) / 10 WHERE 1";
    if (mysqli_query($conn, $sql)) {
        echo "[ave] updated \n\r";
    }
    
    $sql = "UPDATE parts SET suggest_qty = CASE WHEN ((ave-quantity-unissued-on_order) / case_lot) > 0 THEN case_lot ELSE 0 END WHERE case_lot > 0";

    if (mysqli_query($conn, $sql)) {
        echo "[suggest_qty] updated \n\r";
    }

    echo "updated \n\r";
} 

updateSalesMonth();

?>