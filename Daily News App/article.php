<?php

/* =====================================================
   SESSION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =====================================================
   DATABASE
===================================================== */

require_once "config/database.php";


/* =====================================================
   GET NEWS ID
===================================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$news_id = (int) $_GET["id"];


/* =====================================================
   INCREASE VIEW COUNT
===================================================== */

$view_query = " UPDATE news
    SET views = COALESCE(views, 0) + 1
    WHERE id = ?
    AND status = 'published'
";

$view_stmt = mysqli_prepare($conn, $view_query);

if ($view_stmt) {

    mysqli_stmt_bind_param(
        $view_stmt,
        "i",
        $news_id
    );

    mysqli_stmt_execute($view_stmt);

    mysqli_stmt_close($view_stmt);
}


/* =====================================================
   GET NEWS DETAILS
===================================================== */

$query = " SELECT  n.id,
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


$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {

    die(
        "Database Error: " .
        htmlspecialchars(
            mysqli_error($conn),
            ENT_QUOTES,
            "UTF-8"
        )
    );
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $news_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$news = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =====================================================
   NEWS NOT FOUND
===================================================== */

if (!$news) {

    header("Location: index.php");

    exit;
}


/* =====================================================
   AUTHOR
===================================================== */

$author_name = !empty($news["author"])
    ? $news["author"]
    : "Admin";


if (!empty($news["author_id"])) {

    $author_id = (int) $news["author_id"];


    $author_query = " SELECT name
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


        if ($author_data && !empty($author_data["name"])) {

            $author_name =
                $author_data["name"];
        }


        mysqli_stmt_close(
            $author_stmt
        );
    }
}


/* =====================================================
   DATE
===================================================== */

if (!empty($news["created_at"])) {

    $news_date = date(
        "d M Y, h:i A",
        strtotime($news["created_at"])
    );

} else {

    $news_date = "";
}


/* =====================================================
   IMAGE
===================================================== */

if (!empty($news["image"])) {

    $news_image =
        "uploads/news/" .
        $news["image"];

} else {

    $news_image =
        "assets/images/default-news.jpg";
}


/* =====================================================
   SAFE VALUES
===================================================== */

$news_title =
    htmlspecialchars(
        $news["title"] ?? "News",
        ENT_QUOTES,
        "UTF-8"
    );


$category_name =
    htmlspecialchars(
        $news["category_name"] ?? "News",
        ENT_QUOTES,
        "UTF-8"
    );


$subcategory_name =
    htmlspecialchars(
        $news["subcategory_name"] ?? "",
        ENT_QUOTES,
        "UTF-8"
    );


$author_display =
    htmlspecialchars(
        $author_name,
        ENT_QUOTES,
        "UTF-8"
    );


$news_image_display =
    htmlspecialchars(
        $news_image,
        ENT_QUOTES,
        "UTF-8"
    );


$news_content =
    $news["content"] ?? "";


$news_description =
    $news["description"] ?? "";

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
        <?php echo $news_title; ?> | Daily News
    </title>


    <!-- MAIN CSS -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- HEADER CSS -->

    <link
        rel="stylesheet"
        href="assets/css/header.css"
    >


    <style>
    /* Premium redesign using design tokens defined in header */
    body {font-family: var(--font-family); background: var(--dark); color: var(--light);}
    .details-container {width: 90%; max-width: 1100px; margin: 4rem auto; padding: 0 1rem;}
    .breadcrumb {font-size: 0.875rem; color: var(--muted); margin-bottom: 1rem;}
    .breadcrumb a {color: var(--primary); text-decoration: none;}
    .breadcrumb a:hover {text-decoration: underline;}
    .news-details {background: var(--dark-card); border-radius: var(--radius); overflow: hidden; border: 1px solid var(--border);}
    .details-image {height: 400px; overflow: hidden;}
    .details-image img {width: 100%; height: 100%; object-fit: cover; display: block;}
    .details-content {padding: 2rem; color: var(--text);}
    .details-category {background: var(--primary); color: var(--light); padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.825rem; font-weight: 600; text-transform: uppercase; margin-bottom: 1rem;}
    .details-content h1 {font-size: 2rem; margin: 0 0 1rem; color: var(--light);}
    .details-meta {display: flex; gap: 1rem; flex-wrap: wrap; color: var(--muted); font-size: 0.85rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;}
    .details-meta strong {color: var(--light);}
    .details-description {font-size: 1rem; line-height: 1.6; color: var(--muted);}
    .details-description p {margin-bottom: 1rem;}
    .details-description h2, .details-description h3 {color: var(--light); margin: 1.5rem 0 1rem;}
    .back-button {display: inline-block; margin-top: 2rem; padding: 0.6rem 1.2rem; background: var(--primary); color: var(--light); text-decoration: none; border-radius: var(--radius); font-weight: 500; transition: var(--transition);}
    .back-button:hover {opacity: 0.9;}
    @media (max-width: 768px) {
        .details-container {width: 94%;}
        .details-image {height: 300px;}
        .details-content {padding: 1.5rem;}
        .details-content h1 {font-size: 1.75rem;}
        .details-description {font-size: 0.95rem;}
    }
    @media (max-width: 480px) {
        .details-image {height: 230px;}
        .details-content h1 {font-size: 1.5rem;}
        .details-content {padding: 1rem;}
    }
</style>

</head>


<body>


<?php

/* =====================================================
   HEADER
===================================================== */

include "includes/header.php";

?>


<!-- =====================================================
     NEWS DETAILS
===================================================== -->

<main class="details-container">


    <!-- =================================================
         BREADCRUMB
    ================================================== -->

    <div class="breadcrumb">

        <a href="index.php">
            Home
        </a>

        &nbsp; / &nbsp;

        <?php
        echo $category_name;
        ?>

        &nbsp; / &nbsp;

        News

    </div>


    <!-- =================================================
         NEWS ARTICLE
    ================================================== -->

    <article class="news-details">


        <!-- =================================================
             NEWS IMAGE
        ================================================== -->

        <div class="details-image">

            <img
                src="<?php echo $news_image_display; ?>"
                alt="<?php echo $news_title; ?>"
            >

        </div>


        <!-- =================================================
             NEWS CONTENT
        ================================================== -->

        <div class="details-content">


            <!-- CATEGORY -->

            <span class="details-category">

                <?php
                echo $category_name;
                ?>

            </span>


            <!-- TITLE -->

            <h1>

                <?php
                echo $news_title;
                ?>

            </h1>


            <!-- =================================================
                 META INFORMATION
            ================================================== -->

            <div class="details-meta">


                <!-- AUTHOR -->

                <span>

                    👤 By

                    <strong>
                        <?php
                        echo $author_display;
                        ?>
                    </strong>

                </span>


                <!-- DATE -->

                <span>

                    🕒

                    <?php
                    echo htmlspecialchars(
                        $news_date,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>

                </span>


                <!-- VIEWS -->

                <span>

                    👁️

                    <?php
                    echo (int) (
                        $news["views"] ?? 0
                    );
                    ?>

                    Views

                </span>


                <!-- SUBCATEGORY -->

                <?php if (!empty($subcategory_name)): ?>

                    <span>

                        📂

                        <?php
                        echo $subcategory_name;
                        ?>

                    </span>

                <?php endif; ?>


            </div>


            <!-- =================================================
                 DESCRIPTION
            ================================================== -->

            <?php if (!empty($news_description)): ?>

                <div class="details-description">

                    <?php

                    echo nl2br(
                        htmlspecialchars(
                            $news_description,
                            ENT_QUOTES,
                            "UTF-8"
                        )
                    );

                    ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FULL NEWS CONTENT
            ================================================== -->

            <div class="details-description">

                <?php

                if (!empty($news_content)) {

                    echo nl2br(
                        htmlspecialchars(
                            $news_content,
                            ENT_QUOTES,
                            "UTF-8"
                        )
                    );

                } else {

                    echo "No full news content available.";

                }

                ?>

            </div>


            <!-- =================================================
                 BACK BUTTON
            ================================================== -->

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