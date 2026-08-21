USE [bnyfoodproducts]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER PROCEDURE [dbo].[inwshop_select_order_with_SearchType]
	@DateStart Datetime,
	@DateEnd Datetime,
	@Search_type varchar(255),
	@order_number varchar(255),
	@voidtype int = 2
AS
BEGIN
	SET NOCOUNT ON;

	SELECT
		biggrill_data.order_id AS order_number,
		inwshop_taxinvoiceid.taxinvoiceID,
		biggrill_data.ctime AS created_at,
		biggrill_data.delivery AS shipping_fee,
		0 AS voucher_platform,
		biggrill_data.discount AS voucher_seller,
		biggrill_data.price AS price,
		inwshop_item_data.sku AS sku,
		inwshop_item_data.product_name AS ProductName,
		inwshop_item_data.procuct_price AS item_price,
		inwshop_item_data.qty AS qty
	FROM (biggrill_data
		INNER JOIN inwshop_item_data ON (biggrill_data.order_id = inwshop_item_data.order_id))
		INNER JOIN inwshop_taxinvoiceid ON (biggrill_data.order_id = inwshop_taxinvoiceid.order_id)
	WHERE
		(
			@voidtype = 2
			OR (@voidtype = 0 AND biggrill_data.is_void = 0)
			OR (@voidtype = 1 AND biggrill_data.is_void = 1)
		)
		AND (
			(@Search_type = '1'
				AND CAST(biggrill_data.ctime AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(biggrill_data.ctime AS DATE) <= CAST(@DateEnd AS DATE)
				AND biggrill_data.status <> 'ยกเลิก')
			OR (@Search_type = '2'
				AND inwshop_taxinvoiceid.taxinvoiceID = @order_number
				AND biggrill_data.status <> 'Canceled')
			OR (@Search_type = '3'
				AND CAST(biggrill_data.ctime AS DATE) >= CAST(@DateStart AS DATE)
				AND CAST(biggrill_data.ctime AS DATE) <= CAST(@DateEnd AS DATE)
				AND RIGHT(inwshop_taxinvoiceid.taxinvoiceID, 11) > RIGHT(@order_number, 11)
				AND biggrill_data.status <> 'ยกเลิก')
		)
	ORDER BY biggrill_data.ctime ASC
END
GO
