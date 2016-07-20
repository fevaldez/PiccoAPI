<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Project;
use App\Income;

use DB;

class ProjectIncomeController extends Controller
{
    /**
     * Display income/outcome details for a Project
     *
     * @return \Illuminate\Http\Response
     */
    public function details($id, $active = false){
        $query = "
            SELECT * FROM
            (
            SELECT
                A.top_parent_id,
                topParent.description as top_account_name,
                A.parent_id,
                AP.account as parent_account,
                AP.description AS sub_account_name,
                A.account,
                A.description AS account_name,
                C.id_proyecto, C.nombre,
                CASE
                    WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                    ELSE NULL
                END AS abono,
                CASE
                    WHEN AT.naturaleza =0 then (SUM(PD.cargo)-SUM(PD.abono))
                    ELSE NULL
                END AS cargo,
                CASE
                    WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                    ELSE (SUM(PD.cargo)-SUM(PD.abono))
                END AS balance
            FROM
            accounts AS A
            JOIN accounts AS AA ON A.account = AA.account
            JOIN policies_det AS PD ON PD.account_id = AA.account_id
            JOIN policies AS P ON P.id_poliza = PD.id_poliza
            JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
            JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta
            JOIN accounts AS topParent ON AA.top_parent_id = topParent.account_id

            LEFT JOIN (
                SELECT
                    A.top_parent_id,
                    A.parent_id,
                    A.account_id,
                    A.account,
                    A.description-- , NULL, NULL, NULL, NULL, NULL
                FROM accounts AS A
                WHERE LENGTH(A.account) = 6
            ) AS AP ON AA.parent_id = AP.account_id

            WHERE
            C.id_proyecto IN (:id)
            AND AA.top_parent_id IN (355,366)
            AND P.cancelada = 0
            AND AT.tipo_cuenta = 4 
            AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13 ";

        //Active projects flag
        // if ($active)
        //     $query .= "AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13 ";

        $query .=
            "GROUP BY A.top_parent_id
                    ,A.parent_id
                    ,AP.account
                    ,AP.description
                    ,A.account
                    ,A.description
                    ,C.id_proyecto, C.nombre
                WITH ROLLUP
                ) as details
                WHERE
                (parent_account is null and sub_account_name is null
                and account is NOT null and account_name is not null and id_proyecto is not null and nombre is not null)
                or
                (nombre is not null)
                or
                (sub_account_name is not null and account is null and account_name is null and id_proyecto is null and nombre is null)
                or
                (parent_id is null and parent_account is null and sub_account_name is null and account is null
                 and account_name is null and id_proyecto is null and nombre is null)
            ";
        $results = DB::select(
            DB::raw( $query ), array('id' => $id)
        );

        return response()->json(['data' => $results], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // $income_reg = Income::where('project_id', '=', $id)->get();


        $results = DB::select( 
            DB::raw("
            SELECT * FROM
            (
            SELECT
                A.top_parent_id,
                topParent.description as top_account_name,
                A.parent_id,
                AP.account as parent_account,
                AP.description AS sub_account_name,
                A.account,
                A.description AS account_name,
                C.id_proyecto, C.nombre,
                SUM(PD.cargo) AS cargo,
                SUM(PD.abono) AS abono,
                CASE
                    WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                    ELSE (SUM(PD.cargo)-SUM(PD.abono))
                END AS balance
            FROM
            accounts AS A
            JOIN accounts AS AA ON A.account = AA.account
            JOIN policies_det AS PD ON PD.account_id = AA.account_id
            JOIN policies AS P ON P.id_poliza = PD.id_poliza
            JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
            JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta
            JOIN accounts AS topParent ON AA.top_parent_id = topParent.account_id

            LEFT JOIN (
                SELECT
                    A.top_parent_id,
                    A.parent_id,
                    A.account_id,
                    A.account,
                    A.description-- , NULL, NULL, NULL, NULL, NULL
                FROM accounts AS A
                WHERE LENGTH(A.account) = 6
            ) AS AP ON AA.parent_id = AP.account_id

            WHERE
            C.id_proyecto IN (:id)
            AND AA.top_parent_id IN (355,366)
            AND P.cancelada = 0
            AND AT.tipo_cuenta = 4
            AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13

            GROUP BY A.top_parent_id
                ,A.parent_id
                ,AP.account
                ,AP.description
                ,A.account
                ,A.description
                ,C.id_proyecto, C.nombre
            WITH ROLLUP
            ) as details
            WHERE
            (parent_account is null and sub_account_name is null
            and account is NOT null and account_name is not null and id_proyecto is not null and nombre is not null)
            or
            (nombre is not null)
            or
            (sub_account_name is not null and account is null and account_name is null and id_proyecto is null and nombre is null)
            or
            (parent_id is null and parent_account is null and sub_account_name is null and account is null
             and account_name is null and id_proyecto is null and nombre is null)
        "), array('id' => $id)
        );
        // return response()->json($results, 200);

        return response()->json(['data' => $results], 200);
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
