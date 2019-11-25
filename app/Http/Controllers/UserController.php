<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Credentials = ['email' => $request->email, 'password' => $request->password, 'status' => 'active'];
        $authenticate = Auth::attempt($Credentials);
        if ($authenticate) {
            $accesstoken = request('accesstoken');
            $user = Auth::user();
            $success['token'] = $user->createToken($accesstoken)->accessToken;

            return response()->json($success ? $success : "Internal Server Error", $user ? 200 : 500);

        } else {
            return response()->json(["error" => 'Email or Password is Invalid / Account not Activated'], 412);
        }
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'brand_company' => 'required|string|max:50',
            'phone_number' => 'digits:11|numeric',
            'email' => 'required|email|unique:users,email',
            'package' => 'required|string',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|string',
            'location' => 'required|string',
            'place' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'timezone' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $user = User::create($input);

        $input['user_id'] = $user->id;
        $meeting = Meeting::create($input);

        return response()->json($user || $meeting ? ['user' => $user, 'meeting' => $meeting] : "Internal Server Error", $user || $meeting ? 200 : 500);
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
