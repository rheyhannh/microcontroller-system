<!DOCTYPE html>
<?php
        function get_sensor($url){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }

        function post_command($url, $command){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\t\"command\":\"$command\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
        
        $requestData = get_sensor("http://192.168.0.11:5010/web_sensor");
        $obj = json_decode($requestData, true);
        $nilaiSensor = $obj[0]['nilai'];
        echo "Intensitas Cahaya = $nilaiSensor";
        echo "<br>";
        echo "<br>";

        if(isset($_POST['button1'])){
            $data = post_command("http://192.168.0.11:5010/web_command", "on");
            echo "Lampu Dinyalakan";
        }
        if(isset($_POST['button2'])){
            $data = post_command("http://192.168.0.11:5010/web_command", "off");
            echo "Lampu Dimatikan";
        }      
    ?>
<html>
<head>
	<title>Web Service</title>
	<!--Bootsrap 4-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!--Fontawesome-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- Script -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!--Custom styles-->
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<div class="d-flex justify-content-center h-100">
		<div class="card">
			<div class="card-header">
				<h3 class="text-center">Pengendalian LED</h3>
			</div>
			<div class="card-body">
				<form>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-wifi"></i></i></span>
						</div>
						<input type="text" class="form-control" id="disabledInput" placeholder="75d0d7_plus" disabled>
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-microchip"></i></span>
						</div>
						<input type="text" class="form-control" id="disabledInput" placeholder="ESP32" disabled>
					</div>
                    <div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-adjust"></i></span>
						</div>
						<input type="text" class="form-control" id="disabledInput" placeholder="115200" disabled>
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-lightbulb"></i></span>
						</div>
						<input type="text" class="form-control" id="disabledInput" placeholder="115200" disabled>
					</div>
                    <form method="post">
                    <div class="form-group">
						<input type="submit" name="button1" value="Nyalakan" class="btn float-right login_btn">
						<input type="submit" name="button2" value="Matikan" class="btn float-right login_btn">
                    </div>
                    </form>
				</form>
			</div>
		</div>
	</div>
</div>
</body>
</html>
