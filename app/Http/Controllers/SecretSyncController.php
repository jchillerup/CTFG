<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SecretSyncController extends Controller
{
    /**
     * Handle instant sync via secret URL
     */
    public function instantSync($token)
    {
        // Verify the secret token (case-insensitive comparison)
        $validToken = config('app.sync_secret_token', 'default-secret-token');
        
        if (strtolower($token) !== strtolower($validToken)) {
            Log::warning('Invalid sync token attempted: ' . $token);
            return $this->errorResponse('Access denied');
        }

        try {
            Log::info('Manual sync triggered via secret URL');
            
            // Run the sync command
            Artisan::call('sync:tables');
            
            // Get the output
            $output = Artisan::output();
            
            Log::info('Manual sync completed successfully');
            
            return $this->successResponse('Sync completed successfully!');
            
        } catch (\Exception $e) {
            Log::error('Manual sync failed: ' . $e->getMessage());
            return $this->errorResponse('Sync failed. Please try again later.');
        }
    }

    /**
     * Simple success response
     */
    private function successResponse($message)
    {
        return response()->view('sync-result', [
            'status' => 'success',
            'message' => $message,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Simple error response
     */
    private function errorResponse($message)
    {
        return response()->view('sync-result', [
            'status' => 'error',
            'message' => $message,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
