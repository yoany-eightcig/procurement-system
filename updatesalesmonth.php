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
	$password = "copxerkiller";
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

			$sql = "SELECT * FROM Parts WHERE sku='$sku'";
			$result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {

				while($row = mysqli_fetch_assoc($result)) {
					//
				}
				
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

				$sql = "UPDATE `parts` SET `ave` = '$ave', `max` = '$max', `jan` = '$jan', `feb` = '$feb', `mar` = '$mar', `apr` = '$apr', `may` = '$may', `jun` = '$jun', `jul` = '$jul', `aug` = '$aug', `sept` = '$sept', `oct` = '$oct', `nov` = '$nov', `dec` = '$dec' WHERE `sku` LIKE '$sku'";

				if (mysqli_query($conn, $sql)) {
					echo "$sku ";
				}

            } else {
                $missing[] = $line[0];
            }
        }
    }

    echo "updated \n\r";
} 

updateSalesMonth();

?>