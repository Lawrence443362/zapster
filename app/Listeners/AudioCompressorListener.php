<?php

namespace App\Listeners;

use App\Events\AudioAttachedToPostEvent;
use App\Models\AudioFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use App\Services\AudioFile\AudioFileCompressorService;
use Illuminate\Support\Facades\Log;


class AudioCompressorListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 1;
    public $queue = 'job_audio_compress_listeners';

    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(AudioAttachedToPostEvent $event): void
    {
        try {
            $audioFile = AudioFile::findOrFail($event->audioFileID);
            $compressor = App::make(AudioFileCompressorService::class);
            $result = $compressor->compress($audioFile);
            $audioFile->compressed($result['disc'], $result['folder'])->save();

        } catch (\Exception $e) {
            Log::error("Audio Compress Job Failed [{$event->audioFileID}]: {$e->getMessage()}");
            throw new \RuntimeException("FFmpeg failed. Check logs.");
        }
    }

    public function viaQueue(): string
    {
        return 'audio_compressing_queue';
    }
}
