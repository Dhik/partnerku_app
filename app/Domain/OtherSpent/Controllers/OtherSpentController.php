<?php

namespace App\Domain\OtherSpent\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\OtherSpent\BLL\OtherSpent\OtherSpentBLLInterface;
use App\Domain\OtherSpent\Models\OtherSpent;
use App\Domain\OtherSpent\Requests\OtherSpentRequest;

/**
 * @property OtherSpentBLLInterface otherSpentBLL
 */
class OtherSpentController extends Controller
{
    public function __construct(OtherSpentBLLInterface $otherSpentBLL)
    {
        $this->otherSpentBLL = $otherSpentBLL;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OtherSpentRequest $request
     */
    public function store(OtherSpentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param OtherSpent $otherSpent
     */
    public function show(OtherSpent $otherSpent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  OtherSpent  $otherSpent
     */
    public function edit(OtherSpent $otherSpent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OtherSpentRequest $request
     * @param  OtherSpent  $otherSpent
     */
    public function update(OtherSpentRequest $request, OtherSpent $otherSpent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OtherSpent $otherSpent
     */
    public function destroy(OtherSpent $otherSpent)
    {
        //
    }
}
