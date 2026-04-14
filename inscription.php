<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $usePhpMailer = true;
} else {
    $usePhpMailer = false;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode non autorisee']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) { $input = $_POST; }

function clean($val) { return htmlspecialchars(strip_tags(trim($val ?? ''))); }

$prenom           = clean($input['prenom'] ?? '');
$nom              = clean($input['nom'] ?? '');
$email            = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$tel              = clean($input['tel'] ?? '');
$dob              = clean($input['dob'] ?? '');
$ville            = clean($input['ville'] ?? '');
$eglise_yn        = clean($input['eglise_yn'] ?? '');
$eglise           = clean($input['eglise'] ?? '');
$source           = clean($input['source'] ?? '');
$source_autre     = clean($input['source_autre'] ?? '');
$logement         = clean($input['logement'] ?? '');
$allergies        = clean($input['allergies'] ?? '');
$accessibilite    = clean($input['accessibilite'] ?? '');
$question_camp    = clean($input['question_camp'] ?? '');
$autres_questions = clean($input['autres_questions'] ?? '');

if (empty($prenom) || empty($nom) || empty($email) || empty($dob)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Champs obligatoires manquants']);
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email invalide']);
    exit();
}

define('SMTP_HOST', 'mail.infomaniak.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'contact@asonimage.ch');
define('SMTP_PASS', 'WTo&s6CC%C0*&24');
define('ADMIN_EMAIL', 'contact@asonimage.ch');

$eglise_line  = ($eglise_yn === 'oui' && $eglise) ? $eglise : ($eglise_yn === 'non' ? 'Non' : $eglise_yn);
$source_label = ($source === 'autre') ? $source_autre : $source;

$admin_body  = "Nouvelle inscription - A Son Image\n";
$admin_body .= str_repeat("=", 40) . "\n\n";
$admin_body .= "Prenom        : {$prenom}\n";
$admin_body .= "Nom           : {$nom}\n";
$admin_body .= "Email         : {$email}\n";
$admin_body .= "Telephone     : {$tel}\n";
$admin_body .= "Naissance     : {$dob}\n";
$admin_body .= "Ville/Region  : {$ville}\n";
$admin_body .= "Eglise        : {$eglise_line}\n";
$admin_body .= "Source        : {$source_label}\n";
$admin_body .= "Logement      : {$logement}\n";
$admin_body .= "Allergies     : {$allergies}\n";
$admin_body .= "Accessibilite : {$accessibilite}\n";
$admin_body .= "Question camp : {$question_camp}\n";
$admin_body .= "Autres        : {$autres_questions}\n";
$admin_body .= "\nDate : " . date('d/m/Y H:i') . "\n";

$confirm_body  = "Bonjour {$prenom},\n\n";
$confirm_body .= "Merci pour ton inscription au seminaire A Son Image (1-6 aout 2025).\n\n";
$confirm_body .= "Nous avons bien recu ta demande et tu recevras une confirmation d'ici quelques jours avec toutes les informations pratiques.\n\n";
$confirm_body .= "N'hesite pas a nous ecrire a contact@asonimage.ch si tu as des questions.\n\n";
$confirm_body .= "A bientot !\nL'equipe A Son Image\n";

function sendViaSMTP($to, $toName, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(SMTP_USER, 'A Son Image');
    $mail->addAddress($to, $toName);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->isHTML(false);
    $mail->send();
}

// Backup CSV
$csv_file   = __DIR__ . '/inscriptions.csv';
$csv_exists = file_exists($csv_file);
$fp = fopen($csv_file, 'a');
if ($fp) {
    if (!$csv_exists) {
        fputcsv($fp, ['Date','Prenom','Nom','Email','Tel','Naissance','Ville','Eglise','Source','Logement','Allergies','Accessibilite','Question','Autres']);
    }
    fputcsv($fp, [date('d/m/Y H:i'), $prenom, $nom, $email, $tel, $dob, $ville, $eglise_line, $source_label, $logement, $allergies, $accessibilite, $question_camp, $autres_questions]);
    fclose($fp);
}

try {
    if ($usePhpMailer) {
        sendViaSMTP(ADMIN_EMAIL, 'Equipe ASI', 'Nouvelle inscription - ' . $prenom . ' ' . $nom, $admin_body);
        sendViaSMTP($email, $prenom . ' ' . $nom, 'Confirmation inscription - A Son Image', $confirm_body);
    } else {
        $headers = "From: contact@asonimage.ch\r\nReply-To: contact@asonimage.ch\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        mail(ADMIN_EMAIL, '=?UTF-8?B?' . base64_encode('Nouvelle inscription - ' . $prenom . ' ' . $nom) . '?=', $admin_body, $headers);
        mail($email, '=?UTF-8?B?' . base64_encode('Confirmation inscription - A Son Image') . '?=', $confirm_body, $headers);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
