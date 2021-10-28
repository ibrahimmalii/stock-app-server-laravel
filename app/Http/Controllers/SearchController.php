<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search($key){

        if ($key) {
            $companies = DB::table('symbols')->where('Name', 'LIKE', '%' . $key . "%")->paginate(100);
            if ($companies) {
                return $companies;
            }
        }
    }
}
