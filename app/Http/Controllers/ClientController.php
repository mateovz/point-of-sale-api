<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('ability:client.view')->only('index');
        $this->middleware('ability:client.store')->only('store');
        $this->middleware('ability:client.update')->only('update');
        $this->middleware('ability:client.destroy')->only('destroy');
    }
}
