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

    public $items_peer_page = 20;

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

        // dd($search, $filter_name, $filter_sku);

        if ($filter_name && $filter_sku) {
            $parts = DB::table('parts')
                ->where('name', 'LIKE', "%$search%")
                ->orWhere('sku','LIKE','%'.$search.'%')
                ->paginate($this->items_peer_page);
        } else if ($filter_name) {
            $parts = DB::table('parts')->where('name','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        } else if ($filter_sku) {
            $parts = DB::table('parts')->where('sku','LIKE','%'.$search.'%')->paginate($this->items_peer_page);
        }



        return view('home', ['parts' => $parts]);
    }

    public function index()
    {
        $parts = DB::table('parts')->paginate($this->items_peer_page);

        return view('home', ['parts' => $parts]);
    }

}
