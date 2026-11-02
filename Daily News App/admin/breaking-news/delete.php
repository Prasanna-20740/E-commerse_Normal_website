<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../config/database.php";


/* =====================================================
   GET ID
===================================================== */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($id <= 0) {

    header("Location: manage.php?error=invalid");
    exit;
}


/* =====================================================
   DELETE
===================================================== */

$query = " DELETE FROM breaking_news
    WHERE id = ?
    LIMIT 1
";


$stmt = mysqli_prepare($conn, $query);


if ($stmt === false) {

    header("Location: manage.php?error=delete");
    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: manage.php?deleted=1");
    exit;

}


mysqli_stmt_close($stmt);

header("Location: manage.php?error=delete");
exit;