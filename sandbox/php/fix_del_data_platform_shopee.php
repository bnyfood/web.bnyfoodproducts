<?php
/**
 * PATCH DRAFT (sandbox only) — saletaxreport::del_data_platform Shopee branch
 *
 * Fix: ให้ DataDownload.shopee_orderlist_start_date เป็นเดือนที่ผู้ใช้เลือก
 * ไม่ใช่เดือนล่าสุดที่ลบลูปไปถึง
 *
 * วิธี A: วนจากเดือนปัจจุบันลงหาเดือนที่เลือก (รอบสุดท้าย = เดือนเป้าหมาย)
 * วิธี B: หลังลูป ตั้ง checkpoint ครั้งเดียวด้วย $inputDate (แนะนำถ้ามีเมธอดอัปเดตแยก)
 */

// ---------- วิธี A: reverse loop (แทนบล็อก shopee เดิมบรรทัด ~401-418) ----------
/*
}elseif($platform_del == 1){//shopee

	if(strlen($inputDate) == 4){

		$startDate = DateTime::createFromFormat('ym', $inputDate);
		if (!$startDate) {
			die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
		}
		$startDate->setTime(0, 0, 0);

		$cursor = new DateTime('first day of this month');
		$cursor->setTime(0, 0, 0);

		// ลบเดือนปัจจุบันก่อน ไล่ลงถึงเดือนที่เลือก
		while ($cursor >= $startDate) {
			$ym = $cursor->format('ym');
			$this->shopee_orders_model->delete_shopee_order_by_year_month($ym);
			$cursor->modify('-1 month');
		}
	}

}
*/

// ---------- วิธี B (แนะนำ): ลูปเดิม + ตั้ง checkpoint ท้ายสุดด้วยเดือนที่เลือก ----------
/*
}elseif($platform_del == 1){//shopee

	if(strlen($inputDate) == 4){

		$startDate = DateTime::createFromFormat('ym', $inputDate);
		if (!$startDate) {
			die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
		}
		$startDate->setTime(0, 0, 0);
		$intendedStart = clone $startDate; // เก็บเดือนที่ผู้ใช้เลือกไว้

		$currentDate = new DateTime('first day of this month');
		$currentDate->setTime(0, 0, 0);

		while ($startDate <= $currentDate) {
			$ym = $startDate->format('ym');
			$this->shopee_orders_model->delete_shopee_order_by_year_month($ym);
			$startDate->modify('+1 month');
		}

		// ทับค่าที่ SP ตั้งเป็นเดือนล่าสุด — ให้เป็นเดือนที่เลือกจริง
		$checkpoint = '20' . $intendedStart->format('y') . '-' . $intendedStart->format('m') . '-01';
		// ตัวอย่างถ้ามี model:
		// $this->DataDownload_model->set_shopee_orderlist_start_date($checkpoint);
		// หรือเรียก free_model / query UPDATE DataDownload ...
	}

}
*/
