<?php
session_start();
include '../includes/db.php'; // adjust path if needed

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password using password_verify
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', Arial, sans-serif;
            background: #090a0f;
            overflow: hidden;
            color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            flex-direction: column;
            position: relative;
        }

        canvas#stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            display: block;
            background: transparent;
        }

        .login-box {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.07);
            padding: 60px 50px 70px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(74, 144, 226, 0.8);
            max-width: 500px;
            width: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: pulseGlow 3s ease-in-out infinite;
            color: white;
            text-shadow: 0 0 10px rgba(0,0,0,0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transform-origin: center center;
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 25px rgba(74, 144, 226, 0.6), 0 0 50px rgba(74, 144, 226, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 40px rgba(74, 144, 226, 0.9), 0 0 70px rgba(74, 144, 226, 0.5);
                transform: scale(1.05);
            }
        }

        .login-box h2 {
            margin: 0 0 35px;
            font-weight: 700;
            font-size: 2.6rem;
            color: white;
            letter-spacing: 1px;
            text-align: center;
            text-shadow: 0 0 8px #a0c8ff;
        }

        .login-box form {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            padding: 18px 22px;
            margin-bottom: 25px;
            font-size: 1.2rem;
            border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline-offset: 2px;
            outline-color: transparent;
        }

        .login-box input[type="text"]::placeholder,
        .login-box input[type="password"]::placeholder {
            color: #ddd;
        }

        .login-box input[type="text"]:focus,
        .login-box input[type="password"]:focus {
            border-color: #a0c8ff;
            box-shadow: 0 0 8px #a0c8ff;
            background: rgba(255, 255, 255, 0.25);
            outline-color: #a0c8ff;
        }

        .login-box button {
            padding: 18px 0;
            background: linear-gradient(90deg, #4a90e2 0%, #1a53a0 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(74, 144, 226, 0.6);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .login-box button:hover {
            background: linear-gradient(90deg, #1a53a0 0%, #4a90e2 100%);
            transform: scale(1.04);
        }

        .error-msg {
            color: #ff6b6b;
            font-weight: 600;
            margin-top: -10px;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 0 0 5px #ff6b6b;
        }

        .back-btn {
            margin-top: 20px;
            padding: 12px 25px;
            background-color: transparent;
            border: 2px solid white;
            border-radius: 8px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s, color 0.3s;
            font-size: 1rem;
            text-align: center;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 0 8px rgba(255,255,255,0.2);
        }

        .back-btn:hover {
            background-color: white;
            color: #1a53a0;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-box {
                padding: 40px 30px 50px;
                max-width: 95%;
            }

            .login-box h2 {
                font-size: 2rem;
                margin-bottom: 25px;
            }

            .login-box input[type="text"],
            .login-box input[type="password"],
            .login-box button,
            .back-btn {
                font-size: 1rem;
                padding: 14px 18px;
            }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .login-box {
                max-width: 450px;
            }
        }
    </style>
</head>
<body>
    <canvas id="stars"></canvas>

    <div class="login-box">
        <h2>Admin Login</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <a href="../index.php" class="back-btn">← Back to Home</a>
    </div>

<script>
    const canvas = document.getElementById('stars');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    // Stars array
    let stars = [];
    const numStars = 150;

    for(let i=0; i<numStars; i++){
        stars.push({
            x: Math.random()*canvas.width,
            y: Math.random()*canvas.height,
            radius: Math.random()*1.5 + 0.5,
            velocityX: (Math.random()-0.5)*0.4,
            velocityY: (Math.random()-0.5)*0.4,
            blasting: false,
            blastParticles: []
        });
    }

    // Planets
    let planets = [
        {x: Math.random()*canvas.width, y: Math.random()*canvas.height, radius: 80, color: '#3a86ff', velocityX: (Math.random()-0.5)*0.3, velocityY: (Math.random()-0.5)*0.3, satellites: []},
        {x: Math.random()*canvas.width, y: Math.random()*canvas.height, radius: 50, color: '#ff006e', velocityX: (Math.random()-0.5)*0.3, velocityY: (Math.random()-0.5)*0.3, satellites: []},
        {x: Math.random()*canvas.width, y: Math.random()*canvas.height, radius: 65, color: '#ffbe0b', velocityX: (Math.random()-0.5)*0.3, velocityY: (Math.random()-0.5)*0.3, satellites: []}
    ];

    // Satellites orbiting planets
    function createSatellite(planet){
        return {
            orbitRadius: planet.radius + 30 + Math.random()*40,
            orbitAngle: Math.random()*Math.PI*2,
            orbitSpeed: 0.005 + Math.random()*0.015,
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

    // --- Realistic Star Blast (Fireworks style) ---
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

            // Glow effect
            ctx.shadowColor = p.color;
            ctx.shadowBlur = p.glow;

            // Flickering opacity for sparkle effect
            const alpha = (p.life/60)*p.flicker;
            ctx.fillStyle = `hsla(${hueFromColor(p.color)}, 100%, 70%, ${alpha})`;

            ctx.arc(p.x, p.y, p.radius, 0, Math.PI*2);
            ctx.fill();

            // Reset shadow
            ctx.shadowBlur = 0;

            // Update position with velocity and gravity
            p.x += p.velocityX;
            p.y += p.velocityY;
            p.velocityY += p.gravity;

            // Reduce life and flicker
            p.life--;
        });

        // Remove dead particles
        star.blastParticles = star.blastParticles.filter(p => p.life > 0);

        if(star.blastParticles.length === 0){
            star.blasting = false;
        }
    }

    function hueFromColor(colorStr){
        const match = colorStr.match(/hsl\((\d+),/);
        return match ? match[1] : 0;
    }

    // Falling stars (shooting stars) array
    let fallingStars = [];

    function createFallingStar() {
        return {
            x: Math.random() * canvas.width,
            y: -10,
            radius: Math.random() * 1.5 + 0.8,
            velocityX: (Math.random() * 2) - 1, // horizontal speed between -1 and 1
            velocityY: 5 + Math.random() * 3,  // initial downward speed between 5 and 8
            gravity: 0.3,
            life: 60 // frames (~1 sec)
        };
    }

    function updateFallingStars() {
        // Spawn new falling star randomly, about 1 every 1-2 seconds
        if (Math.random() < 0.015) {
            fallingStars.push(createFallingStar());
        }

        // Remove dead stars
        fallingStars = fallingStars.filter(star => star.life > 0);

        fallingStars.forEach(star => {
            star.x += star.velocityX;
            star.y += star.velocityY;
            star.velocityY += star.gravity;
            star.life--;

            ctx.beginPath();
            // Glow effect for shooting star
            let gradient = ctx.createRadialGradient(star.x, star.y, 0, star.x, star.y, star.radius * 10);
            gradient.addColorStop(0, 'rgba(255,255,255,1)');
            gradient.addColorStop(1, 'rgba(255,255,255,0)');
            ctx.fillStyle = gradient;
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fill();

            // Tail line behind the falling star
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
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw stars
        ctx.fillStyle = '#ffffff';
        stars.forEach(star => {
            if(!star.blasting){
                ctx.beginPath();
                ctx.arc(star.x, star.y, star.radius, 0, Math.PI*2);
                ctx.fill();

                star.x += star.velocityX;
                star.y += star.velocityY;

                if(star.x > canvas.width) star.x = 0;
                else if(star.x < 0) star.x = canvas.width;

                if(star.y > canvas.height) star.y = 0;
                else if(star.y < 0) star.y = canvas.height;
            } else {
                drawStarBlastParticles(star);
            }
        });

        // Draw planets & satellites
        planets.forEach(planet => {
            planet.x += planet.velocityX;
            planet.y += planet.velocityY;

            if(planet.x - planet.radius > canvas.width) planet.x = -planet.radius;
            else if(planet.x + planet.radius < 0) planet.x = canvas.width + planet.radius;

            if(planet.y - planet.radius > canvas.height) planet.y = -planet.radius;
            else if(planet.y + planet.radius < 0) planet.y = canvas.height + planet.radius;

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

    // On click, trigger a random star blast effect
    canvas.addEventListener('click', function(e){
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

    // Periodically make a satellite randomly crash (exploding animation)
    setInterval(() => {
        let planet = planets[Math.floor(Math.random()*planets.length)];
        let sat = planet.satellites[Math.floor(Math.random()*planet.satellites.length)];
        if(!sat.crashed){
            sat.crashed = true;
        }
    }, 7000);

    // Resize canvas on window resize
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });

    animate();
</script>
</body>
</html>
