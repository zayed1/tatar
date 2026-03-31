<!DOCTYPE html>
<?php require_once("core-f/config-f/s1.php"); ?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آخر التحديثات - عودة التتار</title>
    <link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--primary:#c0392b;--primary-dark:#8e1a0e;--accent:#f39c12;--bg-dark:#0d0d1a;--bg-card:#151528;--bg-card-light:#1c1c35;--text:#e8e8f0;--text-muted:#9999aa;--text-bright:#fff;--border:rgba(255,255,255,0.08);--radius:12px}
        html{scroll-behavior:smooth}
        body{font-family:'Tajawal',Tahoma,sans-serif;direction:rtl;background:var(--bg-dark);color:var(--text);line-height:1.8;font-size:16px}
        a{text-decoration:none;color:var(--accent)}a:hover{color:#ffd700}
        .back-nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(13,13,26,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between}
        .back-nav a{color:var(--text);font-weight:700}.back-nav .nav-title{color:var(--text-bright);font-weight:800;font-size:1.1rem}
        .hero{background:linear-gradient(135deg,rgba(13,13,26,.9),rgba(142,26,14,.4)),url(core-f/style-f/assets-index/newItems/imgs/main.png) center/cover;padding:120px 20px 60px;text-align:center}
        .hero h1{font-size:3rem;font-weight:900;color:var(--text-bright);text-shadow:0 4px 20px rgba(0,0,0,.5)}
        .hero p{font-size:1.2rem;color:var(--text-muted);max-width:600px;margin:12px auto 0}
        .content{max-width:800px;margin:0 auto;padding:40px 20px 80px}
        .timeline{position:relative;padding-right:30px}
        .timeline::before{content:'';position:absolute;right:8px;top:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--primary),var(--accent));border-radius:3px}
        .update{position:relative;margin-bottom:30px;opacity:0;transform:translateY(20px);transition:all .6s}
        .update.visible{opacity:1;transform:translateY(0)}
        .update::before{content:'';position:absolute;right:-26px;top:8px;width:14px;height:14px;background:var(--primary);border:3px solid var(--bg-dark);border-radius:50%;z-index:1}
        .update-date{font-size:.85rem;color:var(--accent);font-weight:700;margin-bottom:6px}
        .update-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;transition:transform .3s}
        .update-card:hover{transform:translateX(-4px)}
        .update-card h3{color:var(--text-bright);font-size:1.2rem;margin-bottom:10px;display:flex;align-items:center;gap:8px}
        .tag{display:inline-block;padding:2px 10px;border-radius:6px;font-size:.75rem;font-weight:700}
        .tag-new{background:rgba(46,204,113,.15);color:#2ecc71}
        .tag-fix{background:rgba(52,152,219,.15);color:#3498db}
        .tag-improve{background:rgba(243,156,18,.15);color:#f39c12}
        .update-list{list-style:none;margin-top:10px}
        .update-list li{padding:6px 0;color:var(--text-muted);border-bottom:1px solid var(--border);font-size:.95rem}
        .update-list li:last-child{border:none}
        .update-list li::before{content:'→ ';color:var(--accent)}
        .footer{text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;border-top:1px solid var(--border)}
        @media(max-width:768px){.hero h1{font-size:2rem}.hero{padding:100px 16px 40px}.content{padding:20px 16px}}
    </style>
</head>
<body>
    <nav class="back-nav">
        <a href="index.php">&#8594; الرئيسية</a>
        <span class="nav-title">آخر التحديثات</span>
        <a href="login.php">دخول</a>
    </nav>
    <section class="hero">
        <h1>آخر التحديثات</h1>
        <p>تابع أحدث التطويرات والإصلاحات في عودة التتار</p>
    </section>
    <div class="content">
        <div class="timeline">
            <div class="update">
                <div class="update-date"><?php echo date('Y/m/d'); ?></div>
                <div class="update-card">
                    <h3><span class="tag tag-new">جديد</span> إطلاق التطبيق</h3>
                    <ul class="update-list">
                        <li>إطلاق تطبيق عودة التتار على iOS</li>
                        <li>تصميم جديد بالكامل للصفحة الرئيسية</li>
                        <li>صفحة قواعد اللعبة بتصميم عصري</li>
                        <li>صفحة "عن اللعبة" جديدة</li>
                        <li>صفحة دليل اللعبة محدّثة</li>
                    </ul>
                </div>
            </div>
            <div class="update">
                <div class="update-date"><?php echo date('Y/m/d'); ?></div>
                <div class="update-card">
                    <h3><span class="tag tag-improve">تحسين</span> تحسينات الأداء</h3>
                    <ul class="update-list">
                        <li>تحسين سرعة تحميل الصفحات</li>
                        <li>تحسين عرض اللعبة على الجوال</li>
                        <li>إضافة نظام cache لتسريع التحميل</li>
                        <li>تحسين أحجام الأزرار للجوال</li>
                    </ul>
                </div>
            </div>
            <div class="update">
                <div class="update-date"><?php echo date('Y/m/d'); ?></div>
                <div class="update-card">
                    <h3><span class="tag tag-fix">إصلاح</span> إصلاحات تقنية</h3>
                    <ul class="update-list">
                        <li>إصلاح مشاكل التوافق مع PHP 8.1</li>
                        <li>إصلاح مشكلة تسجيل الدخول</li>
                        <li>إصلاح مشكلة التسجيل وتفعيل الحساب</li>
                        <li>إضافة حماية CSRF للنماذج</li>
                        <li>تأمين ملف الإعداد</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer"><p>جميع الحقوق محفوظة &copy; عودة التتار <?php echo date('Y'); ?></p></footer>
    <script>
    var observer=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting)e.target.classList.add('visible')})},{threshold:0.1});
    document.querySelectorAll('.update').forEach(function(u){observer.observe(u)});
    </script>
</body>
</html>
