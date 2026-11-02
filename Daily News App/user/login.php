<?php

session_start();

require_once "../config/database.php";


// =====================================
// IF ALREADY LOGGED IN
// =====================================

if (isset($_SESSION["user_id"])) {

    header("Location: ../index.php");

    exit;
}


// =====================================
// LOGIN
// =====================================

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    $password = $_POST["password"] ?? "";


    // =================================
    // VALIDATION
    // =================================

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {


        // =============================
        // GET USER
        // =============================

        $query = " SELECT
                id,
                name,
                email,
                password,
                status
            FROM users
            WHERE email = ?
            LIMIT 1
        ";


        $stmt = mysqli_prepare(
            $conn,
            $query
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $email
            );


            mysqli_stmt_execute(
                $stmt
            );


            $result =
                mysqli_stmt_get_result(
                    $stmt
                );


            $user =
                mysqli_fetch_assoc(
                    $result
                );


            mysqli_stmt_close(
                $stmt
            );


            // =========================
            // CHECK USER
            // =========================

            if (!$user) {

                $error =
                    "Invalid email or password.";

            } elseif (
                isset($user["status"]) &&
                $user["status"] !== "active"
            ) {

                $error =
                    "Your account is inactive.";

            } else {


                // =====================
                // PASSWORD CHECK
                // =====================

                if (
                    password_verify(
                        $password,
                        $user["password"]
                    )
                ) {


                    // =================
                    // SESSION
                    // =================

                    $_SESSION["user_id"] =
                        (int) $user["id"];

                    $_SESSION["user_name"] =
                        $user["name"];

                    $_SESSION["user_email"] =
                        $user["email"];


                    // =================
                    // REDIRECT
                    // =================

                    $redirect =
                        $_GET["redirect"]
                        ?? "../index.php";


                    header(
                        "Location: " .
                        $redirect
                    );

                    exit;


                } else {

                    $error =
                        "Invalid email or password.";

                }

            }

        } else {

            $error =
                "Database error. Please try again.";

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
        Login - Daily News
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #1b1b1b;

            font-family: Arial, sans-serif;

            color: white;

        }


        .login-container {

            width: 100%;

            max-width: 450px;

            padding: 20px;

        }


        .login-box {

            background: #252525;

            border: 1px solid #3d3d3d;

            border-radius: 12px;

            padding: 35px;

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.3);

        }


        .logo {

            text-align: center;

            font-size: 32px;

            font-weight: bold;

            margin-bottom: 10px;

        }


        .logo span {

            color: #ed1111;

        }


        .subtitle {

            text-align: center;

            color: #aaa;

            margin-bottom: 30px;

        }


        .error {

            background: #3a1d1d;

            border: 1px solid #ed1111;

            color: #ff6b6b;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

        }


        input {

            width: 100%;

            padding: 13px;

            margin-bottom: 20px;

            background: #1d1d1d;

            color: white;

            border: 1px solid #444;

            border-radius: 6px;

            font-size: 15px;

        }


        input:focus {

            outline: none;

            border-color: #ed1111;

        }


        button {

            width: 100%;

            padding: 13px;

            background: #ed1111;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

        }


        button:hover {

            background: #c90000;

        }


        .register-link {

            text-align: center;

            margin-top: 25px;

            color: #aaa;

        }


        .register-link a {

            color: #ff3333;

            text-decoration: none;

            font-weight: bold;

        }


        .back-home {

            display: block;

            text-align: center;

            margin-top: 20px;

            color: #aaa;

            text-decoration: none;

        }

    </style>

</head>


<body>


<div class="login-container">


    <div class="login-box">


        <div class="logo">

            Daily<span>News</span>

        </div>


        <div class="subtitle">

            Login to your account

        </div>


        <?php if ($error !== ""): ?>

            <div class="error">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >


            <label for="email">

                Email

            </label>


            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
                value="<?php
                    echo htmlspecialchars(
                        $_POST["email"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >


            <label for="password">

                Password

            </label>


            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >


            <button type="submit">

                Login

            </button>


        </form>


        <div class="register-link">

            Don't have an account?

            <a href="register.php">

                Register

            </a>

        </div>


        <a
            href="../index.php"
            class="back-home"
        >

            ← Back to Home

        </a>


    </div>


</div>


</body>

</html>