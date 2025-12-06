<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Organization;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->index('slug');
        });
        
        // Generate slugs for existing organizations
        Organization::chunk(100, function ($organizations) {
            foreach ($organizations as $org) {
                if (empty($org->slug) && !empty($org->name)) {
                    $slug = Str::slug($org->name);
                    // Ensure uniqueness
                    $originalSlug = $slug;
                    $counter = 1;
                    while (Organization::where('slug', $slug)->where('id', '!=', $org->id)->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    $org->update(['slug' => $slug]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};
