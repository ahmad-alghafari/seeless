<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Resturant;

class run
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $resturantId = $request->route('resturantName');
        $resturant = Resturant::where('name', $resturantId)->first();
        if($resturant->status != 'run'){
            return back()->with('error', 'The restaurant is currently not operational.');
        }
        return $next($request);
    }
}
