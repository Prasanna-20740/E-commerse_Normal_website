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
   GET VIDEO ID
===================================================== */

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: video.php");
    exit;
}

$video_id = (int) $_GET["id"];


/* =====================================================
   GET VIDEO
===================================================== */

$query = " SELECT
        v.id,
        v.title,
        v.description,
        v.thumbnail,
        v.video_url,
        v.views,
        v.created_at,
        c.name AS category_name
    FROM videos v
    LEFT JOIN categories c
        ON v.category_id = c.id
    WHERE v.id = ?
      AND v.status = 'published'
    LIMIT 1
";


$stmt = mysqli_prepare($conn, $query);


if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $video_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


if (
    !$result ||
    mysqli_num_rows($result) !== 1
) {

    mysqli_stmt_close($stmt);

    header("Location: video.php");

    exit;
}


$video = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =====================================================
   INCREASE VIEWS
===================================================== */

$viewQuery = " UPDATE videos
    SET views = views + 1
    WHERE id = ?
";


$viewStmt = mysqli_prepare(
    $conn,
    $viewQuery
);


if ($viewStmt) {

    mysqli_stmt_bind_param(
        $viewStmt,
        "i",
        $video_id
    );

    mysqli_stmt_execute($viewStmt);

    mysqli_stmt_close($viewStmt);
}


/* =====================================================
   VIDEO URL
===================================================== */

$video_url = trim($video["video_url"]);

$youtube_id = "";


/* =====================================================
   YOUTUBE URL DETECTION
===================================================== */


/*
   Format:

   https://www.youtube.com/watch?v=ABC123
*/

if (
    preg_match(
        '/(?:youtube\.com\/watch\?v=)([^&]+)/i',
        $video_url,
        $matches
    )
) {

    $youtube_id = $matches[1];

}


/*
   Format:

   https://youtu.be/ABC123
*/

elseif (
    preg_match(
        '/youtu\.be\/([^?]+)/i',
        $video_url,
        $matches
    )
) {

    $youtube_id = $matches[1];

}


/*
   Format:

   https://www.youtube.com/embed/ABC123
*/

elseif (
    preg_match(
        '/youtube\.com\/embed\/([^?]+)/i',
        $video_url,
        $matches
    )
) {

    $youtube_id = $matches[1];

}


/* =====================================================
   ESCAPE DATA
===================================================== */

$page_title = htmlspecialchars(
    $video["title"],
    ENT_QUOTES,
    "UTF-8"
);

$thumbnail = htmlspecialchars(
    $video["thumbnail"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

$description = htmlspecialchars(
    $video["description"] ?? "",
    ENT_QUOTES,
    "UTF-8"
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

    <title>
        <?php echo $page_title; ?> - Daily News
    </title>


    <!-- =================================================
         CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="/Daily%20News%20App/assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/Daily%20News%20App/assets/css/header.css"
    >

    <link
        rel="stylesheet"
        href="/Daily%20News%20App/assets/css/navbar.css"
    >

    <link
        rel="stylesheet"
        href="/Daily%20News%20App/assets/css/breaking-news.css"
    >

    <link
        rel="stylesheet"
        href="/Daily%20News%20App/assets/css/responsive.css"
    >


    <style>

        /* =================================================
           WATCH PAGE
        ================================================= */

        .watch-page {

            max-width: 1100px;

            margin: 0 auto;

            padding: 45px 20px;

        }


        /* =================================================
           VIDEO PLAYER
        ================================================= */

        .video-player {

            width: 100%;

            aspect-ratio: 16 / 9;

            background: #000000;

            border-radius: 12px;

            overflow: hidden;

            margin-bottom: 25px;

        }


        .video-player iframe {

            width: 100%;

            height: 100%;

            border: none;

            display: block;

        }


        .video-player video {

            width: 100%;

            height: 100%;

            object-fit: contain;

            background: #000000;

        }


        /* =================================================
           VIDEO INFO
        ================================================= */

        .video-info {

            background: #ffffff;

            padding: 28px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.06);

        }


        .video-category {

            display: inline-block;

            margin-bottom: 10px;

            color: #ed1111;

            font-size: 13px;

            font-weight: 700;

            text-transform: uppercase;

        }


        .video-info h1 {

            margin: 0 0 15px;

            font-size: 32px;

            line-height: 1.3;

            color: #111111;

        }


        .video-description {

            margin: 0 0 20px;

            color: #555555;

            font-size: 16px;

            line-height: 1.7;

        }


        /* =================================================
           META
        ================================================= */

        .video-meta {

            display: flex;

            align-items: center;

            gap: 25px;

            padding-top: 18px;

            border-top:
                1px solid #eeeeee;

            color: #777777;

            font-size: 14px;

        }


        /* =================================================
           BACK BUTTON
        ================================================= */

        .back-button {

            display: inline-block;

            margin-top: 25px;

            padding: 11px 18px;

            background: #ed1111;

            color: #ffffff;

            border-radius: 6px;

            font-size: 14px;

            font-weight: 700;

            text-decoration: none;

        }


        .back-button:hover {

            background: #c90000;

            color: #ffffff;

        }


        /* =================================================
           ERROR
        ================================================= */

        .video-error {

            padding: 60px 20px;

            text-align: center;

            background: #ffffff;

            border-radius: 12px;

        }


        .video-error h2 {

            margin-bottom: 10px;

            color: #333333;

        }


        .video-error p {

            color: #777777;

            line-height: 1.6;

        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 650px) {

            .watch-page {

                padding: 30px 15px;

            }


            .video-info {

                padding: 20px;

            }


            .video-info h1 {

                font-size: 25px;

            }


            .video-meta {

                flex-direction: column;

                align-items: flex-start;

                gap: 8px;

            }

        }

    </style>

</head>


<body>


    <!-- =================================================
         HEADER
    ================================================== -->

    <?php include "includes/header.php"; ?>


    <!-- =================================================
         BREAKING NEWS
    ================================================== -->

    <?php include "includes/breaking-news.php"; ?>


    <!-- =================================================
         MAIN
    ================================================== -->

    <main class="watch-page">


        <?php if (!empty($youtube_id)): ?>


            <!-- =========================================
                 YOUTUBE VIDEO
            ========================================== -->

            <div class="video-player">

                <iframe
                    src="https://www.youtube.com/embed/<?php
                        echo htmlspecialchars(
                            $youtube_id,
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>"

                    title="<?php
                        echo $page_title;
                    ?>"

                    allow="
                        accelerometer;
                        autoplay;
                        clipboard-write;
                        encrypted-media;
                        gyroscope;
                        picture-in-picture;
                        web-share
                    "

                    allowfullscreen
                ></iframe>

            </div>


        <?php elseif (!empty($video_url)): ?>


            <!-- =========================================
                 DIRECT VIDEO
            ========================================== -->

            <div class="video-player">

                <video
                    controls
                    preload="metadata"
                    <?php if (!empty($thumbnail)): ?>
                        poster="<?php echo $thumbnail; ?>"
                    <?php endif; ?>
                >

                    <source
                        src="<?php
                            echo htmlspecialchars(
                                $video_url,
                                ENT_QUOTES,
                                "UTF-8"
                            );
                        ?>"
                    >

                    Your browser does not support
                    video playback.

                </video>

            </div>


        <?php else: ?>


            <!-- =========================================
                 VIDEO ERROR
            ========================================== -->

            <div class="video-error">

                <h2>
                    Video Not Available
                </h2>

                <p>
                    This video does not have a valid
                    video URL.
                </p>

            </div>


        <?php endif; ?>


        <!-- =================================================
             VIDEO INFORMATION
        ================================================== -->

        <div class="video-info">


            <?php if (!empty($video["category_name"])): ?>

                <div class="video-category">

                    <?php
                    echo htmlspecialchars(
                        $video["category_name"],
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>

                </div>

            <?php endif; ?>


            <h1>

                <?php
                echo $page_title;
                ?>

            </h1>


            <?php if (!empty($video["description"])): ?>

                <p class="video-description">

                    <?php
                    echo nl2br($description);
                    ?>

                </p>

            <?php endif; ?>


            <div class="video-meta">

                <span>

                    👁

                    <?php
                    echo (int) $video["views"] + 1;
                    ?>

                    views

                </span>


                <span>

                    📅

                    <?php
                    echo date(
                        "d M Y",
                        strtotime(
                            $video["created_at"]
                        )
                    );
                    ?>

                </span>

            </div>


            <a
                href="video.php"
                class="back-button"
            >

                ← Back to Videos

            </a>


        </div>


    </main>


    <!-- =================================================
         FOOTER
    ================================================== -->

    <?php include "includes/footer.php"; ?>


</body>

</html>