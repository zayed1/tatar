<!DOCTYPE html>
<?php require_once("core-f/config-f/s1.php"); ?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة - عودة التتار</title>
    <link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Tajawal',Tahoma,sans-serif;background:#0d0d1a;color:#e8e8f0;text-align:center;min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:40px 20px;direction:rtl}
        .error-code{font-size:8rem;font-weight:900;background:linear-gradient(135deg,#c0392b,#f39c12);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;margin-bottom:10px}
        .error-icon{font-size:4rem;margin-bottom:20px}
        h2{font-size:1.8rem;font-weight:800;color:#fff;margin-bottom:12px}
        p{font-size:1.1rem;color:#9999aa;margin-bottom:30px;max-width:500px;line-height:1.8}
        .links{display:flex;flex-wrap:wrap;gap:12px;justify-content:center}
        .links a{display:inline-block;padding:12px 28px;border-radius:10px;font-weight:700;font-size:0.95rem;transition:all 0.3s}
        .link-primary{background:linear-gradient(135deg,#c0392b,#8e1a0e);color:#fff;box-shadow:0 4px 15px rgba(192,57,43,0.3)}
        .link-primary:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(192,57,43,0.4);color:#fff}
        .link-secondary{background:rgba(255,255,255,0.06);color:#e8e8f0;border:1px solid rgba(255,255,255,0.1)}
        .link-secondary:hover{background:rgba(255,255,255,0.1);color:#f39c12}
        .footer{margin-top:60px;color:#666;font-size:0.85rem}
    </style>
</head>
<body>
    <div class="error-icon">⚔️</div>
    <div class="error-code">404</div>
    <h2>المحارب ضاع في الطريق!</h2>
    <p>الصفحة التي تبحث عنها غير موجودة. ربما تم نقلها أو حذفها، أو أنك كتبت العنوان بشكل خاطئ.</p>
    <div class="links">
        <a href="index.php" class="link-primary">الصفحة الرئيسية</a>
        <a href="login.php" class="link-secondary">تسجيل الدخول</a>
        <a href="register.php" class="link-secondary">التسجيل</a>
        <a href="terms.php" class="link-secondary">دليل اللعبة</a>
    </div>
    <div class="footer">عودة التتار &copy; <?php echo date('Y'); ?></div>
</body>
</html>
