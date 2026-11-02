<?php

session_start();

require_once "../config/database.php";

$error = "";
$success = "";

$name = "";
$email = "";


// =====================================
// REGISTER
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // =================================
    // VALIDATION
    // =================================

    if (
        $name === "" ||
        $email === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $error = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {


        // =============================
        // CHECK EXISTING EMAIL
        // =============================

        $checkQuery = "
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $checkStmt = mysqli_prepare(
            $conn,
            $checkQuery
        );


        if (!$checkStmt) {

            $error = "Database error.";

        } else {

            mysqli_stmt_bind_param(
                $checkStmt,
                "s",
                $email
            );

            mysqli_stmt_execute(
                $checkStmt
            );

            $checkResult =
                mysqli_stmt_get_result(
                    $checkStmt
                );

            $existingUser =
                mysqli_fetch_assoc(
                    $checkResult
                );

            mysqli_stmt_close(
                $checkStmt
            );


            if ($existingUser) {

                $error =
                    "Email already registered.";

            } else {


                // =========================
                // HASH PASSWORD
                // =========================

                $hashedPassword =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                // =========================
                // INSERT USER
                // =========================

                $insertQuery = "
                    INSERT INTO users
                    (
                        name,
                        email,
                        password,
                        status,
                        created_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        'active',
                        NOW()
                    )
                ";


                $insertStmt =
                    mysqli_prepare(
                        $conn,
                        $insertQuery
                    );


                if (!$insertStmt) {

                    $error =
                        "Unable to create account.";

                } else {

                    mysqli_stmt_bind_param(
                        $insertStmt,
                        "sss",
                        $name,
                        $email,
                        $hashedPassword
                    );


                    if (
                        mysqli_stmt_execute(
                            $insertStmt
                        )
                    ) {

                        mysqli_stmt_close(
                            $insertStmt
                        );


                        header(
                            "Location: login.php?registered=1"
                        );

                        exit;

                    } else {

                        $error =
                            "Registration failed. Please try again.";

                        mysqli_stmt_close(
                            $insertStmt
                        );

                    }

                }

            }

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
        Register - Daily News
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


        .register-container {

            width: 100%;

            max-width: 480px;

            padding: 20px;

        }


        .register-box {

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


        .login-link {

            text-align: center;

            margin-top: 25px;

            color: #aaa;

        }


        .login-link a {

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


<div class="register-container">


    <div class="register-box">


        <div class="logo">

            Daily<span>News</span>

        </div>


        <div class="subtitle">

            Create your account

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


            <!-- NAME -->

            <label for="name">

                Full Name

            </label>


            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                required
                value="<?php
                    echo htmlspecialchars(
                        $name,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >


            <!-- EMAIL -->

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
                        $email,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >


            <!-- PASSWORD -->

            <label for="password">

                Password

            </label>


            <input
                type="password"
                id="password"
                name="password"
                placeholder="Minimum 6 characters"
                required
            >


            <!-- CONFIRM PASSWORD -->

            <label for="confirm_password">

                Confirm Password

            </label>


            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Re-enter your password"
                required
            >


            <button type="submit">

                Create Account

            </button>


        </form>


        <div class="login-link">

            Already have an account?

            <a href="login.php">

                Login

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