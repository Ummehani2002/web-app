<?php

use Illuminate\Support\Facades\Route;
use App\Models\customer1;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/customer1',function(){
    $customer1=customer1::all();
echo"<pre>";
print_r($customer1 -> toarray());
});