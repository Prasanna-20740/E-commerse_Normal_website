<?php

/* =====================================================
   AUTH
===================================================== */

require_once "../includes/auth.php";


/* =====================================================
   DATABASE
===================================================== */

require_once "../../config/database.php";


/* =====================================================
   GET COMMENTS
===================================================== */

$query = " SELECT
        comments.id,
        comments.news_id,
        comments.user_id,
        comments.comment,
        comments.status,
        comments.created_at,

        users.name AS user_name,
        users.email AS user_email,

        news.title AS news_title

    FROM comments

    LEFT JOIN users
        ON comments.user_id = users.id

    LEFT JOIN news
        ON comments.news_id = news.id

    ORDER BY comments.id DESC
";


$result = mysqli_query($conn, $query);


/* =====================================================
   MESSAGE
===================================================== */

$message = "";
$message_type = "";


if (isset($_GET["approved"])) {

    $message = "Comment approved successfully.";

    $message_type = "success";
}


if (isset($_GET["rejected"])) {

    $message = "Comment rejected successfully.";

    $message_type = "warning";
}


if (isset($_GET["deleted"])) {

    $message = "Comment deleted successfully.";

    $message_type = "success";
}


if (isset($_GET["error"])) {

    $message = "Unable to process comment.";

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
        Comments Management - Daily News
    </title>
    <link rel="stylesheet" href="../assets/admin.css">


<style>

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

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #f4f5f7;

    color: #111111;
}



/* =====================================================
   MENU LINK
===================================================== */

.sidebar-menu a {

    display: flex;

    align-items: center;

    width: 100%;

    padding:
        16px 20px;

    color: #dddddd;

    text-decoration: none;

    border-radius: 7px;

    font-size: 17px;

    font-weight: 500;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}


/* =====================================================
   MENU HOVER
===================================================== */

.sidebar-menu a:hover {

    background: #333333;

    color: #ffffff;

    transform:
        translateX(2px);
}


/* =====================================================
   ACTIVE MENU
===================================================== */

.sidebar-menu a.active {

    background: #ed1111;

    color: #ffffff;

    font-weight: 600;
}


.sidebar-menu a.active:hover {

    background: #ed1111;

    color: #ffffff;

    transform: none;
}


/* =====================================================
   LOGOUT
===================================================== */

.logout {

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

    text-decoration: none;

    border-radius: 7px;

    font-size: 17px;

    font-weight: 500;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}


.logout:hover {

    background: #ed1111;

    color: #ffffff;

    transform:
        translateY(-1px);
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
   PAGE TITLE
===================================================== */

.page-title {

    margin: 0 0 12px;

    font-size: 38px;

    line-height: 1.2;

    font-weight: 700;

    color: #111111;
}


/* =====================================================
   PAGE DESCRIPTION
===================================================== */

.page-description {

    margin:
        0 0 50px;

    font-size: 17px;

    color: #687384;
}


/* =====================================================
   SECTION HEADER
===================================================== */

.section-header {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    margin-bottom: 30px;
}


.section-header h2 {

    margin: 0;

    font-size: 28px;

    color: #111111;
}


/* =====================================================
   COMMENTS CARD
===================================================== */

.comments-card {

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

.comments-card table {

    width: 100%;

    border-collapse:
        collapse;

    min-width: 1050px;
}


/* =====================================================
   TABLE HEADER
===================================================== */

.comments-card th {

    padding:
        16px 14px;

    background: #f3f3f3;

    color: #111111;

    text-align: left;

    font-size: 14px;

    font-weight: 700;

    white-space: nowrap;
}


/* =====================================================
   TABLE DATA
===================================================== */

.comments-card td {

    padding:
        17px 14px;

    border-bottom:
        1px solid #eeeeee;

    text-align: left;

    font-size: 14px;

    color: #333333;

    vertical-align: middle;
}


/* =====================================================
   ROW HOVER
===================================================== */

.comments-card tbody tr:hover {

    background: #fafafa;
}


/* =====================================================
   USER NAME
===================================================== */

.user-name {

    font-weight: 700;

    color: #111111;

    margin-bottom: 5px;
}


/* =====================================================
   USER EMAIL
===================================================== */

.user-email {

    font-size: 12px;

    color: #888888;
}


/* =====================================================
   NEWS TITLE
===================================================== */

.news-title {

    max-width: 260px;

    font-weight: 700;

    color: #111111;

    line-height: 1.4;
}


/* =====================================================
   NEWS ID
===================================================== */

.news-id {

    margin-top: 5px;

    font-size: 12px;

    color: #999999;
}


/* =====================================================
   COMMENT TEXT
===================================================== */

.comment-text {

    max-width: 300px;

    line-height: 1.5;

    color: #333333;

    word-break: break-word;
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

    text-transform:
        capitalize;
}


/* =====================================================
   PENDING
===================================================== */

.status.pending {

    background: #fff3cd;

    color: #856404;
}


/* =====================================================
   APPROVED
===================================================== */

.status.approved {

    background: #d9f3df;

    color: #16803c;
}


/* =====================================================
   REJECTED
===================================================== */

.status.rejected {

    background: #ffe0e0;

    color: #c40000;
}


/* =====================================================
   ACTIONS
===================================================== */

.actions {

    display: flex;

    gap: 7px;

    flex-wrap: wrap;
}


/* =====================================================
   ACTION BUTTON
===================================================== */

.action-btn {

    display: inline-block;

    padding:
        8px 13px;

    color: #ffffff;

    text-decoration: none;

    border-radius: 5px;

    font-size: 12px;

    font-weight: 600;

    border: none;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}


.action-btn:hover {

    color: #ffffff;

    transform:
        translateY(-1px);
}


/* =====================================================
   APPROVE
===================================================== */

.approve-btn {

    background: #198754;
}


.approve-btn:hover {

    background: #146c43;
}


/* =====================================================
   REJECT
===================================================== */

.reject-btn {

    background: #f59e0b;
}


.reject-btn:hover {

    background: #d97706;
}


/* =====================================================
   DELETE
===================================================== */

.delete-btn {

    background: #ed1111;
}


.delete-btn:hover {

    background: #c90000;
}


/* =====================================================
   SUCCESS MESSAGE
===================================================== */

.success-message {

    background: #e5f7e8;

    color: #16803c;

    padding:
        12px 15px;

    margin-bottom: 20px;

    border-radius: 6px;
}


/* =====================================================
   WARNING MESSAGE
===================================================== */

.warning-message {

    background: #fff3cd;

    color: #856404;

    padding:
        12px 15px;

    margin-bottom: 20px;

    border-radius: 6px;
}


/* =====================================================
   ERROR MESSAGE
===================================================== */

.error-message {

    background: #ffe5e5;

    color: #c40000;

    padding:
        12px 15px;

    margin-bottom: 20px;

    border-radius: 6px;
}


/* =====================================================
   NO COMMENTS
===================================================== */

.no-comments {

    text-align: center;

    padding:
        50px 20px;

    color: #777777;

    font-size: 16px;
}


/* =====================================================
   SCROLLBAR
===================================================== */

.sidebar::-webkit-scrollbar {

    width: 5px;
}


.sidebar::-webkit-scrollbar-track {

    background: #1f1f1f;
}


.sidebar::-webkit-scrollbar-thumb {

    background: #444444;

    border-radius: 10px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1000px) {

    .sidebar {

        width: 260px;
    }


    .main-content {

        margin-left: 260px;

        padding:
            40px 25px;
    }

}


@media (max-width: 700px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

        padding:
            20px 10px;
    }


    .sidebar-logo {

        padding-bottom: 20px;
    }


    .sidebar-menu {

        margin-top: 20px;
    }


    .logout {

        margin-top: 25px;
    }


    .main-content {

        margin-left: 0;

        padding:
            30px 20px;
    }


    .section-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;
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
            class="menu-item active"
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
        Comments Management
    </h1>


    <p class="page-description">
        Manage user comments on your news articles
    </p>


    <!-- MESSAGE -->

    <?php if ($message !== ""): ?>


        <div
            class="<?php

                if ($message_type === "success") {

                    echo "success-message";

                } elseif ($message_type === "warning") {

                    echo "warning-message";

                } else {

                    echo "error-message";

                }

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


    <!-- SECTION HEADER -->

    <div class="section-header">

        <h2>
            All Comments
        </h2>

    </div>


    <!-- COMMENTS CARD -->

    <div class="comments-card">


        <?php if (
            $result &&
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
                            News
                        </th>

                        <th>
                            Comment
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
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

                                echo (int)
                                    $row["id"];

                                ?>

                            </td>


                            <!-- USER -->

                            <td>

                                <div class="user-name">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["user_name"]
                                        ?? "Unknown User",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>


                                <div class="user-email">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["user_email"]
                                        ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>

                            </td>


                            <!-- NEWS -->

                            <td>

                                <div class="news-title">

                                    <?php

                                    echo htmlspecialchars(
                                        $row["news_title"]
                                        ?? "News not found",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </div>


                                <div class="news-id">

                                    News ID:
                                    <?php

                                    echo (int)
                                        $row["news_id"];

                                    ?>

                                </div>

                            </td>


                            <!-- COMMENT -->

                            <td>

                                <div class="comment-text">

                                    <?php

                                    echo nl2br(
                                        htmlspecialchars(
                                            $row["comment"]
                                            ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        )
                                    );

                                    ?>

                                </div>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php

                                $status =
                                    strtolower(
                                        $row["status"]
                                        ?? "pending"
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


                            <!-- DATE -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $row["created_at"]
                                    )
                                ) {

                                    echo date(
                                        "d M Y, h:i A",
                                        strtotime(
                                            $row["created_at"]
                                        )
                                    );

                                } else {

                                    echo "-";

                                }

                                ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="actions">


                                    <?php

                                    if (
                                        $status !== "approved"
                                    ):

                                    ?>

                                        <a
                                            href="approve.php?id=<?php
                                                echo (int)
                                                    $row["id"];
                                            ?>"
                                            class="action-btn approve-btn"
                                        >
                                            Approve
                                        </a>

                                    <?php endif; ?>


                                    <?php

                                    if (
                                        $status !== "rejected"
                                    ):

                                    ?>

                                        <a
                                            href="reject.php?id=<?php
                                                echo (int)
                                                    $row["id"];
                                            ?>"
                                            class="action-btn reject-btn"
                                        >
                                            Reject
                                        </a>

                                    <?php endif; ?>


                                    <a
                                        href="delete.php?id=<?php
                                            echo (int)
                                                $row["id"];
                                        ?>"
                                        class="action-btn delete-btn"
                                        onclick="
                                            return confirm(
                                                'Are you sure you want to delete this comment?'
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


        <?php else: ?>


            <div class="no-comments">

                No comments found.

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>