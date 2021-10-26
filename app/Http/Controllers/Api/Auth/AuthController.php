<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;





class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> ['required','string','max:255','min:3'],
            'email' => ['required','email','unique:users,email','max:255'],
            'password' => ['required', 'string', 'min:8','max:255', 'confirmed'],
            'type' => 'required',
        ]);


        if ($validator->fails())
        {
            return 'invalid data';
        }

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->tier_id = 1;


        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find(1);

        $data = [
            'access_token' => $token,
            'user' => $user,
            'request_limit'=> $tierData->request_limit
        ];


        return $this->apiResponse($data,'User registered successfully');
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->errors(),200);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find($user->tier_id);
        $data = [
            'access_token' => $token,
            'user' => $user,
            'request_limit'=> $tierData
        ];

        return $this->apiResponse($data, 'User logged successfully');

    }


}
