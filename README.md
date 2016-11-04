
#![ChIP Association Tester](http://www.biotools.fr/images/heatmap_medium.png "CAT") CAT - A ChIP Association Tester

## About

The ChIP Association Tester is an online bioinformatics tool created by Andy Saurin to test for ChIP colocalisation. 
You can either check a series of your own ChIP data using **webCAT** or test colocalisation of your data with modENCODE data using **drosoCAT**, or ENCODE data using **mouseCAT** or **humanCAT**

A working online version is available on Andy's [BioTools website](http://www.biotools.fr) : [http://www.biotools.fr/CAT](http://www.biotools.fr/CAT)

Or you can clone this repository and [host it (privately or publicly) on your own server/cluster](https://github.com/andysaurin/CAT/blob/master/INSTALL.md).


* **webCAT** 
  - Simply enter your own ChIP data (>2 datasets) to test ChIP association
     - Try out [webCAT](http://www.biotools.fr/CAT/webCAT) for yourself!
    

* **humanCAT** 
  - Check colocalisation/association of ENCODE Human ChIP data
     - Add your own **hg19** ChIP peaks
  
  - All ChIP data from Tier 1 and Tier 2 cell types
     - [https://genome.ucsc.edu/ENCODE/dataMatrix/encodeChipMatrixHuman.html](https://genome.ucsc.edu/ENCODE/dataMatrix/encodeChipMatrixHuman.html)
  
  - Ignore ChIP peaks of transcription factors localising to HOT regions is supported through a simple checkbox
     - HOT region data source: [http://encodenets.gersteinlab.org/metatracks/HOT_All_merged.tar.gz](http://encodenets.gersteinlab.org/metatracks/HOT_All_merged.tar.gz)
    
  - Try out [humanCAT](http://www.biotools.fr/CAT/humanCAT) for yourself!


* **mouseCAT** 
  - Check colocalisation/association of ENCODE Mouse ChIP data
     - Add your own **mm9** ChIP peaks
  
  - All ChIP data from all cell and tissue types
     - [https://genome.ucsc.edu/ENCODE/dataMatrix/encodeChipMatrixMouse.html](https://genome.ucsc.edu/ENCODE/dataMatrix/encodeChipMatrixMouse.html)
  
  - Try out [mouseCAT](http://www.biotools.fr/CAT/mouseCAT) for yourself!


* **drosoCAT** 
  - Check colocalisation/association of modENCODE Drosophila melanogaster ChIP data
     - Add your own **dm3** ChIP peaks
  
  - All ChIP data from cell lines and embryos
     - [http://data.modencode.org/?Organism=D.%20melanogaster](http://data.modencode.org/?Organism=D.%20melanogaster)
  
  - Ignore ChIP peaks of transcription factors localising to HOT regions is supported through a simple checkbox
     - HOT region data source: Data S8 from the manuscript [http://www.modencode.org/publications/fly_2010pubs/index.shtm](Identification of Functional Elements and Regulatory Circuits by Drosophila modENCODE)
     - HOT regions defined as those regions showing binding of 8 or more transcription factors 
    
  - Try out [drosoCAT](http://www.biotools.fr/CAT/drosoCAT) for yourself!


