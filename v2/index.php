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
            <form method="post">
                <?php 
                    print_r($_POST);
                    // $items = count($_POST);
                    $page_count = isset($_POST['page']) ? intval($_POST['page']) : 1;

                    if (isset($_POST['prenom']) && isset($_POST['nom']))
                    {
                        if (isset($_POST['reset']) && intval($_POST['reset']) == 1) 
                        {
                            echo 'test';
                            $_POST['reset'] = 0;
                        }
                        else 
                            $page_count = 2;
                    }

                    $progress = $page_count / 2 * 100;
                ?>
                
                <input type="hidden" id="page_id" name="page" value="<?php echo $page_count; ?>">
                <h2>Calendrier</h2>
                <div class="form-progress">
                    <span class="form-progress-back"></span>
                    <span class="form-progress-count" style="width: <?php echo $progress; ?>%"></span>
                </div>

                <?php 
                if ($page_count == 1) {
                ?>  
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
                <?php
                } else if ($page_count == 2) {
                ?>
                    <div class="form-group">
                        <label for="planningFile">Sélectionner le planning à utiliser</label>
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
                    <!-- <button type="submit" name="submit">Télécharger le calendrier</button> -->
                    <div class="form-nav">
                        <button type="submit" name="reset" value="1" class="form-nav-prev">
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
                <?php
                }
                ?>
            </form>
        </div>
    </div>
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