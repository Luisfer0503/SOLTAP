<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Si el usuario ya está autenticado, redirigir al dashboard/inicio
        if (Auth::check()) {
            return redirect()->route('inicio');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        // Validar credenciales
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar iniciar sesión
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirigir a la intención original o al inicio
            return redirect()->intended(route('inicio'));
        }

        // Si falla, regresar con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('inicio');
        }
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Usará Argon2 si HASH_DRIVER=argon en .env
        ]);

        Auth::login($user);

        return redirect()->route('inicio');
    }

    public function indexUsers()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('users-management', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|string|max:255',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->route('users.index')->with('mensaje', 'Usuario creado correctamente. Por favor, comparta las credenciales de forma segura.');
    }

    public function editUser(User $user)
    {
        return view('users-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            // Guardar nueva foto
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->route('users.index')->with('mensaje', 'Usuario actualizado correctamente.');
    }

    public function destroyUser(User $user)
    {
        // Prevenir que un usuario se elimine a sí mismo
        if (Auth::user()->id === $user->id) {
            return redirect()->route('users.index')->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        // Eliminar la foto de perfil si existe
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return redirect()->route('users.index')->with('mensaje', 'Usuario eliminado correctamente.');
    }
}