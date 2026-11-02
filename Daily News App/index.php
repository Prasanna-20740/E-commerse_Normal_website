<?php

require_once "config/database.php";


/* =====================================
   GET PUBLISHED NEWS
===================================== */

$query = " SELECT
        n.id,
        n.title,
        n.slug,
        n.description,
        n.image,
        n.author,
        n.views,
        n.published_at,

        c.name AS category_name,

        s.name AS subcategory_name

    FROM news n

    LEFT JOIN categories c
        ON n.category_id = c.id

    LEFT JOIN subcategories s
        ON n.subcategory_id = s.id

    WHERE n.status = 'published'

    ORDER BY n.published_at DESC

    LIMIT 10
";


$result = mysqli_query(
    $conn,
    $query
);


if ($result === false) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

}


/* =====================================
   STORE NEWS
===================================== */

$newsList = [];

while (
    $row = mysqli_fetch_assoc($result)
) {

    $newsList[] = $row;

}


/* =====================================
   FEATURED NEWS
===================================== */

$featuredNews = $newsList[0] ?? null;


/* =====================================
   OTHER NEWS
===================================== */

$otherNews = array_slice(
    $newsList,
    1
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
        Daily News
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
        href="assets/css/home.css"
    >

</head>


<body>


<?php include "includes/header.php"; ?>
<?php include "includes/breaking-news.php"; ?>




<!-- =====================================
     HOME CONTENT
===================================== -->

<main class="home-container">


    <!-- SECTION TITLE -->

    <div class="section-heading">

        <h1>
            Latest News & Videos
        </h1>

    </div>


    <!-- =================================
         NEWS LAYOUT
    ================================== -->

    <section class="news-layout">


        <!-- =================================
             FEATURED NEWS
        ================================== -->

        <?php if ($featuredNews): ?>


        <article class="featured-news">


            <div class="featured-image">


                <?php if (
                    !empty(
                        $featuredNews["image"]
                    )
                ): ?>

                    <img
                        src="uploads/news/<?php
                        echo htmlspecialchars(
                            $featuredNews["image"]
                        );
                        ?>"
                        alt="<?php
                        echo htmlspecialchars(
                            $featuredNews["title"]
                        );
                        ?>"
                    >

                <?php else: ?>

                    <img
                        src="assets/images/default-news.jpg"
                        alt="Featured News"
                    >

                <?php endif; ?>


                <span class="news-category">

                    <?php
                    echo htmlspecialchars(
                        $featuredNews[
                            "category_name"
                        ] ?? "NEWS"
                    );
                    ?>

                </span>


            </div>


            <div class="featured-content">


                <h2>

                    <a
                        href="news-details.php?id=<?php
                        echo $featuredNews["id"];
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $featuredNews["title"]
                        );
                        ?>

                    </a>

                </h2>


                <p>

                    <?php

                    $description =
                        $featuredNews[
                            "description"
                        ] ?? "";


                    echo htmlspecialchars(
                        $description
                    );

                    ?>

                </p>

                 <p>

                    <?php

                    $content	 =
                        $featuredNews[
                            "content"
                        ] ?? "";


                    echo htmlspecialchars(
                        $content
                    );

                    ?>

                </p>


                <div class="news-meta">


                    <span>

                        👤 By

                        <?php
                        echo htmlspecialchars(
                            $featuredNews[
                                "author"
                            ] ?? "Admin"
                        );
                        ?>

                    </span>


                    <span>

                        🕒

                        <?php

                        if (
                            !empty(
                                $featuredNews[
                                    "published_at"
                                ]
                            )
                        ) {

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $featuredNews[
                                        "published_at"
                                    ]
                                )
                            );

                        }

                        ?>

                    </span>


                </div>


            </div>


        </article>


        <?php endif; ?>


        <!-- =================================
             OTHER NEWS CARDS
        ================================== -->


        <?php if (
            count($otherNews) > 0
        ): ?>


            <?php foreach (
                $otherNews as $news
            ): ?>


            <article class="news-card">


                <!-- IMAGE -->

                <div class="card-image">


                    <?php if (
                        !empty(
                            $news["image"]
                        )
                    ): ?>

                        <img
                            src="uploads/news/<?php
                            echo htmlspecialchars(
                                $news["image"]
                            );
                            ?>"
                            alt="<?php
                            echo htmlspecialchars(
                                $news["title"]
                            );
                            ?>"
                        >

                    <?php else: ?>

                        <img
                            src="assets/images/default-news.jpg"
                            alt="News"
                        >

                    <?php endif; ?>


                    <!-- CATEGORY -->

                    <span
                        class="news-category"
                    >

                        <?php
                        echo htmlspecialchars(
                            $news[
                                "category_name"
                            ] ?? "NEWS"
                        );
                        ?>

                    </span>


                </div>


                <!-- CONTENT -->

                <div class="card-content">


                    <h3>

                        <a
                            href="news-details.php?id=<?php
                            echo $news["id"];
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $news["title"]
                            );
                            ?>

                        </a>

                    </h3>


                    <div class="news-meta">


                        <span>

                            By

                            <?php
                            echo htmlspecialchars(
                                $news[
                                    "author"
                                ] ?? "Admin"
                            );
                            ?>

                        </span>


                        <span>

                            🕒

                            <?php

                            if (
                                !empty(
                                    $news[
                                        "published_at"
                                    ]
                                )
                            ) {

                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $news[
                                            "published_at"
                                        ]
                                    )
                                );

                            }

                            ?>

                        </span>


                    </div>


                </div>


            </article>


            <?php endforeach; ?>


        <?php else: ?>


            <?php if (!$featuredNews): ?>

                <div class="no-news">

                    <h2>
                        No Published News
                    </h2>

                    <p>
                        News published from
                        Admin Panel will
                        appear here.
                    </p>

                </div>

            <?php endif; ?>


        <?php endif; ?>


        <!-- =================================
             ADVERTISEMENT
        ================================== -->

        <aside class="advertisement">


            <div class="ad-title">

                ADVERTISEMENT

            </div>


            <div class="ad-box">


                <h3>

                    Best News Experience
                    with Daily News

                </h3>


                <button>

                    Learn More

                </button>


            </div>


        </aside>


    </section>


    <!-- =================================
         TRENDING TOPICS
    ================================== -->

    <section class="trending-section">


        <h2>
            Trending Topics
        </h2>


        <div class="trending-list">


            <span>
                # ISRO
            </span>


            <span>
                # IndiaVsAus
            </span>


            <span>
                # Budget2026
            </span>


            <span>
                # ElectricVehicles
            </span>


            <span>
                # AI Technology
            </span>


            <span>
                # Chandrayaan-3
            </span>


            <span>
                # Stock Market
            </span>


        </div>


    </section>


</main>


</body>

</html>