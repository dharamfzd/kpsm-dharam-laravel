<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\FileUpload;
use App\Models\User;


class FileUploadController extends Controller
{
    /**
     * Store a newly file resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function FileUpload(Request $request)
    {

        $validator = Validator::make($request->all(), [
          'title' => 'required|string',
          'file' => 'required|mimes:pdf,mp4,mov,wmv,flv,avi,avchd,webm,mkv',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors()
          ], 422);
        }

        $type = $request->file->extension();
        $type = $type == 'pdf' ? $type : 'video';
        $path = $request->file->store('upload/'.$type, 'public');

        $file = FileUpload::create([
          'user_id' => Auth::id(),
          'title'   => $request->title,
          'type'    => $type,
          'path'    => $path,
        ]);

        return response()->json([
          'success' => true,
          'message' => ucwords($type).' uploaded succesfully.',
        ], 200);
    }

    /**
     * Display the all files resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allFiles()
    {
        $files = FileUpload::where('user_id', Auth::id())->get();;

        foreach ($files as  $value) {
          $value['type'] = ucwords($value->type);
          $value['path'] = url('storage/'.$value->path);
        }

        return response()->json([
          'success' => true,
          'message' => 'All files fatched succesfully.',
          'data'    => $files
        ], 200);
    }

}
