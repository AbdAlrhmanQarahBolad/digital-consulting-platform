<?php

namespace App\Http\Controllers;

use App\Models\Business_Management_Consultation;
use App\Models\Family_Consultation;
use App\Models\Medical_Consultation;
use App\Models\Psychological_Consultation;
use App\Models\Vocational_Consultation;
use App\Models\Consultation;
use App\Models\Time;
use App\Models\Expert;
use App\Models\Rating;
use App\Models\Favourite;
use App\Models\User;
use App\Models\Time_Management;
use App\Models\Reserved_Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Carbon\Carbon;

class dataController extends Controller
{

    public function medical(){
        //->whereRelation('Expert','user_id', '!=', auth()->id())
       // ->where('user_id','!=',null)
    //return auth()->guard('api')->id() ;
    return response()->json(["Medical"=>Medical_Consultation::with(['Expert','Expert.Time','Expert.Consultation'])->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get()],200) ;
    }
    public function business(){
        return response()->json(["Business"=>Business_Management_Consultation::with(['Expert','Expert.Time','Expert.Consultation'])->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get()],200);
    }
    public function vocational(){
        return response()->json(["Vocational"=>Vocational_Consultation::with(['Expert','Expert.Time','Expert.Consultation'])->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get()],200);
    }
    public function family(){
        return response()->json(["Family"=>Family_Consultation::with(['Expert','Expert.Time','Expert.Consultation'])->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get()],200);
    }
    public function psychological(){
        return response()->json(["Psychological"=>Psychological_Consultation::with(['Expert','Expert.Time','Expert.Consultation'])->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get()],200);
    }
    public function expertdetails(){
        return response()->json(["experts"=>Expert::with(['Time','Consultation'])->where('user_id', '!=', auth()->guard('api')->id())->get()],200);
    }
    public function profiledetails() {
        if (auth()->user()==null){
            return response()->json([ "expert"=>Expert::with(['Time'])->find(auth()->guard('expert')->user()->id) ,
            "Consultations"=>Consultation::with(['Medical_Consultation','Family_Consultation','Vocational_Consultation','Business_Management_Consultation','Psychological_Consultation'])->find(auth()->guard('expert')->user()->id),
        ],200);
        }
        return response()->json(["user"=>User::find(auth()->guard('api')->user()->id)],200);
    }
    public function highrating() {
       // return response()->json(["top rating"=>  Expert::all()->take(2)->max('value_of_rating')],200) ;
       // return response()->json(["user"=>Expert::where('value_of_rating',Expert::max('value_of_rating'))->get()],200) ;
    return  response()->json(["Top 10 :"=>Expert::orderBy('value_of_rating','DESC')->take(10)->with(['Time','Consultation'])->where('user_id', '!=', auth()->guard('api')->id())->get()],200);
    }

    public function editprofile(Request $request){
        if (auth()->user()==null){
            // $expert = Expert::find(auth()->guard('expert')->user()->id);
            // if (->email==$request->email ){
            // Expert::find(auth()->guard('expert')->user()->id)->update([
            //     'email' => null,
            // ]);
            $oldExpertPhone = Expert::find(auth()->guard('expert')->user()->id)->phonenumber;
            $oldExpertEmail = Expert::find(auth()->guard('expert')->user()->id)->email;
                $id=auth()->guard('expert')->user()->id ;
            if($oldExpertPhone == $request->phonenumber) {
                Expert::find(auth()->guard('expert')->user()->id)->update([
                    'phonenumber' => null,
                ]);
            }
            if($oldExpertEmail == $request->email) {
                Expert::find(auth()->guard('expert')->user()->id)->update([
                    'email' => null,
                ]);
            }

            $validator = FacadesValidator::make($request->all(), [
                'name' => 'required|string|between:2,30',
                'email' => 'required|string|email|max:30|unique:experts',
                'password' => 'required|string|min:8',
                'imgpath' => 'nullable|mimes:jpeg,jpg,png',
                'phonenumber'=>'required|string|between:9,14|unique:experts',
                'address'=>'required|string',
                'experinces'=>'required|string',
            ]);

            if($validator->fails()){

                Expert::find(auth()->guard('expert')->user()->id)->update([
                    'phonenumber' =>  $oldExpertPhone,
                ]);
                Expert::find(auth()->guard('expert')->user()->id)->update([
                    'email' =>  $oldExpertEmail,
                ]);
                return response()->json($validator->errors(), 422);
            }


            $expert=Expert::find(auth()->guard('expert')->user()->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phonenumber'=>$request->phonenumber,
                'address'=>$request->address,
                'experinces'=>$request->experinces ,
            ]);
            $expert=Expert::find($id) ;
            if ($request->file('imgpath')) {
                // $nameofphoto = $request->file('imgpath')->getClientOrginalName();
                $imgpath = $request->file('imgpath')->store('experts','mypublic');
                Expert::where('id', $expert->id)->update(array('imgpath'=>$imgpath)) ;
            }else{
                Expert::where('id', $expert->id)->update(array('imgpath'=>null)) ;

            }
            $expert=Expert::find($id) ;
            Auth::guard('expert')->logout();
            $credentials = $request->only('email', 'password');
            $token = Auth::guard('expert')->attempt($credentials);
            return response()->json([
                'message' => 'expert successfully edited',
                'expert'=>$expert ,
                    'token' => $token,

            ],201);
        }else {
            $id=auth()->guard('api')->user()->id ;
            $oldUserPhone = User::find(auth()->guard('api')->user()->id)->phonenumber;
            $oldUserEmail = User::find(auth()->guard('api')->user()->id)->email;

            if($oldUserPhone == $request->phonenumber) {
                Expert::find(auth()->guard('api')->user()->id)->update([
                    'phonenumber' => null,
                ]);
            }
            if($oldUserEmail == $request->email) {
                Expert::find(auth()->guard('api')->user()->id)->update([
                    'email' => null,
                ]);
            }

            $validator = FacadesValidator::make($request->all(), [
                'name' => 'required|string|between:2,30',
                'email' => 'required|string|email|max:30|unique:users,email,'.$id,
                'password' => 'required|string|min:8',

                'phonenumber'=>'required|string|between:9,14|unique:users,phonenumber,'.$id,

            ]);

            if($validator->fails()){

                User::find(auth()->guard('api')->user()->id)->update([
                    'phonenumber' =>  $oldUserPhone,
                ]);
                User::find(auth()->guard('api')->user()->id)->update([
                    'email' =>  $oldUserEmail,
                ]);
                return response()->json(['message'=>$validator->errors()], 422);
            }


            $user=User::find(auth()->guard('api')->user()->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phonenumber'=>$request->phonenumber,

            ]);
            $user=User::find(auth()->guard('api')->user()->id) ;
            Auth::guard('api')->logout();
            $credentials = $request->only('email', 'password');
            $token = Auth::guard('api')->attempt($credentials);
            return response()->json([
                'message' => 'user successfully edited',
                'user'=>$user ,
                    'token' => $token,

            ],201);


        }
    }
    public function searchall($name) {
    $expert=Expert::with(['Time','Consultation'])->where('name','like',"%".$name."%")->where('user_id', '!=', auth()->guard('api')->id())->get();
    //$timedevided =array()  ;
    // Time::find(1)->sundayS;
    //$timedevided= $this->SplitTime(  Time::find(1)->sundayS ,Time::find(1)->sundayE, Vocational_Consultation::find(1)->time_of_consultaion_v);
    return response()->json([
        'experts' => $expert,
       // 'asd'=>$timedevided,
        ],201) ;
    }
    public function search(Request $request) {

        if ($request->num==3){
            $e=Medical_Consultation::with(['Expert'])->whereRelation('Expert', 'name', 'like', '%'.$request->name.'%')->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get();
            return response()->json([
                'experts' => $e,

                ],201) ;
        }else if ($request->num==2){
            $f=Family_Consultation::with(['Expert'])->whereRelation('Expert', 'name', 'like', '%'.$request->name.'%')->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get();
            return response()->json([
                'experts' => $f,

                ],201) ;
        }else if ($request->num==1){
            $b=Business_Management_Consultation::with(['Expert'])->whereRelation('Expert', 'name', 'like', '%'.$request->name.'%')->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get();
            return response()->json([
                'experts' => $b,

                ],201) ;
        }else if ($request->num==4){
            $p=Psychological_Consultation::with(['Expert'])->whereRelation('Expert', 'name', 'like', '%'.$request->name.'%')->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get();
            return response()->json([
                'experts' => $p,

                ],201) ;
        }else if ($request->num==5){
            $v=Vocational_Consultation::with(['Expert'])->whereRelation('Expert', 'name', 'like', '%'.$request->name.'%')->whereRelation('Expert','user_id', '!=', auth()->guard('api')->id())->get();
            return response()->json([
                'experts' => $v,

                ],201) ;
        }
        }

    public function getexperttimee(Request $request){
        $time=Time::find($request->id);
        $consultaion = Consultation::find($request->id);
        if ($request->num==3){
            $sundaydevided= $this->SplitTime(  $time->sundayS ,$time->sundayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
            $mondaydevided= $this->SplitTime(  $time->mondayS ,$time->mondayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
            $tuesdaydevided= $this->SplitTime(  $time->tuesdayS ,$time->tuesdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
            $wednesdaydevided= $this->SplitTime(  $time->wednesdayS ,$time->wednesdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
            $thursdaydevided= $this->SplitTime(  $time->thursdayS ,$time->thursdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
            return response()->json([
                'sunday'=>$sundaydevided,
                'monday'=>$mondaydevided,
                'tuesday'=>$tuesdaydevided,
                'wednesday'=>$wednesdaydevided,
                'thursday'=>$thursdaydevided,
                ],201) ;
        }
        else if ($request->num==1){
            $sundaydevided= $this->SplitTime(  $time->sundayS ,$time->sundayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            $mondaydevided= $this->SplitTime(  $time->mondayS ,$time->mondayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            $tuesdaydevided= $this->SplitTime(  $time->tuesdayS ,$time->tuesdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            $wednesdaydevided= $this->SplitTime(  $time->wednesdayS ,$time->wednesdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            $thursdaydevided= $this->SplitTime(  $time->thursdayS ,$time->thursdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            return response()->json([
                'sunday'=>$sundaydevided,
                'monday'=>$mondaydevided,
                'tuesday'=>$tuesdaydevided,
                'wednesday'=>$wednesdaydevided,
                'thursday'=>$thursdaydevided,
                ],201) ;
         }else if ($request->num==2){
            $sundaydevided= $this->SplitTime(  $time->sundayS ,$time->sundayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            $mondaydevided= $this->SplitTime(  $time->mondayS ,$time->mondayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            $tuesdaydevided= $this->SplitTime(  $time->tuesdayS ,$time->tuesdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            $wednesdaydevided= $this->SplitTime(  $time->wednesdayS ,$time->wednesdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            $thursdaydevided= $this->SplitTime(  $time->thursdayS ,$time->thursdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            return response()->json([
                'sunday'=>$sundaydevided,
                'monday'=>$mondaydevided,
                'tuesday'=>$tuesdaydevided,
                'wednesday'=>$wednesdaydevided,
                'thursday'=>$thursdaydevided,
                ],201) ;
        }else
        if ($request->num==4){
            $sundaydevided= $this->SplitTime(  $time->sundayS ,$time->sundayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            $mondaydevided= $this->SplitTime(  $time->mondayS ,$time->mondayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            $tuesdaydevided= $this->SplitTime(  $time->tuesdayS ,$time->tuesdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            $wednesdaydevided= $this->SplitTime(  $time->wednesdayS ,$time->wednesdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            $thursdaydevided= $this->SplitTime(  $time->thursdayS ,$time->thursdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            return response()->json([
                'sunday'=>$sundaydevided,
                'monday'=>$mondaydevided,
                'tuesday'=>$tuesdaydevided,
                'wednesday'=>$wednesdaydevided,
                'thursday'=>$thursdaydevided,
                ],201) ;
        }else
        if ($request->num==5){
            $sundaydevided= $this->SplitTime(  $time->sundayS ,$time->sundayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            $mondaydevided= $this->SplitTime(  $time->mondayS ,$time->mondayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            $tuesdaydevided= $this->SplitTime(  $time->tuesdayS ,$time->tuesdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            $wednesdaydevided= $this->SplitTime(  $time->wednesdayS ,$time->wednesdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            $thursdaydevided= $this->SplitTime(  $time->thursdayS ,$time->thursdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            return response()->json([
                'sunday'=>$sundaydevided,
                'monday'=>$mondaydevided,
                'tuesday'=>$tuesdaydevided,
                'wednesday'=>$wednesdaydevided,
                'thursday'=>$thursdaydevided,
                ],201) ;
        }
    }

    public function getexperttime(Request $request){
        $Times = Expert::find($request->id)->Time_Management->where('num_of_day',$request->numday);
        $array=array();
        $arr = array();
        if (sizeof($Times) === 0){ //if
            $t=Time::find($request->id);
            $consultaion = Consultation::find($request->id);
            if ($request->num==3){
                if ($request->numday==1){
                    $sundaydevided= $this->SplitTime(  $t->sundayS ,$t->sundayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);

                    array_push($arr, $sundaydevided);
                    return response()->json([
                        'day'=>$arr,
                        'cost'=>Medical_Consultation::find($consultaion->m_id)->cost_m ,
                    ],200) ;
                }
                if ($request->numday==2){
                    $mondaydevided= $this->SplitTime(  $t->mondayS ,$t->mondayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
                    array_push($arr, $mondaydevided);
                    return response()->json([
                        'day'=>$arr,
                        'cost'=>Medical_Consultation::find($consultaion->m_id)->cost_m ,
                    ],200) ;
                }
                if ($request->numday==3){
                    $tuesdaydevided= $this->SplitTime(  $t->tuesdayS ,$t->tuesdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
                    array_push($arr, $tuesdaydevided);
                    return response()->json([
                        'day'=>$arr,
                        'cost'=>Medical_Consultation::find($consultaion->m_id)->cost_m ,
                    ],200) ;
                }
                if ($request->numday==4){
                    $wednesdaydevided= $this->SplitTime(  $t->wednesdayS ,$t->wednesdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
                    array_push($arr, $wednesdaydevided);
                    return response()->json([
                        'day'=>$arr,
                        'cost'=>Medical_Consultation::find($consultaion->m_id)->cost_m ,
                    ],200) ;
                }
                if ($request->numday==5){
                    $thursdaydevided= $this->SplitTime(  $t->thursdayS ,$t->thursdayE, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
                    array_push($arr, $thursdaydevided);
                    return response()->json([
                        'day'=>$arr,
                        'cost'=>Medical_Consultation::find($consultaion->m_id)->cost_m ,
                    ],200) ;
                }
            }
            else if ($request->num==1){
            if ($request->numday==1){
                $sundaydevided= $this->SplitTime(  $t->sundayS ,$t->sundayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
                array_push($arr, $sundaydevided);
                return response()->json([
                    'day'=>$arr,
                    'cost'=>Business_Management_Consultation::find($consultaion->b_id)->cost_b ,
                ],200) ;
            }
            if ($request->numday==2){
            $mondaydevided= $this->SplitTime(  $t->mondayS ,$t->mondayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            array_push($arr, $mondaydevided);
            return response()->json([
                'day'=>$arr,
                'cost'=>Business_Management_Consultation::find($consultaion->b_id)->cost_b ,
            ],200) ;
            }
            if ($request->numday==3){
            $tuesdaydevided= $this->SplitTime(  $t->tuesdayS ,$t->tuesdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            array_push($arr, $tuesdaydevided);
            return response()->json([
                'day'=>$arr,
                'cost'=>Business_Management_Consultation::find($consultaion->b_id)->cost_b ,
            ],200) ;
            }
            if ($request->numday==4){
            $wednesdaydevided= $this->SplitTime(  $t->wednesdayS ,$t->wednesdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            array_push($arr, $wednesdaydevided);
            return response()->json([
                'day'=>$arr,
                'cost'=>Business_Management_Consultation::find($consultaion->b_id)->cost_b ,
            ],200) ;
            }
            if ($request->numday==5){
            $thursdaydevided= $this->SplitTime(  $t->thursdayS ,$t->thursdayE, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            array_push($arr, $thursdaydevided);
            return response()->json([
                'day'=>$arr,
                'cost'=>Business_Management_Consultation::find($consultaion->b_id)->cost_b ,
            ],200) ;
            }
            }else if ($request->num==2){
            if ($request->numday==1){
                $sundaydevided= $this->SplitTime(  $t->sundayS ,$t->sundayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
                array_push($arr, $sundaydevided);
                return response()->json([
                    'day'=>$arr,
                    'cost'=>Family_Consultation::find($consultaion->f_id)->cost_f ,

                ],200) ;
            }
            if ($request->numday==2){
                $mondaydevided= $this->SplitTime(  $t->mondayS ,$t->mondayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
                array_push($arr, $mondaydevided);
                return response()->json([
                    'day'=>$arr,
                    'cost'=>Family_Consultation::find($consultaion->f_id)->cost_f ,
                ],200) ;
            }
            if ($request->numday==3){
                $tuesdaydevided= $this->SplitTime(  $t->tuesdayS ,$t->tuesdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
                array_push($arr, $tuesdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Family_Consultation::find($consultaion->f_id)->cost_f ,
            ],200) ;
            }
            if ($request->numday==4){
                $wednesdaydevided= $this->SplitTime(  $t->wednesdayS ,$t->wednesdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
                array_push($arr, $wednesdaydevided);
                return response()->json([
                    'day'=>$arr,
                    'cost'=>Family_Consultation::find($consultaion->f_id)->cost_f ,
                ],200) ;
            }
            if ($request->numday==5){
                $thursdaydevided= $this->SplitTime(  $t->thursdayS ,$t->thursdayE, Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
                array_push($arr, $thursdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Family_Consultation::find($consultaion->f_id)->cost_f ,
            ],200) ;
            }
        }else
        if ($request->num==4){
            if ($request->numday==1){
                $sundaydevided= $this->SplitTime(  $t->sundayS ,$t->sundayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
                array_push($arr, $sundaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Psychological_Consultation::find($consultaion->p_id)->cost_p,
                ],200) ;
            }
            if ($request->numday==2){
                $mondaydevided= $this->SplitTime(  $t->mondayS ,$t->mondayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
                array_push($arr, $mondaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Psychological_Consultation::find($consultaion->p_id)->cost_p,
                ],200) ;
            }
            if ($request->numday==3){
                $tuesdaydevided= $this->SplitTime(  $t->tuesdayS ,$t->tuesdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
                array_push($arr, $tuesdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Psychological_Consultation::find($consultaion->p_id)->cost_p,
            ],200) ;
            }
            if ($request->numday==4){
                $wednesdaydevided= $this->SplitTime(  $t->wednesdayS ,$t->wednesdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
                array_push($arr, $wednesdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Psychological_Consultation::find($consultaion->p_id)->cost_p,
                ],200) ;
            }
            if ($request->numday==5){
            $thursdaydevided= $this->SplitTime(  $t->thursdayS ,$t->thursdayE, Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            array_push($arr, $thursdaydevided);
            return response()->json([
            'day'=>$arr,
            'cost'=>Psychological_Consultation::find($consultaion->p_id)->cost_p,
            ],200) ;
            }
        }else
        if ($request->num==5){
            if ($request->numday==1){
                $sundaydevided= $this->SplitTime(  $t->sundayS ,$t->sundayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
                array_push($arr, $sundaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Vocational_Consultation::find($consultaion->v_id)->cost_v ,
                ],200) ;
            }
            if ($request->numday==2){
                $mondaydevided= $this->SplitTime(  $t->mondayS ,$t->mondayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
                array_push($arr, $mondaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Vocational_Consultation::find($consultaion->v_id)->cost_v ,
                ],200) ;
            }
            if ($request->numday==3){
                $tuesdaydevided= $this->SplitTime(  $t->tuesdayS ,$t->tuesdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
                array_push($arr, $tuesdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Vocational_Consultation::find($consultaion->v_id)->cost_v ,
            ],200) ;
            }
            if ($request->numday==4){
                $wednesdaydevided= $this->SplitTime(  $t->wednesdayS ,$t->wednesdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
                array_push($arr, $wednesdaydevided);
                return response()->json([
                'day'=>$arr,
                'cost'=>Vocational_Consultation::find($consultaion->v_id)->cost_v ,
                ],200) ;
            }
            if ($request->numday==5){
            $thursdaydevided= $this->SplitTime(  $t->thursdayS ,$t->thursdayE, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            array_push($arr, $thursdaydevided);
            return response()->json([
            'day'=>$arr,
            'cost'=>Vocational_Consultation::find($consultaion->v_id)->cost_v ,
            ],200) ;
            }
        }
    } //end of first if
        $consultaion = Consultation::find($request->id);
        $cost ;
        foreach ($Times as $time) {
        if ($time->num_of_day == $request->numday){
        if ($request->num==3){
        $divide=$this->SplitTime($time->start ,$time->end, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
        array_push($array, $divide);
        $cost=Medical_Consultation::find($consultaion->m_id)->cost_m ;
        }
        else if ($request->num==1){
            $divide= $this->SplitTime($time->start ,$time->end, Business_Management_Consultation::find($consultaion->b_id)->time_of_consultaion_b);
            array_push($array, $divide);
            $cost=Business_Management_Consultation::find($consultaion->b_id)->cost_b ;
        }
        else if ($request->num==2){
            $divide= $this->SplitTime($time->start ,$time->end,  Family_Consultation::find($consultaion->f_id)->time_of_consultaion_f);
            array_push($array, $divide);
            $cost=Family_Consultation::find($consultaion->f_id)->cost_f ;
        }
        else if ($request->num==4){
            $divide=$this->SplitTime($time->start ,$time->end,Psychological_Consultation::find($consultaion->p_id)->time_of_consultaion_p);
            array_push($array, $divide);
            $cost=Psychological_Consultation::find($consultaion->p_id)->cost_p ;
        }
        else if ($request->num==5){
            $divide= $this->SplitTime($time->start ,$time->end, Vocational_Consultation::find($consultaion->v_id)->time_of_consultaion_v);
            array_push($array, $divide);
            $cost=Vocational_Consultation::find($consultaion->v_id)->cost_v ;
        }
        }
        }


        //sorting of arrays by first element in every array
            for ($co=0 ;$co<count($array);$co++){
                for ($index=0 ;$index<count($array)-1;$index++){
                $t1=strtotime($array[$index][0]);
                $t2=strtotime($array[$index+1][0]);
                if ($t1>$t2){
                    $a= $array[$index];
                    $array[$index]=$array[$index+1];
                    $array[$index+1]=$a;
                }
                }
            }
            if ($request->numday==1){

                return response()->json([
                'day'=>$array,
                'cost'=>$cost,
                ],200) ;
            }else
            if ($request->numday==2){
                return response()->json([
                'day'=>$array,
                'cost'=>$cost,
                ],200) ;
            }else
            if ($request->numday==3){
             return response()->json([
                'day'=>$array,
                'cost'=>$cost,
            ],200) ;
            }else
            if ($request->numday==4){

                return response()->json([
                'day'=>$array,
                'cost'=>$cost,
                ],200) ;
            }else
            if ($request->numday==5){
            return response()->json([
                'day'=>$array ,
                'cost'=>$cost,
                ],200) ;
        }






    }
    /////////////////////////////////////////////////////////////
    public function gettotalexperttime(Request $request){
        $Times = Expert::find($request->id)->Time_Management;
        $tuesday=array();
        $monday=array();
        $sunday=array();
        $wednesday=array();
        $thursday=array();
        $t=Time::find($request->id);
        $ex=Expert::find($request->id);
        $Consultation=Consultation::with(['Medical_Consultation','Family_Consultation','Vocational_Consultation','Business_Management_Consultation','Psychological_Consultation'])->find($request->id) ;
        if (sizeof($Times) === 0){ //if

            return response()->json([

                'expert'=>$ex,
                'consultation'=>$Consultation ,
                'time'=>$t
                ],200) ;
         } //end of first if

        $counter1=0 ;
        $counter2=0 ;
        $counter3=0 ;
        $counter4=0 ;
        $counter5=0 ;
        foreach ($Times as $time) {
        //$divide=$this->SplitTime($time->start ,$time->end, Medical_Consultation::find($consultaion->m_id)->time_of_consultaion_m);
        if ($time->num_of_day==3){
        $array1=array($time->start, $time->end);
        array_push($tuesday,$array1 );
        $counter3++;
        }
        else if ($time->num_of_day==2){
            $array1=array($time->start, $time->end);
            array_push($monday,$array1 );
            $counter2++;
            }
            else if ($time->num_of_day==1){
                $array1=array($time->start, $time->end);
                array_push($sunday,$array1 );
                $counter1++;
                }
                else if ($time->num_of_day==4){
                    $array1=array($time->start, $time->end);
                    array_push($wednesday,$array1 );
                    $counter4++;
                    }
                    else if ($time->num_of_day==5){
                        $array1=array($time->start, $time->end);
                        array_push($thursday,$array1 );
                        $counter5++;
                        }

        }
        if ($counter1==0){
            $array1=array($t->sundayS, $t->sundayE);
            array_push($sunday,$array1 );

        }
        if ($counter2==0){
            $array1=array($t->mondayS, $t->mondayE);
            array_push($monday,$array1 );

        }
        if ($counter3==0){
            $array1=array($t->tuesdayS, $t->tuesdayE);
            array_push($tuesday,$array1 );

        }
        if ($counter4==0){
            $array1=array($t->wednesdayS, $t->wednesdayE);
            array_push($wednesday,$array1 );

        }
        if ($counter5==0){
            $array1=array($t->thursdayS, $t->thursdayE);
            array_push($thursday,$array1 );

        }
        for ($co=0 ;$co<count($sunday);$co++){
            for ($index=0 ;$index<count($sunday)-1;$index++){
            $t1=strtotime($sunday[$index][0]);
            $t2=strtotime($sunday[$index+1][0]);
            if ($t1>$t2){
                $a= $sunday[$index];
                $sunday[$index]=$sunday[$index+1];
                $sunday[$index+1]=$a;
            }
            }
        }
        for ($co=0 ;$co<count($monday);$co++){
            for ($index=0 ;$index<count($monday)-1;$index++){
            $t1=strtotime($monday[$index][0]);
            $t2=strtotime($monday[$index+1][0]);
            if ($t1>$t2){
                $a= $monday[$index];
                $monday[$index]=$monday[$index+1];
                $monday[$index+1]=$a;
            }
            }
        }
        for ($co=0 ;$co<count($tuesday);$co++){
            for ($index=0 ;$index<count($tuesday)-1;$index++){
            $t1=strtotime($tuesday[$index][0]);
            $t2=strtotime($tuesday[$index+1][0]);
            if ($t1>$t2){
                $a= $tuesday[$index];
                $tuesday[$index]=$tuesday[$index+1];
                $tuesday[$index+1]=$a;
            }
            }
        }
        for ($co=0 ;$co<count($wednesday);$co++){
            for ($index=0 ;$index<count($wednesday)-1;$index++){
            $t1=strtotime($wednesday[$index][0]);
            $t2=strtotime($wednesday[$index+1][0]);
            if ($t1>$t2){
                $a= $wednesday[$index];
                $wednesday[$index]=$wednesday[$index+1];
                $wednesday[$index+1]=$a;
            }
            }
        }
        for ($co=0 ;$co<count($thursday);$co++){
            for ($index=0 ;$index<count($thursday)-1;$index++){
            $t1=strtotime($thursday[$index][0]);
            $t2=strtotime($thursday[$index+1][0]);
            if ($t1>$t2){
                $a= $thursday[$index];
                $thursday[$index]=$thursday[$index+1];
                $thursday[$index+1]=$a;
            }
            }
        }

        return response()->json([
            'expert'=>$ex,
            'consultation'=>$Consultation ,
            'sunday'=>$sunday,
            'monday'=>$monday,
            'tuesday'=>$tuesday ,
            'wednesday'=>$wednesday,
            'thursday'=>$thursday ,
            ],200) ;
    }



//////////////////////////////////////////////////////////////////
    public function reserve(Request $request){
        $Times = Expert::find($request->id)->Time_Management->where('num_of_day',$request->numday);
        $t = Time::find($request->id);
        $org = Time::find($request->id);
        $t->sundayS=strtotime($t->sundayS);
        $t->sundayE=strtotime($t->sundayE);
        $t->mondayS=strtotime($t->mondayS);
        $t->mondayE=strtotime($t->mondayE);
        $t->tuesdayS=strtotime($t->tuesdayS);
        $t->tuesdayE=strtotime($t->tuesdayE);
        $t->wednesdayS=strtotime($t->wednesdayS);
        $t->wednesdayE=strtotime($t->wednesdayE);
        $t->thursdayS=strtotime($t->thursdayS);
        $t->thursdayE=strtotime($t->thursdayE);
        $r=strtotime($request->st);
        $rr=strtotime($request->en);
        $e=Expert::find($request->id);
        $u=User::find($request->iduser);
        if (sizeof($Times) === 0){
            if ($request->numday==1){
                if ($t->sundayS < $r && $t->sundayE > $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$org->sundayS,
                        'end'=>$request->st,
                    ]);
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$request->en,
                        'end'=>$org->sundayE,
                    ]);
                }
                else if ($t->sundayS == $r && $t->sundayE == $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>'00:00:00',
                        'end'=>'00:00:00',
                    ]);
            }
            else if ($t->sundayS == $r && $t->sundayE > $rr){
                Time_Management::create([
                    'expert_id' => $request->id,
                    'num_of_day' => $request->numday,
                    'start'=>$request->en,
                    'end'=>$org->sundayE,
                ]);
        }
        else if ($t->sundayS < $r && $t->sundayE == $rr){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$org->sundayS,
                'end'=>$request->st,
            ]);
        }
    }
           else if ($request->numday==2){
                if ($t->mondayS < $r && $t->mondayE >  $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$org->mondayS,
                        'end'=>$request->st,
                    ]);
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$request->en,
                        'end'=>$org->mondayE,
                    ]);

                }
                else if ($t->mondayS == $r && $t->mondayE == $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>'00:00:00',
                        'end'=>'00:00:00',
                    ]);
            }
            else if ($t->mondayS == $r && $t->mondayE > $rr){
                Time_Management::create([
                    'expert_id' => $request->id,
                    'num_of_day' => $request->numday,
                    'start'=>$request->en,
                    'end'=>$org->mondayE,
                ]);
        }
        else if ($t->mondayS < $r && $t->mondayE == $rr){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$org->mondayS,
                'end'=>$request->st,
            ]);
        }
            }
           else if ($request->numday==3){
                if ($t->tuesdayS < $r && $t->tuesdayE > $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$org->tuesdayS,
                        'end'=>$request->st,
                    ]);
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$request->en,
                        'end'=>$org->tuesdayE,
                    ]);

                }
                else if ($t->tuesdayS == $r && $t->tuesdayE == $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>'00:00:00',
                        'end'=>'00:00:00',
                    ]);
            }
            else if ($t->tuesdayS == $r && $t->tuesdayE > $rr){
                Time_Management::create([
                    'expert_id' => $request->id,
                    'num_of_day' => $request->numday,
                    'start'=>$request->en,
                    'end'=>$org->tuesdayE,
                ]);
        }
        else if ($t->tuesdayS < $r && $t->tuesdayE == $rr){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$org->tuesdayS,
                'end'=>$request->st,
            ]);
        }
            }
        else  if ($request->numday==4){
                if ($t->wednesdayS < $r && $t->wednesdayE > $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$org->wednesdayS,
                        'end'=>$request->st,
                    ]);
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$request->en,
                        'end'=>$org->wednesdayE,
                    ]);

                }
                else if ($t->wednesdayS == $r && $t->wednesdayE == $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>'00:00:00',
                        'end'=>'00:00:00',
                    ]);
            }
            else if ($t->wednesdayS == $r && $t->wednesdayE > $rr){
                Time_Management::create([
                    'expert_id' => $request->id,
                    'num_of_day' => $request->numday,
                    'start'=>$request->en,
                    'end'=>$org->wednesdayE,
                ]);
        }
        else if ($t->wednesdayS < $r && $t->wednesdayE == $rr){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$org->wednesdayS,
                'end'=>$request->st,
            ]);
        }
            }
        else  if ($request->numday==5){
                if ($t->thursdayS < $r && $t->thursdayE > $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$org->thursdayS,
                        'end'=>$request->st,
                    ]);
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>$request->en,
                        'end'=>$org->thursdayE,
                    ]);
                }
                else if ($t->thursdayS == $r && $t->thursdayE == $rr){
                    Time_Management::create([
                        'expert_id' => $request->id,
                        'num_of_day' => $request->numday,
                        'start'=>'00:00:00',
                        'end'=>'00:00:00',
                    ]);
            }
            else if ($t->thursdayS == $r && $t->thursdayE > $rr){
                Time_Management::create([
                    'expert_id' => $request->id,
                    'num_of_day' => $request->numday,
                    'start'=>$request->en,
                    'end'=>$org->thursdayE,
                ]);
        }
        else if ($t->thursdayS < $r && $t->thursdayE == $rr){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$org->thursdayS,
                'end'=>$request->st,
            ]);
        }
            }
            Reserved_Time::create([
                'expert_id' => $request->id,
                'user_id'=>$request->iduser,
                'number_of_day' => $request->numday,
                'start_of_time_reserved'=>$request->st,
                'end_of_time_reserved'=>$request->en,
            ]);
            if ($request->numofconsultaion==1){
            $b=Consultation::find($request->id);
            $b1=Business_Management_Consultation::find($b->b_id)->cost_b;
            Expert::find($request->id)->update(['wallet'=>$b1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $b1]);
            }else
            if ($request->numofconsultaion==2){
            $f=Consultation::find($request->id);
            $f1=Family_Consultation::find($f->f_id)->cost_f;
            Expert::find($request->id)->update(['wallet'=>$f1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $f1]);
            }else if ($request->numofconsultaion==3){
            $m=Consultation::find($request->id);
            $m1=Medical_Consultation::find($m->m_id)->cost_m;
            Expert::find($request->id)->update(['wallet'=>$m1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $m1]);
            }else if ($request->numofconsultaion==4){
            $p=Consultation::find($request->id);
            $p1=Psychological_Consultation::find($p->p_id)->cost_p;
            Expert::find($request->id)->update(['wallet'=>$p1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $p1]);
            }else if ($request->numofconsultaion==5){
            $v=Consultation::find($request->id);
            $v1=Vocational_Consultation::find($v->v_id)->cost_v;
            Expert::find($request->id)->update(['wallet'=>$v1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $v1]);
            }


            return response()->json([
                'message' => 'Successfully reserved',
            ],200);


        }






        foreach ($Times as $ti){
            $va1=strtotime($ti->start);
            $va2=strtotime($ti->end);
        if ($r > $va1 &&  $rr < $va2){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$ti->start,
                'end'=>$request->st,
            ]);
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$request->en,
                'end'=>$ti->end,
            ]);
            Time_Management::where('start',$ti->start)->where('end',$ti->end)->delete();

        }else if ($r == $va1 &&  $rr < $va2){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$request->en,
                'end'=>$ti->end,
            ]);
            Time_Management::where('start',$ti->start)->where('end',$ti->end)->delete();
        }else if ($r > $va1 && $rr == $va2){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>$ti->start,
                'end'=>$request->st,
            ]);
            Time_Management::where('start',$ti->start)->where('end',$ti->end)->delete();
        }else if ($r == $va1 &&  $rr == $va2){
            Time_Management::create([
                'expert_id' => $request->id,
                'num_of_day' => $request->numday,
                'start'=>'00:00:00',
                'end'=>'00:00:00',
            ]);
            Time_Management::where('start',$ti->start)->where('end',$ti->end)->delete();
        }

        }
        Reserved_Time::create([
            'expert_id' => $request->id,
            'user_id'=>$request->iduser,
            'number_of_day' => $request->numday,
            'start_of_time_reserved'=>$request->st,
            'end_of_time_reserved'=>$request->en,
        ]);
        if ($request->numofconsultaion==1){
            $b=Consultation::find($request->id);
            $b1=Business_Management_Consultation::find($b->b_id)->cost_b;
            Expert::find($request->id)->update(['wallet'=>$b1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $b1]);
        }else  if ($request->numofconsultaion==2){
            $f=Consultation::find($request->id);
            $f1=Family_Consultation::find($f->f_id)->cost_f;
            Expert::find($request->id)->update(['wallet'=>$f1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $f1]);
        }else if ($request->numofconsultaion==3){
            $m=Consultation::find($request->id);
            $m1=Medical_Consultation::find($m->m_id)->cost_m;
            Expert::find($request->id)->update(['wallet'=>$m1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $m1]);
        }else if ($request->numofconsultaion==4){
            $p=Consultation::find($request->id);
            $p1=Psychological_Consultation::find($p->p_id)->cost_p;
            Expert::find($request->id)->update(['wallet'=>$p1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $p1]);
        }else if ($request->numofconsultaion==5){
            $v=Consultation::find($request->id);
            $v1=Vocational_Consultation::find($v->v_id)->cost_v;
            Expert::find($request->id)->update(['wallet'=>$v1+$e->wallet]);
            User::find($request->iduser)->update(['wallet'=> $u->wallet - $v1]);
        }

        return response()->json([
            'message' => 'Successfully reserved ',
        ],200);
    }

    public function getreservedtime(Request $request){
        $reserved=Reserved_Time::where('expert_id',$request->id)->with(['User'])->get();
        return response()->json([
            'reservedtime' => $reserved ,
        ],200);

    }
    public function getreservedtimeforuser(Request $request){
        $reserved=Reserved_Time::where('user_id',$request->userid)->with(['Expert'])->get();
        return response()->json([
            'reservedtime' => $reserved ,
        ],200);

    }
    public function checkfavourite(Request $request){

        $fav=Favourite::where('expert_id', $request->expertid )->where('user_id', $request->id )->get() ;
        if   (sizeof($fav) === 0){
        return response()->json([
            'wasfavourite' => false ,
        ],200);
        }
        else{
        return response()->json([
            'wasfavourite' => true ,
    ],200);}
    }

    public function addtofavourite(Request $request){
        Favourite::create([
            'user_id'=> $request->id  ,
            'expert_id'=> $request->expertid,
        ]);
        return response()->json([
            'message' => 'successful' ,
        ],200);

    }
    public function getlistfavourite(Request $request){

        $fav=Favourite::where('user_id', $request->id )->with(['Expert'])->get() ;

        return response()->json([
            'favlist' => $fav ,
        ],200);


    }
    public function rate(Request $request){
        Rating::create([
            'user_id'=> $request->id  ,
            'expert_id'=> $request->expertid,
            'value'=> $request->value,
        ]);
        $rating=Rating::where('expert_id',$request->expertid)->get(['value']);
        $sum=0 ;
        foreach ($rating as $rat){
        $sum+=$rat->value;
        }
        $sum=$sum/count($rating);
        Expert::find($request->expertid)->update(['value_of_rating'=>$sum]);
        $expert= Expert::find($request->expertid) ;
        return  response()->json([
            'value_of_rating' => $expert->value_of_rating,
        ],200);

    }
    public function checkisrated(Request $request){
        $rating=Rating::where('user_id',$request->id)->where('expert_id', $request->expertid )->get();

        if   (sizeof($rating) === 0){
            return response()->json([
                'wasrated' => false ,
            ],200);
            }else{
                return response()->json([
                    'wasrated' => true ,
                    'value'=>$rating[0]->value ,
            ],200);}

    }












    public function SplitTime($StartTime, $EndTime, $Duration="60"){
        $ReturnArray = array ();// Define output
        $StartTime    = strtotime ($StartTime); //Get Timestamp
        $EndTime      = strtotime ($EndTime); //Get Timestamp

        $AddMins  = $Duration * 60;

        while ($StartTime <= $EndTime) //Run loop
        {
            $ReturnArray[] = date ("G:i", $StartTime);
            $StartTime += $AddMins; //Endtime check
        }
        return $ReturnArray;
    }



}




    // $validator = FacadesValidator::make($request->all(), [
    //     'name' => 'required|string|between:2,30',
    //     'email' => 'required|string|email|max:30|unique:users',
    //     'phonenumber'=>'required|string|between:9,14:unique:users',
    //     'password' => 'required|string|min:8',
    // ]);
    // if($validator->fails()){
    //     return response()->json($validator->errors()->toJson(), 400);
    // }
    // User::find(auth()->guard('api')->user()->id)->update(array([
    //     'name' => $request->name,
    //     'email' => $request->email,
    //     'phonenumber'=>$request->phonenumber,
    //     'password' => Hash::make($request->password),
    // ]));

    // $token = Auth::login($user);
    // return response()->json([
    //     'message' => 'User successfully edited',
    //     'authorisation' => [
    //         'token' => $token,
    //     ]
    // ],201);






    // public function storephoto(Request $request){
    //   //  $nameofphoto = $request->file('imgpath')->getClientOrginalName();

    //     return  ;
    // }




