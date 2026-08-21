SELECT
	order_sn,
	order_status,
	is_return,
	CONVERT(varchar(19), create_time, 120) AS create_time,
	CONVERT(varchar(19), update_time, 120) AS update_time,
	OrderID
FROM shopee_orders
WHERE order_sn = '260722C5GC7MK6'
ORDER BY update_time, OrderID;

SELECT taxinvoiceID, order_sn FROM Shopee_taxinvoiceid WHERE order_sn = '260722C5GC7MK6';
SELECT FullTaxinvoiceID, shopee_orders_OrderID FROM shopee_taxinvoice WHERE shopee_orders_OrderID IN (
	SELECT OrderID FROM shopee_orders WHERE order_sn = '260722C5GC7MK6'
);

SELECT tracking_number FROM shopee_tracking WHERE order_sn = '260722C5GC7MK6';

SELECT o.order_status, o.OrderID, i.original_price, i.original_cost_of_goods_sold,
	i.seller_discount, i.buyer_paid_shipping_fee, i.EscrowID
FROM shopee_orders o
LEFT JOIN shopee_escrow_detail d ON o.OrderID = d.OrderID
LEFT JOIN shopee_escrow_order_income i ON d.EscrowID = i.EscrowID
WHERE o.order_sn = '260722C5GC7MK6'
ORDER BY o.update_time, o.OrderID;
