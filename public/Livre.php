<?php
Class Livre{
    public $titre;
    public $auteur;
    // function __construct(){
    //     $this->titre = "livre inconnu";
    // }
    function   __construct($nom , $auteur){
        $this->titre=$nom;
        $this->titre=$auteur;

    }
    //}
}
 $livre1= new Livre("livre reconnu","MEFENZA");
 echo "mon livre : ". $livre1->titre;
 //echo `mon livre : {$livre1->titre}`;
?>