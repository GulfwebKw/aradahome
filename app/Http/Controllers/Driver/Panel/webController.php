<?php

namespace App\Http\Controllers\Driver\Panel;

use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class webController extends Controller
{
    public function manifest(){

        $settingInfo = \App\Http\Controllers\webController::settings();
        return response([
            'name' => 'Driver of '. $settingInfo->name_en,
            'short_name' => 'Driver',
            'start_url' => '.',
            'display' => 'standalone',
            'background_color' => '#f2f3f8',
            'theme_color' => '#ffff',
            'description' => 'Assign order to driver of '. $settingInfo->name_en,
            'orientation' => 'portrait-primary',
            'prefer_related_applications' => false,
            'icons' => [
                0 => [
                    'src' => route('driver.panel.icon' , [$settingInfo->favicon , 192 , 192]),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                1 => [
                    'src' => route('driver.panel.icon' , [$settingInfo->favicon , 512 , 512]),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
                2 => [
                    'src' => route('driver.panel.icon' , [$settingInfo->favicon , 192 , 192]),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
                3 => [
                    'src' =>  route('driver.panel.icon' , [$settingInfo->favicon , 512 , 512]),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
        ], 200 );
    }

    public function icon($file , $width , $height){
        $imgbig = Image::make(public_path('uploads/logo/' . $file));
        //resize image
        $imgbig->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        return response($imgbig->stream())->header('Content-type','image/png');
    }

    public function serviceWorker(){
        return response(view('driver.serviceWorker'))->header('Content-type','application/javascript');
    }
}