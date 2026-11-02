<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$error = "";
$success = "";


/* =========================
   GET NEWS ID
========================= */

$id = isset($_GET["id"])
    ? intval($_GET["id"])
    : 0;

if ($id <= 0) {

    die("Invalid news ID.");

}


/* =========================
   GET NEWS
========================= */

$query = " SELECT *
    FROM news
    WHERE id = ?
";

$stmt = mysqli_prepare(
    $conn,
    $query
);

if ($stmt === false) {

    die(
        "SQL Error: " .
        mysqli_error($conn)
    );

}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$news =
    mysqli_fetch_assoc($result);


if (!$news) {

    die("News not found.");

}


/* =========================
   GET CATEGORIES
========================= */

$categoryQuery = " SELECT id, name
    FROM categories
    WHERE status = 'active'
    ORDER BY name ASC
";

$categoryResult =
    mysqli_query(
        $conn,
        $categoryQuery
    );


/* =========================
   GET SUBCATEGORIES
========================= */

$subcategoryQuery = " SELECT id, category_id, name
    FROM subcategories
    WHERE status = 'active'
    ORDER BY name ASC
";

$subcategoryResult =
    mysqli_query(
        $conn,
        $subcategoryQuery
    );

$subcategories = [];

while (
    $row =
    mysqli_fetch_assoc(
        $subcategoryResult
    )
) {

    $subcategories[] = $row;

}


/* =========================
   UPDATE NEWS
========================= */

if (
    $_SERVER["REQUEST_METHOD"]
    === "POST"
) {

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
        $_POST["author"] ?? ""
    );

    $status =
        $_POST["status"] ?? "draft";

    $publishedAtInput =
        $_POST["published_at"] ?? "";


    /* =========================
       VALIDATION
    ========================= */

    if (empty($title)) {

        $error =
            "News title is required.";

    } elseif ($categoryId <= 0) {

        $error =
            "Please select a category.";

    } elseif (empty($content)) {

        $error =
            "News content is required.";

    } elseif (empty($author)) {

        $error =
            "Author name is required.";

    }


    /* =========================
       SLUG
    ========================= */

    if (empty($error)) {

        $slug =
            strtolower($title);

        $slug =
            preg_replace(
                '/[^a-z0-9]+/',
                '-',
                $slug
            );

        $slug =
            trim(
                $slug,
                '-'
            );

    }


    /* =========================
       OLD IMAGE
    ========================= */

    $oldImage =
        $news["image"];

    $imageName =
        $oldImage;


    /* =========================
       NEW IMAGE
    ========================= */

    if (
        empty($error) &&
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"]
        === UPLOAD_ERR_OK
    ) {

        $uploadDir =
            "../../uploads/news/";


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


        $extension =
            strtolower(
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

        } else {

            $imageName =
                time() .
                "_" .
                uniqid() .
                "." .
                $extension;


            $uploadSuccess =
                move_uploaded_file(
                    $tmpName,
                    $uploadDir .
                    $imageName
                );


            if (!$uploadSuccess) {

                $error =
                    "Image upload failed.";

            }

        }

    }


    /* =========================
       PUBLISHED DATE
    ========================= */

    $publishedAt = null;


    if (
        empty($error) &&
        $status === "published"
    ) {

        if (
            !empty(
                $publishedAtInput
            )
        ) {

            $timestamp =
                strtotime(
                    $publishedAtInput
                );


            if (
                $timestamp !== false
            ) {

                $publishedAt =
                    date(
                        "Y-m-d H:i:s",
                        $timestamp
                    );

            } else {

                $publishedAt =
                    date(
                        "Y-m-d H:i:s"
                    );

            }

        } else {

            $publishedAt =
                date(
                    "Y-m-d H:i:s"
                );

        }

    }


    /* =========================
       UPDATE DATABASE
    ========================= */

    if (empty($error)) {

        $updateQuery = " UPDATE news
            SET
                title = ?,
                slug = ?,
                description = ?,
                content = ?,
                image = ?,
                category_id = ?,
                subcategory_id = ?,
                author = ?,
                status = ?,
                published_at = ?
            WHERE id = ?
        ";


        $updateStmt =
            mysqli_prepare(
                $conn,
                $updateQuery
            );


        if (
            $updateStmt === false
        ) {

            die(
                "SQL Prepare Error: " .
                mysqli_error($conn)
            );

        }


        mysqli_stmt_bind_param(
            $updateStmt,
            "sssssiisssi",
            $title,
            $slug,
            $description,
            $content,
            $imageName,
            $categoryId,
            $subcategoryId,
            $author,
            $status,
            $publishedAt,
            $id
        );


        if (
            mysqli_stmt_execute(
                $updateStmt
            )
        ) {

            /*
             * Delete old image only
             * after successful update
             */

            if (
                !empty($oldImage) &&
                $imageName !== $oldImage
            ) {

                $oldImagePath =
                    "../../uploads/news/" .
                    $oldImage;


                if (
                    file_exists(
                        $oldImagePath
                    )
                ) {

                    unlink(
                        $oldImagePath
                    );

                }

            }


            $success = "News updated successfully.";


            /* Refresh displayed data */

            $refreshQuery = " SELECT *
                FROM news
                WHERE id = ?
            ";


            $refreshStmt =
                mysqli_prepare(
                    $conn,
                    $refreshQuery
                );


            mysqli_stmt_bind_param(
                $refreshStmt,
                "i",
                $id
            );

            mysqli_stmt_execute(
                $refreshStmt
            );


            $refreshResult =
                mysqli_stmt_get_result(
                    $refreshStmt
                );


            $news =
                mysqli_fetch_assoc(
                    $refreshResult
                );

        } else {

            $error =
                "Update Error: " .
                mysqli_stmt_error(
                    $updateStmt
                );

        }

    }

}


/* =========================
   FORMAT DATE FOR INPUT
========================= */

$publishValue = "";

if (
    !empty(
        $news["published_at"]
    )
) {

    $publishValue =
        date(
            "Y-m-d\TH:i",
            strtotime(
                $news["published_at"]
            )
        );

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
    Edit News - Daily News
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


.current-image {

    margin-top: 10px;

}


.current-image img {

    width: 220px;

    height: 140px;

    object-fit: cover;

    border-radius: 8px;

    display: block;

    margin-top: 8px;

}


.image-preview {

    width: 220px;

    height: 140px;

    object-fit: cover;

    border-radius: 8px;

    display: none;

    margin-top: 10px;

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


<!-- SIDEBAR -->

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


<!-- MAIN -->

<main class="admin-main">


<header class="admin-header">

    <div>

        <h1>
            Edit News
        </h1>

        <p>
            Update your news article
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
    value="<?php
    echo htmlspecialchars(
        $news["title"]
    );
    ?>"
    required
>

</div>


<!-- CATEGORY -->

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
        $news["category_id"]
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


<!-- SUBCATEGORY -->

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
    $subcategories as $sub
): ?>

<option
    value="<?php
    echo $sub["id"];
    ?>"
    data-category="<?php
    echo $sub["category_id"];
    ?>"
    <?php
    if (
        $news["subcategory_id"]
        == $sub["id"]
    ) {
        echo "selected";
    }
    ?>
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


<!-- DESCRIPTION -->

<div class="form-group">

<label>
    Short Description
</label>

<textarea
    name="description"
><?php
echo htmlspecialchars(
    $news["description"] ?? ""
);
?></textarea>

</div>


<!-- CONTENT -->

<div class="form-group">

<label>
    Full News Content
</label>

<textarea
    name="content"
    required
><?php
echo htmlspecialchars(
    $news["content"] ?? ""
);
?></textarea>

</div>


<!-- CURRENT IMAGE -->

<div class="form-group">

<label>
    Current Image
</label>


<div class="current-image">


<?php if (
    !empty($news["image"])
): ?>

<img
    src="../../uploads/news/<?php
    echo htmlspecialchars(
        $news["image"]
    );
    ?>"
    alt="Current News Image"
>

<?php else: ?>

<p>
    No image uploaded.
</p>

<?php endif; ?>


</div>

</div>


<!-- NEW IMAGE -->

<div class="form-group">

<label>
    Replace Image
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
    alt="New Image Preview"
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
        $news["author"] ?? ""
    );
    ?>"
    required
>

</div>


<div class="form-group">

<label>
    Status
</label>

<select
    name="status"
>

<option
    value="draft"
    <?php
    if (
        $news["status"]
        === "draft"
    ) {
        echo "selected";
    }
    ?>
>
    Draft
</option>


<option
    value="published"
    <?php
    if (
        $news["status"]
        === "published"
    ) {
        echo "selected";
    }
    ?>
>
    Published
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
    echo $publishValue;
    ?>"
>

</div>


<!-- BUTTON -->

<button
    type="submit"
    class="submit-button"
>

    💾 Update News

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

/* =========================
   CATEGORY FILTER
========================= */

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

            } else {

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


/* Run when page loads */

filterSubcategories();


/* =========================
   IMAGE PREVIEW
========================= */

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

</script>


</body>

</html>