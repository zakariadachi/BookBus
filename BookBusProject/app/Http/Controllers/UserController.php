<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    function register(Request $request){
        $isInputsvalidated = $request->validate([
            "name" => ['required' , 'min:3' , Rule::unique('users','name')],
            "email" => ['required' , Rule::unique('users','email')],
            "password" => 'required'
        ]);

        $isInputsvalidated['password'] = bcrypt($isInputsvalidated['password']);

        $User = User::create($isInputsvalidated);
        // $Auth()->login($User);
        return "your register() successfuly name :";

    }
}
