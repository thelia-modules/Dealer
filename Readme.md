# Dealer

author : Penalver Antony <apenalver@openstudio.fr>

This module Thelia generate CRUD interface for dealer.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Dealer.
* Activate it in your thelia administration panel

### Composer
Add it in your main thelia composer.json file

```
composer require thelia/dealer-module:~1.1
```


## Usage

Use form in Tools->Dealer to access to all features (List|Create|Update|Delete)

## Loop

Dealer

### Input arguments

|Argument           |Description                                                                                    |
|---                |---                                                                                            |
|**id**             | filter by id                                                                                  |
|**company**        | filter by company                                                                             |
|**address1**       | filter by address1                                                                            |
|**address2**       | filter by address2                                                                            |
|**zipcode**        | filter by zipcode                                                                             |
|**city**           | filter by city                                                                                |
|**country**        | filter by country                                                                             |
|**schedule**       | filter by schedule                                                                            |
|**phone_number**   | filter by phone_number                                                                        |
|**web_site**       | filter by website                                                                             |
|**latitude**       | filter by latitude                                                                            |
|**longitude**      | filter by longitude                                                                           |
|**order**          | order result by "id","id-reverse","company","company-reverse","address1","address1-reverse", "address2","address2-reverse","zipcode","zipcode-reverse","city","country","country-reverse","city-reverse","description", "description-reverse","schedule","schedule-reverse","phone_number","phone_number-reverse", "web_site", "web_site-reverse","latitude","latitude-reverse","longitude","longitude-reverse" |


### Output arguments

|Variable       |Description                |
|---            |---                        |
|$ID            | id                        |
|$COMPANY       | Company name              |
|$ADDRESS1      | First element address     |
|$ADDRESS2      | Second element address    |
|$ZIPCODE       | Address zip code          |
|$CITY          | City name                 |
|$COUNTRY       | Country name              |
|$DESCRIPTION   | Dealer description        |
|$SCHEDULE      | Dealer schedule           |
|$PHONE_NUMBER  | Dealer phone number       |
|$WEB_SITE      | Dealer website url        |
|$LATITUDE      | Latitude                  |
|$LONGITUDE     | Longitude                 |


### Exemple
```
{loop name="dealer-tab-list" type="dealer-tab" order=$order}
    <tr>
        <td>
            <a href="{url path='/admin/module/Dealer/dealer_tab/edit' dealer_tab_id=$ID}">{$ID}</a>
        </td>
        <td>
            {$COMPANY}
        </td>
        <td>
            {$ADDRESS1}
        </td>
        <td>
            {$ZIPCODE}
        </td>
        <td>
            {$CITY}
        </td>
        <td>
            {$PHONE_NUMBER}
        </td>
        <td>
            <a href="http://{$WEB_SITE}" target="_blank">{$WEB_SITE}</a>
        </td>
        {* Actions *}
        <td>
            <div class="btn-group">
                {loop name="auth-edit" type="auth" role="ADMIN" resource="admin.module" access="UPDATE" module="Dealer"}
                    <a class="btn btn-default btn-xs" title="{intl l='Edit this DealerTab' d='dealer.bo.default'}"  href="{url path='/admin/module/Dealer/dealer_tab/edit' dealer_tab_id=$ID}">
                        <i class="glyphicon glyphicon-edit"></i>
                    </a>
                {/loop}
                {loop name="auth-delete" type="auth" role="ADMIN" resource="admin.module" access="DELETE" module="Dealer"}
                    <a class="btn btn-default btn-xs dealer_tab-delete" title="{intl l='Delete this DealerTab' d='dealer.bo.default'}" data-target="#dealer_tab-delete" data-toggle="modal" data-id="{$ID}">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                {/loop}
            </div>
        </td>
    </tr>
{/loop}
```

