<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Validation\ValidationException;
use App\CentralLogics\Helpers;

class LoginController extends Controller
{
    public function checkUser(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string',
        ]);

        $check = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('userid', $request->email);
        })->first();
        if(!$check) {
            return response()->json(['success' => false, 'message' => 'Please enter valid EmailId or UserId!!']);
        }
        return response()->json(['success' => true, 'message' => 'User Valid!!']);
    }

    public function login(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('userid', $request->email);
        })->first();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The provided password is incorrect.',
            ]);
        }
    
        // Log in the user
        Auth::login($user);
        $url = Helpers::getDashboardRedirect($user);

        return response()->json(['success' => true, 'message' => 'Login SuccessFully!!', 'data' => auth()->user(),'url'=>$url]);     
    }
}
