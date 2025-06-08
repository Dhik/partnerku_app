<?php

namespace App\Domain\Payroll\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Payroll\BLL\Payroll\PayrollBLLInterface;
use App\Domain\Payroll\Models\Payroll;
use App\Domain\Payroll\Requests\PayrollRequest;

/**
 * @property PayrollBLLInterface payrollBLL
 */
class PayrollController extends Controller
{
    public function __construct(PayrollBLLInterface $payrollBLL)
    {
        $this->payrollBLL = $payrollBLL;
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
     * @param PayrollRequest $request
     */
    public function store(PayrollRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Payroll $payroll
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Payroll  $payroll
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PayrollRequest $request
     * @param  Payroll  $payroll
     */
    public function update(PayrollRequest $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Payroll $payroll
     */
    public function destroy(Payroll $payroll)
    {
        //
    }
}
