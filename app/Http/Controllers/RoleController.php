<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $title = 'Role Management';
        return view('role.index', compact('title'));
    }

    public function get_table()
    {
        $roles = Role::all();
        $no = 0;
        return view('role.table', compact('roles', 'no'));
    }

    public function store(Request $request)
    {
        $id = $request->input('id');

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name' . ($id ? ',' . $id : ''),
        ]);

        if ($id) {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->input('name'),
            ]);

            return response()->json('updated');
        }

        Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);

        return response()->json('created');
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function destroy(string $id)
    {
        Role::where('id', $id)->delete();
        return response()->json('deleted');
    }
}
