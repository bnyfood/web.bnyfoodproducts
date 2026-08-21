USE [bnyfoodproducts]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER PROCEDURE [dbo].[delete_shopee_order_by_year_month]
	@year_month varchar(255)
AS
BEGIN
	SET NOCOUNT ON;

	DECLARE @start_date varchar(10);
	SET @start_date = '20' + LEFT(@year_month, 2) + '-' + RIGHT(@year_month, 2) + '-01';

	DELETE FROM shopee_orders WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_orderitems WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_shipping_address WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_taxinvoiceid WHERE LEFT(order_sn, 4) = @year_month;

	DELETE FROM shopee_prep WHERE LEFT(order_sn, 4) = @year_month;

	DELETE FROM shopee_escrow_detail WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_escrow_items WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_escrow_order_income WHERE LEFT(order_sn, 4) = @year_month;
	DELETE FROM shopee_order_list WHERE LEFT(order_sn, 4) = @year_month;
	--UPDATE shopee_order_list SET is_death_status = 0 WHERE LEFT(order_sn, 4) = @year_month;

	UPDATE DataDownload
	SET shopee_orderlist_start_date = @start_date,
		shopee_return_page_no = 1,
		shopee_return_page_size = 10
	WHERE BNY_SUBSCRIPTION_SHOPID = '123456';

	TRUNCATE TABLE shopee_return_order;
END
GO
