<?php

abstract class Personnage
{
  protected $_degats,
          $_id,
          $_nom,
          $_experience = 0,
          $_niveau,
          $_tempsdodo,
          $_nature,
          $_atout;
  
  const CEST_MOI = 1; 
  const PERSONNAGE_TUE = 2; 
  const PERSONNAGE_FRAPPE = 3; 
  const PERSONNAGE_ENSORCELE = 4;
  const PAS_DE_MAGIE = 5;
  const PERSO_ENDORMI = 6;
  
  
  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
    $this->_nature = strtolower(static::class);
  }
  
  public function estEndormi()
  {
    return $this->_tempsdodo > time();
  }

  public function frapper(Personnage $perso)
  {
      if ($this->id() == $perso->id()){
          return self::CEST_MOI;
      }
       
      if ($this->estEndormi()){
          return self::PERSO_ENDORMI;
      }
       
      return $perso->recevoirDegats();
  }
  
  public function recevoirDegats($degats)
  {
      $this->setDegats($this->degats() + $degats);
       
      if ($this->degats() >= 100){
          return self::PERSONNAGE_TUE;
      }
      return self::PERSONNAGE_FRAPPE;
  }

  public function hydrate(array $donnees)
  {
    foreach ($donnees as $key => $value)
    {
      $method = 'set'.ucfirst($key);
      
      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }

  public function reveil(){
    $secondes = $this->tempsDodo();
    $secondes -= time();

    $heures = floor($secondes / 3600);
    $secondes -= $heures * 3600;
    $minutes = floor($secondes / 60);
    $secondes -= $minutes * 60;

    $heures .= $heures <= 1 ? ' heure' : ' heures';
    $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
    $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';

    return $heures . ', ' . $minutes . ' et ' . $secondes;
  }

  public function degats()
  {
    return $this->_degats;
  }
  
  public function id()
  {
    return $this->_id;
  }
  
  public function nom()
  {
    return $this->_nom;
  }

  public function atout()
  {
    return $this->_atout;
  }

  public function tempsDodo()
  {
    return $this->_tempsdodo;
  }
  public function nature()
  {
    return $this->_nature;
  }

  public function setDegats($degats)
  {
    $degats = (int) $degats;
    
    if ($degats >= 0 && $degats <= 100)
    {
      $this->_degats = $degats;
    }
  }
  
  public function setId($id)
  {
    $id = (int) $id;
    
    if ($id > 0)
    {
      $this->_id = $id;
    }
  }
  
  public function setNom($nom)
  {
    if (is_string($nom))
    {
      $this->_nom = $nom;
    }
  }

public function setAtout($atout)
{
  $atout = (int) $atout;
    if ($atout >= 0 && $atout <= 100)
      {
        $this->_atout = $atout;
      }
}

public function setTempsDodo($tempsdodo)
{
  $tempsdodo= (int) $tempsdodo;
    if ($tempsdodo>= 0)
      {
        $this->_tempsdodo = $tempsdodo;
      }
}

public function nomValide()
{
  return !empty($this->_nom);
}
}