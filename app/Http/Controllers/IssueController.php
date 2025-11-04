<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    //
    public function index(Request $request)
    {

        return inertia('Backend/Issue/Index',[
        ]);
    }

    public function jsonAll(Request $request)
    {

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => Issue::query()
                ->with(['cell'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }
}
