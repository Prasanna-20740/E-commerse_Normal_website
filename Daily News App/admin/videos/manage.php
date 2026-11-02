<?php

/* =====================================================
   ADMIN AUTH
===================================================== */

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../config/database.php";


/* =====================================================
   GET VIDEOS
===================================================== */

$query = " SELECT
        v.id,
        v.title,
        v.description,
        v.thumbnail,
        v.video_url,
        v.status,
        v.views,
        v.created_at,
        c.name AS category_name
    FROM videos v
    LEFT JOIN categories c
        ON v.category_id = c.id
    ORDER BY v.id DESC
";

$result = mysqli_query($conn, $query);


/* =====================================================
   MESSAGES
===================================================== */

$success = "";
$error = "";


if (isset($_GET["added"])) {

    $success = "Video added successfully.";

}


if (isset($_GET["updated"])) {

    $success = "Video updated successfully.";

}


if (isset($_GET["deleted"])) {

    $success = "Video deleted successfully.";

}


if (isset($_GET["error"])) {

    $error = "Unable to process video.";

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
        Manage Videos - Daily News
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
           MAIN CONTENT
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

            display: flex;

            justify-content:
                space-between;

            align-items: center;

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
           ADD BUTTON
        ===================================================== */

        .add-button {

            display: inline-block;

            padding:
                13px 20px;

            background: #ed1111;

            color: #ffffff;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 700;

            transition:
                background 0.2s ease;
        }


        .add-button:hover {

            background: #c90000;

            color: #ffffff;
        }


        /* =====================================================
           MESSAGE
        ===================================================== */

        .success-message {

            background: #e5f7e8;

            color: #16803c;

            padding:
                13px 16px;

            margin-bottom: 25px;

            border-radius: 7px;

            font-size: 14px;
        }


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
           TABLE CARD
        ===================================================== */

        .table-card {

            background: #ffffff;

            padding: 22px;

            border-radius: 12px;

            overflow-x: auto;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.05);
        }


        /* =====================================================
           TABLE
        ===================================================== */

        .video-table {

            width: 100%;

            min-width: 1050px;

            border-collapse:
                collapse;
        }


        .video-table th {

            padding:
                15px;

            background: #f3f3f3;

            color: #111111;

            text-align: left;

            font-size: 14px;

            font-weight: 700;
        }


        .video-table td {

            padding:
                16px 15px;

            border-bottom:
                1px solid #eeeeee;

            text-align: left;

            font-size: 14px;

            color: #333333;

            vertical-align: middle;
        }


        .video-table tbody tr:hover {

            background: #fafafa;
        }


        /* =====================================================
           THUMBNAIL
        ===================================================== */

        .video-thumbnail {

            width: 110px;

            height: 65px;

            object-fit: cover;

            border-radius: 6px;

            background: #eeeeee;

            display: block;
        }


        .no-thumbnail {

            width: 110px;

            height: 65px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eeeeee;

            color: #888888;

            border-radius: 6px;

            font-size: 12px;
        }


        /* =====================================================
           TITLE
        ===================================================== */

        .video-title {

            font-weight: 700;

            color: #111111;

            max-width: 280px;

            line-height: 1.4;
        }


        .video-description {

            margin-top: 5px;

            color: #777777;

            font-size: 12px;

            max-width: 300px;

            line-height: 1.4;
        }


        /* =====================================================
           CATEGORY
        ===================================================== */

        .category-name {

            color: #444444;

            font-weight: 500;
        }


        .no-category {

            color: #999999;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status {

            display: inline-block;

            padding:
                6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }


        .status.published {

            background: #d9f3df;

            color: #16803c;
        }


        .status.draft {

            background: #fff3cd;

            color: #856404;
        }


        /* =====================================================
           VIEWS
        ===================================================== */

        .views {

            font-weight: 600;

            color: #555555;
        }


        /* =====================================================
           ACTION BUTTONS
        ===================================================== */

        .edit-button {

            display: inline-block;

            padding:
                8px 13px;

            background: #2563eb;

            color: #ffffff;

            border-radius: 5px;

            font-size: 12px;

            font-weight: 600;

            margin-right: 5px;
        }


        .edit-button:hover {

            background: #1d4ed8;

            color: #ffffff;
        }


        .delete-button {

            display: inline-block;

            padding:
                8px 13px;

            background: #dc2626;

            color: #ffffff;

            border-radius: 5px;

            font-size: 12px;

            font-weight: 600;
        }


        .delete-button:hover {

            background: #b91c1c;

            color: #ffffff;
        }


        /* =====================================================
           WATCH BUTTON
        ===================================================== */

        .watch-button {

            display: inline-block;

            margin-top: 7px;

            color: #ed1111;

            font-size: 12px;

            font-weight: 600;
        }


        .watch-button:hover {

            text-decoration: underline;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-message {

            text-align: center;

            padding:
                45px 20px;

            color: #777777;

            font-size: 16px;
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

            .admin-header {

                flex-direction: column;

                align-items: flex-start;

                gap: 20px;
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
     MAIN
===================================================== -->

<main class="admin-main">


    <header class="admin-header">


        <div>

            <h1>
                Videos
            </h1>

            <p>
                Manage your website videos
            </p>

        </div>


        <a
            href="add.php"
            class="add-button"
        >
            + Add Video
        </a>


    </header>


    <!-- =================================================
         SUCCESS MESSAGE
    ================================================== -->

    <?php if (!empty($success)): ?>

        <div class="success-message">

            <?php
            echo htmlspecialchars($success);
            ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         ERROR MESSAGE
    ================================================== -->

    <?php if (!empty($error)): ?>

        <div class="error-message">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         TABLE
    ================================================== -->

    <div class="table-card">


        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ): ?>


            <table class="video-table">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Thumbnail
                        </th>

                        <th>
                            Title
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Views
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php while (
                        $video =
                        mysqli_fetch_assoc($result)
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php
                                echo (int)
                                    $video["id"];
                                ?>

                            </td>


                            <!-- THUMBNAIL -->

                            <td>


                                <?php
                                if (
                                    !empty(
                                        $video["thumbnail"]
                                    )
                                ):
                                ?>


                                    <img
                                        src="<?php
                                        echo htmlspecialchars(
                                            $video["thumbnail"]
                                        );
                                        ?>"
                                        alt="Video Thumbnail"
                                        class="video-thumbnail"
                                    >


                                <?php else: ?>


                                    <div
                                        class="no-thumbnail"
                                    >
                                        No Image
                                    </div>


                                <?php endif; ?>


                            </td>


                            <!-- TITLE -->

                            <td>


                                <div
                                    class="video-title"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $video["title"]
                                    );
                                    ?>

                                </div>


                                <?php
                                if (
                                    !empty(
                                        $video["description"]
                                    )
                                ):
                                ?>


                                    <div
                                        class="video-description"
                                    >

                                        <?php

                                        $description =
                                            $video[
                                                "description"
                                            ];

                                        if (
                                            strlen(
                                                $description
                                            ) > 70
                                        ) {

                                            $description =
                                                substr(
                                                    $description,
                                                    0,
                                                    70
                                                ) . "...";
                                        }

                                        echo htmlspecialchars(
                                            $description
                                        );

                                        ?>

                                    </div>


                                <?php endif; ?>


                                <a
                                    href="<?php
                                    echo htmlspecialchars(
                                        $video["video_url"]
                                    );
                                    ?>"
                                    target="_blank"
                                    class="watch-button"
                                >
                                    ▶ Watch Video
                                </a>


                            </td>


                            <!-- CATEGORY -->

                            <td>


                                <?php
                                if (
                                    !empty(
                                        $video["category_name"]
                                    )
                                ):
                                ?>


                                    <span
                                        class="category-name"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $video[
                                                "category_name"
                                            ]
                                        );
                                        ?>

                                    </span>


                                <?php else: ?>


                                    <span
                                        class="no-category"
                                    >
                                        No Category
                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- STATUS -->

                            <td>


                                <span
                                    class="status <?php
                                    echo htmlspecialchars(
                                        $video["status"]
                                    );
                                    ?>"
                                >

                                    <?php
                                    echo ucfirst(
                                        $video["status"]
                                    );
                                    ?>

                                </span>


                            </td>


                            <!-- VIEWS -->

                            <td>

                                <span
                                    class="views"
                                >

                                    👁
                                    <?php
                                    echo (int)
                                        $video["views"];
                                    ?>

                                </span>

                            </td>


                            <!-- CREATED -->

                            <td>

                                <?php
                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $video[
                                            "created_at"
                                        ]
                                    )
                                );
                                ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>


                                <a
                                    href="edit.php?id=<?php
                                    echo (int)
                                        $video["id"];
                                    ?>"
                                    class="edit-button"
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete.php?id=<?php
                                    echo (int)
                                        $video["id"];
                                    ?>"
                                    class="delete-button"
                                    onclick="return confirm('Are you sure you want to delete this video?');"
                                >
                                    Delete
                                </a>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="empty-message">

                No videos found.

                <br><br>

                <a
                    href="add.php"
                    class="add-button"
                >
                    + Add Your First Video
                </a>

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>