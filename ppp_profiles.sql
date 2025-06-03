-- Masukkan data PPP Profiles
INSERT INTO ppp_profiles (router_id, `name`, rate_limit, local_address, remote_address, created_at, updated_at)
VALUES
(6, 'default', NULL, NULL, NULL, NOW(), NOW()),
(6, '200k', '7m/7m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, '150k', '3m/3m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'pppoe-1', '1m/1m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'pppoe-2/4', '4m/4m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, '250k', '10m/10m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'profile-live-vip', '10m/10m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, '300k', '15m/15m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'PROFIL_SSTP_CLIENT_TIK_TUNNELING', NULL, NULL, NULL, NOW(), NOW()),
(6, 'OpenVPN-TIK-TUNNELING', NULL, '172.30.31.1', '*3', NOW(), NOW()),
(6, 'PAKET VIAP-2', '40m/40m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'PAKET VIP-1', '30m/30m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'PAKET VIP-UNLIMETAD', '70m/70m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'PAKET VIP 3', '50m/50m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'free-1', '2m/2m 0/0 0/0 0/0 8 64k/64k', NULL, NULL, NOW(), NOW()),
(6, 'default-encryption', NULL, NULL, NULL, NOW(), NOW());

