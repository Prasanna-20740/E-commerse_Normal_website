<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


/* =====================================================
   GET ALL CATEGORIES
===================================================== */

$query = "
    SELECT *
    FROM categories
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);


/* =====================================================
   MESSAGE
===================================================== */

$message = "";
$message_type = "";


if (isset($_GET["deleted"])) {

    $message = "Category deleted successfully.";
    $message_type = "success";

}


if (isset($_GET["error"])) {

    $message = "Unable to delete category.";
    $message_type = "error";

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
        Manage Categories - Daily News
    </title>


    <!-- ADMIN CSS -->
     <link rel="stylesheet" href="../assets/admin.css">

   


    <style>

        /* ==========================================
           PAGE ACTIONS
        ========================================== */

        .page-actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .page-actions h2 {

            margin: 0;

            font-size: 28px;

        }


        /* ==========================================
           ADD BUTTON
        ========================================== */

        .add-button {

            display: inline-block;

            padding: 12px 20px;

            background: #ed1111;

            color: #ffffff;

            text-decoration: none;

            border-radius: 7px;

            font-size: 15px;

            font-weight: 600;

        }


        .add-button:hover {

            background: #c90000;

            color: #ffffff;

        }


        /* ==========================================
           TABLE CARD
        ========================================== */

        .table-card {

            background: #ffffff;

            padding: 22px;

            border-radius: 12px;

            overflow-x: auto;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.05);

        }


        /* ==========================================
           TABLE
        ========================================== */

        .category-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;

        }


        .category-table th {

            padding: 15px;

            background: #f3f3f3;

            color: #111111;

            text-align: left;

            font-size: 14px;

            font-weight: 700;

        }


        .category-table td {

            padding: 16px 15px;

            border-bottom:
                1px solid #eeeeee;

            text-align: left;

            font-size: 14px;

            color: #333333;

            vertical-align: middle;

        }


        .category-table tbody tr:hover {

            background: #fafafa;

        }


        /* ==========================================
           STATUS
        ========================================== */

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


        /* ==========================================
           BUTTONS
        ========================================== */

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


        /* ==========================================
           MESSAGE
        ========================================== */

        .success-message {

            background: #e5f7e8;

            color: #16803c;

            padding: 12px 15px;

            margin-bottom: 20px;

            border-radius: 6px;

        }


        .error-message {

            background: #ffe5e5;

            color: #c40000;

            padding: 12px 15px;

            margin-bottom: 20px;

            border-radius: 6px;

        }


        /* ==========================================
           EMPTY
        ========================================== */

        .empty-message {

            text-align: center;

            padding: 45px 20px;

            color: #777777;

            font-size: 16px;

        }



/* =====================================================
   MAIN CONTENT
===================================================== */

.admin-main {
    margin-left: 315px;

    min-height: 100vh;

    padding: 50px 40px;
}


/* =====================================================
   HEADER
===================================================== */

.admin-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 50px;
}


.admin-header h1 {
    font-size: 38px;

    line-height: 1.2;

    margin-bottom: 12px;

    color: #111111;
}


.admin-header p {
    font-size: 17px;

    color: #687384;
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
    </style>

</head>


<body class="admin-page">


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
            class="menu-item active"
        >
            📂
            <span>
                Categories
            </span>
        </a>


        <!-- SUBCATEGORIES -->

        <a
            href="../subcategories/manage.php"
            class="menu-item"
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
            href="manage.php"
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

<main class="admin-main">


    <!-- HEADER -->

    <header class="admin-header">

        <div>

            <h1>
                Categories
            </h1>

            <p>
                Manage your news categories
            </p>

        </div>

    </header>



    <!-- MESSAGE -->

    <?php if ($message !== ""): ?>


        <div
            class="<?php
                echo $message_type === "success"
                    ? "success-message"
                    : "error-message";
            ?>"
        >

            <?php

            echo htmlspecialchars(
                $message,
                ENT_QUOTES,
                "UTF-8"
            );

            ?>

        </div>


    <?php endif; ?>



    <!-- PAGE ACTIONS -->

    <div class="page-actions">


        <h2>
            All Categories
        </h2>


        <a
            href="add.php"
            class="add-button"
        >

            + Add Category

        </a>


    </div>



    <!-- TABLE -->

    <div class="table-card">


        <?php

        if (
            $result &&
            mysqli_num_rows($result) > 0
        ):

        ?>


            <table
                class="category-table"
            >


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Name
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


                <?php

                while (
                    $category =
                    mysqli_fetch_assoc($result)
                ):

                ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php

                            echo (int)
                                $category["id"];

                            ?>

                        </td>



                        <!-- NAME -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $category["name"] ?? "",
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- SLUG -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $category["slug"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            );

                            ?>

                        </td>



                        <!-- DESCRIPTION -->

                        <td>

                            <?php

                            $description =
                                $category["description"]
                                ?? "";

                            if (
                                strlen($description) > 50
                            ) {

                                $description =
                                    substr(
                                        $description,
                                        0,
                                        50
                                    ) . "...";

                            }

                            echo htmlspecialchars(
                                $description,
                                ENT_QUOTES,
                                "UTF-8"
                            );

                            ?>

                        </td>



                        <!-- STATUS -->

                        <td>

                            <?php

                            $status =
                                strtolower(
                                    $category["status"]
                                    ?? "inactive"
                                );

                            ?>

                            <span
                                class="status
                                <?php
                                echo htmlspecialchars(
                                    $status,
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                ?>"
                            >

                                <?php

                                echo ucfirst(
                                    htmlspecialchars(
                                        $status,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    )
                                );

                                ?>

                            </span>

                        </td>



                        <!-- CREATED -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $category["created_at"]
                                )
                            ) {

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $category["created_at"]
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>



                        <!-- ACTIONS -->

                        <td>


                            <!-- EDIT -->

                            <a
                                href="edit.php?id=<?php
                                    echo (int)
                                        $category["id"];
                                ?>"
                                class="edit-button"
                            >

                                Edit

                            </a>



                            <!-- DELETE -->

                            <a
                                href="delete.php?id=<?php
                                    echo (int)
                                        $category["id"];
                                ?>"
                                class="delete-button"
                                onclick="
                                    return confirm(
                                        'Are you sure you want to delete this category?'
                                    );
                                "
                            >

                                Delete

                            </a>


                        </td>


                    </tr>


                <?php

                endwhile;

                ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="empty-message">

                No categories found.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>