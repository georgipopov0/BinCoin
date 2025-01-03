-- Fill the `user` table
INSERT INTO `user` (`name`) VALUES 
('Alice'),
('Bob'),
('Charlie'),
('Diana'),
('Edward'),
('Frank'),
('Grace'),
('Hannah'),
('Ian'),
('Jack');

-- Fill the `coin_collection` table
INSERT INTO `coin_collection` (`user_name`, `name`,`access`) VALUES 
('Alice', 'A' ,'private'),
('Bob','B' ,'public'),
('Charlie', 'A', 'private'),
('Diana', 'A', 'public'),
('Edward', 'A', 'private'),
('Frank', 'A', 'private'),
('Grace', 'A', 'public'),
('Hannah', 'A', 'private'),
('Ian', 'A', 'public'),
('Jack', 'A', 'private'),
("Alice", "Rare Coins Collection", "private"),
("Bob", "World Coins Collection", "public"),
("Charlie", "Vintage Coins", "private"),
("Diana", "Historical Coins", "public"),
("Edward", "Ancient Coins", "private"),
("Frank", "Commemorative Coins", "public"),
("Grace", "Gold Coins", "private"),
("Hannah", "Silver Coins", "public"),
("Ian", "Copper Coins", "private"),
("Jack", "Unique Finds", "public"),
("Alice", "Modern Coins Collection", "private"),
("Bob", "Euro Coins", "public"),
("Charlie", "Penny Collection", "private"),
("Diana", "Centennial Coins", "public"),
("Edward", "Royal Mint Coins", "private"),
("Frank", "Olympic Coins", "public"),
("Grace", "Limited Edition Coins", "private"),
("Hannah", "Historic Artifacts", "public"),
("Ian", "Special Releases", "private"),
("Jack", "Collector’s Choice", "public"),
("Alice", "Custom Collection", "private"),
("Bob", "Rare World Coins", "public"),
("Charlie", "Golden Era Coins", "private"),
("Diana", "Exotic Coins", "public"),
("Edward", "International Coins", "private"),
("Frank", "Medieval Coins", "public"),
("Grace", "Numismatic Treasures", "private"),
("Hannah", "Collector's Vault", "public"),
("Ian", "Timeless Coins", "private"),
("Jack", "World-Class Coins", "public");

-- Fill the `coin` table
INSERT INTO `coin` (`cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year`, `coin_collection_id`) VALUES 
(10.00, 5.00, 'USD', '/images/front1.png', '/images/back1.png', 'USA', 2000, 1),
(15.00, 7.50, 'EUR', '/images/front2.png', '/images/back2.png', 'Germany', 1995, 2),
(20.00, 10.00, 'GBP', '/images/front3.png', '/images/back3.png', 'UK', 2010, 3),
(25.00, 12.50, 'JPY', '/images/front4.png', '/images/back4.png', 'Japan', 1980, 4),
(30.00, 15.00, 'AUD', '/images/front5.png', '/images/back5.png', 'Australia', 1975, 5),
(8.00, 4.00, 'USD', '/images/front6.png', '/images/back6.png', 'USA', 2015, 1),
(12.00, 6.00, 'EUR', '/images/front7.png', '/images/back7.png', 'Germany', 1990, 2),
(18.00, 9.00, 'GBP', '/images/front8.png', '/images/back8.png', 'UK', 2005, 3),
(22.00, 11.00, 'JPY', '/images/front9.png', '/images/back9.png', 'Japan', 1970, 4),
(28.00, 14.00, 'AUD', '/images/front10.png', '/images/back10.png', 'Australia', 1985, 5),
(35.00, 17.50, 'USD', '/images/front11.png', '/images/back11.png', 'USA', 1990, 6),
(40.00, 20.00, 'EUR', '/images/front12.png', '/images/back12.png', 'Germany', 1985, 7),
(45.00, 22.50, 'GBP', '/images/front13.png', '/images/back13.png', 'UK', 1995, 8),
(50.00, 25.00, 'JPY', '/images/front14.png', '/images/back14.png', 'Japan', 1965, 9),
(55.00, 27.50, 'AUD', '/images/front15.png', '/images/back15.png', 'Australia', 1980, 10),
(100.00, 150.00, "USD", "path/to/front1.jpg", "path/to/back1.jpg", "USA", 2000, 1),
(200.00, 250.00, "EUR", "path/to/front2.jpg", "path/to/back2.jpg", "France", 1995, 2),
(300.00, 400.00, "GBP", "path/to/front3.jpg", "path/to/back3.jpg", "UK", 2010, 3),
(150.00, 180.00, "USD", "path/to/front4.jpg", "path/to/back4.jpg", "USA", 1980, 4),
(250.00, 300.00, "CAD", "path/to/front5.jpg", "path/to/back5.jpg", "Canada", 2005, 5),
(350.00, 400.00, "AUD", "path/to/front6.jpg", "path/to/back6.jpg", "Australia", 1990, 6),
(100.00, 150.00, "JPY", "path/to/front7.jpg", "path/to/back7.jpg", "Japan", 2015, 7),
(200.00, 250.00, "INR", "path/to/front8.jpg", "path/to/back8.jpg", "India", 1998, 8),
(300.00, 350.00, "USD", "path/to/front9.jpg", "path/to/back9.jpg", "USA", 1970, 9),
(150.00, 180.00, "EUR", "path/to/front10.jpg", "path/to/back10.jpg", "Germany", 1985, 10),
(250.00, 300.00, "USD", "path/to/front11.jpg", "path/to/back11.jpg", "USA", 1992, 11),
(350.00, 400.00, "GBP", "path/to/front12.jpg", "path/to/back12.jpg", "UK", 2001, 12),
(100.00, 150.00, "EUR", "path/to/front13.jpg", "path/to/back13.jpg", "Italy", 1987, 13),
(200.00, 250.00, "CAD", "path/to/front14.jpg", "path/to/back14.jpg", "Canada", 2003, 14),
(300.00, 350.00, "AUD", "path/to/front15.jpg", "path/to/back15.jpg", "Australia", 2012, 15),
(150.00, 180.00, "USD", "path/to/front16.jpg", "path/to/back16.jpg", "USA", 1999, 16),
(250.00, 300.00, "INR", "path/to/front17.jpg", "path/to/back17.jpg", "India", 1978, 17),
(350.00, 400.00, "JPY", "path/to/front18.jpg", "path/to/back18.jpg", "Japan", 1982, 18),
(100.00, 150.00, "EUR", "path/to/front19.jpg", "path/to/back19.jpg", "France", 1993, 19),
(200.00, 250.00, "USD", "path/to/front20.jpg", "path/to/back20.jpg", "USA", 2018, 20),
(300.00, 350.00, "GBP", "path/to/front21.jpg", "path/to/back21.jpg", "UK", 1988, 21),
(150.00, 180.00, "CAD", "path/to/front22.jpg", "path/to/back22.jpg", "Canada", 2000, 22),
(250.00, 300.00, "AUD", "path/to/front23.jpg", "path/to/back23.jpg", "Australia", 1975, 23),
(350.00, 400.00, "INR", "path/to/front24.jpg", "path/to/back24.jpg", "India", 2006, 24),
(100.00, 150.00, "USD", "path/to/front25.jpg", "path/to/back25.jpg", "USA", 2011, 25),
(200.00, 250.00, "EUR", "path/to/front26.jpg", "path/to/back26.jpg", "Germany", 2002, 26),
(300.00, 350.00, "JPY", "path/to/front27.jpg", "path/to/back27.jpg", "Japan", 1997, 27),
(150.00, 180.00, "USD", "path/to/front28.jpg", "path/to/back28.jpg", "USA", 1984, 28),
(250.00, 300.00, "AUD", "path/to/front29.jpg", "path/to/back29.jpg", "Australia", 1994, 29),
(350.00, 400.00, "CAD", "path/to/front30.jpg", "path/to/back30.jpg", "Canada", 1972, 30),
(100.00, 150.00, "INR", "path/to/front31.jpg", "path/to/back31.jpg", "India", 1983, 1),
(200.00, 250.00, "USD", "path/to/front32.jpg", "path/to/back32.jpg", "USA", 1996, 2),
(300.00, 350.00, "EUR", "path/to/front33.jpg", "path/to/back33.jpg", "France", 2007, 3),
(150.00, 180.00, "GBP", "path/to/front34.jpg", "path/to/back34.jpg", "UK", 1989, 4),
(250.00, 300.00, "JPY", "path/to/front35.jpg", "path/to/back35.jpg", "Japan", 1991, 5),
(350.00, 400.00, "AUD", "path/to/front36.jpg", "path/to/back36.jpg", "Australia", 2014, 6),
(100.00, 150.00, "CAD", "path/to/front37.jpg", "path/to/back37.jpg", "Canada", 1986, 7),
(200.00, 250.00, "USD", "path/to/front38.jpg", "path/to/back38.jpg", "USA", 2009, 8),
(300.00, 350.00, "EUR", "path/to/front39.jpg", "path/to/back39.jpg", "Germany", 2016, 9),
(150.00, 180.00, "GBP", "path/to/front40.jpg", "path/to/back40.jpg", "UK", 1992, 10),
(250.00, 300.00, "AUD", "path/to/front41.jpg", "path/to/back41.jpg", "Australia", 1976, 11),
(350.00, 400.00, "INR", "path/to/front42.jpg", "path/to/back42.jpg", "India", 1981, 12),
(100.00, 150.00, "JPY", "path/to/front43.jpg", "path/to/back43.jpg", "Japan", 2008, 13),
(200.00, 250.00, "CAD", "path/to/front44.jpg", "path/to/back44.jpg", "Canada", 1990, 14),
(300.00, 350.00, "USD", "path/to/front45.jpg", "path/to/back45.jpg", "USA", 2004, 15),
(150.00, 180.00, "EUR", "path/to/front46.jpg", "path/to/back46.jpg", "France", 2013, 16),
(250.00, 300.00, "GBP", "path/to/front47.jpg", "path/to/back47.jpg", "UK", 1982, 17),
(350.00, 400.00, "AUD", "path/to/front48.jpg", "path/to/back48.jpg", "Australia", 1971, 18),
(100.00, 150.00, "CAD", "path/to/front49.jpg", "path/to/back49.jpg", "Canada", 1999, 19),
(200.00, 250.00, "INR", "path/to/front50.jpg", "path/to/back50.jpg", "India", 2000, 20);


-- Fill the `access_control` table
INSERT INTO `access_control` (`user_name`, `collection_id`) VALUES 
('Alice', 2),
('Bob', 1),
('Charlie', 2),
('Diana', 3),
('Edward', 4),
('Frank', 5),
('Grace', 6),
('Hannah', 7),
('Ian', 8),
('Jack', 9);

-- Fill the `collection_tag` table
INSERT INTO `collection_tag` (`name`, `collection_id`) VALUES 
('Rare', 1),
('Limited Edition', 2),
('Historical', 3),
('Commemorative', 4),
('Modern', 5),
('Antique', 6),
('Exclusive', 7),
('Vintage', 8),
('Collectors Choice', 9),
('Special Edition', 10);

-- Fill the `period` table
INSERT INTO `period` (`country`, `from`, `to`, `name`) VALUES 
('USA', 1900, 2000, 'Modern Era'),
('Germany', 1800, 1900, 'Industrial Revolution'),
('UK', 1500, 1600, 'Renaissance'),
('Japan', 1600, 1868, 'Edo Period'),
('Australia', 1901, 2000, 'Federation Period'),
('USA', 1800, 1900, 'Gilded Age'),
('Germany', 1700, 1800, 'Baroque Period'),
('UK', 1600, 1700, 'Enlightenment'),
('Japan', 1868, 1912, 'Meiji Era'),
('Australia', 1850, 1900, 'Gold Rush');

-- Fill the `coin_period` table
INSERT INTO `coin_period` (`coin_id`, `period_id`) VALUES 
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 1),
(12, 2),
(13, 3),
(14, 4),
(15, 5);

-- Fill the `trade` table
INSERT INTO `trade` (`seller_name`, `buyer_name`, `status`, `coin_id`) VALUES 
('Alice', 'Bob', 'pending', 1),
('Bob', 'Charlie', 'pending', 2),
('Charlie', 'Diana', 'pending', 3),
('Diana', 'Edward', 'pending', 4),
('Edward', 'Alice', 'pending', 5),
('Alice', 'Diana', 'pending', 6),
('Bob', 'Edward', 'pending', 7),
('Charlie', 'Alice', 'pending', 8),
('Diana', 'Bob', 'pending', 9),
('Edward', 'Charlie', 'pending', 10),
('Frank', 'Grace', 'pending', 11),
('Grace', 'Hannah', 'pending', 12),
('Hannah', 'Ian', 'pending', 13),
('Ian', 'Jack', 'pending', 14),
('Jack', 'Frank', 'pending', 15),
("Alice", "Bob", "pending", 1),
("Bob", "Charlie", "pending", 2),
("Charlie", "Diana", "pending", 3),
("Diana", "Edward", "pending", 4),
("Edward", "Frank", "pending", 5),
("Frank", "Grace", "pending", 6),
("Grace", "Hannah", "pending", 7),
("Hannah", "Ian", "pending", 8),
("Ian", "Jack", "pending", 9),
("Jack", "Alice", "pending", 10),
("Alice", "Bob", "pending", 11),
("Bob", "Charlie", "pending", 12),
("Charlie", "Diana", "pending", 13),
("Diana", "Edward", "pending", 14),
("Edward", "Frank", "pending", 15),
("Frank", "Grace", "pending", 16),
("Grace", "Hannah", "pending", 17),
("Hannah", "Ian", "pending", 18),
("Ian", "Jack", "pending", 19),
("Jack", "Alice", "pending", 20),
("Alice", "Bob", "pending", 21),
("Bob", "Charlie", "pending", 22),
("Charlie", "Diana", "pending", 23),
("Diana", "Edward", "pending", 24),
("Edward", "Frank", "pending", 25),
("Frank", "Grace", "pending", 26),
("Grace", "Hannah", "pending", 27),
("Hannah", "Ian", "pending", 28),
("Ian", "Jack", "pending", 29),
("Jack", "Alice", "pending", 30),
("Alice", "Bob", "pending", 31),
("Bob", "Charlie", "pending", 32),
("Charlie", "Diana", "pending", 33),
("Diana", "Edward", "pending", 34),
("Edward", "Frank", "pending", 35),
("Frank", "Grace", "pending", 36),
("Grace", "Hannah", "pending", 37),
("Hannah", "Ian", "pending", 38),
("Ian", "Jack", "pending", 39),
("Jack", "Alice", "pending", 40),
("Alice", "Bob", "pending", 41),
("Bob", "Charlie", "pending", 42),
("Charlie", "Diana", "pending", 43),
("Diana", "Edward", "pending", 44),
("Edward", "Frank", "pending", 45),
("Frank", "Grace", "pending", 46),
("Grace", "Hannah", "pending", 47),
("Hannah", "Ian", "pending", 48),
("Ian", "Jack", "pending", 49),
("Jack", "Alice", "pending", 50);

