-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-01-2026 a las 09:24:46
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `guniversidad`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `afectacionasignaturas`
--

CREATE TABLE `afectacionasignaturas` (
  `idAfectacionasignatura` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idSemestre` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idAsignatura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignatura`
--

CREATE TABLE `asignatura` (
  `idAsignatura` int(11) NOT NULL,
  `NombreAsignatura` varchar(50) NOT NULL,
  `Creditos` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idSemestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `idAula` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `becario`
--

CREATE TABLE `becario` (
  `idBecario` int(11) NOT NULL,
  `institucionBeca` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

CREATE TABLE `carrera` (
  `idCarrera` int(11) NOT NULL,
  `NombreCarrera` varchar(50) NOT NULL,
  `idDepartamento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `idClase` int(11) NOT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `idAsignatura` int(11) DEFAULT NULL,
  `diaSemanal` varchar(20) DEFAULT NULL,
  `idAula` int(11) DEFAULT NULL,
  `idHorario` int(11) DEFAULT NULL,
  `horaInicio` time DEFAULT NULL,
  `HoraFinal` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `idConsulta` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `contenido` varchar(255) DEFAULT NULL,
  `idEmisor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `idContacto` int(11) NOT NULL,
  `contacto` varchar(50) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `idStatic` int(11) DEFAULT NULL,
  `idDepartamento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `idCurso` int(11) NOT NULL,
  `NombreCurso` varchar(50) NOT NULL,
  `CreditosCurso` int(11) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idSemestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_planestudio`
--

CREATE TABLE `curso_planestudio` (
  `idPlanEstudio` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idSemestre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `iddepartamento` int(11) NOT NULL,
  `NombreDepartamento` varchar(50) NOT NULL,
  `TelefonoDepartamento` varchar(50) NOT NULL,
  `idFacultad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatarioconsulta`
--

CREATE TABLE `destinatarioconsulta` (
  `idDestinatario` int(11) NOT NULL,
  `idConsulta` int(11) DEFAULT NULL,
  `idUsuarioDestinatario` int(11) DEFAULT NULL,
  `rolDestinado` varchar(50) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `idDocumento` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `idGuia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `idEstudiante` int(11) NOT NULL,
  `CodigoEstudiante` varchar(50) NOT NULL,
  `NombreEstudiante` varchar(50) NOT NULL,
  `ApellidosEstudiante` varchar(50) NOT NULL,
  `dipEstudiante` int(11) NOT NULL,
  `CorreoEstudiante` varchar(50) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `FechadeNacimiento` date NOT NULL,
  `Sexo` varchar(50) NOT NULL,
  `Nacionalidad` varchar(50) NOT NULL,
  `Foto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante_asignatura`
--

CREATE TABLE `estudiante_asignatura` (
  `idEstudianteConvocatoria` int(11) NOT NULL,
  `codigo` int(11) DEFAULT NULL,
  `idAsignatura` int(11) DEFAULT NULL,
  `convocatoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante_beca`
--

CREATE TABLE `estudiante_beca` (
  `idEstudianteBecario` int(11) NOT NULL,
  `Codigo` int(11) DEFAULT NULL,
  `idBecario` int(11) DEFAULT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFinal` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluacion`
--

CREATE TABLE `evaluacion` (
  `idEvaluacion` int(11) NOT NULL,
  `idExamen` int(11) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `nota` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examen`
--

CREATE TABLE `examen` (
  `idExamen` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idAsignatura` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `EstadoExamen` varchar(50) NOT NULL,
  `NotaExamen` float NOT NULL,
  `NumeroExamen` int(11) NOT NULL,
  `FechaExamen` date NOT NULL,
  `HoraExamen` time(6) NOT NULL,
  `idSemestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultad`
--

CREATE TABLE `facultad` (
  `idfacultad` int(11) NOT NULL,
  `NombreFacultad` varchar(50) NOT NULL,
  `TelefonoFacultad` int(11) NOT NULL,
  `DireccionFacultad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familiar`
--

CREATE TABLE `familiar` (
  `idFamilia` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `NombreTutor` varchar(50) NOT NULL,
  `ResponsabledePago` varchar(50) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Correo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formacion`
--

CREATE TABLE `formacion` (
  `idFormacion` int(11) NOT NULL,
  `institucion` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto`
--

CREATE TABLE `foto` (
  `idFoto` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `idNoticia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guiasdidacticas`
--

CREATE TABLE `guiasdidacticas` (
  `idGuia` int(11) NOT NULL,
  `idAsignatura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `idHorario` int(11) NOT NULL,
  `NumeroAula` int(11) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `dia` varchar(50) NOT NULL,
  `idAsignatura` int(11) NOT NULL,
  `HoraInicio` time(6) NOT NULL,
  `HoraFinal` time(6) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `idSemestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informes`
--

CREATE TABLE `informes` (
  `idInforme` int(11) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `contenido` varchar(255) NOT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `idMatricula` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `FechaMatricula` int(11) NOT NULL,
  `AñoAcademico` varchar(50) NOT NULL,
  `EstadoMatricula` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia`
--

CREATE TABLE `noticia` (
  `idNoticia` int(11) NOT NULL,
  `asunto` varchar(200) DEFAULT NULL,
  `descripción` varchar(255) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `monto` int(11) NOT NULL,
  `idMatricula` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planestudio`
--

CREATE TABLE `planestudio` (
  `idPlanEstudio` int(11) NOT NULL,
  `periodoPlanEstudio` varchar(20) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `idCarrera` int(11) DEFAULT NULL,
  `fechaElaboracion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `idProfesor` int(11) NOT NULL,
  `NombreProfesor` varchar(50) NOT NULL,
  `ApellidosProfesor` varchar(50) NOT NULL,
  `dipProfesor` int(11) NOT NULL,
  `Especialidad` varchar(50) NOT NULL,
  `GradoEstudio` varchar(50) NOT NULL,
  `Telefono` varchar(50) NOT NULL,
  `CorreoProfesor` varchar(50) NOT NULL,
  `idFacultad` int(11) NOT NULL,
  `idDepartamento` int(11) NOT NULL,
  `Foto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semestre`
--

CREATE TABLE `semestre` (
  `idSemestre` int(11) NOT NULL,
  `NombreSemestre` varchar(50) NOT NULL,
  `idAsignatura` int(11) NOT NULL,
  `idCarrera` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `static`
--

CREATE TABLE `static` (
  `idStatic` int(11) NOT NULL,
  `inicio` varchar(255) DEFAULT NULL,
  `sobreNosotros` varchar(255) DEFAULT NULL,
  `urlLogo` varchar(255) DEFAULT NULL,
  `quienesSomos` varchar(255) DEFAULT NULL,
  `cantidadRegistros` int(11) NOT NULL COMMENT 'Numero de registros que contiene cada pagina en el fronEnd'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titulos`
--

CREATE TABLE `titulos` (
  `idTitulo` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `idFormacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `NombreUsuario` varchar(50) NOT NULL,
  `Contraseña` varchar(50) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `Rol` varchar(50) NOT NULL,
  `Foto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `afectacionasignaturas`
--
ALTER TABLE `afectacionasignaturas`
  ADD PRIMARY KEY (`idAfectacionasignatura`),
  ADD KEY `idAsignatura` (`idAsignatura`),
  ADD KEY `idCarrera` (`idCarrera`),
  ADD KEY `idCurso` (`idCurso`),
  ADD KEY `idEstudiante` (`idEstudiante`),
  ADD KEY `idSemestre` (`idSemestre`);

--
-- Indices de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`idAsignatura`),
  ADD KEY `idCarrera` (`idCarrera`),
  ADD KEY `idCurso` (`idCurso`),
  ADD KEY `idProfesor` (`idProfesor`),
  ADD KEY `idSemestre` (`idSemestre`);

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`idAula`);

--
-- Indices de la tabla `becario`
--
ALTER TABLE `becario`
  ADD PRIMARY KEY (`idBecario`);

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`idCarrera`),
  ADD KEY `idDepartamento` (`idDepartamento`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`idClase`),
  ADD KEY `idProfesor` (`idProfesor`),
  ADD KEY `idAsignatura` (`idAsignatura`),
  ADD KEY `idAula` (`idAula`),
  ADD KEY `idHorario` (`idHorario`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`idConsulta`),
  ADD KEY `cfConsultaUsuario` (`idEmisor`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`idContacto`),
  ADD KEY `cfContactosStatic` (`idStatic`),
  ADD KEY `idDepartamento` (`idDepartamento`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`idCurso`),
  ADD KEY `idCarrera` (`idCarrera`),
  ADD KEY `idSemestre` (`idSemestre`);

--
-- Indices de la tabla `curso_planestudio`
--
ALTER TABLE `curso_planestudio`
  ADD PRIMARY KEY (`idPlanEstudio`,`idCurso`),
  ADD KEY `cfCursoPlanEstudioSemestre` (`idSemestre`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`iddepartamento`),
  ADD KEY `idFacultad` (`idFacultad`);

--
-- Indices de la tabla `destinatarioconsulta`
--
ALTER TABLE `destinatarioconsulta`
  ADD PRIMARY KEY (`idDestinatario`),
  ADD KEY `cfDestinatarioConsultaConsulta` (`idConsulta`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`idDocumento`),
  ADD KEY `idGuia` (`idGuia`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`idEstudiante`),
  ADD KEY `idCarrera` (`idCarrera`);

--
-- Indices de la tabla `estudiante_asignatura`
--
ALTER TABLE `estudiante_asignatura`
  ADD PRIMARY KEY (`idEstudianteConvocatoria`),
  ADD KEY `cfestudianteasignaturaEstudiante` (`codigo`),
  ADD KEY `cfestudianteasignaturaAsignatura` (`idAsignatura`);

--
-- Indices de la tabla `estudiante_beca`
--
ALTER TABLE `estudiante_beca`
  ADD PRIMARY KEY (`idEstudianteBecario`),
  ADD KEY `cfestudianteBecaEstudiante` (`Codigo`),
  ADD KEY `cfestudianteBecaBeca` (`idBecario`);

--
-- Indices de la tabla `evaluacion`
--
ALTER TABLE `evaluacion`
  ADD PRIMARY KEY (`idEvaluacion`),
  ADD KEY `cfEvaluacionExamen` (`idExamen`);

--
-- Indices de la tabla `examen`
--
ALTER TABLE `examen`
  ADD PRIMARY KEY (`idExamen`);

--
-- Indices de la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD PRIMARY KEY (`idfacultad`);

--
-- Indices de la tabla `familiar`
--
ALTER TABLE `familiar`
  ADD PRIMARY KEY (`idFamilia`);

--
-- Indices de la tabla `formacion`
--
ALTER TABLE `formacion`
  ADD PRIMARY KEY (`idFormacion`),
  ADD KEY `cfFormacionProfesor` (`idProfesor`);

--
-- Indices de la tabla `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`idFoto`),
  ADD KEY `idNoticia` (`idNoticia`);

--
-- Indices de la tabla `guiasdidacticas`
--
ALTER TABLE `guiasdidacticas`
  ADD PRIMARY KEY (`idGuia`),
  ADD KEY `idAsignatura` (`idAsignatura`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`idHorario`);

--
-- Indices de la tabla `informes`
--
ALTER TABLE `informes`
  ADD PRIMARY KEY (`idInforme`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`idMatricula`),
  ADD KEY `idEstudiante` (`idEstudiante`),
  ADD KEY `idCurso` (`idCurso`);

--
-- Indices de la tabla `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`idNoticia`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idPago`);

--
-- Indices de la tabla `planestudio`
--
ALTER TABLE `planestudio`
  ADD PRIMARY KEY (`idPlanEstudio`),
  ADD UNIQUE KEY `periodoPlanEstudio` (`periodoPlanEstudio`),
  ADD KEY `cfPlanEstudioCarrera` (`idCarrera`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`idProfesor`);

--
-- Indices de la tabla `semestre`
--
ALTER TABLE `semestre`
  ADD PRIMARY KEY (`idSemestre`);

--
-- Indices de la tabla `static`
--
ALTER TABLE `static`
  ADD PRIMARY KEY (`idStatic`);

--
-- Indices de la tabla `titulos`
--
ALTER TABLE `titulos`
  ADD PRIMARY KEY (`idTitulo`),
  ADD KEY `cfTituloFormacion` (`idFormacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `afectacionasignaturas`
--
ALTER TABLE `afectacionasignaturas`
  MODIFY `idAfectacionasignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  MODIFY `idAsignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `idCarrera` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `idCurso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `iddepartamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `examen`
--
ALTER TABLE `examen`
  MODIFY `idExamen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facultad`
--
ALTER TABLE `facultad`
  MODIFY `idfacultad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familiar`
--
ALTER TABLE `familiar`
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `idHorario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informes`
--
ALTER TABLE `informes`
  MODIFY `idInforme` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `idMatricula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `idProfesor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `semestre`
--
ALTER TABLE `semestre`
  MODIFY `idSemestre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `afectacionasignaturas`
--
ALTER TABLE `afectacionasignaturas`
  ADD CONSTRAINT `afectacionasignaturas_ibfk_1` FOREIGN KEY (`idAfectacionasignatura`) REFERENCES `asignatura` (`idAsignatura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `afectacionasignaturas_ibfk_2` FOREIGN KEY (`idAsignatura`) REFERENCES `asignatura` (`idAsignatura`),
  ADD CONSTRAINT `afectacionasignaturas_ibfk_3` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`),
  ADD CONSTRAINT `afectacionasignaturas_ibfk_4` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`idCurso`),
  ADD CONSTRAINT `afectacionasignaturas_ibfk_5` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiante` (`idEstudiante`),
  ADD CONSTRAINT `afectacionasignaturas_ibfk_6` FOREIGN KEY (`idSemestre`) REFERENCES `semestre` (`idSemestre`);

--
-- Filtros para la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD CONSTRAINT `asignatura_ibfk_1` FOREIGN KEY (`idAsignatura`) REFERENCES `afectacionasignaturas` (`idAfectacionasignatura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asignatura_ibfk_2` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`),
  ADD CONSTRAINT `asignatura_ibfk_3` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`idCurso`),
  ADD CONSTRAINT `asignatura_ibfk_4` FOREIGN KEY (`idProfesor`) REFERENCES `profesor` (`idProfesor`),
  ADD CONSTRAINT `asignatura_ibfk_5` FOREIGN KEY (`idSemestre`) REFERENCES `semestre` (`idSemestre`);

--
-- Filtros para la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD CONSTRAINT `carrera_ibfk_1` FOREIGN KEY (`idDepartamento`) REFERENCES `departamento` (`iddepartamento`);

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`idProfesor`) REFERENCES `profesor` (`idProfesor`),
  ADD CONSTRAINT `clase_ibfk_2` FOREIGN KEY (`idAsignatura`) REFERENCES `asignatura` (`idAsignatura`),
  ADD CONSTRAINT `clase_ibfk_3` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`),
  ADD CONSTRAINT `clase_ibfk_4` FOREIGN KEY (`idHorario`) REFERENCES `horario` (`idHorario`);

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `cfConsultaUsuario` FOREIGN KEY (`idEmisor`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `cfContactosStatic` FOREIGN KEY (`idStatic`) REFERENCES `static` (`idStatic`),
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`idDepartamento`) REFERENCES `departamento` (`iddepartamento`);

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`idCurso`) REFERENCES `carrera` (`idCarrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`),
  ADD CONSTRAINT `curso_ibfk_3` FOREIGN KEY (`idSemestre`) REFERENCES `semestre` (`idSemestre`);

--
-- Filtros para la tabla `curso_planestudio`
--
ALTER TABLE `curso_planestudio`
  ADD CONSTRAINT `cfCursoPlanEstudioSemestre` FOREIGN KEY (`idSemestre`) REFERENCES `semestre` (`idSemestre`);

--
-- Filtros para la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD CONSTRAINT `departamento_ibfk_1` FOREIGN KEY (`iddepartamento`) REFERENCES `facultad` (`idfacultad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `departamento_ibfk_2` FOREIGN KEY (`idFacultad`) REFERENCES `facultad` (`idfacultad`);

--
-- Filtros para la tabla `destinatarioconsulta`
--
ALTER TABLE `destinatarioconsulta`
  ADD CONSTRAINT `cfDestinatarioConsultaConsulta` FOREIGN KEY (`idConsulta`) REFERENCES `consultas` (`idConsulta`);

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`idGuia`) REFERENCES `guiasdidacticas` (`idGuia`);

--
-- Filtros para la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `estudiante_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `matricula` (`idMatricula`) ON DELETE CASCADE,
  ADD CONSTRAINT `estudiante_ibfk_2` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`);

--
-- Filtros para la tabla `estudiante_asignatura`
--
ALTER TABLE `estudiante_asignatura`
  ADD CONSTRAINT `cfestudianteasignaturaAsignatura` FOREIGN KEY (`idAsignatura`) REFERENCES `asignatura` (`idAsignatura`),
  ADD CONSTRAINT `cfestudianteasignaturaEstudiante` FOREIGN KEY (`codigo`) REFERENCES `estudiante` (`idEstudiante`);

--
-- Filtros para la tabla `estudiante_beca`
--
ALTER TABLE `estudiante_beca`
  ADD CONSTRAINT `cfestudianteBecaBeca` FOREIGN KEY (`idBecario`) REFERENCES `becario` (`idBecario`),
  ADD CONSTRAINT `cfestudianteBecaEstudiante` FOREIGN KEY (`Codigo`) REFERENCES `estudiante` (`idEstudiante`);

--
-- Filtros para la tabla `evaluacion`
--
ALTER TABLE `evaluacion`
  ADD CONSTRAINT `cfEvaluacionExamen` FOREIGN KEY (`idExamen`) REFERENCES `examen` (`idExamen`);

--
-- Filtros para la tabla `examen`
--
ALTER TABLE `examen`
  ADD CONSTRAINT `examen_ibfk_1` FOREIGN KEY (`idExamen`) REFERENCES `profesor` (`idProfesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `familiar`
--
ALTER TABLE `familiar`
  ADD CONSTRAINT `familiar_ibfk_1` FOREIGN KEY (`idFamilia`) REFERENCES `estudiante` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `formacion`
--
ALTER TABLE `formacion`
  ADD CONSTRAINT `cfFormacionProfesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesor` (`idProfesor`);

--
-- Filtros para la tabla `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `foto_ibfk_1` FOREIGN KEY (`idNoticia`) REFERENCES `noticia` (`idNoticia`);

--
-- Filtros para la tabla `guiasdidacticas`
--
ALTER TABLE `guiasdidacticas`
  ADD CONSTRAINT `guiasdidacticas_ibfk_1` FOREIGN KEY (`idAsignatura`) REFERENCES `asignatura` (`idAsignatura`);

--
-- Filtros para la tabla `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `horario_ibfk_1` FOREIGN KEY (`idHorario`) REFERENCES `carrera` (`idCarrera`) ON DELETE CASCADE;

--
-- Filtros para la tabla `informes`
--
ALTER TABLE `informes`
  ADD CONSTRAINT `informes_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`idMatricula`) REFERENCES `facultad` (`idfacultad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiante` (`idEstudiante`),
  ADD CONSTRAINT `matricula_ibfk_3` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`idCurso`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`idPago`) REFERENCES `matricula` (`idMatricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `planestudio`
--
ALTER TABLE `planestudio`
  ADD CONSTRAINT `cfPlanEstudioCarrera` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`);

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`idProfesor`) REFERENCES `departamento` (`iddepartamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `titulos`
--
ALTER TABLE `titulos`
  ADD CONSTRAINT `cfTituloFormacion` FOREIGN KEY (`idFormacion`) REFERENCES `formacion` (`idFormacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
