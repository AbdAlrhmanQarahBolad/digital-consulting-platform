<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Medical_Consultation;
use App\Models\Psychological_Consultation;
use App\Models\Family_Consultation;
use App\Models\Vocational_Consultation;
use App\Models\Time;
use App\Models\Consultation;
use App\Models\Reset_Time;
use App\Models\Business_Management_Consultation;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //   $this->middleware('auth:api', ['except' => ['login','registeruser','registerexpert']]) ;
    // }

    public function login(Request $request)
    {
        //  $request->validate([
        //     'email' => 'required|string|email',
        //     'password' => 'required|string|min:8',
        // ]);
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        if ($token){
            $id=Auth::guard('api')->user()->id;
            return response()->json([

                    'token' => $token,
                    'id'=>$id ,
                    'type' => 'user',

            ],200);
        }
        $token = Auth::guard('expert')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => 'wrong password or email',
            ], 401);
        }

        $id=Auth::guard('expert')->user()->id ;
        return response()->json([
                'status' => 'success',



                    'token' => $token,
                    'id'=>$id ,
                    'type' => 'expert',

            ],200);

    }

    public function registeruser(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string|between:2,30',
            'email' => 'required|string|email|max:30|unique:users',
            'phonenumber'=>'required|string|between:9,14|unique:users',
            'password' => 'required|string|min:8',
        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->toJson()], 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber'=>$request->phonenumber,
            'password' => Hash::make($request->password),
        ]);
        // $usere = Expert::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
       // Auth::login($usere);
        $token = Auth::login($user);
        return response()->json([
            'message' => 'User successfully registered',
            'id' => $user->id,

                'token' => $token,

        ],201);
    }

    public function registerexpert(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string|between:2,30',
            'email' => 'required|string|email|max:30|unique:experts',
            'password' => 'required|string|min:8',
          //  'imgpath' => 'nullable|mimes:jpeg,jpg,png',
            'M'=> 'required|string|in:true,false',
            'P'=> 'required|string|in:true,false',
            'F'=>'required|string|in:true,false,',
            'V'=>'required|string|in:true,false',
            'B'=>'required|string|in:true,false',
            'phonenumber'=>'required|string|between:9,14|unique:experts',
            'address'=>'required|string',
            'experinces'=>'required|string',
            //'cost_m'=>'required|integer|max:500000',

        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->toJson()], 400);
        }
        $expert = Expert::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'imgpath' => $request->imgpath ,
            'phonenumber'=>$request->phonenumber,
            'address'=>$request->address,
            'experinces'=>$request->experinces ,


        ]);
        if ($request->file('imgpath')){
           // $nameofphoto = $request->file('imgpath')->getClientOrginalName();
            $imgpath = $request->file('imgpath')->store('experts','mypublic');
            Expert::where('id', $expert->id)->update(array('imgpath'=>$imgpath)) ;

        }


        $time=Time::create([
            'sundayS'=>$request->sundayS,
            'mondayS'=>$request->mondayS,
            'tuesdayS'=>$request->tuesdayS,
            'wednesdayS'=>$request->wednesdayS,
            'thursdayS'=>$request->thursdayS,
            'sundayE'=>$request->sundayE,
            'mondayE'=>$request->mondayE,
            'tuesdayE'=>$request->tuesdayE,
            'wednesdayE'=>$request->wednesdayE,
            'thursdayE'=>$request->thursdayE,
            'expert_id' => $expert->id
        ]);





        Consultation::create([]);
        if ($request->M=='true'){
            $ff=FacadesValidator::make($request->all(), [
                'cost_m'=>'required|integer|max:500000',
            ]);
            if($ff->fails()){
                return response()->json(['message'=>$ff->errors()->toJson()], 400);
            }
            $m=Medical_Consultation::create(['expert_id'=>$expert->id,'consultation_id'=>$time->id, 'time_of_consultaion_m'=>$request->time_of_consultaion_m,'cost_m'=>$request->cost_m]);
            Consultation::where('id', $expert->id)->update(array('m_id' => $m->id));


        }
        if ($request->P=='true'){
            $ff=FacadesValidator::make($request->all(), [
                'cost_p'=>'required|integer|max:500000',
            ]);
            if($ff->fails()){
                return response()->json(['message'=>$ff->errors()->toJson()], 400);
            }
            $p=Psychological_Consultation::create(['expert_id'=>$expert->id,'consultation_id'=>$time->id,'time_of_consultaion_p'=>$request->time_of_consultaion_p,'cost_p'=>$request->cost_p]);
            Consultation::where('id', $expert->id)->update(array('p_id'=>$p->id));

        }
        if ($request->F=='true'){
            $ff=FacadesValidator::make($request->all(), [
                'cost_f'=>'required|integer|max:500000',
            ]);
            if($ff->fails()){
                return response()->json(['message'=>$ff->errors()->toJson()], 400);
            }
            $f=Family_Consultation::create(['expert_id'=>$expert->id,'consultation_id'=>$time->id,'time_of_consultaion_f'=>$request->time_of_consultaion_f,'cost_f'=>$request->cost_f]);
            Consultation::where('id', $expert->id)->update(array( 'f_id'=>$f->id));
        }
        if ($request->V=='true'){
            $ff=FacadesValidator::make($request->all(), [
                'cost_v'=>'required|integer|max:500000',
            ]);
            if($ff->fails()){
                return response()->json(['message'=>$ff->errors()->toJson()], 400);
            }
            $v=Vocational_Consultation::create(['expert_id'=>$expert->id,'consultation_id'=>$time->id,'time_of_consultaion_v'=>$request->time_of_consultaion_v,'cost_v'=>$request->cost_v]);
            Consultation::where('id', $expert->id)->update(array(  'v_id'=>$v->id));
        }
        if ($request->B=='true'){
            $ff=FacadesValidator::make($request->all(), [
                'cost_b'=>'required|integer|max:500000',
            ]);
            if($ff->fails()){
                return response()->json(['message'=>$ff->errors()->toJson()], 400);
            }
            $b=Business_Management_Consultation::create(['expert_id'=>$expert->id,'consultation_id'=>$time->id,'time_of_consultaion_b'=>$request->time_of_consultaion_b,'cost_b'=>$request->cost_b]);
            Consultation::where('id', $expert->id)->update(array( 'b_id'=>$b->id,));
        }
        Consultation::where('id', $expert->id)->update(array('expert_id' => $expert->id));

        $token = Auth::login($expert);
        return response()->json([
            'message' => 'expert successfully registered',
           // 'expert' => $expert->only(['id','name','email','phonenumber','address','experinces','imgpath']),
            'id'=>$expert->id ,
            'token' => $token,

        ],201);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        Auth::guard('expert')->logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ],200);


    }

    // public function refresh()
    // {
    //     return response()->json([
    //         'status' => 'success',
    //         'user' => Auth::user(),
    //         'authorisation' => [
    //             'token' => Auth::refresh(),
    //             'type' => 'bearer',
    //         ]
    //     ]);
    // }

    //to get data about user
    public function userProfile() {
        return response()->json(auth()->user());
    }

    public function isvalid(Request $request){
        if (   Auth::guard('api')->check()) {
            return response()->json(['message'=>'authenticate'],200);
        }
         else if (   Auth::guard('expert')->check()){
            return response()->json(['message'=>'authenticate'],200); }
        else
            return response()->json(['message'=>'not authenticate']) ;
    }

    public function converttouser(Request $request ){

        $e=Expert::find($request->id);

        $user = User::create([
            'name' => $e->name,
            'email' => $e->email,
            'phonenumber'=>$e->phonenumber,
            'password' => $e->password,
            'wallet' =>$e->wallet ,
            'expert_id'=>$e->id ,

        ]);
        Expert::find($request->id)->update(['user_id'=>$user->id  ]);
        $token = Auth::guard('api')->login($user);
        return response()->json([
            'message' => 'User successfully converted',
            'user' => $user,
            'token' => $token,

        ],201);
    }
}
