<?php

namespace pages {
    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class TaskPage extends Page {

        #[ Override ]

        public function displayBodyContent(): void {

            if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
                $latitudeFrom = $_POST[ 'latitudeFrom' ];
                $longitudeFrom = $_POST[ 'longitudeFrom' ];
                $latitudeTo = $_POST[ 'latitudeTo' ];
                $longitudeTo = $_POST[ 'longitudeTo' ];

                // Define regular expressions for latitude and longitude
                $latPattern = '/^(-?[1-8]?\d(\.\d+)?|90(\.0+)?)$/';
                $lonPattern = '/^(-?(1[0-7]\d|[1-9]?\d)(\.\d+)?|180(\.0+)?)$/';

                // Validate inputs using regular expressions
                if ( preg_match( $latPattern, $latitudeFrom ) && preg_match( $lonPattern, $longitudeFrom ) &&
                preg_match( $latPattern, $latitudeTo ) && preg_match( $lonPattern, $longitudeTo ) ) {
                    $distance = $this->haversineGreatCircleDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo );
                    echo '<p>Calculated Distance: ' . htmlspecialchars( $distance ) . ' km</p>';
                } else {
                    echo '<p>Please enter valid latitude and longitude values.</p>';
                }
            }

            // Display the form
            echo '
        <form method="post">
            <label for="latitudeFrom">Широта першої точки:</label>
            <input type="text" id="latitudeFrom" name="latitudeFrom" required><br>

            <label for="longitudeFrom">Довгота першої точки</label>
            <input type="text" id="longitudeFrom" name="longitudeFrom" required><br>

            <label for="latitudeTo">Широта другої точки</label>
            <input type="text" id="latitudeTo" name="latitudeTo" required><br>

            <label for="longitudeTo">Довгота другої точки</label>
            <input type="text" id="longitudeTo" name="longitudeTo" required><br>

            <button type="submit">Розрахувати відстань</button>
        </form>';
        }

        function haversineGreatCircleDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371 ) {
            // перетворення градусів у радіани
            $latFrom = deg2rad( $latitudeFrom );
            $lonFrom = deg2rad( $longitudeFrom );
            $latTo = deg2rad( $latitudeTo );
            $lonTo = deg2rad( $longitudeTo );

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin( sqrt( pow( sin( $latDelta / 2 ), 2 ) +
            cos( $latFrom ) * cos( $latTo ) * pow( sin( $lonDelta / 2 ), 2 ) ) );
            return $angle * $earthRadius;
        }
    }
}
?>