<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Agriconnect | আমাদের সম্পর্কে</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />

  <style>
    :root{
      --bg-1: #07130b;
      --bg-2: #0b2a12;
      --card: #07120f;
      --accent: #80ff80;
      --accent-dark: #2e7d32;
      --muted: #cfead1;
      --glass: rgba(255,255,255,0.03);
      --glass-2: rgba(255,255,255,0.02);
      --card-radius: 16px;
      --gap: 28px;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:var(--muted);
      background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
                  linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      line-height:1.6;
      padding-bottom:60px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* subtle animated overlay */
    .bg-anim{
      position:fixed; inset:0; z-index:0; pointer-events:none; opacity:0.06;
      background-image: url("https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1400&auto=format&fit=crop&ixlib=rb-4.0.3");
      background-size:cover; background-position:center;
      filter:grayscale(80%) contrast(80%) blur(1px);
      animation: bgShift 20s linear infinite;
    }
    @keyframes bgShift{ 0%{transform:translateY(0)} 50%{transform:translateY(-10px)} 100%{transform:translateY(0)} }

    header.site-header{
      position:relative; z-index:10;
      padding:18px 32px;
      display:flex; align-items:center; justify-content:space-between;
      gap:12px;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(8px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.6);
    }

    .logo {
      display:flex; align-items:center; gap:12px; text-decoration:none;
      color:var(--muted);
      font-weight:700; font-size:1.15rem;
    }
    .logo svg{ width:42px; height:42px; display:block; flex-shrink:0; }

    nav {
      display:flex; align-items:center; gap:18px;
    }
    nav a{
      color:var(--muted);
      text-decoration:none; padding:8px 12px; border-radius:10px; font-weight:600;
      transition: background 0.3s ease, color 0.3s ease;
    }
    nav a:hover,
    nav a:focus { background:var(--glass); color:var(--accent); outline: none; transform:translateY(-2px); }

    .cta {
      background: linear-gradient(90deg,var(--accent-dark), #1E90FF);
      color:#06110a; padding:8px 14px; border-radius:12px; font-weight:700;
      box-shadow: 0 6px 18px rgba(0,0,0,0.45);
      transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .cta:hover{ transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 30px rgba(0,0,0,0.6); }

    main {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
      z-index: 5;
      position: relative;
    }

    .card {
      position: relative; overflow: visible;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius: var(--card-radius);
      padding: 36px 32px;
      max-width: 800px;
      box-shadow: 0 8px 26px rgba(2,6,2,0.6);
      border: 1px solid rgba(255,255,255,0.03);
      color: var(--muted);
      font-size: 1.1rem;
      line-height: 1.6;
      backdrop-filter: blur(8px);
    }

     /*outline animation */
   .outline  {
  display: none;  /* hide outline entirely */
}

.outline svg rect {
  fill: none;
  stroke-width: 4;
  stroke-linecap: round;
  stroke-linejoin: round;
  opacity: 0.95;
  stroke: var(--accent);
  stroke-dasharray: none;     
  stroke-dashoffset: 0;       
  transition: none;           
}



    h1, h2 {
      color: var(--accent);
      margin-bottom: 18px;
      text-shadow: 0 3px 12px rgba(0, 128, 0, 0.7);
      font-weight: 700;
    }

    p {
      margin-bottom: 20px;
      color: var(--muted);
      opacity: 0.95;
    }

    footer {
      text-align: center;
      margin-top: 40px;
      padding-bottom: 20px;
      color: #bfe6b8;
      opacity: 0.9;
      font-size: 0.95rem;
      z-index: 10;
      position: relative;
    }

    /* Responsive */
    @media (max-width: 640px) {
      header.site-header {
        padding: 12px 18px;
      }
      .card {
        padding: 24px 20px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="bg-anim" aria-hidden="true"></div>

  <header class="site-header" role="banner">
    <a href="home.html" class="logo" aria-label="Agriconnect Home">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent)">
        <path d="M21 11.5c-4.5 1.5-7.5 5.5-10 9-2.3-3.2-5.7-5.2-9-6C3.9 4.9 12 2 21 11.5z"></path>
      </svg> 
      Agriconnect
    </a>

    <nav role="navigation" aria-label="Main navigation">
      <a href="agriofficerloginsignup.html">Agri Officer</a>
      <a href="farmerloginsignup.html">Farmer</a>
      <a href="WholesellerLoginsignin.html">Wholesaler</a>
            <a href="faq.php">FAQ</a>

    </nav>
  </header>

  <main role="main">
    <article class="card" tabindex="0" aria-labelledby="about-title" aria-describedby="about-desc">
      <div class="outline" aria-hidden="true">
        <svg viewBox="0 0 320 260" preserveAspectRatio="none" width="100%" height="100%">
          <rect x="6" y="6" width="308" height="248" rx="16" ry="16"></rect>
        </svg>
      </div>

      <h1 id="about-title">আমাদের সম্পর্কে</h1>

      <section id="about-desc">
        <h2>Agriconnect — কৃষকদের বিশ্বস্ত সহযোগী</h2>
        <p>
          Agriconnect একটি সমন্বিত ডিজিটাল প্ল্যাটফর্ম যা কৃষকদের এবং কৃষি সংশ্লিষ্ট সকলের জন্য তৈরি।
          আমরা কৃষকদের, পাইকারি বিক্রেতাদের এবং কৃষি বিশেষজ্ঞদের সংযোগ স্থাপন করে জ্ঞান শেয়ারিং
          এবং উন্নত কৃষি ব্যবস্থাপনায় সহায়তা করতে কাজ করি।
        </p>

        <h2>আমাদের লক্ষ্য</h2>
        <p>
          একটি সহজ, ব্যবহারবান্ধব ও সমন্বিত ডিজিটাল প্ল্যাটফর্ম তৈরি করা যা কৃষকদের উৎপাদনশীলতা বৃদ্ধি,
          সম্প্রদায় উন্নয়ন এবং টেকসই কৃষি চর্চায় সহায়তা করবে।
        </p>

        <h2>আমাদের দৃষ্টি</h2>
        <p>
          বাংলাদেশে আধুনিক কৃষি প্রযুক্তির সেরা সমাধান প্রদানকারী প্ল্যাটফর্ম হয়ে উঠা,
          যা কৃষক ও আধুনিক কৃষি সম্পদের মধ্যে সেতুবন্ধন করবে।
        </p>
      </section>
    </article>
  </main>

  <footer role="contentinfo">
    &copy; <?php echo date("Y"); ?> Agriconnect — টেকসই কৃষির জন্য নির্মিত।
  </footer>
</body>
</html>
