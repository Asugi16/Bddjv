<?php
class Personnage
{
  private $_degats,
          $_id,
          $_nom,
          $_experience = 0,
          $_niveau,
          $_superforce = 0;
  
  const CEST_MOI = 1; 
  const PERSONNAGE_TUE = 2; 
  const PERSONNAGE_FRAPPE = 3; 
  const PERSONNAGE_PREND_EXPERIENCE = 4;
  const PERSONNAGE_LVL_UP = 5;
  const PERSONNAGE_PREND_FORCE = 6;
  
  
  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
  }
  
  public function frapper(Personnage $perso)
  {
    $this->_experience +=5;
    $superforce = $this->_superforce;

    if ($perso->id() == $this->_id)
    {
      return self::CEST_MOI;
    }
    return $perso->recevoirDegats($superforce);
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
  
  public function recevoirDegats($superforce)
  {
    $this->_degats += 5 + $superforce;
  
    if ($this->_degats >= 100)
    {
      return self::PERSONNAGE_TUE;
    }
    
      return self::PERSONNAGE_FRAPPE;
  }

  public function recevoirExperience()
  {
    $this->_experience += 10;

    if ($this->_experience <=100)
    {
      return self::PERSONNAGE_PREND_EXPERIENCE;
    }
  }
  public function recevoirSuperForce()
  {
    if ($this->_superforce <=28)
    $this->_superforce +=2;
  {
    return self::PERSONNAGE_PREND_FORCE;
  }
  }
  
  public function prendreNiveau(){

    if ($this->_experience == 100)
    {
      $this->_niveau +=1;
      $this->_experience = 0;

      return self::PERSONNAGE_LVL_UP;   
    }
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
  
  public function experience()
  {
    return $this->_experience;
  }

  public function niveau()
  {
    return $this->_niveau;
  }

  public function superForce()
  {
    return $this->_superforce;
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

  public function setExperience($experience)
  {
    $experience = (int) $experience;

    if ($experience >=0 && $experience <=100)
    {
      $this->_experience = $experience;
    }
  }

  public function setNiveau($niveau)
  {
    $niveau = (int) $niveau;

    if ($niveau >=1 && $niveau <=50)
    {
      $this->_niveau = $niveau;
    }
  }

  public function setForce($superforce)
  {
    $superforce = (int) $superforce;

    if ($superforce >=0 && $superforce <=28)
    {
      $this->_superforce = $superforce;
    }
  }

public function nomValide()
{
  return !empty($this->_nom);
}
}