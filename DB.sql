<<<<<<< HEAD
  -- Crear tabla de usuarios
  CREATE TABLE usuario (
      idUsuario INT AUTO_INCREMENT PRIMARY KEY,
      login VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      estado ENUM('activo', 'inactivo') NOT NULL
  );

  -- Crear tabla de logs de conexión
  CREATE TABLE logeo (
      idLogeo INT AUTO_INCREMENT PRIMARY KEY,
      idUsuario INT,
      login_time DATETIME NOT NULL,
      logout_time DATETIME,
      FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario)
  );


  -- Crear tablas de equipos
  CREATE TABLE laptops (
      idLaptop INT AUTO_INCREMENT PRIMARY KEY,
      codigoLaptop VARCHAR(50) NOT NULL,
      password VARCHAR(255),
      marcaLaptop VARCHAR(50),
      modeloLaptop VARCHAR(50),
      numeroSerie VARCHAR(50),
      procesador VARCHAR(50),
      RAM VARCHAR(50),
      discoDuro VARCHAR(50),
      gama VARCHAR(50),
      cargador VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE celulares (
      idCelular INT AUTO_INCREMENT PRIMARY KEY,
      codigoCelular VARCHAR(50) NOT NULL,
      password VARCHAR(255),
      marcaCelular VARCHAR(50),
      modeloCelular VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE lineas (
      idLinea INT AUTO_INCREMENT PRIMARY KEY,
      numero VARCHAR(20) NOT NULL,
      operador VARCHAR(50),
      nombreplan VARCHAR(50),
      plan VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE otroEquipo (
      idOtroEquipo INT AUTO_INCREMENT PRIMARY KEY,
      codigo VARCHAR(50) NOT NULL,
      nombre VARCHAR(50),
      ubicacion VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE licencia (
      idLicencia INT AUTO_INCREMENT PRIMARY KEY,
      software VARCHAR(50) NOT NULL,
      licenciaAsignada VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE correos (
      idCorreo INT AUTO_INCREMENT PRIMARY KEY,
      idUsuario INT,
      correo VARCHAR(100) NOT NULL,
      contraseña VARCHAR(255),
      empresa VARCHAR(50),
      equipo VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE servidores (
      idServidor INT AUTO_INCREMENT PRIMARY KEY,
      software VARCHAR(50) NOT NULL,
      usuario VARCHAR(50),
      contraseña VARCHAR(255),
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  -- Crear tabla de entrega con foreign keys opcionales
  CREATE TABLE entrega (
      idEntrega INT AUTO_INCREMENT PRIMARY KEY,
      idEmpleado INT NOT NULL,
      idLaptop INT,
      idCelular INT,
      idLinea INT,
      idOtroEquipo INT,
      fecha DATETIME NOT NULL,
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idEmpleado) REFERENCES usuario(idUsuario),
      FOREIGN KEY (idLaptop) REFERENCES laptops(idLaptop),
      FOREIGN KEY (idCelular) REFERENCES celulares(idCelular),
      FOREIGN KEY (idLinea) REFERENCES lineas(idLinea),
      FOREIGN KEY (idOtroEquipo) REFERENCES otroEquipo(idOtroEquipo),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  -- Crear tabla de devolucion con foreign keys opcionales
  CREATE TABLE devolucion (
      idDevolucion INT AUTO_INCREMENT PRIMARY KEY,
      idEmpleado INT NOT NULL,
      idLaptop INT,
      idCelular INT,
      idLinea INT,
      idOtroEquipo INT,
      fecha DATETIME NOT NULL,
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idEmpleado) REFERENCES usuario(idUsuario),
      FOREIGN KEY (idLaptop) REFERENCES laptops(idLaptop),
      FOREIGN KEY (idCelular) REFERENCES celulares(idCelular),
      FOREIGN KEY (idLinea) REFERENCES lineas(idLinea),
      FOREIGN KEY (idOtroEquipo) REFERENCES otroEquipo(idOtroEquipo),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE impresora (
      idImpresora INT AUTO_INCREMENT PRIMARY KEY,
      marca VARCHAR(50) NOT NULL,
      modelo VARCHAR(50),
      nombre VARCHAR(50),
      ubicacion VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE toner (
      idConsumible INT AUTO_INCREMENT PRIMARY KEY,
      marca VARCHAR(50) NOT NULL,
      modelo VARCHAR(50),
      cantidad INT NOT NULL,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE impresoratoner (
      idImpresoraToner INT AUTO_INCREMENT PRIMARY KEY,
      idImpresora INT,
      idToner INT,
      fecha DATETIME NOT NULL,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idImpresora) REFERENCES impresora(idImpresora),
      FOREIGN KEY (idToner) REFERENCES toner(idConsumible),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE empleado (
      idEmpleado INT AUTO_INCREMENT PRIMARY KEY,
      DNI VARCHAR(20) NOT NULL,
      nombre VARCHAR(100),
      cargo VARCHAR(50),
      empresa VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );
=======
  -- Crear tabla de usuarios
  CREATE TABLE usuario (
      idUsuario INT AUTO_INCREMENT PRIMARY KEY,
      login VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      estado ENUM('activo', 'inactivo') NOT NULL
  );

  -- Crear tabla de logs de conexión
  CREATE TABLE logeo (
      idLogeo INT AUTO_INCREMENT PRIMARY KEY,
      idUsuario INT,
      login_time DATETIME NOT NULL,
      logout_time DATETIME,
      FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario)
  );


  -- Crear tablas de equipos
  CREATE TABLE laptops (
      idLaptop INT AUTO_INCREMENT PRIMARY KEY,
      codigoLaptop VARCHAR(50) NOT NULL,
      password VARCHAR(255),
      marcaLaptop VARCHAR(50),
      modeloLaptop VARCHAR(50),
      numeroSerie VARCHAR(50),
      procesador VARCHAR(50),
      RAM VARCHAR(50),
      discoDuro VARCHAR(50),
      gama VARCHAR(50),
      cargador VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE celulares (
      idCelular INT AUTO_INCREMENT PRIMARY KEY,
      codigoCelular VARCHAR(50) NOT NULL,
      password VARCHAR(255),
      marcaCelular VARCHAR(50),
      modeloCelular VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE lineas (
      idLinea INT AUTO_INCREMENT PRIMARY KEY,
      numero VARCHAR(20) NOT NULL,
      operador VARCHAR(50),
      nombreplan VARCHAR(50),
      plan VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE otroEquipo (
      idOtroEquipo INT AUTO_INCREMENT PRIMARY KEY,
      codigo VARCHAR(50) NOT NULL,
      nombre VARCHAR(50),
      ubicacion VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE licencia (
      idLicencia INT AUTO_INCREMENT PRIMARY KEY,
      software VARCHAR(50) NOT NULL,
      licenciaAsignada VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE correos (
      idCorreo INT AUTO_INCREMENT PRIMARY KEY,
      idUsuario INT,
      correo VARCHAR(100) NOT NULL,
      contraseña VARCHAR(255),
      empresa VARCHAR(50),
      equipo VARCHAR(50),
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE servidores (
      idServidor INT AUTO_INCREMENT PRIMARY KEY,
      software VARCHAR(50) NOT NULL,
      usuario VARCHAR(50),
      contraseña VARCHAR(255),
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  -- Crear tabla de entrega con foreign keys opcionales
  CREATE TABLE entrega (
      idEntrega INT AUTO_INCREMENT PRIMARY KEY,
      idEmpleado INT NOT NULL,
      idLaptop INT,
      idCelular INT,
      idLinea INT,
      idOtroEquipo INT,
      fecha DATETIME NOT NULL,
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idEmpleado) REFERENCES usuario(idUsuario),
      FOREIGN KEY (idLaptop) REFERENCES laptops(idLaptop),
      FOREIGN KEY (idCelular) REFERENCES celulares(idCelular),
      FOREIGN KEY (idLinea) REFERENCES lineas(idLinea),
      FOREIGN KEY (idOtroEquipo) REFERENCES otroEquipo(idOtroEquipo),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  -- Crear tabla de devolucion con foreign keys opcionales
  CREATE TABLE devolucion (
      idDevolucion INT AUTO_INCREMENT PRIMARY KEY,
      idEmpleado INT NOT NULL,
      idLaptop INT,
      idCelular INT,
      idLinea INT,
      idOtroEquipo INT,
      fecha DATETIME NOT NULL,
      observacion TEXT,
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idEmpleado) REFERENCES usuario(idUsuario),
      FOREIGN KEY (idLaptop) REFERENCES laptops(idLaptop),
      FOREIGN KEY (idCelular) REFERENCES celulares(idCelular),
      FOREIGN KEY (idLinea) REFERENCES lineas(idLinea),
      FOREIGN KEY (idOtroEquipo) REFERENCES otroEquipo(idOtroEquipo),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE impresora (
      idImpresora INT AUTO_INCREMENT PRIMARY KEY,
      marca VARCHAR(50) NOT NULL,
      modelo VARCHAR(50),
      nombre VARCHAR(50),
      ubicacion VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE toner (
      idConsumible INT AUTO_INCREMENT PRIMARY KEY,
      marca VARCHAR(50) NOT NULL,
      modelo VARCHAR(50),
      cantidad INT NOT NULL,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE impresoratoner (
      idImpresoraToner INT AUTO_INCREMENT PRIMARY KEY,
      idImpresora INT,
      idToner INT,
      fecha DATETIME NOT NULL,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (idImpresora) REFERENCES impresora(idImpresora),
      FOREIGN KEY (idToner) REFERENCES toner(idConsumible),
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );

  CREATE TABLE empleado (
      idEmpleado INT AUTO_INCREMENT PRIMARY KEY,
      DNI VARCHAR(20) NOT NULL,
      nombre VARCHAR(100),
      cargo VARCHAR(50),
      empresa VARCHAR(50),
      observacion TEXT,
      estado ENUM('activo', 'inactivo'),
      created_by INT,
      updated_by INT,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES usuario(idUsuario),
      FOREIGN KEY (updated_by) REFERENCES usuario(idUsuario)
  );
>>>>>>> 4d1b9e3 (Commit inicial)
