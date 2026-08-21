-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[shopee_select_order_with_OrdernoStart_OrderEnd]
	-- Add the parameters for the stored procedure here
	@OrderStart varchar(50),
	@OrderEnd varchar(50)
	
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	
select convert(varchar, shopee_orders.[create_time], 23) as transactiondate,
Shopee_taxinvoiceid.taxinvoiceID as start_inv,
Shopee_taxinvoiceid.taxinvoiceID as end_inv,
shopee_taxinvoice.FullTaxinvoiceID as start_tiv,
--shopee_orders.[taxinvoiceID],
shopee_escrow_order_income.buyer_paid_shipping_fee as shipping_fee,
shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins as voucher_platform,
shopee_escrow_order_income.voucher_from_seller as voucher_seller,
shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller as voucher,
shopee_escrow_order_income.original_price as price,
shopee_escrow_order_income.seller_discount as seller_discount,
shopee_orders.[order_sn],

(shopee_escrow_order_income.original_price+shopee_escrow_order_income.buyer_paid_shipping_fee-shopee_escrow_order_income.seller_discount) as priceVATincluded,    
cast(round((shopee_escrow_order_income.original_price+shopee_escrow_order_income.buyer_paid_shipping_fee-shopee_escrow_order_income.seller_discount)/1.07,2) as numeric(36,2))       as priceBeforeVAT,
cast(round(((shopee_escrow_order_income.original_price+shopee_escrow_order_income.buyer_paid_shipping_fee-shopee_escrow_order_income.seller_discount))-(cast(round((shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-shopee_escrow_order_income.seller_discount)/1.07,2) as numeric(36,2))),2) as numeric(36,2))    as VAT

--(shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-(shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller)) as priceVATincluded,    
-- cast(round((shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-(shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller))/1.07,2) as numeric(36,2))       as priceBeforeVAT,
--  cast(round(((shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-(shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller)))-(cast(round((shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-(shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller))/1.07,2) as numeric(36,2))),2) as numeric(36,2))    as VAT

from (((shopee_orders inner join shopee_escrow_detail on (shopee_orders.OrderID=shopee_escrow_detail.OrderID)) 
inner join shopee_escrow_order_income on (shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID))
inner join Shopee_taxinvoiceid on (shopee_orders.order_sn=Shopee_taxinvoiceid.order_sn))
left outer join shopee_taxinvoice on (shopee_orders.OrderID = shopee_taxinvoice.shopee_orders_OrderID)
where  RIGHT(Shopee_taxinvoiceid.taxinvoiceID,11) >= @OrderStart and RIGHT(Shopee_taxinvoiceid.taxinvoiceID,11) <= @OrderEnd
and shopee_orders.[order_status] in ('PROCESSED')
order by CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00',shopee_orders.create_time),RIGHT('0000000'+CAST(ISNULL(shopee_orders.OrderID,0) AS VARCHAR),7) ) AS bigint) asc

END

