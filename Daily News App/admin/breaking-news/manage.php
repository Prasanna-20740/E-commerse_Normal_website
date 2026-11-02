<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


/* =====================================================
   GET ALL BREAKING NEWS
===================================================== */

$query = " SELECT *
    FROM breaking_news
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);


/* =====================================================
   SUCCESS / ERROR MESSAGE
===================================================== */

$success = "";

$error = "";


if (isset($_GET["success"])) {

    $success = "Breaking news added successfully.";

}


if (isset($_GET["updated"])) {

    $success = "Breaking news updated successfully.";

}


if (isset($_GET["deleted"])) {

    $success = "Breaking news deleted successfully.";

}


if (isset($_GET["error"])) {

    $error = "Unable to complete the action.";

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
        Manage Breaking News - Daily News
    </title>

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

            padding: 30px 0;

            overflow-y: auto;

            z-index: 1000;
        }


        /* =====================================================
           BRAND
        ===================================================== */

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

            border-radius: 0;

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


        .menu-item.active:hover {

            background: #ed1111;

            color: #ffffff;
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

            transition:
                all 0.2s ease;
        }


        .logout-button:hover {

            background: #ed1111;

            color: #ffffff;
        }


        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .main-content {

              margin-left: 315px;

    min-height: 100vh;

    padding:
        50px 40px;
        }


        /* =====================================================
           PAGE HEADER
        ===================================================== */

        .page-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 35px;
        }


        .page-title {

            margin: 0;

            font-size: 40px;

            line-height: 1.2;

            color: #111111;
        }


        .page-description {

            margin-top: 10px;

            color: #687384;

            font-size: 17px;
        }


        /* =====================================================
           ADD BUTTON
        ===================================================== */

        .add-button {

            display: inline-block;

            padding: 13px 20px;

            background: #ed1111;

            color: #ffffff;

            border-radius: 7px;

            font-size: 14px;

            font-weight: 600;

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

            padding: 13px 15px;

            margin-bottom: 20px;

            border-radius: 7px;

            font-size: 14px;
        }


        .error-message {

            background: #ffe5e5;

            color: #c40000;

            padding: 13px 15px;

            margin-bottom: 20px;

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

        .breaking-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 850px;
        }


        .breaking-table th {

            padding: 15px;

            background: #f3f3f3;

            color: #111111;

            text-align: left;

            font-size: 14px;

            font-weight: 700;
        }


        .breaking-table td {

            padding: 16px 15px;

            border-bottom:
                1px solid #eeeeee;

            text-align: left;

            font-size: 14px;

            color: #333333;

            vertical-align: middle;
        }


        .breaking-table tbody tr:hover {

            background: #fafafa;
        }


        /* =====================================================
           BREAKING TITLE
        ===================================================== */

        .breaking-title {

            font-weight: 600;

            line-height: 1.5;

            max-width: 400px;
        }


        /* =====================================================
           LINK
        ===================================================== */

        .news-link {

            color: #2563eb;

            font-size: 13px;

            word-break: break-all;
        }


        .news-link:hover {

            text-decoration: underline;
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
           ACTION BUTTONS
        ===================================================== */

        .edit-button {

            display: inline-block;

            padding: 8px 13px;

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

            padding: 8px 13px;

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
           EMPTY
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


            .page-header {

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
            class="menu-item active"
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

    <div class="page-header">


        <div>

            <h1 class="page-title">

                Breaking News

            </h1>


            <p class="page-description">

                Manage your breaking news announcements

            </p>

        </div>


       <a
    href="./add.php"
    class="add-button"
>
    + Add Breaking News
</a>

    </div>



    <!-- =================================================
         SUCCESS MESSAGE
    ================================================== -->

    <?php if (!empty($success)): ?>

        <div class="success-message">

            <?php

            echo htmlspecialchars(
                $success
            );

            ?>

        </div>

    <?php endif; ?>



    <!-- =================================================
         ERROR MESSAGE
    ================================================== -->

    <?php if (!empty($error)): ?>

        <div class="error-message">

            <?php

            echo htmlspecialchars(
                $error
            );

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


            <table class="breaking-table">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Breaking News
                        </th>

                        <th>
                            Link
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


                    <?php while (
                        $row =
                        mysqli_fetch_assoc($result)
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php

                                echo $row["id"];

                                ?>

                            </td>



                            <!-- TITLE -->

                            <td>

                                <div class="breaking-title">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["title"]
                                    );

                                    ?>

                                </div>

                            </td>



                            <!-- LINK -->

                            <td>

                                <?php

                                if (
                                    !empty($row["link"])
                                ):

                                ?>

                                    <a
                                        href="<?php
                                        echo htmlspecialchars(
                                            $row["link"]
                                        );
                                        ?>"
                                        class="news-link"
                                        target="_blank"
                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            $row["link"]
                                        );

                                        ?>

                                    </a>

                                <?php else: ?>

                                    <span>
                                        —
                                    </span>

                                <?php endif; ?>

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
                                        'Are you sure you want to delete this breaking news?'
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

                No breaking news found.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>