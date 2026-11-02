<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: manage.php");
    exit;
}


/* Get subcategory */

$query = " SELECT * FROM subcategories WHERE id = ?";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) === 0) {
    header("Location: manage.php");
    exit;
}

$subcategory =
    mysqli_fetch_assoc($result);


/* Get categories */

$categoryQuery = "
    SELECT id, name
    FROM categories
    WHERE status = 'active'
    ORDER BY name ASC
";

$categoryResult =
    mysqli_query(
        $conn,
        $categoryQuery
    );


$error = "";
$success = "";


/* Update */

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

    $status = $_POST["status"] ?? "active";


    if ($categoryId <= 0) {

        $error = "Please select a category.";

    } elseif (empty($name)) {

        $error =
            "Subcategory name is required.";

    } else {

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


        $updateQuery = "
            UPDATE subcategories
            SET
                category_id = ?,
                name = ?,
                slug = ?,
                description = ?,
                status = ?
            WHERE id = ?
        ";

        $stmt = mysqli_prepare(
            $conn,
            $updateQuery
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssi",
            $categoryId,
            $name,
            $slug,
            $description,
            $status,
            $id
        );


        if (
            mysqli_stmt_execute($stmt)
        ) {

            $success =
                "Subcategory updated successfully.";

            $subcategory["category_id"] =
                $categoryId;

            $subcategory["name"] =
                $name;

            $subcategory["slug"] =
                $slug;

            $subcategory["description"] =
                $description;

            $subcategory["status"] =
                $status;

        } else {

            $error =
                "Failed to update subcategory.";

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
        Edit Subcategory - Daily News
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
        }

        .submit-button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
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


<main class="admin-main">


    <header class="admin-header">

        <div>

            <h1>
                Edit Subcategory
            </h1>

            <p>
                Update subcategory information
            </p>

        </div>

    </header>


    <div class="form-card">


        <?php if (!empty($success)): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars(
                    $success
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars(
                    $error
                );
                ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label>
                    Category
                </label>

                <select
                    name="category_id"
                    required
                >

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
                            <?php
                            if (
                                $category["id"]
                                ==
                                $subcategory[
                                    "category_id"
                                ]
                            ) {
                                echo "selected";
                            }
                            ?>
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


            <div class="form-group">

                <label>
                    Subcategory Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php
                    echo htmlspecialchars(
                        $subcategory["name"]
                    );
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                ><?php
                echo htmlspecialchars(
                    $subcategory["description"]
                );
                ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    Status
                </label>

                <select name="status">

                    <option
                        value="active"
                        <?php
                        if (
                            $subcategory["status"]
                            === "active"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Active
                    </option>

                    <option
                        value="inactive"
                        <?php
                        if (
                            $subcategory["status"]
                            === "inactive"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="submit-button"
            >
                Update Subcategory
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