<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users-management', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => ['required','string','email','max:255','unique:users','ends_with:@casatapier.com'],
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.ends_with' => 'Solo se permiten correos con el dominio @casatapier.com.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Si dejas la contraseña en blanco, se genera una aleatoria. 
        // Así el usuario usará "Olvidé mi contraseña" para elegir la suya.
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        } else {
            $user->password = Hash::make(\Illuminate\Support\Str::random(32));
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->route('users.index')->with('mensaje', 'Usuario creado correctamente.');
    }
}