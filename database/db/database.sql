CREATE DATABASE mapas_j23;
USE mapas_j23;

-- Tabla de roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- INSERTS ROLES
-- INSERT INTO roles (name, created_at, updated_at) 
-- VALUES ('user', NOW(), NOW());

-- INSERT INTO roles (name, created_at, updated_at) 
-- VALUES ('admin', NOW(), NOW());

-- Tabla de gimcanas
CREATE TABLE gymkhanas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de etiquetas
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de lugares de interés
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion TEXT NOT NULL,
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de grupos
CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,  -- Código único del grupo
    creador INT NOT NULL,                -- ID del usuario creador del grupo
    max_miembros INT NOT NULL,           -- Capacidad máxima del grupo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creador) REFERENCES users(id),
) ENGINE=InnoDB;


-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- INSERTS USERS
-- INSERT INTO users (name, email, password, role_id, created_at, updated_at) 
-- VALUES ('user', 'user@example.com', '$2y$12$lzOWclOxoyDaWoW/yO64Nekqmt76YHsy52p7cmC2M7De3A/fHxqbe', 1, NOW(), NOW());

-- INSERT INTO users (name, email, password, role_id, created_at, updated_at) 
-- VALUES ('admin', 'admin@example.com', '$2y$12$lzOWclOxoyDaWoW/yO64Nekqmt76YHsy52p7cmC2M7De3A/fHxqbe', 2, NOW(), NOW());


-- Relación entre lugares e etiquetas
CREATE TABLE place_tags (
    place_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (place_id, tag_id),
    FOREIGN KEY (place_id) REFERENCES places(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
) ENGINE=InnoDB;

-- Tabla de favoritos
CREATE TABLE favorites (
    user_id INT NOT NULL,
    place_id INT NOT NULL,
    PRIMARY KEY (user_id, place_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (place_id) REFERENCES places(id)
) ENGINE=InnoDB;

-- Tabla de puntos de control
CREATE TABLE checkpoints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gymkhana_id INT NOT NULL,
    place_id INT NOT NULL,
    pista TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (gymkhana_id) REFERENCES gymkhanas(id),
    FOREIGN KEY (place_id) REFERENCES places(id)
) ENGINE=InnoDB;


-- Relación entre usuarios y grupos
CREATE TABLE group_users (
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Tabla de progreso en la gimcana
CREATE TABLE gymkhana_progress (
    group_users_id INT NOT NULL,
    checkpoint_id INT NOT NULL,
    completado BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (group_users_id, checkpoint_id),
    FOREIGN KEY (user_id) REFERENCES group_users(id),
    FOREIGN KEY (checkpoint_id) REFERENCES checkpoints(id)
) ENGINE=InnoDB;