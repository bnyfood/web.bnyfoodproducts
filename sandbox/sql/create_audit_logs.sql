-- bnyfoodproducts_lab
-- Centralized JSONB audit for master data (products, BOM, settings).
-- Not used to rebuild issued orders/invoices — those use inline snapshots.
-- tenant_id INT maps to legacy ShopID.

CREATE TABLE IF NOT EXISTS audit_logs (
    id              BIGSERIAL PRIMARY KEY,
    tenant_id       INT NOT NULL,
    table_name      VARCHAR(100) NOT NULL,
    record_id       VARCHAR(100) NOT NULL,
    action          VARCHAR(10) NOT NULL,
    old_values      JSONB,
    new_values      JSONB,
    changed_fields  JSONB,
    user_id         INT,
    user_email      VARCHAR(255),
    user_role       VARCHAR(50),
    ip_address      VARCHAR(45),
    user_agent      VARCHAR(500),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_audit_logs_action CHECK (action IN ('INSERT', 'UPDATE', 'DELETE'))
);

CREATE INDEX IF NOT EXISTS idx_audit_tenant_table
    ON audit_logs (tenant_id, table_name, record_id);

CREATE INDEX IF NOT EXISTS idx_audit_created_at
    ON audit_logs (created_at DESC);

CREATE INDEX IF NOT EXISTS idx_audit_user
    ON audit_logs (tenant_id, user_id, created_at DESC);

CREATE INDEX IF NOT EXISTS idx_audit_changed_fields_gin
    ON audit_logs USING GIN (changed_fields);
