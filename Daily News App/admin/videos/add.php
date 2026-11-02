<?php

/* =====================================================
   ADMIN AUTH
===================================================== */

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../config/database.php";


/* =====================================================
   VARIABLES
===================================================== */

$title = "";
$description = "";
$thumbnail = "";
$video_url = "";
$category_id = "";
$status = "draft";

$error = "";


/* =====================================================
   GET CATEGORIES
===================================================== */

$categoryQuery = " SELECT id, name
    FROM categories
    WHERE status = 'active'
    ORDER BY name ASC
";

$categoryResult = mysqli_query(
    $conn,
    $categoryQuery
);


/* =====================================================
   FORM SUBMIT
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");

    $description = trim(
        $_POST["description"] ?? ""
    );

    $video_url = trim(
        $_POST["video_url"] ?? ""
    );

    $thumbnail = trim(
        $_POST["thumbnail"] ?? ""
    );

    $category_id = !empty($_POST["category_id"])
        ? (int) $_POST["category_id"]
        : null;

    $status = $_POST["status"] ?? "draft";


    /* =================================================
       VALIDATION
    ================================================= */

    if ($title === "") {

        $error = "Please enter video title.";

    } elseif ($video_url === "") {

        $error = "Please enter video URL.";

    } elseif (
        !filter_var(
            $video_url,
            FILTER_VALIDATE_URL
        )
    ) {

        $error = "Please enter a valid video URL.";

    } elseif (
        !in_array(
            $status,
            ["draft", "published"],
            true
        )
    ) {

        $error = "Invalid video status.";

    } else {


        /* =============================================
           INSERT VIDEO
        ============================================= */

        $query = " INSERT INTO videos
            (
                title,
                description,
                thumbnail,
                video_url,
                category_id,
                status
            )
            VALUES
            (?, ?, ?, ?, ?, ?)
        ";


        $stmt = mysqli_prepare(
            $conn,
            $query
        );


        if ($stmt === false) {

            $error =
                "Database error: " .
                mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $title,
                $description,
                $thumbnail,
                $video_url,
                $category_id,
                $status
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {

                mysqli_stmt_close($stmt);

                header(
                    "Location: manage.php?added=1"
                );

                exit;

            } else {

                $error =
                    "Database error: " .
                    mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
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
        Add Video - Daily News
    </title>


    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f5f7;
            color: #111111;
        }


        /* ==========================================
           SIDEBAR
        ========================================== */

        .admin-sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 315px;
            height: 100vh;

            background: #1f1f1f;

            color: #ffffff;

            padding: 30px 8px;

            z-index: 1000;
        }


        .admin-brand {

            padding: 0 14px 32px;

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

            padding: 16px 20px;

            color: #dddddd;

            text-decoration: none;

            border-radius: 7px;

            font-size: 17px;

            font-weight: 500;
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
        }


        .logout-button:hover {

            background: #ed1111;

            color: #ffffff;
        }


        /* ==========================================
           MAIN
        ========================================== */

        .admin-main {

            margin-left: 315px;

            min-height: 100vh;

            padding: 50px 40px;
        }


        .admin-header {

            margin-bottom: 35px;
        }


        .admin-header h1 {

            margin: 0 0 10px;

            font-size: 38px;
        }


        .admin-header p {

            margin: 0;

            color: #687384;

            font-size: 17px;
        }


        /* ==========================================
           FORM CARD
        ========================================== */

        .form-card {

            background: #ffffff;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.05);

            max-width: 100%;
        }


        .form-group {

            margin-bottom: 24px;
        }


        .form-group label {

            display: block;

            margin-bottom: 9px;

            font-size: 15px;

            font-weight: 700;
        }


        .required {

            color: #ed1111;
        }


        .form-group input,
        .form-group textarea,
        .form-group select {

            width: 100%;

            padding: 14px 16px;

            border: 1px solid #d7d7d7;

            border-radius: 8px;

            font-size: 15px;

            font-family: Arial, Helvetica, sans-serif;

            outline: none;

            background: #ffffff;
        }


        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {

            border-color: #ed1111;

            box-shadow:
                0 0 0 2px
                rgba(237, 17, 17, 0.08);
        }


        .form-group textarea {

            min-height: 150px;

            resize: vertical;
        }


        .form-help {

            display: block;

            margin-top: 7px;

            color: #777777;

            font-size: 13px;
        }


        /* ==========================================
           BUTTONS
        ========================================== */

        .form-actions {

            display: flex;

            gap: 12px;

            margin-top: 30px;
        }


        .save-button {

            padding: 13px 24px;

            background: #ed1111;

            color: #ffffff;

            border: none;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;
        }


        .save-button:hover {

            background: #c90000;
        }


        .cancel-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 13px 24px;

            background: #333333;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 700;
        }


        .cancel-button:hover {

            background: #222222;
        }


        /* ==========================================
           ERROR
        ========================================== */

        .error-message {

            background: #ffe5e5;

            color: #c40000;

            padding: 14px 16px;

            margin-bottom: 25px;

            border-radius: 7px;

            font-size: 14px;
        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 900px) {

            .admin-sidebar {
                width: 250px;
            }

            .admin-main {
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

            .admin-main {

                margin-left: 0;

                padding: 30px 20px;
            }

            .form-card {

                padding: 22px;
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
            href="../categories/manage.php"
            class="menu-item"
        >
            📂 <span>Categories</span>
        </a>


        <a
            href="../subcategories/manage.php"
            class="menu-item"
        >
            📁 <span>Subcategories</span>
        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰 <span>News</span>
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            🎥 <span>Videos</span>
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


        <a
            href="../breaking-news/manage.php"
            class="menu-item"
        >
            🔴 <span>Breaking News</span>
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

<main class="admin-main">


    <header class="admin-header">

        <h1>
            Add Video
        </h1>

        <p>
            Add a new video to your Daily News website
        </p>

    </header>


    <?php if (!empty($error)): ?>

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

                    Video Title
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php
                        echo htmlspecialchars($title);
                    ?>"
                    placeholder="Enter video title"
                    required
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">

                    Description

                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter video description"
                ><?php
                    echo htmlspecialchars($description);
                ?></textarea>

            </div>


            <!-- VIDEO URL -->

            <div class="form-group">

                <label for="video_url">

                    Video URL
                    <span class="required">*</span>

                </label>

                <input
                    type="url"
                    id="video_url"
                    name="video_url"
                    value="<?php
                        echo htmlspecialchars($video_url);
                    ?>"
                    placeholder="https://www.youtube.com/watch?v=..."
                    required
                >

                <small class="form-help">

                    Example:
                    https://www.youtube.com/watch?v=xxxx

                </small>

            </div>


            <!-- THUMBNAIL -->

            <div class="form-group">

                <label for="thumbnail">

                    Thumbnail URL

                </label>

                <input
                    type="text"
                    id="thumbnail"
                    name="thumbnail"
                    value="<?php
                        echo htmlspecialchars($thumbnail);
                    ?>"
                    placeholder="https://example.com/image.jpg"
                >

                <small class="form-help">

                    Optional. Enter the thumbnail image URL.

                </small>

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category_id">

                    Category

                </label>

                <select
                    id="category_id"
                    name="category_id"
                >

                    <option value="">
                        Select Category
                    </option>


                    <?php if (
                        $categoryResult &&
                        mysqli_num_rows($categoryResult) > 0
                    ): ?>

                        <?php while (
                            $category =
                            mysqli_fetch_assoc(
                                $categoryResult
                            )
                        ): ?>

                            <option
                                value="<?php
                                    echo $category["id"];
                                ?>"
                                <?php
                                if (
                                    $category_id ==
                                    $category["id"]
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $category["name"]
                                );
                                ?>

                            </option>

                        <?php endwhile; ?>

                    <?php endif; ?>

                </select>

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
                        value="draft"
                        <?php
                        if ($status === "draft") {
                            echo "selected";
                        }
                        ?>
                    >
                        Draft
                    </option>

                    <option
                        value="published"
                        <?php
                        if ($status === "published") {
                            echo "selected";
                        }
                        ?>
                    >
                        Published
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="save-button"
                >
                    + Add Video
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