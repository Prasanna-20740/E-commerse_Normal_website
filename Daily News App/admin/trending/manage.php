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
   SUCCESS MESSAGE
===================================================== */

$success = "";

if (isset($_GET["success"])) {

    if ($_GET["success"] === "1") {

        $success = "Trending topic added successfully.";

    } elseif ($_GET["success"] === "2") {

        $success = "Trending topic updated successfully.";

    } elseif ($_GET["success"] === "3") {

        $success = "Trending topic deleted successfully.";

    }

}


/* =====================================================
   FETCH TRENDING TOPICS
===================================================== */

$query = " SELECT
        id,
        topic_name,
        slug,
        views,
        status,
        created_at
    FROM trending_topics
    ORDER BY id DESC
";


$result = mysqli_query($conn, $query);


if (!$result) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

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
        Manage Trending Topics - Daily News
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f5f7;

            color: #111;

        }


        /* =================================================
           SIDEBAR
        ================================================= */

        .admin-sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 240px;

            height: 100vh;

            background: #111827;

            color: #ffffff;

            padding: 25px 15px;

            z-index: 1000;

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

            color: #ffffff;

        }


        .menu-item.active {

            background: #ef1111;

            color: #ffffff;

        }


        .logout-button {

            position: absolute;

            left: 15px;

            right: 15px;

            bottom: 25px;

            display: block;

            padding: 13px;

            text-align: center;

            background: #1f2937;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

        }


        .logout-button:hover {

            background: #ef1111;

        }


        /* =================================================
           MAIN CONTENT
        ================================================= */

        .main-content {

            margin-left: 240px;

            padding: 40px;

        }


        /* =================================================
           HEADER
        ================================================= */

        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 30px;

        }


        .page-header h1 {

            margin: 0;

            font-size: 32px;

        }


        .page-header p {

            margin: 8px 0 0;

            color: #64748b;

        }


        /* =================================================
           ADD BUTTON
        ================================================= */

        .add-button {

            display: inline-block;

            padding: 13px 20px;

            background: #ef1111;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            font-size: 14px;

            font-weight: 700;

        }


        .add-button:hover {

            background: #d90000;

        }


        /* =================================================
           SUCCESS
        ================================================= */

        .success-message {

            margin-bottom: 20px;

            padding: 14px 18px;

            border-radius: 7px;

            background: #dcfce7;

            color: #166534;

            font-size: 14px;

        }


        /* =================================================
           TABLE CARD
        ================================================= */

        .table-card {

            background: #ffffff;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.06);

        }


        .table-wrapper {

            width: 100%;

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 850px;

        }


        thead {

            background: #111827;

            color: #ffffff;

        }


        th {

            padding: 16px;

            text-align: left;

            font-size: 13px;

            white-space: nowrap;

        }


        td {

            padding: 16px;

            border-bottom:
                1px solid #eeeeee;

            font-size: 14px;

            vertical-align: middle;

        }


        tbody tr:hover {

            background: #f9fafb;

        }


        /* =================================================
           TOPIC
        ================================================= */

        .topic-name {

            font-weight: 700;

            color: #111827;

        }


        .slug {

            color: #64748b;

            font-size: 13px;

        }


        /* =================================================
           STATUS
        ================================================= */

        .status {

            display: inline-block;

            padding: 6px 11px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 700;

        }


        .status.active {

            background: #dcfce7;

            color: #166534;

        }


        .status.inactive {

            background: #fee2e2;

            color: #991b1b;

        }


        /* =================================================
           ACTIONS
        ================================================= */

        .actions {

            display: flex;

            gap: 7px;

        }


        .action-button {

            display: inline-block;

            padding: 8px 12px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 700;

        }


        .edit-button {

            background: #dbeafe;

            color: #1d4ed8;

        }


        .edit-button:hover {

            background: #bfdbfe;

        }


        .delete-button {

            background: #fee2e2;

            color: #dc2626;

        }


        .delete-button:hover {

            background: #fecaca;

        }


        /* =================================================
           EMPTY
        ================================================= */

        .empty-state {

            padding: 60px 20px;

            text-align: center;

            color: #64748b;

        }


        .empty-state-icon {

            font-size: 45px;

            margin-bottom: 15px;

        }


        .empty-state h2 {

            margin: 0 0 8px;

            color: #111827;

        }


        .empty-state p {

            margin: 0 0 20px;

        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 900px) {

            .admin-sidebar {

                width: 200px;

            }


            .main-content {

                margin-left: 200px;

                padding: 25px;

            }

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


            .page-header {

                flex-direction: column;

                align-items: flex-start;

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

        Daily <b>News</b>

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
            href="manage.php"
            class="menu-item active"
        >

            🔥
            <span>Trending Topics</span>

        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >

            📰
            <span>News</span>

        </a>


        <a
            href="../videos/manage.php"
            class="menu-item"
        >

            🎥
            <span>Videos</span>

        </a>


        <a
            href="../categories/manage.php"
            class="menu-item"
        >

            📂
            <span>Categories</span>

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


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="page-header">


        <div>

            <h1>
                Trending Topics
            </h1>

            <p>
                Manage all trending topics
                on your Daily News website.
            </p>

        </div>


        <a
            href="add.php"
            class="add-button"
        >

            + Add Trending Topic

        </a>


    </div>


    <!-- =================================================
         SUCCESS MESSAGE
    ================================================== -->

    <?php if (!empty($success)): ?>

        <div class="success-message">

            <?php

            echo htmlspecialchars(
                $success,
                ENT_QUOTES,
                "UTF-8"
            );

            ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         TABLE
    ================================================== -->

    <div class="table-card">


        <?php if (mysqli_num_rows($result) > 0): ?>


            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Trending Topic
                            </th>

                            <th>
                                Slug
                            </th>

                            <th>
                                Views
                            </th>

                            <th>
                                Status
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


                        <?php while ($topic = mysqli_fetch_assoc($result)): ?>


                            <tr>


                                <!-- ID -->

                                <td>

                                    <?php
                                    echo (int)$topic["id"];
                                    ?>

                                </td>


                                <!-- TOPIC -->

                                <td>

                                    <div class="topic-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $topic["topic_name"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                        ?>

                                    </div>

                                </td>


                                <!-- SLUG -->

                                <td>

                                    <div class="slug">

                                        <?php

                                        echo htmlspecialchars(
                                            $topic["slug"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                        ?>

                                    </div>

                                </td>


                                <!-- VIEWS -->

                                <td>

                                    👁

                                    <?php

                                    echo (int)
                                        $topic["views"];

                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td>


                                    <?php

                                    $statusClass =
                                        $topic["status"] === "active"
                                            ? "active"
                                            : "inactive";

                                    ?>


                                    <span
                                        class="status <?php
                                            echo $statusClass;
                                        ?>"
                                    >

                                        <?php

                                        echo ucfirst(
                                            $topic["status"]
                                        );

                                        ?>

                                    </span>


                                </td>


                                <!-- CREATED -->

                                <td>

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $topic["created_at"]
                                        )
                                    );

                                    ?>

                                </td>


                                <!-- ACTIONS -->

                                <td>


                                    <div class="actions">


                                        <a
                                            href="edit.php?id=<?php
                                                echo (int)$topic["id"];
                                            ?>"
                                            class="action-button edit-button"
                                        >

                                            Edit

                                        </a>


                                        <a
                                            href="delete.php?id=<?php
                                                echo (int)$topic["id"];
                                            ?>"
                                            class="action-button delete-button"
                                            onclick="
                                                return confirm(
                                                    'Are you sure you want to delete this trending topic?'
                                                );
                                            "
                                        >

                                            Delete

                                        </a>


                                    </div>


                                </td>


                            </tr>


                        <?php endwhile; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <!-- EMPTY -->

            <div class="empty-state">


                <div class="empty-state-icon">

                    🔥

                </div>


                <h2>

                    No Trending Topics

                </h2>


                <p>

                    You have not added any
                    trending topics yet.

                </p>


                <a
                    href="add.php"
                    class="add-button"
                >

                    + Add Trending Topic

                </a>


            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>