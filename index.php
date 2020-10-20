<?php
// On enregistre notre autoload.
function chargerClasse($classname)
{
  require $classname . '.php';
}

spl_autoload_register('chargerClasse');

session_start();

if (isset($_GET['deconnexion'])) {
  session_destroy();
  header('Location: .');
  exit();
}

$db = new PDO('mysql:host=localhost;dbname=personnages', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$manager = new PersonnagesManager($db);

if (isset($_SESSION['perso'])) // Si la session perso existe, on restaure l'objet.
{
  $perso = $_SESSION['perso'];
}

if (isset($_POST['creer']) && isset($_POST['nom'])) // Si on a voulu créer un personnage.
{
  switch ($_POST['nature']) {
    case 'magicien':
      $perso = new Magicien(['nom' => $_POST['nom']]);
      break;

    case 'guerrier':
      $perso = new Guerrier(['nom' => $_POST['nom']]);
      break;

    default:
      $message = 'Le type du personnage est invalide.';
      break;
  }

  if (!$perso->nomValide()) {
    $message = 'Le nom choisi est invalide.';
    unset($perso);
  } elseif ($manager->exists($perso->nom())) {
    $message = 'Le nom du personnage est déjà pris.';
    unset($perso);
  } else {
    $manager->add($perso);
  }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) // Si on a voulu utiliser un personnage.
{
  if ($manager->exists($_POST['nom'])) // Si celui-ci existe.
  {
    $perso = $manager->get($_POST['nom']);
  } else {
    $message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
  }
} elseif (isset($_GET['frapper'])) // Si on a cliqué sur un personnage pour le frapper.
{
  if (!isset($perso)) {
    $message = 'Merci de créer un personnage ou de vous identifier.';
  } else {
    if (!$manager->exists((int) $_GET['frapper'])) {
      $message = 'Le personnage que vous voulez frapper n\'existe pas !';
    } else {
      $persoAFrapper = $manager->get((int) $_GET['frapper']);

      $retour = $perso->frapper($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.

      switch ($retour) {
        case Personnage::CEST_MOI:
          $message = 'Mais... pourquoi voulez-vous vous frapper ???';
          break;

        case Personnage::PERSONNAGE_FRAPPE:

          $message = 'Le personnage a bien été frappé !';
          $manager->update($perso);
          $manager->update($persoAFrapper);

          break;

          case Personnage::PERSO_ENDORMI :
            $message = 'Vous êtes endormi, vous ne pouvez pas frapper de personnage !';

            break;

        case Personnage::PERSONNAGE_TUE:
          $message = 'Vous avez tué ce personnage !';
          $manager->update($perso);
          $manager->delete($persoAFrapper);

          break;
      }
    }
  }
}elseif (isset($_GET['ensorceler'])){

  if (!isset($perso)){
      $message = 'Merci de créer un personnage ou de vous identifier.';
  } else {
      if (!$manager->exists((int) $_GET['ensorceler'])){
          $message = 'Le personnage que vous voulez ensorceler n\'existe pas!';
      } else {

          $persoAEnsorceler = $manager->get((int) $_GET['ensorceler']);
          $retour = $perso->lancerUnSort($persoAEnsorceler);

          switch($retour)
          {
              case Personnage::CEST_MOI :
                  $message = 'Mais... pouquoi voulez-vous vous ensorceler ???';
                  break;
              case Personnage::PAS_DE_MAGIE :
                  $message = 'Vous n\'avez pas de magie !';

                  break;
              case Personnage::PERSONNAGE_ENSORCELE;
                  $message = 'Vous avez ensorcelé ce personnage !';

                  $manager->update($perso);
                  $manager->update($persoAEnsorceler);

                  break;

              case Personnage::PERSO_ENDORMI :
                  $message = 'Vous êtes endormi, vous ne pouvez pas frapper de personnage !';
                  break;
          }
      }
  }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TP : Mini jeu de combat</title>
        <meta charset="utf-8" />
    </head>
    <body>
     
        <p> Nombre de personnages créés : <?= $manager->count() ?></p>
    <?php
        if (isset($message)){
            echo '<p>'. $message . '</p>';
        }
         
        if (isset($perso)){
        ?>
            <p><a href="?deconnexion=1">Déconnexion</a></p>
         
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nature : <?=  ucfirst($perso->nature()) ?><br />
                    Nom : <?=  htmlspecialchars($perso->nom()) ?><br />
                    Dégâts : <?= $perso->degats() ?>
<?php
    switch ($perso->nature()){
        case 'magicien' :
            echo 'Magie : ';
            break;
        case 'guerrier' :
            echo 'Protection : ';
            break;
    }
    echo $perso -> atout();
?>
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui attaquer?</legend>
                <p>
                    <?php
                     
                    $persos = $manager->getList($perso->nom());  
                    if (empty($persos)) {
                        echo 'Personne à frapper!';
                    } else {
                        if ($perso -> estEndormi()){
                            echo 'Un magicien vous a endormi ! Vous allez vous réveiller dans ' . $perso->reveil() ; '.';
                        } else {
                            foreach($persos as $unPerso){
                                echo '<a href="?frapper='.$unPerso->id().'">'.htmlspecialchars($unPerso->nom()).'</a> (dégâts : '.$unPerso->degats().' | nature : ' . $unPerso -> nature().')';
                                 
                                if ($perso -> nature() == 'magicien')
                                {
                                    echo ' | <a href="?ensorceler='. $unPerso->id().'">Lancer un sort</a>';
                                }
                                 
                                echo '<br />';
                            }
                        }
                    }
                     
                    ?>
                </p>
            </fieldset>
             
         
        <?php
 
        } else {
             
    ?>
            <form action="" method = "post">
                <p>
                    Nom : <input type="text" name="nom" maxlength="50" />
                    <input type="submit" value = "Utiliser ce personnage" name="utiliser" /><br />
                     
                    nature : <select name="nature">
                        <option value="magicien">Magicien</option>
                        <option value="guerrier">Guerrier</option>
                    </select>
                     
                    <input type="submit" value = "Créer ce personnage" name="creer" />
                </p>
            </form>
    <?php
        }
    ?>
     
     
     
    </body>
</html>
<?php
if (isset($perso)){
    $_SESSION['perso'] = $perso;
}
?>