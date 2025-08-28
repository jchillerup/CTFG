<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller {
    // Get about page
    public function about() {
        return view ('pages.about', [
            'title' => 'About Connecting Current',
            'menu' => 'about',
        ]);
    }
}
