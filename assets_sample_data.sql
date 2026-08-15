-- ============================================================================
--  Tambahan 50 sample data aset untuk asset_db
--  Dijalankan setelah assets_app.sql utama.
-- ============================================================================

INSERT INTO `assets`
(`asset_code`,`name`,`category_id`,`brand_spec`,`location`,`status`,`purchase_date`,`price`) VALUES
-- Komputer (category_id=1)
('AST-0011','PC Desktop HR 01',         1,'Dell OptiPlex 7080 / i5-10500 / 8GB / SSD 256GB','Ruang HRD','tersedia','2023-01-15',  9200000),
('AST-0012','PC Desktop Finance 01',    1,'HP ProDesk 400 G7 / i3-10100 / 8GB / HDD 1TB','Ruang Keuangan','tersedia','2022-08-20',  7500000),
('AST-0013','PC Desktop Finance 02',    1,'HP ProDesk 400 G7 / i3-10100 / 8GB / HDD 1TB','Ruang Keuangan','tersedia','2022-08-20',  7500000),
('AST-0014','PC Desktop Operasional 01',1,'Lenovo ThinkCentre M70a / i5-10400 / 8GB / SSD 256GB','Ruang Operasional','tersedia','2023-03-10',  8800000),
('AST-0015','PC Desktop Operasional 02',1,'Lenovo ThinkCentre M70a / i5-10400 / 8GB / SSD 256GB','Ruang Operasional','rusak','2023-03-10',  8800000),
('AST-0016','PC Desktop IT Support',    1,'Asus ExpertCenter D500 / Ryzen 5 3600 / 16GB / SSD 512GB','Ruang IT','tersedia','2023-06-25', 10500000),
('AST-0017','Workstation Design',       1,'HP Z2 Mini G9 / i7-13700 / 32GB / SSD 1TB / RTX A2000','Ruang Kreatif','tersedia','2024-01-10', 28000000),
('AST-0018','PC Desktop Training 01',   1,'Dell Vostro 3681 / i3-10100 / 4GB / HDD 500GB','Ruang Training','tersedia','2021-05-12',  5500000),
('AST-0019','PC Desktop Training 02',   1,'Dell Vostro 3681 / i3-10100 / 4GB / HDD 500GB','Ruang Training','dipinjam','2021-05-12',  5500000),
('AST-0020','PC Desktop Training 03',   1,'Dell Vostro 3681 / i3-10100 / 4GB / HDD 500GB','Ruang Training','tersedia','2021-05-12',  5500000),

-- Laptop (category_id=2)
('AST-0021','Laptop IT 01',             2,'Lenovo ThinkPad T14 / Ryzen 7 5850U / 16GB / SSD 512GB','Ruang IT','tersedia','2023-04-18', 15500000),
('AST-0022','Laptop IT 02',             2,'Lenovo ThinkPad T14 / Ryzen 7 5850U / 16GB / SSD 512GB','Ruang IT','dipinjam','2023-04-18', 15500000),
('AST-0023','Laptop Sales 01',          2,'HP EliteBook 840 G8 / i5-1135G7 / 8GB / SSD 256GB','Ruang Sales','tersedia','2023-07-05', 12500000),
('AST-0024','Laptop Sales 02',          2,'HP EliteBook 840 G8 / i5-1135G7 / 8GB / SSD 256GB','Ruang Sales','dipinjam','2023-07-05', 12500000),
('AST-0025','Laptop Sales 03',          2,'Asus ZenBook 14 / i7-1165G7 / 16GB / SSD 512GB','Ruang Sales','tersedia','2023-09-22', 14000000),
('AST-0026','Laptop Developer 01',      2,'MacBook Pro 14 M3 / 16GB / SSD 512GB','Ruang Developer','tersedia','2024-02-15', 32000000),
('AST-0027','Laptop Developer 02',      2,'Dell XPS 15 9520 / i7-12700H / 32GB / SSD 1TB','Ruang Developer','tersedia','2023-10-30', 25000000),
('AST-0028','Laptop Developer 03',      2,'Lenovo Legion 5 / Ryzen 7 6800H / 32GB / SSD 1TB / RTX 3060','Ruang Developer','dipinjam','2023-11-08', 22000000),
('AST-0029','Laptop Direksi 02',        2,'MacBook Pro 16 M2 Pro / 16GB / SSD 1TB','Ruang Direksi','dipinjam','2024-03-01', 38000000),
('AST-0030','Laptop Operasional',       2,'Acer Swift 3 / Ryzen 5 5500U / 8GB / SSD 256GB','Ruang Operasional','rusak','2022-06-14',  9500000),

-- Printer (category_id=3)
('AST-0031','Printer Laser Finance',    3,'HP LaserJet Pro M404dn','Ruang Keuangan','tersedia','2022-09-05',  3500000),
('AST-0032','Printer Laser Marketing',  3,'Brother HL-L2351DW','Ruang Marketing','tersedia','2023-02-28',  2200000),
('AST-0033','Printer Multifungsi HR',   3,'Epson EcoTank L4260','Ruang HRD','tersedia','2023-05-15',  4500000),
('AST-0034','Printer Dot Matrix',       3,'Epson LX-310','Ruang Operasional','tersedia','2020-11-20',  3200000),
('AST-0035','Printer Laser Color',      3,'HP Color LaserJet Pro M255dw','Ruang Kreatif','tersedia','2023-08-10',  5800000),
('AST-0036','Scanner Dokumen',          3,'Canon CanoScan LiDE 400','Ruang HRD','tersedia','2022-04-08',  1500000),
('AST-0037','Printer Multifungsi IT',   3,'Brother MFC-L3780CDW','Ruang IT','tersedia','2024-01-20',  7200000),
('AST-0038','Printer Thermal',          3,'Epson TM-T82II','Lobi Utama','tersedia','2021-12-03',  2800000),
('AST-0039','Printer Inkjet Lobi',      3,'Canon PIXMA G3010','Lobi Utama','rusak','2020-08-15',  2100000),
('AST-0040','Printer Laser Operasional',3,'Fuji Xerox DocuPrint P265dw','Ruang Operasional','tersedia','2022-10-22',  3300000),

-- Jaringan (category_id=4)
('AST-0041','Router Utama',             4,'MikroTik RB4011iGS+RM','Ruang Server','tersedia','2023-01-20',  5500000),
('AST-0042','Router Backup',            4,'MikroTik hEX S','Ruang Server','tersedia','2023-01-20',  1800000),
('AST-0043','Switch Access 01',         4,'Cisco SG250-24 24-Port','Ruang Server','tersedia','2022-12-15',  4500000),
('AST-0044','Switch Access 02',         4,'TP-Link TL-SG1024DE 24-Port','Lantai 2','tersedia','2022-12-15',  2200000),
('AST-0045','Switch Access 03',         4,'TP-Link TL-SG1024DE 24-Port','Lantai 3','tersedia','2022-12-15',  2200000),
('AST-0046','Access Point Lantai 1',    4,'Ubiquiti UniFi U6-Pro','Lantai 1','tersedia','2023-10-05',  3500000),
('AST-0047','Access Point Lantai 2',    4,'Ubiquiti UniFi U6-Pro','Lantai 2','tersedia','2023-10-05',  3500000),
('AST-0048','Access Point Lantai 3',    4,'Ubiquiti UniFi U6-Pro','Lantai 3','rusak','2023-10-05',  3500000),
('AST-0049','Firewall',                 4,'FortiGate 60F','Ruang Server','tersedia','2023-06-30', 18500000),
('AST-0050','Modem Backup',             4,'ZTE F670L GPON','Ruang Server','tersedia','2022-05-18',   850000),

-- Umum (category_id=5)
('AST-0051','AC Split 1/2 PK',          5,'Panasonic CS-YW5WKJ','Ruang HRD','tersedia','2022-07-10',  3500000),
('AST-0052','AC Split 1 PK',            5,'Daikin R32 inverter','Ruang Marketing','tersedia','2022-07-10',  4200000),
('AST-0053','AC Split 2 PK',            5,'LG S2NE12AJC','Ruang Rapat','tersedia','2021-09-30',  6800000),
('AST-0054','AC Split 1 PK',            5,'Sharp AH-A5UCY','Ruang Operasional','rusak','2020-06-25',  3900000),
('AST-0055','Proyektor Rapat 02',       5,'BenQ MS560 3800 lumen','Ruang Rapat','tersedia','2023-05-20',  5500000),
('AST-0056','Proyektor Training',       5,'ViewSonic PA503S 3600 lumen','Ruang Training','tersedia','2022-11-15',  4800000),
('AST-0057','TV LED 55 inch',           5,'Samsung UA55CU8500','Lobi Utama','tersedia','2023-12-10', 11000000),
('AST-0058','TV LED 43 inch',           5,'LG 43UR8050','Ruang Rapat','tersedia','2023-12-10',  6500000),
('AST-0059','Kulkas',                   5,'Sharp SJ-X165GSL','Pantry','tersedia','2022-03-08',  4500000),
('AST-0060','Mesin Kopi',               5,'Philips 2200 Series','Pantry','tersedia','2023-08-25',  5800000);

-- Log untuk beberapa aset baru
INSERT INTO `asset_logs` (`asset_id`,`user_id`,`action`,`note`) VALUES
(15,1,'rusak','Motherboard rusak, menunggu sparepart'),
(22,2,'dipinjam','Dipinjam oleh tim IT untuk pengecekan jaringan cabang'),
(24,2,'dipinjam','Dipinjam oleh sales untuk presentasi klien luar kota'),
(28,2,'dipinjam','Dipinjam developer untuk testing aplikasi di lapangan'),
(29,2,'dipinjam','Dipinjam direksi untuk perjalanan dinas luar negeri'),
(30,1,'rusak','SSD rusak, data gagal recovery'),
(39,1,'rusak','Modul scanner error, butuh service center'),
(48,1,'rusak','Perangkat tidak mau boot, indikasi hardware failure');
