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
            
            // Run the sync command in the background to avoid Cloudflare timeout
            // This allows the request to return immediately while sync runs in background
            $phpPath = PHP_BINARY;
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/sync-background.log');
            
            // Run command in background (works on Linux/Unix)
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                // Linux/Unix: Run in background and redirect output
                $command = sprintf(
                    '%s %s sync:tables >> %s 2>&1 &',
                    escapeshellarg($phpPath),
                    escapeshellarg($artisanPath),
                    escapeshellarg($logPath)
                );
                exec($command);
            } else {
                // Windows: Use start command
                $command = sprintf(
                    'start /B %s %s sync:tables >> %s 2>&1',
                    escapeshellarg($phpPath),
                    escapeshellarg($artisanPath),
                    escapeshellarg($logPath)
                );
                pclose(popen($command, 'r'));
            }
            
            Log::info('Sync command dispatched to run in background');
            
            return $this->successResponse('Sync started! The sync is running in the background and may take a few minutes to complete. Check the logs for progress.');
            
        } catch (\Exception $e) {
            Log::error('Manual sync failed: ' . $e->getMessage());
            return $this->errorResponse('Sync failed to start: ' . $e->getMessage());
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
