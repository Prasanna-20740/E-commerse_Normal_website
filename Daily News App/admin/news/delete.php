<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


/* =========================
   GET NEWS ID
========================= */

$id = isset($_GET["id"])
    ? intval($_GET["id"])
    : 0;


if ($id <= 0) {

    header(
        "Location: manage.php?error=invalid"
    );

    exit;

}


/* =========================
   GET NEWS IMAGE
========================= */

$query = " SELECT image
    FROM news
    WHERE id = ?
";

$stmt = mysqli_prepare(
    $conn,
    $query
);


if ($stmt === false) {

    header(
        "Location: manage.php?error=database"
    );

    exit;

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result($stmt);


$news =
    mysqli_fetch_assoc($result);


if (!$news) {

    header(
        "Location: manage.php?error=notfound"
    );

    exit;

}


/* =========================
   DELETE NEWS
========================= */

$deleteQuery = " DELETE FROM news
    WHERE id = ?
";


$deleteStmt =
    mysqli_prepare(
        $conn,
        $deleteQuery
    );


if ($deleteStmt === false) {

    header(
        "Location: manage.php?error=database"
    );

    exit;

}


mysqli_stmt_bind_param(
    $deleteStmt,
    "i",
    $id
);


if (
    mysqli_stmt_execute(
        $deleteStmt
    )
) {


    /* =========================
       DELETE IMAGE
    ========================= */

    if (
        !empty(
            $news["image"]
        )
    ) {

        $imagePath =
            "../../uploads/news/" .
            $news["image"];


        if (
            file_exists(
                $imagePath
            )
        ) {

            unlink(
                $imagePath
            );

        }

    }


    /* SUCCESS */

    header(
        "Location: manage.php?deleted=1"
    );

    exit;

}


/* ERROR */

header(
    "Location: manage.php?error=delete"
);

exit;

?>