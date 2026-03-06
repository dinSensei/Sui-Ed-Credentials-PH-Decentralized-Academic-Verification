<?php
// generate_hash.php

// 1. Database Connection (Update with your actual credentials later)
$host = 'localhost';
$dbname = 'sui_ed_credentials';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// The ID of the student graduating (e.g., passed via a POST request from the adviser dashboard)
$student_id = '2026-0001'; 
$issuer_name = 'Cittadini School'; // The authoritative entity minting the diploma

try {
    // 2. Fetch Student Demographic & Program Data
    $stmt = $pdo->prepare("
        SELECT s.student_id, s.lrn, s.first_name, s.last_name, p.program_code 
        FROM students s
        JOIN programs p ON p.program_id = ? -- Assuming program_id is passed or known
        WHERE s.student_id = ?
    ");
    // For this example, let's assume program_id 1 is "SHS - TVL ICT"
    $stmt->execute([1, $student_id]);
    $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student_info) {
        die("Student not found.");
    }

    // 3. Fetch Final Grades for the Student
    $stmt = $pdo->prepare("
        SELECT sub.subject_code, sub.subject_name, g.final_grade, g.school_year
        FROM grades g
        JOIN subjects sub ON g.subject_id = sub.subject_id
        WHERE g.student_id = ?
        ORDER BY sub.subject_code ASC -- Crucial: Always order data so the hash is consistent
    ");
    $stmt->execute([$student_id]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Construct the Payload
    // We combine the data into a single, structured array.
    $payload = [
        "issuer" => $issuer_name,
        "student" => [
            "id" => $student_info['student_id'],
            "lrn" => $student_info['lrn'],
            "name" => $student_info['first_name'] . " " . $student_info['last_name'],
            "program" => $student_info['program_code']
        ],
        "academic_records" => $grades
    ];

    // 5. Convert to JSON and Hash
    // JSON_UNESCAPED_UNICODE ensures special characters aren't scrambled.
    $json_string = json_encode($payload, JSON_UNESCAPED_UNICODE);
    
    // Generate the SHA-256 hash. This is the exact 64-character string going to the Sui network.
    $document_hash = hash('sha256', $json_string);

    echo "<h3>Success! Data Prepped for Web3</h3>";
    echo "<strong>JSON Payload (Saved locally):</strong><br>";
    echo "<pre>" . print_r($json_string, true) . "</pre><br>";
    echo "<strong>SHA-256 Document Hash (Sent to Sui):</strong><br>";
    echo "<code style='background:#eee; padding:5px;'>" . $document_hash . "</code>";

    // 6. (Optional Next Step) Insert this hash into the `blockchain_credentials` table 
    // to mark it as 'pending' before calling the Node.js Sui SDK!

} catch (Exception $e) {
    echo "Error generating hash: " . $e->getMessage();
}
?>
