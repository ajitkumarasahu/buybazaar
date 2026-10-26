<?php 
 
namespace App\Http\Middleware; 
 
use Closure; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str; 
use Symfony\Component\HttpFoundation\Response; 
 
class AssignRequestId 
{ 
    public function handle(Request $request, Closure $next): Response 
    { 
        // Reuse the client's ID only if it is a valid UUID; otherwise make our own. 
        $incoming = $request->header('X-Request-Id'); 
        $requestId = ($incoming && Str::isUuid($incoming)) ? $incoming : (string) Str::uuid(); 
 
        // Every log line written during this request will include this ID. 
        Log::withContext(['request_id' => $requestId]); 
 
        $response = $next($request); 
 
        $response->headers->set('X-Request-Id', $requestId); 
 
        return $response; 
    } 
}