CREATE TABLE orders (

    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    order_number VARCHAR(30) UNIQUE,
    payment_status ENUM ('success', 'pending', 'refunded') NOT NULL,
    delivery_status ENUM ('processing', 'shipped', 'delivered', 'returned') NOT NULL,
    currency CHAR(3),
    total_amount DECIMAL(10,2),
    shipping_amount DECIMAL(10,2),
    shipping_address TEXT,
    discount_amount DECIMAL(10,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);

CREATE TABLE order_items(

    order_items_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    size_id INT,
    FOREIGN KEY (size_id) REFERENCES inventory(size_id),
    quantity INT,
    price DECIMAL(10,2),
    line_total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);

CREATE TABLE payments(

payment_id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT,
FOREIGN KEY (order_id) REFERENCES orders(order_id),
payment_method VARCHAR(64),
currency CHAR(3),
total_amount DECIMAL(10,2),
transaction_reference VARCHAR(100), 
payment_status ENUM ('success', 'pending', 'refunded') NOT NULL,
payment_date TIMESTAMP DEFAULT NULL,     
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);