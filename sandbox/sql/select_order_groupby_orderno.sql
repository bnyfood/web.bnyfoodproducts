-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[select_order_groupby_orderno]
	-- Add the parameters for the stored procedure here
	@OrderStart varchar(50),
	@OrderEnd varchar(50)
	
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	
select convert(varchar, lazada_orders_view.created_at, 23) as transactiondate,
lazada_taxinvoiceid.taxinvoiceID as start_inv,
lazada_taxinvoiceid.taxinvoiceID as end_inv,
FullTaxinvoiceID as start_tiv,
lazada_taxinvoice.name as cus_name,
lazada_taxinvoice.TaxNo as TaxNo,
price*ISNULL(factor,1) as price,
shipping_fee_original as shipping_fee_original,
shipping_fee_discount_platform as shipping_fee_discount_platform,
shipping_fee_discount_seller as shipping_fee_discount_seller,
shipping_fee as shipping_fee,
voucher_platform as voucher_platform,
voucher_seller as voucher_seller,
voucher as voucher, 
[status] as status1,
[status] as status2,
[bnyfoodproducts].[dbo].[lazada_orders_view].order_number,
(price*ISNULL(factor,1)-voucher*ISNULL(factor,1)) as priceVATincluded,    
 cast(round((price*ISNULL(factor,1)-voucher*ISNULL(factor,1))/1.07,2) as numeric(36,2))       as priceBeforeVAT,
 (price*ISNULL(factor,1)-voucher*ISNULL(factor,1))- cast(round((price*ISNULL(factor,1)-voucher*ISNULL(factor,1))/1.07,2) as numeric(36,2))    as VAT
 

 --(price*ISNULL(factor,1)+shipping_fee*ISNULL(factor,1)-voucher*ISNULL(factor,1)) as priceVATincluded,    
 --cast(round((price*ISNULL(factor,1)+shipping_fee*ISNULL(factor,1)-voucher*ISNULL(factor,1))/1.07,2) as numeric(36,2))       as priceBeforeVAT,
 --(price*ISNULL(factor,1)+shipping_fee*ISNULL(factor,1)-voucher*ISNULL(factor,1))- cast(round((price*ISNULL(factor,1)+shipping_fee*ISNULL(factor,1)-voucher*ISNULL(factor,1))/1.07,2) as numeric(36,2))    as VAT

from (([bnyfoodproducts].[dbo].[lazada_orders_view] left outer join lazada_taxinvoice on ([bnyfoodproducts].[dbo].[lazada_orders_view].order_number = lazada_taxinvoice.lazada_orders_OrderID))
inner join lazada_taxinvoiceid on (lazada_orders_view.order_number=lazada_taxinvoiceid.order_number))
left outer join billing_store on (convert(varchar(7), [bnyfoodproducts].[dbo].[lazada_orders_view].created_at, 126) = billing_store.order_ym)
--group by  convert(varchar, created_at, 23),taxinvoiceID,lazada_taxinvoice.name,lazada_taxinvoice.TaxNo,factor 
where  RIGHT(lazada_taxinvoiceid.taxinvoiceID,11) >= @OrderStart and RIGHT(lazada_taxinvoiceid.taxinvoiceID,11) <= @OrderEnd
and lazada_orders_view.[status] = 'packed'
order by lazada_taxinvoiceid.taxinvoiceID asc
--order by convert(date,convert(varchar, lazada_orders_view.created_at, 23)) 
    
END

