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
   FETCH ACTIVE TOPICS
===================================================== */

$query = " SELECT
        id,
        topic_name,
        slug,
        views,
        created_at
    FROM trending_topics
    WHERE status = 'active'
    ORDER BY views DESC, id DESC
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
        Trending Topics - Daily News
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/header.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/navbar.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/trending.css"
    >


    <style>

        .trending-page {

            width: 90%;

            max-width: 1300px;

            margin: 40px auto 70px;

        }


        .trending-page-header {

            margin-bottom: 30px;

        }


        .trending-page-header h1 {

            margin: 0;

            font-size: 34px;

            color: #fcfcfc;

        }


        .trending-page-header p {

            margin-top: 8px;

            color: #9c9a9a;

            font-size: 15px;

        }


        .trending-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(280px, 1fr)
                );

            gap: 20px;

        }


        .trending-card {

            display: block;

            padding: 25px;

            background: #ffffff;

            border: 1px solid #eeeeee;

            border-radius: 12px;

            text-decoration: none;

            transition: 0.2s ease;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.04);

        }


        .trending-card:hover {

            transform: translateY(-4px);

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.10);

            border-color: #ef1111;

        }


        .trending-card-icon {

            font-size: 30px;

            margin-bottom: 15px;

        }


        .trending-card h2 {

            margin: 0 0 12px;

            color: #111111;

            font-size: 20px;

            line-height: 1.4;

        }


        .trending-card-meta {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #777777;

            font-size: 13px;

        }


        .trending-card-meta .views {

            color: #555555;

        }


        .trending-card-meta .date {

            color: #777777;

        }


        .empty-trending {

            padding: 70px 20px;

            text-align: center;

            background: #f7f7f7;

            border-radius: 12px;

        }


        .empty-trending-icon {

            font-size: 50px;

            margin-bottom: 15px;

        }


        .empty-trending h2 {

            margin: 0 0 10px;

            color: #222222;

        }


        .empty-trending p {

            margin: 0;

            color: #777777;

        }


        @media (max-width: 600px) {

            .trending-page {

                width: 94%;

                margin-top: 25px;

            }


            .trending-page-header h1 {

                font-size: 28px;

            }


            .trending-grid {

                grid-template-columns: 1fr;

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
   TRENDING TOPICS BAR
===================================================== */

include "includes/trending-topics.php";

?>


<main class="trending-page">


    <div class="trending-page-header">

        <h1>
            🔥 Trending Topics
        </h1>

        <p>
            Discover the topics people are
            currently following on Daily News.
        </p>

    </div>


    <?php if (
        mysqli_num_rows($result) > 0
    ): ?>


        <div class="trending-grid">


            <?php while (
                $topic = mysqli_fetch_assoc($result)
            ): ?>


                <a
                    href="search.php?q=<?php
                        echo urlencode(
                            $topic["topic_name"]
                        );
                    ?>"
                    class="trending-card"
                >


                    <div class="trending-card-icon">

                        🔥

                    </div>


                    <h2>

                        #

                        <?php

                        echo htmlspecialchars(
                            $topic["topic_name"],
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        ?>

                    </h2>


                    <div class="trending-card-meta">


                        <span class="views">

                            👁

                            <?php

                            echo (int)
                                $topic["views"];

                            ?>

                            views

                        </span>


                        <span class="date">

                            •


                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $topic["created_at"]
                                )
                            );

                            ?>

                        </span>


                    </div>


                </a>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="empty-trending">


            <div class="empty-trending-icon">

                🔥

            </div>


            <h2>

                No Trending Topics

            </h2>


            <p>

                There are no active trending
                topics right now.

            </p>


        </div>


    <?php endif; ?>


</main>


<?php

include "includes/footer.php";

?>


</body>

</html>