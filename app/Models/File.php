<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCreatorAndUpdater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class File extends Model
{
    use HasFactory, HasCreatorAndUpdater,SoftDeletes, NodeTrait;

const DOCUMENTS = 1;
const PHOTOS = 2;
const VIDEOS = 3;
const AUDIOS = 4;
const CONTACTS =5;
const WHATSAPP = 6;


    protected $fillable = [
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function starred()
    {
        return $this->hasOne(StarredFile::class, 'file_id', 'id')
            ->where('user_id', Auth::id());
    }

    public function owner(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['created_by'] == Auth::id() ? 'me' : $this->user->name;
            }
        );
    }

    public function isOwnedBy($userId): bool
    {
        return $this->created_by == $userId;
    }

    public function isRoot()
    {
        return $this->parent_id === null;
    }

    public function get_file_size()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $power = $this->size > 0 ? floor(log($this->size, 1024)) : 0;

        return number_format($this->size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->parent) {
                return;
            }
            $model->path = ( !$model->parent->isRoot() ? $model->parent->path . '/' : '' ) . Str::slug($model->name);
        });

//        static::deleted(function(File $model) {
//            if (!$model->is_folder) {
//                Storage::delete($model->storage_path);
//            }
//        });
    }

    public function moveToTrash()
    {
        $this->deleted_at = Carbon::now();

        return $this->save();
    }

    public function deleteForever()
    {
        $this->deleteFilesFromStorage([$this]);
        $this->forceDelete();
    }

    public function deleteFilesFromStorage($files)
    {
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->deleteFilesFromStorage($file->children);
            } else {
                Storage::delete($file->storage_path);
            }
        }
    }

    public function countDocuments()
    {
        return $this->countByType(self::DOCUMENTS);
    }

    public function countPhotos()
    {
        return $this->countByType(self::PHOTOS);
    }

    public function countVideos()
    {
        return $this->countByType(self::VIDEOS);
    }

    public function countAudios()
    {
        return $this->countByType(self::AUDIOS);
    }

    public function countContacts()
    {
        return $this->countByType(self::CONTACTS);
    }

    public function countWhatsapp()
    {
        return $this->countByType(self::WHATSAPP);
    }
    public function sizeDocuments()
{
    return $this->sizeByType(self::DOCUMENTS);
}

public function sizePhotos()
{
    return $this->sizeByType(self::PHOTOS);
}

public function sizeVideos()
{
    return $this->sizeByType(self::VIDEOS);
}

public function sizeAudios()
{
    return $this->sizeByType(self::AUDIOS);
}

public function sizeContacts()
{
    return $this->sizeByType(self::CONTACTS);
}

public function sizeWhatsapp()
{
    return $this->sizeByType(self::WHATSAPP);
}


    protected function countByType($type)
    {
        return $this->where('created_by', Auth::id())->where('is_folder', 0)->where('product_id', $type)->count();
    }
    protected function sizeByType($type)
    {
        return $this->where('created_by', Auth::id())->where('is_folder', 0)->where('product_id', $type)->sum('size');
    }
    // public static function getSharedWithMe()
    // {
    //     return File::query()
    //         ->select('files.*')
    //         ->join('file_shares', 'file_shares.file_id', 'files.id')
    //         ->where('file_shares.user_id', Auth::id())
    //         ->orderBy('file_shares.created_at', 'desc')
    //         ->orderBy('files.id', 'desc');
    // }

    // public static function getSharedByMe()
    // {
    //     return File::query()
    //         ->select('files.*')
    //         ->join('file_shares', 'file_shares.file_id', 'files.id')
    //         ->where('files.created_by', Auth::id())
    //         ->orderBy('file_shares.created_at', 'desc')
    //         ->orderBy('files.id', 'desc')
    //         ;
    // }
}
