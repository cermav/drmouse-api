<?php

namespace App\Jobs;

use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoctorImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $defaultImagesOld = [
        'profileDoctor01.png',
        'profileDoctor02.png',
        'profileDoctor03.png',
        'profileDoctor04.png',
    ];
    private $defaultImages = [
        'default_001.jpg',
        'default_002.jpg',
        'default_003.jpg',
        'default_004.jpg',
        'default_004.jpg',
        'default_006.jpg',
        'default_007.jpg',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // go throught all doctors
        foreach (Doctor::all() as $doctor) {
            if (
                !$this->imageExists($doctor) ||
                $this->isDefaultImage($doctor->user->avatar)
            ) {
                // update doctor
                $doctor->user->update([
                    'avatar' => $this->getDefaultImage(),
                ]);
            }
        }
    }

    private function isDefaultImage(string $avatar)
    {
        if (
            empty($avatar) ||
            $avatar == "users/default.png" ||
            in_array($avatar, $this->defaultImagesOld)
        ) {
            return true;
        }

        return false;
    }

    private function getDefaultImage()
    {
        return $this->defaultImages[rand(0, 6)];
    }

    private function imageExists(Doctor $doctor)
    {
        try {
            $imageLink =
                'https://api.drmouse.cz/storage/profile/' .
                $doctor->user->avatar;

            // Open file
            $handle = @fopen($imageLink, 'r');

            // Check if file exists
            if (!$handle) {
                return false;
            } else {
                fclose($handle);
                return true;
            }
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
}
