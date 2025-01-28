-- Insert Users
INSERT INTO `user` (`name`, `password`, `created_at`) VALUES
('alice', 'password123', '2024-01-01 10:00:00'),
('bob', 'securepass', '2024-02-15 12:30:00'),
('charlie', 'charliepwd', '2024-03-20 09:45:00');

-- Insert Coin Collections
INSERT INTO `coin_collection` (`id`, `name`, `user_name`, `access`) VALUES
(1, 'Alice\'s Rare Coins', 'alice', 'public'),
(2, 'Alice\'s Modern Coins', 'alice', 'private'),
(3, 'Bob\'s Collection', 'bob', 'public'),
(4, 'Charlie\'s Classic Coins', 'charlie', 'private');

-- Insert Access Control Entries
INSERT INTO `access_control` (`id`, `user_name`, `collection_id`) VALUES
(1, 'alice', 1),
(2, 'bob', 1),
(3, 'alice', 2),
(4, 'bob', 3),
(5, 'charlie', 3),
(6, 'charlie', 4);

-- Insert Collection Tags
INSERT INTO `collection_tag` (`id`, `name`, `collection_id`) VALUES
(1, 'rare', 1),
(2, 'ancient', 1),
(3, 'modern', 2),
(4, 'vintage', 3),
(5, 'classic', 4),
(6, 'silver', 4);

-- Insert Periods
INSERT INTO `period` (`id`, `country`, `from`, `to`, `name`) VALUES
(1, 'Italy', 1300, 1600, 'Renaissance'),
(2, 'USA', 1760, 1840, 'Industrial Age'),   
(3, 'Global', 1900, 2024, 'Modern Era');

-- Insert Coins
INSERT INTO `coin` (`id`, `cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year`, `coin_collection_id`) VALUES
(1, 100.00, 150.00, 'USD', '/assets/images/initial_data/1f.jpg', '/assets/images/initial_data/1b.jpg', 'Italy', 1500, 1),
(2, 200.00, 300.00, 'USD', '/assets/images/initial_data/2f.jpg', '/assets/images/initial_data/2b.jpg', 'USA', 1800, 1),
(3, 50.00, 75.00, 'USD', '/assets/images/initial_data/3f.jpg', '/assets/images/initial_data/3b.jpg', 'Canada', 2000, 2),
(4, 80.00, 120.00, 'USD', '/assets/images/initial_data/4f.jpg', '/assets/images/initial_data/4b.jpg', 'UK', 1900, 3),
(5, 60.00, 90.00, 'EUR', '/assets/images/initial_data/5f.jpg', '/assets/images/initial_data/5b.jpg', 'Germany', 1950, 4),
(6, 70.00, 110.00, 'EUR', '/assets/images/initial_data/6f.jpg', '/assets/images/initial_data/6b.jpg', 'France', 1960, 4);

-- Insert Coin-Period Relationships
INSERT INTO `coin_period` (`id`, `coin_id`, `period_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 2),
(5, 4, 3),
(6, 5, 3),
(7, 6, 3);

-- Insert Trades
INSERT INTO `trade` (`id`, `seller_name`, `buyer_name`, `status`, `coin_id`) VALUES
(1, 'alice', 'bob', 'completed', 3),
(2, 'bob', 'charlie', 'pending', 4),
(3, 'charlie', 'alice', 'completed', 5);


-- Insert More Users
INSERT INTO `user` (`name`, `password`, `created_at`) VALUES
('dave', 'davepass', '2024-04-10 14:20:00'),
('eve', 'evepassword', '2024-05-05 16:45:00'),
('frank', 'franksecure', '2024-06-18 11:30:00'),
('grace', 'gracepwd', '2024-07-22 09:15:00');

-- Insert More Coin Collections
INSERT INTO `coin_collection` (`id`, `name`, `user_name`, `access`) VALUES
(5, 'Dave\'s Ancient Coins', 'dave', 'public'),
(6, 'Eve\'s Silver Collection', 'eve', 'private'),
(7, 'Frank\'s World Coins', 'frank', 'public'),
(8, 'Grace\'s Limited Edition', 'grace', 'private');

-- Insert More Access Control Entries
INSERT INTO `access_control` (`id`, `user_name`, `collection_id`) VALUES
(7, 'dave', 5),
(8, 'eve', 5),
(9, 'frank', 5),
(10, 'alice', 6),
(11, 'dave', 6),
(12, 'eve', 7),
(13, 'grace', 7),
(14, 'frank', 8),
(15, 'grace', 8);

-- Insert More Collection Tags
INSERT INTO `collection_tag` (`id`, `name`, `collection_id`) VALUES
(7, 'ancient', 5),
(8, 'gold', 5),
(9, 'silver', 6),
(10, 'world', 7),
(11, 'limited', 8),
(12, 'edition', 8);

-- Insert More Periods
INSERT INTO `period` (`id`, `country`, `from`, `to`, `name`) VALUES
(4, 'Egypt', 300, 332, 'Ptolemaic Period'),
(5, 'China', 618, 907, 'Tang Dynasty'),
(6, 'France', 1789, 1799, 'French Revolution');

-- Insert More Coins
INSERT INTO `coin` (`id`, `cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year`, `coin_collection_id`) VALUES
(7, 150.00, 225.00, 'USD', '/assets/images/initial_data/7f.jpg', '/assets/images/initial_data/7f.jpg', 'Egypt', 320, 5),
(8, 120.00, 180.00, 'USD', '/assets/images/initial_data/8f.jpg', '/assets/images/initial_data/8f.jpg', 'China', 700, 5),
(9, 90.00, 135.00, 'EUR', '/assets/images/initial_data/9f.jpg', '/assets/images/initial_data/9f.jpg', 'France', 1795, 5),
(10, 200.00, 250.00, 'USD', '/assets/images/initial_data/10f.jpg', '/assets/images/initial_data/10f.jpg', 'USA', 1990, 6),
(11, 300.00, 450.00, 'EUR', '/assets/images/initial_data/11f.jpg', '/assets/images/initial_data/11f.jpg', 'Germany', 2005, 7),
(12, 500.00, 750.00, 'GBP', '/assets/images/initial_data/12f.jpg', '/assets/images/initial_data/12f.jpg', 'UK', 2020, 8);

-- Insert More Coin-Period Relationships
INSERT INTO `coin_period` (`id`, `coin_id`, `period_id`) VALUES
(8, 7, 4),
(9, 8, 5),
(10, 9, 6),
(11, 10, 3),
(12, 11, 3),
(13, 12, 3);

-- Insert More Trades
INSERT INTO `trade` (`id`, `seller_name`, `buyer_name`, `status`, `coin_id`) VALUES
(4, 'dave', 'eve', 'completed', 7),
(5, 'eve', 'frank', 'pending', 8),
(6, 'frank', 'grace', 'completed', 9),
(7, 'alice', 'dave', 'pending', 10),
(8, 'bob', 'eve', 'completed', 11),
(9, 'charlie', 'frank', 'pending', 12);
