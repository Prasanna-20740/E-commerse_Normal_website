<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$error = "";
$success = "";


/* Get Categories */

$categoryQuery = " SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC";

$categoryResult = mysqli_query(
    $conn,
    $categoryQuery
);


/* Add Subcategory */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $categoryId = intval(
        $_POST["category_id"] ?? 0
    );

    $name = trim(
        $_POST["name"] ?? ""
    );

    $description = trim(
        $_POST["description"] ?? ""
    );


    if ($categoryId <= 0) {

        $error = "Please select a category.";

    } elseif (empty($name)) {

        $error = "Subcategory name is required.";

    } else {


        /* Create slug */

        $slug = strtolower($name);

        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $slug
        );

        $slug = trim(
            $slug,
            '-'
        );


        /* Check duplicate */

        $checkQuery = "
            SELECT id
            FROM subcategories
            WHERE category_id = ?
            AND (name = ? OR slug = ?)
            LIMIT 1
        ";

        $stmt = mysqli_prepare(
            $conn,
            $checkQuery
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $categoryId,
            $name,
            $slug
        );

        mysqli_stmt_execute($stmt);

        $checkResult =
            mysqli_stmt_get_result($stmt);


        if (
            mysqli_num_rows($checkResult) > 0
        ) {

            $error =
                "This subcategory already exists.";

        } else {


            /* Insert */

            $insertQuery = " INSERT INTO subcategories ( category_id,  name, slug, description,  status ) VALUES (?, ?, ?, ?, 'active')";

            $stmt = mysqli_prepare(
                $conn,
                $insertQuery
            );

            mysqli_stmt_bind_param(
                $stmt,
                "isss",
                $categoryId,
                $name,
                $slug,
                $description
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {

                $success =
                    "Subcategory added successfully.";

            } else {

                $error =
                    "Failed to add subcategory.";

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
        Add Subcategory - Daily News
    </title>

    <link
        rel="stylesheet"
        href="../assets/admin.css"
    >

    <style>

        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 750px;
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


<!-- SIDEBAR -->

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
            href="../categories/manage.php"
            class="menu-item"
        >
            📂 <span>Categories</span>
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            📁 <span>Subcategories</span>
        </a>


        <a
            href="../news/manage.php"
            class="menu-item"
        >
            📰 <span>News</span>
        </a>

    </nav>


    <a
        href="../logout.php"
        class="logout-button"
    >
        🚪 Logout
    </a>

</aside>


<!-- MAIN -->

<main class="admin-main">


    <header class="admin-header">

        <div>

            <h1>
                Add Subcategory
            </h1>

            <p>
                Create a subcategory under a category
            </p>

        </div>

    </header>


    <div class="form-card">


        <?php if (!empty($success)): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars($success);
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
        >


            <!-- CATEGORY -->

            <div class="form-group">

                <label>
                    Select Category
                </label>

                <select
                    name="category_id"
                    required
                >

                    <option value="">
                        -- Select Category --
                    </option>


                    <?php while (
                        $category =
                        mysqli_fetch_assoc(
                            $categoryResult
                        )
                    ): ?>

                        <option
                            value="<?php
                            echo $category["id"];
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $category["name"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- NAME -->

            <div class="form-group">

                <label>
                    Subcategory Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Example: Cricket"
                    required
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    placeholder="Enter subcategory description"
                ></textarea>

            </div>


            <button
                type="submit"
                class="submit-button"
            >
                Add Subcategory
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