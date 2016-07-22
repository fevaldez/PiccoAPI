    SELECT
    	A.account
        ,A.description
        ,Acc.*
    FROM 	accounts as A
    JOIN 
        (SELECT
                A.top_parent_id
                ,C.id_proyecto
                ,CASE
                    WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                    ELSE NULL
                END AS abono
                ,CASE
                    WHEN AT.naturaleza =0 then (SUM(PD.cargo)-SUM(PD.abono))
                    ELSE NULL
                END AS cargo
                ,CASE
                    WHEN AT.naturaleza =1 then (SUM(PD.abono)-SUM(PD.cargo))
                    ELSE (SUM(PD.cargo)-SUM(PD.abono))
                END AS balance
            FROM
            accounts AS A
            JOIN policies_det AS PD ON PD.account_id = A.account_id
            JOIN policies AS P ON P.id_poliza = PD.id_poliza
            JOIN constructions AS C ON C.id_proyecto = PD.id_proyecto
            JOIN account_type AS AT ON A.account_type = AT.id_tipo_cuenta
            JOIN accounts AS topParent ON A.top_parent_id = topParent.account_id

            LEFT JOIN (
                SELECT
                    A.top_parent_id,
                    A.parent_id,
                    A.account_id,
                    A.account,
                    A.description
                FROM accounts AS A
                WHERE LENGTH(A.account) = 6
            ) AS AP ON A.parent_id = AP.account_id

            WHERE
            C.id_proyecto IN (87)
            AND P.cancelada = 0
            AND AT.tipo_cuenta = 4 
            AND CAST( RIGHT(P.mes_poliza,2) AS SIGNED INT) < 13
            
            GROUP BY 
					A.top_parent_id
                    ,C.id_proyecto
                WITH ROLLUP
        ) AS Acc
    ON Acc.top_parent_id =  A.account_id
    JOIN constructions As C ON Acc.id_proyecto = C.id_proyecto