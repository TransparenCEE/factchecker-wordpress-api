The backend-part of the Fact-checking scripts uses PEAR libraries (/libraries folder) to connect to the database, run SQL statements, export data, paging the tables, and so on.
The is no need to compile the PHP interpreter with the PEAR libraries (the PEAR calls are made to the functions/classes placed in the /libraries folder).
The connection to the database is defined in the constructor.inc.php script.


