<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class File extends Model
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->append([
            'parents',
            'creation_date',
            'display_size',
            'link'
        ]);
    }

    protected $table = 'files';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'user_id',
        'folder_id',
        'name',
        'type',
        'size',
        'is_archived',
        'is_trashed',
        'is_folder',
        'consulted_at',
        'code',
        'path',
        'batch',
        'is_confirmed'
    ];

    public function getLinkAttribute()
    {
        return !$this->is_folder ? url("uploads/{$this->path}") : null;
    }

    public function getDisplaySizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;

    }

    public function getCreationDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function getParentsAttribute()
    {
        $parents = [];
        $this->getParent($this, $parents);

        return $parents;

    }

    public function getParent(File &$file, &$array)
    {
        if ($file->folder_id) {
            $folder = Folder::find($file->folder_id);
            array_unshift($array, $folder);

            $this->getParent($folder, $array);
        }
    }

    public function getSizeAttribute($size)
    {
        return intval($size);
    }

    public function getNameAttribute($name)
    {
        return $this->is_folder ? $name : $name.'.'.$this->type;
    }

    public function scopeConfirmed(Builder $q)
    {
        return $q->where('is_confirmed', '=', 1);
    }

}
