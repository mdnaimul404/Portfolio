<?php
include '../includes/db.php';

// Fetch all projects ordered by newest first
$result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Projects</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        /* Root colors and fonts */
        :root {
            --primary: #0052cc;
            --primary-dark: #003d99;
            --background-start: #a2d5f2;
            --background-end: #0077b6;
            --card-bg: #ffffff;
            --card-shadow: rgba(0, 82, 204, 0.15);
            --text-primary: #222;
            --text-secondary: #555;
            --link-hover: #002f66;
        }

        /* Reset and base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 50px 20px 120px;
            max-width: 960px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            background: linear-gradient(135deg, var(--background-start), var(--background-end));
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.5s ease;
        }

        h1 {
            color: var(--card-bg);
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            letter-spacing: 1.2px;
        }

        .projects-list {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .project-item {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px 35px;
            box-shadow: 0 8px 24px var(--card-shadow);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            cursor: default;
        }

        .project-item:hover {
            box-shadow: 0 16px 40px rgba(0, 82, 204, 0.3);
            transform: translateY(-8px);
        }

        .project-item h2 {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .project-item img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 82, 204, 0.2);
            transition: transform 0.4s ease;
            object-fit: cover;
        }

        .project-item img:hover {
            transform: scale(1.07);
        }

        .project-item p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            text-align: justify;
            line-height: 1.7;
        }

        .project-links a {
            margin-right: 20px;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            font-size: 1.05rem;
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .project-links a:hover {
            color: var(--link-hover);
            border-color: var(--link-hover);
        }

        hr {
            border: none;
            border-bottom: 1px solid #ccc;
            margin: 40px 0;
            opacity: 0.15;
        }

        /* Home button styling */
        .home-btn {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 82, 204, 0.4);
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            z-index: 100;
            user-select: none;
        }

        .home-btn:hover {
            background-color: var(--primary-dark);
            box-shadow: 0 12px 40px rgba(0, 61, 153, 0.6);
            transform: translateX(-50%) scale(1.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 40px 15px 120px;
            }
            h1 {
                font-size: 2.4rem;
            }
            .project-item {
                padding: 25px 20px;
            }
            .project-item h2 {
                font-size: 1.6rem;
            }
            .project-item p {
                font-size: 1rem;
            }
            .project-links a {
                font-size: 1rem;
                margin-right: 12px;
            }
            .home-btn {
                padding: 14px 32px;
                font-size: 1rem;
                bottom: 25px;
            }
        }
    </style>
</head>
<body>
    <h1>My Projects</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="projects-list">
            <?php while ($project = $result->fetch_assoc()): ?>
                <div class="project-item" tabindex="0" aria-label="Project: <?= htmlspecialchars($project['title']) ?>">
                    <h2><?= htmlspecialchars($project['title']) ?></h2>
                    <?php if (!empty($project['image'])): ?>
                        <img src="../<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                    <?php endif; ?>
                    <p><?= nl2br(htmlspecialchars(substr($project['description'], 0, 200))) ?>...</p>
                    <p class="project-links">
                        <?php if (!empty($project['project_link'])): ?>
                            <a href="<?= htmlspecialchars($project['project_link']) ?>" target="_blank" rel="noopener noreferrer">Visit Project</a>
                        <?php endif; ?>
                        <a href="project.php?id=<?= $project['id'] ?>">Read More</a>
                    </p>
                </div>
                <hr>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center; font-size:1.2rem; color: #ddd;">No projects found.</p>
    <?php endif; ?>

    <button class="home-btn" onclick="window.location.href='../index.php'">← Home</button>
</body>
</html>
