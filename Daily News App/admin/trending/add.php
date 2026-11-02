<?php
/* =====================================================
   ADMIN AUTHENTICATION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}


/* =====================================================
   DATABASE
===================================================== */

require_once "../../config/database.php";


/* =====================================================
   VARIABLES
===================================================== */

$error = "";
$topic_name = "";
$status = "active";


/* =====================================================
   FORM SUBMIT
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $topic_name = trim($_POST["topic_name"] ?? "");
    $status = $_POST["status"] ?? "active";


    /* =================================================
       VALIDATION
    ================================================= */

    if ($topic_name === "") {

        $error = "Please enter trending topic.";

    } elseif (!in_array($status, ["active", "inactive"], true)) {

        $error = "Invalid status.";

    } else {

        /* =================================================
           CREATE SLUG
        ================================================= */

        $slug = strtolower($topic_name);

        $slug = preg_replace(
            "/[^a-z0-9]+/",
            "-",
            $slug
        );

        $slug = trim($slug, "-");


        /* =================================================
           CHECK DUPLICATE SLUG
        ================================================= */

        $checkQuery = " SELECT id
            FROM trending_topics
            WHERE slug = ?
            LIMIT 1
        ";

        $checkStmt = mysqli_prepare($conn, $checkQuery);

        if (!$checkStmt) {

            $error = "Database error: " . mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $checkStmt,
                "s",
                $slug
            );

            mysqli_stmt_execute($checkStmt);

            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) > 0) {

                $error = "This trending topic already exists.";

            }

            mysqli_stmt_close($checkStmt);
        }


        /* =================================================
           INSERT
        ================================================= */

        if ($error === "") {

            $query = " INSERT INTO trending_topics
                (
                    topic_name,
                    slug,
                    views,
                    status
                )
                VALUES (?, ?, 0, ?)
            ";

            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {

                $error = "Database error: " . mysqli_error($conn);

            } else {

                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $topic_name,
                    $slug,
                    $status
                );


                if (mysqli_stmt_execute($stmt)) {

                    mysqli_stmt_close($stmt);

                    header("Location: manage.php?success=1");
                    exit;

                } else {

                    $error = "Database error: " .
                             mysqli_stmt_error($stmt);

                    mysqli_stmt_close($stmt);
                }
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
        Add Trending Topic - Daily News
    </title>


    <!-- IMPORTANT:
         trending/add.php -> ../../assets/
    -->

     <link
        rel="stylesheet"
        href="../assets/admin.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f5f7;

            color: #111;

        }


        
/* =====================================================
   ADMIN SIDEBAR
===================================================== */

.admin-sidebar {
    position: fixed;

    top: 0;
    left: 0;

    width: 315px;
    height: 100vh;

    background: #1f1f1f;
    color: #ffffff;

    padding: 30px 8px;

    overflow-y: auto;

    z-index: 1000;
}


/* =====================================================
   SIDEBAR LOGO
===================================================== */

.admin-brand {
    padding: 0 14px 32px;

    border-bottom: 1px solid #333333;
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
   SIDEBAR MENU
===================================================== */

.admin-menu {
    display: flex;

    flex-direction: column;

    gap: 6px;

    margin-top: 28px;
}


/* =====================================================
   MENU ITEM
===================================================== */

.menu-item {
    display: flex;

    align-items: center;

    gap: 8px;

    width: 100%;

    padding: 16px 20px;

    color: #dddddd;

    text-decoration: none;

    border-radius: 7px;

    font-size: 17px;

    font-weight: 500;

    transition: all 0.2s ease;
}


/* =====================================================
   MENU HOVER
===================================================== */

.menu-item:hover {
    background: #333333;

    color: #ffffff;

    transform: translateX(2px);
}


/* =====================================================
   ACTIVE MENU
===================================================== */

.menu-item.active {
    background: #ed1111;

    color: #ffffff;

    font-weight: 600;
}


.menu-item.active:hover {
    background: #ed1111;

    color: #ffffff;

    transform: none;
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

    text-decoration: none;

    border-radius: 7px;

    font-size: 17px;

    font-weight: 500;

    transition: all 0.2s ease;
}


.logout-button:hover {
    background: #ed1111;

    color: #ffffff;

    transform: translateY(-1px);
}


        .page-container {

             margin-left: 315px;

    min-height: 100vh;

    padding: 50px 40px;

        }


        .page-header {

            margin-bottom: 30px;

        }


        .page-header h1 {

            margin: 0;

            font-size: 38px;

        }


        .page-header p {

            margin-top: 8px;

            color: #64748b;

            font-size: 16px;

        }


        .form-card {

            background: #ffffff;

            border-radius: 15px;

            padding: 35px;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.06);

            max-width: 1000px;

        }


        .form-group {

            margin-bottom: 25px;

        }


        .form-group label {

            display: block;

            margin-bottom: 9px;

            font-weight: 700;

        }


        .required {

            color: red;

        }


        .form-group input,

        .form-group select {

            width: 100%;

            padding: 15px;

            border: 1px solid #d5d5d5;

            border-radius: 9px;

            font-size: 15px;

            outline: none;

        }


        .form-group input:focus,

        .form-group select:focus {

            border-color: #ef1111;

        }


        .help-text {

            display: block;

            margin-top: 7px;

            color: #718096;

            font-size: 13px;

        }


        .error-message {

            background: #fee2e2;

            color: #dc2626;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 25px;

        }


        .button-group {

            display: flex;

            gap: 12px;

        }


        .btn {

            border: none;

            padding: 14px 25px;

            border-radius: 8px;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            text-decoration: none;

            display: inline-block;

        }


        .btn-primary {

            background: #ef1111;

            color: #fff;

        }


        .btn-primary:hover {

            background: #d90000;

        }


        .btn-secondary {

            background: #333;

            color: #fff;

        }


        .btn-secondary:hover {

            background: #222;

        }


        @media (max-width: 900px) {

            .page-container {

                margin-left: 0;

                padding: 20px;

            }

        }


        @media (max-width: 600px) {

            .button-group {

                flex-direction: column;

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
            📊 <span>Dashboard</span>
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            🔥 <span>Trending Topics</span>
        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰 <span>News</span>
        </a>


        <a
            href="../videos/manage.php"
            class="menu-item"
        >
            🎥 <span>Videos</span>
        </a>


        <a
            href="../categories/manage.php"
            class="menu-item"
        >
            📂 <span>Categories</span>
        </a>


        <a
            href="../users/manage.php"
            class="menu-item"
        >
            👥 <span>Users</span>
        </a>


        <a
            href="../comments/manage.php"
            class="menu-item"
        >
            💬 <span>Comments</span>
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

<div class="page-container">


    <div class="page-header">

        <h1>
            Add Trending Topic
        </h1>

        <p>
            Add a new trending topic to your Daily News website
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


            <!-- TOPIC -->

            <div class="form-group">

                <label for="topic_name">

                    Trending Topic

                    <span class="required">
                        *
                    </span>

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
                    placeholder="Enter trending topic"
                    required
                >


                <small class="help-text">

                    Example:
                    RCB IPL Victory 2026

                </small>

            </div>


            <!-- STATUS -->

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


            <!-- BUTTONS -->

            <div class="button-group">


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    + Add Trending Topic
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


</div>


</body>

</html>