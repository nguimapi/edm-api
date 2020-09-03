<?php
namespace App\Scopes;


use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Carbon;

class FileScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
		$date = Carbon::now();
		if ($model->trashed_at) {
			$date = Carbon::parse($model->trashed_at);
			$date->addMinutes(2);
		}
		
        $builder->whereNull(['trashed_at'])->orWhere('trashed_at', '>', $date->format('Y-m-d H:i:s'));
    }
}
