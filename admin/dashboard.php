<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        /* --- Base styles --- */
        body {
            font-family: Arial, sans-serif;
            background: #090a0f;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            position: relative;
            color: white;
            overflow: hidden;
        }
        /* Dashboard container */
        .dashboard {
            position: relative;
            background: rgba(20, 24, 40, 0.9);
            padding: 40px 50px;
            border-radius: 20px;
            max-width: 960px;
            width: 100%;
            box-shadow: 0 0 50px rgba(74, 144, 226, 0.7);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 30px 20px;
            text-align: center;
            margin-bottom: 80px;
            z-index: 2;
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
        }
        .dashboard h1 {
            grid-column: span 3;
            margin-bottom: 30px;
            font-weight: 900;
            font-size: 2.8rem;
            color: #a0c8ff;
            text-shadow:
                0 0 8px #a0c8ff,
                0 0 15px #6990ff,
                0 0 25px #4267b2;
            user-select: none;
        }
        .dashboard a {
            background: linear-gradient(135deg, #3a86ff, #6990ff);
            color: #fff;
            font-size: 1.1rem;
            width: 8cm;    /* fixed width */
            height: 2cm;   /* fixed height */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            box-shadow:
                0 5px 15px rgba(58, 134, 255, 0.6),
                0 0 15px rgba(105, 144, 255, 0.7);
            transition:
                background-color 0.3s ease,
                box-shadow 0.4s ease,
                transform 0.2s ease;
            margin: 0 auto;
            user-select: none;
        }
        .dashboard a:hover {
            background: linear-gradient(135deg, #1a53a0, #3a68ff);
            box-shadow:
                0 8px 25px rgba(26, 83, 160, 0.9),
                0 0 30px rgba(58, 134, 255, 0.9);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                max-width: 100%;
                padding: 25px 20px;
                grid-template-columns: repeat(2, 1fr);
                grid-gap: 18px 15px;
            }
            .dashboard h1 {
                grid-column: span 2;
                font-size: 2rem;
            }
            .dashboard a {
                width: 90%;
                height: 2.2cm;
                font-size: 1rem;
            }
        }
        @media (max-width: 480px) {
            .dashboard {
                grid-template-columns: 1fr;
                grid-gap: 12px;
            }
            .dashboard h1 {
                grid-column: auto;
                font-size: 1.6rem;
            }
            .dashboard a {
                width: 100%;
                height: 2.4cm;
                font-size: 0.9rem;
            }
        }

        /* Fixed Back to Home Button */
        .back-home-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            z-index: 999;
            width: 180px;
            text-align: center;
            user-select: none;
        }
        .back-home-btn:hover {
            background-color: #c0392b;
            box-shadow: 0 8px 20px rgba(192, 57, 43, 0.7);
        }

        /* Canvas placed below dashboard */
        canvas#stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            display: block;
            background: transparent;
        }
    </style>
</head>
<body>

<canvas id="stars"></canvas>

<div class="dashboard" role="main" aria-label="Admin Dashboard">
    <h1>Welcome, MD. NAIMUL ISLAM</h1>

    <a href="manage-projects.php" title="Manage Projects">📁 Manage Projects</a>
    <a href="manage-blogs.php" title="Manage Blogs">📝 Manage Blogs</a>
    <a href="manage-messages.php" title="View Contact Messages">📬 View Contact Messages</a>

    <a href="manage-resume.php" title="Manage Resume & Certificates">📄 Manage Resume & Certificates</a>
    <a href="manage_comments.php" title="Manage Comments">💬 Manage Comments</a>
    <a href="image_upload.php" title="Manage Images">🖼️ Manage Images</a>

    <a href="login.php" title="Logout">🔒 Logout</a>
</div>

<button class="back-home-btn" onclick="window.location.href='../index.php';" aria-label="Back to Home">
    ← Back to Home
</button>

<script>
    const canvas = document.getElementById('stars');
    const ctx = canvas.getContext('2d');
    let width, height;

    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    }
    resize();
    window.addEventListener('resize', resize);

    // Stars
    let stars = [];
    const numStars = 150;
    for(let i=0; i<numStars; i++){
        stars.push({
            x: Math.random()*width,
            y: Math.random()*height,
            radius: Math.random()*1.5 + 0.5,
            velocityX: (Math.random()-0.5)*0.3,
            velocityY: (Math.random()-0.5)*0.3,
            blasting: false,
            blastParticles: []
        });
    }

    // Planets with satellites
    let planets = [
        {x: Math.random()*width, y: Math.random()*height, radius: 80, color: '#3a86ff', velocityX: (Math.random()-0.5)*0.2, velocityY: (Math.random()-0.5)*0.2, satellites: []},
        {x: Math.random()*width, y: Math.random()*height, radius: 50, color: '#ff006e', velocityX: (Math.random()-0.5)*0.2, velocityY: (Math.random()-0.5)*0.2, satellites: []},
        {x: Math.random()*width, y: Math.random()*height, radius: 65, color: '#ffbe0b', velocityX: (Math.random()-0.5)*0.2, velocityY: (Math.random()-0.5)*0.2, satellites: []}
    ];

    function createSatellite(planet){
        return {
            orbitRadius: planet.radius + 30 + Math.random()*40,
            orbitAngle: Math.random()*Math.PI*2,
            orbitSpeed: 0.004 + Math.random()*0.015,
            radius: 10 + Math.random()*6,
            color: `hsl(${Math.random()*360}, 100%, 70%)`,
            crashed: false,
            crashTime: 0,
            x: 0,
            y: 0
        };
    }
    planets.forEach(planet => {
        let satCount = Math.floor(Math.random()*2)+1;
        for(let i=0; i<satCount; i++){
            planet.satellites.push(createSatellite(planet));
        }
    });

    // Satellite explosion particles
    let satelliteParticles = [];
    const particleCount = 30;
    function createSatelliteParticles(x,y){
        satelliteParticles.push([]);
        let particles = [];
        for(let i=0; i<particleCount; i++){
            particles.push({
                x: x,
                y: y,
                radius: Math.random()*3+2,
                color: 'rgba(255, 69, 0, 1)',
                velocityX: (Math.random()-0.5)*6,
                velocityY: (Math.random()-0.5)*6,
                life: 30
            });
        }
        satelliteParticles[satelliteParticles.length-1] = particles;
    }
    function drawSatelliteParticles(){
        satelliteParticles.forEach((particles,index) => {
            particles.forEach(p => {
                ctx.beginPath();
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.life/30;
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI*2);
                ctx.fill();
                ctx.globalAlpha = 1;
                p.x += p.velocityX;
                p.y += p.velocityY;
                p.life--;
            });
            satelliteParticles[index] = particles.filter(p => p.life>0);
            if(satelliteParticles[index].length === 0){
                satelliteParticles.splice(index,1);
            }
        });
    }

    // Star blast particles
    function createStarBlastParticles(star){
        star.blastParticles = [];
        const count = 50;
        for(let i=0; i<count; i++){
            const angle = Math.random()*Math.PI*2;
            const speed = (Math.random()*3) + 2;
            star.blastParticles.push({
                x: star.x,
                y: star.y,
                radius: Math.random()*2 + 1,
                color: `hsl(${Math.random()*360}, 100%, 60%)`,
                velocityX: Math.cos(angle)*speed,
                velocityY: Math.sin(angle)*speed,
                life: 60,
                flicker: Math.random()*0.5 + 0.5,
                gravity: 0.06 + Math.random()*0.04,
                glow: Math.random()*20 + 10
            });
        }
        star.blasting = true;
    }
    function drawStarBlastParticles(star){
        star.blastParticles.forEach(p => {
            ctx.beginPath();
            ctx.shadowColor = p.color;
            ctx.shadowBlur = p.glow;
            const alpha = (p.life/60)*p.flicker;
            ctx.fillStyle = `hsla(${hueFromColor(p.color)}, 100%, 70%, ${alpha})`;
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI*2);
            ctx.fill();
            ctx.shadowBlur = 0;
            p.x += p.velocityX;
            p.y += p.velocityY;
            p.velocityY += p.gravity;
            p.life--;
        });
        star.blastParticles = star.blastParticles.filter(p => p.life > 0);
        if(star.blastParticles.length === 0){
            star.blasting = false;
        }
    }
    function hueFromColor(colorStr){
        const match = colorStr.match(/hsl\((\d+),/);
        return match ? match[1] : 0;
    }

    // Falling stars (shooting stars)
    let fallingStars = [];
    function createFallingStar() {
        return {
            x: Math.random() * width,
            y: -10,
            radius: Math.random() * 1.5 + 0.8,
            velocityX: (Math.random() * 2) - 1,
            velocityY: 5 + Math.random() * 3,
            gravity: 0.3,
            life: 60
        };
    }
    function updateFallingStars() {
        if (Math.random() < 0.015) {
            fallingStars.push(createFallingStar());
        }
        fallingStars = fallingStars.filter(star => star.life > 0);
        fallingStars.forEach(star => {
            star.x += star.velocityX;
            star.y += star.velocityY;
            star.velocityY += star.gravity;
            star.life--;
            ctx.beginPath();
            let gradient = ctx.createRadialGradient(star.x, star.y, 0, star.x, star.y, star.radius * 10);
            gradient.addColorStop(0, 'rgba(255,255,255,1)');
            gradient.addColorStop(1, 'rgba(255,255,255,0)');
            ctx.fillStyle = gradient;
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = 'rgba(255,255,255,0.6)';
            ctx.lineWidth = star.radius;
            ctx.beginPath();
            ctx.moveTo(star.x, star.y);
            ctx.lineTo(star.x - star.velocityX * 10, star.y - star.velocityY * 10);
            ctx.stroke();
        });
    }

    // Animate everything
    function animate(){
        ctx.clearRect(0, 0, width, height);

        // Draw stars
        ctx.fillStyle = '#ffffff';
        stars.forEach(star => {
            if(!star.blasting){
                ctx.beginPath();
                ctx.arc(star.x, star.y, star.radius, 0, Math.PI*2);
                ctx.fill();

                star.x += star.velocityX;
                star.y += star.velocityY;

                if(star.x > width) star.x = 0;
                else if(star.x < 0) star.x = width;

                if(star.y > height) star.y = 0;
                else if(star.y < 0) star.y = height;
            } else {
                drawStarBlastParticles(star);
            }
        });

        // Draw planets & satellites
        planets.forEach(planet => {
            planet.x += planet.velocityX;
            planet.y += planet.velocityY;

            if(planet.x - planet.radius > width) planet.x = -planet.radius;
            else if(planet.x + planet.radius < 0) planet.x = width + planet.radius;

            if(planet.y - planet.radius > height) planet.y = -planet.radius;
            else if(planet.y + planet.radius < 0) planet.y = height + planet.radius;

            let gradient = ctx.createRadialGradient(
                planet.x - 20, planet.y - 20, planet.radius*0.2,
                planet.x, planet.y, planet.radius
            );
            gradient.addColorStop(0, '#ffffff88');
            gradient.addColorStop(1, planet.color);
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.arc(planet.x, planet.y, planet.radius, 0, Math.PI*2);
            ctx.fill();

            planet.satellites.forEach(sat => {
                if(!sat.crashed){
                    sat.orbitAngle += sat.orbitSpeed;
                }
                sat.x = planet.x + sat.orbitRadius * Math.cos(sat.orbitAngle);
                sat.y = planet.y + sat.orbitRadius * Math.sin(sat.orbitAngle);

                ctx.beginPath();
                ctx.fillStyle = sat.crashed ? '#ff0000' : sat.color;
                ctx.shadowColor = sat.crashed ? 'red' : sat.color;
                ctx.shadowBlur = sat.crashed ? 15 : 8;
                ctx.arc(sat.x, sat.y, sat.radius, 0, Math.PI*2);
                ctx.fill();
                ctx.shadowBlur = 0;

                if(sat.crashed){
                    sat.crashTime++;
                    if(sat.crashTime === 1){
                        createSatelliteParticles(sat.x, sat.y);
                    }
                    if(sat.crashTime > 40){
                        sat.crashed = false;
                        sat.crashTime = 0;
                        sat.orbitAngle = Math.random()*Math.PI*2;
                    }
                }
            });
        });

        drawSatelliteParticles();
        updateFallingStars();

        requestAnimationFrame(animate);
    }

    // Click to trigger star explosion
    canvas.addEventListener('click', e => {
        const mx = e.clientX;
        const my = e.clientY;
        let closestStar = null;
        let minDist = 50;
        stars.forEach(star => {
            const dist = Math.hypot(star.x - mx, star.y - my);
            if(dist < minDist && !star.blasting){
                minDist = dist;
                closestStar = star;
            }
        });
        if(closestStar){
            createStarBlastParticles(closestStar);
        }
    });

    // Satellites crash randomly every 7 seconds approx
    setInterval(() => {
        let planet = planets[Math.floor(Math.random()*planets.length)];
        let sat = planet.satellites[Math.floor(Math.random()*planet.satellites.length)];
        if(!sat.crashed){
            sat.crashed = true;
        }
    }, 7000);

    animate();
</script>
</body>
</html>
