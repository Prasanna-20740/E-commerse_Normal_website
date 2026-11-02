<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$error = "";
$success = "";


/* =====================================================
   GET CATEGORIES
===================================================== */

$categoryQuery = "
    SELECT id, name
    FROM categories
    WHERE status = 'active'
    ORDER BY name ASC
";

$categoryResult = mysqli_query(
    $conn,
    $categoryQuery
);


/* =====================================================
   GET SUBCATEGORIES
===================================================== */

$subcategoryQuery = "
    SELECT id, category_id, name
    FROM subcategories
    WHERE status = 'active'
    ORDER BY name ASC
";

$subcategoryResult = mysqli_query(
    $conn,
    $subcategoryQuery
);

$subcategories = [];

while ($row = mysqli_fetch_assoc($subcategoryResult)) {

    $subcategories[] = $row;

}


/* =====================================================
   ADD NEWS
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim(
        $_POST["title"] ?? ""
    );

    $description = trim(
        $_POST["description"] ?? ""
    );

    $content = trim(
        $_POST["content"] ?? ""
    );

    $categoryId = intval(
        $_POST["category_id"] ?? 0
    );

    $subcategoryId = intval(
        $_POST["subcategory_id"] ?? 0
    );

    $author = trim(
        $_POST["author"] ?? "Admin"
    );

    $status = $_POST["status"] ?? "published";

    $publishedAtInput =
        $_POST["published_at"] ?? "";


    /* =================================================
       VALIDATION
    ================================================= */

    if (empty($title)) {

        $error = "News title is required.";

    }

    elseif ($categoryId <= 0) {

        $error = "Please select a category.";

    }

    elseif (empty($content)) {

        $error = "Full news content is required.";

    }

    elseif (empty($author)) {

        $error = "Author name is required.";

    }


    /* =================================================
       CREATE SLUG
    ================================================= */

    if (empty($error)) {

        $slug = strtolower($title);

        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $slug
        );

        $slug = trim(
            $slug,
            '-'
        );

    }


    /* =================================================
       IMAGE UPLOAD
    ================================================= */

    $imageName = null;

    if (
        empty($error) &&
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] === UPLOAD_ERR_OK
    ) {

        $uploadDir =
            "../../uploads/news/";


        /* Create upload folder */

        if (!is_dir($uploadDir)) {

            mkdir(
                $uploadDir,
                0777,
                true
            );

        }


        $originalName =
            $_FILES["image"]["name"];

        $tmpName =
            $_FILES["image"]["tmp_name"];


        $extension = strtolower(
            pathinfo(
                $originalName,
                PATHINFO_EXTENSION
            )
        );


        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];


        if (
            !in_array(
                $extension,
                $allowedExtensions
            )
        ) {

            $error =
                "Only JPG, JPEG, PNG and WEBP images are allowed.";

        }

        else {

            /* Generate unique image name */

            $imageName =
                time()
                . "_"
                . uniqid()
                . "."
                . $extension;


            $uploadSuccess =
                move_uploaded_file(
                    $tmpName,
                    $uploadDir . $imageName
                );


            if (!$uploadSuccess) {

                $error =
                    "Image upload failed.";

            }

        }

    }


    /* =================================================
       PUBLISHED DATE
    ================================================= */

    $publishedAt = null;

    if (
        empty($error) &&
        $status === "published"
    ) {

        if (!empty($publishedAtInput)) {

            $timestamp =
                strtotime(
                    $publishedAtInput
                );


            if ($timestamp !== false) {

                $publishedAt =
                    date(
                        "Y-m-d H:i:s",
                        $timestamp
                    );

            }

            else {

                $publishedAt =
                    date(
                        "Y-m-d H:i:s"
                    );

            }

        }

        else {

            $publishedAt =
                date(
                    "Y-m-d H:i:s"
                );

        }

    }


    /* =================================================
       INSERT NEWS
    ================================================= */

    if (empty($error)) {

        /*
         * author_id is NULL for now.
         * Author name is stored in author column.
         */

        $authorId = null;


        $insertQuery = "

            INSERT INTO news
            (
                title,
                slug,
                description,
                content,
                image,
                category_id,
                subcategory_id,
                author_id,
                author,
                status,
                published_at,
                views
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                0
            )

        ";


        $stmt = mysqli_prepare(
            $conn,
            $insertQuery
        );


        if ($stmt === false) {

            $error =
                "SQL Prepare Error: "
                . mysqli_error($conn);

        }

        else {

            /*
             * s = string
             * i = integer
             *
             * title          s
             * slug           s
             * description    s
             * content        s
             * image          s
             * category_id    i
             * subcategory_id i
             * author_id      i
             * author         s
             * status         s
             * published_at   s
             */

            mysqli_stmt_bind_param(
                $stmt,
                "sssssiiisss",
                $title,
                $slug,
                $description,
                $content,
                $imageName,
                $categoryId,
                $subcategoryId,
                $authorId,
                $author,
                $status,
                $publishedAt
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {

                $success =
                    "News published successfully.";

            }

            else {

                $error =
                    "Insert Error: "
                    . mysqli_stmt_error($stmt);

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
        Add News - Daily News
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

            max-width: 950px;

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

            min-height: 140px;

            resize: vertical;

        }


        .form-row {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;

        }


        .image-preview {

            margin-top: 10px;

            max-width: 300px;

            max-height: 200px;

            display: none;

            border-radius: 8px;

        }


        .submit-button {

            padding: 13px 25px;

            border: none;

            border-radius: 6px;

            background: #e50909;

            color: #ffffff;

            cursor: pointer;

            font-weight: 600;

            font-size: 15px;

        }


        .submit-button:hover {

            background: #c90000;

        }


        .back-button {

            display: inline-block;

            margin-left: 10px;

            padding: 13px 25px;

            border-radius: 6px;

            background: #333333;

            color: #ffffff;

            text-decoration: none;

        }


        .success-message {

            padding: 14px;

            margin-bottom: 20px;

            background: #e5f7e8;

            color: #16803c;

            border-radius: 6px;

        }


        .error-message {

            padding: 14px;

            margin-bottom: 20px;

            background: #ffe5e5;

            color: #c40000;

            border-radius: 6px;

        }


        @media (max-width: 700px) {

            .form-row {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body class="admin-page">


<!-- =================================================
     SIDEBAR
================================================= -->

<aside class="admin-sidebar">


    <div class="admin-brand">

        Daily <b>News</b>

    </div>


    <nav class="admin-menu">


        <a
            href="../dashboard.php"
            class="menu-item"
        >
            📊 Dashboard
        </a>


        <a
            href="../categories/manage.php"
            class="menu-item"
        >
            📂 Categories
        </a>


        <a
            href="../subcategories/manage.php"
            class="menu-item"
        >
            📁 Subcategories
        </a>


        <a
            href="manage.php"
            class="menu-item active"
        >
            📰 News
        </a>


    </nav>


    <a
        href="../logout.php"
        class="logout-button"
    >
        🚪 Logout
    </a>


</aside>


<!-- =================================================
     MAIN CONTENT
================================================= -->

<main class="admin-main">


    <header class="admin-header">

        <div>

            <h1>
                Add News
            </h1>

            <p>
                Publish a new news article
            </p>

        </div>

    </header>


    <div class="form-card">


        <!-- SUCCESS -->

        <?php if (!empty($success)): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars(
                    $success
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- ERROR -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars(
                    $error
                );
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- TITLE -->

            <div class="form-group">

                <label>
                    News Title
                </label>

                <input
                    type="text"
                    name="title"
                    placeholder="Enter news title"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["title"] ?? ""
                        );
                    ?>"
                    required
                >

            </div>


            <!-- CATEGORY + SUBCATEGORY -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select
                        name="category_id"
                        id="category"
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
                                <?php

                                if (
                                    isset(
                                        $_POST["category_id"]
                                    ) &&
                                    $_POST["category_id"]
                                    == $category["id"]
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
                        Subcategory
                    </label>

                    <select
                        name="subcategory_id"
                        id="subcategory"
                    >

                        <option value="">
                            -- Select Subcategory --
                        </option>


                        <?php foreach (
                            $subcategories
                            as $sub
                        ): ?>

                            <option
                                value="<?php
                                    echo $sub["id"];
                                ?>"
                                data-category="<?php
                                    echo $sub["category_id"];
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $sub["name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


            </div>


            <!-- SHORT DESCRIPTION -->

            <div class="form-group">

                <label>
                    Short Description
                </label>

                <textarea
                    name="description"
                    placeholder="Enter short news description"
                ><?php
                    echo htmlspecialchars(
                        $_POST["description"] ?? ""
                    );
                ?></textarea>

            </div>


            <!-- FULL CONTENT -->

            <div class="form-group">

                <label>
                    Full News Content
                </label>

                <textarea
                    name="content"
                    placeholder="Write complete news article..."
                    required
                ><?php
                    echo htmlspecialchars(
                        $_POST["content"] ?? ""
                    );
                ?></textarea>

            </div>


            <!-- IMAGE -->

            <div class="form-group">

                <label>
                    News Image
                </label>

                <input
                    type="file"
                    name="image"
                    id="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >


                <img
                    id="imagePreview"
                    class="image-preview"
                    alt="Image Preview"
                >

            </div>


            <!-- AUTHOR + STATUS -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Author
                    </label>

                    <input
                        type="text"
                        name="author"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["author"] ?? ""
                            );
                        ?>"
                        placeholder="Enter author name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Status
                    </label>

                    <select
                        name="status"
                        id="status"
                    >

                        <option
                            value="published"
                            selected
                        >
                            Published
                        </option>

                        <option value="draft">
                            Draft
                        </option>

                    </select>

                </div>


            </div>


            <!-- PUBLISH DATE -->

            <div class="form-group">

                <label>
                    Publish Date & Time
                </label>

                <input
                    type="datetime-local"
                    name="published_at"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["published_at"]
                            ?? date("Y-m-d\TH:i")
                        );
                    ?>"
                >

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="submit-button"
            >

                📰 Publish News

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


<script>

/* =====================================================
   CATEGORY → SUBCATEGORY
===================================================== */

const category =
    document.getElementById(
        "category"
    );

const subcategory =
    document.getElementById(
        "subcategory"
    );


function filterSubcategories() {

    const categoryId =
        category.value;


    const options =
        subcategory.querySelectorAll(
            "option"
        );


    options.forEach(
        function(option) {

            if (
                !option.dataset.category
            ) {

                option.style.display =
                    "";

                return;

            }


            if (
                option.dataset.category
                === categoryId
            ) {

                option.style.display =
                    "";

            }

            else {

                option.style.display =
                    "none";

            }

        }
    );


}


category.addEventListener(
    "change",
    filterSubcategories
);


/* =====================================================
   IMAGE PREVIEW
===================================================== */

const imageInput =
    document.getElementById(
        "image"
    );

const imagePreview =
    document.getElementById(
        "imagePreview"
    );


imageInput.addEventListener(
    "change",
    function() {

        const file =
            this.files[0];


        if (!file) {

            imagePreview.style.display =
                "none";

            return;

        }


        const reader =
            new FileReader();


        reader.onload =
            function(event) {

                imagePreview.src =
                    event.target.result;

                imagePreview.style.display =
                    "block";

            };


        reader.readAsDataURL(file);

    }
);


/* Run once when page loads */

filterSubcategories();

</script>


</body>

</html>