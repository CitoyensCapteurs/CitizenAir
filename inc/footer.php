<?php
$tpl .= "</div>
    <footer id=\"footer\" class=\"white-links\">
    <p class=\"left\">La page se mettra à jour automatiquement toutes les 5 minutes.</p>
    <p id=\"attribution\">".((!isset($_GET['live'])) ? '<a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | ' : '')." <a href=\"http://www.citoyenscapteurs.net/\">Citoyens Capteurs</a></p>
    </footer>
    <script type=\"text/javascript\" src=\"leaflet.js\"></script>
<script type=\"text/javascript\" src=\"js.js\"></script>
</body>
</html>";
