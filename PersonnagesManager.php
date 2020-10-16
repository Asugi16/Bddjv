<?php
class PersonnagesManager
{
  private $_db;
  
  public function __construct($db)
  {
    $this->setDb($db);
  }
  
  public function add(Personnage $perso)
  {
    $request = $this->_db->prepare('INSERT INTO personnages(nom) VALUES(:nom)');
    $request->bindValue(':nom', $perso->nom());
    $request->execute();
    
    $perso->hydrate([
      'id' => $this->_db->lastInsertId(),
      'degats' => 0,
      'experience' =>0,
      'niveau' => 1,
      'superforce' => 0,
    ]);
  }
  
  public function count()
  {
    return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
  }
  
  public function delete(Personnage $perso)
  {
    $this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->id());
  }
  
  public function exists($info)
  {
    if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
    {
      return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
    }
    
    $request = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
    $request->execute([':nom' => $info]);
    
    return (bool) $request->fetchColumn();
  }
  
  public function get($info)
  {
    if (is_int($info))
    {
      $request = $this->_db->query('SELECT id, nom, degats, experience, niveau, superforce FROM personnages WHERE id = '.$info);
      $donnees = $request->fetch(PDO::FETCH_ASSOC);
      
      return new Personnage($donnees);
    }
    else
    {
      $request = $this->_db->prepare('SELECT id, nom, degats, experience, niveau, superforce FROM personnages WHERE nom = :nom');
      $request->execute([':nom' => $info]);
    
      return new Personnage($request->fetch(PDO::FETCH_ASSOC));
    }
  }
  
  public function getList($nom)
  {
    $persos = [];
    
    $request = $this->_db->prepare('SELECT id, nom, degats, experience, niveau, superforce FROM personnages WHERE nom <> :nom ORDER BY nom');
    $request->execute([':nom' => $nom]);
    
    while ($donnees = $request->fetch(PDO::FETCH_ASSOC))
    {
      $persos[] = new Personnage($donnees);
    }
    
    return $persos;
  }
  
  public function update(Personnage $perso)
  {
    $request = $this->_db->prepare('UPDATE personnages SET degats = :degats, experience = :experience, niveau = :niveau, superforce = :superforce WHERE id = :id');
    
    $request->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
    $request->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);
    $request->bindValue(':niveau', $perso->niveau(), PDO::PARAM_INT);
    $request->bindValue(':superforce', $perso->superforce(), PDO::PARAM_INT);
    $request->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    
    $request->execute();
  }
  
  public function setDb(PDO $db)
  {
    $this->_db = $db;
  }
}