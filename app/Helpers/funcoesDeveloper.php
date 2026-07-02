<?php

namespace App\Helpers;

class funcoesDeveloper {
 //<======== Funções DEVELOPER ============>

    const PRE = '<pre>';
    const PREF = '</pre>';
    const BR = '<br>';
    

    //
    function imperador( $user = 0 ){

        if( $_SESSION["UserID"] == $user ){
            return true;
        }
        else {
            return false;
        }
    }
    
    //
    function pr($texto){
        print($texto);
        echo self::BR ;
    }

    //
    function pa( $array, $titulo = '' ){
        if($titulo != ''){
            self::eh2($titulo);
        }
        if( !empty($array) ){
            echo self::PRE;
            print_r( $array );
            echo self::PREF;
        } else {
            self::ep("Array()");
        }
    }
    
    function pae( $array, $titulo = '' ){
        if($titulo != ''){
            self::eh2($titulo );
        }
        if( !empty($array) ){
            echo self::PRE;
            print_r( $array );
            echo self::PREF;
        } else {
            self::ep("Array()");
        }
        exit();
    }

    //
    function pau( $array, $user = 0, $titulo = '' ){
        if( $user != 0 AND $_SESSION["UserID"] == $user ) {
            // apenas faz o pae se o ID do user for o que atualmente está em variavel de sessão
            if($titulo != ''){
                self::eh2($titulo );
            }
            if( !empty($array) ){
                echo self::PRE;
                print_r( $array );
                echo self::PREF;
            } else {
                self::ep("Array()");
            }
        }
    }
    
    //
    function paeu( $array, $user = 0, $titulo = '' ){
        if( $user != 0 AND $_SESSION["UserID"] == $user ){
            // apenas faz o pae se o ID do user for o que atualmente está em variavel de sessão
            if($titulo != ''){
                self::eh2($titulo );
            }
            if( !empty($array) ){
                echo self::PRE;
                print_r( $array );
                echo self::PREF;
            } else {
                self::ep("Array()");
            }
            exit();
        }
    }
    
    //
    function eh2( $string, $stop = 0 ){
        echo "<h2>" . $string . "</h2>";
        if( $stop == 1 ){
            exit();
        }
    }
    
    //
    function ep( $string, $stop = 0 ){
        echo "<p>" . $string . "</p>";
        if( $stop == 1 ){
            exit();
        }
    }
    
    //
    function stop($string = ""){
        if( $string == "" ){
            echo "<p>STOP</p>";
            exit();
        }else{
            echo "<p>". $string ."</p>";
            exit();
        }
    }
}