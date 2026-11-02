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
   FETCH BREAKING NEWS
===================================================== */

$query = " SELECT
        id,
        title,
        link,
        status
    FROM breaking_news
    WHERE id = ?
    LIMIT 1
";


$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$breaking = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$breaking) {

    header("Location: manage.php?error=notfound");
    exit;
}


/* =====================================================
   VARIABLES
===================================================== */

$title = $breaking["title"];
$link = $breaking["link"];
$status = $breaking["status"];

$error = "";


/* =====================================================
   UPDATE
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $link = trim($_POST["link"] ?? "");
    $status = $_POST["status"] ?? "active";


    /* =================================================
       VALIDATION
    ================================================= */

    if ($title === "") {

        $error = "Please enter breaking news title.";

    } elseif (
        $status !== "active" &&
        $status !== "inactive"
    ) {

        $error = "Invalid status.";

    } else {


        /* =============================================
           UPDATE QUERY
        ============================================= */

        $updateQuery = " UPDATE breaking_news
            SET
                title = ?,
                link = ?,
                status = ?
            WHERE id = ?
        ";


        $updateStmt = mysqli_prepare(
            $conn,
            $updateQuery
        );


        if ($updateStmt === false) {

            $error =
                "Database error: " .
                mysqli_error($conn);

        } else {


            mysqli_stmt_bind_param(
                $updateStmt,
                "sssi",
                $title,
                $link,
                $status,
                $id
            );


            if (
                mysqli_stmt_execute(
                    $updateStmt
                )
            ) {

                mysqli_stmt_close(
                    $updateStmt
                );


                header(
                    "Location: manage.php?updated=1"
                );

                exit;

            } else {

                $error =
                    "Unable to update breaking news: " .
                    mysqli_stmt_error(
                        $updateStmt
                    );

                mysqli_stmt_close(
                    $updateStmt
                );
            }
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Breaking News - Daily News
    </title>


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f5f7;

            color: #111111;
        }


        a {
            text-decoration: none;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .admin-sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 268px;

            height: 100vh;

            background: #1f1f1f;

            color: #ffffff;

            padding: 30px 0;

            overflow-y: auto;

            z-index: 1000;
        }


        .admin-brand {

            padding: 0 16px 32px;

            border-bottom:
                1px solid #333333;
        }


        .admin-brand span {

            font-size: 30px;

            font-weight: 700;

            color: #ffffff;
        }


        .admin-brand b {

            color: #ed1111;
        }


        /* =====================================================
           MENU
        ===================================================== */

        .admin-menu {

            display: flex;

            flex-direction: column;

            gap: 6px;

            margin-top: 28px;
        }


        .menu-item {

            display: flex;

            align-items: center;

            gap: 8px;

            width: 100%;

            padding: 16px 24px;

            color: #dddddd;

            font-size: 17px;

            font-weight: 500;

            transition:
                all 0.2s ease;
        }


        .menu-item:hover {

            background: #333333;

            color: #ffffff;
        }


        .menu-item.active {

            background: #ed1111;

            color: #ffffff;

            font-weight: 600;
        }


        /* =====================================================
           LOGOUT
        ===================================================== */

        .logout-button {

            display: flex;

            align-items: center;

            justify-content: center;

            width: calc(100% - 10px);

            margin: 50px 5px 0;

            padding: 16px;

            background: #333333;

            color: #ffffff;

            border-radius: 7px;

            font-size: 17px;

            font-weight: 500;
        }


        .logout-button:hover {

            background: #ed1111;

            color: #ffffff;
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main-content {

            margin-left: 268px;

            min-height: 100vh;

            padding: 50px 40px;
        }


        /* =====================================================
           PAGE TITLE
        ===================================================== */

        .page-title {

            margin: 0;

            font-size: 40px;

            line-height: 1.2;

            color: #111111;
        }


        .page-description {

            margin: 10px 0 40px;

            color: #687384;

            font-size: 17px;
        }


        /* =====================================================
           ERROR
        ===================================================== */

        .error-message {

            max-width: 900px;

            margin-bottom: 20px;

            padding: 13px 15px;

            background: #ffe5e5;

            color: #c40000;

            border-radius: 7px;

            font-size: 14px;
        }


        /* =====================================================
           FORM CARD
        ===================================================== */

        .form-card {

            max-width: 900px;

            background: #ffffff;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.05);
        }


        /* =====================================================
           FORM GROUP
        ===================================================== */

        .form-group {

            margin-bottom: 22px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 600;
        }


        .required {

            color: #ed1111;
        }


        /* =====================================================
           INPUT
        ===================================================== */

        .form-control {

            width: 100%;

            padding: 13px 14px;

            border:
                1px solid #d9d9d9;

            border-radius: 7px;

            background: #ffffff;

            color: #111111;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 14px;

            outline: none;
        }


        .form-control:focus {

            border-color: #ed1111;

            box-shadow:
                0 0 0 3px
                rgba(237, 17, 17, 0.10);
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .form-actions {

            display: flex;

            align-items: center;

            gap: 10px;

            margin-top: 30px;
        }


        .update-button {

            padding: 12px 22px;

            border: none;

            border-radius: 7px;

            background: #ed1111;

            color: #ffffff;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;
        }


        .update-button:hover {

            background: #c90000;
        }


        .cancel-button {

            display: inline-block;

            padding: 12px 22px;

            background: #333333;

            color: #ffffff;

            border-radius: 7px;

            font-size: 14px;

            font-weight: 600;
        }


        .cancel-button:hover {

            background: #222222;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 900px) {

            .admin-sidebar {

                width: 250px;
            }


            .main-content {

                margin-left: 250px;

                padding: 35px 25px;
            }

        }


        @media (max-width: 650px) {

            .admin-sidebar {

                position: relative;

                width: 100%;

                height: auto;
            }


            .main-content {

                margin-left: 0;

                padding: 30px 20px;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="admin-sidebar">


    <div class="admin-brand">

        <span>
            Daily <b>News</b>
        </span>

    </div>


    <nav class="admin-menu">


        <a
            href="../dashboard.php"
            class="menu-item"
        >
            📊
            <span>Dashboard</span>
        </a>


        <a
            href="../categories/manage.php"
            class="menu-item"
        >
            📂
            <span>Categories</span>
        </a>


        <a
            href="../subcategories/manage.php"
            class="menu-item"
        >
            📁
            <span>Subcategories</span>
        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰
            <span>News</span>
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            🔴
            <span>Breaking News</span>
        </a>


        <a
            href="../users/manage.php"
            class="menu-item"
        >
            👥
            <span>Users</span>
        </a>


        <a
            href="../comments/manage.php"
            class="menu-item"
        >
            💬
            <span>Comments</span>
        </a>


    </nav>


    <a
        href="../logout.php"
        class="logout-button"
    >
        🚪 Logout
    </a>


</aside>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="main-content">


    <h1 class="page-title">

        Edit Breaking News

    </h1>


    <p class="page-description">

        Update your breaking news announcement

    </p>



    <?php if ($error !== ""): ?>

        <div class="error-message">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>



    <div class="form-card">


        <form
            method="POST"
            action=""
        >


            <!-- TITLE -->

            <div class="form-group">

                <label for="title">

                    Breaking News Title

                    <span class="required">
                        *
                    </span>

                </label>


                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="<?php
                    echo htmlspecialchars($title);
                    ?>"
                    maxlength="255"
                    required
                >

            </div>



            <!-- LINK -->

            <div class="form-group">

                <label for="link">

                    News Link

                </label>


                <input
                    type="text"
                    id="link"
                    name="link"
                    class="form-control"
                    value="<?php
                    echo htmlspecialchars($link);
                    ?>"
                    maxlength="500"
                >

            </div>



            <!-- STATUS -->

            <div class="form-group">

                <label for="status">

                    Status

                </label>


                <select
                    id="status"
                    name="status"
                    class="form-control"
                >

                    <option
                        value="active"
                        <?php
                        echo $status === "active"
                            ? "selected"
                            : "";
                        ?>
                    >
                        Active
                    </option>


                    <option
                        value="inactive"
                        <?php
                        echo $status === "inactive"
                            ? "selected"
                            : "";
                        ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>



            <!-- ACTIONS -->

            <div class="form-actions">


                <button
                    type="submit"
                    class="update-button"
                >

                    Update Breaking News

                </button>


                <a
                    href="manage.php"
                    class="cancel-button"
                >

                    Cancel

                </a>


            </div>


        </form>


    </div>


</main>


</body>

</html>