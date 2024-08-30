<?php

namespace App\Jobs;

use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $schedule;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = ElasticTranscoderClient::factory(array(
            'region' => 'eu-west-1',
            'version' => '2012-09-25',
        ));

        $videoPath = $this->schedule->video_path;

        $results = $client->createJob([
            'PipelineId' => '1609942603418-abeee0',
            'Input' => [
                'Key' => $videoPath,
            ],
            'Output' => [
                'Key' => 'streaming/output/hebapilates/' . $this->schedule->id . '/video',
                'SegmentDuration' => '10',
                'PresetId' => '1351620000001-200010',
            ],
        ]);

        //TODO UNCOMMENT THIS
        // $this->schedule->update([
        //     'video_path' => 'streaming/output/' . $this->schedule->id . '.m3u8'
        // ]);
    }
}
