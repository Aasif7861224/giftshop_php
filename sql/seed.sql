-- sql/seed.sql
-- Default admin: admin@demo.com / Admin@123
-- (Change password after first login)

INSERT INTO admins (name, email, password_hash, role)
VALUES ('Admin', 'admin@demo.com', '$2y$10$u/3DjaQZs0Nkf3K5ngGa3uP5gyR/cUH0rDPob.oM09Q6mCmGm/7Ye', 'admin');

INSERT INTO categories (name, slug, image) VALUES
('Cakes', 'cakes', 'images/cake.jpg'),
('Flowers', 'flowers', 'images/flowers.jpg'),
('Chocolates', 'chocolates', 'images/chocolates.jpg'),
('Gift Boxes', 'gift-boxes', 'images/giftbox.jpg');

INSERT INTO products (category_id, name, slug, description, price, stock, image) VALUES
(1,'Chocolate Cake','chocolate-cake','Rich & creamy chocolate cake.',599,20,'images/cake.jpg'),
(2,'Flower Bouquet','flower-bouquet','Fresh flowers bouquet for any occasion.',449,30,'images/flowers.jpg'),
(3,'Ferrero Pack','ferrero-pack','Premium chocolates gift pack.',499,25,'images/Ferrero.jpg'),
(4,'Gift Box','gift-box','Surprise gift box with goodies.',999,10,'images/giftbox.jpg');
