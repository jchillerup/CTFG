<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Tag;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class RenderTagView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $tags = Tag::whereNull('parent_id')
            ->orderBy('order_sort', 'ASC')
            ->orderBy('name', 'ASC')
            ->with('childItems')
            ->get();

        $html = View::make('partials.tags', ['tagHierarchies' => $tags, 'activeTag' => null, 'activeParentTag' => null])->render();
        file_put_contents(resource_path('views/cache/tags_cache.blade.php'), $html);

        Log::info("Done rendering tag view cache");
    }
}
