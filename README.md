# PHPDatabase

Simple PHP script for handling mysql database requests and connection

### Usage

#### Basic Examples

**Connecting to database**

```php

Database::load("host", 123, "database", "username", "password");

```

**Connecting to database with env config file**

```php

Database::load_from_env();

```

**Execute select query**

```php

$result = Database::select(
    "SELECT id FROM users WHERE username = :username",
    array(":username" => "my.username")
);

```

**Execute select query (with handling function)**

```php

$result = Database::execute(
    "SELECT id FROM users WHERE username = :username",
    array(":username" => "my.username"),
    function ($request, $response) {
        // here you can work with the PDO query and execute objects
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
);

```