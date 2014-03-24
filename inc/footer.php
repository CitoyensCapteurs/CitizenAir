<?php
/* 
 * This file is part of CitizenAir.
 * CitizenAir is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * CitizenAir is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CitizenAir.  If not, see <http://www.gnu.org/licenses/>.
 */

$tpl .= "</div>
    <footer id=\"footer\" class=\"white-links\">
    <p class=\"left\">La page se mettra à jour automatiquement toutes les 5 minutes.</p>
    <p id=\"attribution\">".((!isset($_GET['live'])) ? '<a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | ' : '')." <a href=\"http://www.citoyenscapteurs.net/\">Citoyens Capteurs</a></p>
    </footer>
    <script type=\"text/javascript\" src=\"leaflet.js\"></script>
<script type=\"text/javascript\" src=\"js.js\"></script>
</body>
</html>";
