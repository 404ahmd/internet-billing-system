-- Masukkan data IP Pools
INSERT INTO ip_pools (router_id, `name`, `range`, created_at, updated_at)
VALUES
(6, 'dhcp_pool0', '172.30.1.2-172.30.1.254', NOW(), NOW()),
(6, 'dhcp_pool1', '172.30.10.2-172.30.10.254', NOW(), NOW());
