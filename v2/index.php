<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="calendrier-container">
        <div class="container" enctype="multipart/form-data">
            <h2>Calendrier</h2>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
            <?php 
            if (!isset($_POST['prenom']) || !isset($_POST['nom']) || isset($_POST['previous2'])) {
            ?>  
                <form method="post">
                    <div class="form-group">
                        <label for="prenom">Entrer votre prénom</label>
                        <input type="text" name="prenom" id="prenom" placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <label for="nom">Entrer votre nom de famille</label>
                        <input type="text" name="nom" id="nom" placeholder="Nom">
                    </div>
                    <div class="form-nav">
                        <button type="submit" class="form-nav-next">
                            <p id="form-nav-text">Suivant</p>
                            <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff" transform="rotate(180)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
                                            <path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                    </div>
                </form>
            <?php
            } else if (isset($_POST['prenom']) && isset($_POST['nom'])
                    && !isset($_POST['month_planning']) && !isset($_POST['planningFile'])) { // Page 2
            ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="prenom" value="<?php echo $_POST['prenom']; ?>">
                    <input type="hidden" name="nom" value="<?php echo $_POST['nom']; ?>">
                    <div class="form-group">
                        <label for="planningFile">Sélectionner le planning à utiliser 
                            (<a class="font-size-m" href="help.php" target="__blank">Aide</a>)
                        </label>
                        <input type="file" name="planningFile" id="planningFile" class="file-input" onchange="handleFileSelect(event, 'planningFilePreview', 'planningFileCancel')">
                        <label for="planningFile" class="custom-file-label">Choisir un fichier</label>
                        <div id="planningFilePreview" class="file-preview" style="display:none;">
                            <div class="file-image">
                                <img src="assets/document.svg" alt="">
                            </div>
                            <div class="file-name-preview"></div>
                            <button type="button" id="planningFileCancel" class="cancel-btn file-image" onclick="cancelFile('planningFile', 'planningFilePreview', 'planningFileCancel')">
                                <img src="assets/rubbish.svg" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="month_planning">Selectionner un mois</label>
                        <input type="month" id="month_planning" name="month_planning" min="2023-09" max="2024-07" value="<?php echo date('Y') . '-' . date('m'); ?>">
                    </div>
                    <div class="form-error" style="display:none;">
                        <p id="form-error-text">Test</p>
                    </div>
                    <div class="form-nav">
                        <button type="reset" name="previous2" value="1" class="form-nav-prev" id="previous2" onclick="window.history.back();">
                            <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
                                            <path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <p id="form-nav-text">Précédent</p>
                        </button>
                        <button type="submit" class="form-nav-next">
                            <p id="form-nav-text">Suivant</p>
                            <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff" transform="rotate(180)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
                                            <path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                    </div>
                </form>
            <?php
            } else { // All data collected
                require_once('utils/Utils.php');
                require_once('file/FileUploader.php');
                require_once('events/EventProcessor.php');
                require_once('calendrier/CalendrierOutput.php');

                $root = $_SERVER['DOCUMENT_ROOT'];
                $plannings_path = 'plannings/';
                $outputPath = __DIR__ . '/uploads/output.pdf';

                $year = intval(explode('-', $_POST['month_planning'])[0]);
                $month = intval(explode('-', $_POST['month_planning'])[1]);

                if ($month <= 0 || $month > 12) {
                    echo "Sorry, the month is invalid";
                    return;
                }

                $planningFile = $_FILES["planningFile"];
                $planningFilePath = $plannings_path . basename($planningFile["name"]);

                try {

                    echo 'A';

                    $planningUploader = new FileUploader($_FILES["planningFile"], $planningFilePath);
                    $planningUploader->upload();

                    echo 'B';

                    $eventProcessor = new EventProcessor($planningFilePath, $month, $year);
                    $calendrier = $eventProcessor->process();

                    echo 'C';

                    $calendrierOutput = new CalendrierOutput($calendrier, $outputPath);
                    $calendrierOutput->generateOutput();

                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                unlink($planningFilePath);
                unlink($signFilePath);
                unlink($outputPath);
            }
            ?>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('progress-bar');
            let progress = localStorage.getItem('progress') || 0;

            // Set initial progress width without transition
            progressBar.style.width = progress + '%';
            progressBar.style.transition = 'none';

            // Allow reflow, then add the transition back
            requestAnimationFrame(() => {
                progressBar.style.transition = 'width 0.5s ease-in-out';
                progressBar.style.width = progress + '%';
            });

            <?php if (!isset($_POST['prenom']) || !isset($_POST['nom']) || isset($_POST['previous2'])) { ?>
                progress = 0;
            <?php } else if (isset($_POST['prenom']) && isset($_POST['nom'])) { ?>
                progress = 50;
            <?php } ?>

            // Update progress bar with transition
            setTimeout(() => {
                progressBar.style.width = progress + '%';
            }, 100);

            // Save current progress to localStorage
            localStorage.setItem('progress', progress);
        });
    </script>
    <script>
        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 B';

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        function handleFileSelect(event, previewId, cancelBtnId) {
            const file = event.target.files[0];
            const filePreview = document.getElementById(previewId);
            const fileName = filePreview.getElementsByClassName('file-name-preview')[0];
            const cancelBtn = document.getElementById(cancelBtnId);

            if (file) {
                fileName.innerHTML = `<p>${file.name} | ${formatBytes(file.size)} </p>`;
                filePreview.style.display = 'flex';
            } else {
                fileName.innerHTML = '';
                filePreview.style.display = 'none';
            }
        }

        function cancelFile(inputId, previewId, cancelBtnId) {
            const input = document.getElementById(inputId);
            const filePreview = document.getElementById(previewId);
            const fileName = filePreview.getElementsByClassName('file-name-preview')[0];
            const cancelBtn = document.getElementById(cancelBtnId);

            input.value = '';
            fileName.innerHTML = '';
            filePreview.style.display = 'none';
        }
    </script>
</body>

</html>