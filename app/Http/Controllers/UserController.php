<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Employee Management";
        $role = Role::all();
        return view('user.index',compact('title','role'));
    }
    public function get_table(){
        $user = User::all();
        $no = 0;
        $role = Role::all();
        return view('user.table',compact('user', 'no','role'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $id = $request->id;


        if($id){
            if(empty($request->password)){
                $user = User::updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                    'email' => $request->email,
                    'name' => $request->name,
                    'nik' => $request->nik,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    ]
                );
                $user->syncRoles([]);
                $user->assignRole($request->role);
            }else{
                $user = User::updateOrCreate(
                    [
                        'id' => $id
                    ],
                    [
                    'password' => Hash::make($request->password),
                    ]
                );

            }
            return response()->json('Updated');

        }else{
            $user = User::updateOrCreate(
                [
                    'id' => $id
                ],
                [
                'email' => $request->email,
                'name' => $request->name,
                'nik' => $request->nik,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                ]
            );
            $user->syncRoles([]);
            $user->assignRole($request->role);

            if($user){
                return response()->json('created');
            }else{
                return response()->json('Failed to create');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $user[0] = User::find($id);
        $user[1] = $user[0]->getRoleNames();
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = User::where('id', $id)->delete();
    }
}
