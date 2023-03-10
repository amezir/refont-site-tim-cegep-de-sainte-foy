<?php
declare (strict_types = 1);
namespace App\Modeles;
use \PDO;
use App;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Message{
    private $id;
    private $prenom_nom;
    private $courriel;
    private $telephone;
    private $consentement;
    private $sujet;
    private $contenu;
    private $dateheure_creation;
    private $responsable_id;

    public function __construct(){
    }


    static function envoyeBdd():array{
        $pdo = App\App::getPDO();

        $prenom_nom = $_POST['nom'];
        $courriel = $_POST['courriel'];
        $responsable_id = $_POST['responsable_id'];
        $consentement = $responsable_id == 4 ? $_POST['consentement'] : '';
        $sujet = $_POST['sujet'];
        $contenu = $_POST['message'];
        $telephone = $responsable_id == 4 ? $_POST['telephone'] : '';
        $dateheure_creation = date("Y-m-d H:i:s");


        $sql = "INSERT into `messages` (prenom_nom, courriel, telephone, consentement, sujet, contenu, dateheure_creation, responsable_id)
                VALUES ('$prenom_nom', '$courriel', '$telephone', '$consentement', '$sujet', '$contenu', '$dateheure_creation', '$responsable_id')";

        $requete = $pdo->prepare($sql);
        $requete->setFetchMode(PDO::FETCH_CLASS, Message::class);
        $requete->execute();
        $messages = $requete->fetchAll();
        return $messages;
    }

    static function envoyerMail():array{
        $transport = Transport::fromDsn('smtp://amezirmessaoud.test@gmail.com:yphrmvtqybrfedrr@smtp.gmail.com:587');
        $mailer = new Mailer($transport);

        $prenom_nom = $_POST['nom'];
        $courriel = $_POST['courriel'];
        $responsable_id = $_POST['responsable_id'];
        $consentement = $responsable_id == 4 ? $_POST['consentement'] : '';
        $sujet = $_POST['sujet'];
        $contenu = $_POST['message'];
        $telephone = $responsable_id == 4 ? $_POST['telephone'] : '';
        $dateheure_creation = date("Y-m-d H:i:s");

        switch($responsable_id){
            case 1:
                $responsable_id = "techmultimedia@csfoy.ca";
                break;
            case 2:
                $responsable_id = "eve.fevrier@csfoy.ca";
                break;
            case 3:
                $responsable_id = "stages-tim@csfoy.ca";
                break;
            case 4:
                $responsable_id = "benoit.frigon@csfoy.ca";
                break;
            default:
                $responsable_id = "test";
        }
        
        $email = new Email();
        $email->from('amezirmessaoud.test@gmail.com')
                    ->to($responsable_id)
                    ->subject('Formulaire de contact - ' . $sujet)
                    ->text('Pr??nom et nom : '. $prenom_nom .'Adresse courriel : '. $courriel .' Num??ro de t??l??phone : '. $telephone .'' . $contenu . '')
                    ->html('<h3>Pr??nom et nom : '. $prenom_nom .'</h3><h3>Adresse courriel : '. $courriel .'</h3><h3>Num??ro de t??l??phone : '. $telephone .'</h3><h4>Contenu du message : </h4><p>' . $contenu . '</p>');


        // ENVOYER LE COURRIEL
        $bilan = 'OK'; // Par d??faut.
        try {
            // Essayer d'envoyer...
            $mailer->send($email);
            $_SESSION['retroactions'] = '<i class="fa fa-check-circle" aria-hidden="true" style="color:#53db3f; padding-right:5px"></i>Le courriel a ??t?? envoy??.';
        } catch (TransportExceptionInterface $e) {
            // Si ??a ne fonctionne pas...
            $_SESSION['retroactions'] = "<i class='fa fa-exclamation-circle' aria-hidden='true' style='color:#DB3F3F; padding-right:5px'></i>L'envoi du courriel a avort??..";
        }
        return $_SESSION;
    } 
    
}
?>