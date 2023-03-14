<?php

namespace App\Http\Controllers;

use Response;
use Image;

class FileController extends Controller
{

	public function show($main, $file)
	{
		//For Files and Imgs
		if (!empty($file)) {
			$path = base_path('public/uploads/' . $main . '/' . $file);


			if (file_exists($path)) {
				return Image::make($path)->response();
			} else {
				$path = base_path('public/uploads/no-image.png');
				return Image::make($path)->response();
			}
		} else {
			$path = base_path('public/uploads/no-image.png');
			return Image::make($path)->response();
		}
	}

    // for pos - no file
    public function showFile( $file)
    {
        //For Files and Imgs
        if (!empty($file)) {
            $path = base_path('public/uploads/'  . $file);


            if (file_exists($path)) {
                return Image::make($path)->response();
            } else {
                $path = base_path('public/uploads/no-image.png');
                return Image::make($path)->response();
            }
        } else {
            $path = base_path('public/uploads/no-image.png');
            return Image::make($path)->response();
        }
    }


	// Show Thumbnail for Images
	public function showthumb($main, $thumb = '', $file)
	{
		if (!empty($file)) {

			if (!empty($thumb)) {
				$path = base_path('public/uploads/' . $main . '/' . $thumb . '/' . $file);
			} else {
				$path = base_path('public/uploads/' . $main . '/' . $file);
			}

			if (file_exists($path)) {
				return Image::make($path)->response();
			} else {
				$path = base_path('public/uploads/no-image.png');
				return Image::make($path)->response();
			}
		} else {
			$path = base_path('public/uploads/no-image.png');
			return Image::make($path)->response();
		}
	}

	//For Videos
	public function showvideo($file)
	{


		if (!empty($file)) {
			$path = base_path('public/uploads/slideshow/' . $file);

			if (file_exists($path)) {
				$response = Response::make($path, 200);
				$response->header('Content-Type', 'video/mp4');
				return $response;
			} else {
				$path = base_path('public/uploads/no-image.png');
				return Image::make($path)->response();
			}
		} else {
			$path = base_path('public/uploads/no-image.png');
			return Image::make($path)->response();
		}
	}
}
