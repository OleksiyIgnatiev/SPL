<?php

    header( 'Content-Type: application/json' );

    try {
        $db = new PDO( 'sqlite:../lw1.db' );
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $data = json_decode( file_get_contents( 'php://input' ), true );

        if ( isset( $data[ 'vacancy_id' ] ) ) {
            $vacancy_id = $data[ 'vacancy_id' ];

            $sql = 'DELETE FROM vacancy WHERE vacancy_id = :vacancy_id';

            $stmt = $db->prepare( $sql );
            $stmt->bindParam( ':vacancy_id', $vacancy_id, PDO::PARAM_INT );

            if ( $stmt->execute() ) {
                echo json_encode( [ 'message' => 'Record deleted successfully' ] );
            } else {
                http_response_code( 500 );
                echo json_encode( [ 'message' => 'Error deleting record' ] );
            }
        } else {
            http_response_code( 400 );
            echo json_encode( [ 'message' => 'Invalid input' ] );
        }
    } catch ( PDOException $e ) {
        http_response_code( 500 );
        echo json_encode( [ 'message' => 'Error: ' . $e->getMessage() ] );
    }

?>
