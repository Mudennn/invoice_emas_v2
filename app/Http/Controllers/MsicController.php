<?php

namespace App\Http\Controllers;

use App\Http\Requests\MsicFormRequest;
use App\Models\Msic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;

class MsicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $msics = Msic::select(
            'msics.*'
        )
            ->where('msics.status', '0')
            ->orderBy('msics.msic_code', 'asc')
            ->get();

        return view('msics.index', compact('msics'));
    }

    public function view($id)
    {
        $msic = Msic::findOrFail($id);

        $ro = 'readonly';

        return view('msics.view', compact('msic',  'ro'));
    }

    public function create()
    {
        $msic = new Msic();

        $ro = '';
        
        return view('msics.create', compact('msic', 'ro'));
    }


    public function store(MsicFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $msic = Msic::create([
                'msic_code'  => $request->msic_code,
                'description'  => $request->description,
                'category_reference'  => $request->category_reference,
                'status'  => '0',
                'created_by' => $user->id
            ]);


            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'MSIC created successfully',
                    'redirect' => route('msics.index'),
                ]);
            }

            return redirect()->route('msics.index')->with('success', 'MSIC created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => ['error' => [$e->getMessage()]]
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $msic = Msic::findOrFail($id);

        $ro = '';

        return view('msics.edit', compact('msic', 'ro'));
    }

    public function update(MsicFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $msic = Msic::findOrFail($id);

            $msic->update([
                'msic_code'  => $request->msic_code,
                'description'  => $request->description,
                'category_reference'  => $request->category_reference,
                'updated_by' => $user->id
            ]);


            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'MSIC updated successfully',
                    'redirect' => route('msics.index'),
                ]);
            }

            return redirect()->route('msics.index')->with('success', 'MSIC updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => ['error' => [$e->getMessage()]]
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $msic = Msic::findOrFail($id);

        $ro = '';

        return view('msics.show', compact('msic', 'ro'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $msic = Msic::findOrFail($id);


        $msic->update([
            'updated_by' => $user->id,
            'status' => '1'
        ]);

        return redirect()->route('msics.index')->with('success', 'MSIC deleted successfully.');
    }
}
