-- Enum for user roles
CREATE TYPE user_role AS ENUM ('admin', 'customer');

-- Tabla USUARIOS
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    dni VARCHAR(9) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    second_last_name VARCHAR(100),
    birth_date DATE NOT NULL CHECK(EXTRACT(YEAR FROM AGE(birth_date)) >= 18),
    registration_date DATE NOT NULL DEFAULT CURRENT_DATE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address VARCHAR(255) NOT NULL,
    role user_role NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Enum for vehicle status
CREATE TYPE vehicle_status AS ENUM ('available', 'reserved', 'maintenance');

-- Tabla VEHÍCULOS
CREATE TABLE vehicles (
    id BIGSERIAL PRIMARY KEY,
    hourly_price NUMERIC(10, 2) NOT NULL,
    manufacturing_year INT NOT NULL,
    traction VARCHAR(50) NOT NULL,
    transmission VARCHAR(50) NOT NULL,
    engine VARCHAR(100) NOT NULL,
    engine_capacity INT NOT NULL,
    description TEXT,
    doors INT NOT NULL,
    license_plate VARCHAR(10) NOT NULL UNIQUE,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    fuel_type VARCHAR(50) NOT NULL,
    color VARCHAR(50) NOT NULL,
    status vehicle_status NOT NULL DEFAULT 'available',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Enum for reservation status
CREATE TYPE reservation_status AS ENUM ('pending', 'confirmed', 'cancelled', 'completed');

-- Tabla RESERVAS
CREATE TABLE reservations (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    vehicle_id BIGINT NOT NULL,
    start_date TIMESTAMP WITH TIME ZONE NOT NULL,
    end_date TIMESTAMP WITH TIME ZONE NOT NULL,
    amount NUMERIC(10, 2) NOT NULL,
    status reservation_status NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    CONSTRAINT fk_reservations_user 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_reservations_vehicle 
        FOREIGN KEY (vehicle_id) 
        REFERENCES vehicles(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- Constraint to ensure end_date > start_date
    CONSTRAINT check_dates 
        CHECK (end_date > start_date)
);

-- Indices for better performance
CREATE INDEX idx_reservations_user_id ON reservations(user_id);
CREATE INDEX idx_reservations_vehicle_id ON reservations(vehicle_id);
CREATE INDEX idx_reservations_start_date ON reservations(start_date);
CREATE INDEX idx_reservations_status ON reservations(status);

-- Función para actualizar automáticamente updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger para tabla users
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger para tabla vehicles
CREATE TRIGGER update_vehicles_updated_at BEFORE UPDATE ON vehicles
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger para tabla reservations
CREATE TRIGGER update_reservations_updated_at BEFORE UPDATE ON reservations
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
