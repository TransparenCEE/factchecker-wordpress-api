This module import data, factchecks, from factual.ro wordpress website and insert the data into separated administrative area. 


Admin import factchecks page: http://www.factual.ro/api/administrative/factcheck_content.php


The imported factchecks are listed in admin. A link can be asigned to each item: http://www.factual.ro/api/administrative/factchecks_list.php 


Factchecks can be retreived using JSON API. API request example http://www.factual.ro/api/?q=a5605c929a9611efaaa5ba66bbafc7bf
"q" variable is a md5 code of the assigned link.

Response:
`{
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
}`


The response contains factchecks assigned with the requested link.

Admin API stats page provide statistics about API requests http://www.factual.ro/api/administrative/api_stats.php

Admin scripts:
- factcheck_content.php - display the import form
- factcheck_content_exec.php - import data from wordpress
- factchecks_list.php - list the imported items
- factchecks_list_ajax.php - assign a new link 
- factchecks_links_list.php - list asigned links
- factchecks_links_list_ajax.php - edit/disable a link
- factchecks_csv_import_ajax.php - assign multiple links from a CV file
- api_stats.php - API statistics
API SCRIPT:
- index.php - returns the factchecks in JSON format