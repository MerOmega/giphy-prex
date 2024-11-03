<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LogRequestServiceMiddleware
{
    /**
     * Logs all the requests to the services, saving the user, service, request body, response status code, response body, and IP address.
     * As the response might be too large to save it in the DB, we save it in a file and store the file path in the DB.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $filename = 'response_logs/' . uniqid() . '.json';
        Storage::put($filename, $response->getContent());

        RequestLog::create([
            'user_id'              => Auth::check() ? Auth::id() : null,
            'service'              => $request->path(),
            'request_body'         => json_encode($request->all()),
            'response_status_code' => $response->getStatusCode(),
            'response_body'        => $filename,
            'ip_address'           => $request->ip(),
        ]);

        return $response;
    }
}
