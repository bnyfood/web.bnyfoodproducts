-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[select_order_groupby_Date_by_DateStart_DateEnd_CN]
	-- Add the parameters for the stored procedure here
	@DateStart Datetime,
	@DateEnd Datetime
	
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	
select 
convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23) as transactiondate,
convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23) as updated_at,
lazada_orders_cn_date_from_shipped_status_from_latest.created_at as created_at,
lazada_orders_cn_date_from_shipped_status_from_latest.invoice_date as updated_at_2,
lazada_taxinvoiceid.taxinvoiceID as start_inv,
lazada_taxinvoiceid.taxinvoiceID as end_inv,
FullTaxinvoiceID as start_tiv,
lazada_taxinvoice.name as cus_name,
lazada_taxinvoice.TaxNo as TaxNo,
lazada_taxinvoice.name as customer_name,
lazada_taxinvoice.phone as customer_phone,
lazada_taxinvoice.zip as customer_zip,
CAST(address1 AS varchar(max)) as address1,
CAST(address2 AS varchar(max)) as address2,
price*ISNULL(factor,1) as price,
shipping_fee_original as shipping_fee_original,
shipping_fee_discount_platform as shipping_fee_discount_platform,
shipping_fee_discount_seller as shipping_fee_discount_seller,
shipping_fee as shipping_fee,
[bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].voucher_platform as voucher_platform,
[bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].voucher_seller as voucher_seller,
voucher as voucher, 
[status] as status1,
[latest_status] as status2,
cast(round((sum(lazada_orderitems.item_price)-(sum(lazada_orderitems.voucher_seller) )),2) as numeric(36,2)) as ValueBeforeVAT,
cast(round((sum(lazada_orderitems.item_price)- (sum(lazada_orderitems.voucher_seller) ))*(0.07/1.07),2) as numeric(36,2)) as VAT,

--cast(round((sum(lazada_orderitems.item_price)-(sum(lazada_orderitems.voucher_seller)+sum(lazada_orderitems.voucher_platform) )),2) as numeric(36,2)) as ValueBeforeVAT,
--cast(round((sum(lazada_orderitems.item_price)- (sum(lazada_orderitems.voucher_seller)+sum(lazada_orderitems.voucher_platform) ))*(0.07/1.07),2) as numeric(36,2)) as VAT,

--(sum(lazada_orderitems.item_price)+shipping_fee-sum(lazada_orderitems.voucher_seller)) as ValueBeforeVAT,
--(sum(lazada_orderitems.item_price)+shipping_fee-sum(lazada_orderitems.voucher_seller))*(0.07/1.07) as VAT,

(sum(lazada_orderitems.item_price)+([lazada_orders_cn_date_from_shipped_status_from_latest].total_refund_val-sum(lazada_orderitems.item_price))) as ValueBeforeVATPlatform,
(sum(lazada_orderitems.item_price)+([lazada_orders_cn_date_from_shipped_status_from_latest].total_refund_val-sum(lazada_orderitems.item_price)))*(0.07/1.07) as VATPlatform,

 [lazada_orders_cn_date_from_shipped_status_from_latest].order_number,
 [lazada_orders_cn_date_from_shipped_status_from_latest].order_make_cn as order_make_cn,
 [lazada_orders_cn_date_from_shipped_status_from_latest].cn_status as cn_status

from ((([bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest] left outer join lazada_taxinvoice on ([bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].order_number = lazada_taxinvoice.lazada_orders_OrderID))
inner join lazada_taxinvoiceid on ([lazada_orders_cn_date_from_shipped_status_from_latest].order_number=lazada_taxinvoiceid.order_number))
left outer join billing_store on (convert(varchar(7), [bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 126) = billing_store.order_ym))
inner join lazada_orderitems on ([bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].order_number = lazada_orderitems.order_number)

--group by  convert(varchar, created_at, 23),taxinvoiceID,lazada_taxinvoice.name,lazada_taxinvoice.TaxNo,factor 
where convert(date,convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23))>=@DateStart and convert(date,convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23))<=@DateEnd
and [lazada_orders_cn_date_from_shipped_status_from_latest].[latest_status] in ('lost_by_3pl','damaged_by_3pl','shipped_back','returned','failed_delivery','canceled')
and lazada_orders_cn_date_from_shipped_status_from_latest.order_make_cn=1
and lazada_orderitems.orderitem_make_cn = 1
group by 
convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23),
convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23),
lazada_orders_cn_date_from_shipped_status_from_latest.created_at,
lazada_orders_cn_date_from_shipped_status_from_latest.invoice_date,
lazada_taxinvoiceid.taxinvoiceID,
lazada_taxinvoiceid.taxinvoiceID,
FullTaxinvoiceID,
lazada_taxinvoice.name,
lazada_taxinvoice.TaxNo,
lazada_taxinvoice.name,
lazada_taxinvoice.phone,
lazada_taxinvoice.zip,
CAST(address1 AS varchar(max)),
CAST(address2 AS varchar(max)),
price*ISNULL(factor,1),
shipping_fee_original,
shipping_fee_discount_platform,
shipping_fee_discount_seller,
shipping_fee,
[bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].voucher_platform,
[bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].voucher_seller,
voucher, 
[status],
[latest_status],
[bnyfoodproducts].[dbo].[lazada_orders_cn_date_from_shipped_status_from_latest].total_refund_val,
 [lazada_orders_cn_date_from_shipped_status_from_latest].order_make_cn,
 [lazada_orders_cn_date_from_shipped_status_from_latest].cn_status,

 [lazada_orders_cn_date_from_shipped_status_from_latest].order_number

order by convert(date,convert(varchar, [lazada_orders_cn_date_from_shipped_status_from_latest].invoice_date, 23)) 
    
END

