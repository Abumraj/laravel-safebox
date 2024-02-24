<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\AddToFavouritesRequest;
use App\Http\Requests\FilesActionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\TrashFilesRequest;
use App\Http\Resources\FileResource;
use App\Jobs\UploadFileToCloudJob;
use App\Models\File;
use App\Models\StarredFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function myFiles(Request $request, string $folder = null)
    {

        $result = [];
        $search = $request->get('search');

        if ($folder) {
            $folder = File::query()
                ->where('created_by', Auth::id())
                ->where('path', $folder)
                ->firstOrFail();
        }
        if (!$folder) {
            $folder = $this->getRoot();
        }

        $favourites = (int)$request->get('favourites');

        $query = File::query()
            ->select('files.*')
            ->with('starred')
            ->where('created_by', Auth::id())
            ->where('_lft', '!=', 1)
            ->orderBy('is_folder', 'desc')
            ->orderBy('files.created_at', 'desc')
            ->orderBy('files.id', 'desc');

        if ($search) {
            $query->where('name', 'like', "%$search%");
        } else {
            $query->where('parent_id', $folder->id);
        }

        if ($favourites === 1) {
            $query->join('starred_files', 'starred_files.file_id', '=', 'files.id')
                ->where('starred_files.user_id', Auth::id());
        }

        $files = $query->paginate(30);

        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        $folder = new FileResource($folder);
        $result['files'] = $files;
        $result['folders'] = $folder;
        $result['ancestors'] = $ancestors;

        return $result;
    }

    public function recentFiles(){
        // $folder = $this->getRoot();

        $query = File::query()
            ->select('files.*')
            ->with('starred')
            ->where('created_by', Auth::id())
            ->whereNotNull('parent_id')
            ->where('_lft', '!=', 1)
            ->orderBy('files.created_at', 'desc')
            ->orderBy('files.id', 'desc');
            $files = $query->paginate(30);
        $files = FileResource::collection($files);
               return $files;
    }
    public function filesByType($productId){
        // $folder = $this->getRoot();
        Log::debug("$productId");
       $productmarker  = 0;
       if ($productId == 'Files') {
        $productmarker = File::DOCUMENTS;
    } else if ($productId == 'Photos') {
        $productmarker = File::PHOTOS;
    } else if ($productId == 'Videos') {  // <-- Corrected the comparison operator from "=" to "=="
        $productmarker = File::VIDEOS;
    } else if ($productId == 'Audios') {
        $productmarker = File::AUDIOS;
    } else if ($productId == 'Contacts') {
        $productmarker = File::CONTACTS;
    } else if ($productId == 'Whatsapp') {
        $productmarker = File::WHATSAPP;
    }
          Log::debug("$productmarker");
        $query = File::query()
            ->select('files.*')
            ->with('starred')
            ->where('created_by', Auth::id())
            ->whereNotNull('parent_id')
            ->where('product_id', $productmarker)
            ->where('_lft', '!=', 1)
            ->orderBy('files.created_at', 'desc')
            ->orderBy('files.id', 'desc');
            $files = $query->paginate(30);
        $files = FileResource::collection($files);
               return $files;
    }

    public function trash(Request $request)
    {
        $search = $request->get('search');
        $query = File::onlyTrashed()
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('deleted_at', 'desc')
            ->orderBy('files.id', 'desc');

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $files = $query->paginate(10);

        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        return $files;
    }



    public function createFolder(StoreFolderRequest $request)
    {

        $result  = collect();

        // return Auth::user();

        $data = $request->validated();
        $parent = $request->parent;
        // $folderId = $request->folderId;

        if (!$parent) {
            $parent = $this->getRoot();
        }

            $file = new File();
            $file->is_folder = 1;
            $file->name = $data['name'];

            $parent->appendNode($file);

    $result->put('message', 'Folder created successfully');


        return $result;
    }

    public function renameFolder(Request $request)
    {
        $result =  collect();
        $validated = $request->validate([
            'name' => 'required|string',
            'folderId' => 'required'
        ]);
        if($validated){
            $file = File::find($request->folderId)->update([
                'name' =>$request->name ]);
                $message = 'Folder renamed successfully';
            }else {
                $message = "An error occurred";
            }

            $result->put('message', $message);

        return $result;
    }

    public function store(StoreFileRequest $request)
    {
        $files = $request;
    //    return Log::debug($files);

        $data = $request->validated();
        $product_id = 1;

        $parent = $request->parent;
        $user = $request->user();
        if ($request->productId == 'file') {
            $product_id = File::DOCUMENTS;
            # code...
        } else if ($request->productId == 'photo') {
            $product_id = File::PHOTOS;
            # code...
        }elseif ($request->productId == 'video') {
            $product_id = File::VIDEOS;
            # code...
        }elseif ($request->productId == 'audio') {
            $product_id = File::AUDIOS;
            # code...
        }elseif ($request->productId == 'contact') {
            # code...
            $product_id = File::CONTACTS;
        } elseif($request->productId == 'whatsapp'){
            $product_id = File::WHATSAPP;
            # code...
        }
        
        // $fileTree = $request->file_tree;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        // if (!empty($fileTree)) {
        //     $this->saveFileTree($fileTree, $parent, $user, $product_id);
        // }


        // else {
            foreach ($request->allFiles() as $file) {
                /** @var \Illuminate\Http\UploadedFile $file */

                $this->saveFile($file, $user, $parent, $product_id);
            }
        // }
    }

    private function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }

    public function saveFileTree($fileTree, $parent, $user, $parent_id)
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user, $parent_id);
            } else {

                $this->saveFile($file, $user, $parent, $parent_id);
            }
        }
    }

    public function destroy(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if ($data['all']) {
            $children = $parent->children;

            foreach ($children as $child) {
                $child->moveToTrash();
            }
        } else {
            foreach ($data['ids'] ?? [] as $id) {
                $file = File::find($id);
                if ($file) {
                    $file->moveToTrash();
                }
            }
        }

        return to_route('myFiles', ['folder' => $parent->path]);
    }

    public function download(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }

        if ($all) {
            $url = $this->createZip($parent->children);
            $filename = $parent->name . '.zip';
        } else {
            [$url, $filename] = $this->getDownloadUrl($ids, $parent->name);
        }

        return [
            'url' => $url,
            'filename' => $filename
        ];
    }

    /**
     *
     *
     * @param $file
     * @param $user
     * @param $parent
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     */
    private function saveFile($file, $user, $parent, $parent_id): void
    {
        $path = $file->store('/files/' . $user->id, 'local');

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();
        $model->uploaded_on_cloud = 0;
        $model->product_id = $parent_id;

        $parent->appendNode($model);

        UploadFileToCloudJob::dispatch($model);
    }

    public function createZip($files): string
    {
        $zipPath = 'zip/' . Str::random() . '.zip';
        $publicPath = "$zipPath";

        if (!is_dir(dirname($publicPath))) {
            Storage::disk('public')->makeDirectory(dirname($publicPath));
        }

        $zipFile = Storage::disk('public')->path($publicPath);

        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }

        $zip->close();

        return asset(Storage::disk('local')->url($zipPath));
    }

    private function addFilesToZip($zip, $files, $ancestors = '')
    {
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancestors . $file->name . '/');
            } else {
                $localPath = Storage::disk('local')->path($file->storage_path);
                if ($file->uploaded_on_cloud == 1) {
                    $dest = pathinfo($file->storage_path, PATHINFO_BASENAME);
                    $content = Storage::get($file->storage_path);
                    Storage::disk('public')->put($dest, $content);
                    $localPath = Storage::disk('public')->path($dest);
                }

                $zip->addFile($localPath, $ancestors . $file->name);
            }
        }
    }

    public function restore(TrashFilesRequest $request)
    {
        $data = $request->validated();
        if ($data['all']) {
            $children = File::onlyTrashed()->get();
            foreach ($children as $child) {
                $child->restore();
            }
        } else {
            $ids = $data['ids'] ?? [];
            $children = File::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($children as $child) {
                $child->restore();
            }
        }

        return to_route('trash');
    }

    public function deleteForever(TrashFilesRequest $request)
    {
        $result = collect();
        $data = $request->validated();
        // if ($data['all']) {
        //     $children = File::onlyTrashed()->get();
        //     foreach ($children as $child) {
        //         $child->deleteForever();
        //     }
        // } else {
            $ids = $data['ids'] ?? [];
            $children = File::whereIn('id', $ids)->get();
            foreach ($children as $child) {
                $child->deleteForever();
            }
        // }
        $result->put('message', 'files deleted');

        return $result;
    }

    public function addToFavourites(AddToFavouritesRequest $request)
    {
        $result = collect();
        $data = $request->validated();

        $id = $data['id'];
        $file = File::find($id);
        $user_id = Auth::id();

        $starredFile = StarredFile::query()
            ->where('file_id', $file->id)
            ->where('user_id', $user_id)
            ->first();

        if ($starredFile) {
            $starredFile->delete();
            $message = 'Unstarred successfully';
        } else {
            StarredFile::create([
                'file_id' => $file->id,
                'user_id' => $user_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $message = 'Starred successfully';
        }
        $result->put('message', $message);

        return $result;
    }


    private function getDownloadUrl(array $ids, $zipName)
    {
        if (count($ids) === 1) {
            $file = File::find($ids[0]);
            if ($file->is_folder) {
                if ($file->children->count() === 0) {
                    return [
                        'message' => 'The folder is empty'
                    ];
                }
                $url = $this->createZip($file->children);
                $filename = $file->name . '.zip';
            } else {
                $dest = pathinfo($file->storage_path, PATHINFO_BASENAME);
                if ($file->uploaded_on_cloud) {
                    $content = Storage::get($file->storage_path);
                } else {
                    $content = Storage::disk('local')->get($file->storage_path);
                }

                Log::debug("Getting file content. File:  " .$file->storage_path).". Content: " .  intval($content);

                $success = Storage::disk('public')->put($dest, $content);
                Log::debug('Inserted in public disk. "' . $dest . '". Success: ' . intval($success));
                $url = asset(Storage::disk('public')->url($dest));
                Log::debug("Logging URL " . $url);
                $filename = $file->name;
            }
        } else {
            $files = File::query()->whereIn('id', $ids)->get();
            $url = $this->createZip($files);

            $filename = $zipName . '.zip';
        }

        return [$url, $filename];
    }
}
