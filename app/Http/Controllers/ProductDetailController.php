<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProductDetailFormRequest;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoryProduct;

use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $product_details = ProductDetail::select(
            'product_details.*',
            'category_products.name as category_name'
        )
        ->leftJoin('category_products', 'product_details.category', '=', 'category_products.id')
        ->where('product_details.status', '0')
        ->get();

        return view('product_details.index', compact('product_details'));
    }
    
    public function view($id)
    {
        $product_detail = ProductDetail::findOrFail($id);
        $categories = CategoryProduct::where('status', '0')->get();
        $ro = 'readonly'; 
        
        return view('product_details.view', compact('product_detail', 'categories', 'ro'));
    }
    
    public function create()
    {        
        $product_detail = new ProductDetail();
        $categories = CategoryProduct::where('status', '0')->get();
        $ro = ''; 
        
        return view('product_details.create', compact('product_detail', 'categories', 'ro'));
    }

    public function store(ProductDetailFormRequest $request)
    {
        $user = Auth::user();
        
        // Add WO- prefix to the code
        $code = 'WO-' . $request->code;
        
        $product_detail = ProductDetail::create([  
                 'name'  => $request->name,
                 'code'  => $code,
                 'category'  => $request->category,
                 'created_by' => $user->id
            ]);
    
        return redirect()->route('product_details.index')
            ->with('success', 'Product Detail created successfully.');    
    }
    
    public function edit($id)
    {
        $product_detail = ProductDetail::findOrFail($id);
        // Remove WO- prefix for display
        $product_detail->code = substr($product_detail->code, 3);
        $categories = CategoryProduct::where('status', '0')->get();
        $ro = ''; 
        
        return view('product_details.edit', compact('product_detail', 'categories', 'ro'));
    }
    
    public function update(ProductDetailFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $product_detail = ProductDetail::where('id', $id)
            ->update([
                 'name'  => $request->name,
                 'code'  => 'WO-' . $request->code,
                 'category'  => $request->category,
                 'updated_by' => $user->id
            ]);
    
        return redirect()->route('product_details.index')
            ->with('success', 'Product Detail updated successfully.');    
    }
    
    public function show($id)
    {
        $product_detail = ProductDetail::findOrFail($id);
    	$categories = CategoryProduct::where('status', '0')->get(); 
        $ro = ''; 
              
        return view('product_details.show', compact('product_detail', 'categories', 'ro'));
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        ProductDetail::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('product_details.index')
            ->with('success', 'Product Detail deleted successfully.');   
    }
    
}
