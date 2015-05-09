-- Server version: 5.5.34
-- PHP: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database `quaver`
--

-- --------------------------------------------------------

--
-- Table `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `large` varchar(100) NOT NULL DEFAULT '',
  `slug` varchar(3) NOT NULL DEFAULT '',
  `locale` varchar(5) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL,
  `priority` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Insert `lang`
--

INSERT INTO `lang` (`id`, `name`, `large`, `slug`, `locale`, `active`, `priority`) VALUES
(1, 'ENG', 'English', 'eng', 'en_US', 1, 2),
(2, 'ESP', 'Spanish', 'esp', 'es_ES', 1, 1);

-- --------------------------------------------------------

--
-- Table `lang_strings`
--

DROP TABLE IF EXISTS `lang_strings`;
CREATE TABLE `lang_strings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language` int(11) NOT NULL,
  `label` varchar(64) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Table `lang_strings`
--

INSERT INTO `lang_strings` (`id`, `language`, `label`, `text`) VALUES
(1, 1, 'hello_world', 'Hello world'),
(2, 2, 'hello_world', 'Hola mundo'),
(3, 1, 'back', 'Back'),
(4, 1, 'logout', 'Logout'),
(5, 1, 'sidebar-home', 'Home'),
(6, 1, 'sidebar-users', 'Users'),
(7, 1, 'sidebar-languages', 'Languages'),
(8, 1, 'sidebar-lang', 'Lang'),
(9, 1, 'title-404', 'Error 404'),
(10, 1, 'hi', 'Hi'),
(11, 1, 'welcome', 'Welcome to Quaver'),
(12, 1, 'local-lang', 'Local Lang'),
(13, 1, 'save', 'Save'),
(14, 1, 'language', 'Language'),
(16, 1, 'modal-close', 'Close'),
(17, 1, 'modal-delete-title', 'Delete'),
(18, 1, 'modal-delete-question', 'Do you want continue?'),
(19, 1, 'modal-cancel', 'Cancel'),
(20, 1, 'modal-delete-button', 'Delete'),
(21, 2, 'back', 'Volver'),
(22, 2, 'logout', 'Cerrar sesión'),
(23, 2, 'sidebar-home', 'Inicio'),
(24, 2, 'sidebar-users', 'Usuarios'),
(25, 2, 'sidebar-languages', 'Textos'),
(26, 2, 'sidebar-lang', 'Idiomas'),
(27, 2, 'modal-close', 'Cerrar'),
(28, 2, 'modal-delete-title', 'Eliminar'),
(29, 2, 'modal-delete-question', '¿Seguro que quieres eliminar?'),
(30, 2, 'modal-cancel', 'Cancelar'),
(31, 2, 'modal-delete-button', 'Eliminar'),
(32, 2, 'hi', 'Hola'),
(33, 2, 'welcome', 'Bienvenido a Quaver'),
(34, 2, 'local-lang', 'Lenguaje local'),
(35, 2, 'sample-string', 'Ejemplo de traducción'),
(36, 2, 'important', 'Importante'),
(37, 2, 'import', 'Importa'),
(38, 2, 'to-your-database', 'en tu base de datos'),
(39, 2, 'check', 'Chequea'),
(40, 2, 'and', 'y'),
(41, 1, 'sample-string', 'Sample string'),
(42, 1, 'important', 'Important'),
(43, 1, 'import', 'Import'),
(44, 1, 'to-your-database', 'to your database'),
(45, 1, 'check', 'Check'),
(46, 1, 'and', 'and'),
(47, 1, 'register', 'Register'),
(48, 1, 'login', 'Login'),
(49, 1, 'create-a-user', 'Create a user'),
(50, 1, 'and-select-admin-type', 'and select admin type'),
(51, 1, 'and-go-to', 'and go to'),
(52, 2, 'register', 'Registrate'),
(53, 2, 'login', 'Iniciar sesión'),
(54, 2, 'create-a-user', 'Crea un usuario'),
(55, 2, 'and-select-admin-type', 'y selecciona tipo administrador'),
(58, 2, 'new-user', 'Nuevo usuario'),
(57, 2, 'and-go-to', 'y ve a'),
(59, 2, 'admin-type', 'Administrador'),
(60, 1, 'new-user', 'New user'),
(61, 1, 'admin-type', 'Admin'),
(62, 2, 'error-login', 'Error de login'),
(63, 2, 'user-disabled', 'Usuario deshabilitado'),
(64, 1, 'user-disabled', 'User disabled'),
(65, 1, 'error-login', 'Error login'),
(66, 2, 'users', 'Usuarios'),
(67, 2, 'new', 'Nuevo'),
(68, 2, 'active', 'Activo'),
(69, 2, 'level', 'Nivel'),
(70, 2, 'last-login', 'Última sesión'),
(71, 2, 'options', 'Options'),
(72, 2, 'enabled', 'Habilitado'),
(83, 1, 'disabled', 'Disabled'),
(82, 1, 'enabled', 'Enabled'),
(81, 2, 'language', 'Idioma'),
(76, 2, 'save', 'Guardar'),
(77, 2, 'user', 'Usuario'),
(80, 1, 'new', 'New'),
(79, 2, 'disabled', 'Deshabilitado'),
(84, 1, 'users', 'Users'),
(86, 1, 'active', 'Active'),
(87, 1, 'level', 'Level'),
(88, 1, 'last-login', 'Last login'),
(89, 1, 'options', 'Options'),
(90, 1, 'user', 'User'),
(92, 2, 'title-404', 'Error 404'),
(93, 1, 'plugins', 'Plugins'),
(94, 2, 'plugins', 'Extensiones');

-- --------------------------------------------------------

--
-- Table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `level` char(5) NOT NULL DEFAULT 'user',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dateRegister` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dateLastLogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
`id` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL,
  `action` varchar(64) NOT NULL,
  `model` varchar(64) DEFAULT NULL,
  `model_id` bigint(20) DEFAULT NULL,
  `model_title` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
