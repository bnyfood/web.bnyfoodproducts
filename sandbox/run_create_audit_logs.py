import os
import sys

import psycopg

sql_path = os.path.join(os.path.dirname(__file__), "sql", "create_audit_logs.sql")
password = os.environ.get("PGPASSWORD")
if not password:
    sys.exit("PGPASSWORD missing")

with open(sql_path, encoding="utf-8") as f:
    raw = f.read()

parts = []
buf = []
for line in raw.splitlines():
    s = line.strip()
    if not s or s.startswith("--"):
        continue
    buf.append(line)
    if s.endswith(";"):
        parts.append("\n".join(buf))
        buf = []

with psycopg.connect(
    host="192.168.1.252",
    port=5432,
    dbname="bnyfoodproducts_lab",
    user="postgres",
    password=password,
    connect_timeout=10,
    sslmode="disable",
) as conn:
    conn.autocommit = True
    with conn.cursor() as cur:
        for stmt in parts:
            cur.execute(stmt)
        cur.execute(
            """
            SELECT column_name, data_type
            FROM information_schema.columns
            WHERE table_schema = 'public' AND table_name = 'audit_logs'
            ORDER BY ordinal_position
            """
        )
        print("audit_logs_ok")
        for row in cur.fetchall():
            print(row[0], row[1])
