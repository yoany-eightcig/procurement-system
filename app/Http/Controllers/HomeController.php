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

    public $items_peer_page = 40;

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
                ->where('name', 'LIKE', "%$search%")
                ->orWhere('sku','LIKE','%'.$search.'%')
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
                ->where('name', 'LIKE', "%$search%")
                ->orWhere('sku','LIKE','%'.$search.'%')
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
            $parts = DB::table('parts')->whereRaw($query)->paginate($this->items_peer_page);
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
                ->where('name', 'LIKE', "%$search%")
                ->orWhere('sku','LIKE','%'.$search.'%')
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
            $parts = DB::table('parts')->whereRaw($query)->paginate($this->items_peer_page);
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

    public function index()
    {
        $parts = DB::table('parts')->paginate($this->items_peer_page);

        return view('home', ['parts' => $parts]);
    }

}
