<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * This method will accept a query and return the a list collection compatible with TableView component
     * @param Any LaravelBuilder Type $query
     */
    public static function VueTableListResult($query, $paginate = 50)
    {
        $result = $query;
    	if(!empty(request()->sort))
    	{
            $query->{$query->unions ? 'unionOrders' : 'orders'} = null;
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

        if(!empty(request()->dateFilterKey))
        {
            // handle date filtering
            $dateKey = request()->dateFilterKey;
            $result = $result->whereDate( $dateKey, '>=', request()->start )
                            ->whereDate( $dateKey, '<=', request()->end );
        }

    	return $result->paginate($paginate);
    }

    public function redirectParcelCenter($request, $center_target_url, $client_target_url = '/')
    {
        $client = new Client();
        $url = env('PARCELHUB_CENTER_URL') . '/oauth/authenticate?';
        $callback_url = env('APP_URL') . '/oauth/callback';

        //State for callback to validate
        $state = Str::random(40);
        session([
            'state' => $state, 
            'callback_target_url' => $client_target_url  //Url to redirect if back from Parcelhub Center
        ]);

        $query = http_build_query([
            'client_id' => env('PARCELHUB_CLIENT_ID'),
            'redirect_uri' => $callback_url,
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'target_url' => $center_target_url //redirect to target url on parcelhub center system
        ]);

        return redirect($url . $query);
    }
}
