<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Contact Me</title>
<style>
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #eef2f7;
        color: #2c3e50;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .container {
        background: #fff;
        max-width: 480px;
        width: 100%;
        padding: 40px 35px;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 82, 204, 0.15);
        transition: box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .container:hover {
        box-shadow: 0 20px 50px rgba(0, 82, 204, 0.25);
    }

    h1 {
        text-align: center;
        font-size: 2.8rem;
        margin-bottom: 30px;
        color: #0052cc;
        letter-spacing: 1.2px;
        font-weight: 700;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    form label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    input[type="text"],
    input[type="email"],
    textarea {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 25px;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        font-size: 1rem;
        font-family: inherit;
        color: #2c3e50;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        resize: vertical;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
        border-color: #0052cc;
        box-shadow: 0 0 8px rgba(0, 82, 204, 0.4);
        outline: none;
    }

    textarea {
        min-height: 120px;
    }

    button {
        background-color: #0052cc;
        color: #fff;
        border: none;
        padding: 14px 0;
        font-size: 1.25rem;
        font-weight: 700;
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        margin-top: 10px;
        width: 100%;
    }

    button:hover:not(:disabled) {
        background-color: #003d99;
        box-shadow: 0 8px 25px rgba(0, 61, 153, 0.6);
    }

    button:disabled {
        background-color: #777;
        cursor: not-allowed;
        box-shadow: none;
    }

    button.back-btn {
        background-color: #777;
        margin-top: 20px;
    }

    button.back-btn:hover {
        background-color: #444;
        box-shadow: none;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 520px) {
        .container {
            padding: 30px 20px;
        }
        h1 {
            font-size: 2.2rem;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h1>Contact Me</h1>

    <form id="contactForm" action="save_contact.php" method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your full name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@gmail.com" required>

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" placeholder="Subject of your message" required>

        <label for="message">Message</label>
        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>

        <button type="submit" id="submitBtn" disabled>Please fill-up all fields</button>
    </form>

    <button class="back-btn" onclick="window.location.href='index.php'">← Back to Home</button>
</div>

<script>
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');

    function isValidGmail(email) {
        // Strict Gmail validation: lowercase or uppercase letters, digits, dots allowed before @gmail.com
        // Change regex as needed, here simplified to allow anything before @gmail.com
        // Change regex as needed, here simplified to allow anything before @gmail.com
        return /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(email);
    }

    const validateForm = () => {
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const subject = form.subject.value.trim();
        const message = form.message.value.trim();

        return (
            name.length > 0 &&
            subject.length > 0 &&
            message.length > 0 &&
            isValidGmail(email)
        );
    };

    form.addEventListener('input', () => {
        if (validateForm()) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        } else {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Please fill-up all fields with a valid Gmail address';
        }
    });

    form.addEventListener('submit', e => {
        if (!validateForm()) {
            e.preventDefault();
            alert('Please fill all fields correctly and enter a valid Gmail address.');
        }
    });

    window.addEventListener('load', () => {
        if (validateForm()) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        } else {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Please fill-up all fields with a valid Gmail address';
        }
    });
</script>
</body>
</html>
