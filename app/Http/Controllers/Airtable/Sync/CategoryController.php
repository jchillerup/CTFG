<?php

namespace App\Http\Controllers\Airtable\Sync;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Airtable;

use App\Models\Category;
use DB;

class CategoryController extends Controller {
    /**
     * Sync categories table - Truncates the
     * table and recreates it with Airtable data
     * 
     * @return void
     */ 
    public function syncCategories () {
        \Log::info("Categories sync started at ".date('Y-m-d H:i:s'));
        $airtableCategories = Airtable::table('categories')->all();

        // Check if Airtable returned data then truncate table
        if ((Category::count() > 0) && (sizeof($airtableCategories) > 0)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Category::truncate();
        }

        // Recreate categories
        foreach ($airtableCategories as $cat) {
            if(!empty(trim(@$cat["fields"]["Name"]))){
                $name = trim(@$cat["fields"]["Name"]);

                $c = new Category;
                $c->airtable_id = @$cat["id"];
                $c->name = $name;
                $c->description = strip_tags(@$cat["fields"]["Description"]);
                $c->slug = Str::of($name)->slug();
                $c->order_sort = @$cat["fields"]["Order"];
                $c->save();
            }
        }

        // Update category parent category relationship
        foreach ($airtableCategories as $cat) {
            if (!empty(@$cat["fields"]["Parent Category"]) && sizeof(@$cat["fields"]["Parent Category"]) > 0) {
                $dbCat = Category::where('airtable_id', $cat["id"])->first();
                if ($dbCat) {
                    $parentCat = Category::where('airtable_id', $cat["fields"]["Parent Category"][0])->first();
                    if ($parentCat) {
                        $dbCat->update([
                            'parent_id' => $parentCat->id
                        ]);
                    }
                }
            }
        }

        $count = Category::count();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \Log::info("Categories sync finished at ".date('Y-m-d H:i:s')." ... ".$count." records synced.");
    }
}
