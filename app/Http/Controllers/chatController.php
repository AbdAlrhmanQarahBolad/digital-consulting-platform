<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time;
use App\Models\Expert;
use App\Models\Rating;
use App\Models\Favourite;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
class chatController extends Controller
{
    public function send(Request $request){
        $chat=Chat::where('user_id', $request->userid)->where(  'expert_id', $request->expertid )->first();

            Message::create([
                'user_id'=> $request->userid ,
                'expert_id'=> $request->expertid ,
                'message'=>  $request->message,
                'from_user'=>  $request->fromu,
                'from_expert'=>  $request->frome,
                'chat_id'=>   $chat->id,
            ]);
            return response()->json([
                'message' => 'message has been sent' ,
            ],200);
        }





    public function getchats(Request $request){
        if ( $request->userid==0){
            $c=Chat::where('expert_id',$request->expertid)->with('User')->get();
            return response()->json([
                'chat' =>  $c ,
            ],200);
        }
        else if ( $request->expertid==0){
            $cc=Chat::where('user_id',$request->userid)->with('Expert')->get();
            return response()->json([
                'chat' =>  $cc ,
            ],200);
        }

    }
    public function getmessagesofchat(Request $request){

        $m=Message::where('chat_id',  $request->chatid)->get();
            return response()->json([
                'messagesofchat' =>  $m ,
            ],200);


    }
    public function setchat(Request $request){
        if (Chat::where('user_id', $request->userid)->where(  'expert_id', $request->expertid )->exists()){
           $chat=Chat::where('user_id', $request->userid)->where(  'expert_id', $request->expertid )->first();
            return response()->json([
                'chatid'=>$chat->id ,
                'message' => 'chat already exists' ,
            ],200);
        }
        $chat=Chat::create([
            'user_id'=> $request->userid ,
            'expert_id'=> $request->expertid ,
        ]);
        return response()->json([
            'chatid'=>$chat->id ,
            'message' => 'chat has been created' ,
        ],200);
    }


}
