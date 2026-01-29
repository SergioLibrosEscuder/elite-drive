-- SCRIPT INSERT USERS

-- 1. Insert Admins
INSERT INTO users (dni, first_name, last_name, second_last_name, birth_date, email, password, phone, address, role)
VALUES 
('11111111A', 'Admin-1', 'Surname-1', 'Surname-1', '1985-05-15', 'admin1@elitedrive.com', 'apass1', '600111222', 'Calle Principal 1, Valencia', 'admin'),
('22222222B', 'Admin-2', 'Surname-2', 'Surname-2', '1990-08-20', 'admin2@elitedrive.com', 'apass2', '600222333', 'Avenida Central 10, Valencia', 'admin'),
('33333333C', 'Admin-3', 'Surname-3', 'Surname-3', '1982-01-30', 'admin3@elitedrive.com', 'apass3', '600333444', 'Plaza Mayor 5, Valencia', 'admin');

-- 2. Insert Customers
INSERT INTO users (dni, first_name, last_name, second_last_name, birth_date, email, password, phone, address, role)
VALUES 
('44444444D', 'Customer-1', 'Surname-1', 'Surname-1', '1995-03-12', 'customer1@mail.com', 'upass1', '611555666', 'Calle Pez 12, Sevilla', 'customer'),
('55555555E', 'Customer-2', 'Surname-2', 'Surname-2', '2000-07-22', 'customer2@mail.com', 'upass2', '611777888', 'Calle Luna 45, Bilbao', 'customer'),
('66666666F', 'Customer-3', 'Surname-3', 'Surname-3', '1988-11-05', 'customer3@mail.com', 'upass3', '611999000', 'Calle Sol 3, Málaga', 'customer');
