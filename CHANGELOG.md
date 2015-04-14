# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [0.9] - 2015-04-14
### Added
 - Controllers as actions
 - New internal flow
 - Now you can extend {{ qv }} functionality
 - Load external modules (see QV-Module documentation)

### Changed
 - Auto-create language strings
 - Bootstrap 3.3.4


## [0.8.12] - 2015-04-10
### Added
 - New routing system
 - Ajax: new flow

### Fixed
 - LangString


## [0.8.11] - 2015-04-09
### Added
 - Support to extend routing (only one module)

### Fixed
 - Demo controllers


## [0.8.10] - 2015-04-06
### Changed
 - New method getUrlPart
 - Refactor

### Fixed
 - Demo controllers

## [0.8.7] - 2015-01-22
### Changed
 - Added CleanString method to Helper
 - Refactor some methods

### Fixed
 - Fix CLI variables
 - Router: getCurrentURL now works correctly
 - Controller: Fix demo register


## [0.8.6a] - 2015-01-13
### Fixed
 - Fix CLI path and variables


## [0.8.6] - 2015-01-09
### Changed
 - New mail model
 - Change public $table to protected (all models)
 - Many empty changes to isset function for best performance
 - Change globals uses
 - HTML demo updated
 - Delete user twigVars (deprecated), now use "_user" to access full object
 - CLI updated to new structure (0.8)

### Fixed
 - Issue when load globals objects


## [0.8.5] - 2014-12-31
### Added
 - Some test with PHPUnit

### Changed
 - Rename Core to Bootstrap and other minor changes


## [0.8.4] - 2014-12-28
### Added
 - New custom filter to translate strings: usq {{ 'string'|t }}
 - Updated homepage

### Changed
 - Refactoring globals to $GLOBALS
 - Bootstrap 3.3.1

### Fixed
 - Autoloader bugs
 - Routing system now works correctly


## [0.8.2/3] - 2014-12-27
### Changed
 - New URL array to manage path and uri
 - Updated QV array (to use in Twig)


## [0.8.1] - 2014-12-18
### Added
 - A little Model test with PHPUnit
 
### Changed
 - View: Home page (demo)
 - README


## [0.8] - 2014-12-18
### Added
 - Exception model
 - Helper model

### Changed
 - New structure: Quaver/App now contains Models, Controller and Theme
 - Base model rename to Model
 - Added exceptions
 - Now PHP min version is 5.3

### Fixed
 - User methods
 - languages/users controller


## [0.7] - 2014-12-17
### Changed
 - Works fine with composer and all dependencies autoload
 - Delete old libs referencies

## [0.6.1] - 2014-12-17
### Added
 - Added some missing readme and changelog info
 - Added Travis to repo

### Changed
 - Moved branch 0.6.X to master
 - Base model now abstract class
 - Updated composer.json
 - Internal changes and clean on Core and Router model (version var)


# Pre CHANGELOG (legacy)

* Version 0.6 (November-December 2014)
    * New internal core flow and refactor lines of code
    * CLI beta
* Version 0.5 (October 2014)
    * Dashboard
* Version 0.4 (September 2014)
    * Redesign with namespaces and new structure
* Version 0.3 (September 2014)
    * Set new core and new internal flow.
    * New functions and extended models.
* Version 0.2 (Summer 2014)
    * Set new functions.
* Version 0.1 (Summer 2014)
    * First version.
    * Core level 1.
    * Multilanguage supported.
