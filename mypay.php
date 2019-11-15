<?php


require_once 'mercadopago/vendor/autoload.php';


defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.payment.payment');

// Creo el objeto de pago del plugin 


abstract class AbstractMyPayPayment extends JPayment{
	
// Esta función llama a los parámetros a colocar dentro de la opción de pago en el admin de vikbooking


	protected function buildAdminParameters() {

	$logo_img = VIKMYPAY_URI . 'vikbooking/mypay_logo.png';
	return array(	
		'logo' => array(
			'label' => __('','vikbooking'),
			'type' => 'custom',
			'html' => '<img src="'.$logo_img.'"/>'
		),
		'merchantid' => array(
			'label' => __('Merchant ID','vikbooking'),
			'type' => 'text'
		),
		'testmode' => array(
			'label' => __('Test Mode','vikbooking'),
			'type' => 'select',
			'options' => array('Yes', 'No'),
		),
	);
}

// Constructor que guarda en la variable $params los datos que cargamos a la opción de pago así como los del n° de $order


	public function __construct($alias, $order, $params = array()) {
		parent::__construct($alias, $order, $params);
	}
	

// Llamamos al proceso de pago 


	protected function beginTransaction() {
	
// Llamamos la variable cargada MerchantID en el admin. La misma debería ser la llave pública de mercadopago.


	$merchant_id = $this->getParam('merchantid');

// Llamamos un Array con los datos de la orden de vikbooking.


	$uniq_id = $this->get('sid')."-".$this->get('ts');

// Definimos el archivo que da acción al proceso de pago en este caso donde esté alojado.

// IMPORTANTEEE!!! CUANDO SE INSTALE EL PLUGIN ELIMINAR EL PARCHE DE DIRECTORIO /1573575543259/ RECORDAR ES PROVISORIO

	$action_url = "https://kurumamell.com/1573575543259/wp-content/plugins/vikmypay/mercadopago/createcharge.php";
	if( $this->getParam('testmode') == 'Yes' ) {
		$action_url = "https://kurumamell.com/1573575543259/wp-content/plugins/vikmypay/mercadopago/createcharge.php";
	}
// LLAMO AL FORMULARIO DE PAGO, EN ESTE CASO EL SMARTCHECKOUT DE MERCADOPAGO. LE PASAMOS LA VARIABLE DE NOTIFICACIÓN
// DE EXITO $THIS->GET('NOTIFY_URL') ESA VA A SER LA QUE DE POR CONFIRMADA LA RESERVA SEGÚN LA RESPUESTA DE MERCADOPAGO


	$form='<form action="'.$action_url.'" method="post">';
	$form.='<script
    src="https://www.mercadopago.com.ar/integrations/v1/web-tokenize-checkout.js"
    data-public-key="'.$merchant_id.'"
    data-transaction-amount="'.$this->get('total_to_pay').'">
  	</script>';
	$form.='<input type="hidden" name="your_post_data_notifyurl" value="'.$this->get('notify_url').'"/>';
	
	$form.='</form>';


	echo $form;

}

// ESTA FUNCIÓN VALIDA LA TRANSACCIÓN, EN SÍ ES INÚTIL PUESTO QUE SOLAMENTE DA DE ALTA LA RESERVA PERO LA MAYORÍA DEL PROCESO
// LO HACE EL ARCHIVO CREATECHARGE.PHP DE MERCADOPAGO SEGÚN LA RESPUESTA DE PAGO


protected function validateTransaction(JPaymentStatus &$status) {
	$log = '';
	
	/** EN CASO DE ERROR SE ENVÍA UN EMAIL AL ADMINISTRADOR */

// ACÁ DEFINIMOS QUE SI EN EL PROCESO DE CHECKOUT SE LLAMA A LA VARIABLE GET TASK SE DE POR VERIFICADA LA ORDEN Y PAGADA LA RESERVA

	if(isset($_GET['task'])) {
		$status->verified(true); 
		/** Set a value for the value paid */
		$status->paid( $this->get('total_to_pay'));
	} else {
		$status->appendLog( "Transaction Error!\n".$_POST['error_msg']);
	}
// DETENEMOS LA ITERACIÓN DE LA FUNCIÓN

	return true;
}

// ESTE ES UN HANDLER DEL PLUGIN PARA RESPUESTAS, PERO ES INÚTIL PORQUE LAS RESPUESTA LAS MANEJA CREATECHARGE.PHP DE MERCADOPAGO

	protected function complete($esit = 0) {
		
	}
// FIN DEL OBJETO
	
}
?> 