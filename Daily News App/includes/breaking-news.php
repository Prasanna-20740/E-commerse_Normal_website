<?php

require_once __DIR__ . "/../config/database.php";


/* =====================================================
   GET ACTIVE BREAKING NEWS
===================================================== */

$query = " SELECT id, title, link
    FROM breaking_news
    WHERE status = 'active'
    ORDER BY id DESC
    LIMIT 10
";

$result = mysqli_query($conn, $query);

?>

<?php if ($result && mysqli_num_rows($result) > 0): ?>

<section class="breaking-news">

    <div class="breaking-label">
        🔴 BREAKING NEWS
    </div>

    <div class="breaking-content">

        <?php while ($breaking = mysqli_fetch_assoc($result)): ?>

            <?php if (!empty($breaking["link"])): ?>

                <a
                    href="<?php echo htmlspecialchars($breaking["link"]); ?>"
                    class="breaking-item"
                >
                    <?php echo htmlspecialchars($breaking["title"]); ?>
                </a>

            <?php else: ?>

                <span class="breaking-item">
                    <?php echo htmlspecialchars($breaking["title"]); ?>
                </span>

            <?php endif; ?>

        <?php endwhile; ?>

    </div>

</section>

<?php endif; ?>
<style>/* =====================================================
   BREAKING NEWS
===================================================== */

.breaking-news {
    width: 100%;
    display: flex;
    align-items: center;
    background: #ffffff;
    border-bottom: 1px solid #eeeeee;
    min-height: 50px;
    overflow: hidden;
}


/* =====================================================
   LABEL
===================================================== */

.breaking-label {
    flex-shrink: 0;

    background: #ed1111;
    color: #ffffff;

    padding: 15px 22px;

    font-size: 14px;
    font-weight: 700;

    z-index: 2;
}


/* =====================================================
   CONTENT
===================================================== */

.breaking-content {
    display: flex;
    align-items: center;

    gap: 35px;

    padding: 0 20px;

    overflow-x: auto;

    white-space: nowrap;

    scrollbar-width: none;
}

.breaking-content::-webkit-scrollbar {
    display: none;
}


/* =====================================================
   ITEM
===================================================== */

.breaking-item {
    color: #222222;

    text-decoration: none;

    font-size: 14px;
    font-weight: 600;

    transition: color 0.2s ease;
}

.breaking-item:hover {
    color: #ed1111;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 700px) {

    .breaking-label {
        padding: 13px 15px;
        font-size: 12px;
    }

    .breaking-content {
        gap: 20px;
        padding: 0 12px;
    }

    .breaking-item {
        font-size: 13px;
    }
}</style>