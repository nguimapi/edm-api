<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'user_id',
        'parent_id',
        'name',
        'type',
        'size'
    ];

    public function getParentAttribute()
    {
        $this->belongsTo(File::class);
    }

    public function getFolderAttribute()
    {
        $this->belongsTo(Folder::class);
    }

    public function getSizeAttribute($size)
    {
        return intval($size);
    }

    public function getNameAttribute($name)
    {
        return $this->is_folder ? $name : $name.'.'.$this->type;
    }

    public function getTypeAttribute($type)
    {
        return $this->is_folder ? 'folder' : $type;
    }
}
