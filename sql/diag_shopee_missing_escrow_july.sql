/*
  July Check: 7 orders with Excel tax but no escrow (missing API).
  Run on bnyfoodproducts — read only.
*/
USE [bnyfoodproducts];
GO

SELECT
  o.order_sn,
  o.OrderID,
  o.order_status,
  o.create_time,
  d.EscrowID
FROM dbo.shopee_orders o
LEFT JOIN dbo.shopee_escrow_detail d ON o.OrderID = d.OrderID
WHERE o.order_sn IN (
  N'26071816BH45QD',
  N'26071816UBFW48',
  N'26071818Y8WQWS',
  N'2607181CPYU9J4',
  N'2607181CTJ9XMR',
  N'2607182BQ0TKXS',
  N'2607182E7JJY2P'
)
ORDER BY o.order_sn, o.OrderID;
GO

-- If EscrowID is NULL: escrow download never ran for that OrderID.
-- Shopee automation uses get_next_sn() for OrderID NOT IN escrow_detail.
-- After escrow is filled, re-Check; formula will use original + Excel ship.
