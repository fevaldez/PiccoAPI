<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use DB;

class CashFlowController extends Controller
{
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = "
            SELECT
                A.account_id
                ,A.description as parent_account
                ,flow.*
            FROM accounts as A
            JOIN
            (
                SELECT
                    A.account
                    ,A.parent_id
                    ,A.top_parent_id
                    ,A.description
                    ,I.id
                    ,invoice
                    ,invoice_date
                    ,invoice_pay_date
                    ,credit_days
                    ,I.description as supplier
                    ,amount
                FROM pico_bi.invoice_supplier AS I
                JOIN policies_det as PD
                    ON I.policies_det_id = PD.id_poliza_det
                JOIN accounts as A
                    ON PD.account_id = A.account_id
                WHERE I.canceled = 0
                AND I.invoice_pay_date >= DATE_FORMAT(NOW() ,'%Y%m%d')
                ORDER BY invoice_pay_date
            ) AS flow
            ON A.account_id = flow.parent_id;
        ";

        $results = DB::select(
            DB::raw( $query )
        );

        return response()->json($results, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
