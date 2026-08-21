-- Shopee CN document date (issue stamp):
-- Path B: MIN(update_time) of CANCELLED
-- Path A: MIN(end_time) of shopee_return_order at REFUND_PAID / COMPLETED
-- Sold gate: PROCESSED or tax invoice. Report still joins tax invoice (CN ⊂ sales).
-- TO_RETURN is not an issue stamp.

SELECT x.order_sn, x.cn_event_at
FROM (
	SELECT order_sn, MIN(cn_event_at) AS cn_event_at
	FROM (
		SELECT order_sn, update_time AS cn_event_at
		FROM shopee_orders
		WHERE order_status = 'CANCELLED'
		UNION ALL
		SELECT order_sn, end_time AS cn_event_at
		FROM shopee_return_order
		WHERE UPPER(status) IN ('REFUND_PAID', 'COMPLETED')
			AND end_time IS NOT NULL
	) e
	GROUP BY order_sn
) x
INNER JOIN Shopee_taxinvoiceid t ON t.order_sn = x.order_sn
WHERE CONVERT(date, x.cn_event_at) >= '2026-07-01'
	AND CONVERT(date, x.cn_event_at) <= '2026-07-31'
	AND (
		EXISTS (
			SELECT 1 FROM shopee_orders p
			WHERE p.order_sn = x.order_sn
			AND p.order_status = 'PROCESSED'
		)
		OR EXISTS (
			SELECT 1 FROM Shopee_taxinvoiceid inv
			WHERE inv.order_sn = x.order_sn
		)
	)
ORDER BY x.cn_event_at, x.order_sn;
