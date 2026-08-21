/*
  Diagnostic: Shopee escrow vs Excel tax (net + ship).
  Expected: original_cost_of_goods_sold + buyer_paid_shipping_fee
  (= Excel sum(ราคาขายสุทธิ) + ค่าส่งผู้ซื้อ)
*/
USE [bnyfoodproducts];
GO

SELECT
  o.order_sn,
  i.original_cost_of_goods_sold,
  i.buyer_paid_shipping_fee,
  i.buyer_total_amount,
  i.shopee_discount,
  i.voucher_from_shopee,
  i.seller_discount,
  (ISNULL(i.original_cost_of_goods_sold,0) + ISNULL(i.buyer_paid_shipping_fee,0)) AS api_tax_new,
  (ISNULL(i.buyer_total_amount,0) + ISNULL(i.shopee_discount,0) + ISNULL(i.voucher_from_shopee,0)) AS api_tax_buyer_total_path
FROM dbo.shopee_orders o
JOIN dbo.shopee_escrow_detail d ON o.OrderID = d.OrderID
JOIN dbo.shopee_escrow_order_income i ON d.EscrowID = i.EscrowID
WHERE o.order_sn IN (
  N'260702JM3ES70Q',
  N'260714M9V6WA1A',
  N'2607194CVHYMJ5',
  N'260715QX63GQRG',
  N'26071816BH45QD',
  N'2607182E7JJY2P'
)
ORDER BY o.order_sn;
GO

-- Missing escrow for Check (example July missing-API orders):
SELECT o.order_sn, o.order_status, d.EscrowID
FROM dbo.shopee_orders o
LEFT JOIN dbo.shopee_escrow_detail d ON o.OrderID = d.OrderID
WHERE o.order_sn IN (
  N'2607182BQ0TKXS',
  N'2607182E7JJY2P',
  N'26071816BH45QD',
  N'26071816UBFW48',
  N'26071818Y8WQWS',
  N'2607181CPYU9J4',
  N'2607181CTJ9XMR'
);
GO
