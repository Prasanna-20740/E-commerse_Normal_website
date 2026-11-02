<?php


require_once "includes/auth.php";
require_once "../config/database.php";


/* =====================================================
   DASHBOARD COUNTS
===================================================== */


/* TOTAL NEWS */

$total_news = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM news"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_news = (int)($row["total"] ?? 0);
}


/* PUBLISHED NEWS */

$published_news = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM news
     WHERE status = 'published'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $published_news = (int)($row["total"] ?? 0);
}


/* TOTAL USERS */

$total_users = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_users = (int)($row["total"] ?? 0);
}


/* PENDING COMMENTS */

$pending_comments = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM comments
     WHERE status = 'pending'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $pending_comments = (int)($row["total"] ?? 0);
}


/* DRAFT NEWS */

$draft_news = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM news
     WHERE status = 'draft'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $draft_news = (int)($row["total"] ?? 0);
}


/* TOTAL COMMENTS */

$total_comments = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM comments"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_comments = (int)($row["total"] ?? 0);
}


/* =====================================================
   RECENT NEWS
===================================================== */

$recent_news_query = " SELECT
        id,
        title,
        status,
        views,
        created_at
    FROM news
    ORDER BY id DESC
    LIMIT 5
";

$recent_news_result = mysqli_query(
    $conn,
    $recent_news_query
);


/* =====================================================
   RECENT COMMENTS
===================================================== */

$recent_comments_query = " SELECT
        c.id,
        c.comment,
        c.status,
        c.created_at,
        u.name AS user_name,
        n.title AS news_title
    FROM comments c
    LEFT JOIN users u
        ON c.user_id = u.id
    LEFT JOIN news n
        ON c.news_id = n.id
    ORDER BY c.id DESC
    LIMIT 5
";

$recent_comments_result = mysqli_query(
    $conn,
    $recent_comments_query
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

    <title>Daily News - Admin Dashboard</title>

<link rel="stylesheet" href="../assets/admin.css">
<style>
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
    transition: all 0.2s ease;
}

.menu-item:hover {
    background: #333333;
    color: #ffffff;
    transform: translateX(2px);
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

/* =====================================================
   MAIN CONTENT
===================================================== */

.main-content {
    margin-left: 315px;
    min-height: 100vh;
    padding: 50px 40px;
}

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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
}

.stat-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
}

.stat-title {
    margin-top: 7px;
    color: #777777;
    font-size: 15px;
}

.stat-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f1f1;
    border-radius: 10px;
    font-size: 24px;
}

.stat-link {
    display: inline-block;
    margin-top: 18px;
    color: #ed1111;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
}

.stat-link:hover {
    text-decoration: underline;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.content-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
    overflow-x: auto;
}

.content-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.content-header h2 {
    margin: 0;
    font-size: 22px;
}

.view-all {
    color: #ed1111;
    font-size: 14px;
    font-weight: bold;
}

.view-all:hover {
    text-decoration: underline;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 12px 8px;
    background: #f5f5f5;
    text-align: left;
    font-size: 13px;
}

td {
    padding: 14px 8px;
    border-bottom: 1px solid #eeeeee;
    font-size: 13px;
    vertical-align: top;
}

.news-title {
    font-weight: bold;
    line-height: 1.4;
}

.views {
    color: #777777;
    white-space: nowrap;
}

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.status.published,
.status.approved {
    background: #d4edda;
    color: #155724;
}

.status.draft,
.status.pending {
    background: #fff3cd;
    color: #856404;
}

.status.rejected {
    background: #f8d7da;
    color: #721c24;
}

.comment-user {
    margin-bottom: 5px;
    font-weight: bold;
}

.comment-news {
    margin-bottom: 7px;
    color: #ed1111;
    font-size: 12px;
}

.comment-text {
    color: #555555;
    line-height: 1.5;
}

.quick-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 25px;
}

.quick-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 22px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
}

.quick-card h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.quick-number {
    font-size: 28px;
    font-weight: bold;
}

.empty {
    padding: 30px 10px;
    text-align: center;
    color: #777777;
}

/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 900px) {
    .admin-sidebar {
        width: 250px;
    }

    .main-content {
        margin-left: 250px;
        padding: 35px 25px;
    }

    .content-grid {
        grid-template-columns: 1fr;
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

    .stats-grid,
    .quick-grid {
        grid-template-columns: 1fr;
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
            href="dashboard.php"
            class="menu-item active"
        >
            📊 <span>Dashboard</span>
        </a>


        <a
            href="categories/manage.php"
            class="menu-item"
        >
            📂 <span>Categories</span>
        </a>


        <a
            href="subcategories/manage.php"
            class="menu-item"
        >
            📁 <span>Subcategories</span>
        </a>


        <a
            href="news/manage.php"
            class="menu-item"
        >
            📰 <span>News</span>
        </a>


        <a
            href="videos/manage.php"
            class="menu-item "
        >
            🎥 <span>Videos</span>
        </a>


        <a
            href="users/manage.php"
            class="menu-item"
        >
            👥 <span>Users</span>
        </a>


        <a
            href="comments/manage.php"
            class="menu-item"
        >
            💬 <span>Comments</span>
        </a>


        <a
            href="breaking-news/manage.php"
            class="menu-item"
        >
            🔴 <span>Breaking News</span>
        </a>


    </nav>


    <a
        href="logout.php"
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
        Dashboard
    </h1>


    <p class="page-description">
        Welcome to your Daily News admin dashboard
    </p>


    <!-- =================================================
         STATISTICS
    ================================================= -->

    <section class="stats-grid">


        <!-- TOTAL NEWS -->

        <div class="stat-card">

            <div class="stat-top">

                <div>

                    <div class="stat-number">

                        <?php
                        echo $total_news;
                        ?>

                    </div>


                    <div class="stat-title">
                        Total News
                    </div>

                </div>


                <div class="stat-icon">
                    📰
                </div>

            </div>


            <a
                href="news/manage.php"
                class="stat-link"
            >
                Manage News →
            </a>

        </div>


        <!-- PUBLISHED NEWS -->

        <div class="stat-card">

            <div class="stat-top">

                <div>

                    <div class="stat-number">

                        <?php
                        echo $published_news;
                        ?>

                    </div>


                    <div class="stat-title">
                        Published News
                    </div>

                </div>


                <div class="stat-icon">
                    ✅
                </div>

            </div>


            <a
                href="news/manage.php"
                class="stat-link"
            >
                View News →
            </a>

        </div>


        <!-- TOTAL USERS -->

        <div class="stat-card">

            <div class="stat-top">

                <div>

                    <div class="stat-number">

                        <?php
                        echo $total_users;
                        ?>

                    </div>


                    <div class="stat-title">
                        Total Users
                    </div>

                </div>


                <div class="stat-icon">
                    👥
                </div>

            </div>


            <a
                href="users/manage.php"
                class="stat-link"
            >
                Manage Users →
            </a>

        </div>


        <!-- PENDING COMMENTS -->

        <div class="stat-card">

            <div class="stat-top">

                <div>

                    <div class="stat-number">

                        <?php
                        echo $pending_comments;
                        ?>

                    </div>


                    <div class="stat-title">
                        Pending Comments
                    </div>

                </div>


                <div class="stat-icon">
                    💬
                </div>

            </div>


            <a
                href="comments/manage.php"
                class="stat-link"
            >
                Review Comments →
            </a>

        </div>


    </section>


    <!-- =================================================
         RECENT NEWS + COMMENTS
    ================================================= -->

    <section class="content-grid">


        <!-- RECENT NEWS -->

        <div class="content-card">


            <div class="content-header">

                <h2>
                    Recent News
                </h2>


                <a
                    href="news/manage.php"
                    class="view-all"
                >
                    View All
                </a>

            </div>


            <?php

            if (
                $recent_news_result &&
                mysqli_num_rows(
                    $recent_news_result
                ) > 0
            ):

            ?>


                <table>

                    <thead>

                        <tr>

                            <th>
                                News
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Views
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    while (
                        $news =
                        mysqli_fetch_assoc(
                            $recent_news_result
                        )
                    ):

                        $news_status =
                            strtolower(
                                $news["status"] ?? ""
                            );

                    ?>


                        <tr>


                            <td>

                                <div class="news-title">

                                    <?php

                                    echo htmlspecialchars(
                                        $news["title"] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>

                            </td>


                            <td>

                                <span
                                    class="status
                                    <?php
                                    echo htmlspecialchars(
                                        $news_status,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>"
                                >

                                    <?php

                                    echo ucfirst(
                                        htmlspecialchars(
                                            $news_status,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        )
                                    );

                                    ?>

                                </span>

                            </td>


                            <td>

                                <span class="views">

                                    👁️

                                    <?php

                                    echo (int)(
                                        $news["views"] ?? 0
                                    );

                                    ?>

                                </span>

                            </td>


                        </tr>


                    <?php

                    endwhile;

                    ?>


                    </tbody>

                </table>


            <?php

            else:

            ?>


                <div class="empty">
                    No news found.
                </div>


            <?php

            endif;

            ?>


        </div>


        <!-- RECENT COMMENTS -->

        <div class="content-card">


            <div class="content-header">

                <h2>
                    Recent Comments
                </h2>


                <a
                    href="comments/manage.php"
                    class="view-all"
                >
                    View All
                </a>

            </div>


            <?php

            if (
                $recent_comments_result &&
                mysqli_num_rows(
                    $recent_comments_result
                ) > 0
            ):

            ?>


                <table>

                    <thead>

                        <tr>

                            <th>
                                User / Comment
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    while (
                        $comment =
                        mysqli_fetch_assoc(
                            $recent_comments_result
                        )
                    ):

                        $comment_status =
                            strtolower(
                                $comment["status"] ?? ""
                            );

                    ?>


                        <tr>


                            <td>


                                <div class="comment-user">

                                    👤

                                    <?php

                                    echo htmlspecialchars(
                                        $comment["user_name"]
                                        ?? "Unknown User",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>


                                <div class="comment-news">

                                    <?php

                                    echo htmlspecialchars(
                                        $comment["news_title"]
                                        ?? "News Deleted",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>


                                <div class="comment-text">

                                    <?php

                                    echo htmlspecialchars(
                                        $comment["comment"]
                                        ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>


                            </td>


                            <td>

                                <span
                                    class="status
                                    <?php
                                    echo htmlspecialchars(
                                        $comment_status,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>"
                                >

                                    <?php

                                    echo ucfirst(
                                        htmlspecialchars(
                                            $comment_status,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        )
                                    );

                                    ?>

                                </span>

                            </td>


                        </tr>


                    <?php

                    endwhile;

                    ?>


                    </tbody>

                </table>


            <?php

            else:

            ?>


                <div class="empty">
                    No comments found.
                </div>


            <?php

            endif;

            ?>


        </div>


    </section>


    <!-- =================================================
         QUICK STATISTICS
    ================================================= -->

    <section class="quick-grid">


        <!-- DRAFT NEWS -->

        <div class="quick-card">

            <h3>
                📝 Draft News
            </h3>


            <div class="quick-number">

                <?php
                echo $draft_news;
                ?>

            </div>

        </div>


        <!-- TOTAL COMMENTS -->

        <div class="quick-card">

            <h3>
                💬 Total Comments
            </h3>


            <div class="quick-number">

                <?php
                echo $total_comments;
                ?>

            </div>

        </div>


    </section>


</main>


</body>

</html>