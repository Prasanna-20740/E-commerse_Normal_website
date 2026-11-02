<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/database.php";


$id = (int)($_GET["id"] ?? 0);


if ($id <= 0) {

    header("Location: manage.php");
    exit;

}


/* =====================================================
   DELETE
===================================================== */

$query = " DELETE FROM trending_topics
    WHERE id = ?
    LIMIT 1
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


if (!$stmt) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header(
        "Location: manage.php?success=3"
    );

    exit;

}


$error = mysqli_stmt_error($stmt);

mysqli_stmt_close($stmt);


die(
    "Unable to delete trending topic: " .
    htmlspecialchars(
        $error,
        ENT_QUOTES,
        "UTF-8"
    )
);

?>