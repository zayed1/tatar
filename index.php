<!DOCTYPE html
<?
  function TimeAgo($diff_in_unix){
  if ($diff_in_unix > 3600){
  $diff .= intval($diff_in_unix/3600); 
  $diff_in_unix = $diff_in_unix%3600;
  }else{ $diff .= '00'; }
  if($diff_in_unix > 60 AND $diff_in_unix < 3600){
  $diff .= ":".intval($diff_in_unix / 60);
  $diff_in_unix = $diff_in_unix%60;
  }else{ $diff .= ':00'; }
  if ($diff_in_unix < 60 AND $diff_in_unix > 0){
  $diff .= ":".$diff_in_unix;
  }
  return $diff;
  }
$x = 0;
require_once( "core-f/config-f/s1.php" );
$db_port = isset($AppConfig['db']['port']) ? intval($AppConfig['db']['port']) : 3306;
$link = mysqli_connect($AppConfig['db']['host'], $AppConfig['db']['user'], $AppConfig['db']['password'], $AppConfig['db']['database'], $db_port) or die(mysqli_connect_error());

$result = mysqli_query($link, "SELECT * FROM p_queue WHERE id = 1 and proc_type= 24");
// Fetch row as associative array
$row = $result ? mysqli_fetch_assoc($result) : null;
// Access data in row
$end_date = $row ? $row["end_date"] : '';

$fetch = mysqli_query($link, "SELECT * FROM p_queue WHERE id = 3 and proc_type= 57");
// Fetch row as associative array
$fetchs = $fetch ? mysqli_fetch_assoc($fetch) : null;

$redseahost = $AppConfig['system']['server_days'];
// Subtract 10 days from the end date
$subtracted_date = date('Y-m-d H:i:s', strtotime("-$redseahost days", strtotime($end_date)));
$subtracted_date4 = date('Y-m-d H:i:s', strtotime("-3 hours", strtotime($subtracted_date)));

// calculate the difference in seconds between the subtracted date and now
$diff_in_seconds = strtotime($subtracted_date4) - time();


// if the difference is negative, get the absolute value
$diff_in_seconds = abs($diff_in_seconds);

// calculate the remaining days, hours, minutes and seconds
$remaining_days = floor($diff_in_seconds / 86400);
$remaining_hours = floor(($diff_in_seconds % 86400) / 3600);

// format the remaining time as a string
$remaining_time = sprintf('%02d:%02d', $remaining_days, $remaining_hours);

$q = mysqli_query ($link, "SELECT * FROM g_summary");
$sessionTimeoutInSeconds = 9000 * 60;
$g = mysqli_query ($link, "SELECT COUNT(*) FROM p_players WHERE TIME_TO_SEC(TIMEDIFF(NOW(), last_login_date)) <= ".$sessionTimeoutInSeconds."");
$g = $g ? mysqli_fetch_row ($g) : array(0);
$r = $q ? mysqli_fetch_assoc ($q) : array('players_count' => 0, 'active_players_count' => 0);
$online1 = floor((TimeAgo(time() - strtotime(date($AppConfig['system']['server_start'] )))/24));
$online_before1 = floor((TimeAgo(strtotime($AppConfig['system']['server_start']) - time())/24));
$players_count1 = $r["players_count"];
$active_players_count1 = $r['active_players_count'];
$online_players_count1 = $g[0];    
$x +=1;
?>
<?

?>
<html lang="ar">
   <head>
        <title>عودة التتار - TatarwWar | F-V7-EX-LTS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta property="og:title" content="عودة التتار - TatarwWar">
        <meta property="og:image" itemprop="image" content="core-f/style-f/assets-index/icons-1.jpg">
        <meta property="og:type" content="website">
        <meta property="og:description" content="عودة التتار هي لعبة استراتيجية بالكامل ، فهي تعتمد بشكل كلي على التخطيط والتدبير ووضع الخطط الحربية الدقيقة بالتعاون مع تحالفك حتى تتمكن من الفوز بالمعجزة.">
        <meta name="description" content="عودة التتار هي لعبة استراتيجية بالكامل ، فهي تعتمد بشكل كلي على التخطيط والتدبير ووضع الخطط الحربية الدقيقة بالتعاون مع تحالفك حتى تتمكن من الفوز بالمعجزة.">
        <meta http-equiv="cache-control" content="max-age=0">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="imagetoolbar" content="no">
        <meta name="content-language" content="ar">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#d7f2ff" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#d7f2ff" media="(prefers-color-scheme: dark)">
    	<meta name="keywords" content="عودة التتار،سيرفر عودة التتار،لعبة عودة التتار،حرب ترافيان،عودة التتار،عودة التتار السريع،عودة التتار السعودى،سيرفر عودة التتار الرسمى،حرب ترافيان،لعبه حرب ترافيان،سيرفر عودة التتار،لعبه متصفح عودة التتار،سيرفرات عودة التتار السريعة،حرب،التتار،الجرمان،المغول،العرب،الرومان،التحف،المعجزة">
    	<meta name="mobile-web-app-capable" content="yes">
    	<meta name="apple-mobile-web-app-title" content="عودة التتار">
    	<meta property="og:site_name" content="عودة التتار">
    	<meta property="og:url" content="">
    	<meta name="apple-mobile-web-app-status-bar-style" content="white">
    	<meta name="theme-color" content="#ffffff">
    	<meta name="twitter:card" content="summary">
        <meta name="twitter:site" content="@">
        <meta name="twitter:title" content="لعبة عودة التتار">
        <meta name="twitter:description" content="عودة التتار هي لعبة استراتيجية بالكامل ، فهي تعتمد بشكل كلي على التخطيط والتدبير ووضع الخطط الحربية الدقيقة بالتعاون مع تحالفك حتى تتمكن من الفوز بالمعجزة.">
        <meta name="twitter:image" content="core-f/style-f/assets-index/IC-3.png">
        <meta name="twitter:image:alt" content="عودة التتار">
        <meta content="1200" property="og:image:width">
        <meta content="630" property="og:image:height">
        <link rel="canonical" href="">
    	<link rel="shortcut icon" href="core-f/style-f/assets-index/fic.gif" type="image/x-icon">
        <link rel="apple-touch-icon" href="core-f/style-f/assets-index/IC-3.png">
        <link rel="preload" as="image" href="core-f/style-f/assets-index/images/Home.png">
        <link rel="stylesheet" href="core-f/style-f/assets-index/newItems/style.css?1.3">
        <noscript>متصفحك لايدعم الجافاسكربت</noscript>
   </head>
   <body>
    <div class="overlay">
        <div class="mask closer"></div>
        <div id="ovarlay-holder" class="ovarlay-holder">
            <div id="login-bar">
                <div class="bar-title">
                    <h3>أختر سيرفر للدخول</h3>
                    <span class="x">x</span>
                </div>
                <div class="servers-list">
                     
                    <div class="server-details">
                    <a href="login.php" title="سجل الدخول | عودة التتار 1" class="server-href">
                            <img src="core-f/style-f/assets-index/images/welten/en1_big.jpg" alt="عودة التتار | السيرفر 1" title="عودة التتار | السيرفر 1">
                            <div class="server-data">
                                <div class="grid">
                                   <span> اللاعبون : <b><?php echo $players_count1; ?> لاعب</b></span>
                                   <span> منذ : <b><?php
if (($result ? mysqli_num_rows($result) : 0) > 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0) {
  echo "<b>$remaining_days</b> أيام و <b>$remaining_hours</b> ساعة";
}
else if (($result ? mysqli_num_rows($result) : 0) > 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='blue'>لم تبدأ بعد</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='red'>انتهت الجولة</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0)
{
  echo "<font color='red'>بانتظار الإعادة</font>";
}
else
{
  echo "<font color='red'>تم نزول التتار</font>";
}
  ?></b></span>
                                </div>
                            </div>
                        </a>
                        
                			<span class="Pulse redy">(الاجدد)</span>
                			
                    </div>
                    
                    
                    
                </div>
            </div>
            
            <div id="register-bar">
                <div class="bar-title">
                    <h3>أختر سيرفر للتسجيل</h3>
                    <span class="x">x</span>
                </div>
               
                    <div class="server-details">
                        <a  href="register.php"  title="سجل حساب | عودة التتار 1" class="server-href">
                            <img src="core-f/style-f/assets-index/images/welten/en1_big.jpg" alt="عودة التتار | السيرفر 1" title="عودة التتار | السيرفر 1"  >
                            <div class="server-data">
                                <div class="grid">
                                   <span> اللاعبون : <b><?php echo $players_count1; ?> لاعب</b></span>
                                   <span> منذ : <b><?php
if (($result ? mysqli_num_rows($result) : 0) > 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0) {
  echo "<b>$remaining_days</b> أيام و <b>$remaining_hours</b> ساعة";
}
else if (($result ? mysqli_num_rows($result) : 0) > 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='blue'>لم تبدأ بعد</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='red'>انتهت الجولة</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0)
{
  echo "<font color='red'>بانتظار الإعادة</font>";
}
else
{
  echo "<font color='red'>تم نزول التتار</font>";
}
?>					</b></span>
                                </div>
                            </div>
                        </a>
                        
                			<span class="Pulse redy">(الاجدد)</span>
                			
                    </div>
                    
                    
                    

                    
                </div>
            </div>
        </div>
    </div>
      <header>
        
         <ul id="nav">
            <li><b onclick="showbar(1)">تسجيل</b></li>
            <li><b onclick="showbar(2)">دخول</b></li>
            <li><a href="login?terms">قواعد اللعبة</a></li>
         </ul>
         <div class="world">
            <a href="register.php">
            <span class="world-title">العالم 1</span>
            <span class="world-reg">بدأ منذ <?php
if (($result ? mysqli_num_rows($result) : 0) > 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0) {
  echo "<b>$remaining_days</b> أيام و <b>$remaining_hours</b> ساعة";
}
else if (($result ? mysqli_num_rows($result) : 0) > 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='blue'>لم تبدأ بعد</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0  && ($fetch ? mysqli_num_rows($fetch) : 0) > 0)
{
  echo "<font color='red'>انتهت الجولة</font>";
}
else if (($result ? mysqli_num_rows($result) : 0) == 0 && ($fetch ? mysqli_num_rows($fetch) : 0) == 0)
{
  echo "<font color='red'>بانتظار الإعادة</font>";
}
else
{
  echo "<font color='red'>تم نزول التتار</font>";
}
?>	</span>
            </a>
         </div>
         
      </header>
      <main>
         <div class="flex">
            <div class="right">
               <h1 class="title">ماهي عودة التتار</h1>
               <h2 class="h3">هي لعبة متصفح مجانية لاتحتاج إلى تحميل من العاب  الحرب الاستراتيجية</h2>
               <h3 class="h3">وهي عبارة عن لعبة حرب في عالم مليء باللاعبين الحقيقين الذين يبدأون جميعهم كزعماء لقرى صغيرة.</h3>
               <h3 class="h3">في عودة التتار تبني المباني من الثكن الحربية والسفارات والمخازن تتطور القرى الصغيرة لتصبح ممالك تتعرف بها على الحلفاء وتتحدى الأعداء </h3>
               <div class="In_Co flex-3">
                  <table>
                     <tbody>
                        <tr>
                           <td>عدد اللاعبين:</td>
                           <td id="AllPNu"><?php echo $players_count1 + $players_count2 + $players_count3 + $players_count4 + $players_count5; ?></td>
                        </tr>
                        <tr>
                           <td>اللاعبون النشطون:</td>
                           <td id="AllPNuA"><?php echo $players_count1 + $players_count2 + $players_count3 + $players_count4 + $players_count5; ?></td>
                        </tr>
                        <tr>
                           <td>عدد السيرفرات:</td>
                           <td id="AllSNu">1</td>
                        </tr>
                     </tbody>
                  </table>
                  <div class="sub-imgs">
                    <div class="x1"><img src="core-f/style-f/assets-index/newItems/imgs/greek.png" alt="عودة التتار الرسمي" srcset="core-f/style-f/assets-index/newItems/imgs/greek.png" width="200" height="120"></div>
                  </div>
               </div>
              
               <h2>عن لعبة عودة التتار :</h2>
               <ul>
                  <li>ستبدأ كرئيس قرية صغيرة</li>
                  <li>ستبني قريتك وتطور مواردك وجيشك</li>
                  <li>ستحارب مع أو ضد لاعبين حقيقين وتنضم لتحالف</li>
               </ul>
            </div>
            <div class="phone-only"> 

                <a href="register.php" style="display: block"><h2 class="C_O">سجّل في آخر عالم</h2></a>
            <div class="server-details">
                        <a href="register.php" title="سجل الدخول | عودة التتار 1" class="server-href">
                            <img src="core-f/style-f/assets-index/images/welten/en1_big.jpg" alt="عودة التتار | السيرفر 1" title="عودة التتار | السيرفر 1">
                            <div class="server-data">
                                <div class="grid">
                                   <span> اللاعبون : <b><?php echo $players_count1; ?> لاعب</b></span>
                                   <span> منذ : <b><?php
										if ($AppConfig['system']['server_start'] < date('Y/m/d H:i:s') && $online1 != 0){
										    echo"<b>$online1 يوم</b>";
										}
					                    else if ($AppConfig['system']['server_start'] < date('Y/m/d H:i:s') && $online1 == 0){
					                        echo "<b><font color='red'>اليوم</font></b>";
					                    }
					                    else if ($online_before1 == 0 && $AppConfig['system']['server_start'] > date('Y/m/d H:i:s')){
					                        echo "<b><font color='red'>اليوم</font></b>";
					                    }
										else
										{
										   if ($AppConfig['system']['server_start'] > date('Y/m/d H:i:s'))
										   {
                                            echo"<font color='red'><b>لم يبداء </b></font>";  
                                           }
										   else
										   {
										   echo"<b>$online_before1</b> يوم";										
									       }
									   	}
										?></b></span>
                                </div>
                            </div>
                        </a>
                        <span class="Pulse redy">(الاجدد)</span>
                    </div>
               </div>
            <div class="left">
               <table class="GaIm diL">
                  <tbody>
                     <tr>
                        <th>صور من اللعبة </th>
                     </tr>
                     <tr>
                        <th class="Ga_i">
                           <div class="index_1">
                              <img src="core-f/style-f/assets-index/newItems/imgs/1.jpg" alt="خريطة القرية" width="98" height="71">
                           </div>
                           <div class="index_2">
                              <img src="core-f/style-f/assets-index/newItems/imgs/2.jpg" alt="الحقول" width="98" height="71">
                           </div>
                           <div class="index_3">
                              <img src="core-f/style-f/assets-index/newItems/imgs/3.jpg" alt="الخريطة" width="98" height="71">
                           </div>
                        </th>
                     </tr>
                     <tr>
                        <th class="flex-2">الأخبار وشروحات <span class="inf diL" id="NewsdT">حدّث منذ 18 شهر</span></th>
                     </tr>
                     <tr>
                        <th id="News">مرحبا بك في عالم عودة التتار, عالم مليئ بالقوة والتنافس هيا بنا قم بانشاء عضويتك و ادخل المنافسة على الفوز بالمعجزة في اخر السيرفر و سجل اسمك في تاريخ أبطال عودة التتار </th>
                     </tr>
                     <tr>
                        <th><button class="sign-up"><a href="register.php"  title="سجل حساب | عودة التتار ">سجل في أخر عالم</a></button></th>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </main>
      <footer>
       <div class="footer-container">
        <h2 class="footer-title">
            عودة التتار - Retatar
        </h2>
        
        <div class="footer-grid">
            <div class="footer-pages">
                <h3 class="footer-sub-heading">صفحات قد تهمك</h3>
                <ul>
                    <li><a href="index.php">الصفحة الرئيسية</a></li>
                    <li><a href="login?terms">قواعد اللعب</a></li
                </ul>
            </div>
            <div class="footer-servers">
                <h3 class="footer-sub-heading">خريطة السيرفرات</h3>
                <ul>
                    
                    <li><a href="register.php" title="سجل حساب | عودة التتار 1"> السيرفر 1</a></li>
                    
                    
                    
                </ul>
            </div>
            <div class="footer-signin">
                <h3 class="footer-sub-heading">سجل الدخول</h3>
                <ul>
                    
                    <li><a href="login.php" title="سجل حساب | عودة التتار 1"> سجل الدخول | السيرفر  1</a></li>
                    
                    
                    
                </ul>
            </div>
            <div class="footer-signup">
                <h3 class="footer-sub-heading">سجل معنا </h3>
                <ul>
                    
                    <li><a href="register.php" title="سجل حساب | عودة التتار 1"> سجل حساب  | السيرفر  1</a></li>
                    
                   
                    
                </ul>
            </div>
           
        </div>

        <div class="copywrites">جميع الحقوق محفوظة@ عودة التتار سمارت سيرفس 2024 </div>
        <div class="flex-footer-imgs">
            <img src="core-f/style-f/assets-index/newItems/imgs/13.png" alt="عودة التتار | Greek War" srcset="core-f/style-f/assets-index/newItems/imgs/13.png" width="400" height="270">
            <img src="core-f/style-f/assets-index/newItems/imgs/12.png" alt="عودة التتار | Greek War" srcset="core-f/style-f/assets-index/newItems/imgs/12.png"  width="288" height="271">
            <img src="core-f/style-f/assets-index/newItems/imgs/11.png" alt="عودة التتار | Greek War" srcset="core-f/style-f/assets-index/newItems/imgs/11.png" width="200" height="202">

        </div>
       </div>
      </footer>
      <script src="core-f/style-f/assets-index/Jq.js?s2"></script>
      <script>
        function showbar(e){
            $('.overlay').fadeIn();
            if (e == 1){
                $("#login-bar").hide();
                $("#register-bar").fadeIn();
                }
                else if (e == 2){
                    $("#register-bar").hide();
                    $("#login-bar").fadeIn();
                }
        }
        $(function(){
            $('.mask.closer,span.x').bind('click', function(e) {
               
                    if(!$(e.target).is('#ovarlay-holder')) {
                        $('.overlay').fadeOut();
                    }
            });
        })
     </script>
   </body>
</html>