-- Versión del servidor: 5.5.34
-- Versión de PHP: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `quaver`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `slug` varchar(3) NOT NULL DEFAULT '',
  `locale` varchar(5) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `lang`
--

INSERT INTO `lang` (`id`, `name`, `slug`, `locale`, `active`, `priority`) VALUES
(1, 'English', 'eng', 'en_US', 1, 1),
(2, 'Español', 'esp', 'es_ES', 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lang_strings`
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
-- Volcado de datos para la tabla `lang_strings`
--

INSERT INTO `lang_strings` (`id`, `language`, `label`, `text`) VALUES
(1, 1, 'hello_world', 'Hello world'),
(2, 2, 'hello_world', 'Hola mundo');

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
