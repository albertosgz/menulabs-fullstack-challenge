<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;
use App\Models\User;
use App\Services\Weather;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = QueryBuilder::for(User::class)
            ->paginate(10);

        return UserResource::collection($items)->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(?User $user)
    {
        if ($user) {
            $latitude = $user->latitude;
            $longitude = $user->longitude;
            $cacheKey = '' . $latitude . '_' . $longitude;
            $periods = Cache::get($cacheKey);
            if (!$periods) {
                $weatherService = new Weather();
                $periods = $weatherService->getForecast($latitude, $longitude);
                Cache::put($cacheKey, $periods, now()->addHour()); // cache info should expire after 1 hour
            }

            $userResource = new UserResource($user);
            $userResource->additional([
                'forecast' => $periods,
            ]);
            return $userResource->response();
        }
        abort(404, 'User not found');
    }
}
