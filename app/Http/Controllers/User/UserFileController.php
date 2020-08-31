<?php

namespace App\Http\Controllers\User;

use App\File;
use App\Folder;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserFileController extends ApiController
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
            'folder_id' => 'nullable',
            'is_folder' => 'boolean',
            'name' => 'required',
            'file' => 'required_if:is_folder,0|file',
            'folderCode' => 'nullable|string',
            'code' => 'required',
            'batch' => 'required',
            'originalType' => 'nullable',
            'relativePath' => 'nullable',
        ];

        $this->validate($request, $rules);

        return DB::transaction(function () use ($request, $user){
            $data = $request->except(['id']);

            if ($request->is_folder) {
                if ($request->folder_id) {
                    $folder = Folder::findOrFail($request->folder_id);
                    $data['path'] = $folder->path.'/'.$data['name'];
                    $data['relative_path'] = $folder->path;
                } else {
		
				   $data['path'] = $data['name'];
				   $data['relative_path'] = null;

				}

                $folder = $user->folders()->create($data);

                $folder->refresh();

                return $this->showOne($folder);
            }

            
            $file = $request->file('file');

            $path = $request->name;
			
			if ($request->folder_id) {
				$folder = Folder::findOrFail($request->folder_id);
				$path = $folder->path.'/'.$request->name;
			}
			
			if (Storage::exists($path)) {
                return $this->showMessage([
                    'message' => 'failed',
                    'description' => 'A fine with the same name already exist'
                ], 409);
            }

            Storage::put($path ?? $file->getClientOriginalName(), file_get_contents($file));
			
            $relative_path = explode('/', $request->relativePath);

            array_pop($relative_path);

            $data['type'] = $file->clientExtension();
            $data['size'] = $file->getSize();
            $data['path'] = $path;
            $data['relative_path'] = implode('/', $relative_path);
            $data['folder_id'] = $request->has('folder_id') ? $request->folder_id : null;

            $file = $user->files()->create($data);

            $file->refresh();

            return $this->showOne($file);

        });
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

        $files = $user->files()->where('batch', '=', $request->batch);

        $files->update(['is_confirmed' => 1]);

        return $this->successResponse('success');

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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @param File $file
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user, File $file)
    {
        $rules = [
            'folder_id' => 'nullable',
            'name' => 'nullable',
            'is_archived' => 'nullable|boolean',
            'is_trashed' => 'nullable|boolean',
            'consulted_at' => 'nullable|date',
        ];

        $this->validate($request, $rules);
		
	
        return DB::transaction(function () use ($file, $request) {
             $data = $request->only([
                'folder_id',
                'name',
                'is_archived',
                'is_trashed',
                'consulted_at'
            ]);
            if ($request->name) {

			    $currentPath = $file->path;
                $newName = $request->name;
				$newPath = $file->relative_path.'/'.$request->name;
				
                if(Storage::exists($file->path)){
                    Storage::move($currentPath, $newPath);
                }
				
				$data['path'] = $newPath;

            }

            $file->update($data);

            return $this->showOne($file);
        });


    }

    public function editFile(File $file)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(User $user, File $file)
    {
        //
    }
}
