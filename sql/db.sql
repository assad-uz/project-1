-- Table: role
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_type VARCHAR(50) NOT NULL
);

-- Table: user (Admin + Customer)
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role(id)
);

-- Table: meal_type (Dropdown reference)
CREATE TABLE meal_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) UNIQUE
);

-- Table: meal_period (Dropdown reference)
CREATE TABLE meal_period (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(100) UNIQUE
);

-- Table: food_service
CREATE TABLE food_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meal_type_id INT,
    meal_period_id INT,
    price DECIMAL(10,2),
    FOREIGN KEY (meal_type_id) REFERENCES meal_type(id),
    FOREIGN KEY (meal_period_id) REFERENCES meal_period(id)
);

-- Table: room_service
CREATE TABLE room_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100),
    price DECIMAL(10,2)
);

-- Table: service
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_service_id INT,
    food_service INT,
    service_price DECIMAL(10,2),
    FOREIGN KEY (room_service_id) REFERENCES room_service(id),
    FOREIGN KEY (food_service) REFERENCES food_service(id)
);

-- Table: room_type
CREATE TABLE room_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL
);

-- Table: room (No bed_info, uses room_type only)
CREATE TABLE room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT,
    room_type_id INT NOT NULL,
    room_id VARCHAR(50) NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    room_price DECIMAL(10,2) NOT NULL,
    room_status VARCHAR(50),
    description VARCHAR(255),
    FOREIGN KEY (service_id) REFERENCES service(id),
    FOREIGN KEY (room_type_id) REFERENCES room_type(id)
);

-- Table: booking
CREATE TABLE booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    booking_date DATETIME NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    payment_status VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (room_id) REFERENCES room(id)
);

-- Table: cancellation
CREATE TABLE cancellation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    cancel_date DATE NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE
);

-- Table: checkin
CREATE TABLE checkin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE
);

-- Table: checkout
CREATE TABLE checkout (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    checkout_date DATE NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE
);

-- Table: invoice
CREATE TABLE invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    payment_status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (booking_id) REFERENCES booking(id)
);

-- Table: payment
CREATE TABLE payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    invoice_id INT NOT NULL,
    payment_method VARCHAR(50),
    FOREIGN KEY (booking_id) REFERENCES booking(id),
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (invoice_id) REFERENCES invoice(id)
);

-- Table: transaction
CREATE TABLE transaction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    payment_id INT NOT NULL,
    approved_by INT NOT NULL, -- Admin's user_id
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (booking_id) REFERENCES booking(id),
    FOREIGN KEY (payment_id) REFERENCES payment(id),
    FOREIGN KEY (approved_by) REFERENCES user(id)
);
