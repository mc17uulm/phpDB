# phpDB

Version 1.0.5

### Usage

#### Initialize Connection

``Database::connect(string host, int port, string db_name, string username, string password, bool debug = false)``

````php

use phpDB\Database;
use phpDB\DatabaseException;

try {

    Database::connect(
        "localhost",
        3306,
        "database",
        "username",
        "password",
        true
     );

    // Do stuff with Database
    
    Database::close();

} catch (DatabaseException $e) {
    echo $e->getMessage();
}
````

#### Simple SQL Select

````php

/** 
 * Database already initialized with Database::connect()
*/

use phpDB\Database;
use phpDB\DatabaseException;

try {
    // select() returns array with fetched columns
    $result = Database::select("SELECT * FROM users WHERE id = ? LIMIT 1", 1);
    
    
    if(count($result) === 0) {
        echo "No dataset found";
    } else {
        echo $result[0]["username"];
    }
} catch(DatabaseException $e) {
    echo $e->getMessage();
}
````

#### Close connection

````php

use phpDB\Database;

Database::close();

````