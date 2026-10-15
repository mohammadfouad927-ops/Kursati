<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(){
    $users = User::all();
    foreach($users as $user){
        $user->password = bcrypt(strlen($user->name) >= 4 ? ucfirst($user->name).'@1234' : ucfirst($user->name).'@123456');
        $user->save();
        echo "Password for user {$user->name} has been updated to {$user->password}<br>";
    }
});