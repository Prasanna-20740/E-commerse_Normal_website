<?php

/* =====================================================
   ADMIN LOGIN
===================================================== */

session_start();

require_once "../config/database.php";


/* =====================================================
   IF ALREADY LOGGED IN
===================================================== */

if (isset($_SESSION["admin_id"])) {

    header("Location: dashboard.php");

    exit;
}


/* =====================================================
   ERROR
===================================================== */

$error = "";


/* =====================================================
   LOGIN PROCESS
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    /* =================================================
       VALIDATION
    ================================================= */

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } else {


        /* =============================================
           FIND ADMIN
        ============================================= */

        $query = " SELECT
                id,
                name,
                email,
                password,
                role,
                status
            FROM admins
            WHERE email = ?
            LIMIT 1
        ";


        $stmt = mysqli_prepare($conn, $query);


        if (!$stmt) {

            $error = "Something went wrong. Please try again.";

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $email
            );


            mysqli_stmt_execute($stmt);


            $result = mysqli_stmt_get_result($stmt);


            /* =========================================
               CHECK ADMIN
            ========================================= */

            if (mysqli_num_rows($result) === 1) {

                $admin = mysqli_fetch_assoc($result);


                /* =====================================
                   CHECK STATUS
                ===================================== */

                if ($admin["status"] !== "active") {

                    $error = "Your admin account is inactive.";

                }


                /* =====================================
                   VERIFY PASSWORD
                ===================================== */

                elseif (
                    password_verify(
                        $password,
                        $admin["password"]
                    )
                ) {


                    /* =================================
                       SESSION FIXATION PROTECTION
                    ================================= */

                    session_regenerate_id(true);


                    /* =================================
                       STORE ADMIN SESSION
                    ================================= */

                    $_SESSION["admin_id"] =
                        $admin["id"];

                    $_SESSION["admin_name"] =
                        $admin["name"];

                    $_SESSION["admin_email"] =
                        $admin["email"];

                    $_SESSION["admin_role"] =
                        $admin["role"];

                    $_SESSION["session_created"] =
                        time();


                    /* =================================
                       REDIRECT
                    ================================= */

                    header(
                        "Location: dashboard.php"
                    );

                    exit;

                } else {

                    $error =
                        "Invalid email or password.";
                }


            } else {

                $error =
                    "Invalid email or password.";
            }


            mysqli_stmt_close($stmt);
        }
    }
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
        Admin Login - Daily News
    </title>


    <style>

        /* =====================================================
           GLOBAL RESET
        ===================================================== */

        * {

            margin: 0;
            padding: 0;

            box-sizing: border-box;
        }


        /* =====================================================
           BODY
        ===================================================== */

        body {

             margin: 0;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #e4e4e4;

            font-family: Arial, sans-serif;

            color: white;

            padding: 30px;
            
        }


        /* =====================================================
           LOGIN CONTAINER
        ===================================================== */

        .login-container {

            width: 100%;

            max-width: 450px;

            padding: 20px;

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .login-logo {

            text-align: center;

            margin-bottom: 25px;
        }


        .login-logo h1 {

            margin-top: 12px;

            font-size: 32px;

            font-weight: 700;

            color: #000000;
        }


        .login-logo h1 span {

            color: #ed1111;
        }


        /* =====================================================
           LOGO ICON
        ===================================================== */

        .admin-logo-icon {

            width: 55px;

            height: 55px;

            margin: 0 auto;

            background: #ed1111;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 4px;

            padding: 10px;
        }


        .admin-logo-icon span {

            display: block;

            width: 6px;

            background: #ffffff;

            border-radius: 2px;
        }


        .admin-logo-icon span:nth-child(1) {

            height: 20px;
        }


        .admin-logo-icon span:nth-child(2) {

            height: 30px;
        }


        .admin-logo-icon span:nth-child(3) {

            height: 24px;
        }


        .admin-logo-icon span:nth-child(4) {

            height: 34px;
        }


        /* =====================================================
           LOGIN CARD
        ===================================================== */

        .login-card {

            background: #ffffff;

            border-radius: 12px;

            padding: 35px;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.08);

            border: 1px solid #eeeeee;
        }


        /* =====================================================
           TITLE
        ===================================================== */

        .login-card h2 {

            text-align: center;

            font-size: 27px;

            margin-bottom: 8px;

            color: #111111;
        }


        /* =====================================================
           SUBTITLE
        ===================================================== */

        .login-subtitle {

            text-align: center;

            color: #687384;

            font-size: 14px;

            margin-bottom: 28px;

            line-height: 1.5;
        }


        /* =====================================================
           ERROR MESSAGE
        ===================================================== */

        .error-message {

            background: #ffe5e5;

            color: #c40000;

            border: 1px solid #ffcccc;

            padding: 12px 14px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-size: 14px;

            line-height: 1.4;
        }


        /* =====================================================
           FORM GROUP
        ===================================================== */

        .form-group {

            margin-bottom: 20px;
        }


        /* =====================================================
           LABEL
        ===================================================== */

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 600;

            color: #222222;
        }


        /* =====================================================
           INPUT
        ===================================================== */

        .form-group input {

            width: 100%;

            height: 48px;

            padding: 0 14px;

            border: 1px solid #d7d7d7;

            border-radius: 7px;

            background: #ffffff;

            color: #111111;

            font-size: 14px;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        /* =====================================================
           INPUT FOCUS
        ===================================================== */

        .form-group input:focus {

            border-color: #ed1111;

            box-shadow:
                0 0 0 3px
                rgba(237, 17, 17, 0.10);
        }


        /* =====================================================
           PLACEHOLDER
        ===================================================== */

        .form-group input::placeholder {

            color: #999999;
        }


        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .login-button {

            width: 100%;

            height: 48px;

            border: none;

            border-radius: 7px;

            background: #ed1111;

            color: #ffffff;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }


        /* =====================================================
           BUTTON HOVER
        ===================================================== */

        .login-button:hover {

            background: #c90000;

            transform: translateY(-1px);
        }


        /* =====================================================
           BUTTON ACTIVE
        ===================================================== */

        .login-button:active {

            transform: translateY(0);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 600px) {

            body {

                padding: 20px;
            }


            .login-card {

                padding: 25px 20px;
            }


            .login-logo h1 {

                font-size: 28px;
            }


            .login-card h2 {

                font-size: 24px;
            }

        }

    </style>

</head>


<body class="login-page">


<div class="login-container">


    <!-- =================================================
         LOGO
    ================================================= -->

    <div class="login-logo">


        <div class="admin-logo-icon">

            <span></span>

            <span></span>

            <span></span>

            <span></span>

        </div>


        <h1>

            Daily <span>News</span>

        </h1>


    </div>


    <!-- =================================================
         LOGIN CARD
    ================================================= -->

    <div class="login-card">


        <h2>
            Admin Login
        </h2>


        <p class="login-subtitle">

            Login to manage your news website

        </p>


        <!-- =============================================
             ERROR
        ============================================== -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php

                echo htmlspecialchars(
                    $error
                );

                ?>

            </div>

        <?php endif; ?>


        <!-- =============================================
             LOGIN FORM
        ============================================== -->

        <form
            method="POST"
            action=""
        >


            <!-- EMAIL -->

            <div class="form-group">


                <label for="email">

                    Email Address

                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["email"] ?? ""
                        );
                    ?>"
                    autocomplete="email"
                    required
                >


            </div>


            <!-- PASSWORD -->

            <div class="form-group">


                <label for="password">

                    Password

                </label>


                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    autocomplete="current-password"
                    required
                >


            </div>


            <!-- LOGIN BUTTON -->

            <button
                type="submit"
                class="login-button"
            >

                Login

            </button>


        </form>


    </div>


</div>


</body>

</html>