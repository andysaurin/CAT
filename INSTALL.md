## Installation


**Requirements**

  - A [LAMP server](https://en.wikipedia.org/wiki/LAMP_(software_bundle\))
  
  - [bedools](https://github.com/arq5x/bedtools2)
  
  - The [Genomic Association Tool](https://github.com/AndreasHeger/gat/blob/master/scripts/gat-run.py)


To host your own ChIP Annotation Tester:

**Clone the CAT repository**

```
git clone https://github.com/andysaurin/CAT
```


**Install in the data directory**

```
cd cat/data
```


**Install the database**

Create a MySQL user that has CREATE privileges 
  - By default (and in the config/db.php config file), the username is ```CAT``` and password is ```ChangeThisPassToYourDbUserPassword```

  - Create the database and structure

    ```
    cd data
    mysql -u CAT -pChangeThisPassToYourDbUserPassword < db.sql
    ```

  - Import the primary data

    ```
    mysql -u CAT -pChangeThisPassToYourDbUserPassword CAT < db_data.sql
    ```


**Install the modENCODE and ENCODE BED files**

The BED file git clone happens in the ```data``` directory.

```
git clone https://github.com/andysaurin/encode_bedfiles
```


**Install the pre-computed CAT data**

  - modENCODE Drosophila CAT data
  
    ```
    wget http://www.biotools.fr/downloads/precomputed_CAT_data/db_drosophila_gat_data.sql
    mysql -u CAT -pChangeThisPassToYourDbUserPassword CAT < db_drosophila_gat_data.sql
    
    wget http://www.biotools.fr/downloads/precomputed_CAT_data/db_drosophila_gat_data_noHOT.sql
    mysql -u CAT -pChangeThisPassToYourDbUserPassword CAT < db_drosophila_gat_data_noHOT.sql
    ```
  
  - ENCODE Mouse and Human CAT data
    
    - This data is only available for non-profit research. Contact Andy Saurin for access to the data
    

**Configure CAT**

  - Modify general paramaters: ```config/config.php``` 
    - The parameters should be self explanatory!


  - Modify database paramters ```config/db.php``` 
    - Only required if you modified default MySQL parameters from those given above
   
   

**Configure Apache**

  - The htdocs directory is the Apache DocumentRoot
  
  - Enable the mod_rewrite Apache module