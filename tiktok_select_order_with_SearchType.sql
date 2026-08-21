USE [bnyfoodproducts]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER PROCEDURE [dbo].[tiktok_select_order_with_SearchType]
	@DateStart Datetime,
	@DateEnd Datetime,
	@Search_type varchar(255),
	@order_number varchar(255),
	@is_tracking int = 1
AS
BEGIN
	SET NOCOUNT ON;

	SELECT
		tiktok_orders.order_id AS order_number,
		tiktok_taxinvoiceid.taxinvoiceID,
		tiktok_orders.create_time AS created_at,
		tiktok_order_payment_view.shipping_fee AS shipping_fee,
		tiktok_order_payment_view.platform_discount AS voucher_platform,
		tiktok_order_payment_view.seller_discount AS voucher_seller,
		tiktok_order_payment_view.sub_total AS price,
		tiktok_line_items.seller_sku AS sku,
		tiktok_line_items.product_name AS ProductName,
		tiktok_line_items.original_price AS item_price,
		tiktok_line_items.order_qty AS qty
	FROM ((tiktok_orders
		INNER JOIN tiktok_line_items ON (tiktok_orders.order_id = tiktok_line_items.order_id))
		INNER JOIN tiktok_order_payment_view ON (tiktok_orders.order_id = tiktok_order_payment_view.order_id))
		INNER JOIN tiktok_taxinvoiceid ON (tiktok_orders.order_id = tiktok_taxinvoiceid.order_id)
	WHERE
		(
			@is_tracking = 2
			OR (@is_tracking = 1 AND tiktok_taxinvoiceid.is_tracking = 1)
			OR (@is_tracking = 0 AND tiktok_taxinvoiceid.is_tracking = 0)
		)
		AND (
			(@Search_type = '1'
				AND CAST(tiktok_orders.create_time AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(tiktok_orders.create_time AS DATE) <= CAST(@DateEnd AS DATE)
				AND tiktok_orders.status = 'Packet')
			OR (@Search_type = '2'
				AND tiktok_taxinvoiceid.taxinvoiceID = @order_number
				AND tiktok_orders.tracking_number <> '')
			OR (@Search_type = '3'
				AND CAST(tiktok_orders.create_time AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(tiktok_orders.create_time AS DATE) <= CAST(@DateEnd AS DATE)
				AND RIGHT(tiktok_taxinvoiceid.taxinvoiceID, 11) > RIGHT(@order_number, 11)
				AND tiktok_orders.tracking_number <> '')
		)
	ORDER BY tiktok_orders.create_time ASC
END
GO
