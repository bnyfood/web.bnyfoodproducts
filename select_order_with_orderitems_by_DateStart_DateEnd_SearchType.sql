USE [bnyfoodproducts]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER PROCEDURE [dbo].[select_order_with_orderitems_by_DateStart_DateEnd_SearchType]
	@DateStart Datetime,
	@DateEnd Datetime,
	@Search_type varchar(255),
	@order_number varchar(255),
	@voidtype int = 2
AS
BEGIN
	SET NOCOUNT ON;

	SELECT
		lazada_orders.order_number,
		lazada_taxinvoiceid.taxinvoiceID,
		lazada_orders.created_at AS created_at,
		lazada_orders.shipping_fee_original,
		lazada_orders.shipping_fee_discount_platform,
		lazada_orders.shipping_fee_discount_seller,
		lazada_orders.shipping_fee * ISNULL(factor, 1) AS shipping_fee,
		lazada_orders.voucher_platform * ISNULL(factor, 1) AS voucher_platform,
		lazada_orders.voucher_seller * ISNULL(factor, 1) AS voucher_seller,
		lazada_orders.price * ISNULL(factor, 1) AS price,
		lazada_orders.voucher * ISNULL(factor, 1) AS voucher,
		lazada_orderitems.OrderItemID,
		lazada_orderitems.sku,
		lazada_skumap.ProductName,
		lazada_orderitems.name,
		lazada_orderitems.item_price * ISNULL(factor, 1) AS item_price,
		lazada_orderitems.voucher_seller * ISNULL(factor, 1) AS item_voucher_seller,
		lazada_orderitems.voucher_platform * ISNULL(factor, 1) AS item_voucher_platform,
		lazada_orderitems.paid_price * ISNULL(factor, 1) AS paid_price,
		lazada_orderitems.tax_amount
	FROM ((((lazada_orders
		INNER JOIN lazada_orderitems ON (lazada_orders.order_number = lazada_orderitems.order_number))
		LEFT OUTER JOIN lazada_skumap ON (lazada_orderitems.sku = lazada_skumap.sku))
		LEFT OUTER JOIN billing_store ON (CONVERT(varchar(7), lazada_orders.created_at, 126) = billing_store.order_ym))
		INNER JOIN lazada_taxinvoiceid ON (lazada_orders.order_number = lazada_taxinvoiceid.order_number))
		LEFT OUTER JOIN lazada_tracking ON (lazada_orders.order_number = lazada_tracking.order_number)
	WHERE
		(
			@voidtype = 2
			OR (@voidtype = 1 AND ISNULL(lazada_tracking.tracking_number, '') = '')
			OR (@voidtype = 0 AND ISNULL(lazada_tracking.tracking_number, '') <> '')
		)
		AND (
			(@Search_type = '1'
				AND CAST(lazada_orders.created_at AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(lazada_orders.created_at AS DATE) <= CAST(@DateEnd AS DATE)
				AND lazada_orders.status = 'packed')
			OR (@Search_type = '2'
				AND (
					lazada_taxinvoiceid.taxinvoiceID = @order_number
					OR RIGHT(lazada_orders.order_number, 11) = RIGHT(@order_number, 11)
				)
				AND lazada_orders.status = 'packed')
			OR (@Search_type = '3'
				AND CAST(lazada_orders.created_at AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(lazada_orders.created_at AS DATE) <= CAST(@DateEnd AS DATE)
				AND (
					lazada_taxinvoiceid.taxinvoiceID = @order_number
					OR RIGHT(lazada_orders.order_number, 11) = RIGHT(@order_number, 11)
				)
				AND lazada_orders.status = 'packed')
		)
	ORDER BY lazada_orders.created_at ASC
END
GO
