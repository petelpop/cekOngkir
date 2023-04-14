<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use App\Models\Courier;
use Illuminate\Http\Request;
use Kavist\RajaOngkir\Facades\RajaOngkir;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //return view('home');
        $province = $this->getProvince();
        $courier = $this->getCourier();
        return view('home', compact('province', 'courier'));
    }

//     public function store(Request $request){
//         // dd($request->all());
//     $daftarProvinsi = RajaOngkir::ongkosKirim([
//     'origin'        => $request -> origin,     // ID kota/kabupaten asal
//     'destination'   => $request -> destination,      // ID kota/kabupaten tujuan
//     'weight'        => 1300,    // berat barang dalam gram
//     'courier'       => $request -> courier[0]    // kode kurir pengiriman: ['jne', 'tiki', 'pos'] untuk starter
// ])->get();

// dd($cost);
//     }

    public function store(Request $request){
        // dd($request->all());

        $courier = $request->input('courier');

        if ($courier) {
            $result = [];

            foreach ($courier as $value) {
            $cost = RajaOngkir::ongkosKirim([
                    'origin'        => $request -> city_origin,     // ID kota/kabupaten asal
                    'destination'   => $request -> destination,      // ID kota/kabupaten tujuan
                    'weight'        => 1300,    // berat barang dalam gram
                    'courier'       => $value   // kode kurir pengiriman: ['jne', 'tiki', 'pos'] untuk starter
                ])->get(); 

                $result[] = $cost;

            }

            return $result;
        }

    }

    public function getCourier(){
        return Courier::all();
    }

    public function getProvince(){
        return Province::pluck('title', 'code');
    }

    public function getCities($id){
        return City::where('province_code', $id)->pluck('title', 'code');
    }

    public function searchCities(Request $request){
        $search = $request->search;

        if (empty($search)) {
            $cities = City::orderBy('title', 'asc')
            ->select('id', 'title')->limit(5)->get();
        } else {
            $cities = City::orderBy('title', 'asc')
            ->where('title', 'like', '%'.$search.'%')
            ->select('id', 'title')->limit(5)->get();
        }

        $response = [];

        foreach ($cities as $city) {
            $response[] = [
                'id' => $city->id,
                'text' => $city->title
            ];
        }

       return json_encode($response); 

    }

}