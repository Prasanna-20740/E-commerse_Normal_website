<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../config/database.php";


/* =====================================================
   GET VIDEO ID
===================================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: manage.php?error=invalid_id");
    exit;
}


$video_id = (int) $_GET["id"];


/* =====================================================
   CHECK VIDEO EXISTS
===================================================== */

$checkQuery = " SELECT id
    FROM videos
    WHERE id = ?
    LIMIT 1
";

$checkStmt = mysqli_prepare(
    $conn,
    $checkQuery
);

mysqli_stmt_bind_param(
    $checkStmt,
    "i",
    $video_id
);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result(
    $checkStmt
);


if (mysqli_num_rows($checkResult) === 0) {

    mysqli_stmt_close($checkStmt);

    header("Location: manage.php?error=not_found");
    exit;
}


mysqli_stmt_close($checkStmt);


/* =====================================================
   DELETE VIDEO
===================================================== */

$deleteQuery = " DELETE FROM videos
    WHERE id = ?
    LIMIT 1
";

$deleteStmt = mysqli_prepare(
    $conn,
    $deleteQuery
);


if ($deleteStmt === false) {

    header("Location: manage.php?error=delete");
    exit;
}


mysqli_stmt_bind_param(
    $deleteStmt,
    "i",
    $video_id
);


if (
    mysqli_stmt_execute(
        $deleteStmt
    )
) {

    mysqli_stmt_close($deleteStmt);

    header(
        "Location: manage.php?deleted=1"
    );

    exit;
}


mysqli_stmt_close($deleteStmt);


header(
    "Location: manage.php?error=delete"
);

exit;