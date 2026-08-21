-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN]
	-- Add the parameters for the stored procedure here
	@DateStart Datetime,
	@DateEnd Datetime
	
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	
select convert(varchar, shopee_orders_cn_date_from_shipped_status_from_latest.invoice_date, 23) as transactiondate,
convert(varchar, shopee_orders_cn_date_from_shipped_status_from_latest.invoice_date, 23) as updated_at,
Shopee_taxinvoiceid.taxinvoiceID as start_inv,
Shopee_taxinvoiceid.taxinvoiceID as end_inv,
FullTaxinvoiceID as start_tiv,
shopee_taxinvoice.name as cus_name,
shopee_taxinvoice.TaxNo as TaxNo,
shopee_taxinvoice.name as customer_name,
shopee_taxinvoice.phone as customer_phone,
shopee_taxinvoice.zip as customer_zip,
address1,
address2,
total_amount as price,
buyer_paid_shipping_fee as shipping_fee,
voucher_from_shopee+coins as voucher_platform,
voucher_from_seller as voucher_seller,
voucher_from_shopee+coins+voucher_from_seller as voucher, 
[order_status] as status1,
[order_status] as status2,
(original_price+buyer_paid_shipping_fee-seller_discount) as ValueBeforeVAT,
(original_price+buyer_paid_shipping_fee-seller_discount)*(0.07/1.07) as VAT,

--(original_cost_of_goods_sold+buyer_paid_shipping_fee-(voucher_from_shopee+coins+voucher_from_seller)) as ValueBeforeVAT,
--(original_cost_of_goods_sold+buyer_paid_shipping_fee-(voucher_from_shopee+coins+voucher_from_seller))*(0.07/1.07) as VAT,

shopee_orders_cn_date_from_shipped_status_from_latest.order_sn as order_number,
shopee_orders_cn_date_from_shipped_status_from_latest.is_return

from ((([bnyfoodproducts].[dbo].shopee_orders_cn_date_from_shipped_status_from_latest left outer join shopee_taxinvoice on ([bnyfoodproducts].[dbo].shopee_orders_cn_date_from_shipped_status_from_latest.OrderID = shopee_taxinvoice.shopee_orders_OrderID))
inner join Shopee_taxinvoiceid on (shopee_orders_cn_date_from_shipped_status_from_latest.order_sn=Shopee_taxinvoiceid.order_sn))
inner join shopee_escrow_detail on (shopee_orders_cn_date_from_shipped_status_from_latest.OrderID=shopee_escrow_detail.OrderID))
inner join shopee_escrow_order_income on (shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID)
--group by  convert(varchar, created_at, 23),taxinvoiceID,lazada_taxinvoice.name,lazada_taxinvoice.TaxNo,factor 
where convert(date,convert(varchar, shopee_orders_cn_date_from_shipped_status_from_latest.invoice_date, 23))>=@DateStart and convert(date,convert(varchar, shopee_orders_cn_date_from_shipped_status_from_latest.invoice_date, 23))<=@DateEnd
and (shopee_orders_cn_date_from_shipped_status_from_latest.latest_status in ('CANCELLED') OR is_return = 1)
order by convert(date,convert(varchar, shopee_orders_cn_date_from_shipped_status_from_latest.invoice_date, 23)) 
    
END

