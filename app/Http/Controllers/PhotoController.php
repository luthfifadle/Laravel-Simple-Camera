<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function camera()
    {
        $photos = Photo::orderBy('id', 'DESC')->get();
        return view('photos.index', compact('photos'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);


        $imageName = time() . '.png';
        $path = public_path('images/' . $imageName);
        file_put_contents($path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image)));

        Photo::create(['image' => $imageName]);

        return response()->json(['message' => 'Image uploaded successfully']);
    }
}

