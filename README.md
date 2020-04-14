# melis-cms-page-historic

MelisCmsPageHistoric provides an historic system for MelisCms' page edition.

## Getting Started

These instructions will get you a copy of the project up and running on your machine.  
This Melis Platform module is made to work with the MelisCms.

### Prerequisites

You will need to install melisplatform/melis-cms in order to have this module running.  
This will automatically be done when using composer.

### Installing

Run the composer command:
```
composer require melisplatform/melis-cms-page-historic
```

### Database    

Database model is accessible on the MySQL Workbench file:  
/melis-cms-page-historic/install/sql/model  
Database will be installed through composer and its hooks.  
In case of problems, SQL files are located here:  
/melis-cms-page-historic/install/sql  

## Tools & Elements provided

* Historic Page Edition Tab  

## Running the code

### Listening to services and update behavior with custom code  
Most services trigger events so that the behavior can be modified.  
```  
public function attach(EventManagerInterface $events)
{
	$sharedEvents = $events->getSharedManager();
    
	$callBackHandler = $sharedEvents->attach(
		'MelisCms', 
		'meliscms_page_delete_end', 
		function($e) {

			$sm = $e->getTarget()->getServiceManager();
        	$params = $e->getParams();

        	// Custom Code
        },
    50);
    
    $this->listeners[] = $callBackHandler;
}
```  

## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-cms-page-historic/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details