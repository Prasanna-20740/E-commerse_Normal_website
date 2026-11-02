<?php
/* =====================================================
   SESSION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   DATABASE CONNECTION
===================================================== */

require_once "config/database.php";


/* =====================================================
   FETCH PUBLISHED VIDEOS
===================================================== */

$videoQuery = " SELECT
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
    WHERE v.status = 'published'
    ORDER BY v.id DESC
";


$videoResult = mysqli_query($conn, $videoQuery);


/* =====================================================
   DATABASE ERROR
===================================================== */

if (!$videoResult) {

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

    <title>Videos - Daily News</title>


    <!-- MAIN CSS -->

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


    /* Premium redesign using design tokens defined in header */
body {font-family: var(--font-family); background: var(--dark); color: var(--light);}
.video-page {background: var(--dark); padding: 3rem 1rem;}
.video-feed {width: 90%; max-width: 1200px; margin: 0 auto;}
.video-item {background: var(--bg-card); border-radius: var(--radius); overflow: hidden; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.30); transition: var(--transition);}
.video-item:hover {transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0,0,0,0.40);}
.video-frame {position: relative; width: 100%; height: 350px; background: var(--dark-card); overflow: hidden; border-radius: var(--radius);}
.video-thumbnail {width: 100%; height: 100%; object-fit: cover; display: block;}
.video-placeholder {width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--muted); color: var(--light); font-size: 3rem;}
.video-play {position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 68px; height: 68px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--primary); color: var(--light) !important; text-decoration: none; font-size: 28px; box-sizing: border-box; z-index: 10; transition: var(--transition);}
.video-play:hover {transform: translate(-50%, -50%) scale(1.08); background: var(--primary-dark);}
.video-title {margin: 10px 0 12px; font-size: 1.35rem; line-height: 1.45; font-weight: 700;}
.video-title a {color: var(--light) !important; text-decoration: none;}
.video-title a:hover {color: var(--primary) !important;}
.video-meta {display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; color: var(--muted); font-size: 0.85rem;}
.video-category {font-weight: 600;}
.verified {width: 1rem; height: 1rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #1da1f2; color: var(--light); font-size: 0.6rem; font-weight: bold;}
.meta-dot {color: var(--muted);}
.video-views, .video-date {color: var(--muted);}
.video-description {margin: 1rem 0; color: var(--muted); font-size: 0.95rem; line-height: 1.6;}
.watch-button {display: inline-block; margin-top: 1rem; padding: 0.6rem 1.2rem; background: var(--primary); color: var(--light) !important; text-decoration: none; border-radius: var(--radius); font-size: 0.85rem; font-weight: 700; transition: var(--transition);}
.watch-button:hover {background: var(--primary-dark);}
.video-separator {width: 100%; height: 1px; background: var(--border); margin-top: 2rem;}
.no-videos {width: 100%; padding: 4rem 1rem; text-align: center; background: var(--dark-card); border-radius: var(--radius); color: var(--light); box-sizing: border-box;}
.no-videos h2 {margin: 0 0 0.5rem; color: var(--light);}
.no-videos p {margin: 0; color: var(--muted);}
@media (max-width: 700px) {
  .video-page {padding: 2rem 0.75rem;}
  .video-frame {height: 260px;}
  .video-title {font-size: 1.125rem;}
}
@media (max-width: 450px) {
  .video-frame {height: 220px;}
  .video-play {width: 55px; height: 55px; font-size: 23px;}
  .video-title {font-size: 1rem;}
}

        /* =================================================
           VIDEO PAGE
        ================================================= */

        .video-page {

            width: 100%;

            min-height: 100vh;

            background: #1b1b1b;

            padding: 40px 20px 70px;

            box-sizing: border-box;

        }


        /* =================================================
           VIDEO FEED
        ================================================= */

        .video-feed {

            width: 625px;

            max-width: 100%;

            margin: 0 auto;

        }


        /* =================================================
           VIDEO ITEM
        ================================================= */

        .video-item {

            width: 100%;

            margin-bottom: 40px;

        }


        /* =================================================
           VIDEO FRAME
        ================================================= */

        .video-frame {

            position: relative;

            width: 100%;

            height: 350px;

            background: #111111;

            overflow: hidden;

            border-radius: 5px;

        }


        /* =================================================
           THUMBNAIL
        ================================================= */

        .video-thumbnail {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

        }


        /* =================================================
           PLACEHOLDER
        ================================================= */

        .video-placeholder {

            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #292929;

            color: #ffffff;

            font-size: 55px;

        }


        /* =================================================
           PLAY BUTTON
        ================================================= */

        .video-play {

            position: absolute;

            top: 50%;

            left: 50%;

            transform:
                translate(-50%, -50%);

            width: 68px;

            height: 68px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #ed1111;

            color: #ffffff !important;

            text-decoration: none;

            font-size: 28px;

            padding-left: 4px;

            box-sizing: border-box;

            z-index: 10;

        }


        .video-play:hover {

            transform:
                translate(-50%, -50%)
                scale(1.08);

            background: #ff0000;

        }


        /* =================================================
           TITLE
        ================================================= */

        .video-title {

            margin: 10px 0 12px;

            padding: 0;

            font-size: 21px;

            line-height: 1.45;

            font-weight: 700;

        }


        .video-title a {

            color: #ffffff !important;

            text-decoration: none;

        }


        .video-title a:hover {

            color: #ed1111 !important;

        }


        /* =================================================
           META
        ================================================= */

        .video-meta {

            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 8px;

            color: #ffffff;

            font-size: 13px;

        }


        .video-category {

            color: #ffffff;

            font-weight: 600;

        }


        .verified {

            width: 17px;

            height: 17px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #1da1f2;

            color: #ffffff;

            font-size: 10px;

            font-weight: bold;

        }


        .meta-dot {

            color: #888888;

        }


        .video-views {

            color: #dddddd;

        }


        .video-date {

            color: #dddddd;

        }


        /* =================================================
           DESCRIPTION
        ================================================= */

        .video-description {

            margin: 10px 0 0;

            color: #aaaaaa;

            font-size: 14px;

            line-height: 1.6;

        }


        /* =================================================
           WATCH BUTTON
        ================================================= */

        .watch-button {

            display: inline-block;

            margin-top: 15px;

            padding: 10px 17px;

            background: #ed1111;

            color: #ffffff !important;

            text-decoration: none;

            border-radius: 5px;

            font-size: 13px;

            font-weight: 700;

        }


        .watch-button:hover {

            background: #c90000;

        }


        /* =================================================
           SEPARATOR
        ================================================= */

        .video-separator {

            width: 100%;

            height: 1px;

            background: #333333;

            margin-top: 30px;

        }


        /* =================================================
           NO VIDEOS
        ================================================= */

        .no-videos {

            width: 100%;

            padding: 60px 20px;

            text-align: center;

            background: #242424;

            border-radius: 8px;

            color: #ffffff;

            box-sizing: border-box;

        }


        .no-videos h2 {

            margin: 0 0 10px;

            color: #ffffff;

        }


        .no-videos p {

            margin: 0;

            color: #aaaaaa;

        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 700px) {

            .video-page {

                padding: 25px 12px 50px;

            }


            .video-feed {

                width: 100%;

            }


            .video-frame {

                height: 260px;

            }


            .video-title {

                font-size: 18px;

            }

        }


        @media (max-width: 450px) {

            .video-frame {

                height: 220px;

            }


            .video-play {

                width: 55px;

                height: 55px;

                font-size: 23px;

            }


            .video-title {

                font-size: 17px;

            }

        }

    </style>

</head>


<body>


<?php

/* =====================================================
   HEADER
===================================================== */

include "includes/header.php";


/* =====================================================
   BREAKING NEWS
===================================================== */

include "includes/breaking-news.php";

?>


<!-- =====================================================
     VIDEO PAGE
===================================================== -->

<main class="video-page">


    <div class="video-feed">


        <?php if (mysqli_num_rows($videoResult) > 0): ?>


            <?php while ($video = mysqli_fetch_assoc($videoResult)): ?>


                <article class="video-item">


                    <!-- =================================
                         VIDEO THUMBNAIL
                    ================================== -->

                    <div class="video-frame">


                        <?php if (!empty($video["thumbnail"])): ?>


                            <img
                                src="<?php

                                    echo htmlspecialchars(
                                        $video["thumbnail"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                ?>"
                                alt="<?php

                                    echo htmlspecialchars(
                                        $video["title"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                ?>"
                                class="video-thumbnail"
                            >


                        <?php else: ?>


                            <div class="video-placeholder">

                                🎥

                            </div>


                        <?php endif; ?>


                        <!-- PLAY BUTTON -->

                        <a
                            href="watch.php?id=<?php
                                echo (int)$video["id"];
                            ?>"
                            class="video-play"
                        >

                            ▶

                        </a>


                    </div>


                    <!-- =================================
                         TITLE
                    ================================== -->

                    <h2 class="video-title">

                        <a
                            href="watch.php?id=<?php
                                echo (int)$video["id"];
                            ?>"
                        >

                            <?php

                            echo htmlspecialchars(
                                $video["title"],
                                ENT_QUOTES,
                                "UTF-8"
                            );

                            ?>

                        </a>

                    </h2>


                    <!-- =================================
                         META
                    ================================== -->

                    <div class="video-meta">


                        <span class="video-category">

                            <?php

                            echo !empty(
                                $video["category_name"]
                            )

                            ?

                            htmlspecialchars(
                                $video["category_name"],
                                ENT_QUOTES,
                                "UTF-8"
                            )

                            :

                            "News";

                            ?>

                        </span>


                        <span class="verified">

                            ✓

                        </span>


                        <span class="meta-dot">

                            •

                        </span>


                        <span class="video-views">

                            <?php

                            echo (int)$video["views"];

                            ?>

                            views

                        </span>


                        <span class="meta-dot">

                            •

                        </span>


                        <span class="video-date">

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


                    <!-- =================================
                         DESCRIPTION
                    ================================== -->

                    <?php if (!empty($video["description"])): ?>


                        <p class="video-description">

                            <?php

                            echo htmlspecialchars(
                                $video["description"],
                                ENT_QUOTES,
                                "UTF-8"
                            );

                            ?>

                        </p>


                    <?php endif; ?>


                    <!-- =================================
                         WATCH BUTTON
                    ================================== -->

                    <a
                        href="watch.php?id=<?php
                            echo (int)$video["id"];
                        ?>"
                        class="watch-button"
                    >

                        ▶ Watch Video

                    </a>


                    <!-- SEPARATOR -->

                    <div class="video-separator"></div>


                </article>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="no-videos">

                <h2>
                    No Videos Available
                </h2>

                <p>
                    There are no published videos
                    available right now.
                </p>

            </div>


        <?php endif; ?>


    </div>


</main>


<?php

/* =====================================================
   FOOTER
===================================================== */

include "includes/footer.php";

?>


</body>

</html>