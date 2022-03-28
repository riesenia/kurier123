# 123Kurier API client
123Kurier API client implemetation. See 123Kurier api documentation for details.
## Installation
Install the latest version using `composer require riesenia/kurier123`

## Usage

Create API with username and password

```php
use Riesenia\Kurier123\Api;

$api = new Api($username, $password);
```

### Create shipment

```php
$data = [
    'clientOrderNumber' => 12345678,
    'sender' => [
        'id' => 1 
    ],
    'recipient' => [
        'name' => 'John Doe',
        'street' => 'Foo',
        'city' => 'Bar',
        ...
    ],
    ...
];

if (!$api->createShipment($shipment)) {
    echo $api->getErrors();
}
```

### Print shipment labels

```php
$data = [
    // Order numbers
    '60221080912166',
    '60221080912167'
];

$data = $api->generateTickets($data, 'A4');

if (!$data) {
    echo $api->getErrors();
}
```

### Get order status history

```php
$data = [
    // Order numbers
    '60221080912166',
    '60221080912167'
];

$history = $api->statusHistory($data);

if (!$history) {
    echo $api->getErrors();
}
```