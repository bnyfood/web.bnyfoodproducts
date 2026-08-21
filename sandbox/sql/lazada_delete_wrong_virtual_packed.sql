-- Add audit flag, archive, then delete WRONG virtual packed rows.
-- Packed itself is not deleted from orders that already passed pack
-- (ready_to_ship / shipped / delivered / tracking, etc.).
--
-- is_virtual_packed is an audit flag only. Tax/CN still use passed-pack,
-- not this flag.

USE [bnyfoodproducts];
GO

IF COL_LENGTH('dbo.lazada_orders', 'is_virtual_packed') IS NULL
BEGIN
	ALTER TABLE dbo.lazada_orders ADD is_virtual_packed bit NOT NULL
		CONSTRAINT DF_lazada_orders_is_virtual_packed DEFAULT (0);
END
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

IF OBJECT_ID(N'dbo.lazada_orders_virtual_packed_deleted', N'U') IS NULL
BEGIN
	SELECT TOP 0 o.*, GETDATE() AS archived_at
	INTO dbo.lazada_orders_virtual_packed_deleted
	FROM dbo.lazada_orders o;
END
GO

DECLARE @cols nvarchar(max);
SELECT @cols = STUFF((
	SELECT N',' + QUOTENAME(c.COLUMN_NAME)
	FROM INFORMATION_SCHEMA.COLUMNS c
	WHERE c.TABLE_SCHEMA = N'dbo'
		AND c.TABLE_NAME = N'lazada_orders'
	ORDER BY c.ORDINAL_POSITION
	FOR XML PATH(N''), TYPE
).value(N'.', N'nvarchar(max)'), 1, 1, N'');

DECLARE @sql nvarchar(max) = N'
SET IDENTITY_INSERT dbo.lazada_orders_virtual_packed_deleted ON;
INSERT INTO dbo.lazada_orders_virtual_packed_deleted (' + @cols + N', archived_at)
SELECT ' + @cols + N', GETDATE()
FROM dbo.lazada_orders o
WHERE o.status = ''packed''
	AND dbo.fn_lazada_order_passed_pack(o.order_number) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.lazada_orders z
		WHERE z.order_number = o.order_number
			AND z.status IN (''canceled'', ''unpaid'', ''pending'')
	)
	AND NOT EXISTS (
		SELECT 1
		FROM dbo.lazada_orders_virtual_packed_deleted b
		WHERE b.OrderID = o.OrderID
	);
SET IDENTITY_INSERT dbo.lazada_orders_virtual_packed_deleted OFF;';

EXEC sp_executesql @sql;
GO

DELETE o
FROM dbo.lazada_orders o
WHERE o.status = 'packed'
	AND dbo.fn_lazada_order_passed_pack(o.order_number) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.lazada_orders z
		WHERE z.order_number = o.order_number
			AND z.status IN ('canceled', 'unpaid', 'pending')
	);
GO

SELECT COUNT(*) AS archived_wrong_virtual_packed
FROM dbo.lazada_orders_virtual_packed_deleted;

SELECT COUNT(*) AS remaining_wrong_virtual_packed
FROM dbo.lazada_orders o
WHERE o.status = 'packed'
	AND dbo.fn_lazada_order_passed_pack(o.order_number) = 0
	AND EXISTS (
		SELECT 1
		FROM dbo.lazada_orders z
		WHERE z.order_number = o.order_number
			AND z.status IN ('canceled', 'unpaid', 'pending')
	);
GO
