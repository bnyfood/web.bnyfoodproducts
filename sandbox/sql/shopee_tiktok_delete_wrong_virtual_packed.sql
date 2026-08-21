-- Shopee PROCESSED and TikTok Packet: same cleanup as Lazada packed.
-- Audit flag is_virtual_packed. Tax/CN use passed-pack, not the flag.
-- Passed pack does not count PROCESSED / Packet themselves (those may be virtual).

USE [bnyfoodproducts];
GO

IF COL_LENGTH('dbo.shopee_orders', 'is_virtual_packed') IS NULL
BEGIN
	ALTER TABLE dbo.shopee_orders ADD is_virtual_packed bit NOT NULL
		CONSTRAINT DF_shopee_orders_is_virtual_packed DEFAULT (0);
END
GO

IF COL_LENGTH('dbo.tiktok_orders', 'is_virtual_packed') IS NULL
BEGIN
	ALTER TABLE dbo.tiktok_orders ADD is_virtual_packed bit NOT NULL
		CONSTRAINT DF_tiktok_orders_is_virtual_packed DEFAULT (0);
END
GO

IF OBJECT_ID(N'dbo.fn_shopee_order_passed_pack', N'FN') IS NOT NULL
	DROP FUNCTION dbo.fn_shopee_order_passed_pack;
GO

CREATE FUNCTION dbo.fn_shopee_order_passed_pack (@order_sn varchar(64))
RETURNS bit
AS
BEGIN
	DECLARE @passed bit = 0;
	IF @order_sn IS NULL OR LTRIM(RTRIM(@order_sn)) = ''
		RETURN 0;

	IF EXISTS (
		SELECT 1
		FROM dbo.shopee_orders o
		WHERE o.order_sn = @order_sn
			AND o.order_status IN (
				'READY_TO_SHIP',
				'RETRY_SHIP',
				'SHIPPED',
				'TO_CONFIRM_RECEIVE',
				'COMPLETED',
				'TO_RETURN',
				'RETURNED'
			)
	)
		SET @passed = 1;

	IF @passed = 0 AND EXISTS (
		SELECT 1
		FROM dbo.shopee_tracking t
		WHERE t.order_sn = @order_sn
			AND NULLIF(LTRIM(RTRIM(ISNULL(t.tracking_number, ''))), '') IS NOT NULL
	)
		SET @passed = 1;

	RETURN @passed;
END
GO

IF OBJECT_ID(N'dbo.fn_tiktok_order_passed_pack', N'FN') IS NOT NULL
	DROP FUNCTION dbo.fn_tiktok_order_passed_pack;
GO

CREATE FUNCTION dbo.fn_tiktok_order_passed_pack (@order_id varchar(64))
RETURNS bit
AS
BEGIN
	DECLARE @passed bit = 0;
	IF @order_id IS NULL OR LTRIM(RTRIM(@order_id)) = ''
		RETURN 0;

	IF EXISTS (
		SELECT 1
		FROM dbo.tiktok_orders o
		WHERE o.order_id = @order_id
			AND o.status IN (
				'AWAITING_SHIPMENT',
				'AWAITING_COLLECTION',
				'READY_TO_SHIP',
				'IN_TRANSIT',
				'DELIVERED',
				'COMPLETED',
				'Completed',
				'Shipped',
				'SHIPPED',
				'TO_CONFIRM_RECEIVE',
				'PARTIALLY_SHIPPING'
			)
	)
		SET @passed = 1;

	IF @passed = 0 AND EXISTS (
		SELECT 1
		FROM dbo.tiktok_orders o
		WHERE o.order_id = @order_id
			AND NULLIF(LTRIM(RTRIM(ISNULL(o.tracking_number, ''))), '') IS NOT NULL
	)
		SET @passed = 1;

	IF @passed = 0 AND EXISTS (
		SELECT 1
		FROM dbo.tiktok_line_items li
		WHERE li.order_id = @order_id
			AND NULLIF(LTRIM(RTRIM(ISNULL(li.tracking_number, ''))), '') IS NOT NULL
	)
		SET @passed = 1;

	RETURN @passed;
END
GO

IF OBJECT_ID(N'dbo.shopee_orders_virtual_processed_deleted', N'U') IS NULL
BEGIN
	SELECT TOP 0 o.*, GETDATE() AS archived_at
	INTO dbo.shopee_orders_virtual_processed_deleted
	FROM dbo.shopee_orders o;
END
GO

IF OBJECT_ID(N'dbo.tiktok_orders_virtual_packed_deleted', N'U') IS NULL
BEGIN
	SELECT TOP 0 o.*, GETDATE() AS archived_at
	INTO dbo.tiktok_orders_virtual_packed_deleted
	FROM dbo.tiktok_orders o;
END
GO

DECLARE @cols nvarchar(max);
SELECT @cols = STUFF((
	SELECT N',' + QUOTENAME(c.COLUMN_NAME)
	FROM INFORMATION_SCHEMA.COLUMNS c
	WHERE c.TABLE_SCHEMA = N'dbo' AND c.TABLE_NAME = N'shopee_orders'
	ORDER BY c.ORDINAL_POSITION
	FOR XML PATH(N''), TYPE
).value(N'.', N'nvarchar(max)'), 1, 1, N'');

DECLARE @sql nvarchar(max) = N'
SET IDENTITY_INSERT dbo.shopee_orders_virtual_processed_deleted ON;
INSERT INTO dbo.shopee_orders_virtual_processed_deleted (' + @cols + N', archived_at)
SELECT ' + @cols + N', GETDATE()
FROM dbo.shopee_orders o
WHERE o.order_status = ''PROCESSED''
	AND dbo.fn_shopee_order_passed_pack(o.order_sn) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.shopee_orders z
		WHERE z.order_sn = o.order_sn
			AND z.order_status IN (''CANCELLED'', ''UNPAID'', ''INVOICE_PENDING'')
	)
	AND NOT EXISTS (
		SELECT 1 FROM dbo.shopee_orders_virtual_processed_deleted b
		WHERE b.OrderID = o.OrderID
	);
SET IDENTITY_INSERT dbo.shopee_orders_virtual_processed_deleted OFF;';
EXEC sp_executesql @sql;
GO

DELETE o
FROM dbo.shopee_orders o
WHERE o.order_status = 'PROCESSED'
	AND dbo.fn_shopee_order_passed_pack(o.order_sn) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.shopee_orders z
		WHERE z.order_sn = o.order_sn
			AND z.order_status IN ('CANCELLED', 'UNPAID', 'INVOICE_PENDING')
	);
GO

DECLARE @cols2 nvarchar(max);
SELECT @cols2 = STUFF((
	SELECT N',' + QUOTENAME(c.COLUMN_NAME)
	FROM INFORMATION_SCHEMA.COLUMNS c
	WHERE c.TABLE_SCHEMA = N'dbo' AND c.TABLE_NAME = N'tiktok_orders'
	ORDER BY c.ORDINAL_POSITION
	FOR XML PATH(N''), TYPE
).value(N'.', N'nvarchar(max)'), 1, 1, N'');

DECLARE @sql2 nvarchar(max) = N'
SET IDENTITY_INSERT dbo.tiktok_orders_virtual_packed_deleted ON;
INSERT INTO dbo.tiktok_orders_virtual_packed_deleted (' + @cols2 + N', archived_at)
SELECT ' + @cols2 + N', GETDATE()
FROM dbo.tiktok_orders o
WHERE o.status = ''Packet''
	AND dbo.fn_tiktok_order_passed_pack(CONVERT(varchar(64), o.order_id)) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.tiktok_orders z
		WHERE z.order_id = o.order_id
			AND z.status IN (''CANCELLED'', ''Canceled'', ''UNPAID'', ''ON_HOLD'')
	)
	AND NOT EXISTS (
		SELECT 1 FROM dbo.tiktok_orders_virtual_packed_deleted b
		WHERE b.tiktok_orders_id = o.tiktok_orders_id
	);
SET IDENTITY_INSERT dbo.tiktok_orders_virtual_packed_deleted OFF;';
EXEC sp_executesql @sql2;
GO

DELETE o
FROM dbo.tiktok_orders o
WHERE o.status = 'Packet'
	AND dbo.fn_tiktok_order_passed_pack(CONVERT(varchar(64), o.order_id)) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.tiktok_orders z
		WHERE z.order_id = o.order_id
			AND z.status IN ('CANCELLED', 'Canceled', 'UNPAID', 'ON_HOLD')
	);
GO

SELECT COUNT(*) AS archived_shopee_wrong_processed
FROM dbo.shopee_orders_virtual_processed_deleted;

SELECT COUNT(*) AS remaining_shopee_wrong_processed
FROM dbo.shopee_orders o
WHERE o.order_status = 'PROCESSED'
	AND dbo.fn_shopee_order_passed_pack(o.order_sn) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.shopee_orders z
		WHERE z.order_sn = o.order_sn
			AND z.order_status IN ('CANCELLED', 'UNPAID', 'INVOICE_PENDING')
	);

SELECT COUNT(*) AS archived_tiktok_wrong_packet
FROM dbo.tiktok_orders_virtual_packed_deleted;

SELECT COUNT(*) AS remaining_tiktok_wrong_packet
FROM dbo.tiktok_orders o
WHERE o.status = 'Packet'
	AND dbo.fn_tiktok_order_passed_pack(CONVERT(varchar(64), o.order_id)) = 0
	AND EXISTS (
		SELECT 1 FROM dbo.tiktok_orders z
		WHERE z.order_id = o.order_id
			AND z.status IN ('CANCELLED', 'Canceled', 'UNPAID', 'ON_HOLD')
	);
GO
