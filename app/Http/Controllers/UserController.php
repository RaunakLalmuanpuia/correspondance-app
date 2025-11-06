<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-user'),403,'Access Denied');

        return inertia('Backend/User/Index',[
        ]);
    }

    public function jsonAll(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-user'),403,'Access Denied');


        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => User::query()
                ->with(['roles'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }

    public function create(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-user'),403,'Access Denied');


        return inertia('Backend/User/Create',[
            'userRoles'=>Role::query()->get(['name as value','name as label'])
        ]);
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-user'),403,'Access Denied');


        $data=$this->validate($request, [
            'name'=>'required',
            'designation'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed'
        ]);
        $roles = $request->get('roles');
        $mergedData = array_merge($data, ['password' => Hash::make($data["password"])]);
        DB::transaction(function () use ($roles, $request, $mergedData) {
            $user=User::query()->create($mergedData);
            if ($roles) {
                $user->assignRole($roles);
            }
        });

        return to_route('user.index');
    }

    public function edit(Request $request,User $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-user'),403,'Access Denied');


        return inertia('Backend/User/Edit', [
            'userRoles' => Role::query()->get(['name as value', 'name as label']),
            'data' => $model->load(['roles'])
        ]);

    }

    public function update(Request $request, User $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-user'),403,'Access Denied');


        $data=$this->validate($request, [
            'name'=>'required',
            'designation'=>'required',
            'email'=>['required','email',Rule::unique('users','email')->ignore($model->id)],
        ]);
        $roles = $request->get('roles');
        $password = $request->get('password');
        DB::transaction(function () use ($model, $password, $roles, $request, $data) {
            $model->update($data);
            if ($roles) {
                $model->assignRole($roles);

            }
            if ($password) {
                $model->password = Hash::make($password);
                $model->save();
            }

        });

        return to_route('user.index');
    }

    public function show(Request $request, User $model)
    {
        return inertia('Backend/User/Show', [
            'data'=>$model,
        ]);
    }

    public function destroy(Request $request,User $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-user'),403,'Access Denied');

        $model->delete();

        return to_route('user.index');
    }
}
