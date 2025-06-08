<?php

namespace App\Domain\Income\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Income\BLL\Income\IncomeBLLInterface;
use App\Domain\Income\Models\Income;
use App\Domain\Income\Requests\IncomeRequest;

/**
 * @property IncomeBLLInterface incomeBLL
 */
class IncomeController extends Controller
{
    public function __construct(IncomeBLLInterface $incomeBLL)
    {
        $this->incomeBLL = $incomeBLL;
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
     * @param IncomeRequest $request
     */
    public function store(IncomeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Income $income
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Income  $income
     */
    public function edit(Income $income)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param IncomeRequest $request
     * @param  Income  $income
     */
    public function update(IncomeRequest $request, Income $income)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Income $income
     */
    public function destroy(Income $income)
    {
        //
    }
}
