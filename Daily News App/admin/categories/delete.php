<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {

    header("Location: manage.php");

    exit;
}


$query = "
    DELETE FROM categories
    WHERE id = ?
";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: manage.php?deleted=1"
    );

    exit;

} else {

    header(
        "Location: manage.php?error=1"
    );

    exit;
}

?>