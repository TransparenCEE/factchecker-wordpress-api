1. Module scope

The fact-checking module scope is to export the data from a fact-checking data structure (in our case a set of Wordpress tables), to allow the indexing of the data with some info related to the URLs where the content might be used and to have a API that returns the fact-checking elements once a call is made with a certain URL.

2. Description: 
This module import data, factchecks, from factual.ro wordpress website and insert the data into separated administrative area. 
Admin import factchecks page: http://www.factual.ro/api/administrative/factcheck_content.php
The imported factchecks are listed in admin. A link can be asigned to each item: http://www.factual.ro/api/administrative/factchecks_list.php 

3. Scripts instalation


Step 1: Uplad the files in a folder (for example /fact-checking_api)

Step 2: edit the /administrative/common/config.php file to match the database credentials and server configuration

Step 3: Create a new database and import the sql dump to create the table structure (sql file is placed in the factual.ro_db_structure folder)

Step 4: In case the data source has a diffrent setup edit the data retrieval scripts (see 4.A)

Step 5: Access the administrative backend  (/administrative) to import the data and start using the module. The user/password credentials are admin/test




4. Backend scripts description/usage:


A. Data retrieval
- /administrative/factcheck_content.php  + /administrative/factcheck_content_exec.php   - Allows the importing of the facts from the external database (in our case the Wordpress custom structure/tables to a abstrractized database that can be used with other types of platforms.)


B.  URLs adding to the fact-checking items
/administrative/factchecks_list.php +  - /administrative/factchecks_links_list.php _/administrative/factchecks_list_ajax.php
 =====>  allows the system manager to set URLs related to the fact-checking items where the API should return the content of a particular fact-checking item + show the list of the factchecking

C. Bulk importing of the URLs for the items
/administrative/factchecks_csv_import_ajax.php   =====> Allows the importing of a CSV file containing the links snipets where an fact-checking item should be returned once the API makes a call containing a particular link

D. API usage stats

/administrative/api_stats.php - API statistics   ======> listing of the stats related to the API usage (the number of calls per day, weeks, months, top links, top factchecks)



5.  API 


Factchecks API delivers the data using the  JSON data-interchange format. 

API request method example:
http://www.factual.ro/api/?q=a5605c929a9611efaaa5ba66bbafc7bf


"q" variable is a md5  hash code of the assigned link.


Note: In order to insure the privacy of the Chrome extension user, the communication of the URL between the factual.ro server and the client (the Chrome extension) used the MD5 method to hash the content of the URL. Otherwise the factual.ro admin might know all the browsing history of a particular user.

Data output/response schema (JSON formated):

q  = the URL hash code
declaratie = the declaration that was validated/invalidated by the fact-checking team
context = the context where the declaration was issued 
status = the status of the declaration (false, true, partially true)
concluzie = the conclusion of the fact-checking team
sursa = the source of the declaration
URL =  the online resource of the fact-checking iteam 


JSON response schema: 

{
  "type": "object",
  "properties": {
    "q": {
      "type": "string"
    },
    "data": {
      "type": "object",
      "properties": {
        "147": {
          "type": "object",
          "properties": {
            "declaratie": {
              "type": "string"
            },
            "context": {
              "type": "string"
            },
            "status": {
              "type": "string"
            },
            "concluzie": {
              "type": "string"
            },
            "sursa": {
              "type": "string"
            },
            "url": {
              "type": "string"
            },
            "date": {
              "type": "integer"
            }
          },
          "required": [
            "declaratie",
            "context",
            "status",
            "concluzie",
            "sursa",
            "url",
            "date"
          ]
        }
      },
      "required": [
        "147"
      ]
    }
  },
  "required": [
    "q",
    "data"
  ]
}




Response example:
{
"q":"a5605c929a9611efaaa5ba66bbafc7bf",
"data":{
"147":{
"declaratie":"Gabi Firea este sprijinit\u0103 de partid, a fost sprijinit\u0103 s\u0103 c\u00e2\u0219tige alegerile, este \u0219i acum. Am v\u0103zut o \u0219tire c\u0103 Gabi Firea a t\u0103iat pomii pe Kiseleff. Nu exist\u0103 a\u0219a ceva. Nu s-a t\u0103iat niciun pom.",
"context":"<span style=\"font-weight: 400;\">Invitat \u00een cadrul unei emisiuni de la Rom\u00e2nia Tv, pre\u0219edintele PSD, Liviu Dragnea, <\/span><a href=\"http:\/\/www.agerpres.ro\/politica\/2016\/07\/10\/dragnea-de-cand-e-gabriela-firea-primar-nu-s-a-taiat-niciun-pom-pe-kiseleff-21-41-07\"><span style=\"font-weight: 400;\">a declarat<\/span><\/a><span style=\"font-weight: 400;\">:<\/span>\r\n\r\n \r\n<blockquote>Gabi Firea este sprijinit\u0103 de partid, a fost sprijinit\u0103 s\u0103 c\u00e2\u0219tige alegerile, este \u0219i acum. Am v\u0103zut o \u0219tire c\u0103 Gabi Firea a t\u0103iat pomii pe Kiseleff. Nu exist\u0103 a\u0219a ceva. Nu s-a t\u0103iat niciun pom. Vreau s\u0103 v\u0103 spun ceva, e foarte simplu, dac\u0103 pe Kiseleff s-ar t\u0103ia o crengu\u021b\u0103 toate televiziunile ar fi acolo, c\u0103 e Kiseleff. Alea erau poze de nu \u0219tiu c\u00e2nd. Nu avea cum ca s\u0103 se taie un pom pe Kiseleff, ca s\u0103 se taie un pom trebuie s\u0103 fie blocat Kiselefful. De c\u00e2nd e Gabi Firea primar nu s-a t\u0103iat niciun pom.<\/blockquote>",
"status":"Fals",
"concluzie":"<span style=\"font-weight: 400;\"><strong>Declara\u021bia lui Liviu Dragnea este fals\u0103<\/strong>. La 6 zile de c\u00e2nd Gabriela Firea a preluat func\u021bia de primar general al capitalei, Direc\u021bia de Mediu din cadrul PMB a emis aviz pentru defri\u0219area a 72 de arbori de c\u0103tre ADP Sector 1, inclusiv pe Bd. Kiseleff.<\/span>",
"sursa":"",
"url":"http:\/\/www.factual.ro\/declaratii\/liviu-dragnea-de-cand-e-gabi-firea-primar-nu-s-taiat-niciun-pom\/",
"date":1468303937
}
}
}
http://www.factual.ro/api/?q=all - "q=all" method return all the factchecks from the databases.


The script where there the API calls are made
index.php - return the factchecks in JSON format




6.Extending the usage of the scripts

The importing of the data can be changed to accomodate various platforms(custom, or ready-made) that holds the fact-checking items. In our case (factual.ro) the solution was based on a Wordpress platform, but by editing   factcheck_content_exec.php you can customize the solution to different setups.

Beside editing that script (factcheck_content_exec.php ) - to match a differnt set of tables in a WP installation or to retrieve the info from other setups (for example, Drupal, or a custom platform, or different type of databases) - the other script can be used unmodified.



