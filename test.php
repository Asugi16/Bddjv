<?php
// On crée deux personnages
$perso1 = new Personnage;
$perso2 = new Personnage;
    
// Ensuite, on veut que le personnage n°1 frappe le personnage n°2.
$perso1->frapper($perso2);

class Personnage
{
  private $_degats; // Les dégâts du personnage.
  private $_experience; // L'expérience du personnage.
  private $_force; // La force du personnage (plus elle est grande, plus l'attaque est puissante).
        
  public function frapper($persoAFrapper)
  {
    $persoAFrapper->_degats += $this->_force;
  }
        
  public function gagnerExperience()
  {
    // On ajoute 1 à notre attribut $_experience.
    $this->_experience = $this->_experience + 1;
  }
}
frapper($perso1);
frapper($perso2);