<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Student Project Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        /* Reset */
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        body, html {
            height: 100%;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            background: #182848;
            background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            color: #f0f0f5;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Overlay for darken background for text readability */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(24, 40, 72, 0.75);
            z-index: -1;
        }

        .container {
            max-width: 350px;
            width: 100%;
            background: rgba(255, 255, 255, 0.13);
            border-radius: 20px;
            padding: 30px 20px 45px 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            position: relative;
        }

        .hero-image {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px auto;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .hero-image:hover, .hero-image:focus {
            transform: scale(1.05) rotate(2deg);
            outline: none;
            cursor: pointer;
        }

        h1 {
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 12px;
            letter-spacing: 1.6px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.65);
        }

        h2 {
            font-weight: 400;
            font-size: 1.1rem;
            color: #b6c6e2;
            margin-bottom: 30px;
            font-style: italic;
            text-shadow: 0 2px 5px rgba(0,0,0,0.4);
        }

        blockquote {
            font-size: 1rem;
            font-style: italic;
            margin-bottom: 35px;
            color: #a9bad8;
            border-left: 5px solid #59c4f5;
            padding-left: 14px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            min-height: 60px;
            line-height: 1.33;
            user-select: none;
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        a.button {
            text-decoration: none;
            padding: 14px 0;
            background: #59c4f5;
            color: #14213d;
            font-weight: 700;
            border-radius: 40px;
            box-shadow: 0 7px 16px rgba(89,196,245,0.7);
            transition: background 0.35s ease, color 0.35s ease, box-shadow 0.35s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            filter: drop-shadow(0 3px 6px rgba(0,0,0,0.35));
            user-select: none;
            letter-spacing: 0.03em;
        }

        a.button svg {
            width: 24px;
            height: 24px;
            margin-right: 14px;
            fill: #14213d;
            transition: fill 0.35s ease;
        }

        a.button:hover, a.button:focus {
            background: #14213d;
            color: #59c4f5;
            box-shadow: 0 10px 28px rgba(89,196,245,0.95);
            outline: none;
        }

        a.button:hover svg, a.button:focus svg {
            fill: #59c4f5;
        }

        /* Responsive adjustments for small devices */
        @media (max-width: 400px) {
            .container {
                padding: 25px 15px 40px 15px;
            }

            h1 {
                font-size: 1.7rem;
            }

            h2 {
                font-size: 1rem;
                margin-bottom: 20px;
            }

            blockquote {
                font-size: 0.9rem;
                margin-bottom: 25px;
                min-height: 50px;
            }

            .hero-image {
                width: 90px;
                height: 90px;
                margin-bottom: 14px;
            }

            nav a.button {
                font-size: 1rem;
                padding: 12px 0;
            }

            a.button svg {
                width: 20px;
                height: 20px;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
    <main class="container" role="main" aria-label="Student project management system homepage">
        <!-- Hero thematic image related to teamwork/project -->
        <img 
            src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
            alt="Students collaborating on a project" 
            class="hero-image" 
            tabindex="0" 
        />

        <h1>Student Project Management</h1>
        <h2>Organize, track, and succeed together</h2>

        <blockquote cite="https://www.edutopia.org/article/5-quotes-celebrate-achievement">
            &ldquo;Start where you are. Use what you have. Do what you can.&rdquo;
            <br>– Arthur Ashe
        </blockquote>

        <nav aria-label="Primary navigation">
            <a href="user_login.php" class="button" role="link" aria-label="Go to User Login page">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                User Login
            </a>

            <a href="admin_login.php" class="button" role="link" aria-label="Go to Admin Login page">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 2L2 7v6c0 5 3 9 10 9s10-4 10-9V7l-10-5zM8 16l-4-4 1.41-1.41L8 13.17 14.59 6.59 16 8l-8 8z"/>
                </svg>
                Admin Login
            </a>

            <a href="faculty_login.php" class="button" role="link" aria-label="Go to Faculty Login page">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 12c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-7 8c0-2 4-3 7-3s7 1 7 3v1H5v-1zm14-14v2c0 0-3-2-7-2s-7 2-7 2V6c0 0 3-2 7-2s7 2 7 2z"/>
                </svg>
                Faculty Login
            </a>

            <a href="register.php" class="button" role="link" aria-label="Go to Register page">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                Register
            </a>
            <a href="forgot.php" class="button" role="link" aria-label="Forgot Password?">
                Forgot Password
            </a>
        </nav>
    </main>
</body>
</html>