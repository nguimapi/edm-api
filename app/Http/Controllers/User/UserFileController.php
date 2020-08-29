<?php

namespace App\Http\Controllers\User;

use App\File;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserFileController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return Response
     */
    public function index(User $user)
    {
        $files = File::confirmed()->whereNull('folder_id')->get();
        return $this->showAll($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, User $user)
    {
        $rules = [
            'folder_id' => 'nullable|exists:files,id',
            'is_folder' => 'in:0,1',
            'name' => 'required',
            'file' => 'required_if:is_folder,1|file',
            'folderCode' => 'nullable|string',
            'code' => 'required',
            'batch' => 'required',
            'originalType' => 'nullable|string',
            'relativePath' => 'required_if:is_folder,0|string',
        ];

        $this->validate($request, $rules);

        $data = $request->except(['id']);

        if ($request->is_folder) {
            $folder = $user->folders()->create($data);

            return $this->showOne($folder);
        }

        $folder = $user->folders()
            ->where([
                ['batch', '=' , $request->batch],
                ['code', '=', $request->folderCode]
            ])->first();

        $file = $request->file('file');
        $data['type'] = $file->clientExtension();
        $data['link'] = $file->store($request->relativePath);
        $data['folder_id'] = $folder->id;

        $files = $user->files()->create($data);

        return $this->showOne($files);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirmUpload(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'batch'  => 'required|exists:files,batch',
        ];
        $this->validate($request, $rules);

        $user =  User::findOrfail($request->user_id);

        $files = $user->files()->where(['batch', '=', $request->batch]);

        $files->each(function (File $file) {
            $file->update(['is_confirmed' => 1]);
        });

        $files = $files->get();

        return $this->showAll($files);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
