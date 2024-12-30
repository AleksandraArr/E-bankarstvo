<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Rules\PasswordChars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:employees',
            'password' => 'required|string|min:8',
            'role' =>'required|string|in:support,admin'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $employee = Employee::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        $token = $employee->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Employee registered successfully',
            'user' => $employee,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    public function logIn(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = Employee::where('email', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    

    public function logout(Request $request)
    {   
    $user = $request->user();
    
    if ($user) {
        $user->tokens()->delete(); 
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    return response()->json(['message' => 'No user logged in'], 401);
    }

}
