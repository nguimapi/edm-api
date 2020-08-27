<?php

namespace App\Http\Controllers\User;

use App\Folder;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserFolderController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return Response
     */
    public function index(User $user)
    {
        $folders = Folder::all();

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

        $data = $request->only(['folder_id', 'name']);

        $data['is_folder'] = 1;

        $folder = $user->folders()->create($data);

        $folder->refresh();

        return $this->showOne($folder);

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
