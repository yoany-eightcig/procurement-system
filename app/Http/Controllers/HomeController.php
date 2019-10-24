<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Parts;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $items_peer_page = 100;

    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function searchData( $report_id, Request $request) 
    {
        // DB table to use
        $table = 'parts';

        // Table's primary key
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case object
        // parameter names
        $fields = [
            "sku",
            "name",
            "quantity",
            "unissued",
            "on_order",
            "suggest_qty",
            "case_lot",
            "ave",
            "max",
            "jan",
            "feb",
            "mar",
            "apr",
            "may",
            "jun",
            "jul",
            "aug",
            "sept",
            /*
            "oct",
            "nov",
            "dec",
            */
            "vendor",
        ];

        $columns = [];
        for ($i = 0; $i < count($fields); $i++) {
            $columns[] = array( 'db' => $fields[$i],       'dt' => $i+1 );
        }

        /*
        $columns = array(
            array( 'db' => 'sku',       'dt' => 1 ),
            array( 'db' => 'name',      'dt' => 2 ),
            array( 'db' => 'quantity',  'dt' => 3 ),
            array( 'db' => 'unissued',  'dt' => 4),
            array( 'db' => 'on_order',  'dt' => 5),
            array( 'db' => 'ave',       'dt' => 6),
            array( 'db' => 'max',       'dt' => 7),
            array( 'db' => 'dec',       'dt' => 8),
            array( 'db' => 'jan',       'dt' => 9),
            array( 'db' => 'feb',       'dt' => 10),
            array( 'db' => 'mar',       'dt' => 11),
            array( 'db' => 'apr',       'dt' => 12),
            array( 'db' => 'may',       'dt' => 13),
            array( 'db' => 'jun',       'dt' => 14),
            array( 'db' => 'jul',       'dt' => 15),
            array( 'db' => 'aug',       'dt' => 16),
            array( 'db' => 'sept',      'dt' => 17),
            array( 'db' => 'oct',       'dt' => 18),
            array( 'db' => 'nov',       'dt' => 19),
        );
        */

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME', 'forge'),
            'pass' => env('DB_PASSWORD', ''),
            'db'   => env('DB_DATABASE', 'forge'),
            'host' => env('DB_HOST', '127.0.0.1')
        );

        return response()->json(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $report_id));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');

        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    
        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && count($sku_array) == 1) {
            $parts = DB::table('parts')->where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if ($filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('home', ['parts' => $parts]);
    }

    public function monthlySales()
    {
        $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->paginate($this->items_peer_page);    
        return view('monthlysales', ['parts' => $parts]);
    }

    public function monthlySalesSearch(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');

        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    
        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity+unissued+on_order) <= ave')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if ($filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('monthlysales', ['parts' => $parts]);
    }

    public function weeklySales()
    {
        $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->paginate($this->items_peer_page);    
        return view('weeklysales', ['parts' => $parts]);
    }

    public function weeklySalesSearch(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');

        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    
        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity+unissued+on_order) <= (ave/4)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if ($filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('weeklysales', ['parts' => $parts]);
    }

    public function clearance3()
    {
        $parts = DB::table('parts')->whereRaw('quantity > (ave*3)')->paginate($this->items_peer_page);    
        return view('clearance', ['parts' => $parts, 'action' => 'clearance3Search']);
    }

    public function clearance3Search(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');
        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    

        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity >= ave*3)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*3)')->Where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*3)')->Where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*3)')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('clearance', ['parts' => $parts, 'action' => 'clearance3Search']);
    }

    public function clearance6()
    {
        $parts = DB::table('parts')->whereRaw('quantity > (ave*6)')->paginate($this->items_peer_page);    
        return view('clearance', ['parts' => $parts, 'action' => 'clearance6Search']);
    }

    public function clearance6Search(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');
        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    

        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity >= ave*6)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*6)')->Where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*6)')->Where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity >= ave*6)')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('clearance', ['parts' => $parts, 'action' => 'clearance6Search']);
    }

    public function zerosales()
    {
        $parts = DB::table('parts')->Where('ave','=', 0)->paginate($this->items_peer_page);
        return view('zerosales', ['parts' => $parts, 'action' => 'zerosalesSearch']);
    }

    public function zerosalesSearch(Request $request)
    {
        $search = $request->input('search');
        $filter_name = $request->input('filter_name');
        $filter_sku = $request->input('filter_sku');
        $sku_array = [];
        if ($filter_sku) {
            $sku_array = explode(",", str_replace(' ', '', $search));    
        }

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(ave = 0)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->paginate($this->items_peer_page);
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->Where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->Where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->whereRaw($query)->paginate($this->items_peer_page);
        }

        return view('zerosales', ['parts' => $parts, 'action' => 'zerosalesSearch']);
    }

    public function getMonthlySalesData($search, $filter_name, $filter_sku, $sku_array)
    {
        $parts = [];

        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity+unissued+on_order) <= ave')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->get();
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->Where('name','LIKE','%'.$search.'%')->get();
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->Where('sku','LIKE','%'.$search.'%')->get();
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->whereRaw($query)->get();
        }

        return $parts;
    }    

    public function getWeeklySalesData($search, $filter_name, $filter_sku, $sku_array)
    {
        $parts = [];
        
        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity+unissued+on_order) <= (ave/4)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->get();
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->Where('name','LIKE','%'.$search.'%')->get();
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->Where('sku','LIKE','%'.$search.'%')->get();
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->whereRaw($query)->get();
        }

        return $parts;
    }    

    public function getZeroSalesData($search, $filter_name, $filter_sku, $sku_array)
    {
        $parts = [];
        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(ave = 0)')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->get();
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->Where('name','LIKE','%'.$search.'%')->get();
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->Where('sku','LIKE','%'.$search.'%')->get();
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(ave = 0)')->whereRaw($query)->get();
        }

        return $parts;

    }

    public function getClearanceData ($months, $search, $filter_name, $filter_sku, $sku_array)
    {
        if ($filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')
                ->whereRaw('(quantity >= ave * '.$months.')')
                ->whereRaw('(name LIKE "%'.$search.'%" OR sku LIKE "%'.$search.'%")')
                ->get();
        } else if ($filter_name && !$filter_sku) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave * '.$months.')')->Where('name','LIKE','%'.$search.'%')->get();
        } else if (!$filter_name && $filter_sku && count($sku_array) == 1) {
            $parts = DB::table('parts')->whereRaw('(quantity >= ave * '.$months.')')->Where('sku','LIKE','%'.$search.'%')->get();
        } elseif ($filter_sku && count($sku_array) > 1) {
            $query = "sku IN (";
            foreach ($sku_array as $value) {
                $query .= "'$value',";
            }
            $query = rtrim($query, ',');
            $query .= ")";
            $parts = DB::table('parts')->whereRaw('(quantity >= ave * '.$months.')')->whereRaw($query)->get();
        }

        return $parts;
    }

    public function exportToExcel ($export, Request $request)
    {
        $filename = $export;
        $exportOptions = explode(';', $request->input('exportOptions') );
        $search = "";
        $filter_name = null;
        $filter_sku = null;
        $sku_array = [];

        if (count($exportOptions) > 1) {
            $search = explode(':', $exportOptions[0])[1];
            $filter_name = explode(':', $exportOptions[1])[1];
            $filter_sku = explode(':', $exportOptions[2])[1];

            if ($filter_sku) {
                $sku_array = explode(",", str_replace(' ', '', $search));    
            }
        }

        if ($export == 'zerosales') {
            if (count($exportOptions) > 1) {
                $parts = $this->getZeroSalesData($search, $filter_name, $filter_sku, $sku_array);
            } else {
                $parts = DB::table('parts')->Where('ave','=', 0)->get();
            }
        }

        if ($export == 'clearance3Search' || $export == 'clearance6Search') {
            $months = ($export == 'clearance3Search') ? 3 : 6;
            $filename = ($export == 'clearance3Search') ? 'clearance_months_3' : 'clearance_months_6';
            if (count($exportOptions) > 1) {
                $parts = $this->getClearanceData ($months, $search, $filter_name, $filter_sku, $sku_array);
            } else {
                $parts = DB::table('parts')->whereRaw('(quantity >= ave * '.$months.')')->get();
            }
        }

        if ($export == 'monthlysales') {
            if (count($exportOptions) > 1) {
                $parts = $this->getMonthlySalesData($search, $filter_name, $filter_sku, $sku_array);
            } else {
                $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= ave')->get();
            }
        }

        if ($export == 'weeklysales') {
            if (count($exportOptions) > 1) {
                $parts = $this->getWeeklySalesData($search, $filter_name, $filter_sku, $sku_array);
            } else {
                $parts = DB::table('parts')->whereRaw('(quantity+unissued+on_order) <= (ave/4)')->get();
            }
        }


        return view("export_excel", ['filename' => $filename, 'parts' => $parts]);
    }

    public function index()
    {
        $parts = DB::table('parts')->paginate($this->items_peer_page);

        return view('home', ['parts' => $parts]);
    }

}
