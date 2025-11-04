<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    //
    public function index(Request $request)
    {

        return inertia('Backend/Receipt/Index',[
        ]);
    }

    public function jsonAll(Request $request)
    {

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => Receipt::query()
                ->with(['cell'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }
}
