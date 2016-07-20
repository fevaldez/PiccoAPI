<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;

class BalanceController extends Controller
{
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
    
       // $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    	$query = "
    		SELECT
				A.id
				,account_id
				,account
				,description
				,parent_id
				,top_parent_id
				,tipo_cuenta as account_type
				,sub_tipo_cuenta as sub_account_type
				,naturaleza as nature
				,cuenta_inicial as initial_account
				,cuenta_final as final_account
			FROM
			accounts AS A
			JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
    	";

		$accounts =
			DB::select(
            	DB::raw($query)
        	);
        
        return response()->json( $accounts, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    	$query = "
    		SELECT
				A.id
				,account_id
				,account
				,description
				,parent_id
				,top_parent_id
				,tipo_cuenta as account_type
				,sub_tipo_cuenta as sub_account_type
				,naturaleza as nature
				,cuenta_inicial as initial_account
				,cuenta_final as final_account
			FROM
			accounts AS A
			JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
			WHERE A.account ='" .$id. "';
    	";

		$account =
			DB::select(
            	DB::raw($query)
        	);

        if (!$account)
        {
            return response()->json(['message' => 'The specified account could not be found', 'code' => 404], 404);
        }
        
        return response()->json( $account, 200);
    }

	/**
     * Return JSON object for Account Status (DATE RANGE AND variable Business Unit).
     *
     * @return \Illuminate\Http\Response
     */
    public function accountStatusByRange($company, $bu, $startdate, $enddate=null)
    {

    	$params = array('startdate' => $startdate);
        //RAW QUERY
        $query = "
            SELECT DISTINCT
				pre.tipo_cuenta
				,pre.sub_tipo_cuenta
				,pre.top_parent_id
				,CASE
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN NULL
						WHEN pre.account is null THEN pre.top_parent_id
						ELSE pre.parent_id
				END AS parent_id
				,CASE
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN pre.top_parent_id
						WHEN pre.account_id is null THEN account_id_l2
						ELSE pre.account_id
				END AS account_id
				,CASE
						WHEN pre.sub_tipo_cuenta is null THEN 9999
						-- WHEN pre.top_parent_id is null THEN CONCAT(LEFT(account_l2, 1) , '99')
						WHEN pre.top_parent_id is null THEN LEFT(account_l2, 3)+1
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN A.account
						WHEN pre.account is null THEN pre.account_l2
						ELSE pre.account
				END AS account
				,CASE
						WHEN pre.sub_tipo_cuenta is null THEN 'UTILIDAD O PERDIDA DEL EJERCICIO'
						WHEN pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null THEN pre.tp_account_desc
						WHEN pre.account_id is null AND pre.account is null THEN account_l2_desc
						ELSE pre.description
				END AS description
				,pre.cargo
				,pre.abono
				-- ,pre.balance				
				,CASE
					-- Utilidad o perdida del ejercicio
					WHEN pre.naturaleza is null THEN pre.abono - pre.cargo
					ELSE pre.balance
				END as balance
			FROM accounts AS A
			RIGHT JOIN
			(
				SELECT DISTINCT
				AT.tipo_cuenta
				,AT.sub_tipo_cuenta
				,AT.naturaleza
				,A.top_parent_id
				,TA.account as tp_account
				,TA.description as tp_account_desc
				,A.parent_id
				,A.account_id
				,A.account
				,AA.description
				,CA.account_id as account_id_l2
				,CA.account as account_l2
				,CA.description as account_l2_desc
				,SUM(PD.cargo) AS cargo
				,SUM(PD.abono) AS abono
				,CASE
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
				JOIN business_unit as BU ON C.id_unidad_negocio = BU.id_unidad_negocio

				LEFT JOIN (
						SELECT
							account_id
							,account
							,description
							,parent_id
						FROM
						accounts AS A
						JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
						WHERE AT.tipo_cuenta = 4
						AND LENGTH(account) =6
					) as CA ON A.parent_id = CA.account_id
					AND (A.top_parent_id <> A.parent_id) AND (A.top_parent_id <> A.account_id)
					AND (A.parent_id <> A.account_id)
			/*
				TOP PARENT ID
			*/
			JOIN accounts as TA ON A.top_parent_id = TA.account_id
			/*
				TOP PARENT ID
			*/
				WHERE
				P.cancelada = 0
				AND AT.tipo_cuenta = 4
				AND C.id_proyecto = 87
				AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13
				AND BU.unidad_negocio LIKE ('" .$bu. "%')";

				if ( empty($enddate) ){
					$query .= " AND P.mes_poliza = :startdate";
				}else{
					//Validate date range
					if ($startdate > $enddate)
        				return response()->json(['message' => 'Invalid range.'], 400);

					$query .= " AND P.mes_poliza BETWEEN :startdate AND :enddate";
    				$params = array('startdate' => $startdate, 'enddate' => $enddate);
				}

		$query .=" GROUP BY
				AT.tipo_cuenta
				,AT.sub_tipo_cuenta
				,AT.naturaleza
				,A.top_parent_id
				,TA.account
				,CA.account_id
				,A.account_id,A.account
				WITH ROLLUP
			 ) as pre
			ON A.account_id = pre.top_parent_id
			WHERE (pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is not null AND pre.account is not null AND pre.account_id_l2 is null AND pre.account_l2 is null AND pre.account_l2_desc is null)
			OR
			(pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is not null AND pre.account is not null AND pre.account_id_l2 is not null AND pre.account_l2 is not null  AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is not null AND pre.account_l2 is not null AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.tp_account is null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null AND pre.account_l2 is not null AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.tp_account is null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null AND pre.account_l2 is null AND pre.account_l2_desc is null)
			OR -- sub_tipo_cuenta Summary
			(pre.tipo_cuenta is not null AND pre.top_parent_id is null AND pre.tp_account is null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null)
			ORDER BY sub_tipo_cuenta, account";

		if ($company == 'ERP'){
			$results =
				DB::select(
	            	DB::raw($query), $params
	        	);
		}
		else if ($company == 'FNR'){
			$results =
				DB::connection('mysql_fnr')->select( DB::raw($query), $params );
		}

        return response()->json($results, 200);
    }

    /**
     * Return JSON object for Account Status (CURRENT YEAR).
     *
     * @return \Illuminate\Http\Response
     */
    public function accountStatus()
    {

        //RAW QUERY
        
        $results = DB::select( 
            DB::raw("
            SELECT
				pre.top_parent_id
				,CASE
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN NULL
						WHEN pre.account is null THEN pre.top_parent_id
						ELSE pre.parent_id
				END AS parent_id
				,CASE
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN pre.top_parent_id
						WHEN pre.account_id is null THEN account_id_l2
						ELSE pre.account_id
				END AS account_id
				,CASE
						WHEN pre.account_id is null AND pre.account_id_l2 is null THEN A.account
						WHEN pre.account is null THEN pre.account_l2
						ELSE pre.account
				END AS account
				,CASE
						WHEN pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null THEN pre.tp_account_desc
						WHEN pre.account_id is null AND pre.account is null THEN account_l2_desc
						ELSE pre.description
				END AS description
				,pre.cargo
				,pre.abono
				,pre.balance
			FROM accounts AS A
			JOIN
			(
				SELECT DISTINCT
				A.top_parent_id
				,TA.account as tp_account
				,TA.description as tp_account_desc
				,A.parent_id
				,A.account_id
				,A.account
				,AA.description
				,CA.account_id as account_id_l2
				,CA.account as account_l2
				,CA.description as account_l2_desc
				,SUM(PD.cargo) AS cargo
				,SUM(PD.abono) AS abono
				,CASE
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
				JOIN business_unit as BU ON C.id_unidad_negocio = BU.id_unidad_negocio

				LEFT JOIN (
						SELECT
							account_id
							,account
							,description
							,parent_id
						FROM
						accounts AS A
						JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
						WHERE AT.tipo_cuenta = 4
						AND LENGTH(account) =6
					) as CA ON A.parent_id = CA.account_id
					AND (A.top_parent_id <> A.parent_id) AND (A.top_parent_id <> A.account_id)
					AND (A.parent_id <> A.account_id)
			/*
				TOP PARENT ID
			*/
			JOIN accounts as TA ON A.top_parent_id = TA.account_id
			/*
				TOP PARENT ID
			*/
				WHERE 
				CAST( LEFT(mes_poliza, 4) AS unsigned) = year(curdate())
				AND P.cancelada = 0
				AND AT.tipo_cuenta = 4
				AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13
				AND BU.unidad_negocio LIKE ('0101%')

				GROUP BY A.top_parent_id
				,TA.account
				,CA.account_id
				,A.account_id,A.account
				WITH ROLLUP
			 ) as pre
			ON A.account_id = pre.top_parent_id
			WHERE (pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is not null AND pre.account is not null AND pre.account_id_l2 is null AND pre.account_l2 is null AND pre.account_l2_desc is null)
			OR
			(pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is not null AND pre.account is not null AND pre.account_id_l2 is not null AND pre.account_l2 is not null  AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.parent_id is not null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is not null AND pre.account_l2 is not null AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.tp_account is null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null AND pre.account_l2 is not null AND pre.account_l2_desc is not null)
			OR
			(pre.top_parent_id is not null AND pre.tp_account is null AND pre.account_id is null AND pre.account is null AND pre.account_id_l2 is null AND pre.account_l2 is null AND pre.account_l2_desc is null)
			ORDER BY account;")
        );

        return response()->json($results, 200);
    }

	/**
     * Return JSON object with Account Initial Balance.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountInitialBalance($acc, $bu, $startdate){

    	$query = "
				SELECT
					CASE
						WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
						WHEN AT.naturaleza =0 then (SUM(PD.cargo)-SUM(PD.abono))							
					END AS initial_balance
				FROM
					accounts AS A
					JOIN policies_det AS PD ON PD.account_id = A.account_id
					JOIN policies AS P ON P.id_poliza = PD.id_poliza
					JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
					JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
					JOIN business_unit as BU ON C.id_unidad_negocio = BU.id_unidad_negocio
				WHERE
				P.cancelada = 0
				AND BU.unidad_negocio LIKE ('" .$bu. "%')
				AND mes_poliza < :startdate
				AND A.account LIKE ('" .$acc. "%');
            ";

    	$params = array('startdate' => $startdate);
        //RAW QUERY        
        $results =
        	DB::select(
	            DB::raw($query), $params
            );

        return response()->json($results, 200);
    }

	/**
     * Return JSON object with Account Initial Balance.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountDebitsCredits($acc, $bu, $startdate, $enddate=null){

    	$query = "
				SELECT
					SUM(PD.cargo) AS debit
					,SUM(PD.abono) AS credit
				FROM
					accounts AS A
					JOIN policies_det AS PD ON PD.account_id = A.account_id
					JOIN policies AS P ON P.id_poliza = PD.id_poliza
					JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
					JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
					JOIN business_unit as BU ON C.id_unidad_negocio = BU.id_unidad_negocio
				WHERE
				P.cancelada = 0
				AND BU.unidad_negocio LIKE ('" .$bu. "%')
				AND mes_poliza BETWEEN :startdate AND :enddate
				AND A.account LIKE ('" .$acc. "%');
            ";

    	$params = array('startdate' => $startdate, 'enddate' => $enddate);
        //RAW QUERY        
        $results =
        	DB::select(
	            DB::raw($query), $params
            );
        
        return response()->json($results, 200);
    }

}
