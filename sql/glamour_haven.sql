CREATE DATABASE IF NOT EXISTS glamour_haven;
USE glamour_haven;

CREATE USER IF NOT EXISTS 'glamour_user'@'localhost' IDENTIFIED BY 'GlamourPass2026!';
GRANT SELECT, INSERT, UPDATE, DELETE ON glamour_haven.* TO 'glamour_user'@'localhost';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(50),
    zip VARCHAR(10),
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    brand VARCHAR(100),
    featured TINYINT(1) DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address VARCHAR(255),
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(50),
    shipping_zip VARCHAR(10),
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (first_name, last_name, email, password, phone, role) VALUES
('Admin', 'User', 'admin@glamourhaven.com', '$2y$10$YMjKDG6GHCOPxFJ0kN2FXOm8v9y3VkVgVQ0bRz.5TjKrM1lPwqGHe', '555-000-0001', 'admin');

INSERT INTO users (first_name, last_name, email, password, phone, address, city, state, zip, role) VALUES
('Jane', 'Doe', 'jane@example.com', '$2y$10$YMjKDG6GHCOPxFJ0kN2FXOm8v9y3VkVgVQ0bRz.5TjKrM1lPwqGHe', '555-111-1111', '123 Beauty Lane', 'Los Angeles', 'CA', '90001', 'customer'),
('Sarah', 'Smith', 'sarah@example.com', '$2y$10$YMjKDG6GHCOPxFJ0kN2FXOm8v9y3VkVgVQ0bRz.5TjKrM1lPwqGHe', '555-222-2222', '456 Glamour Ave', 'New York', 'NY', '10001', 'customer'),
('Emily', 'Johnson', 'emily@example.com', '$2y$10$YMjKDG6GHCOPxFJ0kN2FXOm8v9y3VkVgVQ0bRz.5TjKrM1lPwqGHe', '555-333-3333', '789 Makeup Blvd', 'Chicago', 'IL', '60601', 'customer');

INSERT INTO categories (name, description, image) VALUES
('Face', 'Foundation, concealer, primer, setting spray, and more for a flawless complexion.', 'category_face.jpg'),
('Eyes', 'Eyeshadow, mascara, eyeliner, and brow products to make your eyes pop.', 'category_eyes.jpg'),
('Lips', 'Lipstick, lip gloss, lip liner, and lip care for the perfect pout.', 'category_lips.jpg'),
('Tools & Brushes', 'Professional makeup brushes, sponges, and beauty tools.', 'category_tools.jpg');

INSERT INTO products (category_id, name, description, price, sale_price, image, stock, brand, featured) VALUES
(1, 'Silk Radiance Foundation', 'Lightweight, buildable foundation with a natural satin finish. Provides medium-to-full coverage that lasts up to 24 hours. Available in 40 shades.', 42.00, 35.99, 'foundation_beige.jpg', 150, 'Glamour Haven', 1),
(1, 'Luminous Glow Primer', 'Hydrating primer with light-reflecting particles that blur imperfections and create a radiant base for makeup. Enriched with hyaluronic acid.', 34.00, NULL, 'primer_clear.jpg', 200, 'Glamour Haven', 1),
(1, 'Full Cover HD Concealer', 'High-definition concealer that covers dark circles, blemishes, and redness. Crease-proof formula with a natural matte finish.', 28.00, 22.99, 'concealer_light.jpg', 180, 'Glamour Haven', 1),
(1, 'All-Day Setting Spray', 'Lock your makeup in place for up to 16 hours with this micro-fine mist setting spray. Oil-free and suitable for all skin types.', 32.00, NULL, 'setting_spray.jpg', 120, 'Glamour Haven', 1),
(1, 'Sun-Kissed Bronzer', 'Matte bronzer for natural-looking warmth and dimension. Finely milled for seamless blending. Buildable color payoff.', 30.00, 25.99, 'bronzer_sun.jpg', 100, 'Glamour Haven', 1),
(1, 'Petal Soft Rose Blush', 'Silky powder blush in a universally flattering rose shade. Delivers a fresh, healthy flush of color that lasts all day.', 26.00, NULL, 'blush_rose.jpg', 160, 'Glamour Haven', 1),
(2, 'Midnight Dream Eyeshadow Palette', '12-shade eyeshadow palette with a mix of mattes, shimmers, and metallics. Ultra-pigmented and blendable formula.', 48.00, 39.99, 'eyeshadow_palette.jpg', 90, 'Glamour Haven', 1),
(2, 'Mega Volume Mascara', 'Dramatic volume and length in a single coat. Smudge-proof and flake-free formula with a curved precision brush.', 24.00, NULL, 'mascara_black.jpg', 250, 'Glamour Haven', 1),
(2, 'Precision Felt-Tip Eyeliner', 'Ultra-fine felt tip for precise lines and wings. Waterproof, smudge-proof formula that lasts all day. Intense black pigment.', 22.00, 18.99, 'eyeliner_black.jpg', 200, 'Glamour Haven', 1),
(2, '24K Gold Highlighter', 'Buttery-soft powder highlighter with a blinding gold shimmer. Can be used on cheekbones, brow bone, and body.', 36.00, NULL, 'highlighter_gold.jpg', 80, 'Glamour Haven', 1),
(3, 'Velvet Matte Lipstick - Red', 'Rich, full-coverage matte lipstick in a classic red shade. Comfortable wear with no drying. Infused with vitamin E.', 22.00, 18.99, 'lipstick_red.jpg', 300, 'Glamour Haven', 1),
(3, 'Crystal Shine Lip Gloss - Pink', 'High-shine lip gloss with a non-sticky formula. Enriched with jojoba oil for soft, hydrated lips. Sheer pink tint.', 18.00, NULL, 'lip_gloss_pink.jpg', 220, 'Glamour Haven', 1);

INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 2, 5, 'Best foundation I have ever used! The coverage is amazing and it looks so natural on the skin.'),
(1, 3, 4, 'Great foundation, very lightweight. Takes off half a star because it can be slightly drying after 8+ hours.'),
(7, 2, 5, 'These eyeshadow colors are stunning! So pigmented and blend beautifully.'),
(8, 4, 5, 'This mascara is incredible! My lashes look so full and long without any clumping.'),
(11, 3, 5, 'The perfect red lipstick! Stays on all day and the color is gorgeous.'),
(11, 4, 4, 'Love the color and formula. Very comfortable to wear.'),
(2, 2, 5, 'My skin looks so glowy with this primer underneath foundation. Holy grail product!'),
(5, 3, 4, 'Nice bronzer for contouring. Good color payoff but I wish it came in more shades.'),
(10, 2, 5, 'This highlighter is BLINDING in the best way possible. A little goes a long way!');

INSERT INTO orders (user_id, total_amount, shipping_address, shipping_city, shipping_state, shipping_zip, status) VALUES
(2, 78.98, '123 Beauty Lane', 'Los Angeles', 'CA', '90001', 'delivered');

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 35.99),
(1, 8, 1, 24.00),
(1, 11, 1, 18.99);
