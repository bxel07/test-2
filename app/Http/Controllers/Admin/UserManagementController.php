<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view("admin.userManagement", compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validate =  $request->validate([
                'npwp' => 'required|string|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:255',
                'status' => 'required|in:user,admin',
            ]);

            User::create([
                'npwp' => $validate['npwp'],
                'password' => Hash::make($validate['password']),
                'nama' => $validate['nama'],
                'alamat' => $validate['alamat'],
                'status' => $validate['status'],
            ]);

            return redirect()->back()->with('success', 'User added successfully!');

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'An error occurred while adding the user. Please try again.']);
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
    public function edit($id)
    {
        $user = User::findOrFail($id)->first();
        return view('admin.editPage', compact('user'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
               
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'npwp' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'status' => 'required|in:user,admin',
        ]);

        $user->update($validatedData);
        return redirect()->route('user-management.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Delete the user
            $user = User::find($id);
            $user->delete();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the user. Please try again.']);
        }
    }
}
