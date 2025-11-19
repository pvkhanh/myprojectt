<?php

// namespace App\Http\Controllers;

// use App\Http\Requests\LoginRequest;
// use App\Http\Requests\RegisterRequest;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;

// class AuthController extends Controller
// {
//     // Hiển thị form đăng nhập
//     public function showLoginForm()
//     {
//         return view('admin.auth.pages.login');
//     }

//     // Xử lý đăng nhập (cho phép login bằng email hoặc username)
//     public function login(LoginRequest $request)
//     {
//         $credentials = $request->validated();
//         $login = $credentials['login'];
//         $password = $credentials['password'];

//         $user = User::where('email', $login)->orWhere('username', $login)->first();

//         if ($user && (Hash::check($password, $user->password) || hash_equals($user->password, $password))) {
//             Auth::login($user);
//             session()->regenerate();
//             return redirect()->route('admin.dashboard')->with('success', 'Login successful! Welcome back.');
//         }

//         return back()->with('error', 'Invalid email/username or password.')->withInput();
//     }

//     // Hiển thị form đăng ký
//     public function register()
//     {
//         return view('admin.auth.pages.register');
//     }

//     // Xử lý đăng ký
//     public function postRegister(RegisterRequest $request)
//     {
//         User::create([
//             'first_name' => $request->get('first_name'),
//             'last_name' => $request->get('last_name'),
//             'username' => $request->get('username'),
//             'email' => $request->get('email'),
//             'password' => bcrypt($request->get('password')),
//             'role' => 'buyer',
//             'is_active' => true,
//         ]);

//         return redirect()->route('login')->with('message', 'Register successfully! Please log in.');
//     }

//     // Đăng nhập phiên bản khác
//     public function postLogin(LoginRequest $request)
//     {
//         $loginInput = $request->input('login');
//         $password = $request->input('password');
//         $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

//         $credentials = [$fieldType => $loginInput, 'password' => $password];

//         if (Auth::attempt($credentials, $request->boolean('remember'))) {
//             $request->session()->regenerate();
//             return redirect()->route('admin.dashboard')->with('message', 'Login successful!');
//         }

//         return back()->withErrors([
//             'login' => 'Invalid email/username or password.',
//         ])->withInput();
//     }

//     // Đăng xuất
//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect()->route('login')->with('success', 'Logged out successfully!');
//     }
// }




namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('admin.auth.pages.login');
    }

    // Xử lý đăng nhập (cho phép login bằng email hoặc username)
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $login = $credentials['login'];
        $password = $credentials['password'];

        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if ($user && (Hash::check($password, $user->password) || hash_equals($user->password, $password))) {
            Auth::login($user);
            session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Login successful! Welcome back.');
        }

        return back()->with('error', 'Invalid email/username or password.')->withInput();
    }

    // Hiển thị form đăng ký
    public function register()
    {
        return view('admin.auth.pages.register');
    }

    // Xử lý đăng ký
    public function postRegister(RegisterRequest $request)
    {
        User::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'role' => 'buyer',
            'is_active' => true,
        ]);

        return redirect()->route('login')->with('message', 'Register successfully! Please log in.');
    }

    // Đăng nhập phiên bản khác
    public function postLogin(LoginRequest $request)
    {
        $loginInput = $request->input('login');
        $password = $request->input('password');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$fieldType => $loginInput, 'password' => $password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('message', 'Login successful!');
        }

        return back()->withErrors([
            'login' => 'Invalid email/username or password.',
        ])->withInput();
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
