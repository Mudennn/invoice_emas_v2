<?php

namespace App\Http\Controllers;
use App\Http\Requests\CategoryProductFormRequest;
use App\Models\CategoryProduct;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $category_products = CategoryProduct::select(
			'category_products.*'
		)
		->where('category_products.status', '0')
		->get();
	
        return view('category_products.index', compact('category_products'));
    }
    
    public function view($id)
    {
        $category_product = CategoryProduct::findOrFail($id);
    	 
        $ro = 'readonly'; 
        
        return view('category_products.view', compact('category_product',  'ro'));
    }
    
    public function create()
    {        
        $category_product = new CategoryProduct();
    	 
        $ro = ''; 
        
        return view('category_products.create', compact('category_product', 'ro'));
    }
    
    public function store(CategoryProductFormRequest $request)
    {
        $user = Auth::user();
        
        $category_product = CategoryProduct::create([  
                 'name'  => request('name'),
                 'created_by' => $user->id
            ]);
    

        return redirect()->route('category_products.index')
            ->with('success', 'Category Product created successfully.');
    }
    
    public function edit($id)
    {
        $category_product = CategoryProduct::findOrFail($id);
    	 
        $ro = ''; 
        
        return view('category_products.edit', compact('category_product', 'ro'));
    }
    
    public function update(CategoryProductFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $category_product = CategoryProduct::where('id', $id)
            ->update([
                 'name'  => request('name'),
                 'updated_by' => $user->id
            ]);
    

        return redirect()->route('category_products.index')
            ->with('success', 'Category Product updated successfully.');    
    }
    
    public function show($id)
    {
        $category_product = CategoryProduct::findOrFail($id);
    	 
        $ro = ''; 
              
        return view('category_products.show', compact('category_product', 'ro'));   
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        CategoryProduct::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('category_products.index')
            ->with('success', 'Category Product deleted successfully.');   
    }
}
