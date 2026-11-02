<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";


$query = " SELECT
        n.id,
        n.title,
        n.slug,
        n.description,
        n.image,
        n.author,
        n.status,
        n.views,
        n.published_at,
        n.created_at,

        c.name AS category_name,

        s.name AS subcategory_name

    FROM news n

    LEFT JOIN categories c
        ON n.category_id = c.id

    LEFT JOIN subcategories s
        ON n.subcategory_id = s.id

    ORDER BY n.id DESC
";


$result = mysqli_query(
    $conn,
    $query
);


if ($result === false) {

    die(
        "SQL Error: " .
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
        Manage News - Daily News
    </title>


    <link rel="stylesheet" href="../assets/admin.css">


    <style>

        .page-actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .add-button {

            display: inline-block;

            padding: 12px 20px;

            background: #e50909;

            color: #ffffff;

            text-decoration: none;

            border-radius: 6px;

            font-weight: 600;

        }


        .table-card {

            background: #ffffff;

            padding: 20px;

            border-radius: 10px;

            overflow-x: auto;

        }


        .news-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1100px;

        }


        .news-table th,
        .news-table td {

            padding: 14px;

            border-bottom:
                1px solid #eeeeee;

            text-align: left;

        }


        .news-table th {

            background: #f5f5f5;

            font-size: 13px;

        }


        .news-table td {

            font-size: 14px;

        }


        .news-image {

            width: 90px;

            height: 60px;

            object-fit: cover;

            border-radius: 6px;

            display: block;

        }


        .no-image {

            width: 90px;

            height: 60px;

            background: #eeeeee;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 6px;

            color: #777777;

            font-size: 12px;

        }


        .title-cell {

            max-width: 250px;

            font-weight: 600;

        }


        .category-badge {

            display: inline-block;

            padding: 5px 9px;

            background: #eeeeee;

            border-radius: 15px;

            font-size: 12px;

        }


        .status {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 15px;

            font-size: 12px;

        }


        .status.published {

            background: #e5f7e8;

            color: #16803c;

        }


        .status.draft {

            background: #fff4d6;

            color: #9a6700;

        }


        .edit-button {

            display: inline-block;

            padding: 7px 12px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            font-size: 12px;

            margin-right: 4px;

        }


        .delete-button {

            display: inline-block;

            padding: 7px 12px;

            background: #dc2626;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            font-size: 12px;

        }


        .empty-message {

            text-align: center;

            padding: 40px;

            color: #777777;

        }


        .success-message {

            padding: 12px;

            margin-bottom: 20px;

            background: #e5f7e8;

            color: #16803c;

            border-radius: 6px;

        }


        .error-message {

            padding: 12px;

            margin-bottom: 20px;

            background: #ffe5e5;

            color: #c40000;

            border-radius: 6px;

        }

        /* =====================================================
   RESET
===================================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =====================================================
   BODY
===================================================== */

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f5f7;
    color: #111111;
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


/* =====================================================
   ADD CATEGORY BUTTON
===================================================== */

.add-button {
    display: inline-block;

    padding: 12px 20px;

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
   CATEGORY TABLE
===================================================== */

.category-table {
    width: 100%;

    border-collapse: collapse;

    min-width: 900px;
}


/* =====================================================
   TABLE HEADER
===================================================== */

.category-table th {
    padding: 15px;

    background: #f3f3f3;

    color: #111111;

    text-align: left;

    font-size: 14px;

    font-weight: 700;
}


/* =====================================================
   TABLE DATA
===================================================== */

.category-table td {
    padding: 16px 15px;

    border-bottom:
        1px solid #eeeeee;

    text-align: left;

    font-size: 14px;

    color: #333333;

    vertical-align: middle;
}


/* =====================================================
   TABLE HOVER
===================================================== */

.category-table tbody tr:hover {
    background: #fafafa;
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
   EDIT BUTTON
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


/* =====================================================
   DELETE BUTTON
===================================================== */

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
   SUCCESS MESSAGE
===================================================== */

.success-message {
    background: #e5f7e8;

    color: #16803c;

    padding: 12px 15px;

    margin-bottom: 20px;

    border-radius: 6px;
}


/* =====================================================
   ERROR MESSAGE
===================================================== */

.error-message {
    background: #ffe5e5;

    color: #c40000;

    padding: 12px 15px;

    margin-bottom: 20px;

    border-radius: 6px;
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

    .admin-main {
        margin-left: 250px;

        padding: 40px 25px;
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
            class="menu-item active"
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




<!-- MAIN -->

<main class="admin-main">


    <header class="admin-header">

        <div>

            <h1>
                News Management
            </h1>

            <p>
                Manage all your news articles
            </p>

        </div>

    </header>


    <!-- ACTION -->

    <div class="page-actions">

        <h2>
            All News
        </h2>


        <a
            href="add.php"
            class="add-button"
        >
            + Add News
        </a>

    </div>


    <!-- TABLE -->

    <div class="table-card">


        <?php if (
            mysqli_num_rows($result) > 0
        ): ?>


            <table class="news-table">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Image
                        </th>

                        <th>
                            Title
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Subcategory
                        </th>

                        <th>
                            Author
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Views
                        </th>

                        <th>
                            Published
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php while (
                    $row =
                    mysqli_fetch_assoc(
                        $result
                    )
                ): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php
                            echo $row["id"];
                            ?>

                        </td>


                        <!-- IMAGE -->

                        <td>


                            <?php if (
                                !empty(
                                    $row["image"]
                                )
                            ): ?>

                                <img
                                    src="../../uploads/news/<?php
                                    echo htmlspecialchars(
                                        $row["image"]
                                    );
                                    ?>"
                                    class="news-image"
                                    alt="News"
                                >

                            <?php else: ?>

                                <div
                                    class="no-image"
                                >
                                    No Image
                                </div>

                            <?php endif; ?>


                        </td>


                        <!-- TITLE -->

                        <td
                            class="title-cell"
                        >

                            <?php
                            echo htmlspecialchars(
                                $row["title"]
                            );
                            ?>

                        </td>


                        <!-- CATEGORY -->

                        <td>

                            <span
                                class="category-badge"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $row["category_name"]
                                    ?? "-"
                                );
                                ?>

                            </span>

                        </td>


                        <!-- SUBCATEGORY -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row["subcategory_name"]
                                ?? "-"
                            );
                            ?>

                        </td>


                        <!-- AUTHOR -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row["author"]
                                ?? ""
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


                        <!-- VIEWS -->

                        <td>

                            👁️
                            <?php
                            echo (int)
                                $row["views"];
                            ?>

                        </td>


                        <!-- PUBLISHED DATE -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row[
                                        "published_at"
                                    ]
                                )
                            ) {

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $row[
                                            "published_at"
                                        ]
                                    )
                                );

                            } else {

                                echo "-";

                            }

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
                                onclick="return confirm('Are you sure you want to delete this news?');"
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

                📰 No news found.

                <br><br>

                Click
                <strong>
                    + Add News
                </strong>
                to publish your first article.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>