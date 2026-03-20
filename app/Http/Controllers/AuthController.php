<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
            'email' => ['required', 'email', 'ends_with:@casatapier.com'],
            'password' => ['required'],
        ], [
            'email.ends_with' => 'Solo se permiten correos con el dominio @casatapier.com.',
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
            'email'    => 'required|string|email|max:255|unique:users|ends_with:@casatapier.com',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.ends_with' => 'Solo se permiten correos con el dominio @casatapier.com.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Usará Argon2 si HASH_DRIVER=argon en .env
        ]);

        Auth::login($user);

        return redirect()->route('inicio');
    }

    // --- Métodos para Restablecer Contraseña ---
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|ends_with:@casatapier.com']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|ends_with:@casatapier.com',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function indexUsers()
    {
        $users = User::leftJoin('roles', 'users.role', '=', 'roles.id')
                     ->leftJoin('areas', 'users.area', '=', 'areas.id')
                     ->leftJoin('departamentos', 'users.departamento', '=', 'departamentos.id')
                     ->select('users.*', 'roles.nombre as role_name', 'areas.nombre as area_name', 'departamentos.nombre as departamento_name')
                     ->orderBy('users.id', 'desc')
                     ->get();

        $roles = DB::table('roles')->get();
        $areas = DB::table('areas')->get();
        $departamentos = DB::table('departamentos')->get();
        return view('users-management', compact('users', 'roles', 'areas', 'departamentos'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users', 'ends_with:@casatapier.com'],
            'password' => 'nullable|string|min:8',
            'role'     => 'required|string|max:255',
            'area'     => 'required',
            'departamento' => 'required',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.ends_with' => 'Solo se permiten correos con el dominio @casatapier.com.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->area = $request->area;
        $user->departamento = $request->departamento;

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

        return redirect()->route('users.index')->with('mensaje', 'Usuario creado correctamente. Por favor, comparta las credenciales de forma segura.');
    }

    public function editUser(User $user)
    {
        $roles = DB::table('roles')->get();
        $areas = DB::table('areas')->get();
        $departamentos = DB::table('departamentos')->get();
        return view('users-edit', compact('user', 'roles', 'areas', 'departamentos'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id), 'ends_with:@casatapier.com'],
            'role'     => 'required|string|max:255',
            'area'     => 'required',
            'departamento' => 'required',
            'password' => 'nullable|string|min:8',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.ends_with' => 'Solo se permiten correos con el dominio @casatapier.com.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->area = $request->area;
        $user->departamento = $request->departamento;

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