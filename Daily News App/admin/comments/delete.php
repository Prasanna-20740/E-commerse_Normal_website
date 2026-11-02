<?php

session_start();

require_once "../../config/database.php";


/* =====================================
   GET COMMENT ID
===================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: manage.php");
    exit;

}

$comment_id = (int) $_GET["id"];


/* =====================================
   DELETE COMMENT
===================================== */

$query = " DELETE FROM comments
    WHERE id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $comment_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

}


/* =====================================
   BACK TO COMMENTS
===================================== */

header("Location: manage.php");

exit;