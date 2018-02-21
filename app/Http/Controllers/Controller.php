<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * This method will accept a query and return the a list collection compatible with TableView component
     * @param Any LaravelBuilder Type $query
     */
    public static function VueTableListResult($query)
    {
        $result = $query;
    	if(!empty(request()->sort))
    	{
            // handle sorting
            $sorts = explode(",", request()->sort);

            foreach($sorts as $sort)
            {
                $sorting = explode('|', $sort);

                $result = $query->orderBy($sorting[0], $sorting[1]);
            }
    	}

        if(!empty(request()->filter))
        {
            // handle searching
            $searchables = explode(",", request()->searchables);

            $result = $result->where(function($query) use ($searchables){
                foreach($searchables as $searchable)
                {
                    $query = $query->orWhere($searchable, 'LIKE', '%' . request()->filter . '%');
                }
            });
            
        }

    	return $result->paginate(10);
    }
}
