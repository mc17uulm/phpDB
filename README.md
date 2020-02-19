# phpDB

### Usage

#### Initialize Connection

``Connection::initialize(string host, int port, string db_name, string username, string password)``

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

use phpDB\QueryFactory;
use phpDB\QueryException;

try {
    $f = new QueryFactory();
    $f->select("*")->from("users")->where(":id = id", [":id" => 1])->limit(1);
    $rs = $f->execute();
    
    if($rs->was_success()) {
        echo $rs->get_first_result()->get_value("username");
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

use phpDB\Connection;

Connection::close();

````