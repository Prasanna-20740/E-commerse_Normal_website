<?php

/* =====================================================
   ADMIN AUTHENTICATION
===================================================== */

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../config/database.php";


/* =====================================================
   CHECK VIDEO ID
===================================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: manage.php?error=invalid_id");

    exit;
}


$video_id = (int) $_GET["id"];


/* =====================================================
   GET VIDEO
===================================================== */

$query = " SELECT
        id,
        title,
        description,
        thumbnail,
        video_url,
        category_id,
        status
    FROM videos
    WHERE id = ?
    LIMIT 1
";


$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $video_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) !== 1) {

    header("Location: manage.php?error=not_found");

    exit;
}


$video = mysqli_fetch_assoc($result);


/* =====================================================
   FORM VALUES
===================================================== */

$title = $video["title"];

$description = $video["description"];

$thumbnail = $video["thumbnail"];

$video_url = $video["video_url"];

$category_id = $video["category_id"];

$status = $video["status"];

$error = "";


/* =====================================================
   UPDATE VIDEO
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $title = trim(
        $_POST["title"] ?? ""
    );


    $description = trim(
        $_POST["description"] ?? ""
    );


    $video_url = trim(
        $_POST["video_url"] ?? ""
    );


    $thumbnail = trim(
        $_POST["thumbnail"] ?? ""
    );


    $category_id = !empty(
        $_POST["category_id"]
    )
        ? (int) $_POST["category_id"]
        : null;


    $status = $_POST["status"] ?? "draft";


    /* =================================================
       VALIDATION
    ================================================= */

    if ($title === "") {

        $error =
            "Video title is required.";

    } elseif ($video_url === "") {

        $error =
            "Video URL is required.";

    } elseif (
        !in_array(
            $status,
            ["draft", "published"],
            true
        )
    ) {

        $error =
            "Invalid video status.";

    }


    /* =================================================
       UPDATE DATABASE
    ================================================= */

    if ($error === "") {


        $update_query = " UPDATE videos
            SET
                title = ?,
                description = ?,
                thumbnail = ?,
                video_url = ?,
                category_id = ?,
                status = ?
            WHERE id = ?
        ";


        $update_stmt = mysqli_prepare(
            $conn,
            $update_query
        );


        mysqli_stmt_bind_param(
            $update_stmt,
            "ssssisi",
            $title,
            $description,
            $thumbnail,
            $video_url,
            $category_id,
            $status,
            $video_id
        );


        if (
            mysqli_stmt_execute(
                $update_stmt
            )
        ) {

            header(
                "Location: manage.php?updated=1"
            );

            exit;

        } else {

            $error =
                "Database error: "
                . mysqli_error($conn);

        }

    }

}


/* =====================================================
   GET CATEGORIES
===================================================== */

$category_query = " SELECT
        id,
        name
    FROM categories
    WHERE status = 'active'
    ORDER BY name ASC
";


$category_result = mysqli_query(
    $conn,
    $category_query
);

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
        Edit Video - Daily News
    </title>


    <style>

        /* =====================================================
           GLOBAL
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

            width: 315px;
            height: 100vh;

            background: #1f1f1f;

            color: #ffffff;

            padding: 30px 8px;

            overflow-y: auto;

            z-index: 1000;
        }


        .admin-brand {

            padding:
                0 14px 32px;

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

            border-radius: 7px;

            font-size: 17px;

            font-weight: 500;

            transition:
                all 0.2s ease;
        }


        .menu-item:hover {

            background: #333333;

            color: #ffffff;

            transform:
                translateX(2px);
        }


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

            width:
                calc(100% - 10px);

            margin:
                50px 5px 0;

            padding: 16px;

            background: #333333;

            color: #ffffff;

            border-radius: 7px;

            font-size: 17px;

            font-weight: 500;

            transition:
                all 0.2s ease;
        }


        .logout-button:hover {

            background: #ed1111;

            color: #ffffff;

            transform:
                translateY(-1px);
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .admin-main {

            margin-left: 315px;

            min-height: 100vh;

            padding:
                50px 40px;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .admin-header {

            margin-bottom: 35px;
        }


        .admin-header h1 {

            font-size: 38px;

            line-height: 1.2;

            margin-bottom: 10px;
        }


        .admin-header p {

            font-size: 17px;

            color: #687384;
        }


        /* =====================================================
           ERROR MESSAGE
        ===================================================== */

        .error-message {

            background: #ffe5e5;

            color: #c40000;

            padding:
                13px 16px;

            margin-bottom: 25px;

            border-radius: 7px;

            font-size: 14px;
        }


        /* =====================================================
           FORM CARD
        ===================================================== */

        .form-card {

            background: #ffffff;

            padding: 38px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.05);
        }


        /* =====================================================
           FORM GROUP
        ===================================================== */

        .form-group {

            margin-bottom: 25px;
        }


        .form-group label {

            display: block;

            margin-bottom: 10px;

            font-size: 16px;

            font-weight: 700;

            color: #111111;
        }


        .required {

            color: #ed1111;
        }


        /* =====================================================
           INPUTS
        ===================================================== */

        .form-control {

            width: 100%;

            padding:
                15px 17px;

            border:
                1px solid #d8d8d8;

            border-radius: 8px;

            font-size: 16px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            outline: none;

            transition:
                border 0.2s ease;
        }


        .form-control:focus {

            border-color: #ed1111;
        }


        textarea.form-control {

            min-height: 150px;

            resize: vertical;
        }


        select.form-control {

            background: #ffffff;

            cursor: pointer;
        }


        /* =====================================================
           HELP TEXT
        ===================================================== */

        .help-text {

            margin-top: 7px;

            color: #777777;

            font-size: 13px;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .form-actions {

            display: flex;

            gap: 12px;

            margin-top: 10px;
        }


        .update-button {

            border: none;

            padding:
                14px 24px;

            background: #ed1111;

            color: #ffffff;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;
        }


        .update-button:hover {

            background: #c90000;
        }


        .cancel-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                14px 24px;

            background: #333333;

            color: #ffffff;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 700;
        }


        .cancel-button:hover {

            background: #222222;

            color: #ffffff;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 900px) {

            .admin-sidebar {

                width: 250px;
            }


            .admin-main {

                margin-left: 250px;

                padding:
                    35px 25px;
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

                padding:
                    30px 20px;
            }


            .form-card {

                padding: 25px;
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
            Edit Video
        </h1>

        <p>
            Update your Daily News video
        </p>

    </header>


    <?php if (!empty($error)): ?>

        <div class="error-message">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         FORM
    ================================================== -->

    <div class="form-card">


        <form
            method="POST"
            action=""
        >


            <!-- VIDEO TITLE -->

            <div class="form-group">

                <label for="title">

                    Video Title

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
                    class="form-control"
                    placeholder="Enter video description"
                ><?php
                echo htmlspecialchars($description);
                ?></textarea>

            </div>


            <!-- VIDEO URL -->

            <div class="form-group">

                <label for="video_url">

                    Video URL

                    <span class="required">
                        *
                    </span>

                </label>


                <input
                    type="url"
                    id="video_url"
                    name="video_url"
                    class="form-control"
                    value="<?php
                    echo htmlspecialchars($video_url);
                    ?>"
                    placeholder="https://www.youtube.com/watch?v=..."
                    required
                >


                <div class="help-text">

                    Example:
                    https://www.youtube.com/watch?v=xxxx

                </div>

            </div>


            <!-- THUMBNAIL -->

            <div class="form-group">

                <label for="thumbnail">
                    Thumbnail URL
                </label>


                <input
                    type="url"
                    id="thumbnail"
                    name="thumbnail"
                    class="form-control"
                    value="<?php
                    echo htmlspecialchars($thumbnail);
                    ?>"
                    placeholder="https://example.com/image.jpg"
                >


                <div class="help-text">

                    Optional. Enter the thumbnail image URL.

                </div>

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category_id">
                    Category
                </label>


                <select
                    id="category_id"
                    name="category_id"
                    class="form-control"
                >


                    <option value="">
                        Select Category
                    </option>


                    <?php

                    if (
                        $category_result &&
                        mysqli_num_rows(
                            $category_result
                        ) > 0
                    ):

                    ?>


                        <?php while (
                            $category =
                            mysqli_fetch_assoc(
                                $category_result
                            )
                        ): ?>


                            <option
                                value="<?php
                                echo (int)
                                    $category["id"];
                                ?>"
                                <?php

                                if (
                                    (int)
                                    $category_id
                                    ===
                                    (int)
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
                    class="form-control"
                >


                    <option
                        value="draft"
                        <?php
                        echo
                            $status === "draft"
                            ? "selected"
                            : "";
                        ?>
                    >
                        Draft
                    </option>


                    <option
                        value="published"
                        <?php
                        echo
                            $status === "published"
                            ? "selected"
                            : "";
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
                    class="update-button"
                >
                    Update Video
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