
CREATE PROCEDURE [dbo].[shopee_select_order_with_SearchType]
	@DateStart Datetime,
	@DateEnd Datetime,
	@Search_type varchar(255),
	@order_number varchar(255),
	@voidtype int = 2
AS
BEGIN
	SET NOCOUNT ON;

	SELECT DISTINCT
		shopee_orders.order_sn AS order_number,
		Shopee_taxinvoiceid.taxinvoiceID,
		shopee_orders.create_time AS created_at,
		shopee_escrow_order_income.buyer_paid_shipping_fee AS shipping_fee,
		shopee_escrow_order_income.voucher_from_shopee + shopee_escrow_order_income.coins AS voucher_platform,
		shopee_escrow_order_income.voucher_from_seller AS voucher_seller,
		shopee_escrow_order_income.seller_discount AS seller_discount,
		shopee_escrow_order_income.original_price AS price,
		shopee_orderitems.item_sku AS sku,
		lazada_skumap.ProductName AS ProductName,
		shopee_escrow_items.discounted_price AS item_price,
		shopee_orderitems.model_quantity_purchased AS qty,
		shopee_orderitems.order_item_id
	FROM ((((((shopee_orders
		INNER JOIN shopee_escrow_detail ON (shopee_orders.OrderID = shopee_escrow_detail.OrderID))
		INNER JOIN shopee_escrow_order_income ON (shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID))
		INNER JOIN shopee_orderitems ON (shopee_orders.order_sn = shopee_orderitems.order_sn))
		INNER JOIN Shopee_taxinvoiceid ON (shopee_orders.order_sn = Shopee_taxinvoiceid.order_sn))
		INNER JOIN shopee_escrow_items ON (
			shopee_escrow_detail.EscrowID = shopee_escrow_items.EscrowID
			AND shopee_orderitems.item_sku = shopee_escrow_items.item_sku
			AND shopee_orderitems.promotion_type = shopee_escrow_items.activity_type
			AND shopee_escrow_items.original_price = shopee_orderitems.model_original_price * shopee_orderitems.model_quantity_purchased
		))
		LEFT OUTER JOIN lazada_skumap ON (shopee_orderitems.item_sku = lazada_skumap.sku))
		LEFT OUTER JOIN shopee_tracking ON (shopee_orders.order_sn = shopee_tracking.order_sn)
	WHERE
		(
			@voidtype = 2
			OR (@voidtype = 1 AND ISNULL(shopee_tracking.tracking_number, '') = '')
			OR (@voidtype = 0 AND ISNULL(shopee_tracking.tracking_number, '') <> '')
		)
		AND (
			(@Search_type = '1'
				AND CAST(shopee_orders.create_time AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(shopee_orders.create_time AS DATE) <= CAST(@DateEnd AS DATE)
				AND order_status = 'PROCESSED')
			OR (@Search_type = '2'
				AND shopee_orders.taxinvoiceID = @order_number
				AND order_status = 'PROCESSED')
			OR (@Search_type = '3'
				AND CAST(shopee_orders.create_time AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(shopee_orders.create_time AS DATE) <= CAST(@DateEnd AS DATE)
				AND RIGHT(Shopee_taxinvoiceid.taxinvoiceID, 11) > RIGHT(@order_number, 11)
				AND order_status = 'PROCESSED')
		)
	ORDER BY shopee_orders.create_time ASC
END
