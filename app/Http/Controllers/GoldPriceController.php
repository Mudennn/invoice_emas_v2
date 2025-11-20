<?php

namespace App\Http\Controllers;
use App\Http\Requests\GoldPriceFormRequest;
use App\Models\GoldPrice;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class GoldPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $gold_prices = GoldPrice::select(
			'gold_prices.*'
		)
		->where('gold_prices.status', '0')
		->get();
	
        return view('gold_prices.index', compact('gold_prices'));
    }
    
    public function view($id)
    {
        $gold_price = GoldPrice::findOrFail($id);
    	 
        $ro = 'readonly'; 
        
        return view('gold_prices.view', compact('gold_price',  'ro'));
    }
    
    public function create()
    {        
        $gold_price = new GoldPrice();
    	 
        $ro = ''; 
        
        return view('gold_prices.create', compact('gold_price', 'ro'));
    }
    
    public function store(GoldPriceFormRequest $request)
    {
        $user = Auth::user();
        
        $gold_price = GoldPrice::create([  
                 'name'  => request('name'),
                 'price'  => request('price'),
                 'date_change'  => request('date_change'),
                 'created_by' => $user->id
            ]);
    

        return redirect()->route('gold_prices.index')
            ->with('success', 'Gold Price created successfully.');    
    }
    
    public function edit($id)
    {
        $gold_price = GoldPrice::findOrFail($id);
    	 
        $ro = ''; 
        
        return view('gold_prices.edit', compact('gold_price', 'ro'));
    }
    
    public function update(GoldPriceFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $gold_price = GoldPrice::where('id', $id)
            ->update([
                 'name'  => request('name'),
                 'price'  => request('price'),
                 'date_change'  => request('date_change'),
                 'updated_by' => $user->id
            ]);
    

        return redirect()->route('gold_prices.index')
            ->with('success', 'Gold Price updated successfully.');    
    }
    
    public function show($id)
    {
        $gold_price = GoldPrice::findOrFail($id);
    	 
        $ro = ''; 
              
        return view('gold_prices.show', compact('gold_price', 'ro'));
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        GoldPrice::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('gold_prices.index')
            ->with('success', 'Gold Price deleted successfully.');   
    }
    
}
