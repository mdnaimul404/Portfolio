<?php
include '../includes/db.php';

// Fetch all blogs ordered newest first
$result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Blogs</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
            color: #2c3e50;
            max-width: 960px;
            margin: 40px auto;
            padding: 0 20px 100px; /* Extra bottom padding for fixed button */
            line-height: 1.6;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #3498db;
            margin-bottom: 40px;
            text-align: center;
            letter-spacing: 1.2px;
        }

        /* Removed previous home-link styling from top */

        .blog-list {
            display: flex;
            flex-direction: column;
            gap: 35px;
        }

        .blog-item {
            background: #fff;
            border-radius: 15px;
            padding: 25px 30px;
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.15);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            cursor: default;
        }

        .blog-item:hover {
            box-shadow: 0 15px 40px rgba(41, 128, 185, 0.25);
            transform: translateY(-5px);
        }

        .blog-item h2 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 15px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .blog-item h2:hover {
            color: #2980b9;
        }

        .blog-item img {
            width: 100%;
            max-width: 320px;
            height: auto;
            float: right;
            margin-left: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(41, 128, 185, 0.3);
            transition: transform 0.3s ease;
        }

        .blog-item img:hover {
            transform: scale(1.05);
        }

        .blog-item p {
            font-size: 1.1rem;
            color: #34495e;
            text-align: justify;
        }

        .blog-item .read-more {
            display: inline-block;
            margin-top: 15px;
            font-weight: 600;
            color: #3498db;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s ease, color 0.3s ease;
        }

        .blog-item .read-more:hover {
            color: #2980b9;
            border-color: #2980b9;
        }

        hr {
            border: none;
            border-bottom: 1px solid #ddd;
            margin: 40px 0;
        }

        /* Fixed Home Button */
        .home-button {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #3498db;
            color: white;
            padding: 14px 36px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            z-index: 1000;
        }

        .home-button:hover {
            background-color: #2980b9;
            box-shadow: 0 12px 30px rgba(41, 128, 185, 0.6);
        }

        @media (max-width: 768px) {
            body {
                margin: 20px auto;
                padding: 0 15px 80px;
            }
            h1 {
                font-size: 2.2rem;
            }
            .blog-item h2 {
                font-size: 1.5rem;
            }
            .blog-item img {
                float: none;
                display: block;
                margin: 0 0 15px 0;
                max-width: 100%;
            }
            .blog-item {
                padding: 20px 20px;
            }
            .home-button {
                padding: 12px 28px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <h1>My Blogs</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="blog-list">
            <?php while ($blog = $result->fetch_assoc()): ?>
                <div class="blog-item">
                    <h2><?= htmlspecialchars($blog['title']) ?></h2>
                    <?php if ($blog['image']): ?>
                        <img src="../<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                    <?php endif; ?>
                    <p><?= nl2br(htmlspecialchars(substr($blog['content'], 0, 200))) ?>...</p>
                    <a class="read-more" href="blog.php?id=<?= $blog['id'] ?>">Read More</a>
                </div>
                <hr>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No blogs found.</p>
    <?php endif; ?>

    <a href="../index.php" class="home-button">← Home</a>
</body>
</html>
