<!DOCTYPE html>
<?php
function TimeAgo($diff_in_unix){
    $diff = '';
    if ($diff_in_unix > 3600){
        $diff .= intval($diff_in_unix/3600);
        $diff_in_unix = $diff_in_unix%3600;
    } else { $diff .= '00'; }
    if($diff_in_unix > 60 AND $diff_in_unix < 3600){
        $diff .= ":".intval($diff_in_unix / 60);
        $diff_in_unix = $diff_in_unix%60;
    } else { $diff .= ':00'; }
    if ($diff_in_unix < 60 AND $diff_in_unix > 0){
        $diff .= ":".$diff_in_unix;
    }
    return $diff;
}

$x = 0;
require_once("core-f/config-f/s1.php");
$db_port = isset($AppConfig['db']['port']) ? intval($AppConfig['db']['port']) : 3306;
$link = mysqli_connect($AppConfig['db']['host'], $AppConfig['db']['user'], $AppConfig['db']['password'], $AppConfig['db']['database'], $db_port) or die(mysqli_connect_error());

$result = mysqli_query($link, "SELECT * FROM p_queue WHERE id = 1 and proc_type= 24");
$row = $result ? mysqli_fetch_assoc($result) : null;
$end_date = $row ? $row["end_date"] : '';

$fetch = mysqli_query($link, "SELECT * FROM p_queue WHERE id = 3 and proc_type= 57");
$fetchs = $fetch ? mysqli_fetch_assoc($fetch) : null;

$redseahost = $AppConfig['system']['server_days'];
$subtracted_date = date('Y-m-d H:i:s', strtotime("-$redseahost days", strtotime($end_date)));
$subtracted_date4 = date('Y-m-d H:i:s', strtotime("-3 hours", strtotime($subtracted_date)));
$diff_in_seconds = strtotime($subtracted_date4) - time();
$diff_in_seconds = abs($diff_in_seconds);
$remaining_days = floor($diff_in_seconds / 86400);
$remaining_hours = floor(($diff_in_seconds % 86400) / 3600);
$remaining_time = sprintf('%02d:%02d', $remaining_days, $remaining_hours);

$q = mysqli_query($link, "SELECT * FROM g_summary");
$sessionTimeoutInSeconds = 9000 * 60;
$g = mysqli_query($link, "SELECT COUNT(*) FROM p_players WHERE TIME_TO_SEC(TIMEDIFF(NOW(), last_login_date)) <= ".$sessionTimeoutInSeconds."");
$g = $g ? mysqli_fetch_row($g) : array(0);
$r = $q ? mysqli_fetch_assoc($q) : array('players_count' => 0, 'active_players_count' => 0);
$online1 = floor((TimeAgo(time() - strtotime(date($AppConfig['system']['server_start'])))/24));
$online_before1 = floor((TimeAgo(strtotime($AppConfig['system']['server_start']) - time())/24));
$players_count1 = $r["players_count"];
$active_players_count1 = $r['active_players_count'];
$online_players_count1 = $g[0];
$x += 1;

// Heroes of previous round queries
$top_attacker_result = mysqli_query($link, "SELECT name, attack_points FROM p_players WHERE player_type != 3 ORDER BY attack_points DESC LIMIT 1");
$top_attacker = $top_attacker_result ? mysqli_fetch_assoc($top_attacker_result) : null;

$top_defender_result = mysqli_query($link, "SELECT name, defense_points FROM p_players WHERE player_type != 3 ORDER BY defense_points DESC LIMIT 1");
$top_defender = $top_defender_result ? mysqli_fetch_assoc($top_defender_result) : null;

// Server status helper
function getServerStatus($result, $fetch, $remaining_days, $remaining_hours) {
    $result_rows = $result ? mysqli_num_rows($result) : 0;
    $fetch_rows = $fetch ? mysqli_num_rows($fetch) : 0;
    if ($result_rows > 0 && $fetch_rows == 0) {
        return "<b>$remaining_days</b> ايام و <b>$remaining_hours</b> ساعة";
    } else if ($result_rows > 0 && $fetch_rows > 0) {
        return "<span class='status-pending'>لم تبدا بعد</span>";
    } else if ($result_rows == 0 && $fetch_rows > 0) {
        return "<span class='status-ended'>انتهت الجولة</span>";
    } else if ($result_rows == 0 && $fetch_rows == 0) {
        return "<span class='status-waiting'>بانتظار الاعادة</span>";
    } else {
        return "<span class='status-ended'>تم نزول التتار</span>";
    }
}
$serverStatus = getServerStatus($result, $fetch, $remaining_days, $remaining_hours);
?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عودة التتار - لعبة الحروب الاستراتيجية</title>
    <meta property="og:title" content="عودة التتار">
    <meta property="og:image" itemprop="image" content="core-f/style-f/assets-index/icons-1.jpg">
    <meta property="og:type" content="website">
    <meta property="og:description" content="عودة التتار هي لعبة استراتيجية بالكامل ، فهي تعتمد بشكل كلي على التخطيط والتدبير ووضع الخطط الحربية الدقيقة بالتعاون مع تحالفك حتى تتمكن من الفوز بالمعجزة.">
    <meta name="description" content="عودة التتار هي لعبة استراتيجية بالكامل ، فهي تعتمد بشكل كلي على التخطيط والتدبير ووضع الخطط الحربية الدقيقة بالتعاون مع تحالفك حتى تتمكن من الفوز بالمعجزة.">
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta name="content-language" content="ar">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="keywords" content="عودة التتار،سيرفر عودة التتار،لعبة عودة التتار،حرب ترافيان،عودة التتار،عودة التتار السريع،عودة التتار السعودى،سيرفر عودة التتار الرسمى،حرب ترافيان،لعبه حرب ترافيان">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="عودة التتار">
    <meta property="og:site_name" content="عودة التتار">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="لعبة عودة التتار">
    <meta name="twitter:description" content="عودة التتار هي لعبة استراتيجية بالكامل">
    <meta name="twitter:image" content="core-f/style-f/assets-index/IC-3.png">
    <link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
    <link rel="apple-touch-icon" href="core-f/style-f/assets-index/IC-3.png">
    <link rel="preload" as="image" href="core-f/style-f/assets-index/newItems/imgs/main.png">
    <link rel="stylesheet" href="core-f/style-f/assets-index/newItems/style.css?2.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <noscript>متصفحك لايدعم الجافاسكربت</noscript>
</head>
<body>

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-backdrop" id="modalBackdrop"></div>

        <!-- Login Modal -->
        <div class="modal" id="loginModal">
            <div class="modal-header">
                <h3>تسجيل الدخول</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form action="login.php" method="POST" class="modal-form">
                    <div class="form-group">
                        <label for="login-user">اسم المستخدم</label>
                        <input type="text" id="login-user" name="user" placeholder="ادخل اسم المستخدم" required>
                    </div>
                    <div class="form-group">
                        <label for="login-pass">كلمة المرور</label>
                        <input type="password" id="login-pass" name="pw" placeholder="ادخل كلمة المرور" required>
                    </div>
                    <button type="submit" class="btn-submit">دخول</button>
                </form>
                <div class="modal-server-info">
                    <img src="core-f/style-f/assets-index/images/welten/en1_big.jpg" alt="عودة التتار | السيرفر 1">
                    <div class="server-meta">
                        <span>العالم 1</span>
                        <span>اللاعبون: <b><?php echo $players_count1; ?></b></span>
                        <span>الحالة: <?php echo $serverStatus; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Register Modal -->
        <div class="modal" id="registerModal">
            <div class="modal-header">
                <h3>تسجيل حساب جديد</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-server-info">
                    <img src="core-f/style-f/assets-index/images/welten/en1_big.jpg" alt="عودة التتار | السيرفر 1">
                    <div class="server-meta">
                        <span>العالم 1</span>
                        <span>اللاعبون: <b><?php echo $players_count1; ?></b></span>
                        <span>الحالة: <?php echo $serverStatus; ?></span>
                    </div>
                </div>
                <a href="register.php" class="btn-submit btn-register-link">سجل حسابك الان في العالم 1</a>
                <span class="Pulse redy">(الاحدث)</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="core-f/style-f/assets-index/fic.gif" alt="عودة التتار" width="40" height="40">
                <span>عودة التتار</span>
            </div>
            <div class="nav-links">
                <a href="login?terms" class="nav-link">قواعد اللعبة</a>
                <button class="btn-cta btn-login" onclick="showModal('login')">دخول</button>
                <button class="btn-cta btn-register" onclick="showModal('register')">سجل الان</button>
            </div>
            <button class="nav-toggle" id="navToggle" aria-label="القائمة">
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="nav-mobile" id="navMobile">
            <a href="login?terms" class="nav-link">قواعد اللعبة</a>
            <button class="btn-cta btn-login" onclick="showModal('login')">دخول</button>
            <button class="btn-cta btn-register" onclick="showModal('register')">سجل الان</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content animate-on-scroll">
            <h1>عودة التتار</h1>
            <p class="hero-subtitle">لعبة الحروب الاستراتيجية الاولى عربيا</p>
            <div class="hero-status">
                <div class="status-item">
                    <span class="status-label">العالم 1</span>
                    <span class="status-value"><?php echo $serverStatus; ?></span>
                </div>
            </div>
            <div class="hero-buttons">
                <button class="btn-hero btn-hero-register" onclick="showModal('register')">سجل الان</button>
                <button class="btn-hero btn-hero-login" onclick="showModal('login')">تسجيل الدخول</button>
            </div>
        </div>
    </header>

    <!-- Stats Bar -->
    <section class="stats-bar">
        <div class="container">
            <div class="stats-grid animate-on-scroll">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $players_count1; ?></span>
                    <span class="stat-label">عدد اللاعبين</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $active_players_count1; ?></span>
                    <span class="stat-label">اللاعبون النشطون</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $online_players_count1; ?></span>
                    <span class="stat-label">متصلون الان</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1</span>
                    <span class="stat-label">عدد السيرفرات</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text animate-on-scroll">
                    <h2 class="section-title">ماهي عودة التتار</h2>
                    <p class="about-desc">هي لعبة متصفح مجانية لاتحتاج الى تحميل من العاب الحرب الاستراتيجية. عالم مليء باللاعبين الحقيقيين الذين يبداون جميعهم كزعماء لقرى صغيرة.</p>
                    <p class="about-desc">في عودة التتار تبني المباني من الثكن الحربية والسفارات والمخازن، تتطور القرى الصغيرة لتصبح ممالك تتعرف بها على الحلفاء وتتحدى الاعداء.</p>
                    <ul class="feature-list">
                        <li>
                            <span class="feature-icon">&#9876;</span>
                            <span>ستبدا كرئيس قرية صغيرة</span>
                        </li>
                        <li>
                            <span class="feature-icon">&#9971;</span>
                            <span>ستبني قريتك وتطور مواردك وجيشك</span>
                        </li>
                        <li>
                            <span class="feature-icon">&#9760;</span>
                            <span>ستحارب مع او ضد لاعبين حقيقيين وتنضم لتحالف</span>
                        </li>
                    </ul>
                </div>
                <div class="about-image animate-on-scroll">
                    <img src="core-f/style-f/assets-index/newItems/imgs/greek.png" alt="عودة التتار" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Game Screenshots -->
    <section class="screenshots-section">
        <div class="container">
            <h2 class="section-title animate-on-scroll">صور من اللعبة</h2>
            <div class="screenshots-grid animate-on-scroll">
                <div class="screenshot-card">
                    <img src="core-f/style-f/assets-index/newItems/imgs/1.jpg" alt="خريطة القرية" loading="lazy">
                    <div class="screenshot-label">خريطة القرية</div>
                </div>
                <div class="screenshot-card">
                    <img src="core-f/style-f/assets-index/newItems/imgs/2.jpg" alt="الحقول" loading="lazy">
                    <div class="screenshot-label">الحقول</div>
                </div>
                <div class="screenshot-card">
                    <img src="core-f/style-f/assets-index/newItems/imgs/3.jpg" alt="الخريطة" loading="lazy">
                    <div class="screenshot-label">الخريطة</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Heroes Section -->
    <section class="heroes-section">
        <div class="container">
            <h2 class="section-title animate-on-scroll">ابطال الجولة</h2>
            <div class="heroes-grid animate-on-scroll">
                <div class="hero-card hero-attacker">
                    <div class="hero-card-icon">&#9876;</div>
                    <h3>افضل مهاجم</h3>
                    <?php if ($top_attacker): ?>
                        <div class="hero-name"><?php echo htmlspecialchars($top_attacker['name']); ?></div>
                        <div class="hero-points"><?php echo number_format($top_attacker['attack_points']); ?> نقطة هجوم</div>
                    <?php else: ?>
                        <div class="hero-name">لا يوجد بيانات</div>
                    <?php endif; ?>
                </div>
                <div class="hero-card hero-defender">
                    <div class="hero-card-icon">&#9737;</div>
                    <h3>افضل مدافع</h3>
                    <?php if ($top_defender): ?>
                        <div class="hero-name"><?php echo htmlspecialchars($top_defender['name']); ?></div>
                        <div class="hero-points"><?php echo number_format($top_defender['defense_points']); ?> نقطة دفاع</div>
                    <?php else: ?>
                        <div class="hero-name">لا يوجد بيانات</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <div class="news-card animate-on-scroll">
                <h2 class="section-title">اخر الاخبار</h2>
                <p>مرحبا بك في عالم عودة التتار, عالم مليء بالقوة والتنافس. هيا بنا قم بانشاء عضويتك وادخل المنافسة على الفوز بالمعجزة في اخر السيرفر وسجل اسمك في تاريخ ابطال عودة التتار.</p>
                <button class="btn-hero btn-hero-register" onclick="showModal('register')">سجل في اخر عالم</button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container animate-on-scroll">
            <h2>ابدا رحلتك الان</h2>
            <p>انضم الى الاف اللاعبين وابنِ امبراطوريتك</p>
            <div class="cta-buttons">
                <button class="btn-hero btn-hero-register" onclick="showModal('register')">سجل الان مجانا</button>
                <button class="btn-hero btn-hero-login" onclick="showModal('login')">لديك حساب؟ ادخل</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-brand">
                    <h2 class="footer-title">عودة التتار</h2>
                    <p class="footer-desc">لعبة الحروب الاستراتيجية الاولى عربيا</p>
                </div>
            </div>
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-sub-heading">صفحات مهمة</h3>
                    <ul>
                        <li><a href="index.php">الصفحة الرئيسية</a></li>
                        <li><a href="login?terms">قواعد اللعب</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-sub-heading">السيرفرات</h3>
                    <ul>
                        <li><a href="register.php" title="سجل حساب | عودة التتار 1">السيرفر 1</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-sub-heading">تسجيل الدخول</h3>
                    <ul>
                        <li><a href="login.php" title="سجل الدخول | عودة التتار 1">دخول السيرفر 1</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-sub-heading">حساب جديد</h3>
                    <ul>
                        <li><a href="register.php" title="سجل حساب | عودة التتار 1">تسجيل في السيرفر 1</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-images">
                <img src="core-f/style-f/assets-index/newItems/imgs/13.png" alt="عودة التتار" width="400" height="270" loading="lazy">
                <img src="core-f/style-f/assets-index/newItems/imgs/12.png" alt="عودة التتار" width="288" height="271" loading="lazy">
                <img src="core-f/style-f/assets-index/newItems/imgs/11.png" alt="عودة التتار" width="200" height="202" loading="lazy">
            </div>
            <div class="copyright">جميع الحقوق محفوظة &copy; عودة التتار <?php echo date('Y'); ?></div>
        </div>
    </footer>

    <script src="core-f/style-f/assets-index/Jq.js?s2"></script>
    <script>
        // Modal functions
        function showModal(type) {
            document.getElementById('modalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
            if (type === 'login') {
                document.getElementById('loginModal').classList.add('active');
                document.getElementById('registerModal').classList.remove('active');
            } else {
                document.getElementById('registerModal').classList.add('active');
                document.getElementById('loginModal').classList.remove('active');
            }
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('loginModal').classList.remove('active');
            document.getElementById('registerModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('modalBackdrop').addEventListener('click', closeModal);

        // Mobile nav toggle
        document.getElementById('navToggle').addEventListener('click', function() {
            document.getElementById('navMobile').classList.toggle('active');
            this.classList.toggle('active');
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
            observer.observe(el);
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>
