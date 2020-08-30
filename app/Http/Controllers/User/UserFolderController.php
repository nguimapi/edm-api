<?php

namespace App\Http\Controllers\User;

use App\Folder;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserFolderController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return Response
     */
    public function index(User $user)
    {
        $folders = Folder::confirmed()->get();

        return $this->showAll($folders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, User $user)
    {
        $rules = [
            'folder_id' => 'nullable|exists:files,id',
            'name' => 'required|max:255'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function () use ($request, $user) {
            $data = $request->only(['folder_id', 'name']);

            if ($request->has('folder_id')) {
                $folder = Folder::where([
                    ['folder_id', '=', $request->folder_id],
                    ['name', '=', $request->name]])
                    ->first();

                if ($folder) {
                    return $this->showMessage([
                        'message' => 'failed',
                        'description' => 'A folder with the same name already exist'
                    ], 409);
                }

                $folder = Folder::findOrFail($request->folder_id);
                $data['path'] = $folder->path.'/'.$data['name'];
                $data['relative_path'] = $folder->relative_path.'/'.$data['name'];
            }

            $data['is_folder'] = 1;
            $data['is_confirmed'] = 1;
            $data['path'] = 'uploads';

            if (!$request->has('folder_id') && Storage::exists($request->input('name'))) {
                return $this->showMessage([
                    'message' => 'failed',
                    'description' => 'A folder with the same name already exist'
                ], 409);
            }

            $folder = $user->folders()->create($data);

            Storage::makeDirectory($folder->getOriginal('name'));

            $folder->refresh();

            return $this->showOne($folder);
        });

    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param  \App\Folder $folder
     * @return Response
     */
    public function show(User $user, Folder $folder)
    {
        $folder->files;

        $folder->update(['consulted_at' => now()->format('Y-m-d H:i:s')]);

        return $this->showOne($folder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Folder  $folder
     * @return Response
     */
    public function update(Request $request, Folder $folder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Folder  $folder
     * @return Response
     */
    public function destroy(Folder $folder)
    {
        //
    }
}
