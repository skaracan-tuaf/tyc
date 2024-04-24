<?php

namespace App\Http\Controllers;

use App\Models\Munition;
use Illuminate\Http\Request;

class MunitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $munitions = Munition::all();
        return view('Backend.pages.munition', compact('munitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Munition $munition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Munition $munition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Munition $munition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Munition $munition)
    {
        //
    }
}
