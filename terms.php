<!DOCTYPE html>
<?php
require_once("core-f/config-f/s1.php");
?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قوانين اللعبة - عودة التتار</title>
    <link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--primary:#c0392b;--primary-dark:#8e1a0e;--accent:#f39c12;--bg-dark:#0d0d1a;--bg-card:#151528;--bg-card-light:#1c1c35;--text:#e8e8f0;--text-muted:#9999aa;--text-bright:#fff;--border:rgba(255,255,255,0.08);--gold:#ffd700;--radius:12px}
        html{scroll-behavior:smooth}
        body{font-family:'Tajawal',Tahoma,Arial,sans-serif;direction:rtl;background:var(--bg-dark);color:var(--text);line-height:1.8;font-size:16px;-webkit-font-smoothing:antialiased}
        a{text-decoration:none;color:var(--accent);transition:color .3s}a:hover{color:var(--gold)}
        .terms-hero{background:linear-gradient(135deg,rgba(13,13,26,.9),rgba(142,26,14,.4)),url(core-f/style-f/assets-index/newItems/imgs/main.png) center/cover;padding:120px 20px 60px;text-align:center}
        .terms-hero h1{font-size:3rem;font-weight:900;color:var(--text-bright);margin-bottom:12px;text-shadow:0 4px 20px rgba(0,0,0,.5)}
        .terms-hero p{font-size:1.2rem;color:var(--text-muted);max-width:600px;margin:0 auto}
        .back-nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(13,13,26,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between}
        .back-nav a{color:var(--text);font-weight:700;font-size:1rem}.back-nav .nav-title{color:var(--text-bright);font-weight:800;font-size:1.1rem}
        .terms-layout{max-width:1100px;margin:0 auto;padding:40px 20px 80px;display:flex;gap:40px}
        .terms-sidebar{position:sticky;top:80px;width:260px;flex-shrink:0;height:fit-content}
        .sidebar-nav{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;list-style:none}
        .sidebar-nav h3{color:var(--accent);font-size:.9rem;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border)}
        .sidebar-nav a{display:flex;align-items:center;gap:10px;color:var(--text-muted);padding:10px 12px;border-radius:8px;font-size:.9rem;font-weight:500;transition:all .3s;margin-bottom:4px}
        .sidebar-nav a:hover,.sidebar-nav a.active{color:var(--text-bright);background:rgba(255,255,255,.05)}
        .sidebar-nav a.active{border-right:3px solid var(--accent);color:var(--accent)}
        .sidebar-nav .icon{font-size:1.2rem;width:28px;text-align:center}
        .terms-content{flex:1;min-width:0}
        .terms-section{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:32px;margin-bottom:24px;opacity:0;transform:translateY(30px);transition:opacity .6s ease,transform .6s ease}
        .terms-section.visible{opacity:1;transform:translateY(0)}
        .terms-section h2{display:flex;align-items:center;gap:12px;font-size:1.5rem;font-weight:800;color:var(--text-bright);margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid var(--border)}
        .terms-section h2 .si{font-size:1.6rem;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:10px}
        .rule-item{display:flex;gap:14px;padding:16px;margin-bottom:10px;border-radius:10px;background:var(--bg-card-light);border:1px solid transparent;transition:all .3s}
        .rule-item:hover{border-color:var(--border);transform:translateX(-4px)}
        .rule-num{flex-shrink:0;width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--primary);color:#fff;border-radius:8px;font-weight:800;font-size:.85rem}
        .rule-text{flex:1}.rule-text strong{color:var(--accent)}
        .penalty-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:16px}
        .penalty-card{text-align:center;padding:24px 16px;border-radius:var(--radius);border:1px solid var(--border);transition:transform .3s}
        .penalty-card:hover{transform:translateY(-4px)}
        .penalty-card .pi{font-size:2.2rem;margin-bottom:10px}
        .penalty-card h4{color:var(--text-bright);margin-bottom:6px}
        .penalty-card p{font-size:.85rem;color:var(--text-muted)}
        .pw{background:rgba(241,196,15,.08)}.ps{background:rgba(230,126,34,.08)}.pb{background:rgba(192,57,43,.08)}
        .faq-item{margin-bottom:12px;border:1px solid var(--border);border-radius:10px;overflow:hidden}
        .faq-question{width:100%;background:var(--bg-card-light);color:var(--text-bright);border:none;padding:16px 20px;font-family:inherit;font-size:1rem;font-weight:700;text-align:right;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:background .3s}
        .faq-question:hover{background:rgba(255,255,255,.05)}
        .faq-arrow{transition:transform .3s;font-size:1.2rem;color:var(--accent)}
        .faq-item.open .faq-arrow{transform:rotate(180deg)}
        .faq-answer{max-height:0;overflow:hidden;transition:max-height .4s ease,padding .3s;padding:0 20px;color:var(--text-muted);line-height:1.9}
        .faq-item.open .faq-answer{max-height:300px;padding:16px 20px}
        .terms-cta{text-align:center;padding:50px 20px;background:linear-gradient(135deg,var(--bg-card),var(--bg-card-light));border:1px solid var(--border);border-radius:var(--radius)}
        .terms-cta h2{justify-content:center;border:none;margin-bottom:12px}
        .terms-cta p{color:var(--text-muted);margin-bottom:24px}
        .btn-agree{display:inline-block;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;font-family:inherit;font-weight:700;font-size:1.1rem;padding:14px 48px;border:none;border-radius:10px;cursor:pointer;transition:all .3s;box-shadow:0 4px 20px rgba(192,57,43,.3)}
        .btn-agree:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(192,57,43,.4);color:#fff}
        .terms-footer{text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;border-top:1px solid var(--border)}
        @media(max-width:768px){.terms-hero h1{font-size:2rem}.terms-hero{padding:100px 16px 40px}.terms-layout{flex-direction:column;padding:20px 16px 60px;gap:20px}.terms-sidebar{position:static;width:100%}.terms-section{padding:20px}.terms-section h2{font-size:1.2rem}.penalty-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <nav class="back-nav">
        <a href="index.php">&#8594; الرئيسية</a>
        <span class="nav-title">قوانين اللعبة</span>
        <a href="register.php">التسجيل</a>
    </nav>
    <section class="terms-hero">
        <h1>قوانين اللعبة</h1>
        <p>اقرأ القوانين والشروط قبل الانضمام لعالم عودة التتار</p>
    </section>
    <div class="terms-layout">
        <aside class="terms-sidebar">
            <nav class="sidebar-nav">
                <h3>التنقل السريع</h3>
                <a href="#general" class="active"><span class="icon">&#9876;</span> القواعد العامة</a>
                <a href="#rights"><span class="icon">&#9733;</span> حقوق اللاعب</a>
                <a href="#financial"><span class="icon">&#9830;</span> التعاملات المالية</a>
                <a href="#penalties"><span class="icon">&#9888;</span> العقوبات</a>
                <a href="#faq"><span class="icon">&#10067;</span> الأسئلة الشائعة</a>
            </nav>
        </aside>
        <main class="terms-content">
            <section class="terms-section" id="general">
                <h2><span class="si">&#9876;</span> القواعد العامة</h2>
                <p style="margin-bottom:16px;color:var(--text-muted)">لعبة عودة التتار هي لعبة للترفيه وتسلية الوقت. يجب على جميع اللاعبين الالتزام بالآداب العامة والاحترام المتبادل.</p>
                <div class="rule-item"><span class="rule-num">1</span><div class="rule-text"><strong>حساب واحد فقط:</strong> يسمح لكل لاعب بحساب واحد فقط. سيتم حظر جميع الحسابات في حال وجود تعدد في العضويات.</div></div>
                <div class="rule-item"><span class="rule-num">2</span><div class="rule-text"><strong>ممنوع الإشهار:</strong> ممنوع نشر أو الإشهار لأي مواقع أخرى من خلال موقعنا عن طريق إرسال الرسائل أو الكتابة بالوصف. المخالف يتم حظره نهائيا.</div></div>
                <div class="rule-item"><span class="rule-num">3</span><div class="rule-text"><strong>الإبلاغ عن الأخطاء:</strong> من يقوم باستغلال خطأ باللعبة لصالحه ضد اللاعبين وعدم تبليغ الإدارة يتم إيقافه من 72 ساعة إلى إيقاف دائم حسب الحالة.</div></div>
                <div class="rule-item"><span class="rule-num">4</span><div class="rule-text"><strong>احترام اللاعبين:</strong> ممنوع الإساءة أو التهديد أو استخدام ألفاظ خارجة عن الآداب العامة تجاه أي لاعب.</div></div>
                <div class="rule-item"><span class="rule-num">5</span><div class="rule-text"><strong>عدم انتحال الشخصية:</strong> ممنوع انتحال شخصية الإدارة أو أي لاعب آخر.</div></div>
            </section>
            <section class="terms-section" id="rights">
                <h2><span class="si">&#9733;</span> حقوق اللاعب</h2>
                <div class="rule-item"><span class="rule-num">1</span><div class="rule-text"><strong>الدعم المستمر:</strong> للاعب الحق في الحصول على دعم مستمر والرد على مشاكله واستفساراته من خلال طاقم الإدارة في مدة أقصاها 24 ساعة.</div></div>
                <div class="rule-item"><span class="rule-num">2</span><div class="rule-text"><strong>التعويض:</strong> للاعب الحق في الحصول على تعويض إن لحقت به أضرار ناتجة عن أخطاء اللعبة.</div></div>
                <div class="rule-item"><span class="rule-num">3</span><div class="rule-text"><strong>اللعب العادل:</strong> للاعب الحق في بيئة لعب عادلة خالية من الغش والاستغلال.</div></div>
                <div class="rule-item"><span class="rule-num">4</span><div class="rule-text"><strong>حماية البيانات:</strong> بيانات اللاعب الشخصية محمية ولن يتم مشاركتها مع أي طرف ثالث.</div></div>
            </section>
            <section class="terms-section" id="financial">
                <h2><span class="si">&#9830;</span> التعاملات المالية وشراء الذهب</h2>
                <p style="margin-bottom:16px;color:var(--text-muted)">يوجد في عودة التتار تعاملات مالية وهي شراء الذهب الخاص باللعبة لمنح اللاعب المزيد من الصلاحيات.</p>
                <div class="rule-item"><span class="rule-num">1</span><div class="rule-text"><strong>عدم وصول الذهب:</strong> في حال شراء اللاعب الذهب ولم يصل إليه، عليه مراسلة الإدارة برقم العملية الشرائية حتى يتم التأكد منها.</div></div>
                <div class="rule-item"><span class="rule-num">2</span><div class="rule-text"><strong>عدم الاسترجاع:</strong> لا يحق للاعب طلب إرجاع الأموال التي اشترى بها الذهب وقد وصل إليه الذهب بدون مشاكل.</div></div>
                <div class="rule-item"><span class="rule-num">3</span><div class="rule-text"><strong>نقل الذهب:</strong> عند انتهاء السيرفر أو حذف حسابك، ينتقل الذهب المتبقي إلى السيرفر الجديد تلقائيا عند تسجيلك بنفس البريد الإلكتروني.</div></div>
            </section>
            <section class="terms-section" id="penalties">
                <h2><span class="si">&#9888;</span> نظام العقوبات</h2>
                <p style="margin-bottom:16px;color:var(--text-muted)">العقوبات تتدرج حسب خطورة المخالفة وتكرارها:</p>
                <div class="penalty-grid">
                    <div class="penalty-card pw"><div class="pi">&#9888;</div><h4>تحذير</h4><p>إنذار أولي للمخالفات البسيطة مع تنبيه بعدم التكرار</p></div>
                    <div class="penalty-card ps"><div class="pi">&#9201;</div><h4>إيقاف مؤقت</h4><p>إيقاف الحساب من 24 ساعة إلى 72 ساعة حسب المخالفة</p></div>
                    <div class="penalty-card pb"><div class="pi">&#128683;</div><h4>حظر دائم</h4><p>حظر نهائي للحساب في المخالفات الجسيمة أو التكرار</p></div>
                </div>
            </section>
            <section class="terms-section" id="faq">
                <h2><span class="si">&#10067;</span> الأسئلة الشائعة</h2>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>هل يمكنني امتلاك أكثر من حساب؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">لا، يسمح لكل لاعب بحساب واحد فقط. تعدد الحسابات يؤدي لحظر جميع الحسابات المرتبطة.</div></div>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>ماذا أفعل إذا وجدت خطأ في اللعبة؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">يجب الإبلاغ عن أي خطأ فورا عبر نظام الدعم. استغلال الأخطاء لصالحك يعرض حسابك للإيقاف أو الحظر.</div></div>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>هل يمكنني استرجاع أموال شراء الذهب؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">لا يمكن استرجاع الأموال بعد استلام الذهب بنجاح. في حال عدم وصول الذهب، تواصل مع الإدارة برقم العملية.</div></div>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>ماذا يحدث لذهبي عند انتهاء السيرفر؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">ينتقل الذهب المتبقي تلقائيا إلى السيرفر الجديد عند تسجيلك بنفس البريد الإلكتروني.</div></div>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>كم مدة حماية المبتدئين؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">تحصل على حماية مبتدئين لمدة <?php echo $AppConfig['Game']['protection']; ?> ساعة من وقت تسجيلك. خلال هذه الفترة لا يمكن لأحد مهاجمتك.</div></div>
                <div class="faq-item"><button class="faq-question" onclick="toggleFaq(this)"><span>هل يمكن تغيير هذه القوانين؟</span><span class="faq-arrow">&#9660;</span></button><div class="faq-answer">نعم، يحق لإدارة اللعبة تغيير القوانين أو الإضافة عليها في أي وقت. بمجرد تسجيلك تكون قد وافقت على القوانين الحالية والمستقبلية.</div></div>
            </section>
            <section class="terms-cta">
                <h2>هل أنت مستعد للمعركة؟</h2>
                <p>بتسجيلك في عودة التتار تكون قد وافقت على جميع القوانين أعلاه</p>
                <a href="register.php" class="btn-agree">أوافق وأريد التسجيل</a>
            </section>
        </main>
    </div>
    <footer class="terms-footer"><p>جميع الحقوق محفوظة &copy; عودة التتار <?php echo date('Y'); ?></p></footer>
    <script>
    function toggleFaq(btn){var item=btn.parentElement;var wasOpen=item.classList.contains('open');document.querySelectorAll('.faq-item.open').forEach(function(i){i.classList.remove('open')});if(!wasOpen)item.classList.add('open')}
    var observer=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting)entry.target.classList.add('visible')})},{threshold:0.1});
    document.querySelectorAll('.terms-section').forEach(function(s){observer.observe(s)});
    var sections=document.querySelectorAll('.terms-section');var sidebarLinks=document.querySelectorAll('.sidebar-nav a');
    window.addEventListener('scroll',function(){var current='';sections.forEach(function(s){if(window.scrollY>=s.offsetTop-120)current=s.getAttribute('id')});sidebarLinks.forEach(function(link){link.classList.remove('active');if(link.getAttribute('href')==='#'+current)link.classList.add('active')})});
    </script>
</body>
</html>
