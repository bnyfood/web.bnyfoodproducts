-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[shopee_select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN]
	-- Add the parameters for the stored procedure here
	@DateStart Datetime,
	@DateEnd Datetime,
	@Search_type varchar(255),
	@order_number varchar(255)
	
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DECLARE @shopee_temp_table_cancel TABLE (order_sn varchar(50))

	insert into @shopee_temp_table_cancel select shopee_orders_cn_view.order_sn from shopee_orders_cn_view 
	inner join Shopee_taxinvoiceid on (shopee_orders_cn_view.order_sn=Shopee_taxinvoiceid.order_sn)
 WHERE (CASE 
          WHEN @Search_type = '1' and  convert(date,convert(varchar, [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time], 23))>=@DateStart and convert(date,convert(varchar, [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time], 23))<=@DateEnd and order_status = 'CANCELLED'
          THEN 'True'
		  WHEN @Search_type = '2'  and  [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[taxinvoiceID] = @order_number and order_status = 'CANCELLED'
          THEN 'True'
		  WHEN @Search_type = '3'  and  convert(date,convert(varchar, [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time], 23))>=@DateStart and convert(date,convert(varchar, [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time], 23))<=@DateEnd and RIGHT(Shopee_taxinvoiceid.taxinvoiceID,11) > RIGHT(@order_number,11) and order_status = 'CANCELLED'
          THEN 'True'
		  WHEN @Search_type = '4'  and  [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[order_sn] = @order_number and order_status = 'CANCELLED'
          THEN 'True'
       END) ='True'

	select 

[bnyfoodproducts].[dbo].[shopee_orders_cn_view].[order_sn] as order_number,

Shopee_taxinvoiceid.[taxinvoiceID],

[bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time] as created_at,

[bnyfoodproducts].[dbo].[shopee_orders_cn_view].[update_time] as updated_at,

[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[buyer_paid_shipping_fee] as shipping_fee,

[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[voucher_from_shopee] as voucher_platform,

[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[voucher_from_seller] as voucher_seller,

[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[original_price]-[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[seller_discount]-[bnyfoodproducts].[dbo].[shopee_escrow_order_income].[shopee_discount] as price,


[bnyfoodproducts].[dbo].[shopee_orders_cn_view].[order_status],

shopee_taxinvoice.[FullTaxinvoiceID] as FullTaxinvoiceID,

ISNULL(shopee_taxinvoice.TaxNo,'-') as TaxNo,

ISNULL(shopee_taxinvoice.name,'-') as customer_name,

ISNULL(shopee_taxinvoice.phone,'-') as customer_phone,

ISNULL(shopee_taxinvoice.zip,'-') as customer_zip,
ISNULL(address1,'-') as address1,
ISNULL(address2,'-') as address2,

shopee_orderitems.[OrderItemID],

shopee_orderitems.[item_sku] as sku,

lazada_skumap.[ProductName],

shopee_orderitems.[item_name] as name,

shopee_orderitems.[model_discounted_price] as paid_price

from ((((([bnyfoodproducts].[dbo].[shopee_orders_cn_view] inner join shopee_orderitems on ([bnyfoodproducts].[dbo].[shopee_orders_cn_view].order_sn = shopee_orderitems.order_sn))
inner join lazada_skumap on (shopee_orderitems.[item_sku]=lazada_skumap.sku))
left outer join shopee_taxinvoice on ([bnyfoodproducts].[dbo].[shopee_orders_cn_view].order_sn = shopee_taxinvoice.shopee_orders_OrderID))
inner join shopee_escrow_detail on (shopee_orders_cn_view.OrderID = shopee_escrow_detail.OrderID))
inner join shopee_escrow_order_income on (shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID))
inner join Shopee_taxinvoiceid on (shopee_orders_cn_view.order_sn=Shopee_taxinvoiceid.order_sn)
 WHERE 
 [bnyfoodproducts].[dbo].[shopee_orders_cn_view].order_sn in (select order_sn from @shopee_temp_table_cancel group by order_sn)
 and order_status = 'SHIPPED'
order by [bnyfoodproducts].[dbo].[shopee_orders_cn_view].[create_time] asc
END