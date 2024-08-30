<?php

namespace App\Http\Controllers;

use App\Models\AwsSnsRequest;
use App\Models\OnDemand;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AwsSnsController extends Controller
{
    public function transcoderComplete(Request $request)
    {
        $json = json_decode($request->getContent());
        $decoded = json_decode($json->Message);

        if ($json->Type == "SubscriptionConfirmation") {

            $client = new Client();
            $res = $client->get($json->SubscribeURL);

        }

        if (strpos($decoded->input->key, 'hebapilates') !== false) {
            $sns = AwsSnsRequest::create([
                'json' => $request,
            ]);

            if ($json->Type == "SubscriptionConfirmation") {

                // $client = new Client();
                // $res = $client->get($json->SubscribeURL);

            } elseif ($json->Type == "Notification") {
                $json->Message = json_decode($json->Message);

                foreach ($json->Message->outputs as $item) {
                    $transcoded_path = $item->key . ".m3u8";
                    $raw_path = $json->Message->input->key;
                    $asset = OnDemand::where('video_path', $raw_path)->first();
                    $asset->video_path = $transcoded_path;
                    $asset->processed = 1;
                    $asset->save();
                }
            }
        }

        return response()->json(["message" => "Success"], 200);
    }
}
