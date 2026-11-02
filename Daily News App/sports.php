<?php


/* =====================================================
   SESSION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



require_once "config/database.php";


/* =====================================================
   GET SPORTS NEWS
===================================================== */

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

    WHERE LOWER(TRIM(c.name)) = 'sports'

    ORDER BY
        n.id DESC
";


$result = mysqli_query($conn, $query);


/* =====================================================
   ERROR
===================================================== */

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
        Sports News - Daily News
    </title>


    <!-- EXISTING CSS -->

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
        href="assets/css/news.css"
    >


    <style>

        .sports-container {

            width: 90%;

            max-width: 1400px;

            margin: auto;

            padding:
                45px 0
                70px;
        }


        .sports-heading {

            margin-bottom: 35px;
        }


        .sports-heading h1 {

            font-size: 42px;

            color: #ffffff;

            margin-bottom: 10px;
        }


        .sports-heading p {

            color: #999999;

            font-size: 17px;
        }


        .sports-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 30px;
        }


        .sports-card {

            background: #ffffff;

            border-radius: 12px;

            overflow: hidden;

            color: #111111;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.25);

            transition:
                transform .25s ease;
        }


        .sports-card:hover {

            transform:
                translateY(-6px);
        }


        .sports-image {

            width: 100%;

            height: 180px;

            object-fit: cover;

            display: block;
        }


        .sports-no-image {

            width: 100%;

            height: 180px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eeeeee;

            font-size: 55px;

            color: #777777;
        }


        .sports-content {

            padding: 22px;
        }


        .sports-category {

            color: #ed1111;

            font-size: 13px;

            font-weight: bold;

            text-transform: uppercase;

            margin-bottom: 10px;
        }


        .sports-title {

            font-size: 23px;

            line-height: 1.4;

            color: #111111;

            margin-bottom: 12px;
        }


        .sports-description {

            color: #555555;

            font-size: 15px;

            line-height: 1.6;

            margin-bottom: 18px;
        }


        .sports-meta {

            display: flex;

            gap: 12px;

            color: #777777;

            font-size: 13px;

            margin-bottom: 18px;
        }


        .sports-button {

            display: inline-block;

            padding:
                11px 18px;

            background: #ed1111;

            color: #ffffff;

            text-decoration: none;

            border-radius: 6px;

            font-weight: bold;

            font-size: 14px;
        }


        .sports-button:hover {

            background: #c90000;

            color: #ffffff;
        }


        .sports-empty {

            background: #222222;

            border:
                1px solid #333333;

            border-radius: 10px;

            padding: 60px 20px;

            text-align: center;

            color: #aaaaaa;

            font-size: 18px;
        }


        @media (max-width: 1000px) {

            .sports-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 650px) {

            .sports-grid {

                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<?php

/* HEADER */

require_once "includes/header.php";

?>


<main class="sports-container">


    <!-- =================================================
         HEADING
    ================================================== -->

    <div class="sports-heading">

        <h1>
            Sports News
        </h1>

        <p>
            Latest sports news, matches, teams and updates.
        </p>

    </div>


    <!-- =================================================
         SPORTS NEWS
    ================================================== -->

    <?php if (mysqli_num_rows($result) > 0): ?>


        <div class="sports-grid">


            <?php while ($row = mysqli_fetch_assoc($result)): ?>


                <article class="sports-card">


                    <!-- IMAGE -->

                    <?php

                    $image =
                        trim(
                            $row['image'] ?? ''
                        );

                    ?>


                    <?php if ($image !== ''): ?>


                        <img
                            src="uploads/news/<?php
                                echo htmlspecialchars(
                                    $image,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $row['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>"
                            class="sports-image"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >


                        <div
                            class="sports-no-image"
                            style="display:none;"
                        >
                            🏏
                        </div>


                    <?php else: ?>


                        <div class="sports-no-image">
                            🏏
                        </div>


                    <?php endif; ?>


                    <!-- CONTENT -->

                    <div class="sports-content">


                        <!-- CATEGORY -->

                        <div class="sports-category">

                            <?php

                            echo htmlspecialchars(
                                $row['category_name']
                                ?? 'Sports',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </div>


                        <!-- TITLE -->

                        <h2 class="sports-title">

                            <?php

                            echo htmlspecialchars(
                                $row['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </h2>


                        <!-- DESCRIPTION -->

                        <p class="sports-description">

                            <?php

                            $description =
                                strip_tags(
                                    $row['description']
                                    ?? ''
                                );


                            if (
                                strlen($description) > 180
                            ) {

                                echo htmlspecialchars(
                                    substr(
                                        $description,
                                        0,
                                        180
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                echo "...";

                            } else {

                                echo htmlspecialchars(
                                    $description,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                            }

                            ?>

                        </p>


                        <!-- META -->

                        <div class="sports-meta">

                            <span>

                                👁️

                                <?php
                                echo (int)$row['views'];
                                ?>

                                views

                            </span>


                            <span>
                                •
                            </span>


                            <span>

                                <?php

                                $date =
                                    !empty(
                                        $row['published_at']
                                    )
                                    ? $row['published_at']
                                    : $row['created_at'];


                                if ($date) {

                                    echo date(
                                        "d M Y",
                                        strtotime($date)
                                    );

                                }

                                ?>

                            </span>

                        </div>


                        <!-- BUTTON -->

                        <a
                            href="article.php?id=<?php
                                echo (int)$row['id'];
                            ?>"
                            class="sports-button"
                        >

                            Read Full Article →

                        </a>


                    </div>


                </article>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="sports-empty">

            🏏

            <br><br>

            No Sports News Available.

        </div>


    <?php endif; ?>


</main>


</body>

</html>