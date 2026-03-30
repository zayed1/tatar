<?php
$AppConfig = array (
        'db'                                   => array (
                'host'                         => 'gondola.proxy.rlwy.net',
                'port'                         => 28295,
                'user'                         => 'root',
                'password'                     => 'AXFOnRCygdQiDloCXxQNnvQxnPqsySvu',
                'database'                     => 'railway',
                   ),
		            'paylink' => array (
                  	'apiId' => 'pay-link-id-here',
                  	'secretKey' => 'paylink-key-here'
                ),
                'Game'                         => array (		
				// اعدادات اللعبه
                'speed'                        => '100',//سرعة اللعبه
                'speed_t'                      => '100',//سرعه توقيت تدريب القوات
                'speed_b'                      => '100',//سرعه توقيت بناء المباني
                'dev_troop_to_20'              => '1000',//ذهب افران صهر الحديد بناء الى 20
                'WWW'                          => '100',//مضروب مووارد بناء المعجزه
                'WWW2'                         => '25',// مضروب توقيت بناء المعجزه
                'map'                          => '401',//حجم الخريطه
				'tid_npc'                => '100',  ///مبادلة القوات بالسوق							
                'attack'                       => '30',//100سرعه الهجوم يفضل
                'protection'                   => '24',// الحمايه بالساعات
                'protectionx'                  => '0',//تدبيل الحمايه
                'X'                            => '1328827643',//غير مهم يرجى تركه هكذا
                'capacity'                     => '200', // المخازن يفضل 125
                'cranny'                       => '50', // المخابأ
                'cp'                           => '100',//ولاء القريه الجديده
				'auto_training_t'              => '120', // زمن التدريب التلقائي
				'auto_training_g'              => '120', // عدد التدريب التلقائى
                'market'                       => '50', // حمولة التجار
                'plusoases'                    => '1000',// ذهب شراء الواحات
                'plusoases_count'              => '30',// اقصى عدد من الواحات يمكن شراؤه
                'backtroops'        	       => '1000',		 // ارجاع التعزيزات
                'speedmarket'                  => '500', // سرعه التجار
		        'hero_gold'                    => '5000', // انهاء البطل فورا
                //البلاس
                'plus1'                        => '1',//مده بلاس بالايام
                'plus2'                        => '1',//مده الزياده بالايام
                'plus3'                        => '150',//قائمة بلاس
                'plus4'                        => '20',// زياده بلاس
                'plus5'                        => '5',//انهاء المباني فورأ
                'plus6'                        => '5',//تاجر مبادله
                'plus7'                        => '35',//انهاء تدريب القوات فورأ
                'plus9'                        => '1000',//انهاء التعزيزات فورأ
                'plus_by_abdullah_1_1'         => '1',  // المدة
                'plus_by_abdullah_1_2'         => '200', // +20% قوة الهجوم
                'plus_by_abdullah_2_1'         => '1', // المدة
                'plus_by_abdullah_2_2'         => '200', // +20% قوة الدفاع
                'plus_by_abdullah_3_1'         => '1', // المدة
                'plus_by_abdullah_3_2'         => '200', // +20% قوة المباني ضد المقاليع
                'plus_by_abdullah_4_1'         => '1', // المدة
                'plus_by_abdullah_4_2'         => '200', //+30% سرعة القوات
                'plus_by_abdullah_5_1'         => '1', // المدة
                'plus_by_abdullah_5_2'         => '200', // +20% سرعة الإنتاج
                'plus_by_abdullah_6_1'         => '1', // المدة
                'plus_by_abdullah_6_2'         => '100', // +20% زيادة حمولة القوات
                  
                  ///مميزات جديدة
                  'jozr'                       => '0',      // ايقاف وتشغيل الجزر  
                  'numpro'                     => '12',      //عدد ساعات حماية 
                 '8_cost'                     => '1000',      //تكلفة حماية بالذهب لكل مره 
                    'upes'                     => '15',      //عدد مرات شراء حماية 
                  'village_type_new_oasis'      => '100',      //تكلفة مهندس الواحات
                  'sooq'      => '1',      //ايقاف ارسال موارد 
                   'wa7at'     => '150', //زياددة عدد واحات خارطه 
 
                  
                  
                  
                'berq_time'     => '3600', //وقت جزيرة الكنز
                'berq_gold'     => '1000', //ذهب جزيرة الكنز
               
				//مميزات خاصة //
                'activitebuytroops'            => '0',// 1 سوق المحاربين شغال 0 تعطيل
                'plus7_on'                     => '1',
                'activitebuyres'               => '0',// 1 شراء الموارد شغال 0 تعطيل
                'res'                          => '25000',// 1 شراء الموارد شغال 0 تعطيل
				'mkale'                        => '10', // المقاليع 10 للتشغيل 21 للايقاف
				'link'                         => '1',// ربح الذهب  1 يعمل 0 موقوف
				'silver'                       => '0',// الفضة  1 يعمل 0 موقوف
				'animal'                       => '1',// الهدية اليومية  1 يعمل 0 موقوف
				'hdya'                         => '1',// الهدية اليومية  1 يعمل 0 موقوف
				'copon'                        => '1',// كوبون الذهب 1 يعمل 0 موقوف
          //______ اسعار الجنود فى سوق المحاربين _______//
		// قبيلة العرب
		'araab'                => '0.011', // رامي السهام
		'araab1'               => '0.0127', // محارب بشوكه
		'araab2'               => '0.0153', // الحارس 
		'araab3'                   => '0.0099', // طائر التجسس
		'araab4'                => '0.0327', // فارس العرب
		'araab5'              => '0.0485', //فارس الفؤوس
		'araab6'              => '0.05', //الكبش 
		'araab7'              => '0.074', //المنجنيق الناري
		// قبيلة الرومان
		'romaan'                => '0.008', // جندى أول الرومان
		'romaan1'               => '0.0076', // حراس الأمبراطور
		'romaan2'               => '0.011', // جندى مهاجم
		'romaan3'               => '0.0066', // فرقة التجسس
		'romaan4'                   => '0.025', // سلاح الفرسان
		'romaan5'                => '0.0397', // فرسان القيصر
		'romaan6'                => '0.0335', // الكبش
		'romaan7'              => '0.0548', //المقلاع
		// قبيلة الأغريق
		'greeec'                => '0.0057', // الكتيبه
		'greeec1'               => '0.0098', // المبارز
		'greeec2'               => '0.0069', // المستكشف
		'greeec3'                   => '0.0176', // رعد الاغريق
		'greeec4'                => '0.0199', // فرسان السلت
		'greeec5'              => '0.0360', //فرسان الهيدوانر
		'greeec6'              => '0.0350', //محطمة الابواب
		'greeec7'              => '0.0573', //المقلاع الحربي
		// قبيلة الجرمان
		'germaan'                => '0.0045', // مقاتل بهراوة
		'germaan1'               => '0.0062', // مقاتل برمح
		'germaan2'               => '0.00898', // مقاتل بفاس
		'germaan3'                   => '0.0066', // الكشاف
		'germaan4'                => '0.0184', // مقاتل القيصر
		'germaan5'              => '0.0279', //فرسان الجرمان
		'germaan6'              => '0.0315', //محطمة الابواب
		'germaan7'              => '0.0506', //المقلاع


		// قبيلة الفرس
		'piyadeh'               => '0.006',
		'piyadeh1'              => '0.009',
		'piyadeh2'              => '0.012',
		'savareh'               => '0.020',
		'shovalieh'             => '0.030',
		// قبيلة المغول
		'mgol'                  => '0.005',
		'mgol1'                 => '0.007',
		'mgol2'                 => '0.010',
		'mgol3'                 => '0.007',
		'mgol4'                 => '0.020',
		'mgol5'                 => '0.030',
		'mgol6'                 => '0.035',
		'mgol7'                 => '0.055',

                //امـور عامه
                'pepolegold'                   => '1500',//سكان استلام الذهب لكسب الذهب
		'gift_silver'       	=> '0', 			//عدد الفضة
                'setgold'                      => '10000',//الذهب المعطى لكسب الذهب
                'bonous'                       => '200',//غير مهم
                'osas'                         => '75',// الواحات العاديه
		'hero_gold'                    =>  3000, // سعر انهاء البطل
                'osasX'                        => '150',// الواحات الكبيره القمح
                'online'                       => '222422',//المتصلين خروجهم بعد
                'freegold'                     => '250',//ذهب مجاني عند التسجيل
                'Warrior'                     => '0',//ذهب مجاني عند التسجيل

                'freegoldweek'                 => '0',//ذهب مجاني أسبوعي
                'awsmh'                        => '432000',// مده توزيع الاوسمه بالثواني 
                'RegisterOver'                 => '100',//مدة اغلاق التسجيل عدد الايام
                'farmtime'                     => '10',// مدة الانتظار لارسال هجمة المزرعة من جديد
        ),
        'page'                                 => array (
                'ar_title'                     => 'عودة التتار',
                'asset_version'                => 'Nhjkh1ka111alcMfks'
        ),
         'system'                               => array (
                'blocklistword'                => array( "goo.gl", "tatar4", "acunetix", "SomeCustomInjectedHeader", "0x31", "UNION", "' or", "NdcMasterLog", "
                game", "InstallGame", "iron-sy", "elaml.org", "lionab"),
                'adminName'                    => 'admin', //admin
                'adminPassword'                => '4297f44b13955235245b2497399d7a93',//باس الادمن

                'tatar'                        => '10',//مده نزول التتار بالايام
                'artef'                        => '8',//مده نزول التحف بالايام
                'start'                        => '',//مده العداد بالايام
                'endin'                        => '1',//مده اعاده سيرفر عودة التتار بالساعات
			    'server_days'           => '10',//وقت فتح السيرفر بالتاريخ ليكون فى رئيسية الموقع
				
			
				'moda'           => '10',//مدة السيرفر
				'tat'           => '10',//عدد ايام خروج التتار
				'wqt'           => '02/26/2023',//وقت بداء السيرفر
				'sasa'           => '10:00',//ساعة بداء السيرفر

                'lang'                         => 'ar',//اللغه
                'admin_email'                  => 'smartservs.com@gmail.com',//الايميل
                'webname'                      => 'عودة التتار',
                'email'                        => 'smartservs.com@gmail.com',//المرسل
                'namesite'                     => 'Retatar',//اسم الموقع انجليزي
                'linksite'                     => 'core-f/style-f/',//رابط الصور
                'installkey'                   => 'inst000123213all9',//رمز التسطيب

),

       'plus'                                  => array (
                'packages'                     => array (

	array(

				'name'		=> 'الأولى',

				'gold'		=> 3600,

				'goldplus'	=> 3600,

				'plus'		=> 0,

				'cost'		=> 3.20,

				'costplus'	=> 3.20,

				'currency'	=> 'usd',

				'image'		=> 'package_a.webp'

			),

			array(

				'name'		=> 'الثانية',

				'gold'		=> 7200,

				'goldplus'	=> 7300,

				'plus'		=> 1,

				'cost'		=> 6.39,

				'costplus'	=> 6.39,

				'currency'	=> 'usd',

				'image'		=> 'package_b.webp'

			),

			array(

				'name'		=> 'الثالثة',

				'gold'		=> 14400,

				'goldplus'	=> 14850,

				'plus'		=> 3,

				'cost'		=> 12.78,

				'costplus'	=> 12.78,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),

			array(

				'name'		=> 'الرابعة',

				'gold'		=> 25000,

				'goldplus'	=> 27050,

				'plus'		=> 6,

				'cost'		=> 22.63,

				'costplus'	=> 22.63,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),

			array(

				'name'		=> 'الخامسة',

				'gold'		=> 57000,

				'goldplus'	=> 62700,

				'plus'		=> 10,

				'cost'		=> 50.59,

				'costplus'	=> 50.59,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),

			array(

				'name'		=> 'السادسة',

				'gold'		=> 117000,

				'goldplus'	=> 133400,

				'plus'		=> 14,

				'cost'		=> 103.85,

				'costplus'	=> 103.85,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'


			),
			array(

				'name'		=> 'السابعة',

				'gold'		=> 240000,

				'goldplus'	=> 283200,

				'plus'		=> 18,

				'cost'		=> 213,

				'costplus'	=> 213,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),

			array(

				'name'		=> 'الثامنة',

				'gold'		=> 480000,

				'goldplus'	=> 580800,

				'plus'		=> 21,

				'cost'		=> 426,

				'costplus'	=> 426,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),

			array(

				'name'		=> 'التاسعة',

				'gold'		=> 750000,

				'goldplus'	=> 937500,

				'plus'		=> 25,

				'cost'		=> 665,

				'costplus'	=> 665,

				'currency'	=> 'usd',

				'image'		=> 'package_c.webp'

			),
			array(

				'name'		=> 'عرض خاص 1',

				'gold'		=> 27050,

				'goldplus'	=> 40575,

				'plus'		=> 50,

				'cost'		=> 22.63,

				'costplus'	=> 22.63,

				'currency'	=> 'usd',

				'image'		=> 'offer.webp'

			),
			array(

				'name'		=> 'عرض خاص 2',

				'gold'		=> 283200,

				'goldplus'	=> 495600,

				'plus'		=> 75,

				'cost'		=> 213,

				'costplus'	=> 213,

				'currency'	=> 'usd',

				'image'		=> 'offer.webp'

			),
			array(

				'name'		=> 'عرض خاص 3',

				'gold'		=> 937500,

				'goldplus'	=> 1968750,

				'plus'		=> 110,

				'cost'		=> 426,

				'costplus'	=> 426,

				'currency'	=> 'usd',

				'image'		=> 'offer.webp'

			),

		),

                'payments'                     => array (

                        'paypal'               => array (

                                'testMode'     => false,

                                'name'         => 'PayPal',
 
                                'image'        => 'PayPal-logo-1.webp',

				'merchant_id'	=> '',

				'currency'		=> 'USD'

                        ),
                        'paylink'	=> array (
				'testMode'		=> false,
				'name'			=> 'Visa | Mastercard | Mada',
				'image'			=> 'paylink.webp',
				'merchant_id'	=> '',
				'currency'		=> 'USD'
				),
                        'apple_pay'	=> array (
				'testMode'		=> false,
				'name'			=> 'Apple Pay',
				'image'			=> 'apple.webp',
				'merchant_id'	=> '',
				'currency'		=> 'USD'
				)



                )

        )



);

?>