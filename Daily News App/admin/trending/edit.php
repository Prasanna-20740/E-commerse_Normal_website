<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/database.php";

$error = "";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: manage.php");
    exit;
}


/* =====================================================
   FETCH TOPIC
===================================================== */

$query = " SELECT
        id,
        topic_name,
        slug,
        status
    FROM trending_topics
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$topic = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$topic) {
    header("Location: manage.php");
    exit;
}


$topic_name = $topic["topic_name"];
$status = $topic["status"];


/* =====================================================
   UPDATE
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $topic_name = trim($_POST["topic_name"] ?? "");
    $status = $_POST["status"] ?? "active";


    if ($topic_name === "") {

        $error = "Please enter trending topic.";

    } elseif (!in_array($status, ["active", "inactive"], true)) {

        $error = "Invalid status.";

    } else {

        /* CREATE SLUG */

        $slug = strtolower($topic_name);

        $slug = preg_replace(
            "/[^a-z0-9]+/",
            "-",
            $slug
        );

        $slug = trim($slug, "-");


        /* CHECK DUPLICATE */

        $checkQuery = "
            SELECT id
            FROM trending_topics
            WHERE slug = ?
            AND id != ?
            LIMIT 1
        ";

        $checkStmt = mysqli_prepare(
            $conn,
            $checkQuery
        );

        mysqli_stmt_bind_param(
            $checkStmt,
            "si",
            $slug,
            $id
        );

        mysqli_stmt_execute($checkStmt);

        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {

            $error = "Another trending topic already uses this name.";

        }

        mysqli_stmt_close($checkStmt);


        /* UPDATE */

        if ($error === "") {

            $updateQuery = " UPDATE trending_topics
                SET
                    topic_name = ?,
                    slug = ?,
                    status = ?
                WHERE id = ?
            ";

            $updateStmt = mysqli_prepare(
                $conn,
                $updateQuery
            );

            mysqli_stmt_bind_param(
                $updateStmt,
                "sssi",
                $topic_name,
                $slug,
                $status,
                $id
            );


            if (mysqli_stmt_execute($updateStmt)) {

                mysqli_stmt_close($updateStmt);

                header(
                    "Location: manage.php?success=2"
                );

                exit;

            } else {

                $error =
                    "Database error: " .
                    mysqli_stmt_error($updateStmt);

                mysqli_stmt_close($updateStmt);
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
        Edit Trending Topic - Daily News
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f5f7;
        }

        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #111827;
            color: #fff;
            padding: 25px 15px;
        }

        .admin-brand {
            padding: 10px 15px 30px;
            font-size: 24px;
            font-weight: 700;
        }

        .admin-brand b {
            color: #ef1111;
        }

        .admin-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 15px;
            border-radius: 7px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
        }

        .menu-item:hover {
            background: #1f2937;
            color: #fff;
        }

        .menu-item.active {
            background: #ef1111;
            color: #fff;
        }

        .logout-button {
            position: absolute;
            left: 15px;
            right: 15px;
            bottom: 25px;
            padding: 13px;
            text-align: center;
            background: #1f2937;
            color: #fff;
            text-decoration: none;
            border-radius: 7px;
        }

        .main-content {
            margin-left: 240px;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 32px;
        }

        .page-header p {
            color: #64748b;
            margin-top: 8px;
        }

        .form-card {
            max-width: 900px;
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,.06);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 1px solid #d5d5d5;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #ef1111;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 13px 22px;
            border: 0;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #ef1111;
            color: #fff;
        }

        .btn-secondary {
            background: #333;
            color: #fff;
        }

        @media (max-width: 700px) {

            .admin-sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .logout-button {
                position: static;
                margin-top: 20px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<aside class="admin-sidebar">

    <div class="admin-brand">
        Daily <b>News</b>
    </div>


    <nav class="admin-menu">

        <a
            href="../dashboard.php"
            class="menu-item"
        >
            📊 Dashboard
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            🔥 Trending Topics
        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰 News
        </a>


        <a
            href="../videos/manage.php"
            class="menu-item"
        >
            🎥 Videos
        </a>


        <a
            href="../categories/manage.php"
            class="menu-item"
        >
            📂 Categories
        </a>


        <a
            href="../users/manage.php"
            class="menu-item"
        >
            👥 Users
        </a>


        <a
            href="../comments/manage.php"
            class="menu-item"
        >
            💬 Comments
        </a>

    </nav>


    <a
        href="../logout.php"
        class="logout-button"
    >
        🚪 Logout
    </a>

</aside>


<main class="main-content">


    <div class="page-header">

        <h1>
            Edit Trending Topic
        </h1>

        <p>
            Update your trending topic details.
        </p>

    </div>


    <div class="form-card">


        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >


            <div class="form-group">

                <label for="topic_name">

                    Trending Topic

                </label>


                <input
                    type="text"
                    id="topic_name"
                    name="topic_name"
                    value="<?php
                        echo htmlspecialchars(
                            $topic_name,
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="status">

                    Status

                </label>


                <select
                    id="status"
                    name="status"
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


            <div class="button-group">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update Trending Topic
                </button>


                <a
                    href="manage.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>


        </form>


    </div>


</main>


</body>

</html>