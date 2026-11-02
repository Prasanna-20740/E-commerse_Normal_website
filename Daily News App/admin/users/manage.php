<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


/* =====================================
   FETCH ALL USERS
===================================== */

$query = " SELECT
        id,
        name,
        email,
        created_at
    FROM users
    ORDER BY id DESC
";


$result = mysqli_query(
    $conn,
    $query
);


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
        User Management - Daily News
    </title>

    <link rel="stylesheet" href="../assets/admin.css">

    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                sans-serif;

            background: #f4f5f7;

            color: #111;

        }



        /* =====================================
           MAIN
        ===================================== */

        .admin-main {

            margin-left: 315px;

    min-height: 100vh;

    padding: 50px 40px;

        }


        .admin-header {

            margin-bottom: 45px;

        }


        .admin-header h1 {

            margin: 0;

            font-size: 38px;

        }


        .admin-header p {

            margin-top: 10px;

            color: #777;

            font-size: 16px;

        }


        /* =====================================
           SECTION
        ===================================== */

        .section-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;

        }


        .section-header h2 {

            margin: 0;

            font-size: 28px;

        }


        /* =====================================
           USERS CARD
        ===================================== */

        .users-card {

            background: white;

            border-radius: 10px;

            padding: 25px;

            box-shadow:
                0 2px 10px
                rgba(0, 0, 0, 0.05);

            overflow-x: auto;

        }


        /* =====================================
           TABLE
        ===================================== */

        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 750px;

        }


        thead {

            background: #f1f1f1;

        }


        th {

            text-align: left;

            padding: 16px 12px;

            font-size: 14px;

            font-weight: bold;

        }


        td {

            padding: 18px 12px;

            border-bottom:
                1px solid #eee;

            vertical-align: middle;

            font-size: 14px;

        }


        tbody tr:hover {

            background: #fafafa;

        }


        /* =====================================
           USER NAME
        ===================================== */

        .user-info {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .user-avatar {

            width: 42px;

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #eeeeee;

            font-size: 20px;

        }


        .user-name {

            font-weight: bold;

            font-size: 15px;

        }


        .user-email {

            color: #777;

            margin-top: 4px;

            font-size: 13px;

        }


        /* =====================================
           ID
        ===================================== */

        .user-id {

            font-weight: bold;

            color: #555;

        }


        /* =====================================
           DATE
        ===================================== */

        .date {

            color: #555;

            white-space: nowrap;

        }


        /* =====================================
           ACTIONS
        ===================================== */

        .actions {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;

        }


        .action-button {

            display: inline-block;

            padding: 9px 14px;

            border-radius: 5px;

            color: white;

            text-decoration: none;

            border: none;

            font-size: 12px;

            font-weight: bold;

            cursor: pointer;

        }


        .view-button {

            background: #2563eb;

        }


        .view-button:hover {

            background: #1d4ed8;

        }


        .delete-button {

            background: #ed1111;

        }


        .delete-button:hover {

            background: #c90000;

        }


        /* =====================================
           EMPTY
        ===================================== */

        .no-users {

            text-align: center;

            padding: 60px 20px;

            color: #777;

            font-size: 17px;

        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 900px) {

            .admin-sidebar {

                width: 220px;

            }


            .admin-main {

                margin-left: 220px;

                padding: 30px 20px;

            }

        }


        @media (max-width: 600px) {

            .admin-sidebar {

                width: 200px;

            }


            .admin-main {

                margin-left: 200px;

                padding: 25px 15px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================
     SIDEBAR
===================================== -->
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
            class="menu-item "
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
            class="menu-item active"
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



<!-- =====================================
     MAIN CONTENT
===================================== -->

<main class="admin-main">


    <header class="admin-header">

        <h1>

            User Management

        </h1>


        <p>

            Manage registered users of Daily News

        </p>

    </header>


    <!-- =====================================
         ALL USERS
    ===================================== -->

    <div class="section-header">

        <h2>

            All Users

        </h2>

    </div>


    <div class="users-card">


        <?php if (
            mysqli_num_rows($result) > 0
        ): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            User
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Registered
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php while (
                        $user =
                        mysqli_fetch_assoc($result)
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <span class="user-id">

                                    #

                                    <?php
                                    echo (int)
                                        $user["id"];
                                    ?>

                                </span>

                            </td>


                            <!-- USER -->

                            <td>

                                <div class="user-info">


                                    <div class="user-avatar">

                                        👤

                                    </div>


                                    <div>


                                        <div
                                            class="user-name"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $user["name"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );
                                            ?>

                                        </div>


                                    </div>


                                </div>

                            </td>


                            <!-- EMAIL -->

                            <td>

                                <div class="user-email">

                                    <?php
                                    echo htmlspecialchars(
                                        $user["email"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </div>

                            </td>


                            <!-- REGISTERED DATE -->

                            <td>

                                <div class="date">

                                    <?php

                                    if (
                                        !empty(
                                            $user["created_at"]
                                        )
                                    ) {

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $user["created_at"]
                                            )
                                        );

                                    } else {

                                        echo "-";

                                    }

                                    ?>

                                </div>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="actions">


                                    <a
                                        href="view.php?id=<?php
                                            echo (int)
                                            $user["id"];
                                        ?>"
                                        class="action-button view-button"
                                    >

                                        View

                                    </a>


                                    <a
                                        href="delete.php?id=<?php
                                            echo (int)
                                            $user["id"];
                                        ?>"
                                        class="action-button delete-button"
                                        onclick="return confirm(
                                            'Are you sure you want to delete this user?'
                                        );"
                                    >

                                        Delete

                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-users">

                👥 No registered users found.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>