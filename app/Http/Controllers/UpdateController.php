<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Parts;


class UpdateController extends Controller
{
    //

    public function importData() {
    	set_time_limit(0);
		ini_set('max_execution_time', 0);

        $csv = [];
        //SalesQtyByMonth.csv
        $fileName = "current_inventory.csv";

        if (file_exists(storage_path().'/app/public/'.$fileName)) {
            $csv = array_map('str_getcsv', file(storage_path().'/app/public/'.$fileName));
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

                    $part = Parts::create($data);
                }

            }
        }

        return response()->json(['status' => 'imported']);
    }

    public function currentInventory() 
    {
    	set_time_limit(0);
		ini_set('max_execution_time', 0);

        /*
        $report_name = "Inventory By Part";
        $report = $this->locateRequest('GET', '/report?name='.urlencode($report_name), $this->sessionToken);
        $report_id = $report->data[0]->id;
        */

        $report_id = 645;

        $response = $this->locateRequest('GET', "/report/".$report_id."/run", $this->sessionToken, array(
            // 'includeoutofstockparts' => '1',
            'orderby' => 1,
            'availableforsale' => 1,
            'bomqtydeconstructable' => 0,
            'bomquantitybuildable' => 0,
            'costingvalue' => 1,
            'formatassinglepage' => 0,
            'lastcostatsite' => 0,
            'lmbundlequantitybuildable' => 0,
            'lmbundlequantitydeconstructable' => 0,
            'partdescription' => 0,
            'partname' => 1,
            'partnumber' => 1,
            'purchasevalue' => 0,
            'qtyallocated' => 1,
            'qtycommitted' => 1,
            'qtyininventory' => 1,
            'qtyonorder' => 1,
            'uom' => 1,
            'format'=> 'csv',
        ));

        $result = file_put_contents(storage_path().'/app/public/current_inventory.csv', $response);

        if ($result) {
            return response()->json(['status' => 'updated']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function updateUnissued ()
    {
    	set_time_limit(0);
		ini_set('max_execution_time', 0);
	
	    $fileName = "purchase_order_summary.csv";

	    if (file_exists(storage_path().'/app/public/'.$fileName)) {
	        $csv = array_map('str_getcsv', file(storage_path().'/app/public/'.$fileName));
	    }
	    // dd($csv);
	    $result = false;

	    $missing = [];

	    foreach ($csv as $line) {
	    	$sku = null;
	    	$unissued = null;

	    	if (count($line) == 16 && $line[1] != "" && $line[0] != "  " && $line[0] !="Purchase Order #" && $line[1] != "Part Number" && $line[5] !="Unissued") {
	    		// echo "$line[0] - $line[1] - $line[7] \n";
	    		$sku = $line[1];
	    		$unissued = intval(str_replace(',', '', $line[7]));

	    	} else if (count($line) == 14 && $line[1] != "" && $line[0] != "  " && $line[0] !="Purchase Order #" && $line[1] != "Part Number") {
	    		$sku = $line[1];
	    		$unissued = intval(str_replace(',', '', $line[5]));
	    	}
	    	if ($unissued && $sku) {
		    	$parts = Parts::where('sku','LIKE','%'.$sku.'%')->get();
		    	if (count($parts)) {
		    		foreach ($parts as $part) { 
			    		$part->unissued = "$unissued";
			    		$part->save();
					    $result = true;
					}
		    	} else {
		    		$missing[] = $sku;
		    	}
	    	}
	    }

        if ($result) {
            return response()->json(['status' => 'updated', 'missing' => $missing]);
        } else {
            return response()->json(['status' => 'error', 'missing' => $missing]);
        }
    }

    public function purchaseOrderSummary ()
    {
    	set_time_limit(0);
		ini_set('max_execution_time', 0);


        $report_id = 646;

        $response = $this->locateRequest('GET', "/report/".$report_id."/run", $this->sessionToken, array(
            'childlines' => 0,
	        'orderby' => 'apppurchaseorder.number',
	        'datefilter' => 'apppurchaseorder.created_at',
	        'formatassinglepage' => 0,
	        'pocarriername' => 0,
	        'pocarrierservicelevel' => 0,
	        'podatecompleted' => 1,
	        'poexpecteddelivery' => 0,
	        'poissuedate' => 1,
	        'pomemo' => 0,
	        'ponumber' => 1,
	        'poorderfobpoint' => 0,
	        'popaymentterms' => 1,
	        'popriority' => 0,
	        'poremittoaddress' => 0,
	        'poscheduledfulfillment' => 0,
	        'poshiptoaddress' => 0,
	        'poshippingterms' => 0,
	        'postatus' => 1,
	        'potax' => 0,
	        'pototalcost' => 1,
	        'povendorname' => 1,
	        'povendorsonumber' => 0,
	        'lineitemdatescheduled' => 1,
	        'lineitemdiscounts' => 0,
	        'lineitemduedate' => 0,
	        'lineitemmemo' => 0,
	        'lineitemnumber' => 1,
	        'lineitempartdescription' => 0,
	        'lineitempartname' => 1,
	        'lineitempartnumber' => 1,
	        'lineitemqtyfulfilled' => 1,
	        'lineitemqtyordered' => 1,
	        'lineitemqtyremaining' => 0,
	        'lineitemtotalcost' => 1,
	        'lineitemunitcost' => 1,
	        'lineitemuom' => 1,
	        'daterange' => 'All Time',
	        'daterange2' => 'All Time',
	        'purchaseorderstatus' => [25],
            'format'=> 'csv',
        ));

        $result = file_put_contents(storage_path().'/app/public/purchase_order_summary.csv', $response);

        if ($result) {
            return response()->json(['status' => 'updated']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

	public function updateSalesMonth() 
	{
    	set_time_limit(0);
		ini_set('max_execution_time', 0);

	    $fileName = "finished_data.csv";

	    if (file_exists(storage_path().'/app/public/'.$fileName)) {
	        $csv = array_map('str_getcsv', file(storage_path().'/app/public/'.$fileName));
	    }

	    $missing = [];
	    // echo "updateSalesMonth \n\r";

	    foreach ($csv as $line) {
	        if (!empty($line[0])) {
	            $parts = Parts::where('sku','LIKE','%'.$line[0].'%')->get();
	            if (count($parts)) {
	                foreach ($parts as $part) {
	                    $part->ave = str_replace(',', '', $line[3]);
	                    $part->max = str_replace(',', '', $line[4]);
	                    $part->jan = str_replace(',', '', $line[5]);
	                    $part->feb = str_replace(',', '', $line[6]);
	                    $part->mar = str_replace(',', '', $line[7]);
	                    $part->apr = str_replace(',', '', $line[8]);
	                    $part->may = str_replace(',', '', $line[9]);
	                    $part->jun = str_replace(',', '', $line[10]);
	                    $part->jul = str_replace(',', '', $line[11]);
	                    $part->aug = str_replace(',', '', $line[12]);
	                    $part->sept = str_replace(',', '', $line[13]);
	                    $part->oct = str_replace(',', '', $line[14]);
	                    $part->nov = str_replace(',', '', $line[15]);
	                    $part->dec = str_replace(',', '', $line[16]);
	                    $part->save();
	                    // echo ".";
	                }
	            } else {
	                $missing[] = $line[0];
	            }
	        }
	    }

	    return response()->json(['status' => 'updated', 'missing' => $missing]);
	}         
}
