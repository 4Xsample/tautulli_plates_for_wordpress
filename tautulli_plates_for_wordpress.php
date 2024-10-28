<?php
// Configuració
$api_key = 'LA_TEVA_CLAU_API'; // Aquí hi va la clau API, directament incrustada perquè no ens importa gens les best practices.
$tautulli_url = 'https://URL_DE_TAUTULLI/api/v2';

// CSS inclòs perquè sí, un estil independent seria més net però què hi farem.
echo '<style>
/* Estils per al contingut */

#library-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px 0;
}

/* Aquests estils són, bàsicament, per fer-ho veure presentable */
.dashboard-stats-instance {
    flex: 1;
    min-width: 300px;
}

.dashboard-stats-container {
    position: relative;
    background-color: #282828;
    border-radius: 8px;
    overflow: hidden;
    color: #fff;
    margin-bottom: 20px;
}

/* I aquí un gradient perquè no es vegi massa sòrdid */
.dashboard-stats-background::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.7));
    z-index: 1;
}

/* Estils responsive, per si algú encara no ho havia pensat */
@media (max-width: 768px) {
    #library-stats {
        flex-direction: column;
    }
}
</style>';

// Funció per a fer les crides a l’API del Tautulli, si falla, doncs ja ho veurem.
if (!function_exists('tautulli_api_call')) {
    function tautulli_api_call($tautulli_url, $api_key, $cmd, $params = []) {
        // Definim els paràmetres com si fos necessari explicar-ho
        $params_query = http_build_query($params);
        $url = "$tautulli_url?apikey=$api_key&cmd=$cmd&$params_query";

        // Crida a l'API amb cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);

        // Mirem si cURL ha decidit complicar-nos el dia
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            die('Error en la crida cURL: ' . $error_msg); // Aquí, si falla, millor ni continuar
        } else {
            // Si el codi de resposta no és 200, doncs fins aquí hem arribat
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                curl_close($ch);
                die('Error HTTP en la crida cURL: Codi ' . $http_code); // Sí, tanquem sense preguntar
            }
            curl_close($ch);
            return json_decode($response, true); // Si hem arribat aquí, ja podem donar gràcies
        }
    }
}

// Aconseguim les dades de les biblioteques, en cas que falli, no perdem el temps
$libraries_data = tautulli_api_call($tautulli_url, $api_key, 'get_libraries');

// Si la crida falla, aquí ja no continuem
if (!$libraries_data || $libraries_data['response']['result'] != 'success') {
    die('Error en obtenir les biblioteques: ' . $libraries_data['response']['message']); // Cap error ben explicat, només tanquem
}

$libraries = $libraries_data['response']['data'];

// Inicialitzem arrays per a cada tipus de biblioteca (més per estètica que per altra cosa)
$movie_libraries = [];
$show_libraries = [];
$music_libraries = [];

// Assignem imatges de fons per cada tipus de biblioteca perquè quedi presentable
$background_images = [
    'movie' => '/:/resources/movie-fanart.jpg',
    'show' => '/:/resources/show-fanart.jpg',
    'artist' => '/:/resources/artist-fanart.jpg',
];

// Processament de cada biblioteca, excepte si són “Vídeos personals” (ningú vol veure això)
foreach ($libraries as $library) {
    if ($library['section_name'] == 'Vídeos personals') {
        continue; // Passem endavant, sense pena ni glòria
    }

    // Posem cada biblioteca al seu lloc corresponent, intentant ser organitzats
    if ($library['section_type'] == 'movie') {
        $movie_libraries[] = $library;
    } elseif ($library['section_type'] == 'show') {
        $show_libraries[] = $library;
    } elseif ($library['section_type'] == 'artist') {
        $music_libraries[] = $library;
    }
}

// Funció per obtenir la portada de l'últim element afegit, si és que tenim sort
function get_last_added_thumb($tautulli_url, $api_key, $section_id, $media_type) {
    // Cridem Tautulli amb els paràmetres per veure què trobem
    $params = [
        'section_id' => $section_id,
        'count' => 1,
        'media_type' => $media_type,
    ];
    $recent_data = tautulli_api_call($tautulli_url, $api_key, 'get_recently_added', $params);

    // Si la crida falla, res de res, tornem amb les mans buides
    if (!$recent_data || $recent_data['response']['result'] != 'success') {
        return null;
    }

    // Retornem la miniatura, si hi és. Si no, doncs no passa res
    $recent_items = $recent_data['response']['data']['recently_added'];
    if (!empty($recent_items)) {
        return $recent_items[0]['grandparent_thumb'] ?? $recent_items[0]['parent_thumb'] ?? $recent_items[0]['thumb'] ?? null;
    }

    return null; // Mala sort, sense portada
}

// Obtenim portades per a cada tipus
$movie_thumb = null;
if (!empty($movie_libraries)) {
    $movie_thumb = get_last_added_thumb($tautulli_url, $api_key, $movie_libraries[0]['section_id'], 'movie');
    if ($movie_thumb) {
        $background_images['movie'] = $movie_thumb;
    }
}

$show_thumb = null;
if (!empty($show_libraries)) {
    $show_thumb = get_last_added_thumb($tautulli_url, $api_key, $show_libraries[0]['section_id'], 'episode');
    if ($show_thumb) {
        $background_images['show'] = $show_thumb;
    }
}

$music_thumb = null;
if (!empty($music_libraries)) {
    $music_thumb = get_last_added_thumb($tautulli_url, $api_key, $music_libraries[0]['section_id'], 'track');
    if ($music_thumb) {
        $background_images['artist'] = $music_thumb;
    }
}

// Funció per generar l'HTML de cada secció, reutilitzable per alguna cosa que ningú entendrà
function render_library_section($type, $libraries, $background_images) {
    // Títols per a cada tipus de biblioteca
    $type_titles = [
        'movie' => ['title' => 'Biblioteca de Pel·lícules', 'units' => 'Pel·lícules'],
        'show' => ['title' => 'Biblioteca de Sèries', 'units' => 'Sèries / Temporades / Episodis'],
        'artist' => ['title' => 'Biblioteca de Música', 'units' => 'Artistes / Àlbums / Cançons'],
    ];

    $type_info = $type_titles[$type];
    $background_image = $background_images[$type];

    // Generem la URL completa de la imatge de fons, per si algú li interessa
    global $tautulli_url, $api_key;
    if (strpos($background_image, '/api/v2') === false) {
        $background_image = $tautulli_url . '?apikey=' . $api_key . '&cmd=pms_image_proxy&img=' . urlencode($background_image);
    }

    echo '<div class="dashboard-stats-instance" data-section_type="' . $type . '">';
    echo '  <div class="dashboard-stats-container">';
    echo '    <div class="dashboard-stats-background" style="background-image: url(\'' . $background_image . '\');">';
    echo '      <div class="dashboard-stats-info-container">';
    echo '        <div class="dashboard-stats-info-title">';
    echo '          <h4>' . $type_info['title'] . '</h4>';
    echo '        </div>';
    echo '        <div class="dashboard-stats-info-units">';
    echo '          <span class="dashboard-stats-stats-units">' . $type_info['units'] . '</span>';
    echo '        </div>';
    echo '        <div class="dashboard-stats-info-scroller">';
    echo '          <div class="dashboard-stats-info scroller-content">';
    echo '            <ul class="dashboard-stats-info-list">';

    // Iterem a través de cada biblioteca i mostrem la informació. Mira que simple.
    foreach ($libraries as $library) {
        $count = isset($library['count']) ? intval($library['count']) : 0;
        $parent_count = isset($library['parent_count']) ? intval($library['parent_count']) : 0;
        $child_count = isset($library['child_count']) ? intval($library['child_count']) : 0;

        echo '              <li class="dashboard-stats-info-item">';
        echo '                <div class="sub-count">';
        if ($type == 'movie') {
            echo number_format($count);
        } elseif ($type == 'show' || $type == 'artist') {
            echo number_format($count) . ' / ' . number_format($parent_count) . ' / ' . number_format($child_count);
        }
        echo '                </div>';
        echo '              </li>';
    }
    echo '            </ul>';
    echo '          </div>'; // Fi de scroller-content
    echo '        </div>'; // Fi de dashboard-stats-info-scroller
    echo '      </div>'; // Fi de dashboard-stats-info-container
    echo '    </div>'; // Fi de dashboard-stats-background
    echo '  </div>'; // Fi de dashboard-stats-container
    echo '</div>'; // Fi de dashboard-stats-instance
}

// Mostrem el contingut principal
echo '<div id="library-stats" class="library-platforms">';
render_library_section('movie', $movie_libraries, $background_images);
render_library_section('show', $show_libraries, $background_images);
render_library_section('artist', $music_libraries, $background_images);
echo '</div>';
?>
