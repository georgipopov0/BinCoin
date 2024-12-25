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
('Jack', 'A', 'private');

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
(55.00, 27.50, 'AUD', '/images/front15.png', '/images/back15.png', 'Australia', 1980, 10);

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
('Alice', 'Bob', 'completed', 1),
('Bob', 'Charlie', 'pending', 2),
('Charlie', 'Diana', 'cancelled', 3),
('Diana', 'Edward', 'completed', 4),
('Edward', 'Alice', 'pending', 5),
('Alice', 'Diana', 'completed', 6),
('Bob', 'Edward', 'pending', 7),
('Charlie', 'Alice', 'cancelled', 8),
('Diana', 'Bob', 'completed', 9),
('Edward', 'Charlie', 'pending', 10),
('Frank', 'Grace', 'completed', 11),
('Grace', 'Hannah', 'pending', 12),
('Hannah', 'Ian', 'cancelled', 13),
('Ian', 'Jack', 'completed', 14),
('Jack', 'Frank', 'pending', 15);
