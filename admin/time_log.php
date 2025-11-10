<?php
include 'db_connect.php';

// Set PHP timezone to UTC to ensure consistency
date_default_timezone_set('UTC');

extract($_POST);
$data = array();

// Retrieve employee information
$qry = $conn->query("SELECT * FROM employee WHERE employee_no ='$eno' ");
if($qry->num_rows > 0){
    $emp = $qry->fetch_array();
    $employee_id = $emp['id'];
    $employee_name = ucwords($emp['firstname'].' '.$emp['lastname']);
    $log_type = intval($type);

    // Convert datetime to Nigeria timezone
    $current_time = new DateTime('now', new DateTimeZone('Africa/Lagos'));
    $datetime_log = $current_time->format('Y-m-d H:i:s');

    // Insert attendance record into the database
    $insert_query = "INSERT INTO attendance (log_type, employee_id, datetime_log) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('iis', $log_type, $employee_id, $datetime_log);
    
    if ($type == 1) {
        $log = ' Time in';
    } elseif ($type == 2) {
        $log = ' Time out';
    } 
    
    if ($stmt->execute()) {
        $data['status'] = 1;
        $data['msg'] = "$employee_name, your <b>$log</b> has been recorded in the  attendance sheet";
    } else {
        $data['status'] = 0;
        $data['msg'] = "Error: " . $stmt->error;
    }
    
    
} else {
    $data['status'] = 0;
    $data['msg'] = "Unknown Employee Number";
}

echo json_encode($data);

$conn->close();
?>
