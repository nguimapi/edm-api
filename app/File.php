<?php

namespace App;

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
            'creation_date_human'
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
        'consulted_at'
    ];

    public function getCreationDateHumanAttribute()
    {
        $dateHour = Carbon::parse($this->created_at);
        $date = Carbon::parse($this->creation_date);

        if (Carbon::today()->equalTo($date) || Carbon::yesterday()->equalTo($date) ||
            $dateHour->isBetween(Carbon::now()->subDays(7) ,Carbon::now())) {
            return ucfirst(explode(' ', $dateHour->calendar())[0]);
        }

        return $this->created_at_human;
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

    public function getTypeAttribute($type)
    {
        return $this->is_folder ? 'folder' : $type;
    }

}
