<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize input fields
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $dob = htmlspecialchars($_POST['dob']);
    $address = htmlspecialchars($_POST['address']);
    $gender = htmlspecialchars($_POST['gender']);
    $bloodGroup = htmlspecialchars($_POST['bloodGroup']);
    $medicalHistory = htmlspecialchars($_POST['medicalHistory']);

    // Directories for uploads
    $uploadDir = 'uploads/';
    $idProofDir = $uploadDir . 'idProofs/';
    $docsDir = $uploadDir . 'medicalDocuments/';
    
    // Create directories if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!is_dir($idProofDir)) {
        mkdir($idProofDir, 0777, true);
    }
    if (!is_dir($docsDir)) {
        mkdir($docsDir, 0777, true);
    }

    // Handle ID Proof upload
    if (isset($_FILES['idProof']) && $_FILES['idProof']['error'] === UPLOAD_ERR_OK) {
        $idProofTmpPath = $_FILES['idProof']['tmp_name'];
        $idProofFileName = basename($_FILES['idProof']['name']);
        $idProofDestPath = $idProofDir . $idProofFileName;

        if (!move_uploaded_file($idProofTmpPath, $idProofDestPath)) {
            echo "<p style='color: red;'>Failed to upload ID proof. Please try again.</p>";
            exit;
        }
    } else {
        echo "<p style='color: red;'>ID proof upload error. Please try again.</p>";
        exit;
    }

    // Handle supporting medical documents upload
    $uploadedDocs = [];
    if (isset($_FILES['supportDocs']) && is_array($_FILES['supportDocs']['name'])) {
        foreach ($_FILES['supportDocs']['name'] as $index => $fileName) {
            if ($_FILES['supportDocs']['error'][$index] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['supportDocs']['tmp_name'][$index];
                $fileDestPath = $docsDir . basename($fileName);

                if (move_uploaded_file($fileTmpPath, $fileDestPath)) {
                    $uploadedDocs[] = $fileDestPath;
                } else {
                    echo "<p style='color: red;'>Failed to upload document: $fileName. Please try again.</p>";
                    exit;
                }
            }
        }
    } else {
        echo "<p style='color: red;'>Medical document upload error. Please try again.</p>";
        exit;
    }

    // Display the submission details
    echo "<h2>Medical Registration Submitted Successfully!</h2>";
    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Phone:</strong> $phone</p>";
    echo "<p><strong>Date of Birth:</strong> $dob</p>";
    echo "<p><strong>Address:</strong> $address</p>";
    echo "<p><strong>Gender:</strong> $gender</p>";
    echo "<p><strong>Blood Group:</strong> $bloodGroup</p>";
    echo "<p><strong>Medical History:</strong> $medicalHistory</p>";
    echo "<p><strong>ID Proof Uploaded:</strong> <a href='$idProofDestPath' target='_blank'>View ID Proof</a></p>";

    if (!empty($uploadedDocs)) {
        echo "<p><strong>Supporting Medical Documents:</strong></p><ul>";
        foreach ($uploadedDocs as $doc) {
            echo "<li><a href='$doc' target='_blank'>" . basename($doc) . "</a></li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>Invalid request. Please submit the form correctly.</p>";
}
?>
