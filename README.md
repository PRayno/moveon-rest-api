# MoveOn REST API Symfony bundle

MoveOn (https://www.qs-unisolution.com/moveon/) is an application used to manage International Mobility between universities and schools (Erasmus program for example).
This package is a Symfony bundle for the MoveOn API.

## Installation
Install the library via Composer by running the following command:

`composer require prayno/moveon-rest-api`

### Prerequisites
Prior to usage, you must contact your MoveON technical rep to activate the API in your MoveON instance.

### Configuration

This package includes a Symfony bundle already registered to your instance. All you need to do is to add those variables in the .env file of the Symfony app :
>MOVEON_REST_API_BASE_URI="https://mymoveoninstance.restapi.moveonfr.com/api/v1/"
>MOVEON_REST_API_USERNAME="accoun@myemail.com"
>MOVEON_REST_API_PASSWORD="MyComplexPassword"

## Usage

Please refer to the API documentation in the following url :  
https://qsunisolution.zendesk.com/hc/en-us/articles/25476553829276-1-14-RESTful-API?brand_id=290751

Inject the MoveOnRestApi class into your constructor.

```php
public function __construct(private readonly MoveOnRestApi $moveOnRestApi)
{
    parent::__construct();
}
```

### Search for elements
```php
$relations = $this->moveOnRestApi->search('relations', [
    ["field"=>"relation_status_id","operator"=>"in","value"=>"1,2"]
],10);
```

### Update elements
```php
$this->moveOnRestApi->update("relations",1234,["customField"=>
    ["customfield1234"=>"foo","customfield4567"=>"bar"]
]);
```