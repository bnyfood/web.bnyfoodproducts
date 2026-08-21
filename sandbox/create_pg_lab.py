import getpass
import os
import psycopg

password = os.environ.get("PGPASSWORD")
if not password:
    password = getpass.getpass("Postgres password: ")

with psycopg.connect(
    host="192.168.1.252",
    port=5432,
    dbname="postgres",
    user="postgres",
    password=password,
    connect_timeout=8,
    sslmode="disable",
) as conn:
    conn.autocommit = True
    with conn.cursor() as cur:
        cur.execute("SELECT current_database(), current_user")
        print("connected", cur.fetchone())
        cur.execute("SELECT 1 FROM pg_database WHERE datname = 'bnyfoodproducts_lab'")
        if cur.fetchone():
            print("db_exists bnyfoodproducts_lab")
        else:
            cur.execute("CREATE DATABASE bnyfoodproducts_lab")
            print("created bnyfoodproducts_lab")
