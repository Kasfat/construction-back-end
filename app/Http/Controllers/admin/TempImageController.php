<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $image = $request->image;

        $ext = $image->getClientOriginalExtension();
        $imageName = strtotime(now()) . '.' . $ext;

        $model = new TempImage();
        $model->name = $imageName;
        $model->save();


        $image->move(public_path('uploads/temp'), $imageName);


        // Create Thumbnail
        $sourchPath = public_path('uploads/temp/' . $imageName);
        $destPath = public_path('uploads/temp/thumb/' . $imageName);
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($sourchPath);
        $image->coverDown(300, 300);
        $image->save($destPath);

        return response()->json([
            'status' => true,
            'data' => $model,
            'message' => 'You have successfully uploaded an image'
        ], 200);
    }
}
