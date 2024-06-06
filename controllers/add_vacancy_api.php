<?php

    header( 'Content-Type: application/json' );

    try {
        $db = new PDO( 'sqlite:../lw1.db' );
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $data = json_decode( file_get_contents( 'php://input' ), true );

        // Додатковий вивід для налагодження
        error_log( print_r( $data, true ) );

        if ( isset( $data[ 'company_id' ] ) && isset( $data[ 'description' ] ) ) {
            $company_id = $data[ 'company_id' ];
            $description = $data[ 'description' ];
            $is_remote = isset( $data[ 'is_remote' ] ) ? 1 : 0;
            $monthly_salary = $data[ 'monthly_salary' ] ?? null;
            $worker_competence = $data[ 'worker_competence' ] ?? null;
            $profile_requirement = $data[ 'profile_requirement' ] ?? null;
            $language = $data[ 'language' ] ?? null;
            $location = $data[ 'location' ] ?? null;

            $sql = "INSERT INTO vacancy (company_id, description, is_remote, monthly_salary, worker_competence, profile_requirement, language, location)
                VALUES (:company_id, :description, :is_remote, :monthly_salary, :worker_competence, :profile_requirement, :language, :location)";

            $stmt = $db->prepare( $sql );
            $stmt->bindParam( ':company_id', $company_id, PDO::PARAM_INT );
            $stmt->bindParam( ':description', $description, PDO::PARAM_STR );
            $stmt->bindParam( ':is_remote', $is_remote, PDO::PARAM_INT );
            $stmt->bindParam( ':monthly_salary', $monthly_salary, PDO::PARAM_STR );
            $stmt->bindParam( ':worker_competence', $worker_competence, PDO::PARAM_STR );
            $stmt->bindParam( ':profile_requirement', $profile_requirement, PDO::PARAM_STR );
            $stmt->bindParam( ':language', $language, PDO::PARAM_STR );
            $stmt->bindParam( ':location', $location, PDO::PARAM_STR );

            if ( $stmt->execute() ) {
                echo json_encode( [ 'message' => 'New record created successfully' ] );
            } else {
                http_response_code( 500 );
                echo json_encode( [ 'message' => 'Error creating record' ] );
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
