<?php

namespace App;

use App\Scopes\FolderScope;

class Folder extends File
{
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::addGlobalScope(new FolderScope());
    }

    public function setIsFolderAttribute($is_folder)
    {
        $this->attributes['is_folder'] = 1;
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

}
