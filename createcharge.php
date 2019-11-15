<?php
    require_once 'vendor/autoload.php';

    MercadoPago\SDK::setAccessToken("TEST-3260720186220625-111416-7e77e8f82ac6c98a0cd75fae4abd8ff2-219171988");
    //...

    $payment = new MercadoPago\Payment();

    // ESTA ES LA URL DE ÉXITO EN CASO DE TENER RESPUESTA EN EL CARGO DE LA TARJETA.
    $url = $_REQUEST["your_post_data_notifyurl"];
    $payment->transaction_amount = 100;

    // Estos $_REQUEST son los datos que se cargan en el formulario de mercadopago

    $payment->token =  $_REQUEST["token"];
    $payment->description = "Practical Bronze Shirt";
    $payment->installments =  $_REQUEST["installments"];
    $payment->payment_method_id =  $_REQUEST["payment_method_id"];
    $payment->issuer_id = $_REQUEST["issuer_id"];

    // Aquí irían los datos del cliente, por el momento el ejemplo contempla un email predeterminado
    $payment->payer = array(
    "email" => "jarrell_hagenes@hotmail.com"
    );


    
    // Guarda y postea el pago
    $payment->save();
    //EN CASO DE QUE LA RESPUESTA DE PAGO SEA APROBADA POR MERCADOPAGO SE REDIRIGE A LA URL DE ÉXITO
    // EN ESTA ESTÁ LA VARIABLE $_GET['TASK'] QUE ES LA QUE ACCIONA LA CONFIRMACIÓN Y DADA DE PAGO

    if ($payment->status =="approved") {
    $status = $_POST['status'];
    header("Location:".$url);       
    
    } 

    // EN CASO QUE LA RESPUESTA DE MERCADOPAGO SEA DISTINTA A LA APROBACIÓN DE PAGO HAY UNA REDIRECCIÓN HEADER
    // Y UN SCRIPT DE ERROR, LA RESERVA VA A QUEDAR COMO PENDIENTE
    
    else {
        echo "<script>alert('Hubo un error procesando el pago!')</script>";
        header("Location: https://kurumamell.com/1573575543259/");
    }
   
  

?>