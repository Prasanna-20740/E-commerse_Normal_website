<?php

require_once "config/database.php";

/* =====================================================
   GET NEWS FROM DATABASE
===================================================== */

$query = "SELECT
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


$result = mysqli_query($conn, $query);


/* =====================================================
   CHECK QUERY
===================================================== */

if ($result === false) {

    die(
        "Database Error: " .
        mysqli_error($conn)
    );

}


/* =====================================================
   HEADER
===================================================== */

require_once "includes/header.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Latest News - Daily News</title>
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
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-family);
            background: var(--dark);
            color: var(--light);
            min-height: 100vh;
        }

        /* CONTAINER */
        .news-container {
            width: 90%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 0 5rem;
        }

        /* PAGE TITLE */
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--light);
            margin-bottom: 0.5rem;
        }
        .page-title p {
            color: var(--muted);
            font-size: 1.05rem;
        }

        /* GRID */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.75rem;
        }

        /* CARD */
        .news-card {
            background: var(--light);
            color: var(--text);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: transform var(--transition), box-shadow var(--transition);
        }
        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.25);
        }

        /* IMAGE */
        .news-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            background: #eee;
        }
        .no-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #eee, #ddd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--muted);
        }

        /* CONTENT */
        .news-content { padding: 1.25rem; }

        /* CATEGORY */
        .category {
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        /* TITLE */
        .news-title {
            font-size: 1.3rem;
            line-height: 1.35;
            margin-bottom: 0.75rem;
            color: var(--text);
            font-weight: 700;
        }

        /* DESCRIPTION */
        .description {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        /* META */
        .meta {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* READ BUTTON */
        .read-button {
            display: inline-block;
            padding: 0.6rem 1.1rem;
            background: var(--primary);
            color: var(--light);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: background var(--transition);
        }
        .read-button:hover {
            background: #c90000;
            color: var(--light);
        }

        /* EMPTY STATE */
        .empty {
            background: var(--dark-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 3.5rem 1.25rem;
            text-align: center;
            color: var(--muted);
            font-size: 1.1rem;
        }

        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .news-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 650px) {
            .news-container { width: 94%; }
            .news-grid { grid-template-columns: 1fr; }
            .page-title h1 { font-size: 2rem; }
        }
    </style>


</head>


<body>


<main class="news-container">


    <!-- ==========================================
         PAGE TITLE
    ========================================== -->

    <div class="page-title">

        <h1>
            Latest News
        </h1>

        <p>
            Stay updated with the latest news and important stories.
        </p>

    </div>


    <!-- ==========================================
         NEWS
    ========================================== -->

    <?php if (mysqli_num_rows($result) > 0): ?>


        <div class="news-grid">


            <?php while ($row = mysqli_fetch_assoc($result)): ?>


                <article class="news-card">


                    <!-- IMAGE -->

                    <?php

                    $image =
                        trim(
                            $row['image'] ?? ''
                        );

                    ?>


                    <?php if ($image != ''): ?>


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
                            class="news-image"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >


                        <div
                            class="no-image"
                            style="display:none;"
                        >
                            📰
                        </div>


                    <?php else: ?>


                        <div class="no-image">
                            📰
                        </div>


                    <?php endif; ?>


                    <!-- CONTENT -->

                    <div class="news-content">


                        <!-- CATEGORY -->

                        <div class="category">

                            <?php

                            echo htmlspecialchars(
                                $row['category_name']
                                ?? 'News',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </div>


                        <!-- TITLE -->

                        <h2 class="news-title">

                            <?php

                            echo htmlspecialchars(
                                $row['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </h2>


                        <!-- DESCRIPTION -->

                        <p class="description">

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

                        <div class="meta">

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


                        <!-- READ -->

                        <a
                            href="article.php?id=<?php
                                echo (int)$row['id'];
                            ?>"
                            class="read-button"
                        >
                            Read Full Article →
                        </a>


                    </div>


                </article>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="empty">

            📰

            <br><br>

            No news found in database.

        </div>


    <?php endif; ?>


</main>


</body>

</html>