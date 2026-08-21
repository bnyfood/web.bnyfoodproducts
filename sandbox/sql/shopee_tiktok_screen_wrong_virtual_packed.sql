-- Screen only: Shopee PROCESSED / TikTok Packet that look like a WRONG virtual pack.
-- Do not delete from this file.
--
-- Passed pack does not count PROCESSED / Packet themselves (those may be virtual).
-- Requires dbo.fn_shopee_order_passed_pack and dbo.fn_tiktok_order_passed_pack
-- (created by shopee_tiktok_delete_wrong_virtual_packed.sql).

USE [bnyfoodproducts];
GO

SELECT
	o.OrderID,
	o.order_sn,
	o.create_time,
	o.update_time,
	o.order_status,
	o.total_amount
FROM dbo.shopee_orders o
WHERE o.order_status = 'PROCESSED'
	AND dbo.fn_shopee_order_passed_pack(o.order_sn) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.shopee_orders z
		WHERE z.order_sn = o.order_sn
			AND z.order_status IN ('CANCELLED', 'UNPAID', 'INVOICE_PENDING')
	)
ORDER BY o.create_time, o.order_sn;

SELECT
	o.tiktok_orders_id,
	o.order_id,
	o.create_time,
	o.update_time,
	o.status,
	o.tracking_number
FROM dbo.tiktok_orders o
WHERE o.status = 'Packet'
	AND dbo.fn_tiktok_order_passed_pack(CONVERT(varchar(64), o.order_id)) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.tiktok_orders z
		WHERE z.order_id = o.order_id
			AND z.status IN ('CANCELLED', 'Canceled', 'UNPAID', 'ON_HOLD')
	)
ORDER BY o.create_time, o.order_id;
GO
