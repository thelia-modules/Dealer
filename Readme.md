# Dealer 2.4.2

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thelia-modules/Dealer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thelia-modules/Dealer/?branch=master)

author : Penalver Antony <apenalver@openstudio.fr>

This module Thelia generate CRUD interface for dealer.

## Compatibility

Thelia >= 2.1.3
Developped for : Thelia 2.2.1

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Dealer.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/dealer-module:~2.0
```

## Usage

You can find main Dealer interface on modules admin menu.

## Loop

### DealerLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**city**           | filter by city                                            |
|**country_id**     | filter by country                                         |
|**order**          | order result by "id","id-reverse","date","date-reverse"	|


### Output arguments

|Variable       |Description                |
|---            |---                        |
|$ID            | id                        |
|$TITLE      	| Name 			            |
|$ADDRESS1      | First element address     |
|$ADDRESS2      | Second element address    |
|$ADDRESS3      | Third element address  	|
|$ZIPCODE       | Address zip code          |
|$CITY          | City name                 |
|$COUNTRY_ID    | Country id-reverse        |
|$DESCRIPTION   | Dealer description        |
|$LAT   		| Latitude                  |
|$LON 		 	| Longitude                 |
|$CREATE_DATE	| Creation date             |
|$UPDATE_DATE	| Last update date          |

### ContactLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**dealer_id**      | filter by dealer                                          |
|**order**          | order result by "id","id-reverse","label","label-reverse"	|


### Output arguments

|Variable       |Description                |
|---            |---                        |
|$ID            | id                        |
|$DEALER_ID    	| Associated Dealer id      |
|$IS_DEFAULT    | Boolean					|
|$LABEL     	| Contact group name 	    |

### ContactInfoLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**contact_id**     | filter by contact                                         |
|**order**          | order result by "id","id-reverse","value","value-reverse"	|


### Output arguments

|Variable       	|Description                |
|---            	|---                        |
|$ID            	| id                        |
|$CONTACT_ID    	| Associated Contact id     |
|$CONTACT_TYPE   	| Contact type 				|
|$CONTACT_TYPE_ID   | Contact type id 			|
|$VALUE     		| Contact value 	 	    |

### SchedulesLoop

### Input arguments

|Argument           |Description                                                                                                                |
|---                |---                                                                                                                        |
|**id**             | filter by id                                                                                                              |
|**dealer_id**      | filter by dealer                                                                                                          |
|**default_period** | boolean filter by default schedule                                                                                                |
|**hide_past**      | boolean for hide past schedules (default: false)                                                                          |
|**closed**         | boolean for closed or open schedule (default: false)                                                                      |
|**day** 			| filter by day 		                                                                                                    |
|**order**          | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse","period-begin","period-begin-reverse"		|


### Output arguments

|Variable       	|Description                |
|---            	|---                        |
|$ID            	| id                        |
|$DEALER_ID    		| Associated Dealer id      |
|$DAY    			| Day value     			|
|$DAY_LABEL   		| Day label					|
|$BEGIN 			| Schedules start 			|
|$END    			| Schedules end 	 	    |
|$PERIOD_BEGIN 		| Schedules period start	|
|$PERIOD_END    	| Schedules period end		|

### RegularSchedulesLoop

### Input arguments

|Argument               |Description                                                                            |
|---                    |---                                                                                    |
|**id**                 | filter by id                                                                          |
|**dealer_id**          | filter by dealer                                                                      |
|**day** 			    | filter by day 		                                                                |
|**hour_separator**     | separator between hours for ouput formatted_hours (default: ' - ') 		            |
|**half_day_separator** | separator between half day for ouput formatted_hours (default: ' / ') 	            |
|**merge_day**          | boolean to allow concatenated hours of schedule with the same day (default: true)     |
|**order**              | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse"         |

### Output arguments

|Variable       	|Description                                                                        |
|---            	|---                                                                                |
|$ID            	| id                                                                                |
|$DEALER_ID    		| Associated Dealer id                                                              |
|$DAY    			| Day value     			                                                        |
|$DAY_LABEL   		| Day label					                                                        |
|$BEGIN 			| Schedules start 			                                                        |
|$END    			| Schedules end (end of the afternoon when merge_day input argument is true	 	    |
|$FORMATTED_HOURS   | Formatted hours when merge_day input argument is true 	 	                    |

### ExtraSchedulesLoop

### Input arguments

|Argument               |Description                                                                                                                |
|---                    |---                                                                                                                        |
|**id**                 | filter by id                                                                                                              |
|**dealer_id**          | filter by dealer                                                                                                          |
|**day** 			    | filter by day 		                                                                                                    |
|**hour_separator**     | separator between hours for ouput formatted_hours (default:  ' - ') 		                                                |
|**half_day_separator** | separator between half day for ouput formatted_hours (default:  ' / ') 	                                                |
|**merge_day**          | boolean to allow concatenated hours of schedule with the same periods (default = true)                                    |
|**hide_past**          | boolean for hide past schedules (default: false)                                                                          |
|**closed**             | boolean for closed or open schedule (default: false)                                                                      |
|**order**              | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse","period-begin","period-begin-reverse"       |
