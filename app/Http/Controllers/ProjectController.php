<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\Account;
use App\Construction;
use App\Policy;
use App\PoliciesDet;

use DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $projects = Project::all();

        $projects = DB::select(
            DB::raw("                
                SELECT
                    A.top_parent_id,
                    C.id_proyecto as id,
                    C.nombre as name,
                    C.nombre_completo as full_name,
                    C.fecha_inicio as start_date,
                    C.fecha_fin as end_date,
                    CASE
                        WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                        ELSE NULL
                    END AS income,
                    CASE
                        WHEN AT.naturaleza =0 then (SUM(PD.cargo)-SUM(PD.abono))
                        ELSE NULL
                    END AS outcome,
                    CASE
                            WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                            ELSE (SUM(PD.cargo)-SUM(PD.abono))
                    END AS balance,
                    MAX(B.budget) as budget
                FROM
                accounts AS A
                JOIN accounts AS AA ON A.account = AA.account
                JOIN policies_det AS PD ON PD.account_id = AA.account_id
                    -- ON C.id_proyecto = PD.id_proyecto AND 
                JOIN policies AS P ON P.id_poliza = PD.id_poliza
                JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
                JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta
                LEFT JOIN budget AS B on C.id_proyecto = B.project_id

                WHERE 
                P.cancelada = 0
                AND AT.tipo_cuenta = 4

                GROUP BY
                     C.id_proyecto, C.nombre, C.nombre_completo
                WITH ROLLUP
                HAVING C.nombre IS NOT NULL AND C.nombre_completo IS NOT NULL;")
        );
        
        return response()->json(['data' => $projects], 200);   
    }  

    /**
     * Return JSON object of active projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function active()
    {

        //RAW QUERY
        
        $results = DB::select( 
            DB::raw("
                SELECT
                    A.top_parent_id,
                    C.id_proyecto as id,
                    C.nombre as name,
                    C.nombre_completo as full_name,
                    C.fecha_inicio as start_date,
                    C.fecha_fin as end_date,
                    CASE
                        WHEN AT.naturaleza =1 then TRUNCATE( (SUM(PD.abono)-SUM(PD.cargo)), 2)
                        ELSE NULL
                    END AS income,
                    CASE
                        WHEN AT.naturaleza =0 then TRUNCATE( (SUM(PD.cargo)-SUM(PD.abono)), 2)
                        ELSE NULL
                    END AS outcome,
                    CASE
                            WHEN AT.naturaleza =1 then TRUNCATE( (SUM(PD.abono)-SUM(PD.cargo)), 2)
                            ELSE TRUNCATE( (SUM(PD.cargo)-SUM(PD.abono)), 2)
                    END AS balance,
                    TRUNCATE( MAX(B.budget), 2) as budget
                FROM
                accounts AS A
                -- JOIN accounts AS AA ON A.account = AA.account
                -- JOIN policies_det AS PD ON PD.account_id = AA.account_id
                JOIN policies_det AS PD ON PD.account_id = A.account_id
                JOIN policies AS P ON P.id_poliza = PD.id_poliza
                JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
                -- JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta           
                JOIN business_unit as BU ON C.id_unidad_negocio = BU.id_unidad_negocio
                JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
                JOIN
                (

                    SELECT
                    C.id_proyecto, C.nombre_completo, C.fecha_inicio, C.fecha_fin
                    FROM
                    accounts AS A
                    JOIN accounts AS AA ON A.account = AA.account
                    JOIN policies_det AS PD ON PD.account_id = AA.account_id
                        -- ON C.id_proyecto = PD.id_proyecto AND 
                    JOIN policies AS P ON P.id_poliza = PD.id_poliza
                    JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
                    JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta
                    WHERE 
                    CAST( LEFT(mes_poliza, 4) AS unsigned) = year(curdate())
                    AND AA.top_parent_id IN (355, 366)
                    AND P.cancelada = 0
                    AND AT.tipo_cuenta = 4
                    AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13

                    GROUP BY C.id_proyecto, C.nombre_completo, C.fecha_inicio, C.fecha_fin
                ) as active ON C.id_proyecto = active.id_proyecto
                LEFT JOIN budget AS B on C.id_proyecto = B.project_id
                WHERE 
                CAST( LEFT(mes_poliza, 4) AS unsigned) = year(curdate())
                AND P.cancelada = 0
                AND AT.tipo_cuenta = 4
                AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13
                AND BU.unidad_negocio NOT IN ('0101009','0101010')

                GROUP BY
                     C.id_proyecto, C.nombre, C.nombre_completo
                WITH ROLLUP
                HAVING C.nombre IS NOT NULL AND C.nombre_completo IS NOT NULL;")
        );

        return response()->json($results, 200);
    }

    /**
     * Return JSON object of active projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeDetails($projects)
    {
        $results = DB::select( 
            DB::raw("
                SELECT
                    account_id,
                    account,
                    description,
                    prj.id_proyecto,
                    prj.nombre,
                    prj.cargo,
                    prj.abono
                FROM accounts AS A

                JOIN
                (
                    SELECT
                    A.top_parent_id,
                    C.id_proyecto, C.nombre,
                    SUM(PD.cargo) AS cargo,
                    SUM(PD.abono) AS abono
                    FROM
                    accounts AS A
                    JOIN accounts AS AA ON A.account = AA.account
                    JOIN policies_det AS PD ON PD.id_cuenta = AA.account_id
                    JOIN policies AS P ON P.id_poliza = PD.id_poliza
                    JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
                    JOIN account_type AS AT ON AA.account_type = AT.id_tipo_cuenta
                    WHERE 
                    CAST( LEFT(mes_poliza, 4) AS unsigned) = YEAR( CURDATE() )
                    AND P.cancelada = 0
                    AND AT.tipo_cuenta = 4
                    AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13

                    GROUP BY A.top_parent_id, C.id_proyecto, C.nombre
                    WITH ROLLUP
                ) AS prj
                ON A.top_parent_id = prj.top_parent_id
                WHERE A.parent_id is null")
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
        $project = Project::find($id);

        if (!$project)
        {
            return response()->json(['message' => 'The specified project could not be found', 'code' => 404], 404);
        }

        return response()->json(['data' => $project], 200);  
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
