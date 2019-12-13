<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsWebController extends Controller
{
    public function index(Request $request)
    {
        return Product::paginate(10);
    }
}
