<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About | AgriConnect - FAQ</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <style>
      :root {
        --bg-1: #07130b;
        --bg-2: #0b2a12;
        --accent: #80ff80;
        --accent-dark: #2e7d32;
        --muted: #cfead1;
        --glass: rgba(255, 255, 255, 0.03);
        --card-radius: 16px;
        --gap: 28px;
      }

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }
      html,
      body {
        height: 100%;
      }
      body {
        font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        color: var(--muted);
        background: radial-gradient(
            1000px 600px at 10% 10%,
            rgba(128, 255, 128, 0.05),
            transparent 10%
          ),
          linear-gradient(180deg, var(--bg-1), var(--bg-2) 60%);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        line-height: 1.4;
        padding-bottom: 60px;
      }
      header.site-header {
        position: relative;
        z-index: 10;
        padding: 18px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
      }
      .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--muted);
        font-weight: 700;
        font-size: 1.15rem;
      }
      .logo svg {
        width: 42px;
        height: 42px;
        display: block;
        flex-shrink: 0;
      }
      nav {
        display: flex;
        align-items: center;
        gap: 18px;
      }
      nav a {
        color: var(--muted);
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 10px;
        font-weight: 600;
      }
      nav a:hover,
      nav a:focus {
        background: var(--glass);
        color: var(--accent);
        outline: none;
        transform: translateY(-2px);
        transition: transform 0.18s ease;
      }
      .cta {
        background: linear-gradient(90deg, var(--accent-dark), #1e90ff);
        color: #06110a;
        padding: 8px 14px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
      }
      .cta:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
      }
      main {
        max-width: 900px;
        margin: 40px auto 80px;
        padding: 0 22px;
        position: relative;
        z-index: 8;
      }
      h1 {
        font-size: clamp(2rem, 5vw, 3.6rem);
        color: var(--accent);
        letter-spacing: -0.02em;
        margin-bottom: 24px;
        text-align: center;
        text-shadow: 0 6px 26px rgba(0, 0, 0, 0.6);
      }
      p.intro {
        font-size: 1.1rem;
        color: #d9efd8;
        max-width: 700px;
        margin: 0 auto 48px auto;
        opacity: 0.9;
        line-height: 1.6;
        text-align: center;
      }

      /* FAQ Accordion */
      .faq-item {
        background: linear-gradient(
          180deg,
          rgba(255, 255, 255, 0.02),
          rgba(255, 255, 255, 0.01)
        );
        border-radius: var(--card-radius);
        padding: 20px 24px;
        margin-bottom: 18px;
        box-shadow: 0 8px 26px rgba(2, 6, 2, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.03);
        cursor: pointer;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
      }
      .faq-item:hover {
        box-shadow: 0 14px 40px rgba(2, 6, 2, 0.8);
        transform: translateY(-6px);
      }
      .faq-question {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--muted);
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .faq-question span {
        font-size: 1.6rem;
        transition: transform 0.3s ease;
      }
      .faq-item.active .faq-question span {
        transform: rotate(45deg);
      }
      .faq-answer {
        margin-top: 12px;
        font-size: 1rem;
        color: #cfead1;
        line-height: 1.5;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
      }
      .faq-item.active .faq-answer {
        max-height: 500px; /* enough for content */
      }

      footer {
        text-align: center;
        margin-top: 40px;
        color: #bfe6b8;
        opacity: 0.9;
        font-size: 0.95rem;
      }

      /* Responsive */
      @media (max-width: 640px) {
        header.site-header {
          padding: 12px 18px;
        }
        main {
          margin: 24px 12px 60px;
          padding: 0 12px;
        }
        .faq-question {
          font-size: 1.1rem;
        }
      }
    </style>
</head>
<body>
  <header class="site-header">
    <a href="#" class="logo" aria-label="AgriConnect Home">
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.1"
        stroke-linecap="round"
        stroke-linejoin="round"
        style="color: var(--accent);"
      >
        <path
          d="M21 11.5c-4.5 1.5-7.5 5.5-10 9-2.3-3.2-5.7-5.2-9-6C3.9 4.9 12 2 21 11.5z"
        ></path>
      </svg>
      <span>AgriConnect</span>
    </a>
    <nav aria-label="Main navigation">
      <a href="agriofficerloginsignup.html">Agri Officer</a>
      <a href="farmerloginsignup.html">Farmer</a>
      <a href="WholesellerLoginsignin.html">Wholesaler</a>
      <a href="about.php" class="cta">About</a>
    </nav>
  </header>

  <main>
    <h1>সাধারণ কৃষি সম্পর্কিত প্রশ্নাবলী (FAQ)</h1>
    <p class="intro">AgriConnect এ স্বাগতম! নিচে কিছু সাধারণ কৃষি সম্পর্কিত প্রশ্ন ও তাদের উত্তর দেওয়া হয়েছে।</p>

    <div class="faq-item" tabindex="0">
      <div class="faq-question" aria-expanded="false" role="button">
        কৃষিতে কী কী প্রধান ফসল চাষ করা হয়?
        <span>+</span>
      </div>
      <div class="faq-answer" hidden>
        বাংলাদেশের প্রধান ফসল গুলোর মধ্যে ধান, গম, মসুর ডাল, ভুট্টা এবং আলু অন্যতম।
      </div>
    </div>

    <div class="faq-item" tabindex="0">
      <div class="faq-question" aria-expanded="false" role="button">
        কীভাবে পোকামাকড় ও রোগ প্রতিরোধ করা যায়?
        <span>+</span>
      </div>
      <div class="faq-answer" hidden>
        নিয়মিত মাঠ পরিদর্শন, বায়োলজিক্যাল কন্ট্রোল ব্যবহার এবং প্রয়োজনমতো কীটনাশক প্রয়োগ করলে পোকামাকড় ও রোগ প্রতিরোধ করা যায়।
      </div>
    </div>

    <div class="faq-item" tabindex="0">
      <div class="faq-question" aria-expanded="false" role="button">
        মাটির পিএইচ মান কিভাবে পরিমাপ করা হয়?
        <span>+</span>
      </div>
      <div class="faq-answer" hidden>
        মাটির পিএইচ পরিমাপের জন্য পিএইচ মিটার বা কেমিক্যাল টেস্ট কিট ব্যবহার করা হয়।
      </div>
    </div>

    <div class="faq-item" tabindex="0">
      <div class="faq-question" aria-expanded="false" role="button">
        কী ধরনের সেচ পদ্ধতি সবচেয়ে কার্যকর?
        <span>+</span>
      </div>
      <div class="faq-answer" hidden>
        ড্রিপ সেচ পদ্ধতি সবচেয়ে কার্যকর, কারণ এতে পানির অপচয় কমে এবং গাছের প্রয়োজন মত সেচ দেওয়া যায়।
      </div>
    </div>

    <div class="faq-item" tabindex="0">
      <div class="faq-question" aria-expanded="false" role="button">
        কীভাবে ফসলের উৎপাদন বাড়ানো যায়?
        <span>+</span>
      </div>
      <div class="faq-answer" hidden>
        ভালো বীজ নির্বাচন, সঠিক সেচ ও সার প্রয়োগ, এবং নিয়মিত পরিচর্যা করলে ফসলের উৎপাদন বাড়ানো যায়।
      </div>
    </div>
  </main>

  <footer>&copy; <?php echo date('Y'); ?> AgriConnect — Built for sustainable agriculture.</footer>

  <script>
    // FAQ accordion toggle
    document.querySelectorAll('.faq-item').forEach(item => {
      const question = item.querySelector('.faq-question');
      const answer = item.querySelector('.faq-answer');

      function toggleFAQ() {
        const expanded = question.getAttribute('aria-expanded') === 'true';
        question.setAttribute('aria-expanded', !expanded);
        item.classList.toggle('active');
        if (!expanded) {
          answer.hidden = false;
          answer.style.maxHeight = answer.scrollHeight + 'px';
        } else {
          answer.style.maxHeight = null;
          setTimeout(() => (answer.hidden = true), 300);
        }
      }

      question.addEventListener('click', toggleFAQ);
      item.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleFAQ();
        }
      });
    });
  </script>
</body>
</html>
