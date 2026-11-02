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
   SEARCH QUERY
===================================================== */

$search = trim($_GET['q'] ?? '');

$searchResult = null;
$resultCount = 0;


/* =====================================================
   SEARCH NEWS
===================================================== */

if ($search !== '') {

    /*
     * Split search text into words
     *
     * Example:
     * RCB Win IPL Victory 2026
     *
     * RCB
     * Win
     * IPL
     * Victory
     * 2026
     */

    $words = preg_split(
        '/\s+/',
        $search,
        -1,
        PREG_SPLIT_NO_EMPTY
    );


    $conditions = [];
    $params = [];
    $types = '';


    /* =================================================
       CREATE SEARCH CONDITIONS
    ================================================== */

    foreach ($words as $word) {

        $conditions[] = "
            (
                n.title LIKE ?
                OR n.content LIKE ?
            )
        ";

        $likeWord = '%' . $word . '%';

        $params[] = $likeWord;
        $params[] = $likeWord;

        $types .= 'ss';
    }


    /* =================================================
       WHERE CONDITIONS
    ================================================== */

    $whereConditions = implode(
        ' AND ',
        $conditions
    );


    /* =================================================
       SQL
    ================================================== */

    $sql = " SELECT
            n.id,
            n.title,
            n.slug,
            n.content,
            n.image,
            n.views,
            n.created_at,
            c.name AS category_name

        FROM news n

        LEFT JOIN categories c
            ON n.category_id = c.id

        WHERE
            n.status = 'published'

            AND

            $whereConditions

        ORDER BY
            n.created_at DESC,
            n.id DESC
    ";


    /* =================================================
       PREPARE
    ================================================== */

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );


    if (!$stmt) {

        die(
            'Database Error: ' .
            mysqli_error($conn)
        );
    }


    /* =================================================
       BIND PARAMETERS
    ================================================== */

    if (!empty($params)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }


    /* =================================================
       EXECUTE
    ================================================== */

    mysqli_stmt_execute($stmt);


    /* =================================================
       GET SEARCH RESULT
    ================================================= */

    $searchResult = mysqli_stmt_get_result($stmt);


    if ($searchResult) {

        $resultCount = mysqli_num_rows(
            $searchResult
        );
    }


    mysqli_stmt_close($stmt);
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


    <!-- =================================================
         TITLE
    ================================================== -->

    <title>

        <?php if ($search !== ''): ?>

            Search:
            <?php
            echo htmlspecialchars(
                $search,
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

            - Daily News

        <?php else: ?>

            Search - Daily News

        <?php endif; ?>

    </title>


    <!-- =================================================
         EXISTING CSS
    ================================================== -->

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


    <!-- =================================================
         SEARCH PAGE CSS
    ================================================== -->

    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 0;
        }


        /* =================================================
           MAIN SEARCH PAGE
        ================================================== */

        .search-page {

            width: 90%;

            max-width: 1300px;

            margin: 45px auto 80px;

            min-height: 500px;
        }


        /* =================================================
           SEARCH HEADER
        ================================================== */

        .search-header {

            margin-bottom: 30px;
        }


        .search-header h1 {

            margin: 0 0 12px;

            font-size: 34px;

            font-weight: 700;

            color: #ffffff !important;
        }


        .search-header p {

            margin: 0;

            font-size: 16px;

            color: #a4a4a4 !important;
        }


        .search-query {

            color: #ed1111 !important;
        }


        .result-count {

            margin-top: 10px;

            color: #bebebe !important;

            font-size: 14px;
        }


        /* =================================================
           SEARCH GRID
        ================================================== */

        .search-grid {

            display: grid !important;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(300px, 1fr)
                );

            gap: 25px;

            width: 100%;

            margin-top: 25px;

            visibility: visible !important;

            opacity: 1 !important;
        }


        /* =================================================
           SEARCH CARD
        ================================================== */

        .search-card {

            display: block !important;

            width: 100%;

            background: #ffffff !important;

            border: 1px solid #dddddd;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.18);

            color: #111 !important;

            visibility: visible !important;

            opacity: 1 !important;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;
        }


        .search-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.25);
        }


        /* =================================================
           IMAGE
        ================================================== */

        .search-image {

            width: 100%;

            height: 210px;

            background: #eeeeee;

            overflow: hidden;

            display: flex;

            align-items: center;

            justify-content: center;
        }


        .search-image img {

            display: block;

            width: 100%;

            height: 100%;

            object-fit: cover;
        }


        /* =================================================
           DEFAULT IMAGE
        ================================================== */

        .default-news-image {

            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #eeeeee,
                    #dcdcdc
                );

            color: #777;

            font-size: 55px;
        }


        /* =================================================
           CONTENT
        ================================================== */

        .search-content {

            display: block !important;

            padding: 20px;

            background: #ffffff !important;

            color: #111111 !important;
        }


        /* =================================================
           CATEGORY
        ================================================== */

        .search-category {

            display: block;

            margin-bottom: 8px;

            color: #ed1111 !important;

            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;
        }


        /* =================================================
           TITLE
        ================================================== */

        .search-title {

            margin: 0 0 12px;

            font-size: 21px;

            line-height: 1.4;

            font-weight: 700;
        }


        .search-title a {

            color: #111111 !important;

            text-decoration: none;

            transition: color 0.2s ease;
        }


        .search-title a:hover {

            color: #ed1111 !important;
        }


        /* =================================================
           DESCRIPTION
        ================================================== */

        .search-description {

            margin: 0 0 15px;

            color: #555555 !important;

            font-size: 14px;

            line-height: 1.6;
        }


        /* =================================================
           META
        ================================================== */

        .search-meta {

            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

            color: #777777 !important;

            font-size: 13px;
        }


        /* =================================================
           READ MORE
        ================================================== */

        .read-more {

            display: inline-block;

            margin-top: 17px;

            padding: 9px 15px;

            background: #ed1111;

            color: #ffffff !important;

            text-decoration: none;

            border-radius: 5px;

            font-size: 13px;

            font-weight: 700;

            transition:
                background 0.2s ease;
        }


        .read-more:hover {

            background: #c90000;

            text-decoration: none;
        }


        /* =================================================
           NO RESULTS
        ================================================== */

        .no-results {

            width: 100%;

            padding: 70px 20px;

            text-align: center;

            background: #ffffff !important;

            border-radius: 12px;

            border: 1px solid #dddddd;

            color: #222222 !important;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.12);
        }


        .no-results-icon {

            font-size: 55px;

            margin-bottom: 15px;
        }


        .no-results h2 {

            margin: 0 0 10px;

            color: #222222 !important;
        }


        .no-results p {

            margin: 0;

            color: #777777 !important;

            line-height: 1.6;
        }


        /* =================================================
           EMPTY SEARCH
        ================================================== */

        .empty-search {

            padding: 80px 20px;

            text-align: center;
        }


        .empty-search-icon {

            font-size: 55px;

            margin-bottom: 15px;
        }


        .empty-search h2 {

            margin: 0 0 10px;

            color: #222222 !important;
        }


        .empty-search p {

            margin: 0;

            color: #777777 !important;
        }


        /* =================================================
           MOBILE
        ================================================== */

        @media (max-width: 700px) {

            .search-page {

                width: 94%;

                margin-top: 25px;
            }


            .search-header h1 {

                font-size: 28px;
            }


            .search-grid {

                grid-template-columns: 1fr;
            }


            .search-image {

                height: 200px;
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
     SEARCH PAGE
===================================================== -->

<main class="search-page">


<?php if ($search !== ''): ?>


    <!-- =================================================
         SEARCH HEADER
    ================================================== -->

    <div class="search-header">

        <h1>
            Search Results
        </h1>


        <p>

            Results for:

            <strong class="search-query">

                <?php

                echo htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    'UTF-8'
                );

                ?>

            </strong>

        </p>


        <div class="result-count">

            <?php

            echo $resultCount;

            ?>

            result<?php

            echo $resultCount == 1
                ? ''
                : 's';

            ?>

            found

        </div>

    </div>


    <!-- =================================================
         RESULTS
    ================================================== -->

    <?php if (
        $searchResult &&
        $resultCount > 0
    ): ?>


        <div class="search-grid">


        <?php while (
            $news = mysqli_fetch_assoc(
                $searchResult
            )
        ): ?>


            <!-- =========================================
                 SEARCH CARD
            ========================================== -->

            <article class="search-card">


                <!-- =====================================
                     IMAGE
                ====================================== -->
<div class="search-image">

    <?php if (!empty($news['image'])): ?>

        <?php
        $imagePath = trim($news['image']);

        // Full URL
        if (
            strpos($imagePath, 'http://') === 0 ||
            strpos($imagePath, 'https://') === 0
        ) {

            $finalImage = $imagePath;

        } else {

            // If DB contains uploads/news/...
            if (
                strpos($imagePath, 'uploads/') === 0
            ) {

                $finalImage = $imagePath;

            } else {

                $finalImage =
                    'uploads/news/' . $imagePath;
            }
        }
        ?>

        <img
            src="<?php echo htmlspecialchars($finalImage); ?>"
            alt="<?php echo htmlspecialchars($news['title']); ?>"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >

        <div
            class="default-news-image"
            style="display:none;"
        >
            📰
        </div>

    <?php else: ?>

        <div class="default-news-image">
            📰
        </div>

    <?php endif; ?>

</div>
                <!-- =====================================
                     CONTENT
                ====================================== -->

                <div class="search-content">


                    <!-- CATEGORY -->

                    <div class="search-category">

                        <?php

                        echo htmlspecialchars(
                            $news['category_name']
                            ?? 'News',
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </div>


                    <!-- TITLE -->

                    <h2 class="search-title">

                        <a
                            href="article.php?id=<?php
                                echo (int)$news['id'];
                            ?>"
                        >

                            <?php

                            echo htmlspecialchars(
                                $news['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                        </a>

                    </h2>


                    <!-- DESCRIPTION -->

                    <?php

                    $content = strip_tags(
                        $news['content'] ?? ''
                    );

                    $content = trim($content);

                    ?>


                    <?php if ($content !== ''): ?>

                        <p class="search-description">

                            <?php

                            echo htmlspecialchars(
                                mb_substr(
                                    $content,
                                    0,
                                    180
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>

                            <?php

                            if (
                                mb_strlen($content) > 180
                            ) {

                                echo '...';

                            }

                            ?>

                        </p>

                    <?php endif; ?>


                    <!-- META -->

                    <div class="search-meta">


                        <span>

                            👁

                            <?php

                            echo (int)(
                                $news['views'] ?? 0
                            );

                            ?>

                            views

                        </span>


                        <span>

                            •

                        </span>


                        <span>

                            <?php

                            if (
                                !empty(
                                    $news['created_at']
                                )
                            ) {

                                echo date(
                                    'd M Y',
                                    strtotime(
                                        $news['created_at']
                                    )
                                );

                            }

                            ?>

                        </span>


                    </div>


                    <!-- READ MORE -->

                    <a
                        href="article.php?id=<?php
                            echo (int)$news['id'];
                        ?>"
                        class="read-more"
                    >

                        Read Full Article →

                    </a>


                </div>


            </article>


        <?php endwhile; ?>


        </div>


    <?php else: ?>


        <!-- =================================================
             NO RESULTS
        ================================================== -->

        <div class="no-results">


            <div class="no-results-icon">

                🔍

            </div>


            <h2>

                No Results Found

            </h2>


            <p>

                We couldn't find any published
                news matching

                <strong>

                    "<?php

                    echo htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    ?>"

                </strong>

                <br>

                Try searching with another keyword.

            </p>


        </div>


    <?php endif; ?>


<?php else: ?>


    <!-- =================================================
         EMPTY SEARCH
    ================================================== -->

    <div class="empty-search">


        <div class="empty-search-icon">

            🔍

        </div>


        <h2>

            Search Daily News

        </h2>


        <p>

            Enter a keyword to find news.

        </p>


    </div>


<?php endif; ?>


</main>


<?php

/* =====================================================
   FOOTER
===================================================== */

include "includes/footer.php";

?>


</body>

</html>