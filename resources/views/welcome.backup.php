<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANALIS - Sistem Terpadu</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4F46E5;
            --primary-glow: rgba(79, 70, 229, 0.4);
            --secondary: #06B6D4;
            --bg-color: #0F172A;
            --surface: rgba(30, 41, 59, 0.7);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Gradients */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            top: -200px;
            left: -200px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 8s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Navbar */
        nav {
            padding: 24px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(to right, #4F46E5, #06B6D4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            gap: 32px;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 400;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--secondary);
        }

        /* Hero Section */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            padding: 20px;
        }

        .hero-container {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 56px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
        }

        .hero-content h1 span {
            color: var(--primary);
        }

        .hero-content p {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 500px;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .btn-primary:hover {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Glassmorphism Card */
        .hero-visual {
            position: relative;
        }

        .glass-card {
            background: var(--surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }

        .glass-card:hover {
            transform: perspective(1000px) rotateY(0deg) scale(1.02);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .pulse-dot {
            width: 12px;
            height: 12px;
            background-color: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .stat-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-content p {
                margin: 20px auto 40px;
            }
            .cta-buttons {
                justify-content: center;
            }
            .glass-card {
                transform: none;
            }
            .glass-card:hover {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>

    <!-- Decorative Gradients -->
    <div class="bg-glow"></div>
    <div class="bg-glow-2"></div>

    <!-- Navigation -->
    <nav>
        <div class="logo">ANALIS</div>
        <div class="nav-links">
            <a href="#">Beranda</a>
            <a href="#">Fitur</a>
            <a href="#">Dokumentasi</a>
            <a href="#">Kontak</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="hero-container">
            
            <!-- Left Text Content -->
            <div class="hero-content">
                <h1>Sistem Informasi<br><span>ANALIS</span> Terpadu</h1>
                <p>Platform analitik dan manajemen data generasi terbaru. Didukung oleh antarmuka yang intuitif dan performa tinggi untuk mempermudah pekerjaan Anda.</p>
                <div class="cta-buttons">
                    <a href="#" class="btn btn-primary">Masuk ke Sistem</a>
                    <a href="#" class="btn btn-outline">Pelajari Lebih Lanjut</a>
                </div>
            </div>

            <!-- Right Visual Content -->
            <div class="hero-visual">
                <div class="glass-card">
                    <div class="card-header">
                        <div class="pulse-dot"></div>
                        <div class="card-title">Status Sistem: Online</div>
                    </div>
                    
                    <div class="stat-grid">
                        <div class="stat-box">
                            <div class="stat-value">99.9%</div>
                            <div class="stat-label">Uptime Server</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">< 0.5s</div>
                            <div class="stat-label">Waktu Respon</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">Aman</div>
                            <div class="stat-label">Enkripsi Data</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">Aktif</div>
                            <div class="stat-label">Database Sync</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
