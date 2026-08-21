-- One-time screen: Lazada packed rows that are likely a WRONG virtual pack.
-- SELECT / function only. Do not delete from this file.
--
-- Passed pack is NOT decided by packed.updated_at vs created_at.
-- Scheduled-task insert time can drift from order created_at.
--
-- dbo.fn_lazada_order_passed_pack = 1 when the order really went past pack:
--   1) another lazada_orders row has an after-pack status (packed itself does not count)
--   2) lazada_tracking has a non-empty tracking_number
--
-- Wrong virtual pack candidate = has packed AND function returns 0
-- AND the order also died as canceled / unpaid / pending
-- (so currently packed, waiting to ship, is not listed).

USE [bnyfoodproducts];
GO

IF OBJECT_ID(N'dbo.fn_lazada_order_passed_pack', N'FN') IS NOT NULL
	DROP FUNCTION dbo.fn_lazada_order_passed_pack;
GO

CREATE FUNCTION dbo.fn_lazada_order_passed_pack (@order_number varchar(50))
RETURNS bit
AS
BEGIN
	DECLARE @passed bit = 0;

	IF @order_number IS NULL OR LTRIM(RTRIM(@order_number)) = ''
		RETURN 0;

	-- Other status rows that mean the order already went past pack.
	-- Do not count packed / repacked: those may be the virtual row we are testing.
	IF EXISTS (
		SELECT 1
		FROM dbo.lazada_orders o
		WHERE o.order_number = @order_number
			AND o.status IN (
				'ready_to_ship',
				'ready_to_ship_pending',
				'shipped',
				'shipping',
				'delivered',
				'confirmed',
				'returned',
				'failed_delivery',
				'lost_by_3pl',
				'damaged_by_3pl',
				'shipped_back',
				'shipped_back_success',
				'shipped_back_failed',
				'package_scrapped'
			)
	)
		SET @passed = 1;

	IF @passed = 0 AND EXISTS (
		SELECT 1
		FROM dbo.lazada_tracking t
		WHERE t.order_number = @order_number
			AND NULLIF(LTRIM(RTRIM(ISNULL(t.tracking_number, ''))), '') IS NOT NULL
	)
		SET @passed = 1;

	RETURN @passed;
END
GO

-- Candidates: packed exists, never passed pack, and canceled/unpaid/pending.
;WITH packed AS (
	SELECT
		o.OrderID,
		o.order_number,
		o.created_at,
		o.updated_at,
		o.price,
		o.voucher_seller,
		o.shipping_fee
	FROM dbo.lazada_orders o
	WHERE o.status = 'packed'
),
status_list AS (
	SELECT
		p.order_number,
		STUFF((
			SELECT N', ' + x.status
			FROM dbo.lazada_orders x
			WHERE x.order_number = p.order_number
			ORDER BY x.OrderID
			FOR XML PATH(N''), TYPE
		).value(N'.', N'nvarchar(max)'), 1, 2, N'') AS statuses
	FROM packed p
)
SELECT
	p.order_number,
	tid.taxinvoiceID,
	dbo.fn_lazada_order_passed_pack(p.order_number) AS passed_pack,
	sl.statuses,
	trk.tracking_number,
	p.OrderID AS packed_OrderID,
	p.created_at AS packed_created_at,
	p.updated_at AS packed_updated_at,
	c.OrderID AS canceled_OrderID,
	c.updated_at AS canceled_updated_at,
	p.price,
	p.voucher_seller,
	p.shipping_fee,
	CASE
		WHEN c.order_number IS NOT NULL THEN N'cancel_before_pack_virtual_packed'
		ELSE N'unpaid_or_pending_virtual_packed'
	END AS suspect_reason
FROM packed p
INNER JOIN status_list sl ON sl.order_number = p.order_number
LEFT JOIN dbo.lazada_taxinvoiceid tid ON tid.order_number = p.order_number
LEFT JOIN dbo.lazada_orders c
	ON c.order_number = p.order_number
	AND c.status = 'canceled'
OUTER APPLY (
	SELECT TOP 1 t.tracking_number
	FROM dbo.lazada_tracking t
	WHERE t.order_number = p.order_number
		AND NULLIF(LTRIM(RTRIM(ISNULL(t.tracking_number, ''))), '') IS NOT NULL
	ORDER BY t.lazada_tracking_id DESC
) trk
WHERE dbo.fn_lazada_order_passed_pack(p.order_number) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.lazada_orders z
		WHERE z.order_number = p.order_number
			AND z.status IN ('canceled', 'unpaid', 'pending')
	)
ORDER BY p.created_at, p.order_number;
GO
