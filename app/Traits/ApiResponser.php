<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    private $reservedKeyWords = [
        'sort_by',
        'page',
        'per_page',
        'sort_by_desc',
    ];

    protected function successResponse($data,$code = 200)
    {
        return response()->json($data,$code);
    }

    protected function errorResponse($message,$code)
    {
        return response()->json(['error'=>$message,'code'=>$code],$code);
    }
    protected function showAll(Collection $collection, $code = 200, $filter = true, $sort = true, $paginate = true, $cache = false)
    {
        if($collection->isEmpty()){
            return $this->successResponse($collection,$code);
        }

        if($filter) {
            $collection = $this->filterData($collection);
        }

        if($sort) {
            $collection = $this->sortData($collection);
            $collection = $this->sortDataDesc($collection);
        }

        if($paginate) {
            $collection = $this->paginateDataCollection($collection);
        }

        if($cache) {
            $collection = $this->cacheResponse($collection);
        }

        return $this->showMessage($collection,$code);
    }


    protected function showAlls(Collection $collection,$code=200)
    {
        if($collection->isEmpty()){
            return $this->successResponse($collection,$code);
        }

        return $this->showMessage($collection,$code);

    }

    protected function showOne(Model $instance,$code = 200)
    {
        return $this->successResponse($instance,$code);
    }

    protected function showMessage($message,$code = 200)
    {
        return $this->successResponse(['data'=>$message],$code);
    }

    public function filterData(Collection $collection)
    {

        foreach (request()->query() as $query => $value){
            if(isset($query, $value) && !in_array($query, $this->reservedKeyWords)){
                $collection = $collection->where($query,strtolower($value));
            }
        }

        return $collection;

    }

    protected function sortData(Collection $collection)
    {
        if(request()->has('sort_by')){
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }


    protected function sortDataDesc(Collection $collection)
    {
        if(request()->has('sort_by_desc')){
            $attribute = request()->sort_by_desc;
            $collection = $collection->sortByDesc->{$attribute};
        }
        return $collection;
    }


    protected function paginateDataCollection(Collection $collection)
    {
        if(request()->has('per_page') && request()->per_page == '*'){
            return $collection;
        }

        $rules = [
            'per_page'=>'integer|min:1|max:50',
        ];

        $validator = Validator::make(request()->all(),$rules);

        $validator->validate();

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;

        if(request()->has('per_page')){
            $perPage = (int) request()->per_page;
        }

        $results = $collection->slice(($page-1) * $perPage,$perPage)->values();

        $paginated = new LengthAwarePaginator($results,$collection->count(),$perPage,$page,[
            'path'=>LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();

        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = $queryParams = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember($fullUrl,300,function () use ($data){

            return $data;
        });
    }



    /**
     * @param Collection $products
     * @param array $date_filter
     * @return Collection
     */
    protected function filterByDate(Collection $collection,array $date_filter,$criteria )
    {

        if($date_filter ){
            if(count($date_filter)==3 ){

                $date_start=$date_filter[1];
                $date_end=$date_filter[2];
            }else{
                $date_start=$date_filter[1];
            }



            switch ($date_filter[0]) {
                case "isDateEquals":

                    return $collection->where($criteria,$date_start);
                    break;
                case "isDateOnOrAfter":
                    return  $collection->where($criteria,'>=',$date_start);
                    break;
                case "isDateAfter":
                    return $collection->where($criteria,'>',$date_start);
                    break;
                case "isDateBefore":
                    return $collection->where($criteria,'<',$date_start);
                    break;
                case "isDateBeforeOrOn":
                    return $collection->where($criteria,'<=',$date_start);
                    break;
                case "DateBetween":
                    return   $collection->whereBetween($criteria,[$date_start,$date_end]);
                    break;
                case "dateInTheLast":
                    $type=$date_filter[2];
                    $number  = $date_filter[1];

                    if($type==="month"){
                        return $collection->where("number_month",'=', $number);
                    } else{
                        return $collection->where("number_day",'=',$number);
                    }

                default:
                    return $collection;
            }


        }

    }



}
