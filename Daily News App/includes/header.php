<?php

/* =====================================
   START SESSION
===================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<style>
    :root {
    --primary: #ef1b23;
    --dark: #171717;
    --dark-card: #222222;
    --light: #ffffff;
    --light-bg: #f5f5f5;
    --muted: #777777;
    --text: #111111;
    --border: #333333;
    --radius: 12px;
    --transition: 0.25s ease;
    --font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
/* =====================================
   LOGIN / LOGOUT
===================================== */

.auth-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: var(--font-family);
    color: var(--light);
    background: var(--primary);
    transition: var(--transition);
    white-space: nowrap;
}


/* LOGIN */

.login-link {
    background: var(--primary);
    color: var(--light);
    border-radius: var(--radius);
    transition: var(--transition);
}

.login-link:hover {
    background: #c90000; /* keep a slightly darker hover */
    color: var(--light);
}


/* LOGOUT */

.logout-link {
    background: var(--dark);
    color: var(--light);
    border-radius: var(--radius);
    transition: var(--transition);
}

.logout-link:hover {
    background: var(--primary);
    color: var(--light);
}
</style>

<header class="site-header">



    <!-- =====================================
         MAIN HEADER
    ===================================== -->

    <div class="header-main">


        <!-- =====================================
             LOGO
        ===================================== -->

        <div class="logo">

            <div class="logo-icon">

                <span class="dot dot-1"></span>
                <span class="dot dot-2"></span>
                <span class="dot dot-3"></span>
                <span class="dot dot-4"></span>

            </div>

            <span class="logo-text">
                Daily<span>News</span>
            </span>

        </div>


        <!-- =====================================
             NAVIGATION
        ===================================== -->

        <nav class="main-nav">


            <!-- HOME -->

            <a
                href="index.php"
                class="nav-link "
            >
                Home
            </a>


            <!-- NEWS -->

            <a
                href="news.php"
                class="nav-link"
            >
                News
            </a>

           <a class="nav-link" href="/Daily%20News%20App/video.php">
             Video
            </a>


            <!-- TRENDING -->

            <a
                href="trending.php"
                class="nav-link"
            >
                Trending Topics
            </a>


            <!-- SPORTS -->

            <a
                href="sports.php"
                class="nav-link"
            >
                Sports
            </a>


            <!-- =====================================
                 MORE DROPDOWN
            ===================================== -->

            <div class="nav-dropdown">

                <button
                    class="more-button"
                    type="button"
                >
                    More

                    <span class="arrow">
                        ⌄
                    </span>

                </button>


                <div class="dropdown-menu">

                    <a href="live-tv.php">
                        Live TV
                    </a>

                    <a href="entertainment.php">
                        Movies & Entertainment
                    </a>

                    <a href="humour.php">
                        Humour
                    </a>

                    <a href="lifestyle.php">
                        Health & Lifestyle
                    </a>

                </div>

            </div>


        </nav>


        <!-- =====================================
             RIGHT SIDE
        ===================================== -->

        <div class="header-actions">


            <!-- =====================================
                 LOGIN / LOGOUT
            ===================================== -->

            <?php if (isset($_SESSION["user_id"])): ?>

                <!-- LOGGED IN -->

                <a
                    href="user/logout.php"
                    class="auth-link logout-link"
                >

                    👤

                    <?php
                    echo htmlspecialchars(
                        $_SESSION["user_name"] ?? "User",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>

                    <span>
                        Logout
                    </span>

                </a>


            <?php else: ?>

                <!-- NOT LOGGED IN -->

                <a
                    href="user/login.php"
                    class="auth-link login-link"
                >
                    🔐 Login
                </a>

            <?php endif; ?>




        </div>


    </div>

</header>