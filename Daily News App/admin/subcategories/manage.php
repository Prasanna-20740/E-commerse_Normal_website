<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


/* =====================================================
   GET ALL SUBCATEGORIES
===================================================== */

$query = " SELECT
        s.id,
        s.name,
        s.slug,
        s.description,
        s.status,
        s.created_at,
        c.name AS category_name
    FROM subcategories s
    INNER JOIN categories c
        ON s.category_id = c.id
    ORDER BY s.id DESC
";

$result = mysqli_query($conn, $query);

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
        Manage Subcategories - Daily News
    </title>


    <!-- ADMIN CSS -->
     <link rel="stylesheet" href="../assets/admin.css">

    <style>

        /* =====================================================
           GLOBAL RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            padding: 0;
        }


        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f5f7;
            color: #111111;
        }


        a {
            text-decoration: none;
        }

        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .main-content {

           margin-left: 315px;

    min-height: 100vh;

    padding: 50px 40px;
        }

    

        /* =====================================================
           PAGE HEADER
        ===================================================== */

        .page-title {

            margin: 0;

            font-size: 40px;

            line-height: 1.2;

            color: #111111;
        }


        .page-description {

            margin: 10px 0 45px;

            color: #687384;

            font-size: 17px;
        }


        /* =====================================================
           PAGE ACTIONS
        ===================================================== */

        .page-actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;
        }


        .page-actions h2 {

            margin: 0;

            font-size: 28px;

            color: #111111;
        }


        /* =====================================================
           ADD BUTTON
        ===================================================== */

        .add-button {

            display: inline-block;

            padding: 13px 20px;

            background: #ed1111;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 600;

            transition: 0.2s ease;
        }


        .add-button:hover {

            background: #c90000;

            color: #ffffff;
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

        .subcategory-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 950px;
        }


        .subcategory-table th {

            padding: 15px;

            background: #f3f3f3;

            color: #111111;

            text-align: left;

            font-size: 14px;

            font-weight: 700;
        }


        .subcategory-table td {

            padding: 16px 15px;

            border-bottom:
                1px solid #eeeeee;

            text-align: left;

            font-size: 14px;

            color: #333333;

            vertical-align: middle;
        }


        .subcategory-table tbody tr:hover {

            background: #fafafa;
        }


        /* =====================================================
           CATEGORY BADGE
        ===================================================== */

        .category-badge {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            background: #eeeeee;

            color: #333333;

            font-size: 12px;

            font-weight: 600;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }


        .status.active {

            background: #d9f3df;

            color: #16803c;
        }


        .status.inactive {

            background: #ffe0e0;

            color: #c40000;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .edit-button {

            display: inline-block;

            padding: 8px 13px;

            background: #2563eb;

            color: #ffffff;

            text-decoration: none;

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

            padding: 8px 13px;

            background: #dc2626;

            color: #ffffff;

            text-decoration: none;

            border-radius: 5px;

            font-size: 12px;

            font-weight: 600;
        }


        .delete-button:hover {

            background: #b91c1c;

            color: #ffffff;
        }


        /* =====================================================
           EMPTY MESSAGE
        ===================================================== */

        .empty-message {

            text-align: center;

            padding: 45px 20px;

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


            .page-actions {

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

    <!-- LOGO -->

    <div class="admin-brand">

        <span>
            Daily <b>News</b>
        </span>

    </div>


    <!-- MENU -->

    <nav class="admin-menu">


        <!-- DASHBOARD -->

        <a
            href="../dashboard.php"
            class="menu-item"
        >
            📊
            <span>
                Dashboard
            </span>
        </a>


        <!-- CATEGORIES -->

        <a
            href="../categories/manage.php"
            class="menu-item "
        >
            📂
            <span>
                Categories
            </span>
        </a>


        <!-- SUBCATEGORIES -->

        <a
            href="../subcategories/manage.php"
            class="menu-item active"
        >
            📁
            <span>
                Subcategories
            </span>
        </a>


        <!-- NEWS -->

        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰
            <span>
                News
            </span>
        </a>


        <a
            href="../videos/manage.php"
            class="menu-item "
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


    <!-- LOGOUT -->

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


    <!-- PAGE HEADER -->

    <h1 class="page-title">
        Subcategories
    </h1>


    <p class="page-description">
        Manage your news subcategories
    </p>



    <!-- PAGE ACTIONS -->

    <div class="page-actions">

        <h2>
            All Subcategories
        </h2>


        <a
            href="add.php"
            class="add-button"
        >
            + Add Subcategory
        </a>

    </div>



    <!-- TABLE -->

    <div class="table-card">


        <?php if (mysqli_num_rows($result) > 0): ?>


            <table class="subcategory-table">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Subcategory
                        </th>

                        <th>
                            Slug
                        </th>

                        <th>
                            Description
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


                    <?php while ($row = mysqli_fetch_assoc($result)): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php
                                echo $row["id"];
                                ?>

                            </td>



                            <!-- CATEGORY -->

                            <td>

                                <span class="category-badge">

                                    <?php
                                    echo htmlspecialchars(
                                        $row["category_name"]
                                    );
                                    ?>

                                </span>

                            </td>



                            <!-- SUBCATEGORY -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $row["name"]
                                    );
                                    ?>

                                </strong>

                            </td>



                            <!-- SLUG -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["slug"]
                                );
                                ?>

                            </td>



                            <!-- DESCRIPTION -->

                            <td>

                                <?php

                                $description =
                                    $row["description"];

                                if (
                                    strlen($description) > 40
                                ) {

                                    $description =
                                        substr(
                                            $description,
                                            0,
                                            40
                                        ) . "...";
                                }

                                echo htmlspecialchars(
                                    $description
                                );

                                ?>

                            </td>



                            <!-- STATUS -->

                            <td>

                                <span
                                    class="status <?php
                                    echo htmlspecialchars(
                                        $row["status"]
                                    );
                                    ?>"
                                >

                                    <?php
                                    echo ucfirst(
                                        $row["status"]
                                    );
                                    ?>

                                </span>

                            </td>



                            <!-- CREATED -->

                            <td>

                                <?php

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $row["created_at"]
                                    )
                                );

                                ?>

                            </td>



                            <!-- ACTIONS -->

                            <td>

                                <a
                                    href="edit.php?id=<?php
                                    echo $row["id"];
                                    ?>"
                                    class="edit-button"
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete.php?id=<?php
                                    echo $row["id"];
                                    ?>"
                                    class="delete-button"
                                    onclick="return confirm(
                                        'Delete this subcategory?'
                                    );"
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

                No subcategories found.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>