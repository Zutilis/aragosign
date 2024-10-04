<?php

class FileUploader {

    private $file;         // Tableau contenant les informations sur le fichier (taille, nom, chemin temporaire)
    private $targetPath;   // Chemin de destination où le fichier sera uploadé

    /**
     * Constructeur qui initialise les propriétés avec le fichier et le chemin cible.
     * 
     * @param array $file Informations sur le fichier (souvent provenant de $_FILES)
     * @param string $targetPath Chemin complet où le fichier sera enregistré
     */
    public function __construct($file, $targetPath) {
        $this->file = $file;                   // On assigne le fichier à la propriété $file
        $this->targetPath = $targetPath;       // On assigne le chemin cible à la propriété $targetPath
    }

    /**
     * Méthode pour uploader le fichier vers le chemin cible avec des vérifications.
     * 
     * @throws Exception si une erreur survient (fichier existe déjà, trop lourd, problème de téléversement)
     * @return void
     */
    public function upload() {
        // Vérifie si un fichier existe déjà au même emplacement
        if (file_exists($this->targetPath)) {
            throw new Exception("Le fichier existe déjà.");
        }

        // Vérifie si la taille du fichier dépasse 500 Ko
        if ($this->file["size"] > 500000) {
            throw new Exception("Le fichier est trop lourd.");
        }

        // Déplace le fichier depuis son emplacement temporaire vers la destination finale
        if (!move_uploaded_file($this->file["tmp_name"], $this->targetPath)) {
            throw new Exception("Problème lors du téléversement.");
        }
    }
}