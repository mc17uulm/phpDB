# phpDB

Version 1.0.1

### Usage

#### Initialize Connection

``Database::initialize(string host, int port, string db_name, string username, string password)``

````php

use phpDB\Connection;
use phpDB\DatabaseException;

try {
    Connection::initialize(
        "localhost",
        3306,
        "database",
        "username",
        "password"
     );
} catch (DatabaseException $e) {
    echo $e->getMessage();
}
````

#### Simple SQL Select

````php

/** 
 * Connection already initialized with Connection::initialize()
*/

use phpDB\Database;
use phpDB\QueryException;

try {
    $rs = Database::select("SELECT * FROM users WHERE id = ? LIMIT 1", 1);
    
    if($rs->was_success()) {
        echo $rs->get_first_result()["username"];
    } else {
        echo "Failure: " . $rs->get_error_msg();
    }
} catch(QueryException $e) {
    echo $e->getMessage();
}
````

#### Close connection

The connection is automatically closed after your script is executed. To close ist manually:

````php

use phpDB\Database;

Database::close();

````