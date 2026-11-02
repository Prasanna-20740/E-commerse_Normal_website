<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config/database.php";


/* =====================================
   GET NEWS ID
===================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit;

}

$news_id = (int) $_GET["id"];


/* =====================================
   SUBMIT COMMENT
===================================== */

$comment_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_SESSION["user_id"])) {
        header("Location: user/login.php");
        exit;
    }

    $user_id = (int) $_SESSION["user_id"];

    $comment_text = trim(
        $_POST["comment"] ?? ""
    );

    if ($comment_text === "") {

        $comment_error = "Please enter a comment.";

    } elseif (strlen($comment_text) > 1000) {

        $comment_error =
            "Comment must be less than 1000 characters.";

    } else {

        $comment_query = " INSERT INTO comments
            (
                news_id,
                user_id,
                comment,
                status
            )
            VALUES
            (?, ?, ?, 'pending')
        ";

        $comment_stmt = mysqli_prepare(
            $conn,
            $comment_query
        );

        if ($comment_stmt) {

            mysqli_stmt_bind_param(
                $comment_stmt,
                "iis",
                $news_id,
                $user_id,
                $comment_text
            );

            if (mysqli_stmt_execute($comment_stmt)) {

                mysqli_stmt_close($comment_stmt);

                header(
                    "Location: news-details.php?id=" .
                    $news_id .
                    "&comment=success"
                );

                exit;
            }

            $comment_error =
                "Unable to post comment. Please try again.";

            mysqli_stmt_close($comment_stmt);

        } else {

            $comment_error =
                "Database error while posting comment.";
        }
    }
}


/* =====================================
   GET APPROVED COMMENTS
===================================== */

$comments = [];

$comment_query = " SELECT
        c.id,
        c.comment,
        c.created_at,
        u.name AS user_name
    FROM comments c
    LEFT JOIN users u
        ON c.user_id = u.id
    WHERE c.news_id = ?
    AND c.status = 'approved'
    ORDER BY c.created_at DESC
";

$comment_stmt = mysqli_prepare(
    $conn,
    $comment_query
);

if ($comment_stmt) {

    mysqli_stmt_bind_param(
        $comment_stmt,
        "i",
        $news_id
    );

    mysqli_stmt_execute(
        $comment_stmt
    );

    $comment_result =
        mysqli_stmt_get_result(
            $comment_stmt
        );

    while (
        $comment_row =
        mysqli_fetch_assoc($comment_result)
    ) {
        $comments[] = $comment_row;
    }

    mysqli_stmt_close($comment_stmt);
}


/* =====================================
   INCREASE VIEW COUNT
===================================== */

$view_query = " UPDATE news
    SET views = COALESCE(views, 0) + 1
    WHERE id = ?
    AND status = 'published'
";

$view_stmt = mysqli_prepare(
    $conn,
    $view_query
);

if ($view_stmt) {

    mysqli_stmt_bind_param(
        $view_stmt,
        "i",
        $news_id
    );

    mysqli_stmt_execute(
        $view_stmt
    );

    mysqli_stmt_close(
        $view_stmt
    );
}


/* =====================================
   GET NEWS DETAILS
===================================== */
$query = " SELECT
    n.id,
    n.title,
    n.slug,
    n.description,
    n.content,
    n.image,
    n.category_id,
    n.subcategory_id,
    n.author_id,
    n.author,
    n.views,
    n.status,
    n.published_at,
    n.created_at,
    n.updated_at,

    c.name AS category_name,

    s.name AS subcategory_name

    FROM news n

    LEFT JOIN categories c
        ON n.category_id = c.id

    LEFT JOIN subcategories s
        ON n.subcategory_id = s.id

    WHERE n.id = ?
    AND n.status = 'published'

    LIMIT 1
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


if (!$stmt) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $news_id
);


mysqli_stmt_execute(
    $stmt
);


$result = mysqli_stmt_get_result(
    $stmt
);


$news = mysqli_fetch_assoc(
    $result
);


mysqli_stmt_close(
    $stmt
);


/* =====================================
   NEWS NOT FOUND
===================================== */

if (!$news) {

    header(
        "Location: index.php"
    );

    exit;

}


/* =====================================
   AUTHOR
===================================== */

/*
 * Priority:
 * 1) The "author" text typed by admin in the news form (news.author)
 * 2) If a linked user account exists (author_id), use that user's name instead
 * 3) Fallback to "Admin" if neither is available
 */

$author_name =
    !empty($news["author"])
        ? $news["author"]
        : "Admin";


if (!empty($news["author_id"])) {

    $author_id = (int) $news["author_id"];


    $author_query = "
        SELECT name
        FROM users
        WHERE id = ?
        LIMIT 1
    ";


    $author_stmt = mysqli_prepare(
        $conn,
        $author_query
    );


    if ($author_stmt) {

        mysqli_stmt_bind_param(
            $author_stmt,
            "i",
            $author_id
        );

        mysqli_stmt_execute(
            $author_stmt
        );


        $author_result =
            mysqli_stmt_get_result(
                $author_stmt
            );


        $author_data =
            mysqli_fetch_assoc(
                $author_result
            );


        if ($author_data) {

            $author_name =
                $author_data["name"];

        }


        mysqli_stmt_close(
            $author_stmt
        );

    }

}


/* =====================================
   FORMAT DATE
===================================== */

$news_date = date(
    "d M Y, h:i A",
    strtotime(
        $news["created_at"]
    )
);


/* =====================================
   IMAGE
===================================== */

if (!empty($news["image"])) {

    $news_image =
        "uploads/news/" .
        $news["image"];

} else {

    $news_image =
        "assets/images/default-news.jpg";

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

        <?php
        echo htmlspecialchars(
            $news["title"]
        );
        ?>

        | Daily News

    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/header.css"
    >


    <style>

        /* =================================
           NEWS DETAILS PAGE
        ================================= */

        .details-container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

        }


        .breadcrumb {

            margin-bottom: 25px;

            color: #aaa;

            font-size: 14px;

        }


        .breadcrumb a {

            color: #ff3333;

            text-decoration: none;

        }


        .news-details {

            background: #222;

            border-radius: 12px;

            overflow: hidden;

            border: 1px solid #333;

        }


        .details-image {

            width: 100%;

            height: 500px;

            overflow: hidden;

        }


        .details-image img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

        }


        .details-content {

            padding: 35px;

        }


        .details-category {

            display: inline-block;

            background: #ed1111;

            color: white;

            padding: 7px 15px;

            border-radius: 5px;

            font-size: 14px;

            font-weight: bold;

            text-transform: uppercase;

            margin-bottom: 18px;

        }


        .details-content h1 {

            font-size: 42px;

            line-height: 1.2;

            margin: 0 0 20px;

            color: white;

        }


        .details-meta {

            display: flex;

            gap: 25px;

            flex-wrap: wrap;

            color: #aaa;

            border-bottom: 1px solid #444;

            padding-bottom: 20px;

            margin-bottom: 25px;

        }

.details-description {
    color: #f1f1f1;
    font-size: 20px;
    line-height: 1.9;
    margin-top: 35px;
    white-space: pre-line;
    word-wrap: break-word;
}

.details-description p {
    margin-bottom: 25px;
}

.details-description strong {
    color: #ffffff;
    font-weight: 700;
}

.details-description h2 {
    color: #ffffff;
    font-size: 28px;
    margin: 35px 0 20px;
}

.details-description h3 {
    color: #ffffff;
    font-size: 23px;
    margin: 30px 0 15px;
}


        .back-button {

            display: inline-block;

            margin-top: 30px;

            padding: 12px 22px;

            background: #ed1111;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            font-weight: bold;

        }


        .back-button:hover {

            background: #c90000;

        }


        @media (max-width: 768px) {

            .details-container {

                width: 94%;

            }


            .details-image {

                height: 300px;

            }


            .details-content {

                padding: 22px;

            }


            .details-content h1 {

                font-size: 30px;

            }


            .details-description {

                font-size: 16px;

            }

        }

        .comments-section {
    margin-top: 50px;
    padding-top: 35px;
    border-top: 1px solid #333;
}

.comments-section h2 {
    font-size: 32px;
    margin-bottom: 25px;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.comment-card {
    background: #292929;
    border: 1px solid #444;
    border-radius: 10px;
    padding: 20px;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 15px;
}

.comment-header strong {
    color: #ffffff;
    font-size: 17px;
}

.comment-header span {
    color: #aaa;
    font-size: 14px;
}

.comment-body {
    color: #ddd;
    font-size: 16px;
    line-height: 1.7;
}

.no-comments {
    background: #292929;
    border: 1px solid #444;
    border-radius: 10px;
    padding: 25px;
    color: #aaa;
}


/* =====================================
   COMMENT FORM
===================================== */

.comment-form-box {
    margin-top: 20px;
    margin-bottom: 35px;
    padding: 25px;
    background: #242424;
    border: 1px solid #3d3d3d;
    border-radius: 10px;
}

.comment-form-box h3 {
    margin: 0 0 18px;
    color: #fff;
}

.comment-form-box textarea {
    width: 100%;
    min-height: 130px;
    padding: 15px;
    background: #1b1b1b;
    color: #fff;
    border: 1px solid #444;
    border-radius: 7px;
    font-size: 16px;
    line-height: 1.6;
    resize: vertical;
    outline: none;
    font-family: inherit;
}

.comment-form-box textarea:focus {
    border-color: #ed1111;
}

.comment-form-box button {
    margin-top: 15px;
    padding: 12px 25px;
    background: #ed1111;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.comment-form-box button:hover {
    background: #c90000;
}

.comment-error {
    margin-bottom: 15px;
    padding: 12px;
    background: #3a1d1d;
    color: #ff7777;
    border: 1px solid #8b2929;
    border-radius: 6px;
}

.comment-success {
    margin-bottom: 15px;
    padding: 12px;
    background: #193a25;
    color: #72e89a;
    border: 1px solid #2f7547;
    border-radius: 6px;
}

.login-to-comment {
    margin-top: 20px;
    margin-bottom: 35px;
    padding: 25px;
    text-align: center;
    background: #242424;
    border: 1px solid #3d3d3d;
    border-radius: 10px;
}

.login-to-comment p {
    color: #aaa;
    margin-bottom: 15px;
}

.login-to-comment a {
    display: inline-block;
    padding: 10px 20px;
    background: #ed1111;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}

.login-to-comment a:hover {
    background: #c90000;
}

    </style>

</head>


<body>


<?php include "includes/header.php"; ?>


<!-- =====================================
     NEWS DETAILS
===================================== -->

<main class="details-container">


    <!-- BREADCRUMB -->

    <div class="breadcrumb">

        <a href="index.php">
            Home
        </a>

        &nbsp; / &nbsp;

        <?php
        echo htmlspecialchars(
            $news["category_name"] ??
            "News"
        );
        ?>

        &nbsp; / &nbsp;

        News

    </div>


    <!-- NEWS ARTICLE -->

    <article class="news-details">


        <!-- IMAGE -->

        <div class="details-image">

            <img
                src="<?php
                echo htmlspecialchars(
                    $news_image
                );
                ?>"
                alt="<?php
                echo htmlspecialchars(
                    $news["title"]
                );
                ?>"
            >

        </div>


        <!-- CONTENT -->

        <div class="details-content">


            <!-- CATEGORY -->

            <span class="details-category">

                <?php
                echo htmlspecialchars(
                    $news["category_name"] ??
                    "NEWS"
                );
                ?>

            </span>


            <!-- TITLE -->

            <h1>

                <?php
                echo htmlspecialchars(
                    $news["title"]
                );
                ?>

            </h1>


            <!-- META -->

            <div class="details-meta">


                <span>

                    👤 By

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $author_name
                        );
                        ?>

                    </strong>

                </span>


                <span>

                    🕒

                    <?php
                    echo $news_date;
                    ?>

                </span>


                <span>

                    👁️

                    <?php
                    echo (int)
                        $news["views"];
                    ?>

                    Views

                </span>


                <?php if (
                    !empty(
                        $news[
                            "subcategory_name"
                        ]
                    )
                ): ?>

                    <span>

                        📂

                        <?php
                        echo htmlspecialchars(
                            $news[
                                "subcategory_name"
                            ]
                        );
                        ?>

                    </span>

                <?php endif; ?>


            </div>


            <!-- FULL NEWS -->

            <div class="details-description">

    <?php

    if (!empty($news["content"])) {

        echo nl2br(
            htmlspecialchars(
                $news["content"],
                ENT_QUOTES,
                "UTF-8"
            )
        );

    } else {

        echo "No full news content available.";

    }

    ?>

</div>
            <!-- =====================================
     COMMENTS
===================================== -->

<section class="comments-section">

    <h2>
        Comments
    </h2>


    <?php if (isset($_SESSION["user_id"])): ?>

        <div class="comment-form-box">

            <h3>
                Write a Comment
            </h3>


            <?php if ($comment_error !== ""): ?>

                <div class="comment-error">

                    <?php
                    echo htmlspecialchars(
                        $comment_error,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>

                </div>

            <?php endif; ?>


            <?php if (
                isset($_GET["comment"]) &&
                $_GET["comment"] === "success"
            ): ?>

                <div class="comment-success">

                    Comment submitted successfully.
                    It will appear after admin approval.

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action="news-details.php?id=<?php echo $news_id; ?>"
            >

                <textarea
                    name="comment"
                    rows="5"
                    maxlength="1000"
                    placeholder="Write your comment..."
                    required
                ></textarea>


                <button type="submit">
                    Post Comment
                </button>

            </form>

        </div>


    <?php else: ?>

        <div class="login-to-comment">

            <p>
                Please login to comment on this news.
            </p>

            <a href="user/login.php">
                🔐 Login to Comment
            </a>

        </div>

    <?php endif; ?>


    <?php if (count($comments) > 0): ?>

        <div class="comments-list">

            <?php foreach ($comments as $comment): ?>

                <div class="comment-card">

                    <div class="comment-header">

                        <strong>
                            👤

                            <?php
                            echo htmlspecialchars(
                                $comment["user_name"] ?? "User",
                                ENT_QUOTES,
                                "UTF-8"
                            );
                            ?>
                        </strong>


                        <span>
                            🕒

                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $comment["created_at"]
                                )
                            );
                            ?>
                        </span>

                    </div>


                    <div class="comment-body">

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $comment["comment"],
                                ENT_QUOTES,
                                "UTF-8"
                            )
                        );
                        ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>

        <div class="no-comments">

            No approved comments yet.
            Be the first to comment.

        </div>

    <?php endif; ?>


</section>


<!-- BACK -->

            <a
                href="index.php"
                class="back-button"
            >

                ← Back to Home

            </a>


        </div>


    </article>


</main>


</body>

</html>