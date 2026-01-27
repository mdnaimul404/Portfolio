<?php
session_start();
include 'includes/db.php';

// Fetch slider images from DB
$sliderImages = [];
$result = $conn->query("SELECT image_path FROM home_slider_images ORDER BY uploaded_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_path'];

        // Allow only valid image formats
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        if (in_array($ext, $allowedExts)) {
            $sliderImages[] = $imagePath;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Md. Naimul Islam | Portfolio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    :root {
      --primary: #1e2a78;
      --secondary: #f0f0f0;
      --light: #ffffff;
      --dark: #121212;
      --accent: #3c91e6;

      --bg-color: #f0f4ff;
      --text-color: var(--dark);
      --box-color: var(--light);
    }

    body.dark-mode {
      --bg-color: #121212;
      --text-color: #e0e0e0;
      --box-color: #1f1f1f;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: var(--bg-color);
      color: var(--text-color);
      text-align: center;
      transition: background 0.3s ease, color 0.3s ease;
      position: relative;
      overflow-x: hidden;
      cursor: none; /* hide default cursor */
      min-height: 100vh;
    }

    /* Canvas background layer */
    #backgroundCanvas {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      pointer-events: none;
      background: transparent;
    }

    /* Custom glowing cursor */
    #customCursor {
      position: fixed;
      top: 0; left: 0;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      pointer-events: none;
      background: var(--accent);
      box-shadow:
        0 0 8px var(--accent),
        0 0 15px var(--accent),
        0 0 25px var(--accent);
      transition: transform 0.1s ease;
      transform: translate(-50%, -50%);
      z-index: 9999;
      mix-blend-mode: difference;
    }

    header {
      padding: 40px 20px 20px 20px;
      background-color: var(--primary);
      color: var(--light);
      position: relative;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    header h1 {
      margin: 0;
      font-size: 2.5rem;
    }

    header p {
      margin-top: 10px;
      font-size: 1.1rem;
      color: #dce3f5;
    }

    .slider-container {
      max-width: 922px;
      margin: 40px auto 20px auto;
      background-color: var(--box-color);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 12px;
      padding: 10px 10px 20px 10px;
      position: relative;
    }

    #slider {
      width: 100%;
      height: 600px;
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      background: #f0f0f0;
      box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    #slider img {
      width: 100%;
      height: 100%;
      position: absolute;
      left: 0; top: 0;
      opacity: 0;
      transition: opacity 1s ease-in-out;
      object-fit: cover;
      user-select: none;
    }

    #slider img.active {
      opacity: 1;
      z-index: 10;
    }

    .slider-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(30, 42, 120, 0.7);
      color: white;
      border: none;
      padding: 10px 15px;
      cursor: pointer;
      border-radius: 50%;
      font-size: 24px;
      transition: background 0.3s ease;
      user-select: none;
      z-index: 20;
    }

    .slider-arrow:hover {
      background: var(--accent);
    }

    #arrow-left {
      left: 15px;
    }

    #arrow-right {
      right: 15px;
    }

    .description-container {
      max-width: 900px;
      margin: 20px auto 40px auto;
      padding: 20px;
      background-color: var(--box-color);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 12px;
      text-align: justify;
      font-size: 1rem;
      line-height: 1.6;
    }

    nav {
      max-width: 900px;
      margin: 0 auto 40px auto;
      text-align: center;
    }

    nav a {
      display: inline-block;
      margin: 10px;
      padding: 12px 24px;
      background-color: var(--light);
      color: var(--primary);
      border: 2px solid var(--primary);
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    nav a:hover {
      background-color: var(--accent);
      color: #fff;
      border-color: var(--accent);
      transform: translateY(-2px);
    }

    footer {
      margin-top: 40px;
      padding: 20px;
      background-color: var(--primary);
      color: var(--light);
      font-size: 0.9rem;
    }

    .theme-toggle {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .toggle-btn {
      background-color: #fff;
      border: none;
      border-radius: 20px;
      padding: 8px 16px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
      transition: background 0.3s ease, color 0.3s ease;
    }

    .toggle-btn:hover {
      background-color: var(--accent);
      color: #fff;
    }

    @media (max-width: 960px) {
      .slider-container, .description-container, nav {
        max-width: 95%;
      }
      #slider {
        height: 250px;
      }
      .slider-arrow {
        font-size: 18px;
        padding: 8px 12px;
      }
    }

    /* --- Memory Card Match Game Styles --- */
    .memory-game-container {
      max-width: 400px;
      margin: 60px auto 60px auto;
      padding: 20px;
      background-color: #121212;
      border-radius: 15px;
      box-shadow: 0 0 20px #00f0ffaa;
      color: #00f0ff;
      user-select: none;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .memory-game-container h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.8rem;
      text-shadow:
        0 0 5px #00f0ff,
        0 0 10px #00e0ff,
        0 0 20px #00d0ff;
    }

    .memory-game {
      display: grid;
      grid-template-columns: repeat(4, 80px);
      grid-gap: 15px;
      justify-content: center;
      perspective: 1000px;
    }

    .memory-card {
      width: 80px;
      height: 80px;
      background: #000;
      border-radius: 10px;
      cursor: pointer;
      transform-style: preserve-3d;
      transition: transform 0.5s;
      position: relative;
      box-shadow:
        0 0 5px #00f0ff,
        inset 0 0 15px #00e0ff;
      color: #00f0ff;
      font-size: 2rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .memory-card.flipped {
      transform: rotateY(180deg);
    }

    .memory-card .front,
    .memory-card .back {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .memory-card .front {
      background: #00f0ff;
      color: #000;
      transform: rotateY(180deg);
      box-shadow:
        0 0 10px #00f0ff,
        inset 0 0 20px #00b8ff;
    }

    .memory-card .back {
      background: #000;
      color: #00f0ff;
      box-shadow:
        0 0 10px #00f0ff,
        inset 0 0 15px #00b8ff;
    }
  </style>
</head>
<body>
  <canvas id="backgroundCanvas"></canvas>
  <div id="customCursor"></div>

  <header>
    <div class="theme-toggle">
      <button class="toggle-btn" onclick="toggleTheme()">Toggle Theme</button>
    </div>
    <h1>Md. Naimul Islam</h1>
    <p>Final-Year CSE Student at AIUB | AI • Embedded Systems • Full-Stack Development</p>
  </header>

  <nav>
    <a href="pages/projects.php">Projects</a>
    <a href="pages/blogs.php">Blogs</a>
    <a href="contact.php">Contact</a>
    <a href="resume.php">Resume</a>
    <a href="admin/login.php">Admin Login</a>
  </nav>

  <div class="description-container">
    <p><strong>I am Md. Naimul Islam</strong>, a final-year student of Computer Science and Engineering at the American International University-Bangladesh (AIUB). I have a strong interest in leveraging technology to solve real-world problems, particularly in the fields of artificial intelligence, machine learning, embedded systems, and full-stack web development. With a solid academic foundation and practical exposure to diverse technical domains, I strive to build intelligent, scalable, and user-focused digital solutions.</p>

    <p>My core strengths include analytical thinking, adaptability, and a constant desire to learn and innovate. I am particularly enthusiastic about exploring how AI and data-driven insights can optimize processes in industries such as retail, automation, and smart systems. I am also deeply interested in UI/UX design and system architecture, and I aim to continue expanding my expertise through research, collaboration, and meaningful contributions to the tech community.</p>
  </div>

  <div class="slider-container">
    <div id="slider">
      <?php foreach ($sliderImages as $index => $imgPath): ?>
        <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Slide <?php echo $index + 1; ?>" class="<?php echo ($index === 0) ? 'active' : ''; ?>">
      <?php endforeach; ?>

      <button class="slider-arrow" id="arrow-left" aria-label="Previous Slide">&#10094;</button>
      <button class="slider-arrow" id="arrow-right" aria-label="Next Slide">&#10095;</button>
    </div>
  </div>

  <section class="memory-game-container" aria-label="Memory Card Match Game">
    <h2>🧠 Memory Card Match</h2>
    <div class="memory-game" id="memoryGame">
      <!-- Cards inserted by JS -->
    </div>
  </section>

  <footer>
    &copy; <?php echo date("Y"); ?> Md. Naimul Islam. All Rights Reserved.
  </footer>

  <script>
    // Theme toggle
    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    }

    // Persist theme on reload
    window.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
      }
    });

    // Slider functionality
    const slides = document.querySelectorAll('#slider img');
    const leftArrow = document.getElementById('arrow-left');
    const rightArrow = document.getElementById('arrow-right');
    let currentIndex = 0;
    let slideInterval;

    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
      });
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
    }

    function prevSlide() {
      currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      showSlide(currentIndex);
    }

    leftArrow.addEventListener('click', () => {
      prevSlide();
      resetInterval();
    });

    rightArrow.addEventListener('click', () => {
      nextSlide();
      resetInterval();
    });

    function startInterval() {
      slideInterval = setInterval(nextSlide, 4000);
    }

    function resetInterval() {
      clearInterval(slideInterval);
      startInterval();
    }

    startInterval();

    /* --- Memory Card Match Game Script --- */
    const emojis = ['🍕', '🚀', '🌈', '🎧', '🎮', '🐱', '⚽', '🍩'];
    const gameBoard = document.getElementById('memoryGame');
    let cards = [];
    let flippedCard = null;
    let lockBoard = false;

    function shuffle(array) {
      return array.concat(array).sort(() => 0.5 - Math.random());
    }

    function createCard(emoji) {
      const card = document.createElement('div');
      card.classList.add('memory-card');

      const front = document.createElement('div');
      front.classList.add('front');
      front.textContent = emoji;

      const back = document.createElement('div');
      back.classList.add('back');
      back.textContent = '?';

      card.appendChild(front);
      card.appendChild(back);

      card.addEventListener('click', () => {
        if (lockBoard || card.classList.contains('flipped')) return;

        card.classList.add('flipped');

        if (!flippedCard) {
          flippedCard = card;
        } else {
          lockBoard = true;
          const isMatch = flippedCard.querySelector('.front').textContent === card.querySelector('.front').textContent;
          if (isMatch) {
            flippedCard = null;
            lockBoard = false;
            if (document.querySelectorAll('.memory-card.flipped').length === cards.length) {
              setTimeout(() => {
                alert('🎉 Great memory! Game restarting...');
                initGame();
              }, 700);
            }
          } else {
            setTimeout(() => {
              card.classList.remove('flipped');
              flippedCard.classList.remove('flipped');
              flippedCard = null;
              lockBoard = false;
            }, 1000);
          }
        }
      });

      return card;
    }

    function initGame() {
      gameBoard.innerHTML = '';
      const shuffledEmojis = shuffle(emojis);
      cards = shuffledEmojis.map(createCard);
      cards.forEach(card => gameBoard.appendChild(card));
    }

    initGame();

    // === BACKGROUND CANVAS PARTICLE NETWORK ===
    const canvas = document.getElementById('backgroundCanvas');
    const ctx = canvas.getContext('2d');
    let width, height;
    let particles = [];
    const PARTICLE_COUNT = 100;
    const MAX_DISTANCE = 150;

    function initCanvas() {
      width = window.innerWidth;
      height = window.innerHeight;
      canvas.width = width;
      canvas.height = height;
    }

    class Particle {
      constructor() {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.radius = Math.random() * 2 + 1;
        this.vx = (Math.random() - 0.5) * 0.5;
        this.vy = (Math.random() - 0.5) * 0.5;
      }

      update() {
        this.x += this.vx;
        this.y += this.vy;

        if (this.x < 0 || this.x > width) this.vx *= -1;
        if (this.y < 0 || this.y > height) this.vy *= -1;
      }

      draw() {
        ctx.beginPath();
        let gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.radius * 3);
        gradient.addColorStop(0, 'rgba(60, 145, 230, 0.9)');
        gradient.addColorStop(1, 'rgba(60, 145, 230, 0)');
        ctx.fillStyle = gradient;
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fill();
      }
    }

    function connectParticles() {
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          let dx = particles[i].x - particles[j].x;
          let dy = particles[i].y - particles[j].y;
          let dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < MAX_DISTANCE) {
            ctx.strokeStyle = `rgba(60, 145, 230, ${1 - dist / MAX_DISTANCE})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.stroke();
          }
        }
      }
    }

    function animate() {
      ctx.clearRect(0, 0, width, height);
      particles.forEach(p => {
        p.update();
        p.draw();
      });
      connectParticles();
      requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => {
      initCanvas();
      initParticles();
    });

    function initParticles() {
      particles = [];
      for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push(new Particle());
      }
    }

    initCanvas();
    initParticles();
    animate();

    // === CUSTOM CURSOR ===
    const cursor = document.getElementById('customCursor');
    window.addEventListener('mousemove', (e) => {
      cursor.style.left = e.clientX + 'px';
      cursor.style.top = e.clientY + 'px';
    });
  </script>
</body>
</html>
