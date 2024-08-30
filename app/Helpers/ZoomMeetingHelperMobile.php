<?php

namespace App\Helpers;

use App\Models\SessionZoomLink;
use App\Models\TrainerZoomIds;
use GuzzleHttp\Client;
use Log;

/**
 * trait ZoomMeetingTrait
 */
trait ZoomMeetingHelperMobile
{
    public $client;
    public $jwt;
    public $headers;

    public function __construct()
    {
        $this->client = new Client();
        $this->jwt = $this->generateZoomToken();
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->jwt,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function generateZoomToken()
    {
        $key = env('ZOOM_API_KEY', '');
        $secret = env('ZOOM_API_SECRET', '');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];

        return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    }

    private function retrieveZoomUrl()
    {
        return env('ZOOM_API_URL', '');
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new \DateTime($dateTime);

            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());

            return '';
        }
    }

    public function create($data)
    {

        $trainerZoom = TrainerZoomIds::where('trainer_id', $data['trainer_id'])->first();
        $sessionZoom = SessionZoomLink::where('session_id', $data['session_id'])->first();

        if (!$sessionZoom || $data['regenerate']) {
            $path = 'users/' . $trainerZoom->zoom_user_id . '/meetings';
            $url = $this->retrieveZoomUrl();

            $body = [
                'headers' => $this->headers,
                'body' => json_encode([
                    'topic' => "Virtual Training Session",
                    'type' => self::MEETING_TYPE_INSTANT,
                    'start_time' => $this->toZoomTimeFormat(date("Y-m-d H:i:s")),
                    'duration' => $data['duration'],
                    'agenda' => null,
                    'timezone' => 'London/Europe',
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => false,
                        'waiting_room' => true,
                    ],
                ]),
            ];

            $response = $this->client->post($url . $path, $body);

            $response = json_decode($response->getBody(), true);

            if ($sessionZoom) {
                $sessionZoom->link = $response["join_url"];
                $sessionZoom->save();
            } else {
                $sessionZoom = SessionZoomLink::create([
                    "session_id" => $data["session_id"],
                    "link" => $response["join_url"],
                ]);
            }
        }

        return response()->json($sessionZoom);
    }

    // Not used - here for future reference
    public function update($id, $data)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->headers,
            'body' => json_encode([
                'topic' => $data['topic'],
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration' => $data['duration'],
                'agenda' => (!empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone' => 'Asia/Kolkata',
                'settings' => [
                    'host_video' => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room' => true,
                ],
            ]),
        ];
        $response = $this->client->patch($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data' => json_decode($response->getBody(), true),
        ];
    }

    // Not used - future reference
    public function get($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();
        $this->jwt = $this->generateZoomToken();
        $body = [
            'headers' => $this->headers,
            'body' => json_encode([]),
        ];

        $response = $this->client->get($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data' => json_decode($response->getBody(), true),
        ];
    }

    /**
     * @param string $id
     *
     * @return bool[]
     */
    public function delete($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();
        $body = [
            'headers' => $this->headers,
            'body' => json_encode([]),
        ];

        $response = $this->client->delete($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
        ];
    }
}
