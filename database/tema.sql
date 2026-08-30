-- ============================================
-- TABLA DE CONFIGURACION DE TEMA
-- Ejecutar UNA SOLA VEZ para agregar la tabla
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion_tema (
    id SERIAL PRIMARY KEY,
    variable VARCHAR(50) NOT NULL UNIQUE,
    valor VARCHAR(20) NOT NULL,
    descripcion VARCHAR(100)
);

INSERT INTO configuracion_tema (variable, valor, descripcion) VALUES
('color_primario', '#EC407A', 'Color principal de la app'),
('color_primario_oscuro', '#AD1457', 'Color del sidebar y titulos'),
('color_fondo', '#FCE4EC', 'Fondo de pagina')
ON CONFLICT (variable) DO NOTHING;
