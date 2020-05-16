<?php
  function connect(){
    $dsn = "mysql:dbname=tasks";
    $username = "root";
    $password = "";
    try{
      $conn = new PDO( $dsn, $username, $password );
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }catch(PDOException $e){
      echo "Connection failed: ".$e->getMessage();
    }
    return $conn;
  }
  function disconnect($conn){
    $conn = "";
  }

  $action = $_REQUEST['action'];

  switch($action){
    case "get":
      get_all_tasks();
      break;
    case "add":
      add_task();
      break;
    case "del":
      del_task();
      break;
    case "done":
      done_task();
      break;
    case "undo":
      undo_task();
      break;
    default:
      unknown_action();
  }
  function get_all_tasks(){
    $conn = connect();
    $sql = "SELECT * FROM tasks";
    try{
      $rows = $conn->query( $sql );
      $tasks = array();
      foreach($rows as $row){
        $tasks[] = $row;
      }
      echo json_encode($tasks);
    }catch(PDOException $e){
      echo "Query failed: ".$e->getMessage();
    }
    disconnect($conn);
  }
  function add_task(){
    $subject = $_POST['subject'];
    $conn = connect();
    $sql = "INSERT INTO tasks (subject, status, created_date) VALUES ('$subject', 0, now())";
    try{
      $result = $conn->query( $sql );
      $id =  $conn->lastInsertId();
      if($result){
        echo json_encode(array("err" => 0, "id"=>$id));
      }else{
        echo json_encode(array("err" => 1, "msg" => "Unable to insert task"));
      }
    }catch(PDOException $e){
      echo "Query failed: ".$e->getMessage();
    }
    disconnect($conn);
  }
  function del_task(){
    $id = $_POST['id'];
    $conn = connect();
    $sql = "DELETE FROM tasks WHERE id = $id";
    try{
      $result = $conn->query( $sql );
      if($result){
        echo json_encode(array("err" => 0));
      }else{
        echo json_encode(array("err" => 1, "msg" => "Unable to delete task"));
      }
    }catch(PDOException $e){
      echo "Query failed: ".$e->getMessage();
    }
    disconnect($conn);
  }
  function done_task(){
    $id = $_POST['id'];
    $conn = connect();
    $sql = "UPDATE tasks SET status = 1 WHERE id = $id";
    try{
      $result = $conn->query( $sql );
      if($result){
        echo json_encode(array("err" => 0));
      }else{
        echo json_encode(array("err" => 1, "msg" => "Unable to update task"));
      }
    }catch(PDOException $e){
      echo "Query failed: ".$e->getMessage();
    }
    disconnect($conn);
  }
  function undo_task(){
    $id = $_POST['id'];
    $conn = connect();
    $sql = "UPDATE tasks SET status = 0 WHERE id = $id";
    try{
      $result = $conn->query( $sql );
      if($result){
        echo json_encode(array("err" => 0));
      }else{
        echo json_encode(array("err" => 1, "msg" => "Unable to update task"));
      }
    }catch(PDOException $e){
      echo "Query failed: ".$e->getMessage();
    }
    disconnect($conn);
  }
  function unknown_action(){
    echo json_encode(array("err" => 1, "msg" => "Unknown Action"));
  }
