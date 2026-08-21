-- TikTok sales tax: มูลค่าสินค้า must be original goods (like Shopee original_price),
-- NOT total_amount (buyer total after seller discount).
--
-- Row TTK20260700001 proof:
--   original_total_product_price = 190  ← correct มูลค่าสินค้า
--   seller_discount = 19
--   sub_total / total_amount / sale_price = 171  ← after seller disc (was wrongly shown as goods)
--   expected ราคารวม VAT = 190 - 19 + shipping_fee(0) = 171

USE [bnyfoodproducts];
GO

ALTER PROCEDURE [dbo].[tiktok_select_order_with_OrderStart_OrderEnd]
	@OrderStart varchar(50),
	@OrderEnd varchar(50)
AS
BEGIN
	SET NOCOUNT ON;

	SELECT
		CONVERT(varchar, a.[create_time], 23) AS transactiondate,
		c.taxinvoiceID AS start_inv,
		c.taxinvoiceID AS end_inv,
		d.FullTaxinvoiceID AS start_tiv,
		b.original_total_product_price AS original_price,
		b.shipping_fee AS shipping_fee,
		b.platform_discount AS voucher_platform,
		b.seller_discount AS voucher_seller,
		b.platform_discount + b.seller_discount AS voucher,
		b.original_total_product_price AS price,
		a.order_id AS order_sn,
		(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0)) AS priceVATincluded,
		CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2)) AS priceBeforeVAT,
		CAST(ROUND(
			(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))
			- CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2))
		, 2) AS numeric(36,2)) AS VAT
	FROM ((tiktok_orders a
		INNER JOIN tiktok_order_payment b ON (a.order_id = b.order_id))
		INNER JOIN tiktok_taxinvoiceid c ON (a.order_id = c.order_id))
		LEFT OUTER JOIN tiktok_fulltaxinvoice d ON (a.order_id = d.order_id)
	WHERE
		RTRIM(c.taxinvoiceID) >= @OrderStart AND RTRIM(c.taxinvoiceID) <= @OrderEnd
		AND a.[status] IN ('Packet')
	ORDER BY CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', a.create_time), RIGHT('0000000'+CAST(ISNULL(a.order_id,0) AS VARCHAR),7)) AS bigint) ASC
END
GO

ALTER PROCEDURE [dbo].[tiktok_select_order_with_DateStart_DateEnd]
	@DateStart Datetime,
	@DateEnd Datetime
AS
BEGIN
	SET NOCOUNT ON;

	SELECT
		CONVERT(varchar, a.[create_time], 23) AS transactiondate,
		a.[status] AS status,
		c.taxinvoiceID AS start_inv,
		c.taxinvoiceID AS end_inv,
		d.FullTaxinvoiceID AS start_tiv,
		b.original_total_product_price AS original_price,
		b.shipping_fee AS shipping_fee,
		b.platform_discount AS voucher_platform,
		b.seller_discount AS voucher_seller,
		b.platform_discount + b.seller_discount AS voucher,
		b.original_total_product_price AS price,
		a.order_id AS order_id,
		(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0)) AS priceVATincluded,
		CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2)) AS priceBeforeVAT,
		CAST(ROUND(
			(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))
			- CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2))
		, 2) AS numeric(36,2)) AS VAT
	FROM ((tiktok_orders_views a
		INNER JOIN tiktok_order_payment b ON (a.order_id = b.order_id))
		INNER JOIN tiktok_taxinvoiceid c ON (a.order_id = c.order_id))
		LEFT OUTER JOIN tiktok_fulltaxinvoice d ON (a.order_id = d.order_id)
	WHERE
		CONVERT(date, CONVERT(varchar, a.[create_time], 23)) >= @DateStart
		AND CONVERT(date, CONVERT(varchar, a.[create_time], 23)) <= @DateEnd
		AND a.[status] IN ('Packet')
	ORDER BY CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', a.create_time), RIGHT('0000000'+CAST(ISNULL(a.order_id,0) AS VARCHAR),7)) AS bigint) ASC
END
GO
