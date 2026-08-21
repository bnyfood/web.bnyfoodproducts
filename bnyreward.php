<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .campaign-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
        }

        .badge-live {
            background: #ff4d4d;
            color: white;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 15px; /* ลดลงเล็กน้อยเพื่อให้สมดุลกับรูปด้านล่าง */
        }

        h1 span {
            color: #ff4d4d;
        }

        /* ส่วนแสดงรูปภาพและรายละเอียดของรางวัลที่เพิ่มเข้ามาใหม่ */
        .prize-container {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .prize-image-wrapper {
            width: 100%;
            height: 260px;
            background: #fdf2ee; /* สีพื้นหลังจำลองกรณีไม่มีรูป */
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .prize-image-wrapper img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: none;
        }

        /* สัญลักษณ์จำลองรูปภาพ (ลบออกได้เมื่อใส่รูปจริง) */
        .prize-placeholder-icon {
            font-size: 50px;
            color: #ff7a45;
            opacity: 0.7;
        }

        .prize-detail {
            text-align: center;
        }

        .prize-title {
            font-size: 18px;
            font-weight: 600;
            color: #e63946;
            margin-bottom: 4px;
        }

        .prize-desc {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .steps-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 30px;
            text-align: left;
        }

        .step-item {
            display: flex;
            align-items: center;
            background: #fff8f5;
            padding: 18px 20px;
            border-radius: 14px;
        }

        .step-icon {
            background: #ff7a45;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            margin-right: 18px;
            flex-shrink: 0;
        }

        .step-text h3 {
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }

        .btn-register {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #ff7a45 0%, #ff4d4d 100%);
            color: white;
            text-decoration: none;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(255, 77, 77, 0.25);
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 77, 77, 0.35);
            background: linear-gradient(135deg, #ff4d4d 0%, #ff7a45 100%);
        }

        /* Responsive สำหรับหน้าจอมือถือ */
        @media (max-width: 480px) {
            h1 { font-size: 26px; }
            .campaign-container { padding: 30px 20px; }
            .step-text h3 { font-size: 16px; }
            .btn-register { font-size: 16px; padding: 14px 30px; }
            .prize-image-wrapper { height: 200px; }
        }
    </style>
</head>
<body>

    <div class="campaign-container">
        <div class="badge-live">
            <i class="fa-solid fa-fire-flame-curved"></i> แคมเปญพิเศษคืนกำไร
        </div>

        <h1><span>แจกจริงทุกสัปดาห์!</span></h1>

        <div class="prize-container">
            <div class="prize-image-wrapper">
                <i id="giftPlaceholderIcon" class="fa-solid fa-box-open prize-placeholder-icon"></i>
                <img id="latestGiftImage" src="" alt="ของรางวัลล่าสุด">
            </div>
            <div class="prize-detail">
                <div class="prize-title">🎁 ของรางวัลประจำสัปดาห์นี้</div>
                <div id="latestGiftDetail" class="prize-desc">กำลังโหลดข้อมูลรางวัลล่าสุด...</div>
            </div>
        </div>
        <div class="steps-wrapper">
            <div class="step-item">
                <div class="step-icon">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <div class="step-text">
                    <h3>ทุกๆ 500 บาทรับ 1 คะแนน</h3>
                </div>
            </div>

            <div class="step-item">
                <div class="step-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div class="step-text">
                    <h3>ประกาศผลทุกวันจันทร์</h3>
                </div>
            </div>
        </div>

        <a href="https://www.bnyfoodproducts.com/social_login" class="btn-register" target = "_blank">
            <i class="fa-solid fa-user-plus"></i> ลงทะเบียน/เช็คผลรางวัล
        </a>
    </div>

</body>
<script>
$(function () {
    $.ajax({
        url: '/marketing/crm/bnyadminreward/get_gift_lasted',
        method: 'GET',
        dataType: 'json'
    }).done(function (res) {
        if (!res || res.status !== true) {
            $('#latestGiftDetail').text('ไม่พบข้อมูลรางวัลล่าสุด');
            return;
        }

        var detail = (res.gift_detail || '').trim();
        $('#latestGiftDetail').text(detail !== '' ? detail : '-');

        var picUrl = (res.gift_pic_url || '').trim();
        if (picUrl !== '') {
            $('#latestGiftImage').attr('src', picUrl).show();
            $('#giftPlaceholderIcon').hide();
        }
    }).fail(function () {
        $('#latestGiftDetail').text('ไม่สามารถโหลดข้อมูลรางวัลได้');
    });
});
</script>
</html>