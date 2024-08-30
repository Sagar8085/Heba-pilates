<?php

return [
    'image_disk' => env('ONDEMAND_IMAGE_DISK', 's3'),
    'video_disk' => env('ONDEMAND_VIDEO_DISK', 's3videos'),
    'video_transcode' => env('ONDEMAND_VIDEO_TRANSCODE', true),
];
