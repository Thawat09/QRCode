<?php
include "./class.php";

function getJobData($pdo_pgsql) {
    $sql = "SELECT job.unid AS unid, job.main_job_id AS main_job_id, job.ref_doc_so AS ref_doc_so, job.sim, job.call_id,
    ( CASE WHEN sc.status = 'Cancel' OR sc.status = 'Shift' THEN 0 ELSE 1 END ) AS order_status,
    ( CASE WHEN st_s.count_stock IS NOT NULL THEN st_s.count_stock ELSE 0 END ) AS count_send_stock,
    ( CASE WHEN stsa.count_send_stock_all IS NOT NULL THEN stsa.count_send_stock_all ELSE 0 END ) AS count_send_stock_all,
    CALL.main_call_id, st_s.amount AS amount, st_s.mat_code AS mat_code, st_s.mat_name AS mat_name
    FROM sys_plan_job_send_stock AS sst
    LEFT JOIN sys_plan_job_schedule AS sc ON sst.schedule_id = sc.unid
    LEFT JOIN sys_plan_job_main AS job ON sst.job_id = job.unid
    LEFT JOIN sys_plan_job_callcenter AS CALL ON CALL.unid = CAST ( job.call_id AS INT )
    LEFT JOIN ( SELECT COUNT ( unid ) AS count_stock, send_stock_id, job_id, amount, mat_code, mat_name 
        FROM sys_plan_job_stock 
        WHERE 1 = 1 AND job_id IS NOT NULL AND send_stock_id IS NOT NULL 
        GROUP BY send_stock_id, job_id, amount, mat_code, mat_name 
    ) AS st_s ON st_s.send_stock_id = sst.unid
    LEFT JOIN ( SELECT COUNT ( st.unid ) AS count_send_stock_all, st.job_id 
        FROM sys_plan_job_stock AS st
        LEFT JOIN sys_plan_job_send_stock AS sst ON sst.unid = st.send_stock_id 
        WHERE 1 = 1 AND st.job_id IS NOT NULL AND st.send_stock_id IS NOT NULL AND sst.status != 'Return' 
        GROUP BY st.job_id 
    ) AS stsa ON stsa.job_id = job.unid
    LEFT JOIN sys_plan_config_employee AS emp ON sc.emp_id = emp.emp_id
    LEFT JOIN sys_plan_config_zone_ts AS zts ON zts.unid = emp.emp_zone AND emp.emp_department = 'TS' 
    WHERE 1 = 1 AND sst.status = 'Send' 
    ORDER BY CASE WHEN sst.print_time IS NULL THEN 0 ELSE 1 END ASC, sst.send_time DESC, CASE WHEN sst.edit_time IS NOT NULL THEN sst.edit_time ELSE sst.create_time END DESC 
    LIMIT 300";
    $stmt = $pdo_pgsql->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    $data = getJobData($pdo_pgsql);
} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage();
}
// print_r($data)

?>