<!DOCTYPE html>
<?php
require_once("core-f/config-f/s1.php");
?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عن اللعبة - عودة التتار</title>
    <link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--primary:#c0392b;--primary-dark:#8e1a0e;--accent:#f39c12;--bg-dark:#0d0d1a;--bg-card:#151528;--bg-card-light:#1c1c35;--text:#e8e8f0;--text-muted:#9999aa;--text-bright:#fff;--border:rgba(255,255,255,0.08);--gold:#ffd700;--radius:12px}
        html{scroll-behavior:smooth}
        body{font-family:'Tajawal',Tahoma,Arial,sans-serif;direction:rtl;background:var(--bg-dark);color:var(--text);line-height:1.8;font-size:16px;-webkit-font-smoothing:antialiased}
        a{text-decoration:none;color:var(--accent);transition:color .3s}
        a:hover{color:var(--gold)}
        /* nav */
        .back-nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(13,13,26,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between}
        .back-nav a{color:var(--text);font-weight:700;font-size:1rem}
        .back-nav .nav-title{color:var(--text-bright);font-weight:800;font-size:1.1rem}
        /* hero */
        .about-hero{background:linear-gradient(135deg,rgba(13,13,26,.88),rgba(142,26,14,.45)),url(core-f/style-f/assets-index/newItems/imgs/main.png) center/cover;padding:120px 20px 60px;text-align:center}
        .about-hero h1{font-size:3rem;font-weight:900;color:var(--text-bright);margin-bottom:12px;text-shadow:0 4px 20px rgba(0,0,0,.5)}
        .about-hero p{font-size:1.2rem;color:var(--text-muted);max-width:600px;margin:0 auto}
        /* layout */
        .about-layout{max-width:1100px;margin:0 auto;padding:40px 20px 80px;display:flex;gap:40px}
        .about-sidebar{position:sticky;top:80px;width:260px;flex-shrink:0;height:fit-content}
        .sidebar-nav{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;list-style:none}
        .sidebar-nav h3{color:var(--accent);font-size:.9rem;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border)}
        .sidebar-nav a{display:flex;align-items:center;gap:10px;color:var(--text-muted);padding:10px 12px;border-radius:8px;font-size:.9rem;font-weight:500;transition:all .3s;margin-bottom:4px}
        .sidebar-nav a:hover,.sidebar-nav a.active{color:var(--text-bright);background:rgba(255,255,255,.05)}
        .sidebar-nav a.active{border-right:3px solid var(--accent);color:var(--accent)}
        .sidebar-nav .icon{font-size:1.2rem;width:28px;text-align:center}
        /* content */
        .about-content{flex:1;min-width:0}
        .about-section{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:32px;margin-bottom:24px;opacity:0;transform:translateY(30px);transition:opacity .6s ease,transform .6s ease}
        .about-section.visible{opacity:1;transform:translateY(0)}
        .about-section h2{display:flex;align-items:center;gap:12px;font-size:1.5rem;font-weight:800;color:var(--text-bright);margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid var(--border)}
        .about-section h2 .si{font-size:1.4rem;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:10px;flex-shrink:0}
        /* feature grid */
        .feature-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:16px}
        .feature-card{text-align:center;padding:24px 16px;border-radius:var(--radius);border:1px solid var(--border);background:var(--bg-card-light);transition:transform .3s,border-color .3s}
        .feature-card:hover{transform:translateY(-5px);border-color:rgba(243,156,18,.3)}
        .feature-card .fi{font-size:2.5rem;margin-bottom:12px}
        .feature-card h4{color:var(--text-bright);margin-bottom:8px;font-size:1rem}
        .feature-card p{font-size:.85rem;color:var(--text-muted);line-height:1.7}
        /* contact */
        .contact-item{display:flex;align-items:center;gap:16px;padding:18px 20px;border-radius:10px;background:var(--bg-card-light);border:1px solid var(--border);margin-bottom:12px;transition:border-color .3s}
        .contact-item:hover{border-color:rgba(243,156,18,.35)}
        .contact-icon{width:48px;height:48px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:10px;font-size:1.5rem;flex-shrink:0}
        .contact-info h4{color:var(--text-bright);font-size:1rem;margin-bottom:3px}
        .contact-info p,.contact-info a{color:var(--text-muted);font-size:.95rem}
        /* social */
        .social-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-top:6px}
        .social-btn{display:flex;align-items:center;justify-content:center;gap:10px;padding:16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-card-light);color:var(--text-muted);font-weight:700;font-size:.95rem;transition:all .3s;cursor:pointer}
        .social-btn:hover{transform:translateY(-3px);border-color:rgba(255,255,255,.18);color:var(--text-bright)}
        .social-btn .sicon{font-size:1.5rem}
        .social-btn.twitter:hover{border-color:rgba(29,161,242,.5);color:#1da1f2}
        .social-btn.discord:hover{border-color:rgba(114,137,218,.5);color:#7289da}
        .social-btn.youtube:hover{border-color:rgba(255,0,0,.5);color:#ff0000}
        .social-btn.telegram:hover{border-color:rgba(0,136,204,.5);color:#0088cc}
        .coming-soon{display:inline-block;font-size:.75rem;background:rgba(243,156,18,.15);color:var(--accent);border:1px solid rgba(243,156,18,.3);border-radius:6px;padding:2px 8px;margin-right:8px;vertical-align:middle}
        /* footer */
        .about-footer{text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;border-top:1px solid var(--border)}
        @media(max-width:768px){
            .about-hero h1{font-size:2rem}
            .about-hero{padding:100px 16px 40px}
            .about-layout{flex-direction:column;padding:20px 14px 60px;gap:20px}
            .about-sidebar{position:static;width:100%}
            .about-section{padding:20px}
            .about-section h2{font-size:1.2rem}
            .feature-grid{grid-template-columns:1fr 1fr}
            .social-grid{grid-template-columns:1fr 1fr}
        }
    </style>
</head>
<body>
    <nav class="back-nav">
        <a href="index.php">&#8594; الرئيسية</a>
        <span class="nav-title">عن اللعبة</span>
        <a href="register.php">التسجيل</a>
    </nav>

    <section class="about-hero">
        <h1>&#9876; عودة التتار</h1>
        <p>لعبة استراتيجية حربية تنافسية - ابنِ امبراطوريتك وقُد جيوشك نحو النصر</p>
    </section>

    <div class="about-layout">
        <aside class="about-sidebar">
            <nav class="sidebar-nav">
                <h3>التنقل السريع</h3>
                <a href="#about" class="active"><span class="icon">&#9876;</span> عن اللعبة</a>
                <a href="#features"><span class="icon">&#9733;</span> مميزات اللعبة</a>
                <a href="#contact"><span class="icon">&#9993;</span> تواصل معنا</a>
                <a href="#social"><span class="icon">&#127760;</span> التواصل الاجتماعي</a>
            </nav>
        </aside>

        <main class="about-content">

            <!-- About Section -->
            <section class="about-section" id="about">
                <h2><span class="si">&#9876;</span> عن اللعبة</h2>
                <p style="color:var(--text-muted);margin-bottom:18px;line-height:2">
                    <strong style="color:var(--text-bright)">عودة التتار</strong> هي لعبة استراتيجية حربية تنافسية تعمل عبر المتصفح مباشرةً. تأسس قريتك، تدرّب جيوشك، وتحالف مع اللاعبين لتسيطر على الخريطة وتحتل معجزة العالم قبل أن يفعل غيرك.
                </p>
                <p style="color:var(--text-muted);margin-bottom:18px;line-height:2">
                    تتميز اللعبة بنظام تحف فريد يمنحك مزايا استراتيجية، وقبائل متعددة ذات خصائص مختلفة مثل <strong style="color:var(--accent)">الرومان</strong>، <strong style="color:var(--accent)">الأغريق</strong>، <strong style="color:var(--accent)">الجرمان</strong>، <strong style="color:var(--accent)">العرب</strong>، والمزيد. كل سيرفر يمتد لأيام محدودة مما يجعل كل معركة مثيرة ومشوّقة.
                </p>
                <p style="color:var(--text-muted);line-height:2">
                    انضم إلى آلاف اللاعبين واثبت أنك الأقوى — لأن في عودة التتار، <strong style="color:var(--text-bright)">الحرب لا تنتهي حتى يقف آخر مقاتل.</strong>
                </p>
            </section>

            <!-- Features Section -->
            <section class="about-section" id="features">
                <h2><span class="si">&#9733;</span> مميزات اللعبة</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="fi">&#9876;</div>
                        <h4>معارك استراتيجية</h4>
                        <p>خطط هجماتك بعناية واختر توقيت الضربة المثالية</p>
                    </div>
                    <div class="feature-card">
                        <div class="fi">&#9733;</div>
                        <h4>نظام التحف</h4>
                        <p>احتل التحف النادرة واحصل على مزايا حاسمة في المعركة</p>
                    </div>
                    <div class="feature-card">
                        <div class="fi">&#127961;</div>
                        <h4>بناء الامبراطورية</h4>
                        <p>طوّر قراك ومبانيك لتكوّن قوة لا تُقهر</p>
                    </div>
                    <div class="feature-card">
                        <div class="fi">&#129309;</div>
                        <h4>التحالفات</h4>
                        <p>تعاون مع لاعبين آخرين لتسيطر على الخريطة معاً</p>
                    </div>
                    <div class="feature-card">
                        <div class="fi">&#127958;</div>
                        <h4>قبائل متعددة</h4>
                        <p>اختر قبيلتك واستغل قدراتها الفريدة لتحقيق النصر</p>
                    </div>
                    <div class="feature-card">
                        <div class="fi">&#127942;</div>
                        <h4>توزيع الأوسمة</h4>
                        <p>تنافس يومياً لتحتل مراكز الصدارة وتنال الأوسمة</p>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="about-section" id="contact">
                <h2><span class="si">&#9993;</span> تواصل معنا</h2>
                <p style="color:var(--text-muted);margin-bottom:20px">فريق عودة التتار في خدمتك — لا تتردد في التواصل معنا لأي استفسار أو مشكلة.</p>

                <div class="contact-item">
                    <div class="contact-icon">&#9993;</div>
                    <div class="contact-info">
                        <h4>البريد الإلكتروني</h4>
                        <a href="mailto:<?php echo htmlspecialchars($AppConfig['system']['email']); ?>">
                            <?php echo htmlspecialchars($AppConfig['system']['email']); ?>
                        </a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">&#128172;</div>
                    <div class="contact-info">
                        <h4>رسائل داخل اللعبة</h4>
                        <p>تستطيع مراسلة الإدارة مباشرةً من داخل اللعبة عبر نظام الرسائل — الرد خلال 24 ساعة كحد أقصى.</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">&#128680;</div>
                    <div class="contact-info">
                        <h4>الإبلاغ عن مشكلة</h4>
                        <p>إذا واجهت خطأ في اللعبة أو لاحظت استغلالاً للثغرات، أبلغنا فوراً عبر البريد الإلكتروني أعلاه.</p>
                    </div>
                </div>
            </section>

            <!-- Social Section -->
            <section class="about-section" id="social">
                <h2><span class="si">&#127760;</span> التواصل الاجتماعي</h2>
                <p style="color:var(--text-muted);margin-bottom:20px">تابعنا على منصات التواصل الاجتماعي لتكون أول من يعلم بالأحداث والسيرفرات الجديدة.</p>
                <div class="social-grid">
                    <div class="social-btn twitter">
                        <span class="sicon">&#120143;</span>
                        <span>تويتر / X <span class="coming-soon">قريباً</span></span>
                    </div>
                    <div class="social-btn discord">
                        <span class="sicon">&#127381;</span>
                        <span>ديسكورد <span class="coming-soon">قريباً</span></span>
                    </div>
                    <div class="social-btn youtube">
                        <span class="sicon">&#127909;</span>
                        <span>يوتيوب <span class="coming-soon">قريباً</span></span>
                    </div>
                    <div class="social-btn telegram">
                        <span class="sicon">&#128228;</span>
                        <span>تيليجرام <span class="coming-soon">قريباً</span></span>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <footer class="about-footer">
        <p>جميع الحقوق محفوظة &copy; عودة التتار <?php echo date('Y'); ?></p>
    </footer>

    <script>
    var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){if(entry.isIntersecting) entry.target.classList.add('visible')});
    },{threshold:0.08});
    document.querySelectorAll('.about-section').forEach(function(s){observer.observe(s)});

    var sections = document.querySelectorAll('.about-section');
    var sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    window.addEventListener('scroll',function(){
        var current='';
        sections.forEach(function(s){if(window.scrollY>=s.offsetTop-120) current=s.getAttribute('id')});
        sidebarLinks.forEach(function(link){
            link.classList.remove('active');
            if(link.getAttribute('href')==='#'+current) link.classList.add('active');
        });
    });
    </script>
</body>
</html>
