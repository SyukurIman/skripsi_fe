<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\dummy_token;
use App\Models\Key_Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeetingController extends Controller
{
    public function createMeeting(Request $request) {

        $METERED_DOMAIN     = env('METERED_DOMAIN');
        $METERED_SECRET_KEY = env('METERED_SECRET_KEY');

        $response = Http::post("https://{$METERED_DOMAIN}/api/v1/room?secretKey={$METERED_SECRET_KEY}", [
            'autoJoin' => true
        ]);

        $roomName = $response->json("roomName");
        return redirect("/meeting/{$roomName}");
    }

    public function validateMeeting(Request $request) {
        $METERED_DOMAIN     = env('METERED_DOMAIN');
        $METERED_SECRET_KEY = env('METERED_SECRET_KEY');
        $meetingId          = $request->input('meetingId');

        $response = Http::get("https://{$METERED_DOMAIN}/api/v1/room/{$meetingId}?secretKey={$METERED_SECRET_KEY}");
        $roomName = $response->json("roomName");


        if ($response->status() === 200)  {
            return redirect("/meeting/{$roomName}");
        } else {
            return redirect("/?error=Invalid Meeting ID");
        }
    }

    function meeting($number_key){
        $check_data = Dummy_token::where('number_key', $number_key)->first();
        $token = $check_data->token;

        $data = ['data_key' => $number_key, 'token' => $token];
        return view('meeting', $data);
    }

    function meeting_post(Request $request){
        try {
            $token = $request->bearerToken();
            $user = JWTAuth::parseToken()->getPayload();
            $check = User::where('id', $user->get('sub'))->get();

            $data_link = Key_Link::where('id_user', $check[0]->id)->first();
            if ($check[0]->status_role == 1) {
                $check = Dokter::where('id_user', $check[0]->id)->get();
                $data_link = Key_Link::where('id_dokter', $check[0]->id)->first();
            }

            $check_data = dummy_token::where('number_key', $data_link->number_key)->first();
            if(!isset($check_data)){
                $create = new Dummy_token();
                $create->number_key = $data_link->number_key;
                $create->token      = $token;
                $create->save();
            } else {
                $check_data->token = $token;
                $check_data->update();
            }
            return response()->json(['token' => $check_data]);
        } catch (\Throwable $th) {
            return response()->json(['status_error' => $th]);
        }

    }
}
