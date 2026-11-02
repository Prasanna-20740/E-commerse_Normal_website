<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($name)) {

        $error = "Category name is required.";

    } else {

        // Create slug
        $slug = strtolower($name);

        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        $slug = trim($slug, '-');

        // Check existing category
        $checkQuery = "
            SELECT id
            FROM categories
            WHERE name = ?
               OR slug = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($conn, $checkQuery);

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $name,
            $slug
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "Category already exists.";

        } else {

            $insertQuery = " INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, 'active')";

            $stmt = mysqli_prepare(
                $conn,
                $insertQuery
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $slug,
                $description
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Category added successfully.";

            } else {

                $error = "Failed to add category.";

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

    <title>Add Category - Daily News</title>

    <link
        rel="stylesheet"
        href="../assets/admin.css"
    >

    <style>

        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            background: #e50909;
            color: #ffffff;
            cursor: pointer;
            font-weight: 600;
        }

        .back-button {
            display: inline-block;
            margin-left: 10px;
            padding: 12px 25px;
            border-radius: 6px;
            background: #333333;
            color: #ffffff;
            text-decoration: none;
        }

        .success-message {
            padding: 12px;
            margin-bottom: 20px;
            background: #e5f7e8;
            color: #16803c;
            border-radius: 6px;
        }

        .error-message {
            padding: 12px;
            margin-bottom: 20px;
            background: #ffe5e5;
            color: #c40000;
            border-radius: 6px;
        }

    </style>

</head>

<body class="admin-page">


<aside class="admin-sidebar">

    <div class="admin-brand">

        <span>
            Daily <b>News</b>
        </span>

    </div>


    <nav class="admin-menu">

        <a
            href="../dashboard.php"
            class="menu-item"
        >
            📊 <span>Dashboard</span>
        </a>

        <a
            href="manage.php"
            class="menu-item active"
        >
            📂 <span>Categories</span>
        </a>

        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰 <span>News</span>
        </a>

        <a
            href="../users/manage.php"
            class="menu-item"
        >
            👥 <span>Users</span>
        </a>

        <a
            href="../comments/manage.php"
            class="menu-item"
        >
            💬 <span>Comments</span>
        </a>

    </nav>


    <a
        href="../logout.php"
        class="logout-button"
    >
        🚪 Logout
    </a>

</aside>


<main class="admin-main">


    <header class="admin-header">

        <div>

            <h1>
                Add Category
            </h1>

            <p>
                Create a new news category
            </p>

        </div>

    </header>


    <div class="form-card">


        <?php if (!empty($message)): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >


            <div class="form-group">

                <label for="name">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Example: Sports"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter category description"
                ></textarea>

            </div>


            <button
                type="submit"
                class="submit-button"
            >
                Add Category
            </button>


            <a
                href="manage.php"
                class="back-button"
            >
                Back
            </a>


        </form>


    </div>


</main>


</body>

</html>